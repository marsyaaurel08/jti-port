#!/bin/bash

# Parameter 1: Nama File Encrypt (Contoh: .env.production.enc)
# Parameter 2: Key/


# Parameter 3: Nama File Output (Opsional, Contoh: .env)

FILE_IN=$1
ENV_KEY=$2
CUSTOM_OUT=$3

if [ -z "$FILE_IN" ] || [ -z "$ENV_KEY" ]; then
    echo "❌ Error: Parameter tidak lengkap!"
    echo "Penggunaan: ./decrypt.sh [nama_file_encrypt] [key] [nama_output_opsional]"
    exit 1
fi

# Tentukan nama file output
if [ -z "$CUSTOM_OUT" ]; then
    # Jika tidak ada parameter 3, hapus ekstensi .enc
    FILE_OUT="${FILE_IN%.enc}"
else
    # Jika ada parameter 3, gunakan nama tersebut
    FILE_OUT="$CUSTOM_OUT"
fi

# Proses Dekripsi
openssl aes-256-cbc -d -pbkdf2 -in "$FILE_IN" -out "$FILE_OUT" -pass pass:"$ENV_KEY"

if [ $? -eq 0 ]; then
    echo "✅ Dekripsi Berhasil!"
    echo "Input: $FILE_IN -> Output: $FILE_OUT"
else
    echo "❌ Dekripsi Gagal! Pastikan file benar dan Key tepat."
    exit 1
fi
