<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({ channel: Object, messages: Object, post_message: Boolean });
const page = usePage();
const allMessages = ref(props.messages.data);

function loadMorePosts() {
    if (props.messages.next_page_url === null) {
        return;
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
                <p v-if="allMessages.length" v-for="message in allMessages" :key="message.id">
                <div class="my-3 py-1"
                    :class="message.user.id === page.props.auth.user.id ? 'bg-blue-300 dark:bg-blue-900': 'bg-white dark:bg-gray-800'">
                    <p class="font-bold">{{ message.user.first_name }} {{ message.user.last_name }}</p>
                    <p>{{ message.content }}</p>
                    <p class="font-light italic">{{ new Date(message.created_at).toLocaleString() }}</p>
                </div>
                </p>
                <p v-else>No Messages available in {{ channel.name }}</p>

                <Link v-if="post_message" :href="route('channels.messages.create', channel.id)">
                <PrimaryButton class="mt-5">Post Message</PrimaryButton>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
