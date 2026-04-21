<?php

// File: config/constants.php
// Letakkan file ini di folder config/ Laravel Anda

return [
    /*
    |--------------------------------------------------------------------------
    | Instruksi Transfer Bank & Pembayaran
    |--------------------------------------------------------------------------
    |
    | Daftar langkah-langkah untuk setiap metode pembayaran
    |
    */

    'instructions' => [
        // ==================== BNI ====================
        'bni_mobile' => [
            '1. Buka aplikasi BNI Mobile Banking',
            '2. Masuk dengan nomor rekening dan password',
            '3. Pilih menu "Transfer" > "Ke Bank Lain"',
            '4. Masukkan nomor VA dan nominal yang tertera',
            '5. Verifikasi data transfer',
            '6. Masukkan PIN atau biometric untuk konfirmasi',
            '7. Transfer berhasil, cek status akun Anda',
        ],
        'bni_atm' => [
            '1. Masukkan kartu ATM BNI ke mesin ATM',
            '2. Masukkan PIN ATM Anda',
            '3. Pilih menu "Transfer"',
            '4. Pilih "Transfer ke Bank Lain"',
            '5. Masukkan kode bank tujuan dan nomor VA',
            '6. Masukkan nominal sesuai yang ditampilkan',
            '7. Verifikasi dan konfirmkan transaksi',
            '8. Ambil bukti transfer (struk)',
        ],
        'bni_internet' => [
            '1. Buka website https://ibank.bni.co.id',
            '2. Login dengan user ID dan password',
            '3. Masuk ke menu "Transfer" > "Domestic"',
            '4. Pilih "To Other Bank"',
            '5. Isi nomor VA dan nominal pembayaran',
            '6. Review data transfer dengan teliti',
            '7. Verifikasi dengan token BNI atau mPIN',
            '8. Proses transfer selesai',
        ],

        // ==================== BCA ====================
        'bca_mobile' => [
            '1. Buka aplikasi BCA Mobile atau M-BCA',
            '2. Login dengan username dan password',
            '3. Tap menu "m-Transfer"',
            '4. Pilih "Ke Bank Lain"',
            '5. Masukkan nomor VA penerima',
            '6. Verifikasi data, masukkan nominal',
            '7. Review rincian transfer',
            '8. Masukkan PIN atau gunakan biometric',
            '9. Transfer berhasil',
        ],
        'bca_atm' => [
            '1. Masukkan kartu ATM BCA ke mesin',
            '2. Pilih bahasa yang diinginkan',
            '3. Masukkan PIN ATM Anda',
            '4. Pilih "Transaksi Lain"',
            '5. Pilih "Transfer ke Bank Lain"',
            '6. Pilih "Input Rekening Tujuan"',
            '7. Masukkan nomor VA dan nominal',
            '8. Konfirmasi dan selesaikan transaksi',
        ],
        'bca_internet' => [
            '1. Buka website https://www.klikbca.com',
            '2. Login dengan user ID dan password',
            '3. Pilih "Pembayaran" > "Transfer Dana"',
            '4. Pilih "Ke Bank Lain (RTGS/SKN)"',
            '5. Input nomor VA dan data transfer',
            '6. Masukkan nominal sesuai yang ditampilkan',
            '7. Review kembali data transfer',
            '8. Verifikasi dengan Challenge dari SMS',
            '9. Transaksi berhasil',
        ],

        // ==================== MANDIRI ====================
        'mandiri_mobile' => [
            '1. Buka aplikasi Livin\' Mandiri',
            '2. Login dengan user ID dan password',
            '3. Navigasi ke "Transfer"',
            '4. Pilih "Transfer ke Bank Lain"',
            '5. Masukkan nomor VA dan nominal',
            '6. Verifikasi penerima dana',
            '7. Masukkan 6 digit PIN Livin\'',
            '8. Konfirmasi final transfer',
            '9. Transfer selesai, cek status transaksi',
        ],
        'mandiri_atm' => [
            '1. Masukkan kartu ATM Mandiri ke mesin',
            '2. Masukkan PIN ATM Anda',
            '3. Pilih "Transaksi Lainnya"',
            '4. Pilih "Transfer"',
            '5. Pilih "Ke Bank Lain"',
            '6. Input nomor rekening tujuan (VA)',
            '7. Masukkan jumlah transfer',
            '8. Konfirmasi dan masukkan PIN',
            '9. Transaksi berhasil',
        ],

        // ==================== BRI ====================
        'bri_mobile' => [
            '1. Buka aplikasi BRImo',
            '2. Login dengan username dan PIN',
            '3. Pilih "Transfer"',
            '4. Pilih "Transfer ke Rekening Bank Lain"',
            '5. Masukkan nomor VA penerima',
            '6. Masukkan nominal transfer',
            '7. Verifikasi detail transaksi',
            '8. Masukkan PIN BRImo Anda',
            '9. Transfer berhasil',
        ],
        'bri_atm' => [
            '1. Masukkan kartu ATM BRI ke mesin',
            '2. Masukkan PIN ATM Anda',
            '3. Pilih "MENU UTAMA"',
            '4. Pilih "TRANSFER"',
            '5. Pilih "Ke Bank Lain"',
            '6. Masukkan nomor VA dan nominal',
            '7. Konfirmasi data transfer',
            '8. Masukkan PIN untuk final',
            '9. Ambil struk bukti transfer',
        ],

        // ==================== CIMB Niaga ====================
        'cimb_mobile' => [
            '1. Buka aplikasi CIMB Niaga Mobile atau OCTO Mobile',
            '2. Login dengan user ID dan PIN',
            '3. Pilih menu "Transfer"',
            '4. Pilih "Transfer ke Bank Lain"',
            '5. Masukkan nomor VA tujuan',
            '6. Masukkan nominal pembayaran',
            '7. Verifikasi data transfer',
            '8. Masukkan PIN atau biometric',
            '9. Transfer berhasil',
        ],
        'cimb_atm' => [
            '1. Masukkan kartu ATM CIMB Niaga ke mesin',
            '2. Masukkan PIN ATM',
            '3. Pilih "Transfer"',
            '4. Pilih "Ke Bank Lain"',
            '5. Masukkan nomor VA',
            '6. Masukkan nominal',
            '7. Konfirmasi dan selesaikan',
        ],

        // ==================== DANAMON ====================
        'danamon_mobile' => [
            '1. Buka aplikasi D-Mobile',
            '2. Login dengan user ID dan password',
            '3. Pilih menu "Transfer"',
            '4. Pilih "Transfer ke Bank Lain"',
            '5. Masukkan nomor VA',
            '6. Masukkan nominal transfer',
            '7. Verifikasi dan konfirmasi',
            '8. Masukkan PIN transaksi',
            '9. Transfer selesai',
        ],

        // ==================== PERMATA ====================
        'permata_mobile' => [
            '1. Buka aplikasi PermataMobile X',
            '2. Login dengan PIN',
            '3. Pilih "Transfer"',
            '4. Pilih "Transfer ke Bank Lain"',
            '5. Masukkan nomor VA',
            '6. Masukkan nominal',
            '7. Konfirmasi transaksi',
            '8. Masukkan mPIN',
            '9. Transfer berhasil',
        ],

        // ==================== INDOMARET (Over the Counter) ====================
        'indomaret' => [
            '1. Datang ke gerai Indomaret terdekat',
            '2. Sampaikan kepada kasir bahwa Anda ingin membayar tagihan / pesanan online',
            '3. Sebutkan kode pembayaran / nomor VA yang tertera di halaman ini',
            '4. Kasir akan memasukkan kode pembayaran ke sistem',
            '5. Kasir akan menyebutkan nominal yang harus dibayar',
            '6. Lakukan pembayaran tunai sesuai nominal',
            '7. Kasir akan memproses pembayaran dan memberikan struk',
            '8. Simpan struk sebagai bukti pembayaran',
            '9. Upload bukti pembayaran (foto struk) ke halaman ini',
        ],

        // ==================== ALFAMART ====================
        'alfamart' => [
            '1. Datang ke gerai Alfamart terdekat',
            '2. Sampaikan ke kasir bahwa ingin membayar pesanan online',
            '3. Berikan kode pembayaran / nomor VA yang tertera',
            '4. Kasir akan memindai atau memasukkan kode',
            '5. Konfirmasi nominal pembayaran ke kasir',
            '6. Lakukan pembayaran tunai',
            '7. Kasir akan memberikan struk setelah pembayaran berhasil',
            '8. Pastikan status pada struk "LUNAS" atau "SUCCESS"',
            '9. Upload foto struk sebagai bukti pembayaran',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Metode Pembayaran
    |--------------------------------------------------------------------------
    |
    | Informasi detail tentang setiap metode pembayaran yang tersedia
    |
    */

    'transfer_methods' => [
        // Bank BNI
        'bni_mobile' => [
            'name' => 'BNI Mobile Banking',
            'bank' => 'BNI',
            'type' => 'mobile_banking',
            'description' => 'Transfer via aplikasi BNI Mobile Banking',
        ],
        'bni_atm' => [
            'name' => 'BNI ATM',
            'bank' => 'BNI',
            'type' => 'atm',
            'description' => 'Transfer via mesin ATM BNI',
        ],
        'bni_internet' => [
            'name' => 'BNI Internet Banking',
            'bank' => 'BNI',
            'type' => 'internet_banking',
            'description' => 'Transfer via website BNI',
        ],

        // Bank BCA
        'bca_mobile' => [
            'name' => 'BCA Mobile Banking',
            'bank' => 'BCA',
            'type' => 'mobile_banking',
            'description' => 'Transfer dari bank BCA - mBanking',
        ],
        'bca_atm' => [
            'name' => 'BCA ATM',
            'bank' => 'BCA',
            'type' => 'atm',
            'description' => 'Transfer via mesin ATM BCA',
        ],
        'bca_internet' => [
            'name' => 'BCA Internet Banking',
            'bank' => 'BCA',
            'type' => 'internet_banking',
            'description' => 'Transfer via website BCA (KlikBCA)',
        ],

        // Bank Mandiri
        'mandiri_mobile' => [
            'name' => 'Mandiri Mobile Banking',
            'bank' => 'Mandiri',
            'type' => 'mobile_banking',
            'description' => 'Transfer via aplikasi Livin\' Mandiri',
        ],
        'mandiri_atm' => [
            'name' => 'Mandiri ATM',
            'bank' => 'Mandiri',
            'type' => 'atm',
            'description' => 'Transfer via mesin ATM Mandiri',
        ],

        // Bank BRI
        'bri_mobile' => [
            'name' => 'BRI Mobile Banking',
            'bank' => 'BRI',
            'type' => 'mobile_banking',
            'description' => 'Transfer via aplikasi BRImo',
        ],
        'bri_atm' => [
            'name' => 'BRI ATM',
            'bank' => 'BRI',
            'type' => 'atm',
            'description' => 'Transfer via mesin ATM BRI',
        ],

        // Bank CIMB Niaga
        'cimb_mobile' => [
            'name' => 'CIMB Niaga Mobile',
            'bank' => 'CIMB Niaga',
            'type' => 'mobile_banking',
            'description' => 'Transfer via aplikasi OCTO Mobile',
        ],
        'cimb_atm' => [
            'name' => 'CIMB Niaga ATM',
            'bank' => 'CIMB Niaga',
            'type' => 'atm',
            'description' => 'Transfer via mesin ATM CIMB Niaga',
        ],

        // Bank Danamon
        'danamon_mobile' => [
            'name' => 'Danamon Mobile',
            'bank' => 'Danamon',
            'type' => 'mobile_banking',
            'description' => 'Transfer via aplikasi D-Mobile',
        ],

        // Bank Permata
        'permata_mobile' => [
            'name' => 'Permata Mobile',
            'bank' => 'Permata',
            'type' => 'mobile_banking',
            'description' => 'Transfer via aplikasi PermataMobile X',
        ],

        // Retail / Minimarket
        'indomaret' => [
            'name' => 'Indomaret',
            'bank' => null,
            'type' => 'retail',
            'description' => 'Bayar tunai di gerai Indomaret terdekat',
        ],
        'alfamart' => [
            'name' => 'Alfamart',
            'bank' => null,
            'type' => 'retail',
            'description' => 'Bayar tunai di gerai Alfamart terdekat',
        ],
    ],
];
