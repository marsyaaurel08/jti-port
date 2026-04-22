#!/bin/bash
set -euo pipefail

# =====================================================================
# HYBRID DEPLOYMENT SCRIPT - Blue-Green (FIXED & ROBUST)
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

# Error handling dengan trap
trap 'handle_error $? $LINENO' ERR
handle_error() {
    local exit_code=$1
    local line_num=$2
    echo -e "${RED}❌ ERROR at line $line_num (exit code: $exit_code)${NC}"
    exit $exit_code
}

echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}     Blue-Green Deployment - $(date)${NC}"
echo -e "${BLUE}     Environment: $APP_ENV${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"

# =====================================================================
# 0. UTILITY FUNCTIONS
# =====================================================================

wait_mysql_ready() {
    echo -e "${YELLOW}⏳ Waiting for MySQL to be ready...${NC}"
    for i in {1..60}; do
        if docker exec ${PROJECT_NAME}_mysql mysqladmin ping -h localhost -uroot -proot >/dev/null 2>&1; then
            echo -e "${GREEN}✅ MySQL ready!${NC}"
            return 0
        fi
        echo -n "."
        sleep 2
    done
    echo -e "${RED}❌ MySQL timeout!${NC}"
    return 1
}

wait_nginx_ready() {
    echo -e "${YELLOW}⏳ Waiting for Nginx to be ready...${NC}"
    for i in {1..30}; do
        if docker ps | grep -q "${PROJECT_NAME}_nginx" && \
           docker exec ${PROJECT_NAME}_nginx nginx -t >/dev/null 2>&1; then
            echo -e "${GREEN}✅ Nginx ready!${NC}"
            return 0
        fi
        echo -n "."
        sleep 1
    done
    echo -e "${RED}❌ Nginx timeout!${NC}"
    return 1
}

