<?php

namespace App\Livewire;

use App\Models\Course;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
class CourseSlider extends Component
{
    public $perPage = 6; // Ambil 6 data di awal (biar cepat)
    public $maxItems = 15; // Batas maksimal sesuai permintaan Anda

    public function loadMore()
    {
        if ($this->perPage < $this->maxItems) {
            $this->perPage += 6; // Tambah 6 data setiap kali scroll/klik

            // Pastikan tidak lewat dari 20
            if ($this->perPage > $this->maxItems) {
                $this->perPage = $this->maxItems;
            }
        }
    }

    public function render()
    {
        $allCourses = Cache::remember('course_slider_all', 600, function () {
            return Course::with('category')
                ->where('status', 'published')
                ->latest()
                ->take(15)
                ->get();
        });

        $courses = $allCourses->take($this->perPage);

        return view('livewire.course-slider', compact('courses'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex gap-4 overflow-hidden">
            @for($i=0; $i<6; $i++)
                <div class="shrink-0 w-[240px] bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm animate-pulse">
                    {{-- Skeleton Image --}}
                    <div class="h-32 bg-gray-200"></div>

                    {{-- Skeleton Content --}}
                    <div class="p-3.5 space-y-3 flex flex-col h-full">
                        {{-- Instructor --}}
                        <div class="h-2 bg-gray-200 rounded w-1/3"></div>

                        {{-- Title --}}
                        <div class="space-y-2">
                            <div class="h-3.5 bg-gray-200 rounded w-full"></div>
                            <div class="h-3.5 bg-gray-200 rounded w-5/6"></div>
                        </div>

                        {{-- Rating --}}
                        <div class="flex items-center gap-2 mt-2">
                            <div class="h-3 w-6 bg-gray-200 rounded"></div>
                            <div class="flex gap-1">
                                @for($j=0; $j<5; $j++)
                                    <div class="w-2.5 h-2.5 bg-gray-200 rounded-full"></div>
                                @endfor
                            </div>
                        </div>

                        {{-- Price & Button Section --}}
                        <div class="mt-auto pt-3 border-t border-gray-50">
                            <div class="flex justify-between items-center mb-3">
                                <div class="h-2 bg-gray-100 rounded w-1/4"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                            </div>
                            <div class="grid grid-cols-5 gap-1.5">
                                <div class="col-span-1 h-8 bg-gray-100 rounded-lg"></div>
                                <div class="col-span-4 h-8 bg-gray-200 rounded-lg"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    HTML;
    }
}
