#!/bin/bash
set -euo pipefail

# =====================================================================
# HYBRID DEPLOYMENT SCRIPT - Jtiport (Blue-Green) [FIXED]
# =====================================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

IMAGE_TAG=${1:-latest}
DOCKER_IMAGE="${DOCKER_HUB_USERNAME}/jtiport:${IMAGE_TAG}"
NETWORK_NAME="jtiport_network"
PROJECT_NAME="jtiport"
BACKUP_DIR="/home/dika/backups/jtiport"
STATE_FILE="$BACKUP_DIR/deployment.state"
NGINX_CONF_DIR="./docker/nginx"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
APP_ENV="${APP_ENV:-production}"

is_container_running() {
    local container_name="$1"
    [ "$(docker inspect -f '{{.State.Running}}' "$container_name" 2>/dev/null || true)" = "true" ]
}

# Error handling
trap 'handle_error $? $LINENO' ERR
handle_error() {
    local exit_code=$1
    local line_num=$2
    echo -e "${RED}❌ ERROR at line $line_num (exit code: $exit_code)${NC}"
    exit $exit_code
}

echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}     Jtiport Blue-Green Deployment - $TIMESTAMP${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"

# =====================================================================
# 0. PRE-FLIGHT: NETWORK & INFRA (DB + REDIS ONLY)
# FIX: Nginx TIDAK distart di sini karena container app belum ada.
#      Nginx di-start setelah container app pertama ready.
# =====================================================================

# Pastikan network ada
if ! docker network inspect $NETWORK_NAME >/dev/null 2>&1; then
    echo -e "${YELLOW}🌐 Creating network: $NETWORK_NAME...${NC}"
    docker network create $NETWORK_NAME
fi

# Start DB dan Redis saja — nginx menyusul setelah container app siap
if ! docker ps | grep -q "${PROJECT_NAME}_mysql"; then
    echo -e "${YELLOW}🏗️ Starting infrastructure (DB, Redis)...${NC}"
    docker-compose up -d db redis
    echo -e "${YELLOW}⏳ Waiting for DB to be ready...${NC}"
    sleep 10
fi


# =====================================================================
# 1. DETECT TARGET (STATE TRACKING)
# =====================================================================

mkdir -p $BACKUP_DIR
if [ -f "$STATE_FILE" ]; then
    source "$STATE_FILE"
    echo -e "${YELLOW}📋 Previous active: $LAST_ACTIVE_CONTAINER${NC}"
else
    LAST_ACTIVE_CONTAINER=""
fi

if [ "$LAST_ACTIVE_CONTAINER" == "${PROJECT_NAME}_blue" ]; then
    NEW_NAME="${PROJECT_NAME}_green"
    OLD_NAME="${PROJECT_NAME}_blue"
    IS_FIRST_DEPLOYMENT=false
elif [ "$LAST_ACTIVE_CONTAINER" == "${PROJECT_NAME}_green" ]; then
    NEW_NAME="${PROJECT_NAME}_blue"
    OLD_NAME="${PROJECT_NAME}_green"
    IS_FIRST_DEPLOYMENT=false
else
    NEW_NAME="${PROJECT_NAME}_blue"
    OLD_NAME=""
    IS_FIRST_DEPLOYMENT=true
    echo -e "${YELLOW}🆕 First time deployment detected.${NC}"
fi

echo -e "${BLUE}🚀 Target Container: $NEW_NAME${NC}"

# =====================================================================
# 2. PULL & RUN NEW CONTAINER
# =====================================================================

echo -e "${YELLOW}📥 Pulling image: $DOCKER_IMAGE ...${NC}"
docker pull $DOCKER_IMAGE

echo -e "${YELLOW}🧹 Removing old $NEW_NAME if exists...${NC}"
docker rm -f $NEW_NAME 2>/dev/null || true

echo -e "${YELLOW}⚙️ Starting $NEW_NAME...${NC}"
# Default to .env file if it exists, otherwise pass minimal vars
ENV_OPTS=""
if [ -f ".env" ]; then
    ENV_OPTS="--env-file .env"
else
    ENV_OPTS="-e LARAVEL_ENV_ENCRYPTION_KEY=$LARAVEL_ENV_ENCRYPTION_KEY -e APP_ENV=$APP_ENV"
fi

docker run -d \
  --name $NEW_NAME \
  --network $NETWORK_NAME \
  --restart unless-stopped \
  $ENV_OPTS \
  -v app_storage:/var/www/html/storage \
  $DOCKER_IMAGE

# Tunggu container siap (PHP-FPM butuh waktu start)
echo -e "${YELLOW}⏳ Waiting for $NEW_NAME to be ready...${NC}"
for i in $(seq 1 15); do
    if docker exec $NEW_NAME php -r "echo 'ok';" >/dev/null 2>&1; then
        echo -e "${GREEN}✓ $NEW_NAME is ready (${i}s)${NC}"
        break
    fi
    if [ $i -eq 15 ]; then
        echo -e "${RED}❌ Container $NEW_NAME failed to start in time${NC}"
        exit 1
    fi
    sleep 2
done

# =====================================================================
# 3. LARAVEL OPTIMIZATION
# =====================================================================

echo -e "${YELLOW}⚡ Running Laravel commands on $NEW_NAME...${NC}"
docker exec $NEW_NAME php artisan optimize
docker exec $NEW_NAME php artisan storage:link --force || true
docker exec $NEW_NAME php artisan migrate --force

# =====================================================================
# 4. NGINX CONFIG & TRAFFIC SWITCH
# FIX: Gunakan set $backend variable (lazy DNS resolve) agar Nginx
#      tidak crash saat container belum ada.
#      Config digenerate ulang setiap deploy, bukan di-sed toggle.
# =====================================================================

echo -e "${YELLOW}🔄 Updating Nginx config to point to $NEW_NAME...${NC}"

# Generate config baru dengan backend yang aktif
cat > "${NGINX_CONF_DIR}/default.conf" << EOF
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;

    # Resolver DNS internal Docker — lazy resolve per request
    # FIX: Dengan resolver + set \$backend, Nginx tidak crash saat
    #      container belum ada. DNS di-resolve saat request masuk,
    #      bukan saat Nginx startup.
    resolver 127.0.0.11 valid=5s ipv6=off;

    location /health {
        access_log off;
        return 200 "healthy\n";
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        # FIX: Gunakan variable agar DNS resolve lazy (tidak saat startup).
        # Tanpa ini, Nginx akan "host not found in upstream" jika container
        # belum ada saat Nginx start.
        set \$backend "${NEW_NAME}:9000";

        fastcgi_pass \$backend;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;

        # Pass real IP
        fastcgi_param HTTP_X_REAL_IP \$remote_addr;
    }
}
EOF

