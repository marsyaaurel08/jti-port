<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CourseIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $selectedCategories = [];

    #[Url(history: true)]
    public $selectedRatings = '';

    #[Url(history: true)]
    public $selectedLevels = [];

    #[Url(history: true)]
    public $isFree = false;

    #[Url(history: true)]
    public $isPaid = false;

    #[Url(history: true)]
    public $sort = 'latest';

    #[Url(history: true)]
    public $search = '';

    public function updatedSelectedCategories(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedRatings(): void
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

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->selectedCategories = [];
        $this->selectedRatings = '';
        $this->selectedLevels = [];
        $this->isFree = false;
        $this->isPaid = false;
        $this->sort = 'latest';
        $this->search = '';

        $this->resetPage();
    }

    private function getCacheKey(): string
    {
        return 'courses_' . md5(json_encode([
            'categories' => $this->selectedCategories,
            'ratings' => $this->selectedRatings,
            'levels' => $this->selectedLevels,
            'isFree' => $this->isFree,
            'isPaid' => $this->isPaid,
            'sort' => $this->sort,
            'search' => $this->search,
            'page' => $this->getPage(),
        ]));
    }


    public function render()
    {
        $cacheKey = $this->getCacheKey();

        $courses = Cache::remember($cacheKey, 600, function () {

            $query = Course::query()->where('status', 'published');

            if (! empty($this->search)) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"])
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                        });
                });
            }

            if (! empty($this->selectedRatings)) {
                $query->where('avg_rating', '>=', (float) $this->selectedRatings);
            }

            if (! empty($this->selectedCategories)) {
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

            return $query->paginate(10);
        });

        // Cache kategori (jarang berubah)
        $categories = Cache::remember('categories_all', 3600, function () {
            return Category::orderBy('name')->get();
        });

        return view('livewire.course-index', [
            'courses' => $courses,
            'categories' => $categories,
            'selectedCategories' => $this->selectedCategories,
            'selectedRatings' => $this->selectedRatings,
            'selectedLevels' => $this->selectedLevels,
            'isFree' => $this->isFree,
            'isPaid' => $this->isPaid,
            'sort' => $this->sort,
            'search' => $this->search,
        ]);
    }
}