wait_app_ready() {
    local container=$1
    echo -e "${YELLOW}⏳ Waiting for $container to be ready...${NC}"
    for i in {1..60}; do
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

rollback_deployment() {
    local new_container=$1
    local old_container=$2

    echo -e "${RED}🔄 ROLLBACK in progress...${NC}"

    if [ -f /tmp/default.conf.bak ]; then
        docker exec ${PROJECT_NAME}_nginx cp /tmp/default.conf.bak /etc/nginx/conf.d/default.conf 2>/dev/null || true
        docker exec ${PROJECT_NAME}_nginx nginx -s reload 2>/dev/null || true
        echo -e "${YELLOW}✓ Nginx config restored${NC}"
    fi

    docker stop $new_container 2>/dev/null || true
    sleep 1
    docker rm -f $new_container 2>/dev/null || true

    echo -e "${RED}Rollback complete. Old container still running.${NC}"
}

# =====================================================================
# 1. LOAD DEPLOYMENT STATE
# =====================================================================

mkdir -p $BACKUP_DIR

if [ -f "$STATE_FILE" ]; then
    source "$STATE_FILE" 2>/dev/null || true
    echo -e "${YELLOW}📋 Previous deployment state loaded${NC}"
else
    echo -e "${YELLOW}🆕 No previous deployment state${NC}"
fi

if ! docker network inspect $NETWORK_NAME >/dev/null 2>&1; then
    echo -e "${YELLOW}Creating network: $NETWORK_NAME${NC}"
    docker network create $NETWORK_NAME
fi

# Start infrastructure jika belum ada
if ! docker ps | grep -q "${PROJECT_NAME}_nginx"; then
    echo -e "${YELLOW}Starting infrastructure (first time)...${NC}"

    # Cleanup stuck containers
    docker stop ${PROJECT_NAME}_blue ${PROJECT_NAME}_green 2>/dev/null || true
    docker rm -f ${PROJECT_NAME}_blue ${PROJECT_NAME}_green 2>/dev/null || true

    # Start database & redis
    echo -e "${YELLOW}📥 Starting database & redis...${NC}"
    docker-compose up -d db redis
    wait_mysql_ready || exit 1

    # Start initial app container
    INIT_APP="${PROJECT_NAME}_blue"
    echo -e "${YELLOW}Starting initial app container: $INIT_APP${NC}"
    docker run -d \
      --name $INIT_APP \
      --network $NETWORK_NAME \
      --restart unless-stopped \
      -e LARAVEL_ENV_ENCRYPTION_KEY="$LARAVEL_ENV_ENCRYPTION_KEY" \
      -e APP_ENV="$APP_ENV" \
      -v app_storage:/var/www/html/storage \
      $DOCKER_IMAGE

    wait_app_ready $INIT_APP || exit 1
    sleep 3

    # Start nginx
    echo -e "${YELLOW}Starting Nginx...${NC}"
    docker-compose up -d nginx
    wait_nginx_ready || exit 1
fi

# =====================================================================
# 2. BACKUP DATABASE
# =====================================================================

echo -e "${YELLOW}📦 Creating database backup...${NC}"
if ! docker exec ${PROJECT_NAME}_mysql mysqldump -uroot -proot --all-databases 2>/dev/null | \
     gzip > $BACKUP_DIR/backup_${TIMESTAMP}.sql.gz; then
    echo -e "${RED}❌ Database backup failed!${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Database backup: backup_${TIMESTAMP}.sql.gz${NC}"

# Cleanup old backups (keep 7 days)
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +7 -delete

# =====================================================================
# 3. DETECT DEPLOYMENT TYPE (WITH STATE TRACKING)
# =====================================================================

BLUE_EXISTS=$(docker ps -a --filter name="${PROJECT_NAME}_blue" -q 2>/dev/null || echo "")
GREEN_EXISTS=$(docker ps -a --filter name="${PROJECT_NAME}_green" -q 2>/dev/null || echo "")

# Determine if FIRST deployment by checking state file (NOT by container existence)
if [ -z "$LAST_ACTIVE_CONTAINER" ]; then
    IS_FIRST_DEPLOYMENT=true
    NEW_NAME="${PROJECT_NAME}_blue"
    OLD_NAME=""
    CURRENT_ACTIVE="none"
    echo -e "${YELLOW}🆕 FIRST-TIME DEPLOYMENT DETECTED${NC}"
else
    IS_FIRST_DEPLOYMENT=false

    # Check nginx config untuk detect active container
    if wait_nginx_ready 2>/dev/null; then
        if docker exec ${PROJECT_NAME}_nginx grep "${PROJECT_NAME}_blue:9000" /etc/nginx/conf.d/default.conf 2>/dev/null | \
           grep -v " down" >/dev/null 2>&1; then
            NEW_NAME="${PROJECT_NAME}_green"
            OLD_NAME="${PROJECT_NAME}_blue"
            CURRENT_ACTIVE="blue"
        else
            NEW_NAME="${PROJECT_NAME}_blue"
            OLD_NAME="${PROJECT_NAME}_green"
            CURRENT_ACTIVE="green"
        fi
    else
        echo -e "${RED}❌ Cannot determine active container (Nginx not responding)${NC}"
        exit 1
    fi

    echo -e "${YELLOW}♻️  UPDATING EXISTING DEPLOYMENT${NC}"
fi

echo -e "${GREEN}Current active: $CURRENT_ACTIVE${NC}"
echo -e "${BLUE}Deploying to: $NEW_NAME${NC}"

# =====================================================================
# 4. PULL IMAGE & PREPARE NEW CONTAINER
# =====================================================================

echo -e "${YELLOW}📥 Pulling latest image...${NC}"
docker pull $DOCKER_IMAGE

echo -e "${YELLOW}🧹 Cleaning up any existing $NEW_NAME container...${NC}"
docker stop $NEW_NAME 2>/dev/null || true
sleep 1
docker rm -f $NEW_NAME 2>/dev/null || true
sleep 1

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
# 5. SETUP & OPTIMIZE APPLICATION
# =====================================================================

wait_app_ready $NEW_NAME || {
    rollback_deployment "$NEW_NAME" "$OLD_NAME"
    exit 1
}

echo -e "${YELLOW}⚡ Running composer install & Laravel optimization...${NC}"
docker exec $NEW_NAME git config --global --add safe.directory /var/www/html
docker exec $NEW_NAME composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist 2>/dev/null || \
    echo -e "${YELLOW}Composer already up to date${NC}"

docker exec $NEW_NAME php artisan config:cache || true
docker exec $NEW_NAME php artisan route:cache || true
docker exec $NEW_NAME php artisan view:cache || true
docker exec $NEW_NAME php artisan storage:link || true

# =====================================================================
# 6. RUN MIGRATIONS
# =====================================================================

echo -e "${YELLOW}📊 Running database migrations...${NC}"
if ! docker exec $NEW_NAME php artisan migrate --force 2>&1; then
    echo -e "${RED}❌ Migrations failed!${NC}"
    rollback_deployment "$NEW_NAME" "$OLD_NAME"
    exit 1
fi
echo -e "${GREEN}✅ Migrations successful${NC}"

# =====================================================================
# 7. BACKUP NGINX CONFIG & SWITCH TRAFFIC
# =====================================================================

wait_nginx_ready || exit 1

sleep 2
docker exec ${PROJECT_NAME}_nginx cp /etc/nginx/conf.d/default.conf /tmp/default.conf.bak || true

if [ "$IS_FIRST_DEPLOYMENT" = true ]; then
    echo -e "${YELLOW}🚀 First deployment - activating $NEW_NAME${NC}"
    # Config already has blue active, no changes needed
else
    echo -e "${YELLOW}🔄 Switching traffic from $OLD_NAME to $NEW_NAME...${NC}"
    docker exec ${PROJECT_NAME}_nginx sed -i "s/server ${OLD_NAME}:9000 /server ${OLD_NAME}:9000 down /g" \
        /etc/nginx/conf.d/default.conf
    docker exec ${PROJECT_NAME}_nginx sed -i "s/server ${NEW_NAME}:9000 down /server ${NEW_NAME}:9000 /g" \
        /etc/nginx/conf.d/default.conf
fi

sleep 1

if docker exec ${PROJECT_NAME}_nginx nginx -t >/dev/null 2>&1; then
    docker exec ${PROJECT_NAME}_nginx nginx -s reload
    echo -e "${GREEN}✅ Nginx config updated${NC}"
    sleep 3
else
    echo -e "${RED}❌ Nginx config invalid!${NC}"
    docker exec ${PROJECT_NAME}_nginx cp /tmp/default.conf.bak /etc/nginx/conf.d/default.conf 2>/dev/null || true
    docker exec ${PROJECT_NAME}_nginx nginx -s reload 2>/dev/null || true
    rollback_deployment "$NEW_NAME" "$OLD_NAME"
    exit 1
fi

# =====================================================================
# 8. VERIFY DEPLOYMENT
# =====================================================================

echo -e "${YELLOW}🔍 Verifying deployment (HTTP health check)...${NC}"
sleep 5

HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/health 2>/dev/null || echo "000")

