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
                <x-table>
                    <x-slot name="header">
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
                                    <form action="{{ route('courses.destroy', $course) }}" method="POST">
                                        @csrf
                                        @method('delete')
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>
                                <td><a href="{{ route('courses.edit', $course) }}">{{ $course->name }}</a></td>
                                <td><a href="{{ route('courses.edit', $course) }}">{{ $course->description }}</a></td>
                                <td><a href="{{ route('courses.edit', $course) }}">{{ $course->fee }}</a></td>
                                <td><a href="{{ route('courses.edit', $course) }}">{{ $course->capacity }}</a></td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-table>
                <a href="{{ route('courses.create') }}">Create Course</a>
            </div>
        </div>
    </div>
</x-app-layout>
