#!/bin/bash
set -euo pipefail

# =====================================================================
# ROLLBACK SCRIPT - Jtiport (Blue-Green) [FIXED]
# =====================================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

PROJECT_NAME="jtiport"
NETWORK_NAME="jtiport_network"
BACKUP_DIR="/home/dika/backups/jtiport"
STATE_FILE="$BACKUP_DIR/deployment.state"
NGINX_CONF_DIR="./docker/nginx"
DOCKER_IMAGE="${DOCKER_HUB_USERNAME}/jtiport"
ROLLBACK_TAG=${1:-""}
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

trap 'handle_error $? $LINENO' ERR
handle_error() {
    echo -e "${RED}❌ ROLLBACK ERROR at line $2 (exit code: $1)${NC}"
    exit $1
}

echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}     Jtiport Rollback - $TIMESTAMP${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"

# =====================================================================
# 1. BACA STATE
# =====================================================================

if [ ! -f "$STATE_FILE" ]; then
    echo -e "${RED}❌ State file tidak ditemukan: $STATE_FILE${NC}"
    echo -e "${YELLOW}   Tidak ada informasi deployment sebelumnya.${NC}"
    exit 1
fi

source "$STATE_FILE"
echo -e "${YELLOW}📋 Active container: $LAST_ACTIVE_CONTAINER${NC}"

# Tentukan target rollback
if [ "$LAST_ACTIVE_CONTAINER" == "${PROJECT_NAME}_blue" ]; then
    ROLLBACK_TARGET="${PROJECT_NAME}_green"
    CURRENT="${PROJECT_NAME}_blue"
elif [ "$LAST_ACTIVE_CONTAINER" == "${PROJECT_NAME}_green" ]; then
    ROLLBACK_TARGET="${PROJECT_NAME}_blue"
    CURRENT="${PROJECT_NAME}_green"
else
    echo -e "${RED}❌ State tidak dikenali: $LAST_ACTIVE_CONTAINER${NC}"
    exit 1
fi

echo -e "${YELLOW}🔄 Rolling back from $CURRENT → $ROLLBACK_TARGET${NC}"

# =====================================================================
# 2. JALANKAN CONTAINER ROLLBACK
# =====================================================================

# Tentukan image tag
if [ -n "$ROLLBACK_TAG" ]; then
    IMAGE="${DOCKER_IMAGE}:${ROLLBACK_TAG}"
    echo -e "${YELLOW}📥 Pulling rollback image: $IMAGE ...${NC}"
    docker pull $IMAGE
else
    # Pakai image yang sudah ada di lokal (latest sebelumnya)
    IMAGE="${DOCKER_IMAGE}:latest"
    echo -e "${YELLOW}⚠️  No tag specified — using local image: $IMAGE${NC}"
fi

docker rm -f $ROLLBACK_TARGET 2>/dev/null || true

echo -e "${YELLOW}⚙️ Starting rollback container: $ROLLBACK_TARGET...${NC}"
docker run -d \
  --name $ROLLBACK_TARGET \
  --network $NETWORK_NAME \
  --restart unless-stopped \
  -e LARAVEL_ENV_ENCRYPTION_KEY="$LARAVEL_ENV_ENCRYPTION_KEY" \
  -e APP_ENV="${APP_ENV:-production}" \
  -v app_storage:/var/www/html/storage \
  $IMAGE

# Tunggu container ready
echo -e "${YELLOW}⏳ Waiting for $ROLLBACK_TARGET...${NC}"
for i in $(seq 1 15); do
    if docker exec $ROLLBACK_TARGET php -r "echo 'ok';" >/dev/null 2>&1; then
        echo -e "${GREEN}✓ $ROLLBACK_TARGET ready (${i}s)${NC}"
        break
    fi
    if [ $i -eq 15 ]; then
        echo -e "${RED}❌ Rollback container gagal start${NC}"
        exit 1
    fi
    sleep 2
done

# =====================================================================
# 3. SWITCH NGINX KE ROLLBACK TARGET
# =====================================================================

echo -e "${YELLOW}🔄 Switching Nginx to $ROLLBACK_TARGET...${NC}"

cat > "${NGINX_CONF_DIR}/default.conf" << EOF
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;

    resolver 127.0.0.11 valid=5s ipv6=off;

    location /health {
        access_log off;
        return 200 "healthy\n";
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        set \$backend "${ROLLBACK_TARGET}:9000";
        fastcgi_pass \$backend;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_param HTTP_X_REAL_IP \$remote_addr;
    }
}
EOF

if docker exec ${PROJECT_NAME}_nginx nginx -t >/dev/null 2>&1; then
    docker exec ${PROJECT_NAME}_nginx nginx -s reload
    echo -e "${GREEN}✅ Nginx switched to $ROLLBACK_TARGET${NC}"
else
    echo -e "${RED}❌ Nginx config error saat rollback!${NC}"
    exit 1
fi

# =====================================================================
# 4. RESTART WORKER WITH ROLLBACK IMAGE
# =====================================================================

echo -e "${YELLOW}⚙️ Rolling back worker...${NC}"
docker rm -f ${PROJECT_NAME}_worker 2>/dev/null || true
docker run -d \
  --name ${PROJECT_NAME}_worker \
  --network $NETWORK_NAME \
  --restart unless-stopped \
  -e LARAVEL_ENV_ENCRYPTION_KEY="$LARAVEL_ENV_ENCRYPTION_KEY" \
  -e APP_ENV="${APP_ENV:-production}" \
  -v app_storage:/var/www/html/storage \
  $IMAGE \
  php artisan queue:work redis --sleep=3 --tries=3 --timeout=90
echo -e "${GREEN}✓ Worker rolled back with image: $IMAGE${NC}"

# =====================================================================
# 5. SAVE STATE & CLEANUP
# =====================================================================

echo "LAST_ACTIVE_CONTAINER=\"$ROLLBACK_TARGET\"" > "$STATE_FILE"

echo -e "${YELLOW}🧹 Stopping & removing $CURRENT...${NC}"
docker stop $CURRENT 2>/dev/null || true
docker rm   $CURRENT 2>/dev/null || true

echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ ROLLBACK SUCCESSFUL: $ROLLBACK_TARGET IS ACTIVE${NC}"
echo -e "${GREEN}   Time : $TIMESTAMP${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
