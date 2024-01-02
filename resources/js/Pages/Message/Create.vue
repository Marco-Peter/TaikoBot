<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({ channel: Object })

const form = useForm({
    content: '',
});

const submit = () => {
    form.post(route("channels.messages.store", props.channel.id));
}
</script>

<template>
    <AppLayout title="Post">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Post to {{ channel.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="content" value="Text" />
                            <textarea id="content" v-model="form.content" cols="30" rows="10"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                            <InputError :message="form.errors.content" class="mt-2" />
                        </div>

                        <PrimaryButton type="submit" class="mt-3">Post</PrimaryButton>
                        <Link :href="route('channels.messages.index', channel.id)">
                        <SecondaryButton>Cancel</SecondaryButton>
                        </Link>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
