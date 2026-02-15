<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\CourseClass;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ClassesIndex extends Component
{
    use WithPagination;

    // Filter properties yang tersinkronisasi dengan URL
    #[Url(history: true)]
    public $selectedCategories = [];

    #[Url(history: true)]
    public $selectedRatings = [];

    #[Url(history: true)]
    public $selectedLevels = [];

    #[Url(history: true)]
    public $isFree = false;

    #[Url(history: true)]
    public $isPaid = false;

    #[Url(history: true)]
    public $sort = 'latest';

    public function updatedSelectedCategories(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedLevels(): void
    {
        $this->resetPage();
    }

    public function updatedIsFree(): void
    {
        $this->resetPage();
    }

    public function updatedIsPaid(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['selectedCategories', 'selectedRatings', 'selectedLevels', 'isFree', 'isPaid', 'sort']);
        $this->resetPage();
    }

    public function render()
    {
        // Eager loading instructor dan course untuk optimasi N+1 query
        $query = CourseClass::with(['instructor', 'course.category'])
            ->where('is_active', '1');

        // Filter Berdasarkan Kategori Course
        if (! empty($this->selectedCategories)) {
            $query->whereHas('course', function ($q) {
                $q->whereIn('category_id', $this->selectedCategories);
            });
        }

        // Filter Berdasarkan Level
        if (! empty($this->selectedLevels)) {
            $query->whereIn('level', $this->selectedLevels);
        }

        // Filter Berdasarkan Harga
        if ($this->isFree && ! $this->isPaid) {
            $query->where('price', 0);
        } elseif ($this->isPaid && ! $this->isFree) {
            $query->where('price', '>', 0);
        }

        // Sorting
        switch ($this->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        return view('livewire.classes-index', [
            'classes' => $query->paginate(10),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
