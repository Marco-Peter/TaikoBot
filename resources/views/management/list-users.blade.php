<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <x-table>
                    <x-slot name="header">
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>email</th>
                        <th>Role</th>
                        <th>Teams</th>
                    </x-slot>

                    <x-slot name="body">
                        @foreach ($users as $user)
                            <tr>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->first_name }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->last_name }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->email }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->role }}</a></td>
                                <td>
                                    @foreach ($user->teams as $team)
                                        <a href="{{ route('users.edit', $user) }}">{{ $team->name }}</a>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-table>
            </div>
        </div>
    </div>
</x-app-layout>
