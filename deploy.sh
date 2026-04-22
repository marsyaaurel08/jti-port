#!/bin/bash
set -e

# =====================================================================
# HYBRID DEPLOYMENT SCRIPT - Blue-Green (Logika utama tetap sama)
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
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
APP_ENV="${APP_ENV:-production}"

echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}     Blue-Green Deployment - $(date)${NC}"
echo -e "${BLUE}     Environment: $APP_ENV${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"

# =====================================================================
# 1. INISIALISASI INFRASTRUKTUR
# =====================================================================

if ! docker network inspect $NETWORK_NAME >/dev/null 2>&1; then
    echo -e "${YELLOW}Creating network: $NETWORK_NAME${NC}"
    docker network create $NETWORK_NAME
fi

if [ ! "$(docker ps -q -f name=${PROJECT_NAME}_nginx)" ]; then
    echo -e "${YELLOW}Starting infrastructure...${NC}"
    docker-compose up -d db redis
    sleep 5

    # Tentukan container app mana yang harus running
    if docker ps -a | grep -q "${PROJECT_NAME}_blue"; then
        INIT_APP="${PROJECT_NAME}_blue"
    else
        INIT_APP="${PROJECT_NAME}_blue"
    fi

    echo -e "${YELLOW}Starting app container: $INIT_APP${NC}"
    if docker ps -a | grep -q "^${INIT_APP}$" 2>/dev/null; then
        docker start $INIT_APP || true
    else
        docker run -d \
          --name $INIT_APP \
          --network $NETWORK_NAME \
          --restart unless-stopped \
          -e LARAVEL_ENV_ENCRYPTION_KEY="$LARAVEL_ENV_ENCRYPTION_KEY" \
          -e APP_ENV="$APP_ENV" \
          -v app_storage:/var/www/html/storage \
          $DOCKER_IMAGE || true
    fi

    # WAIT untuk app container siap sebelum nginx start
    echo -e "${YELLOW}⏳ Waiting for app container to be ready...${NC}"
    for i in {1..60}; do
        if docker exec $INIT_APP php artisan --version >/dev/null 2>&1; then
            echo -e "${GREEN}✅ App container ready!${NC}"
            break
        fi
        if [ $i -eq 60 ]; then
            echo -e "${RED}❌ App container timeout!${NC}"
            exit 1
        fi
        echo -n "."
        sleep 2
    done

    sleep 3

    echo -e "${YELLOW}Starting Nginx...${NC}"
    docker-compose up -d nginx
    sleep 5
fi

# =====================================================================
# 2. BACKUP DATABASE
# =====================================================================

echo -e "${YELLOW}📦 Creating database backup...${NC}"
mkdir -p $BACKUP_DIR

if docker ps | grep -q "${PROJECT_NAME}_mysql"; then
    docker exec ${PROJECT_NAME}_mysql mysqldump -uroot -proot --all-databases | gzip > $BACKUP_DIR/backup_${TIMESTAMP}.sql.gz
    echo -e "${GREEN}✅ Database backup: backup_${TIMESTAMP}.sql.gz${NC}"
    find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +7 -delete
else
    echo -e "${RED}❌ Database not running! Aborting...${NC}"
    exit 1
fi

# =====================================================================
# 3. DETEKSI DEPLOYMENT PERTAMA vs UPDATE
# =====================================================================

BLUE_EXISTS=$(docker ps -a --filter name="${PROJECT_NAME}_blue" -q)
GREEN_EXISTS=$(docker ps -a --filter name="${PROJECT_NAME}_green" -q)

if [ -z "$BLUE_EXISTS" ] && [ -z "$GREEN_EXISTS" ]; then
    echo -e "${YELLOW}🆕 FIRST-TIME DEPLOYMENT DETECTED${NC}"
    IS_FIRST_DEPLOYMENT=true
    NEW_NAME="${PROJECT_NAME}_blue"
    OLD_NAME=""
    CURRENT_ACTIVE="none"
else
    echo -e "${YELLOW}♻️  UPDATING EXISTING DEPLOYMENT${NC}"
    IS_FIRST_DEPLOYMENT=false

    # Cek mana container yang aktif di nginx (bukan down)
    if docker exec ${PROJECT_NAME}_nginx grep "${PROJECT_NAME}_blue:9000" /etc/nginx/conf.d/default.conf 2>/dev/null | grep -v " down" > /dev/null; then
        NEW_NAME="${PROJECT_NAME}_green"
        OLD_NAME="${PROJECT_NAME}_blue"
        CURRENT_ACTIVE="blue"
    else
        NEW_NAME="${PROJECT_NAME}_blue"
        OLD_NAME="${PROJECT_NAME}_green"
        CURRENT_ACTIVE="green"
    fi
