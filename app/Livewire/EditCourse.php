<?php

namespace App\Livewire;

use App\Models\Course;
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

    public $publishedTeams = [];
    public $invitees = [];

    public function mount(Course $course)
    {
        $this->course = $course;

        $this->name = $course->name;
        $this->description = $course->description;
        $this->capacity = $course->capacity;
        $this->publishedTeams = $course->teams->pluck('id')->all();
        $this->invitees = $this->course->invitees()->get();
        //dd($this->invitees);
    }

    public function update_publishedTeams()
    {
        $this->course->teams()->sync($this->publishedTeams);
        $this->invitees = $this->course->invitees()->get();

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
