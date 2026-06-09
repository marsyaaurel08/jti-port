#!/bin/bash

# =============================================
# Encrypt Script - Laravel Environment
# =============================================
# Parameter 1 : Input file (default: .env)
# Parameter 2 : Encryption key / password
# Parameter 3 : Output file (opsional, default: [input_file].enc)

FILE_IN=${1:-.env}
ENV_KEY=$2
FILE_OUT=${3:-${FILE_IN}.enc}

# Validasi
if [ -z "$ENV_KEY" ]; then
    echo "❌ Error: Masukkan Encryption Key sebagai parameter kedua!"
    echo "Penggunaan: ./encrypt.sh [input_file] [key] [output_file]"
    echo "Contoh   : ./encrypt.sh .env.production MYSECRETKEY .env.production.jaghos.enc"
    exit 1
fi

if [ ! -f "$FILE_IN" ]; then
    echo "❌ Error: File input '$FILE_IN' tidak ditemukan!"
    exit 1
fi

# Proses Enkripsi
echo "🔐 Mengenkripsi: $FILE_IN → $FILE_OUT"
openssl aes-256-cbc -pbkdf2 -salt -in "$FILE_IN" -out "$FILE_OUT" -pass pass:"$ENV_KEY"

if [ $? -eq 0 ]; then
    echo "✅ Enkripsi Berhasil!"
    echo "   Input  : $FILE_IN"
    echo "   Output : $FILE_OUT"
    echo "   Ukuran : $(du -h "$FILE_OUT" | cut -f1)"
else
    echo "❌ Enkripsi Gagal."
    exit 1
fi