fi

echo -e "${GREEN}Current active: $CURRENT_ACTIVE${NC}"
echo -e "${BLUE}Deploying to: $NEW_NAME${NC}"

# =====================================================================
# 4. PULL & RUN NEW CONTAINER
# =====================================================================

echo -e "${YELLOW}📥 Pulling latest image...${NC}"
docker pull $DOCKER_IMAGE

docker stop $NEW_NAME 2>/dev/null || true
docker rm $NEW_NAME 2>/dev/null || true

echo -e "${YELLOW}🚀 Starting new container: $NEW_NAME${NC}"
docker run -d \
  --name $NEW_NAME \
  --network $NETWORK_NAME \
  --restart unless-stopped \
  -e LARAVEL_ENV_ENCRYPTION_KEY="$LARAVEL_ENV_ENCRYPTION_KEY" \
  -e APP_ENV="$APP_ENV" \
  -v app_storage:/var/www/html/storage \
  $DOCKER_IMAGE

# =====================================================================
# 5. SAFETY NET: Install dependencies & Cache (penting karena image sekarang sudah benar)
# =====================================================================

echo -e "${YELLOW}⚡ Running composer install & Laravel optimization...${NC}"

docker exec $NEW_NAME git config --global --add safe.directory /var/www/html

docker exec $NEW_NAME composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || echo -e "${YELLOW}Composer already up to date${NC}"

docker exec $NEW_NAME php artisan config:cache || true
docker exec $NEW_NAME php artisan route:cache || true
docker exec $NEW_NAME php artisan view:cache || true
docker exec $NEW_NAME php artisan storage:link || true

# =====================================================================
# 6. HEALTH CHECK (Auto-rollback jika gagal)
# =====================================================================

echo -e "${YELLOW}🏥 Waiting for container to be healthy...${NC}"
HEALTH_CHECK_MAX=30

for i in $(seq 1 $HEALTH_CHECK_MAX); do
    if docker exec $NEW_NAME php artisan --version >/dev/null 2>&1; then
        echo -e "${GREEN}✅ Container is ready!${NC}"
        break
    fi

    if [ $i -eq $HEALTH_CHECK_MAX ]; then
        echo -e "${RED}❌ Container failed to start! AUTO-ROLLBACK...${NC}"
        docker logs --tail 100 $NEW_NAME
        docker stop $NEW_NAME && docker rm $NEW_NAME
        echo -e "${YELLOW}Old container $OLD_NAME still running. No downtime.${NC}"
        exit 1
    fi
    echo -n "."
    sleep 3
done

# =====================================================================
# 7. RUN MIGRATIONS
# =====================================================================

echo -e "${YELLOW}📊 Running database migrations...${NC}"
if docker exec $NEW_NAME php artisan migrate --force; then
    echo -e "${GREEN}✅ Migrations successful${NC}"
else
    echo -e "${RED}❌ Migrations failed! AUTO-ROLLBACK...${NC}"
    docker stop $NEW_NAME && docker rm $NEW_NAME
    echo -e "${YELLOW}Rollback complete. Old container still running.${NC}"
    exit 1
fi

# =====================================================================
# 8. BACKUP NGINX CONFIG + SWITCH TRAFFIC
# =====================================================================

echo -e "${YELLOW}⏳ Waiting for Nginx to be ready...${NC}"
for i in {1..30}; do
    if docker ps | grep -q "${PROJECT_NAME}_nginx"; then
        break
    fi
    echo -n "."
    sleep 1
done

sleep 2

docker exec ${PROJECT_NAME}_nginx cp /etc/nginx/conf.d/default.conf /tmp/default.conf.bak 2>/dev/null || true

if [ "$IS_FIRST_DEPLOYMENT" = true ]; then
    echo -e "${YELLOW}🚀 First deployment - activating $NEW_NAME${NC}"
    # No changes needed for first deployment - config already has blue active
else
    echo -e "${YELLOW}🔄 Switching traffic from $OLD_NAME to $NEW_NAME...${NC}"
    docker exec ${PROJECT_NAME}_nginx sed -i "s/server ${OLD_NAME}:9000 /server ${OLD_NAME}:9000 down /g" /etc/nginx/conf.d/default.conf
    docker exec ${PROJECT_NAME}_nginx sed -i "s/server ${NEW_NAME}:9000 down /server ${NEW_NAME}:9000 /g" /etc/nginx/conf.d/default.conf
fi

sleep 2

if docker exec ${PROJECT_NAME}_nginx nginx -t >/dev/null 2>&1; then
    docker exec ${PROJECT_NAME}_nginx nginx -s reload
    echo -e "${GREEN}✅ Traffic configuration updated!${NC}"
    sleep 3
