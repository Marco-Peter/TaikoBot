<?php

namespace App\Livewire;

use App\Models\Course;
use Livewire\Component;

class EditCourse extends Component
{
    public Course $course;

    public function mount(Course $course)
    {
        $this->course = $course;
    }

    public function render()
    {
        return view('livewire.edit-course');
    }

    public function submit()
    {
        $this->validate();

        $this->course->save();
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'description' => [
                'string',
                'nullable',
            ],
            'capacity' => [
                'int',
                'required',
            ],
        ];
    }
}
