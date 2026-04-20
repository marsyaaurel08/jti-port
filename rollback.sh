#!/bin/bash
set -e

# =====================================================================
# MANUAL ROLLBACK SCRIPT
# Jalankan ini jika Anda menemukan bug logika setelah deploy
# =====================================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

BACKUP_DIR="/backups/jtiport"
PROJECT_NAME="jtiport"

echo -e "${YELLOW}═══════════════════════════════════════════════════════════${NC}"
echo -e "${YELLOW}     MANUAL ROLLBACK - $(date)${NC}"
echo -e "${YELLOW}═══════════════════════════════════════════════════════════${NC}"

# Load last deployment info
if [ -f "$BACKUP_DIR/last_deploy.txt" ]; then
    source $BACKUP_DIR/last_deploy.txt
    echo -e "${GREEN}Last deployment: $TIMESTAMP${NC}"
    echo -e "Rollback target: ${BLUE}$OLD_CONTAINER${NC}"
    echo -e "Container to remove: ${RED}$NEW_CONTAINER${NC}"
else
    echo -e "${RED}No deployment info found!${NC}"
    echo -e "Please specify container names manually:"
    read -p "Container to keep (old): " OLD_CONTAINER
    read -p "Container to remove (new): " NEW_CONTAINER
fi

# Check if old container exists
if ! docker ps -a | grep -q $OLD_CONTAINER; then
    echo -e "${RED}Old container $OLD_CONTAINER not found!${NC}"
    echo -e "Available containers:"
    docker ps -a --format "table {{.Names}}\t{{.Status}}" | grep jtiport
    exit 1
fi

# =====================================================================
# 1. BACKUP CURRENT NGINX CONFIG
# =====================================================================

echo -e "${YELLOW}💾 Backing up current Nginx config...${NC}"
docker exec ${PROJECT_NAME}_nginx cp /etc/nginx/conf.d/default.conf /tmp/default.conf.rollback.bak

# =====================================================================
# 2. START OLD CONTAINER IF STOPPED
# =====================================================================

if ! docker ps | grep -q $OLD_CONTAINER; then
    echo -e "${YELLOW}Starting old container: $OLD_CONTAINER${NC}"
    docker start $OLD_CONTAINER
    sleep 10
fi

# =====================================================================
# 3. SWITCH TRAFFIC BACK TO OLD CONTAINER
# =====================================================================

echo -e "${YELLOW}🔄 Switching traffic back to $OLD_CONTAINER...${NC}"
docker exec ${PROJECT_NAME}_nginx /bin/sh -c "
    sed -i 's/\($NEW_CONTAINER:9000;\)/\1 down;/g' /etc/nginx/conf.d/default.conf
    sed -i 's/\($OLD_CONTAINER:9000 down;\)/$OLD_CONTAINER:9000;/g' /etc/nginx/conf.d/default.conf
"

# Test and reload
if docker exec ${PROJECT_NAME}_nginx nginx -t; then
    docker exec ${PROJECT_NAME}_nginx nginx -s reload
    echo -e "${GREEN}✅ Traffic switched back!${NC}"
else
    echo -e "${RED}❌ Nginx config error! Restoring backup...${NC}"
    docker exec ${PROJECT_NAME}_nginx cp /tmp/default.conf.rollback.bak /etc/nginx/conf.d/default.conf
    docker exec ${PROJECT_NAME}_nginx nginx -s reload
    exit 1
fi

# =====================================================================
# 4. VERIFY ROLLBACK
# =====================================================================

echo -e "${YELLOW}🔍 Verifying rollback...${NC}"
sleep 5

HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/health 2>/dev/null || echo "000")

if [ "$HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✅ Application is healthy (HTTP 200)${NC}"
else
    echo -e "${RED}⚠️  Health check failed after rollback! Check manually.${NC}"
fi

# =====================================================================
# 5. STOP & REMOVE NEW (BROKEN) CONTAINER
# =====================================================================

echo -e "${YELLOW}🧹 Removing broken container: $NEW_CONTAINER${NC}"
docker stop $NEW_CONTAINER 2>/dev/null || true
docker rm $NEW_CONTAINER 2>/dev/null || true

# =====================================================================
# 6. RESTORE DATABASE IF NEEDED (Opsional)
# =====================================================================

echo ""
echo -e "${YELLOW}Do you want to restore database from backup?${NC}"
echo -e "  ${BLUE}Backup available: $BACKUP_DIR/backup_${TIMESTAMP}.sql.gz${NC}"
read -p "Restore database? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${YELLOW}📦 Restoring database...${NC}"
    gunzip < $BACKUP_DIR/backup_${TIMESTAMP}.sql.gz | docker exec -i ${PROJECT_NAME}_mysql mysql -uroot -proot
    echo -e "${GREEN}✅ Database restored${NC}"
fi

# =====================================================================
# 7. FINAL STATUS
# =====================================================================

echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅✅✅ ROLLBACK COMPLETE! ✅✅✅${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}Active container: $OLD_CONTAINER${NC}"
echo ""

docker ps --format "table {{.Names}}\t{{.Status}}" | grep jtiport
