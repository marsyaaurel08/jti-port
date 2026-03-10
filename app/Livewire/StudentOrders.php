<?php

namespace App\Livewire;

use App\Models\Order;
use App\Services\VABankService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class StudentOrders extends Component
{
    public ?object $user = null;

    public bool $showVAInstructions = false;

    public array $vaData = [];

    public ?Order $selectedOrder = null;

    public array $transferMethods = [
        'bni_mobile' => [
            'name' => 'BNI Mobile Banking',
            'bank' => 'BNI',
            'description' => 'Transfer via aplikasi BNI Mobile Banking',
        ],
        'bni_atm' => [
            'name' => 'BNI ATM',
            'bank' => 'BNI',
            'description' => 'Transfer via mesin ATM BNI',
        ],
        'bni_internet' => [
            'name' => 'BNI Internet Banking',
            'bank' => 'BNI',
            'description' => 'Transfer via website BNI',
        ],
        'bca_mobile' => [
            'name' => 'BCA Mobile Banking',
            'bank' => 'BCA',
            'description' => 'Transfer dari bank BCA - mBanking',
        ],
        'bca_atm' => [
            'name' => 'BCA ATM',
            'bank' => 'BCA',
            'description' => 'Transfer via mesin ATM BCA',
        ],
        'bca_internet' => [
            'name' => 'BCA Internet Banking',
            'bank' => 'BCA',
            'description' => 'Transfer via website BCA',
        ],
        'mandiri_mobile' => [
            'name' => 'Mandiri Mobile Banking',
            'bank' => 'Mandiri',
            'description' => 'Transfer via aplikasi Livin\' Mandiri',
        ],
        'mandiri_atm' => [
            'name' => 'Mandiri ATM',
            'bank' => 'Mandiri',
            'description' => 'Transfer via mesin ATM Mandiri',
        ],
        'bri_mobile' => [
            'name' => 'BRI Mobile Banking',
            'bank' => 'BRI',
            'description' => 'Transfer via aplikasi BRImo',
        ],
        'bri_atm' => [
            'name' => 'BRI ATM',
            'bank' => 'BRI',
            'description' => 'Transfer via mesin ATM BRI',
        ],
    ];

    public function mount(): void
    {
        if (! Auth::check()) {
            redirect()->route('home');
        }

        $this->user = Auth::user();
    }

    public function getTransferInstructions(?string $transferMethod = null): array
    {
        $method = $transferMethod ?? 'bni_mobile';

        $instructions = [
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
        ];

        return $instructions[$method] ?? [];
    }

    public function viewVAInstructions($orderId): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'pending') {
            session()->flash('error', 'Order ini sudah dibayar');

            return;
        }

        $this->selectedOrder = $order;
        $vaService = new VABankService;
        $this->vaData = $vaService->generateBNIVA($order);
        $this->showVAInstructions = true;
    }

    public function closeModal(): void
    {
        $this->showVAInstructions = false;
        $this->vaData = [];
        $this->selectedOrder = null;
    }

    public function render()
    {
        $unpaidOrders = Auth::user()
            ->orders()
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $transferInstructions = [];
        if ($this->selectedOrder) {
            $transferInstructions = $this->getTransferInstructions($this->selectedOrder->payment_method);
        }

        return view('livewire.student-orders', [
            'orders' => $unpaidOrders,
            'transferMethods' => $this->transferMethods,
            'transferInstructions' => $transferInstructions,
        ]);
    }
}
