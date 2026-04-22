#!/bin/bash
set -euo pipefail

# =====================================================================
# HYBRID DEPLOYMENT SCRIPT - Blue-Green (Sesuai Docker Compose Service)
# =====================================================================

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Konfigurasi - WAJIB DISESUAIKAN DENGAN SERVICE DI COMPOSE
DOCKER_IMAGE="${DOCKER_HUB_USERNAME}/jtiport:latest"
NETWORK_NAME="jtiport_network"
PROJECT_NAME="jtiport"
BACKUP_DIR="/home/dika/backups/jtiport"
STATE_FILE="$BACKUP_DIR/deployment.state"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
APP_ENV="${APP_ENV:-production}"

# Error handling
trap 'handle_error $? $LINENO' ERR
handle_error() {
    local exit_code=$1
    local line_num=$2
    echo -e "${RED}❌ ERROR at line $line_num (exit code: $exit_code)${NC}"
    exit $exit_code
}

echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}     Blue-Green Deployment - $TIMESTAMP${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"

# =====================================================================
# 0. UTILITY FUNCTIONS
# =====================================================================

wait_app_ready() {
    local container=$1
    echo -e "${YELLOW}⏳ Waiting for $container to be ready...${NC}"
    for i in {1..30}; do
        # Menggunakan php-fpm check atau artisan
        if docker exec $container php artisan --version >/dev/null 2>&1; then
            echo -e "${GREEN}✅ $container ready!${NC}"
            return 0
        fi
        echo -n "."
        sleep 2
    done
    echo -e "${RED}❌ $container timeout!${NC}"
    return 1
}

# =====================================================================
# 1. PRE-DEPLOYMENT CHECKS
# =====================================================================

mkdir -p $BACKUP_DIR

# Tambahkan ini di deploy.sh setelah baris mkdir -p $BACKUP_DIR
if ! docker network inspect $NETWORK_NAME >/dev/null 2>&1; then
    echo -e "${YELLOW}🌐 Creating network: $NETWORK_NAME...${NC}"
    docker network create $NETWORK_NAME
fi

# Load previous state
if [ -f "$STATE_FILE" ]; then
    source "$STATE_FILE"
    echo -e "${YELLOW}📋 Previous active: $LAST_ACTIVE_CONTAINER${NC}"
else
    LAST_ACTIVE_CONTAINER=""
fi

# =====================================================================
# 2. DETECT TARGET
# =====================================================================

# Jika sebelumnya blue (atau kosong), target sekarang adalah green, dan sebaliknya
if [ "$LAST_ACTIVE_CONTAINER" == "app_blue" ]; then
    NEW_NAME="app_green"
    OLD_NAME="app_blue"
    IS_FIRST_DEPLOYMENT=false
elif [ "$LAST_ACTIVE_CONTAINER" == "app_green" ]; then
    NEW_NAME="app_blue"
    OLD_NAME="app_green"
    IS_FIRST_DEPLOYMENT=false
else
    # First time deployment
    NEW_NAME="app_blue"
    OLD_NAME=""
    IS_FIRST_DEPLOYMENT=true
    echo -e "${YELLOW}🆕 First time deployment detected.${NC}"
fi

echo -e "${BLUE}🚀 Deploying to: $NEW_NAME${NC}"

# =====================================================================
# 3. RUN NEW CONTAINER
# =====================================================================

echo -e "${YELLOW}📥 Pulling image...${NC}"
docker pull $DOCKER_IMAGE

echo -e "${YELLOW}🧹 Removing old target $NEW_NAME...${NC}"
docker rm -f $NEW_NAME 2>/dev/null || true

echo -e "${YELLOW}⚙️ Starting $NEW_NAME...${NC}"
# Gunakan --name yang sama dengan nama service di docker-compose
docker run -d \
  --name $NEW_NAME \
  --network $NETWORK_NAME \
  --restart unless-stopped \
  -e LARAVEL_ENV_ENCRYPTION_KEY="$LARAVEL_ENV_ENCRYPTION_KEY" \
  -e APP_ENV="$APP_ENV" \
  -v app_storage:/var/www/html/storage \
  $DOCKER_IMAGE

wait_app_ready $NEW_NAME || exit 1

# =====================================================================
# 4. OPTIMIZE APP
# =====================================================================

echo -e "${YELLOW}⚡ Optimizing Laravel...${NC}"
docker exec $NEW_NAME php artisan config:cache
docker exec $NEW_NAME php artisan route:cache
docker exec $NEW_NAME php artisan view:cache
docker exec $NEW_NAME php artisan storage:link --force || true
docker exec $NEW_NAME php artisan migrate --force

# =====================================================================
# 5. SWITCH TRAFFIC (NGINX)
# =====================================================================

echo -e "${YELLOW}🔄 Switching traffic in Nginx...${NC}"

# Backup config
docker exec ${PROJECT_NAME}_nginx cp /etc/nginx/conf.d/default.conf /tmp/default.conf.bak

# Logika Sed:
# 1. Hapus 'down' dari NEW_NAME
# 2. Tambah 'down' ke OLD_NAME
if [ "$IS_FIRST_DEPLOYMENT" = false ]; then
    docker exec ${PROJECT_NAME}_nginx sed -i "s/server ${NEW_NAME}:9000 down/server ${NEW_NAME}:9000/g" /etc/nginx/conf.d/default.conf
    docker exec ${PROJECT_NAME}_nginx sed -i "s/server ${OLD_NAME}:9000;/server ${OLD_NAME}:9000 down;/g" /etc/nginx/conf.d/default.conf
    # Pastikan tidak ada double semicolon
    docker exec ${PROJECT_NAME}_nginx sed -i "s/;;/;/g" /etc/nginx/conf.d/default.conf
fi

# Reload Nginx
if docker exec ${PROJECT_NAME}_nginx nginx -t >/dev/null 2>&1; then
    docker exec ${PROJECT_NAME}_nginx nginx -s reload
    echo -e "${GREEN}✅ Traffic switched to $NEW_NAME${NC}"
else
    echo -e "${RED}❌ Nginx config error, rolling back config...${NC}"
    docker exec ${PROJECT_NAME}_nginx cp /tmp/default.conf.bak /etc/nginx/conf.d/default.conf
    exit 1
fi

# =====================================================================
# 6. VERIFY & CLEANUP
# =====================================================================

echo -e "${YELLOW}🔍 Final Health Check...${NC}"
sleep 5
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/health || echo "000")

if [ "$HTTP_CODE" == "200" ]; then
    echo -e "${GREEN}✅ Deployment Successful (HTTP 200)${NC}"

    # Simpan state baru
    echo "LAST_ACTIVE_CONTAINER=\"$NEW_NAME\"" > "$STATE_FILE"

    # Hapus container lama jika ini bukan deployment pertama
    if [ "$IS_FIRST_DEPLOYMENT" = false ]; then
        echo -e "${YELLOW}🧹 Cleaning up $OLD_NAME...${NC}"
        docker stop $OLD_NAME 2>/dev/null || true
        docker rm $OLD_NAME 2>/dev/null || true
    fi
else
    echo -e "${RED}❌ Health check failed (HTTP $HTTP_CODE). Please check logs.${NC}"
    exit 1
fi

echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}   DEPLOYMENT COMPLETE: $NEW_NAME IS NOW ACTIVE${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
