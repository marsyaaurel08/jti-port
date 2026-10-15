<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]

class StudentTransactions extends Component
{
    use WithPagination;

    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function transactions()
    {
        return Order::with([
            'orderItems.course',
            'orderItems.class',
        ])
            ->where('user_id', Auth::id())
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function sort($field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }

        $this->resetPage();
    }

    public function getStatusColor($status): string
    {
        return match ($status) {
            'paid' => 'green',
            'pending' => 'yellow',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getStatusLabel($status): string
    {
        return match ($status) {
            'paid' => 'Lunas',
            'pending' => 'Menunggu',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($status),
        };
    }

    public function render()
    {
        return view('livewire.student-transactions');
    }
}
