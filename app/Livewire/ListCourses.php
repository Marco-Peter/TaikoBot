<?php

namespace App\Livewire;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ListCourses extends Component
{
    public Collection $courses;

    public function mount()
    {
        $this->courses = Course::all();
    }

    public function render()
    {
        return view('livewire.list-courses');
    }
}
