#!/bin/bash
set -euo pipefail

# =====================================================================
# HYBRID DEPLOYMENT SCRIPT - Jtiport (Blue-Green)
# =====================================================================

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Konfigurasi
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
echo -e "${BLUE}     Jtiport Blue-Green Deployment - $TIMESTAMP${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"

# =====================================================================
# 0. PRE-FLIGHT: NETWORK & INFRASTRUCTURE
# =====================================================================

# Pastikan network ada
if ! docker network inspect $NETWORK_NAME >/dev/null 2>&1; then
    echo -e "${YELLOW}🌐 Creating network: $NETWORK_NAME...${NC}"
    docker network create $NETWORK_NAME
fi

# Pastikan Nginx, DB, dan Redis menyala (Infrastruktur dasar)
if ! docker ps | grep -q "${PROJECT_NAME}_nginx"; then
    echo -e "${YELLOW}🏗️ Starting infrastructure (Nginx, DB, Redis)...${NC}"
    docker-compose up -d db redis nginx
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
# 2. RUN NEW CONTAINER
# =====================================================================

echo -e "${YELLOW}📥 Pulling image...${NC}"
docker pull $DOCKER_IMAGE

echo -e "${YELLOW}🧹 Removing old $NEW_NAME...${NC}"
docker rm -f $NEW_NAME 2>/dev/null || true

echo -e "${YELLOW}⚙️ Starting $NEW_NAME...${NC}"
docker run -d \
  --name $NEW_NAME \
  --network $NETWORK_NAME \
  --restart unless-stopped \
  -e LARAVEL_ENV_ENCRYPTION_KEY="$LARAVEL_ENV_ENCRYPTION_KEY" \
  -e APP_ENV="$APP_ENV" \
  -v app_storage:/var/www/html/storage \
  $DOCKER_IMAGE

# Tunggu container siap
echo -e "${YELLOW}⏳ Waiting for $NEW_NAME to be ready...${NC}"
sleep 10 # Memberi waktu PHP-FPM start

# =====================================================================
# 3. LARAVEL OPTIMIZATION
# =====================================================================

echo -e "${YELLOW}⚡ Running Laravel commands...${NC}"
docker exec $NEW_NAME php artisan config:cache
docker exec $NEW_NAME php artisan route:cache
docker exec $NEW_NAME php artisan view:cache
docker exec $NEW_NAME php artisan storage:link --force || true
docker exec $NEW_NAME php artisan migrate --force

# =====================================================================
# 4. SWITCH NGINX TRAFFIC
# =====================================================================

echo -e "${YELLOW}🔄 Switching traffic in Nginx...${NC}"

# Backup config
docker exec ${PROJECT_NAME}_nginx cp /etc/nginx/conf.d/default.conf /tmp/default.conf.bak

if [ "$IS_FIRST_DEPLOYMENT" = false ]; then
    # Hapus status 'down' dari container baru dan berikan ke container lama
    docker exec ${PROJECT_NAME}_nginx sed -i "s/server ${NEW_NAME}:9000 down/server ${NEW_NAME}:9000/g" /etc/nginx/conf.d/default.conf
    docker exec ${PROJECT_NAME}_nginx sed -i "s/server ${OLD_NAME}:9000;/server ${OLD_NAME}:9000 down;/g" /etc/nginx/conf.d/default.conf
fi

# Reload Nginx
if docker exec ${PROJECT_NAME}_nginx nginx -t >/dev/null 2>&1; then
    docker exec ${PROJECT_NAME}_nginx nginx -s reload
    echo -e "${GREEN}✅ Nginx reloaded. Traffic is now on $NEW_NAME${NC}"
else
    echo -e "${RED}❌ Nginx config error! Rolling back...${NC}"
    docker exec ${PROJECT_NAME}_nginx cp /tmp/default.conf.bak /etc/nginx/conf.d/default.conf
    exit 1
fi

# =====================================================================
# 5. VERIFY & SAVE STATE
# =====================================================================

echo "LAST_ACTIVE_CONTAINER=\"$NEW_NAME\"" > "$STATE_FILE"

if [ "$IS_FIRST_DEPLOYMENT" = false ]; then
    echo -e "${YELLOW}🧹 Cleaning up $OLD_NAME...${NC}"
    docker stop $OLD_NAME 2>/dev/null || true
    docker rm $OLD_NAME 2>/dev/null || true
fi

echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ DEPLOYMENT SUCCESSFUL: $NEW_NAME IS ACTIVE${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
