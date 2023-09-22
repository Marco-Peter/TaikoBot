<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Course Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <label for="name">Name</label><p>
                <input class="text-gray-800" type="text" name="name" value="{{ $course->name }}"><p>
                <label for="description">Description</label><p>
                <textarea class="text-gray-800" name="description" id="description" cols="30" rows="10">{{ $course->description }}</textarea><p>
                <label for="fee">Fee</label><p>
                <input class="text-gray-800" type="text" name="fee" id="fee" value="{{ $course->fee }}"><p>
                <label for="capacity">Capacity</label><p>
                <input class="text-gray-800" type="text" name="capacity" id="capacity" value="{{ $course->capacity }}"><p>
                <label for="teams">Teams</label><p>
                <select class="text-gray-800" name="teams" id="teams" multiple>
                    @foreach ($teams as $team)
                        <option value="{{ $team }}" @selected(in_array($team, $course->teams->all()))>{{ $team->name }}</option>
                    @endforeach
                </select><p>
            </div>
        </div>
    </div>
</x-app-layout>
