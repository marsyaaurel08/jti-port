#!/bin/bash

# Parameter 1: Nama File (Default: .env)
# Parameter 2: Key/Password
FILE_IN=${1:-.env}
ENV_KEY=$2

if [ -z "$ENV_KEY" ]; then
    echo "❌ Error: Masukkan Key sebagai parameter kedua!"
    echo "Penggunaan: ./encrypt.sh [nama_file] [key]"
    exit 1
fi

if [ ! -f "$FILE_IN" ]; then
    echo "❌ Error: File '$FILE_IN' tidak ditemukan!"
    exit 1
fi

FILE_OUT="${FILE_IN}.enc"

# Proses Enkripsi
openssl aes-256-cbc -pbkdf2 -salt -in "$FILE_IN" -out "$FILE_OUT" -pass pass:"$ENV_KEY"

if [ $? -eq 0 ]; then
    echo "✅ Enkripsi Berhasil!"
    echo "Lokal: $FILE_IN -> Output: $FILE_OUT"
else
    echo "❌ Enkripsi Gagal."
    exit 1
fi
