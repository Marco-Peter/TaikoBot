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
                        <th></th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>email</th>
                        <th>Role</th>
                        <th>Teams</th>
                    </x-slot>

                    <x-slot name="body">
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST">
                                        @csrf
                                        @method('delete')
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->first_name }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->last_name }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->email }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->role }}</a></td>
                                @foreach ($user->teams as $team)
                                    <td><a href="{{ route('users.edit', $user) }}">{{ $team->name }}</a></td>
                                @endforeach
                            </tr>
                        @endforeach

                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <tr>
                                <td>
                                    <button type="submit">Add</button>
                                </td>
                                <td class="text-gray-800"><input type="text" name="first_name"></td>
                                <td class="text-gray-800"><input type="text" name="last_name"></td>
                                <td class="text-gray-800"><input type="email" name="email"></td>
                                <td rowspan="3"><input type="radio" name="role" value="student"
                                        id="student"><label for="student">Student</label>
                                    <p>
                                        <input type="radio" name="role" value="teacher" id="teacher"><label
                                            for="teacher">Teacher</label>
                                    <p>
                                        <input type="radio" name="role" value="admin" id="admin"><label
                                            for="admin">Admin</label>
                                </td>
                            </tr>
                        </form>
                    </x-slot>
                </x-table>
            </div>
        </div>
    </div>
</x-app-layout>