else
    echo -e "${RED}❌ Nginx config invalid! AUTO-ROLLBACK...${NC}"
    docker exec ${PROJECT_NAME}_nginx cp /tmp/default.conf.bak /etc/nginx/conf.d/default.conf 2>/dev/null || true
    docker exec ${PROJECT_NAME}_nginx nginx -s reload 2>/dev/null || true
    docker stop $NEW_NAME && docker rm $NEW_NAME
    exit 1
fi

# =====================================================================
# 9. VERIFY DEPLOYMENT (logika tetap sama)
# =====================================================================

echo -e "${YELLOW}🔍 Verifying deployment...${NC}"
sleep 5

HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/health 2>/dev/null || echo "000")

if [ "$HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✅ Application is healthy (HTTP 200)${NC}"
else
    echo -e "${RED}❌ Health check failed (HTTP $HTTP_CODE)! AUTO-ROLLBACK...${NC}"
    docker exec ${PROJECT_NAME}_nginx cp /tmp/default.conf.bak /etc/nginx/conf.d/default.conf
    docker exec ${PROJECT_NAME}_nginx nginx -s reload
    docker stop $NEW_NAME && docker rm $NEW_NAME
    echo -e "${YELLOW}Rollback complete.${NC}"
    exit 1
fi

# =====================================================================
# 9. MANUAL APPROVAL (skip untuk first deployment di dev/staging)
# =====================================================================

if [ "$APP_ENV" = "production" ] && [ "$IS_FIRST_DEPLOYMENT" = false ]; then
    echo ""
    echo -e "${YELLOW}═══════════════════════════════════════════════════════════${NC}"
    echo -e "${YELLOW}⚠️  MANUAL APPROVAL REQUIRED FOR PRODUCTION ⚠️${NC}"
    echo -e "${YELLOW}═══════════════════════════════════════════════════════════${NC}"
    echo -e "New version deployed to: ${GREEN}$NEW_NAME${NC}"
    echo -e "Old version still available: ${YELLOW}$OLD_NAME${NC}"
    echo ""
    echo -e "Please test the new version:"
    echo -e "  ${BLUE}curl http://localhost:8000/health${NC}"
    echo ""
    echo -e "${RED}If something is wrong, run: ./rollback.sh${NC}"
    echo ""

    read -p "✅ Keep this new version? (y/n) " -n 1 -r
    echo ""

    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}User rejected. Rolling back...${NC}"
        docker exec ${PROJECT_NAME}_nginx cp /tmp/default.conf.bak /etc/nginx/conf.d/default.conf
        docker exec ${PROJECT_NAME}_nginx nginx -s reload
        docker stop $NEW_NAME && docker rm $NEW_NAME
        echo -e "${RED}Rollback complete. Old version still active.${NC}"
        exit 1
    fi
fi

# =====================================================================
# 10. CLEANUP OLD CONTAINER (skip untuk first deployment)
# =====================================================================

if [ "$IS_FIRST_DEPLOYMENT" = false ] && [ -n "$OLD_NAME" ]; then
    echo -e "${YELLOW}🧹 Cleaning up old container: $OLD_NAME${NC}"
    docker stop $OLD_NAME 2>/dev/null || true
    docker rm $OLD_NAME 2>/dev/null || true
else
    echo -e "${YELLOW}✓ First deployment - no old container to cleanup${NC}"
fi

# =====================================================================
# 11. SAVE DEPLOYMENT INFO + FINAL STATUS
# =====================================================================

cat > $BACKUP_DIR/last_deploy.txt << EOF
TIMESTAMP: $TIMESTAMP
DEPLOYMENT_TYPE: $([ "$IS_FIRST_DEPLOYMENT" = true ] && echo "FIRST-TIME" || echo "UPDATE")
NEW_CONTAINER: $NEW_NAME
OLD_CONTAINER: ${OLD_NAME:-none}
PREVIOUS_ACTIVE: $CURRENT_ACTIVE
APP_ENV: $APP_ENV
DATE: $(date)
EOF

echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅✅✅ DEPLOYMENT SUCCESSFUL! ✅✅✅${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}Active container: ${GREEN}$NEW_NAME${NC}"
echo -e "${BLUE}Backup: ${GREEN}$BACKUP_DIR/backup_${TIMESTAMP}.sql.gz${NC}"
echo -e "${BLUE}Deployment type: $([ "$IS_FIRST_DEPLOYMENT" = true ] && echo "🆕 FIRST-TIME" || echo "♻️  UPDATE")${NC}"
echo ""

docker ps --format "table {{.Names}}\t{{.Status}}" | grep jtiport
