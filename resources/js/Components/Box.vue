<script setup>
defineProps({
});
</script>

<template>
    <div class="flex flex-col items-stretch gap-2 px-4 py-4 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <slot />
    </div>
</template>
