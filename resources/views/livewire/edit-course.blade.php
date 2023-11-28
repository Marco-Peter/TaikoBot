<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Course Management') }}
    </h2>
</x-slot>

<div>
    <form wire:submit="save">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="name" value="Name" />
                        <x-input type="text" wire:model="name" class="mt-1 block w-full" />
                        <x-input-error for="name" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="description" value="Description" />
                        <textarea wire:model="description" cols="30" rows="10"
                            placeholder="Public course description - make it catchy"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <h1>Fees</h1>
                        @foreach ($course->incomeGroups as $income_group)
                            <x-label for="fees[{{ $income_group->id }}]" value="{{ $income_group->name }}" />
                            <x-input id="fees[{{ $income_group->id }}]" name="fees[{{ $income_group->id }}]"
                                type="integer" class="mt-1 block w-full"
                                value="{{ $course->incomeGroups->find($income_group->id)->pivot->fee }}" />
                            <x-input-error for="fee" class="mt-2" />
                        @endforeach
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="capacity" value="Capacity" />
                        <x-input type="integer" wire:model="capacity" class="mt-1 block w-full" />
                        <x-input-error for="capacity" class="mt-2" />
                    </div>
                </div>
                <x-button type="submit"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Save
                    Changes</x-button>
            </div>
        </div>
    </form>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <fieldset>
                    <legend>Team Availability</legend>
                    @foreach (App\Models\Team::all() as $team)
                        <div class="block" wire:key="{{ $team->id }}">
                            <input id="selectedTeams[{{ $team->id }}]" type="checkbox" value="{{ $team->id }}"
                                wire:model="selectedTeams" wire:click="update_selectedTeams" />
                            <label for="selectedTeams[{{ $team->id }}]">{{ $team->name }}</label>
                        </div>
                    @endforeach
                </fieldset>
            </div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <table>
                    <thead>
                        <th>Signed In</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Team</th>
                        <th>Income Group</th>
                        <th>Price</th>
                        <th>Paid</th>
                    </thead>
                    <tbody>
                        @foreach ($invitees as $invitee)
                            <tr>
                                <td>
                                    <input type="checkbox" name="participants[{{ $invitee['id'] }}]"
                                        value="{{ $invitee['id'] }}" wire:model="participants"
                                        wire:click="update_participants" />
                                </td>
                                <td>{{ $invitee['first_name'] }}</td>
                                <td>{{ $invitee['last_name'] }}</td>
                                <td>{{ $invitee['team']['name'] }}</td>
                                <td>{{ $invitee['income_group']['name'] }}</td>
                                <td>
                                    {{ $course->incomeGroups->where('id', $invitee['income_group']['id'])->first()->pivot->fee }}
                                </td>
                                <td>
                                    <input type="checkbox" name="paid[{{ $invitee['id'] }}]"
                                        @disabled(!in_array($invitee['id'], $participants)) value="{{ $invitee['id'] }}" wire:model="paid"
                                        wire:click="update_payment({{ $invitee['id'] }})" />
                                </td>
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
                        <th colspan="2"></th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Title</th>
                    </thead>
                    <tbody>
                        @foreach ($course->lessons->sortBy('start') as $lesson)
                            <tr>
                                <td>
                                    <x-button
                                        onclick="window.location.href='{{ route('lessons.edit', $lesson) }}';">Edit</x-button>
                                </td>
                                <td>
                                    <x-danger-button type="button"
                                        wire:click="remove_lesson({{ $lesson->id }})">Remove</x-danger-button>
                                </td>
                                <td>{{ $lesson->start }}</td>
                                <td>{{ $lesson->finish }}</td>
                                <td>{{ $lesson->title }}</td>
                            </tr>
                        @endforeach
                        <form wire:submit="add_lesson">
                            <tr>
                                <td colspan="2">
                                    <x-button type="submit">Add</x-button>
                                </td>
                                <td><x-input type="datetime-local" wire:model="lesson_start"
                                        class="mt-1 block w-full" />
                                    <x-input-error for="start" class="mt-2" />
                                </td>
                                <td><x-input type="datetime-local" wire:model="lesson_end" class="mt-1 block w-full" />
                                    <x-input-error for="finish" class="mt-2" />
                                </td>
                                <td>
                                    <x-input type="text" class="mt-1 block w-full" wire:model="lesson_title" />
                                    <x-input-error for="title" class="mt-2" />
                                </td>
                            </tr>
                        </form>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
