<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lesson Management') }}
        </h2>
    </x-slot>
    <form method="POST" action="{{ route('lessons.update', $lesson) }}">
        @csrf
        @method('patch')
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="title" value="Title" />
                        <x-input id="title" name="title" type="text" class="mt-1 block w-full" required
                            autocomplete="title" value="{{ $lesson->title }}" />
                        <x-input-error for="title" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="start" value="Start" />
                        <x-input id="start" name="start" type="datetime-local" class="mt-1 block w-full" required
                            autocomplete="start" value="{{ $lesson->start }}" />
                        <x-input-error for="start" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="finish" value="Finish" />
                        <x-input id="finish" name="finish" type="datetime-local" class="mt-1 block w-full"
                            value="{{ $lesson->finish }}" />
                        <x-input-error for="finish" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="notes" value="Notes" />
                        <textarea name="notes" id="notes" cols="30" rows="10"
                            placeholder="Lesson planning, anouncements - not visible to students"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ $lesson->notes }}</textarea>
                        <x-input-error for="notes" class="mt-2" />
                    </div>
                    <button name="changed_item" value="field" type="submit"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        Change</button>
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <table>
                        <thead>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Team</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            @foreach ($lesson->participants as $participant)
                                <tr>
                                    <td><a
                                            href="{{ route('users.edit', $participant) }}">{{ $participant->first_name }}</a>
                                    </td>
                                    <td><a
                                            href="{{ route('users.edit', $participant) }}">{{ $participant->last_name }}</a>
                                    </td>
                                    <td><a
                                            href="{{ route('users.edit', $participant) }}">{{ $participant->team->name }}</a>
                                    </td>
                                    <td>
                                        <input type="hidden" name="participant" value="{{ $participant->id }}">
                                        <select
                                            class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
                                            name="participation" id="participation">
                                            @foreach (\App\Enums\LessonParticipationEnum::values() as $key => $value)
                                                <option value="{{ $key }}" @selected($value == $participant->pivot->participation)>
                                                    {{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button name="update_participation" type="submit"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        Update Participation</button>
                </div>
            </div>
        </div>
    </form>

</x-app-layout>