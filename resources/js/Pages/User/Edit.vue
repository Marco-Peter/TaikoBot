<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ user: Object, roles: Object, teams: Object });

const form = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    role: props.user.role,
    team_id: props.user.team_id,
});

const submit = () => {
    form.put(route("users.update", props.user.id));
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
                        <InputLabel for="first_name" value="First Name" />
                        <TextInput id="first_name" v-model="form.first_name" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.first_name" class="mt-2" />

                        <InputLabel for="last_name" value="Last Name" />
                        <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full" />
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
                                <option disabled value=null>Please select one</option>
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
