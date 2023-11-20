<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Lesson;
use DateTime;
use Livewire\Component;
use Livewire\Attributes\Rule;

class EditCourse extends Component
{
    public Course $course;
    public Team $teams;

    #[Rule('required|string|min:3')]
    public string $name = '';

    #[Rule('required|min:3')]
    public string $description = '';

    #[Rule('required|int')]
    public int $capacity = 0;

    #[Rule('required|string|min:3')]
    public string $lesson_title = '';

    #[Rule('required|datetime')]
    public DateTime $lesson_start;

    #[Rule('required|datetime')]
    public DateTime $lesson_end;

    public $publishedTeams = [];
    public $invitees = [];
    public $participants = [];
    public $paid = [];

    public function mount(Course $course)
    {
        $this->course = $course;

        $this->name = $course->name;
        $this->description = $course->description;
        $this->capacity = $course->capacity;
        $this->publishedTeams = $course->teams->pluck('id')->all();
        $this->invitees = $this->course->invitees()->get();
        $this->participants = $course->participants->pluck('id')->all();
        $this->paid = $course->participants_paid()->get()->pluck('id')->all();
    }

    public function update_publishedTeams()
    {
        $this->course->teams()->sync($this->publishedTeams);
        $this->invitees = $this->course->invitees()->get();
    }

    public function update_participants()
    {
        $this->course->participants()->sync($this->participants);
        $this->paid = $this->course->participants_paid()->get()->pluck('id')->all();
    }

    public function update_payment(int $id)
    {
        $this->course->participants()->updateExistingPivot($id, ['paid' => in_array($id, $this->paid)]);
    }

    public function remove_lesson(int $id)
    {
        Lesson::find($id)->delete();
    }

    public function add_lesson()
    {
        $this->validate();
        dd();
    }

    public function render()
    {
        return view('livewire.edit-course');
    }

    public function save()
    {
        $this->validate();
        $this->course->save();
    }
}
