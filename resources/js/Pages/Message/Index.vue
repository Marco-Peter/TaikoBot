<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({ channel: Object })

function destroy(id) {
    if (confirm("Are you sure you want to delete?")) {
        router.delete(route('users.destroy', id), { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout title="Messages">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Messages in {{ channel.name }}
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <p v-if="channel.messages.length" v-for="message in channel.messages">
                <div class="my-3 py-1 bg-white dark:bg-gray-800">
                    <p class="font-bold">{{ message.user.first_name }} {{ message.user.last_name }}</p>
                    <p>{{ message.content }}</p>
                    <p class="font-light italic">{{ new Date(message.created_at).toLocaleString() }}</p>
                </div>
                </p>
                <p v-else>No Messages available in {{ channel.name }}</p>

                <Link :href="route('channels.messages.create', channel.id)">
                <PrimaryButton class="mt-5">Post Message</PrimaryButton>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