if [ "$HTTP_CODE" != "200" ]; then
    echo -e "${RED}❌ Health check failed (HTTP $HTTP_CODE)!${NC}"
    rollback_deployment "$NEW_NAME" "$OLD_NAME"
    exit 1
fi

echo -e "${GREEN}✅ Application is healthy (HTTP 200)${NC}"

# =====================================================================
# 9. MANUAL APPROVAL REQUIRED FOR PRODUCTION
# =====================================================================

if [ "$APP_ENV" = "production" ]; then
    echo ""
    echo -e "${YELLOW}═══════════════════════════════════════════════════════════${NC}"
    echo -e "${YELLOW}⚠️  MANUAL APPROVAL REQUIRED FOR PRODUCTION ⚠️${NC}"
    echo -e "${YELLOW}═══════════════════════════════════════════════════════════${NC}"
    echo ""
    echo "Deployment Details:"
    echo "  Container: $NEW_NAME"
    echo "  Image: $DOCKER_IMAGE"
    echo "  Backup: backup_${TIMESTAMP}.sql.gz"
    echo ""

    if [ "$IS_FIRST_DEPLOYMENT" = true ]; then
        echo -e "${YELLOW}This is a FIRST-TIME deployment.${NC}"
    else
        echo -e "${YELLOW}Switching traffic from $OLD_NAME to $NEW_NAME${NC}"
    fi

    echo ""
    read -p "Do you want to FINALIZE this deployment? (yes/no): " CONFIRM

    if [ "$CONFIRM" != "yes" ]; then
        echo -e "${RED}❌ Deployment REJECTED. Performing rollback...${NC}"
        rollback_deployment "$NEW_NAME" "$OLD_NAME"
        exit 1
    fi

    echo -e "${GREEN}✅ Deployment APPROVED${NC}"
fi
    echo -e "${YELLOW}═══════════════════════════════════════════════════════════${NC}"
    echo -e "${YELLOW}New version deployed to: ${GREEN}$NEW_NAME${NC}"
    echo -e "${YELLOW}Old version still available: ${OLD_NAME}${NC}"
    echo ""

# =====================================================================
# 10. CLEANUP OLD CONTAINER & SAVE STATE
# =====================================================================

echo -e "${YELLOW}🧹 Cleaning up resources...${NC}"

# Don't cleanup if it's first deployment (only one container running)
if [ "$IS_FIRST_DEPLOYMENT" = false ] && [ -n "$OLD_NAME" ]; then
    sleep 2
    docker stop $OLD_NAME 2>/dev/null || true
    sleep 1
    docker rm -f $OLD_NAME 2>/dev/null || true
    echo -e "${GREEN}✅ Old container cleaned up${NC}"
fi

# Save deployment state to file (for next deployment detection)
echo "LAST_ACTIVE_CONTAINER=\"$NEW_NAME\"" > "$STATE_FILE"
echo "LAST_DEPLOYMENT_TYPE=\"$([ "$IS_FIRST_DEPLOYMENT" = true ] && echo 'first' || echo 'update')\"" >> "$STATE_FILE"
echo "LAST_DEPLOYMENT_TIME=\"$TIMESTAMP\"" >> "$STATE_FILE"
echo "LAST_DEPLOYMENT_IMAGE=\"$DOCKER_IMAGE\"" >> "$STATE_FILE"
chmod 600 "$STATE_FILE"
echo -e "${GREEN}✅ Deployment state saved to $STATE_FILE${NC}"

# =====================================================================
# 11. SUMMARY
# =====================================================================

echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ DEPLOYMENT SUCCESSFUL${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo ""
echo "Summary:"
echo "  Active Container: $NEW_NAME"
echo "  Image: $DOCKER_IMAGE"
echo "  Environment: $APP_ENV"
echo "  Deployment Type: $([ "$IS_FIRST_DEPLOYMENT" = true ] && echo 'First Time' || echo 'Update')"
echo ""
echo -e "Check health: ${BLUE}curl http://localhost:8000/health${NC}"
echo -e "View logs: ${BLUE}docker logs -f $NEW_NAME${NC}"
echo ""
echo -e "${GREEN}Deployment completed at $(date)${NC}"
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
    sleep 1
    docker rm -f $OLD_NAME 2>/dev/null || true
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
