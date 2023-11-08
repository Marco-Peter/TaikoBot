<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('My Lessons') }} - {{ $user->first_name . ' ' . $user->last_name }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <table class="border-separate table-auto border-solid border-spacing-30">
                    <thead>
                        <th>Title</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach ($user->lessons as $lesson)
                            <tr>
                                <td>{{ $lesson->title }}</td>
                                <td>{{ $lesson->start }}</td>
                                <td>{{ $lesson->finish }}</td>
                                <td>{{ $lesson->pivot->participation }}</td>
                                <td>
                                    @if ($lesson->pivot->participation === App\Enums\LessonParticipationEnum::SIGNED_IN->value)
                                    <x-button class="ml-3" wire:click="signOut({{ $lesson }})"
                                        wire:loading.attr="disabled">{{ __('Sign Out') }}
                                    </x-button>
                                    @elseif ($lesson->pivot->participation === App\Enums\LessonParticipationEnum::SIGNED_OUT->value)
                                    <x-button class="ml-3" wire:click="signIn({{ $lesson }})"
                                        wire:loading.attr="disabled">{{ __('Sign In') }}
                                    </x-button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
