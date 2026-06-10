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
SERVER=${2:-server-ihza}
DOCKER_IMAGE="${DOCKER_HUB_USERNAME}/jtiport:${IMAGE_TAG}"
NETWORK_NAME="jtiport_network"
PROJECT_NAME="jtiport"

# ==================== TAMBAHAN UNTUK MULTI SERVER ====================
NGINX_CONFIG_DIR=${NGINX_CONFIG_DIR:-server_ihza}

BACKUP_DIR="$HOME/backups/jtiport"
STATE_FILE="$BACKUP_DIR/deployment.state"
NGINX_CONF_DIR="./docker/nginx/${NGINX_CONFIG_DIR}"
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
# =====================================================================

if ! docker network inspect $NETWORK_NAME >/dev/null 2>&1; then
    echo -e "${YELLOW}🌐 Creating network: $NETWORK_NAME...${NC}"
    docker network create $NETWORK_NAME
fi

if ! docker ps | grep -q "${PROJECT_NAME}_mysql"; then
    echo -e "${YELLOW}🏗️ Starting infrastructure (DB, Redis)...${NC}"
    docker compose up -d db redis
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
ENV_OPTS=""
if [ -f ".env" ]; then
    ENV_OPTS="--env-file .env"
else
    ENV_OPTS="-e LARAVEL_ENV_ENCRYPTION_KEY=$LARAVEL_ENV_ENCRYPTION_KEY -e APP_ENV=$APP_ENV"
fi

docker run \
  --name $NEW_NAME \
  --network $NETWORK_NAME \
  --restart unless-stopped \
  -e IS_FIRST_DEPLOYMENT=$IS_FIRST_DEPLOYMENT \
  $ENV_OPTS \
  -v app_storage:/var/www/html/storage \
  $DOCKER_IMAGE
# CATATAN: app_public TIDAK di-mount ke container app (blue/green)
# karena public/ sudah ada di dalam image. Volume hanya dipakai
# nginx untuk serve static files — diisi di Step 6.

# Tunggu entrypoint selesai
echo -e "${YELLOW}⏳ Waiting for $NEW_NAME to be ready...${NC}"
for i in $(seq 1 60); do
    if docker exec $NEW_NAME test -f /tmp/.entrypoint-ready 2>/dev/null; then
        echo -e "${GREEN}✓ $NEW_NAME is ready (${i}s)${NC}"
        break
    fi
    if [ $i -eq 60 ]; then
        echo -e "${RED}❌ Container $NEW_NAME failed to start in time${NC}"
        docker logs $NEW_NAME --tail 20
        exit 1
    fi
    sleep 2
done

# =====================================================================
# 3. LARAVEL OPTIMIZATION
# =====================================================================

# Sudah dijalankan di entrypoint (migrate, cache, storage:link).

# =====================================================================
# 4. NGINX CONFIG & TRAFFIC SWITCH
# =====================================================================

echo -e "${YELLOW}🔄 Updating Nginx config to point to $NEW_NAME...${NC}"

sed -i "s/set \\\$backend \"${PROJECT_NAME}_[a-z]*:9000\";/set \$backend \"${NEW_NAME}:9000\";/" \
    "${NGINX_CONF_DIR}/default.conf"

echo -e "${GREEN}✓ Backend switched to $NEW_NAME${NC}"

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
    NGINX_CONFIG_DIR=$NGINX_CONFIG_DIR docker compose up -d nginx
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
docker compose up -d worker

# =====================================================================
# 6. SYNC PUBLIC ASSETS KE SHARED VOLUME
# =====================================================================

echo -e "${YELLOW}📦 Copying public assets to nginx...${NC}"

TEMP_PUBLIC_DIR="/tmp/jtiport_public_$TIMESTAMP"

# --entrypoint sh agar tidak trigger wait-for-db
docker run --rm \
    --entrypoint sh \
    -v "$TEMP_PUBLIC_DIR:/target" \
    "$DOCKER_IMAGE" \
    -c "cp -a /var/www/html/public/. /target/ && chown -R $(id -u):$(id -g) /target/"

if [ -d "$TEMP_PUBLIC_DIR" ] && [ "$(ls -A "$TEMP_PUBLIC_DIR")" ]; then

    # 1. Hapus symlink 'storage' dari temp folder agar tidak ikut tercopy
    # dan menimpa symlink storage yang sudah ada di Nginx
    rm -rf "$TEMP_PUBLIC_DIR/storage"

    docker exec ${PROJECT_NAME}_nginx mkdir -p /var/www/html/public

    # 2. Hapus isi folder public lama di Nginx KECUALI folder/symlink 'storage'.
    # Ini mencegah error "Read-only file system" yang terjadi akibat menghapus isi Docker Volume.
    docker exec ${PROJECT_NAME}_nginx sh -c 'cd /var/www/html/public && ls -A | grep -v "^storage$" | xargs -r rm -rf'

    # 3. Copy asset yang baru (js, css, index.php, dll) ke Nginx
    docker cp "$TEMP_PUBLIC_DIR/." "${PROJECT_NAME}_nginx:/var/www/html/public/"

    # 4. Pastikan symlink storage tetap ada dan terhubung dengan benar
    docker exec ${PROJECT_NAME}_nginx sh -c '[ ! -e /var/www/html/public/storage ] && ln -s /var/www/html/storage/app/public /var/www/html/public/storage || true'

    rm -rf "$TEMP_PUBLIC_DIR"

    if docker exec ${PROJECT_NAME}_nginx test -f /var/www/html/public/index.php; then
        echo -e "${GREEN}✓ Public assets copied to nginx${NC}"
    else
        echo -e "${RED}❌ Failed to copy public assets${NC}"
        exit 1
    fi
else
    echo -e "${RED}❌ Failed to extract public assets from image${NC}"
    exit 1
fi

docker exec ${PROJECT_NAME}_nginx nginx -s reload 2>/dev/null || true
echo -e "${GREEN}✓ Nginx reloaded${NC}"
# =====================================================================
# 7. CLEANUP OLD IMAGES
# =====================================================================

echo -e "${YELLOW}🧹 Cleaning up old images...${NC}"
docker image prune -f
docker images --format "{{.Repository}}:{{.Tag}} {{.ID}}" | \
    grep "${DOCKER_HUB_USERNAME}/jtiport" | \
    grep -v "$IMAGE_TAG" | \
    grep -v "latest" | \
    awk '{print $2}' | \
    xargs -r docker rmi || true
echo -e "${GREEN}✓ Old images removed${NC}"

echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ DEPLOYMENT SUCCESSFUL: $NEW_NAME IS ACTIVE${NC}"
echo -e "${GREEN}   Image   : $DOCKER_IMAGE${NC}"
echo -e "${GREEN}   Time    : $TIMESTAMP${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
