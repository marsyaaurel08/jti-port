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
        // Bank BNI
        'bni_va' => [
            'ATM' => [
                'Pilih menu lainnya pada menu utama.',
                'Pilih transfer.',
                'Pilih ke rekening BNI.',
                'Masukkan nomor Virtual Account.',
                'Masukkan nominal pembayaran, lalu konfirmasi.',
                'Pembayaran selesai.'
            ],
            'Internet Banking' => [
                'Pilih transaksi, lalu info administrasi transfer.',
                'Pilih atur rekening tujuan.',
                'Masukkan info rekening, lalu Konfirmasi.',
                'Pilih transfer, lalu transfer ke rekening BNI.',
                'Masukkan detail pembayaran, lalu konfirmasi.',
                'Pembayaran selesai.'
            ],
            'Mobile Banking' => [
                'Pilih transfer.',
                'Pilih tagihan Virtual Account.',
                'Pilih rekening debit yang ingin Anda gunakan.',
                'Masukkan nomor Virtual Account, lalu konfirmasi.',
                'Pembayaran selesai.'
            ],
            'Melalui bank lain' => [
                'Pilih bank & saluran pembayaran pilihan Anda (ATM/internet/mobile banking).',
                'Pilih transfer ke bank lain.',
                'Masukkan nomor Virtual Account BNI.',
                'Masukkan nominal pembayaran, lalu konfirmasi.',
                'Pembayaran selesai.'
            ]
        ],

        // Bank BCA
        'bca_va' => [
            'ATM' => [
                'Pilih Transaksi Lainnya pada menu utama.',
                'Pilih Transfer.',
                'Pilih ke Rekening BCA Virtual Account.',
                'Masukkan nomor Virtual Account.',
                'Masukkan nominal pembayaran, lalu konfirmasi.',
                'Pembayaran selesai.'
            ],
            'Internet Banking (KlikBCA)' => [
                'Pilih Transfer Dana.',
                'Pilih Transfer ke BCA Virtual Account.',
                'Masukkan nomor Virtual Account.',
                'Masukkan nominal pembayaran, lalu konfirmasi.',
                'Pembayaran selesai.'
            ],
            'Mobile Banking (BCA Mobile)' => [
                'Pilih m-Transfer.',
                'Pilih BCA Virtual Account.',
                'Masukkan nomor Virtual Account, lalu konfirmasi.',
                'Masukkan PIN m-BCA Anda.',
                'Pembayaran selesai.'
            ],
            'Melalui bank lain' => [
                'Pilih bank & saluran pembayaran pilihan Anda (ATM/internet/mobile banking).',
                'Pilih transfer ke bank lain.',
                'Masukkan nomor Virtual Account BCA.',
                'Masukkan nominal pembayaran, lalu konfirmasi.',
                'Pembayaran selesai.'
            ]
        ],

        // Bank Mandiri (E-Channel) - Menggunakan Biller Code & Bill Key
        'echannel' => [
            'ATM' => [
                'Pilih Bayar/Beli pada menu utama.',
                'Pilih Lainnya, lalu pilih Multi Payment.',
                'Masukkan Kode Perusahaan (Biller Code).',
                'Masukkan No. VA (Bill Key).',
                'Konfirmasi pembayaran.',
                'Pembayaran selesai.'
            ],
            'Internet Banking' => [
                'Login ke Internet Banking Mandiri.',
                'Pilih menu Bayar, lalu pilih Multi Payment.',
                'Pilih penyedia jasa dan masukkan No. VA (Bill Key).',
                'Konfirmasi detail pembayaran.',
                'Pembayaran selesai.'
            ],
            'Mobile Banking (Livin\')' => [
                'Login ke aplikasi Livin\' by Mandiri.',
                'Pilih menu Bayar.',
                'Cari penyedia jasa (Biller Code) atau pilih Multi Payment.',
                'Masukkan No. VA (Bill Key) dan nominal.',
                'Konfirmasi dengan PIN Anda.',
                'Pembayaran selesai.'
            ],
            'Melalui bank lain' => [
                'Pilih bank & saluran pembayaran pilihan Anda.',
                'Pilih transfer ke bank lain (Mandiri).',
                'Masukkan nomor Virtual Account Mandiri.',
                'Masukkan nominal pembayaran, lalu konfirmasi.',
                'Pembayaran selesai.'
            ]
        ],

        // Bank BRI
        'bri_va' => [
            'ATM' => [
                'Pilih Transaksi Lain pada menu utama.',
                'Pilih Pembayaran, lalu pilih Lainnya.',
                'Pilih BRIVA.',
                'Masukkan nomor BRIVA (Virtual Account).',
                'Konfirmasi pembayaran Anda.',
                'Pembayaran selesai.'
            ],
            'Internet Banking' => [
                'Login ke Internet Banking BRI.',
                'Pilih menu Pembayaran & Pembelian.',
                'Pilih BRIVA.',
                'Masukkan nomor BRIVA, lalu konfirmasi.',
                'Pembayaran selesai.'
            ],
            'Mobile Banking (BRImo)' => [
                'Login ke aplikasi BRImo.',
                'Pilih menu BRIVA.',
                'Masukkan nomor BRIVA (Virtual Account).',
                'Konfirmasi detail dan masukkan PIN Anda.',
                'Pembayaran selesai.'
            ],
            'Melalui bank lain' => [
                'Pilih bank & saluran pembayaran pilihan Anda.',
                'Pilih transfer ke bank lain (BRI).',
                'Masukkan nomor Virtual Account BRI.',
                'Masukkan nominal pembayaran, lalu konfirmasi.',
                'Pembayaran selesai.'
            ]
        ],

        // Retail: Indomaret
        'indomaret' => [
            'Ketuk "Unduh info ini" atau simpan kode pembayaran Anda.',
            'Kunjungi gerai Indomaret terdekat dan tunjukkan kode pembayaran/barcode Anda kepada kasir.',
            'Kasir akan mengonfirmasi detail transaksi Anda.',
            'Lakukan pembayaran tunai atau non-tunai kepada kasir.',
            'Setelah transaksi berhasil, Anda akan menerima email/notifikasi konfirmasi pembayaran.',
            'Harap simpan struk pembayaran Anda sebagai bukti jika memerlukan bantuan lebih lanjut.'
        ],

        // Retail: Alfamart
        'alfamart' => [
            'Ketuk "Unduh info ini" atau simpan kode pembayaran Anda.',
            'Kunjungi gerai Alfamart terdekat dan tunjukkan kode pembayaran/barcode Anda kepada kasir.',
            'Kasir akan mengonfirmasi detail transaksi Anda.',
            'Lakukan pembayaran tunai atau non-tunai kepada kasir.',
            'Setelah transaksi berhasil, Anda akan menerima email/notifikasi konfirmasi pembayaran.',
            'Harap simpan struk pembayaran Anda sebagai bukti jika memerlukan bantuan lebih lanjut.'
        ],

        // E-Wallet: GoPay
        'gopay' => [
            'Buka aplikasi Gojek atau aplikasi e-wallet pendukung lainnya.',
            'Pilih menu Bayar / Scan.',
            'Scan QRIS yang muncul pada layar monitor Anda atau gunakan deeplink jika dari ponsel.',
            'Periksa kembali nominal pembayaran pada aplikasi.',
            'Konfirmasi pembayaran dengan PIN GoPay Anda.',
            'Pembayaran selesai.'
        ],

        // E-Wallet: ShopeePay
        'shopeepay' => [
            'Buka aplikasi Shopee.',
            'Pilih ikon scan/bayar pada menu ShopeePay.',
            'Scan QRIS yang muncul pada layar monitor Anda atau gunakan deeplink jika dari ponsel.',
            'Periksa kembali nominal pembayaran pada aplikasi.',
            'Konfirmasi pembayaran dengan PIN ShopeePay Anda.',
            'Pembayaran selesai.'
        ]
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
        'bni_va' => [
            'name' => 'BNI Virtual Account',
            'bank' => 'BNI',
            'type' => 'virtual_account',
            'description' => 'Transfer via BNI Mobile, Internet Banking, atau ATM',
            'logo' => 'icons/bni.png',
        ],

        // Bank BCA
        'bca_va' => [
            'name' => 'BCA Virtual Account',
            'bank' => 'BCA',
            'type' => 'virtual_account',
            'description' => 'Transfer via BCA Mobile, KlikBCA, atau ATM BCA',
            'logo' => 'icons/bca.png',
        ],

        // Bank Mandiri (Midtrans menggunakan key 'echannel' untuk Mandiri Bill)
        'echannel' => [
            'name' => 'Mandiri Bill Payment',
            'bank' => 'Mandiri',
            'type' => 'virtual_account',
            'description' => 'Transfer via Livin\' by Mandiri, Internet Banking, atau ATM',
            'logo' => 'icons/mandiri.png',
        ],

        // Bank BRI
        'bri_va' => [
            'name' => 'BRI Virtual Account',
            'bank' => 'BRI',
            'type' => 'virtual_account',
            'description' => 'Transfer via aplikasi BRImo, Internet Banking, atau ATM',
            'logo' => 'icons/bri.png',
        ],

        // Retail / Minimarket (Key sudah sesuai standar Midtrans)
        'indomaret' => [
            'name' => 'Indomaret',
            'bank' => null,
            'type' => 'retail',
            'description' => 'Bayar tunai di gerai Indomaret terdekat',
            'logo' => 'icons/indomaret.png',
        ],
        'alfamart' => [
            'name' => 'Alfamart',
            'bank' => null,
            'type' => 'retail',
            'description' => 'Bayar tunai di gerai Alfamart terdekat',
            'logo' => 'icons/alfamart.png',
        ],

        // Opsional: E-Wallet (Sangat disarankan untuk ditambahkan)
        'gopay' => [
            'name' => 'GoPay',
            'bank' => null,
            'type' => 'e_wallet',
            'description' => 'Bayar instan menggunakan saldo GoPay',
            'logo' => 'icons/gopay.png',
        ],
        'shopeepay' => [
            'name' => 'ShopeePay',
            'bank' => null,
            'type' => 'e_wallet',
            'description' => 'Bayar instan menggunakan saldo ShopeePay',
            'logo' => 'icons/spay.png',
        ],
    ],
];
