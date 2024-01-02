<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({ channels: Object })

function destroy(id) {
    if (confirm("Are you sure you want to delete?")) {
        router.delete(route('users.destroy', id), { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout title="Message Channels">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Message Channels
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <p v-if="channels" v-for="channel in channels">
                    <Link :href="route('channels.messages.index', channel.id)">{{ channel.name }}</Link>
                </p>
                <p v-else> No Message channels for you</p>
            </div>
        </div>
    </AppLayout>
</template>
