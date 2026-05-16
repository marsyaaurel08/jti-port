#!/bin/bash
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}🚀 Container starting: $HOSTNAME${NC}"

# =====================================================================
# 1. DECRYPT ENV (REMOVED)
# Env vars are now passed via docker run --env-file
# =====================================================================

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
# Bypass with IS_FIRST_DEPLOYMENT=1 (e.g., for CI/CD testing or first deploy).
# Non-fatal: warns and continues on failure instead of crashing.
# =====================================================================
if [ "${IS_FIRST_DEPLOYMENT:-}" = true ]; then
    echo -e "${YELLOW}⏭️ Running migrations (IS_FIRST_DEPLOYMENT=1)${NC}"
    php artisan migrate:fresh --seed --force || {
        echo -e "${RED}⚠️ Migration failed — continuing anyway${NC}"
    }
    echo -e "${YELLOW}⏭️ Running migrations (IS_FIRST_DEPLOYMENT=1)${NC}"
    php artisan shield:generate --all --panel=admin || {
        echo -e "${RED}⚠️ Migration failed — continuing anyway${NC}"
    }
    php artisan shield:super-admin --user=1 --panel=admin || {
        echo -e "${RED}⚠️ Migration failed — continuing anyway${NC}"
    }
else
    echo -e "${YELLOW}🗄️ Running migrations...${NC}"
    php artisan migrate --force || {
        echo -e "${RED}⚠️ Migration failed — continuing anyway${NC}"
    }
    echo -e "${GREEN}✓ Migrations done (or skipped)${NC}"
fi

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

# Mark entrypoint as complete (so deploy script can wait without spawning PHP)
touch /tmp/.entrypoint-ready

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

# Mark entrypoint as complete
touch /tmp/.entrypoint-ready

# =====================================================================
# 8. SETUP CRON JOB (Laravel Scheduler)
# =====================================================================
echo -e "${YELLOW}⏰ Setting up Laravel Scheduler (Cron)...${NC}"

cat > /etc/cron.d/laravel-scheduler << EOF
* * * * * www-data cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
EOF

chmod 0644 /etc/cron.d/laravel-scheduler
crontab -u www-data /etc/cron.d/laravel-scheduler

echo -e "${GREEN}✓ Laravel Scheduler cron installed${NC}"

# =====================================================================
# 9. START CRON + MAIN PROCESS
# =====================================================================
echo -e "${GREEN}▶ Starting cron and $@${NC}"

# Jalankan cron di background
crond
exec "$@"
