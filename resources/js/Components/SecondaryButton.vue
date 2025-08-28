<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    small: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        :type="type"
        :class="[
            'inline-flex items-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150',
            small ? 'px-2 py-1 text-2xs' : 'px-4 py-2 text-xs'
        ]">
        <slot />
    </button>
</template>
