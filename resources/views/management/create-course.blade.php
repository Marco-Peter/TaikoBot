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
                <form method="POST" action="{{ route('courses.store') }}">
                    @csrf
                    <label for="name">Name</label>
                    <p>
                        <input class="text-gray-800" type="text" name="name" value="{{ old('name') }}">
                    <p>
                        <label for="description">Description</label>
                    <p>
                        <textarea class="text-gray-800" name="description" id="description" cols="30" rows="10">{{ old('description') }}</textarea>
                    <p>
                    <h1>Fees</h1>
                    @foreach (App\Models\IncomeGroup::all() as $income_group)
                        <label for="fee[{{ $income_group->id }}]">{{ $income_group->name }}</label>
                        <p>
                            <input class="text-gray-800" type="text" name="fees[{{ $income_group->id }}]"
                                id="fee[{{ $income_group->id }}]" value="{{ old('fees[' . $income_group->id . ']') }}">
                        <p>
                    @endforeach
                    <label for="capacity">Capacity</label>
                    <p>
                        <input class="text-gray-800" type="text" name="capacity" id="capacity"
                            value="{{ old('capacity') }}">
                    <p>
                        <x-button class="mt-3" type="submit">{{ __('Create Course') }}</x-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