# FIX: Start Nginx SETELAH container app ready (first deployment),
#      atau reload saja kalau Nginx sudah berjalan.
if is_container_running "${PROJECT_NAME}_nginx"; then
    echo -e "${YELLOW}🔄 Nginx sudah running, test & reload config...${NC}"
    if docker exec ${PROJECT_NAME}_nginx nginx -t >/dev/null 2>&1; then
        docker exec ${PROJECT_NAME}_nginx nginx -s reload
        echo -e "${GREEN}✅ Nginx reloaded. Traffic is now on $NEW_NAME${NC}"
    else
        echo -e "${RED}❌ Nginx config error! Check ${NGINX_CONF_DIR}/default.conf${NC}"
        exit 1
    fi
else
    echo -e "${YELLOW}🚀 Starting Nginx for the first time...${NC}"
    # FIX: Nginx baru start di sini setelah container app sudah ready,
    #      sehingga DNS untuk $NEW_NAME sudah bisa di-resolve.
    docker-compose up -d nginx

    # Tunggu sampai state container benar-benar running untuk hindari false negative
    for i in $(seq 1 15); do
        if is_container_running "${PROJECT_NAME}_nginx"; then
            echo -e "${GREEN}✅ Nginx started. Traffic is on $NEW_NAME${NC}"
            break
        fi

        if [ $i -eq 15 ]; then
            echo -e "${RED}❌ Nginx failed to start${NC}"
            docker ps -a --filter "name=${PROJECT_NAME}_nginx"
            exit 1
        fi

        sleep 1
    done
fi

# =====================================================================
# 5. SAVE STATE & CLEANUP
# =====================================================================

echo "LAST_ACTIVE_CONTAINER=\"$NEW_NAME\"" > "$STATE_FILE"
echo -e "${GREEN}💾 State saved: active=$NEW_NAME${NC}"

if [ "$IS_FIRST_DEPLOYMENT" = false ]; then
    echo -e "${YELLOW}🧹 Cleaning up old container: $OLD_NAME...${NC}"
    docker stop $OLD_NAME 2>/dev/null || true
    docker rm   $OLD_NAME 2>/dev/null || true
    echo -e "${GREEN}✓ $OLD_NAME removed${NC}"
fi

# Restart/Update worker
echo -e "${YELLOW}⚙️ Starting/Updating queue worker...${NC}"
docker-compose up -d worker

echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ DEPLOYMENT SUCCESSFUL: $NEW_NAME IS ACTIVE${NC}"
echo -e "${GREEN}   Image   : $DOCKER_IMAGE${NC}"
echo -e "${GREEN}   Time    : $TIMESTAMP${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
