#!/bin/bash
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}🚀 Container starting: $HOSTNAME${NC}"

# =====================================================================
# 1. DECRYPT ENV
# =====================================================================
if [ -f .env.production.encrypted ]; then
    echo -e "${YELLOW}🔐 Decrypting .env...${NC}"
    php artisan env:decrypt \
        --env=production \
        --key="$LARAVEL_ENV_ENCRYPTION_KEY" \
        --filename=.env \
        --force
    echo -e "${GREEN}✓ .env decrypted${NC}"
fi

# =====================================================================
# 2. WAIT FOR DB
# FIX: Gunakan loop dengan timeout yang jelas agar container tidak
#      hang selamanya kalau DB bermasalah.
# =====================================================================
echo -e "${YELLOW}⏳ Waiting for DB (db:3306)...${NC}"
MAX_TRIES=30
TRIES=0
until nc -z db 3306 2>/dev/null; do
    TRIES=$((TRIES + 1))
    if [ $TRIES -ge $MAX_TRIES ]; then
        echo -e "${RED}❌ DB tidak ready setelah ${MAX_TRIES} percobaan. Abort.${NC}"
        exit 1
    fi
    echo -e "${YELLOW}   Waiting... (${TRIES}/${MAX_TRIES})${NC}"
    sleep 2
done
echo -e "${GREEN}✓ DB ready${NC}"

# =====================================================================
# 3. MIGRATE
# Dijalankan di entrypoint sebagai fallback safety net.
# Deploy.sh juga menjalankan migrate setelah container ready,
# tapi ini memastikan container baru selalu dalam state yang benar.
# =====================================================================
echo -e "${YELLOW}🗄️ Running migrations...${NC}"
php artisan migrate --force || {
    echo -e "${RED}❌ Migration failed${NC}"
    exit 1
}
echo -e "${GREEN}✓ Migrations done${NC}"

# =====================================================================
# 4. LARAVEL CACHE
# Hanya generate kalau belum ada (misalnya di-generate deploy.sh).
# Cek config.php sebagai indikator cache sudah ada.
# =====================================================================
if [ ! -f bootstrap/cache/config.php ]; then
    echo -e "${YELLOW}⚡ Generating Laravel cache...${NC}"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo -e "${GREEN}✓ Cache generated${NC}"
else
    echo -e "${GREEN}✓ Cache already exists, skipping${NC}"
fi

# =====================================================================
# 5. STORAGE LINK
# =====================================================================
php artisan storage:link --force 2>/dev/null || true
echo -e "${GREEN}✓ Storage linked${NC}"

# =====================================================================
# 6. PERMISSIONS
# Pastikan permissions benar walau volume di-mount dari luar
# =====================================================================
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# =====================================================================
# 7. SLACK NOTIFY (optional)
# =====================================================================
if [ -n "${SLACK_WEBHOOK_URL:-}" ]; then
    curl -s -X POST \
      -H "Content-Type: application/json" \
      -d "{\"text\":\"✅ Container \`$HOSTNAME\` started (env: ${APP_ENV:-production})\"}" \
      "$SLACK_WEBHOOK_URL" > /dev/null || true
fi

echo -e "${GREEN}▶ Starting: $@${NC}"

# =====================================================================
# 8. START MAIN PROCESS
# =====================================================================
exec "$@"