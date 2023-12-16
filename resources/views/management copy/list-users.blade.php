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
                        <th></th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>email</th>
                        <th>Role</th>
                        <th>Teams</th>
                        <th>Income Group</th>
                    </x-slot>

                    <x-slot name="body">
                        @foreach (App\Models\User::all() as $user)
                            <tr>
                                <td>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST">
                                        @csrf
                                        @method('delete')
                                        <x-danger-button type="submit">Delete</x-danger-button>
                                    </form>
                                </td>
                                <td><a href="{{ route('users.edit', $user) }}"><img
                                            class="h-8 w-8 rounded-full object-cover"
                                            src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                                    </a>
                                </td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->first_name }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->last_name }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->email }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->role }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->team->name }}</a></td>
                                <td><a href="{{ route('users.edit', $user) }}">{{ $user->incomeGroup->name }}</a>
                                </td>
                            </tr>
                        @endforeach

                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <tr>
                                <td colspan="2">
                                    <x-button type="submit">Add</x-button>
                                </td>
                                <td class="text-gray-800"><input type="text" name="first_name"></td>
                                <td class="text-gray-800"><input type="text" name="last_name"></td>
                                <td class="text-gray-800"><input type="email" name="email"></td>
                                <td class="text-gray-800">
                                    <select name="role" id="role">
                                        @foreach (App\Enums\UserRoleEnum::values() as $role)
                                            <option value="role" @selected($role === App\Enums\UserRoleEnum::STUDENT->value)>{{ $role }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-gray-800">
                                    <select name="team_id" id="team">
                                        @foreach (App\Models\Team::all() as $team)
                                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-gray-800">
                                    <select name="incomeGroup" id="incomeGroup">
                                        @foreach (App\Models\IncomeGroup::all() as $incomeGroup)
                                            <option value="{{ $incomeGroup->id }}">{{ $incomeGroup->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        </form>
                    </x-slot>
                </x-table>
            </div>
        </div>
    </div>
</x-app-layout>
