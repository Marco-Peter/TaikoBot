<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Livewire\Component;

class MyCourses extends Component
{
    public User $user;
    public $showingCourseInfo = false;

    public function mount(Request $request)
    {
        $this->user = $request->user();
    }

    public function showCourseInfo()
    {
        $this->showingCourseInfo = true;
    }

    public function signInToCourse(Course $course)
    {
        $this->showingCourseInfo = false;
        $course->add_participant($this->user);
    }

    public function signOutFromCourse(Course $course)
    {
        $this->showingCourseInfo = false;
        $course->remove_participant($this->user);
    }

    public function render()
    {
        return view('livewire.my-courses');
    }
}
