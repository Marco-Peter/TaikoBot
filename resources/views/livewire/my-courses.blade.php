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
                <h1>Signed Up</h1>
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Start</th>
                        <th>End</th>
                    </thead>
                    <tbody>
                        @foreach ($user->courses as $course)
                            <tr>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->lessons()->orderBy('start', 'asc')->first()->start }}</td>
                                <td>{{ $course->lessons()->orderBy('start', 'desc')->first()->finish }}</td>
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
                <h1>Available</h1>
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Start</th>
                        <th>End</th>
                    </thead>
                    <tbody>
                        @foreach ($user->team->courses->diff($user->courses) as $course)
                        <tr>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->lessons()->orderBy('start', 'asc')->first()->start }}</td>
                            <td>{{ $course->lessons()->orderBy('start', 'desc')->first()->finish }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
