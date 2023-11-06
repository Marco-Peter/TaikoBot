<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Http\Request;
use Livewire\Component;

class MyCourses extends Component
{
    public User $user;

    public function mount(Request $request)
    {
        $this->user = $request->user();
    }

    public function render()
    {
        return view('livewire.my-courses');
    }
}
