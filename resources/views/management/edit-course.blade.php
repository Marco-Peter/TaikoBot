<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Course Management') }}
        </h2>
    </x-slot>

    <form method="POST" action="{{ route('courses.update', $course) }}">
        @csrf
        @method('patch')
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="name" value="Name" />
                        <x-input id="name" name="name" type="text" class="mt-1 block w-full" required
                            autocomplete="name" value="{{ $course->name }}" />
                        <x-input-error for="name" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="description" value="Description" />
                        <x-input id="description" name="description" type="text" class="mt-1 block w-full"
                            value="{{ $course->description }}" />
                        <x-input-error for="description" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <h1>Fees</h1>
                        @foreach (App\Models\WageGroup::all() as $wage_group)
                            <x-label for="{{ $wage_group->name }}" value="{{ $wage_group->name }}" />
                            <x-input id="fee" name="{{ $wage_group->name }}" type="integer"
                                class="mt-1 block w-full"
                                value="{{ $course->fees->find($wage_group->id)->pivot->fee }}" />
                            <x-input-error for="fee" class="mt-2" />
                        @endforeach
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="capacity" value="Capacity" />
                        <x-input id="capacity" name="capacity" type="integer" class="mt-1 block w-full"
                            value="{{ $course->capacity }}" />
                        <x-input-error for="capacity" class="mt-2" />
                    </div>
                    <button type="submit"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Change</button>
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <fieldset>
                        <legend>Team Availability</legend>
                        @foreach (App\Models\Team::all() as $team)
                            <div class="block">
                                <input type="checkbox" id="team[{{ $team->name }}]" name="teams[{{ $team->id }}]"
                                    value="1"
                                    {{ old('teams', $course->teams->contains($team)) ? "checked" : "" }} />
                                <label for="team[{{ $team->name }}]">{{ $team->name }}</label>
                            </div>
                        @endforeach
                    </fieldset>
                </div>
            </div>
        </div>
    </form>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <table>
                    <thead>
                        <th></th>
                        <th>Start</th>
                        <th>End</th>
                    </thead>
                    <tbody>
                        @foreach ($course->lessons as $lesson)
                            <form action="{{ route('lessons.destroy', $lesson) }}" method="post">
                                @csrf
                                @method('delete')
                                <tr>
                                    <td><button type="submit" name="remove_team"
                                            value="{{ $team->id }}">Remove</button></td>
                                    <td><a href="{{ route('lessons.edit', $lesson) }}">{{ $lesson->start }}</a></td>
                                    <td><a href="{{ route('lessons.edit', $lesson) }}">{{ $lesson->finish }}</a></td>
                                </tr>
                            </form>
                        @endforeach
                        <form method="POST" action="{{ route('lessons.store') }}">
                            @csrf
                            <tr>
                                <td><button type="submit" name="course_id" value="{{ $course->id }}"
                                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Add</button>
                                </td>
                                <td><x-input id="start" name="start" type="datetime-local"
                                        class="mt-1 block w-full" required />
                                    <x-input-error for="start" class="mt-2" />
                                </td>
                                <td><x-input id="finish" name="finish" type="datetime-local"
                                        class="mt-1 block w-full" required />
                                    <x-input-error for="finish" class="mt-2" />
                                </td>
                            </tr>
                        </form>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <table>
                    <thead>
                        <th></th>
                        <th>First Name</th>
                        <th>Last Name</th>
                    </thead>
                    <tbody>
                        @foreach ($course->participants as $participant)
                        <form action="{{ route('courses.add-participant', $participant) }}" method="post">
                            @csrf
                            @method('delete')
                            <tr>
                                <td><button type="submit" name="remove_participant"
                                        value="{{ $team->id }}">Remove</button></td>
                                <td><a href="{{ route('courses.add-participant', $participant) }}">{{ $participant->first_name }}</a></td>
                                <td><a href="{{ route('courses.add-participant', $participant) }}">{{ $participant->last_name }}</a></td>
                            </tr>
                        </form>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
