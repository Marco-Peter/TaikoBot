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
                <form method="POST" action="{{ route('courses.update', $course) }}">
                    @csrf
                    @method('patch')
                    <label for="name">Name</label>
                    <p>
                        <input class="text-gray-800" type="text" name="name" value="{{ $course->name }}">
                    <p>
                        <label for="description">Description</label>
                    <p>
                        <textarea class="text-gray-800" name="description" id="description" cols="30" rows="10">{{ $course->description }}</textarea>
                    <p>
                        <label for="fee">Fee</label>
                    <p>
                        <input class="text-gray-800" type="text" name="fee" id="fee"
                            value="{{ $course->fee }}">
                    <p>
                        <label for="capacity">Capacity</label>
                    <p>
                        <input class="text-gray-800" type="text" name="capacity" id="capacity"
                            value="{{ $course->capacity }}">
                    <p>
                        <button name="changed_item" value="field" type="submit">Change</button>
                    <p>
                        <label for="teams">Teams</label>
                    <p>
                    <table>
                        <caption>Automatically signed in</caption>
                        <tbody>
                            @foreach ($course->teams_signed_in as $team)
                                <tr>
                                    <td><button name="remove_team" value="{{ $team->id }}"
                                            type="submit">Remove</button></td>
                                    <td><button name="autosign_off" value="{{ $team->id }}", type="submit">Manual
                                            Sign-In</button></td>
                                    <td>{{ $team->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table>
                        <caption>Sign in manually</caption>
                        <tbody>
                            @foreach ($course->teams_not_signed_in as $team)
                                <tr>
                                    <td><button name="remove_team" value="{{ $team->id }}"
                                            type="submit">Remove</button></td>
                                    <td><button name="autosign_on" value="{{ $team->id }}" type="submit">Auto
                                            Sign-In</button></td>
                                    <td>{{ $team->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table>
                        <caption>Available teams</caption>
                        <tbody>
                            @foreach ($teams_not_selected as $team)
                                <tr>
                                    <td><button name="add_team" value="{{ $team->id }}" type="submit">Add</button>
                                    </td>
                                    <td>{{ $team->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
