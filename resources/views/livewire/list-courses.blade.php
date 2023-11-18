<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Course Management') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                @if ($courses->count())
                    <x-table>
                        <x-slot name="header">
                            <th></th>
                            <th></th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>fee</th>
                            <th>Capacity</th>
                        </x-slot>

                        <x-slot name="body">
                            @foreach ($courses as $course)
                                <tr>
                                    <td>
                                        <x-danger-button type="button"
                                            wire:click="delete({{ $course->id }})">Delete</x-danger-button>
                                    </td>
                                    <td><x-button
                                            onclick="window.location.href='{{ route('course.edit', $course) }}';">Edit</x-button>
                                    </td>
                                    <td><a href="{{ route('course.edit', $course) }}">{{ $course->name }}</a></td>
                                    <td><a href="{{ route('course.edit', $course) }}">{{ $course->description }}</a>
                                    </td>
                                    <td><a href="{{ route('course.edit', $course) }}">{{ $course->fee }}</a></td>
                                    <td><a href="{{ route('course.edit', $course) }}">{{ $course->capacity }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-table>
                @else
                    <p>No courses available</p>
                @endif
                <x-button onclick="window.location.href='{{ route('course.edit') }}';">Create Course</x-button>
            </div>
        </div>
    </div>
</div>
