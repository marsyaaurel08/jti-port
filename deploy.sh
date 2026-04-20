#!/bin/bash
set -e

# =====================================================================
# HYBRID DEPLOYMENT SCRIPT
# Auto-rollback untuk error teknis, Manual approval untuk production
# =====================================================================

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Konfigurasi
DOCKER_IMAGE="username/jtiport:latest"
NETWORK_NAME="jtiport_network"
PROJECT_NAME="jtiport"
BACKUP_DIR="/backups/jtiport"
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
    docker-compose up -d nginx db redis
    sleep 10
fi

# =====================================================================
# 2. BACKUP DATABASE (WAJIB!)
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
# 3. DETEKSI CURRENT ACTIVE
# =====================================================================

if docker exec ${PROJECT_NAME}_nginx grep -q "jtiport_blue:9000;" /etc/nginx/conf.d/default.conf 2>/dev/null; then
    NEW_NAME="jtiport_green"
    OLD_NAME="jtiport_blue"
    CURRENT_ACTIVE="blue"
else
    NEW_NAME="jtiport_blue"
    OLD_NAME="jtiport_green"
    CURRENT_ACTIVE="green"
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
  $DOCKER_IMAGE

# =====================================================================
# 5. HEALTH CHECK (Auto-rollback jika gagal)
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
        docker logs --tail 50 $NEW_NAME
        docker stop $NEW_NAME && docker rm $NEW_NAME
        echo -e "${YELLOW}Old container $OLD_NAME still running. No downtime.${NC}"
        exit 1
    fi
    echo -n "."
    sleep 2
done

# =====================================================================
# 6. RUN MIGRATIONS (Auto-rollback jika gagal)
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
# 7. BACKUP NGINX CONFIG
# =====================================================================

docker exec ${PROJECT_NAME}_nginx cp /etc/nginx/conf.d/default.conf /tmp/default.conf.bak

# =====================================================================
# 8. SWITCH TRAFFIC
# =====================================================================

echo -e "${YELLOW}🔄 Switching traffic from $OLD_NAME to $NEW_NAME...${NC}"
docker exec ${PROJECT_NAME}_nginx /bin/sh -c "
    sed -i 's/\($OLD_NAME:9000;\)/\1 down;/g' /etc/nginx/conf.d/default.conf
    sed -i 's/\($NEW_NAME:9000 down;\)/$NEW_NAME:9000;/g' /etc/nginx/conf.d/default.conf
"

# Test Nginx config
if docker exec ${PROJECT_NAME}_nginx nginx -t; then
    docker exec ${PROJECT_NAME}_nginx nginx -s reload
    echo -e "${GREEN}✅ Traffic switched!${NC}"
else
    echo -e "${RED}❌ Nginx config invalid! AUTO-ROLLBACK...${NC}"
    docker exec ${PROJECT_NAME}_nginx cp /tmp/default.conf.bak /etc/nginx/conf.d/default.conf
    docker exec ${PROJECT_NAME}_nginx nginx -s reload
    docker stop $NEW_NAME && docker rm $NEW_NAME
    exit 1
fi

# =====================================================================
# 9. VERIFY DEPLOYMENT (Auto-rollback jika tidak responsif)
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
# 10. MANUAL APPROVAL UNTUK PRODUCTION (Hybrid)
# =====================================================================

if [ "$APP_ENV" = "production" ]; then
    echo ""
    echo -e "${YELLOW}═══════════════════════════════════════════════════════════${NC}"
    echo -e "${YELLOW}⚠️  MANUAL APPROVAL REQUIRED FOR PRODUCTION ⚠️${NC}"
    echo -e "${YELLOW}═══════════════════════════════════════════════════════════${NC}"
    echo -e "New version deployed to: ${GREEN}$NEW_NAME${NC}"
    echo -e "Old version still available: ${YELLOW}$OLD_NAME${NC}"
    echo ""
    echo -e "Please test the new version:"
    echo -e "  ${BLUE}curl http://localhost:8000/health${NC}"
    echo -e "  ${BLUE}curl http://localhost:8000/your-test-endpoint${NC}"
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
# 11. CLEANUP OLD CONTAINER
# =====================================================================

echo -e "${YELLOW}🧹 Cleaning up old container: $OLD_NAME${NC}"
docker stop $OLD_NAME 2>/dev/null || true
docker rm $OLD_NAME 2>/dev/null || true

# =====================================================================
# 12. SAVE DEPLOYMENT INFO
# =====================================================================

cat > $BACKUP_DIR/last_deploy.txt << EOF
TIMESTAMP: $TIMESTAMP
NEW_CONTAINER: $NEW_NAME
OLD_CONTAINER: $OLD_NAME
PREVIOUS_ACTIVE: $CURRENT_ACTIVE
APP_ENV: $APP_ENV
DATE: $(date)
EOF

# =====================================================================
# 13. FINAL STATUS
# =====================================================================

echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅✅✅ DEPLOYMENT SUCCESSFUL! ✅✅✅${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}Active container: ${GREEN}$NEW_NAME${NC}"
echo -e "${BLUE}Backup: ${GREEN}$BACKUP_DIR/backup_${TIMESTAMP}.sql.gz${NC}"
echo ""

docker ps --format "table {{.Names}}\t{{.Status}}" | grep jtiport
