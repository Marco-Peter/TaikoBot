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
                        @foreach ($incomeGroups as $i => $value)
                            <x-label for="fees[{{ $value['id'] }}]"
                                value="{{ $value['name'] }}" />
                            <x-input id="fees[{{ $value['id'] }}]" type="integer" class="mt-1 block w-full"
                                wire:model="fees.{{ $value['id'] }}.fee" />
                            <x-input-error for="fees[{{ $value['id'] }}]" class="mt-2" />
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
</div>
