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

const props = defineProps({ user: Object, roles: Array, teams: Array, messageChannels: Array });

const form = useForm({
    nickname: props.user.nickname,
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    role: props.user.role,
    team_id: props.user.team_id,
});

const newMessageChannel = ref("");
const canPost = ref(false);

const submit = () => {
    form.put(route("users.update", props.user.id));
}

function addMessageChannel(channel) {
    router.post(route('channels.addRecipient', channel), {
        'recipient': props.user.id,
        'can_post': canPost.value,
    }, { preserveScroll: true });
    canPost.value = false;
}

function removeMessageChannel(channel) {
    if (confirm(`Are you sure you want to remove ${channel.name} from the user?`)) {
        router.post(route('channels.removeRecipient', channel.id),
            { 'recipient': props.user.id }, { preserveScroll: true });
    }
}

function updateCanPost(channel) {
    router.post(route('channels.setCanPost', channel.id), {
        'recipient': props.user.id,
        'can_post': channel.pivot.can_post,
    }, { preserveScroll: true });
}
</script>

<template>
    <AppLayout title="Edit User">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit User
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <InputLabel for="nickname" value="Nickname" class="mt-3" />
                        <TextInput id="nickname" v-model="form.nickname" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.nickname" class="mt-2" />

                        <InputLabel for="first_name" value="First Name" class="mt-3" />
                        <TextInput id="first_name" v-model="form.first_name" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.first_name" class="mt-2" />

                        <InputLabel for="last_name" value="Last Name" class="mt-3" />
                        <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.last_name" class="mt-2" />

                        <InputLabel for="email" value="Email" class="mt-3" />
                        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" />
                        <InputError :message="form.errors.email" class="mt-2" />

                        <InputLabel for="role" value="Role" class="mt-3" />
                        <select v-model="form.role" id="role"
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option disabled value="">Please select one</option>
                            <option v-for="role in roles" :value="role">{{ role }}</option>
                        </select>
                        <InputError :message="form.errors.role" class="mt-2" />

                        <InputLabel for="team" value="Team" class="mt-3" />
                        <p>
                            <select v-model="form.team_id" id="team"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option disabled value=null>Please select one</option>
                                <option v-for="team in teams" :value="team.id">{{ team.name }}</option>
                            </select>
                        </p>
                        <InputError :message="form.errors.team_id" class="mt-2" />

                        <PrimaryButton type="submit" class="mt-5">Submit</PrimaryButton>
                        <Link :href="route('users.index')">
                        <SecondaryButton class="mt-5">Cancel</SecondaryButton>
                        </Link>
                    </form>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="font-semibold text-xl mb-2 mt-3">Message Channels</h1>
                    <select v-model="newMessageChannel"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <option value="" disabled>--- Select a channel ---</option>
                        <option v-for="messageChannel in messageChannels" :key="messageChannel.id" :value="messageChannel">
                            {{ messageChannel.name }}</option>
                    </select>
                    <input type="checkbox" :id="canPost" v-model="canPost"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                    <label :for="canPost" class="mx-3">Can post messages</label>
                    <PrimaryButton :disabled="newMessageChannel === ''" @click="addMessageChannel(newMessageChannel)">Add
                    </PrimaryButton>
                    <table v-if="user.subscribed_message_channels.length" class="mt-3">
                        <thead>
                            <tr>
                                <th class="pr-5">Name</th>
                                <th class="pr-5">Can post</th>
                                <th class="pr-5"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="channel in user.subscribed_message_channels" :key="channel.id">
                                <td class="pr-5">{{ channel.name }}</td>
                                <td class="pr-5">
                                    <input type="checkbox" v-model="channel.pivot.can_post" true-value="1" false-value="0"
                                        @change="updateCanPost(channel)"
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />

                                </td>
                                <td>
                                    <DangerButton @click="removeMessageChannel(channel)">Remove</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No Message Channels added</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
