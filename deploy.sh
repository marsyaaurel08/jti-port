#!/bin/bash

# =====================================================================
# 1. INISIALISASI INFRASTRUKTUR (Auto-Create jika belum ada)
# =====================================================================

# Cek apakah network 'jtiport_network' sudah ada
if ! docker network inspect jtiport_network >/dev/null 2>&1; then
    echo "Network 'jtiport_network' tidak ditemukan. Membuat network baru..."
    docker network create jtiport_network
fi

# Cek apakah container Nginx sudah jalan
if [ ! "$(docker ps -q -f name=jtiport_nginx)" ]; then
    echo "Infrastruktur (Nginx/DB/Redis) belum jalan. Menjalankan docker-compose..."
    # Menjalankan compose dan menunggu sejenak agar DB & Nginx siap
    docker-compose up -d
    echo "Menunggu infrastruktur siap (10 detik)..."
    sleep 10
fi

# =====================================================================
# 2. DETEKSI STATUS FLIP-FLOP
# =====================================================================

# Cek siapa yang sedang aktif di config Nginx
# Jika ini pertama kali (file default.conf standar), kita asumsikan Blue yang akan dibuat
if docker exec jtiport_nginx grep -q "jtiport_blue:9000;" /etc/nginx/conf.d/default.conf 2>/dev/null; then
    NEW_NAME="jtiport_green"
    OLD_NAME="jtiport_blue"
else
    # Default jika belum ada config upstream atau Blue tidak aktif
    NEW_NAME="jtiport_blue"
    OLD_NAME="jtiport_green"
fi

echo "--- Deploying to $NEW_NAME ---"
# =====================================================================
# 3. PROSES PULL & RUN APLIKASI
# =====================================================================

# Gunakan nama image yang sama dengan yang di-push oleh GitHub Action
DOCKER_IMAGE="username/jtiport:latest"

echo "Pulling latest image dari Docker Hub..."
docker pull $DOCKER_IMAGE

# Bersihkan container target jika ada sisa
docker stop $NEW_NAME || true
docker rm $NEW_NAME || true

# Jalankan container menggunakan image hasil PULL
docker run -d \
  --name $NEW_NAME \
  --network jtiport_network \
  -e LARAVEL_ENV_ENCRYPTION_KEY="$LARAVEL_ENV_ENCRYPTION_KEY" \
  $DOCKER_IMAGE

# Tunggu proses Booting & Dekripsi Env
echo "Waiting for app to boot and decrypt .env..."
sleep 15

# =====================================================================
# 4. SWITCH TRAFFIC & CLEANUP
# =====================================================================

echo "Switching traffic from $OLD_NAME to $NEW_NAME..."

# Eksekusi penukaran status 'down' di config Nginx
docker exec jtiport_nginx sed -i "s/$OLD_NAME:9000;/$OLD_NAME:9000 down;/g" /etc/nginx/conf.d/default.conf
docker exec jtiport_nginx sed -i "s/$NEW_NAME:9000 down;/$NEW_NAME:9000;/g" /etc/nginx/conf.d/default.conf

# Reload Nginx tanpa memutus koneksi
docker exec jtiport_nginx nginx -s reload

# (Opsional) Matikan container lama untuk hemat resource
docker stop $OLD_NAME || true

echo "--- DEPLOY SUCCESS ---"
