<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    status: Number,
});

const title = computed(() => {
    return {
        419: 'Page Expired',
        403: 'Forbidden',
        404: 'Page Not Found',
        500: 'Server Error',
        503: 'Service Unavailable',
    }[props.status] || 'Error';
});

const description = computed(() => {
    return {
        419: 'Your session has expired. Please refresh the page to continue.',
        403: 'You do not have permission to access this resource.',
        404: 'The page you are looking for could not be found.',
        500: 'Whoops, something went wrong on our servers.',
        503: 'Service is temporarily unavailable. Please try again later.',
    }[props.status] || 'An error occurred.';
});

function refreshPage() {
    window.location.reload();
}
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col justify-center items-center px-6">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-4">
                {{ status }}
            </h1>
            <h2 class="text-2xl font-semibold text-gray-600 dark:text-gray-400 mb-4">
                {{ title }}
            </h2>
            <p class="text-gray-500 dark:text-gray-500 mb-8 max-w-md mx-auto">
                {{ description }}
            </p>
            <div class="flex gap-4 justify-center">
                <button 
                    v-if="status === 419" 
                    @click="refreshPage"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
                >
                    Refresh Page
                </button>
                <Link 
                    :href="route('dashboard')" 
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
                >
                    Go to Dashboard
                </Link>
            </div>
        </div>
    </div>
</template>