<?php

namespace App\Filament\Resources\Promos\Schemas;

use App\Models\{Course, CourseClass};
use Filament\Forms\Components\{DateTimePicker,Repeater,Select,Textarea,TextInput,Toggle};
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PromoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3) // Menggunakan total grid 3 kolom pembagi
                    ->schema([

                        // ================= KOLOM UTAMA (KIRI - 2 KOLOM) =================
                        Grid::make(1)
                            ->schema([

                                // Section 1: Atribut Dasar Promo
                                Section::make('Informasi Utama')
                                    ->description('Tentukan kode unik promo dan judul promosi.')
                                    ->icon('heroicon-o-ticket')
                                    ->schema([
                                        TextInput::make('code')
                                            ->label('Kode Promo')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->alphaNum()
                                            ->placeholder('CONTOH: RAMADAN2026'),

                                        TextInput::make('name')
                                            ->label('Nama Promo')
                                            ->required()
                                            ->placeholder('Diskon Berkah Ramadan'),

                                        Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->placeholder('Tuliskan syarat & ketentuan promo di sini...')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                // Section 2: Aturan Perhitungan Potongan
                                Section::make('Skema Potongan Harga')
                                    ->description('Atur jenis kalkulasi pemotongan harga beserta batas minimal transaksi.')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->schema([
                                        Select::make('type')
                                            ->label('Tipe Potongan')
                                            ->options([
                                                'percentage' => 'Persentase (%)',
                                                'fixed' => 'Nominal Tetap (Rp)',
                                            ])
                                            ->required()
                                            ->live()
                                            ->default('percentage'),

                                        TextInput::make('value')
                                            ->label('Nilai Potongan')
                                            ->required()
                                            ->numeric()
                                            ->prefix(fn(callable $get) => $get('type') === 'fixed' ? 'Rp' : null)
                                            ->suffix(fn(callable $get) => $get('type') === 'percentage' ? '%' : null)
                                            ->placeholder(fn(callable $get) => $get('type') === 'percentage' ? '10' : '50000'),

                                        TextInput::make('min_order')
                                            ->label('Minimal Pembelian')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->placeholder('0'),
                                    ])->columns(3),

                                // Section 3: Pembatasan Relasi Produk Eksklusif
                                Section::make('Cakupan Berlaku Produk')
                                    ->description('Tentukan apakah kupon berlaku global atau terbatas pada entitas tertentu.')
                                    ->icon('heroicon-o-shopping-bag')
                                    ->schema([
                                        Toggle::make('for_all_products')
                                            ->label('Berlaku untuk semua produk kursus & kelas')
                                            ->default(true)
                                            ->live()
                                            ->columnSpanFull(),

                                        Repeater::make('promoProducts')
                                            ->relationship('promoProducts')
                                            ->hidden(fn(callable $get) => $get('for_all_products') === true)
                                            ->schema([
                                                Select::make('type_product')
                                                    ->label('Jenis Item')
                                                    ->options([
                                                        'course' => '📚 Kursus (Course)',
                                                        'class' => '🏛️ Kelas (Class)',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn(callable $set) => $set('product_id', null)),

                                                Select::make('product_id')
                                                    ->label('Pilih Produk')
                                                    ->required()
                                                    ->disabled(fn(callable $get) => !$get('type_product'))
                                                    ->placeholder(fn(callable $get) => !$get('type_product') ? 'Pilih jenis item terlebih dahulu' : 'Cari produk...')
                                                    ->options(function (callable $get) {
                                                        $type = $get('type_product');
                                                        if (!$type) return [];

                                                        // Ambil data dinamis berdasarkan enum type_product
                                                        if ($type === 'course') {
                                                            return Course::pluck('title', 'id');
                                                        }
                                                        return CourseClass::pluck('title', 'id');
                                                    })
                                                    ->searchable()
                                                    ->preload(),
                                            ])
                                            ->columns(2)
                                            ->label('Pengecualian Item')
                                            ->createItemButtonLabel('Tambah Batasan Item Baru')
                                            ->columnSpanFull()
                                    ]),
                            ])
                            ->columnSpan(2),

                        // ================= KOLOM KONTROL (KANAN - 1 KOLOM SIDEBAR) =================
                        Grid::make(1)
                            ->schema([

                                // Sidebar Section 1: Validasi Waktu & Status Aktif
                                Section::make('Status & Masa Aktif')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Status Aktif')
                                            ->helperText('Matikan untuk menonaktifkan kupon instan.')
                                            ->default(true),

                                        DateTimePicker::make('start_date')
                                            ->label('Waktu Mulai')
                                            ->required()
                                            ->native(false),

                                        DateTimePicker::make('end_date')
                                            ->label('Waktu Berakhir')
                                            ->required()
                                            ->native(false)
                                            ->after('start_date'),
                                    ]),

                                // Sidebar Section 2: Manajemen Batas Kuota
                                Section::make('Limitasi Penggunaan')
                                    ->icon('heroicon-o-user-group')
                                    ->schema([
                                        TextInput::make('max_usage')
                                            ->label('Total Kuota Global')
                                            ->numeric()
                                            ->placeholder('Tak terbatas')
                                            ->helperText('Kosongkan jika kuota tidak dibatasi'),

                                        TextInput::make('max_usage_per_user')
                                            ->label('Batas per Pengguna')
                                            ->numeric()
                                            ->required()
                                            ->default(1),
                                    ]),
                            ])
                            ->columnSpan(1),

                    ])->columnSpanFull(),
            ]);
    }
}
