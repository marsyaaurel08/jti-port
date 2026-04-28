#!/bin/bash

# Parameter 1: Nama File Encrypt (Contoh: .env.enc)
# Parameter 2: Key/Password
FILE_IN=$1
ENV_KEY=$2

if [ -z "$FILE_IN" ] || [ -z "$ENV_KEY" ]; then
    echo "❌ Error: Parameter tidak lengkap!"
    echo "Penggunaan: ./decrypt.sh [nama_file_encrypt] [key]"
    exit 1
fi

# Menghasilkan nama output dengan menghapus ekstensi .enc
# Contoh: .env.enc menjadi .env
FILE_OUT="${FILE_IN%.enc}"

# Proses Dekripsi
openssl aes-256-cbc -d -pbkdf2 -in "$FILE_IN" -out "$FILE_OUT" -pass pass:"$ENV_KEY"

if [ $? -eq 0 ]; then
    echo "✅ Dekripsi Berhasil!"
    echo "Input: $FILE_IN -> Output: $FILE_OUT"
else
    echo "❌ Dekripsi Gagal! Pastikan file benar dan Key tepat."
    exit 1
fi
