<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('My Courses') }} - {{ $user->first_name . ' ' . $user->last_name }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                @if ($user->courses->count())
                <p>Signed Up</p>
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Start</th>
                        <th>End</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach ($user->courses as $course)
                            <tr>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->lessons()->orderBy('start', 'asc')->first()->start }}</td>
                                <td>{{ $course->lessons()->orderBy('start', 'desc')->first()->finish }}</td>
                                <td>
                                    <x-button class="ml-3" wire:click="showCourseInfo" wire:loading.attr="disabled">
                                        {{ __('Details') }}
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p>You are not signed in to any courses</p>
                @endif
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                @if ($user->team->courses->count())
                <h1>Available</h1>
                <table class="table-auto">
                    <thead>
                        <th>Name</th>
                        <th>Start</th>
                        <th>End</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach ($user->team->courses->diff($user->courses) as $course)
                            <tr>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->lessons()->orderBy('start', 'asc')->first()->start }}</td>
                                <td>{{ $course->lessons()->orderBy('start', 'desc')->first()->finish }}</td>
                                <td>
                                    <x-button class="ml-3" wire:click="showCourseInfo" wire:loading.attr="disabled">
                                        {{ __('Details') }}
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Course Information Modal -->
                <x-dialog-modal wire:model.live="showingCourseInfo">
                    <x-slot name="title">
                        {{ $course->name }}
                    </x-slot>

                    <x-slot name="content">
                        {{ $course->description }}
                    </x-slot>

                    <x-slot name="footer">
                        <x-secondary-button wire:click="$toggle('showingCourseInfo')" wire:loading.attr="disabled">
                            {{ __('Back') }}
                        </x-secondary-button>

                        @if ($user->courses->contains($course))
                            <x-danger-button class="ml-3" wire:click="signOutFromCourse({{ $course }})"
                                wire:loading.attr="disabled">
                                {{ __('Sign Out') }}
                            </x-danger-button>
                        @else
                            <x-button class="ml-3" wire:click="signInToCourse({{ $course }})"
                                wire:loading.attr="disabled">
                                {{ __('Sign In') }}
                            </x-button>
                        @endif
                    </x-slot>
                </x-dialog-modal>
                @else
                <p>No new courses available</p>
                @endif
            </div>
        </div>
    </div>
</div>
