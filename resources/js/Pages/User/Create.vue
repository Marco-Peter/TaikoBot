<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineProps({ roles: Object, teams: Object })

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    role: '',
    team_id: '',
});

const submit = () => {
    form.post(route("users.store"));
}
</script>

<template>
    <AppLayout template="Create User">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create User
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <InputLabel for="firstName" value="First Name" />
                        <TextInput id="firstName" v-model="form.first_name" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.first_name" class="mt-2" />

                        <InputLabel for="lastName" value="Last Name" />
                        <TextInput id="lastName" v-model="form.last_name" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.last_name" class="mt-2" />

                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" />
                        <InputError :message="form.errors.email" class="mt-2" />

                        <InputLabel for="role" value="Role" />
                        <select v-model="form.role" id="role"
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option disabled value="">Please select one</option>
                            <option v-for="role in roles" :value="role">{{ role }}</option>
                        </select>
                        <InputError :message="form.errors.role" class="mt-2" />

                        <InputLabel for="team" value="Team" />
                        <p>
                            <select v-model="form.team_id" id="team"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option disabled value="">Please select one</option>
                                <option v-for="team in teams" :value="team.id">{{ team.name }}</option>
                            </select>
                        </p>
                        <InputError :message="form.errors.team_id" class="mt-2" />

                        <PrimaryButton type="submit">Submit</PrimaryButton>
                        <Link :href="route('users.index')">
                        <SecondaryButton>Cancel</SecondaryButton>
                        </Link>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
