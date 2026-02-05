<?php

namespace App\Livewire;

use App\Models\Course;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CourseIndex extends Component
{
    use WithPagination;

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

    public function updating($name, $value)
    {
        if (str_contains($name, 'selected') || str_contains($name, 'is') || $name === 'sort') {
            $this->resetPage();
        }
    }

    public function updatedSelectedRatings()
    {
        $this->resetPage();
    }

    public function updatedSelectedLevels()
    {
        $this->resetPage();
    }

    public function updatedIsFree()
    {
        $this->resetPage();
    }

    public function updatedIsPaid()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->selectedCategories = [];
        $this->selectedRatings = [];
        $this->selectedLevels = [];
        $this->isFree = false;
        $this->isPaid = false;
        $this->sort = 'latest';

        $this->resetPage();
    }

    public function render()
    {
        $query = Course::query()->where('status', 'published');

        if (! empty($this->selectedRatings)) {
            $query->where(function ($q) {
                foreach ($this->selectedRatings as $rating) {
                    $q->orWhere('avg_rating', '>=', (float) $rating);
                }
            });
        }

        if (!empty($this->selectedCategories)) {
            $query->whereIn('category_id', $this->selectedCategories);
        }

        if (! empty($this->selectedLevels)) {
            $query->whereIn('level', $this->selectedLevels);
        }

        if ($this->isFree && ! $this->isPaid) {
            $query->where('price', 0);
        } elseif ($this->isPaid && ! $this->isFree) {
            $query->where('price', '>', 0);
        }

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

        return view('livewire.course-index', [
            'courses' => $query->paginate(10),
        ]);
    }
}
