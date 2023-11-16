<?php

namespace App\Livewire;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyCourses extends Component
{
    public $user;
    public bool $courseInfoVisible = false;
    public Course $displayedCourse;
    public int $displayedCourse_id;
    public string $displayedCourse_name;
    public string $displayedCourse_description;
    public bool $displayedCourse_signedIn;

    public function render()
    {
        $this->user = Auth::user();
        $this->displayedCourse = Course::first();
        return view('livewire.my-courses');
    }

    public function showCourseInfo(int $course_id)
    {
        $this->displayedCourse = Course::find($course_id);
        $this->displayedCourse_id = $this->displayedCourse->id;
        $this->displayedCourse_name = $this->displayedCourse->name;
        $this->displayedCourse_description = $this->displayedCourse->description;
        $this->displayedCourse_signedIn = $this->user->courses->contains($this->displayedCourse);
        $this->courseInfoVisible = true;
    }

    public function signInToCourse(int $course_id)
    {
        $this->courseInfoVisible = false;
        Course::find($course_id)->add_participant(Auth::user());
    }

    public function signOutFromCourse(int $course_id)
    {
        $this->courseInfoVisible = false;
        Course::find($course_id)->remove_participant(Auth::user());
    }
}
