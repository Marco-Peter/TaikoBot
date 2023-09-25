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
                        <x-label for="fee" value="Fee" />
                        <x-input id="fee" name="fee" type="integer" class="mt-1 block w-full"
                            value="{{ $course->fee }}" />
                        <x-input-error for="fee" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="capacity" value="Capacity" />
                        <x-input id="capacity" name="capacity" type="integer" class="mt-1 block w-full"
                            value="{{ $course->capacity }}" />
                        <x-input-error for="capacity" class="mt-2" />
                    </div>
                    <button name="changed_item" value="field" type="submit"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Change</button>
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1>Teams</h1>
                    <table class="w-full">
                        <caption>Automatically signed in</caption>
                        <tbody>
                            @foreach ($course->teams_signed_in as $team)
                                <tr>
                                    <td><button name="remove_team" value="{{ $team->id }}" type="submit"
                                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Remove</button>
                                    </td>
                                    <td><button name="autosign_off" value="{{ $team->id }}", type="submit"
                                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Manual
                                            Sign-In</button></td>
                                    <td>{{ $team->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <table class="w-full">
                        <caption>Sign in manually</caption>
                        <tbody>
                            @foreach ($course->teams_not_signed_in as $team)
                                <tr>
                                    <td><button name="remove_team" value="{{ $team->id }}" type="submit"
                                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Remove</button>
                                    </td>
                                    <td><button name="autosign_on" value="{{ $team->id }}" type="submit"
                                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Auto
                                            Sign-In</button></td>
                                    <td>{{ $team->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <table class="w-full">
                        <caption>Available teams</caption>
                        <tbody>
                            @foreach ($teams_not_selected as $team)
                                <tr>
                                    <td><button name="add_team" value="{{ $team->id }}" type="submit"
                                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Add</button>
                                    </td>
                                    <td>{{ $team->name }}</td>
                                </tr>
                            @endforeach
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
                            <th>Start</th>
                            <th>End</th>
                        </thead>
                        <tbody>
                            @foreach ($course->lessons as $lesson)
                            <tr>
                                <td><a href="{{ route('lessons.edit', $lesson) }}">{{ $lesson->start }}</a></td>
                                <td><a href="{{ route('lessons.edit', $lesson) }}">{{ $lesson->finish }}</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
