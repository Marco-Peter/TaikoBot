<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ messageChannel: Object, users: Array });
router.reload();

const form = useForm({
    name: props.messageChannel.name,
});

const newRecipient = ref("");
const canPost = ref(false);

const submit = () => {
    form.put(route("channels.update", props.messageChannel.id));
}

function addRecipient(recipient) {
    router.post(route('channels.addRecipient', props.messageChannel.id), {
        'recipient': recipient.id,
        'can_post': canPost.value
    }, { preserveScroll: true });
}

function removeRecipient(recipient) {
    if (confirm(`Are you sure you want to remove ${recipient.first_name} ${recipient.last_name} from the channel?`)) {
        router.post(route('channels.removeRecipient', props.messageChannel.id),
            { 'recipient': recipient.id }, { preserveScroll: true });
    }
}

function updateCanPost(recipient) {
    router.post(route('channels.setCanPost', props.messageChannel.id), {
        'recipient': recipient.id,
        'can_post': recipient.pivot.can_post,
    }, { preserveScroll: true });
}
</script>

<template>
    <AppLayout title="Edit Channel">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit {{ messageChannel.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.name" class="mt-2" />

                        <PrimaryButton type="submit" class="mt-3">Submit</PrimaryButton>
                        <Link :href="route('channels.index')">
                        <SecondaryButton>Back</SecondaryButton>
                        </Link>
                    </form>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="font-semibold text-xl mb-2 mt-3">Recipients (Members)</h1>
                    <select v-model="newRecipient"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <option value="" disabled>--- Select a User ---</option>
                        <option v-for="recipient in users" :key="recipient.id" :value="recipient">{{ recipient.first_name }}
                            {{ recipient.last_name }}</option>
                    </select>
                    <input type="checkbox" :id="canPost" v-model="canPost"
                        class="mx-2 rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                    <label :for="canPost" class="mr-2">Can post messages</label>
                    <PrimaryButton :disabled="newRecipient === ''" @click="addRecipient(newRecipient)">Add</PrimaryButton>
                    <table v-if="messageChannel.recipients.length" class="mt-3">
                        <thead>
                            <tr>
                                <th class="pr-5">First Name</th>
                                <th class="pr-5">Last Name</th>
                                <th class="pr-5">Can post</th>
                                <th class="pr-5"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="recipient in messageChannel.recipients" :key="recipient.id">
                                <td class="pr-5">{{ recipient.first_name }}</td>
                                <td class="pr-5">{{ recipient.last_name }}</td>
                                <td class="pr-5">
                                    <input type="checkbox" v-model="recipient.pivot.can_post" true-value="1"
                                        false-value="0" @change="updateCanPost(recipient)"
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />

                                </td>
                                <td>
                                    <DangerButton @click="removeRecipient(recipient)">Remove</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No Recipients added</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
