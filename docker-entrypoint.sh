#!/bin/bash
set -e

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}🚀 Starting container: $HOSTNAME${NC}"

# Decrypt .env file (force overwrite setiap start)
if [ -f .env.encrypted ]; then
    echo -e "${YELLOW}🔐 Decrypting .env file...${NC}"
    php artisan env:decrypt --env=production --key=$LARAVEL_ENV_ENCRYPTION_KEY --filename=.env --force
    echo -e "${GREEN}✓ .env decrypted${NC}"
fi

# Optimasi untuk production
if [ "$APP_ENV" = "production" ]; then
    echo -e "${YELLOW}⚡ Caching configuration...${NC}"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo -e "${GREEN}✓ Cache created${NC}"
fi

# Run migrations jika diminta (opsional, biasanya via deploy.sh)
if [ "$AUTO_MIGRATE" = "true" ]; then
    echo -e "${YELLOW}📊 Running migrations...${NC}"
    php artisan migrate --force
    echo -e "${GREEN}✓ Migrations complete${NC}"
fi

# Kirim notifikasi ke Slack jika di-set (opsional)
if [ -n "$SLACK_WEBHOOK_URL" ]; then
    curl -s -X POST -H "Content-Type: application/json" \
        -d "{\"text\":\"✅ Container $HOSTNAME started on $(date)\"}" \
        $SLACK_WEBHOOK_URL > /dev/null 2>&1 || true
fi

# Jalankan perintah utama
echo -e "${GREEN}▶️  Starting: $@${NC}"
exec "$@"
