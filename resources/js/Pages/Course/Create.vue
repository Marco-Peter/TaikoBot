<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({ teams: Object });

const page = usePage();
const form = useForm({
    name: '',
    description: '',
    capacity: '10',
    signout_limit: page.props.env.app.taikokarmaSignoutLimit,
    teams: [],
});

const submit = () => {
    form.post(route("courses.store"));
}
</script>

<template>
    <AppLayout title="Create Course">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create Course
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <!-- Course Name -->
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.name" class="mt-2" />

                        <!-- Course Description -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="description" value="Description" />
                            <textarea id="description" v-model="form.description" cols="30" rows="10"
                                title="This text will be formatted using GitHub style Markdown format.&#013;Check Google about what is possible!&#013;Double line breaks for paragraph&#013;#, ##, ### for hierarchic titles&#013;'-' for unorderet lists&#013;'1)', '2)', '3)' for ordered lists, ..."
                                placeholder="Public course description - make it catchy"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <!-- Course Capacity (max number of participants) -->
                        <InputLabel for="capacity" value="Capacity" />
                        <TextInput id="capacity" v-model="form.capacity" type="number" class="mt-1 block w-full" />
                        <InputError :message="form.errors.capacity" class="mt-2" />

                        <!-- Sign Out time limit to gain TaikoKarma -->
                        <InputLabel for="signoutLimit" value="Sign Out time limit (hours)" />
                        <TextInput id="signoutLimit" v-model="form.signout_limit" type="number" min="0"
                            title="Minimum hours to sign out before lessons to gain TaikoKarma"
                            class="mt-1 block w-full" />
                        <InputError :message="form.errors.signout_limit" class="mt-2" />

                        <!-- Groups to which the Course is Published -->
                        <h1 class="font-semibold text-xl mb-2 mt-3">Publish to Groups</h1>
                        <div v-for="team in teams" :key="team.id">
                            <input type="checkbox" :id="team.name" v-model="form.teams" :value="String(team.id)"
                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                            <label :for="team.name" class="ml-3">{{ team.name }}</label>
                        </div>

                        <!-- Form Submission -->
                        <PrimaryButton type="submit" class="mt-3">Create</PrimaryButton>
                        <Link :href="route('courses.index')">
                        <SecondaryButton>Cancel</SecondaryButton>
                        </Link>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
