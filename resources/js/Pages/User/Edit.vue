<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import Box from '@/Components/Box.vue';
import PageContent from '@/Components/PageContent.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ user: Object, roles: Array, teams: Array });

const form = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    role: props.user.role,
    karma: String(props.user.karma),
    team_id: props.user.team_id,
});

const infiniteKarma = ref(props.user.karma === null);

const submit = () => {
    if (infiniteKarma.value) {
        form.karma = null;
    }
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

        <PageContent>

        <Box>
            <form @submit.prevent="submit">
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

                <!-- Taiko Karma -->
                <InputLabel for="karma" value="Karma" class="mt-3" />
                <input type="checkbox" id="infiniteKarma" v-model="infiniteKarma"
                    @click="form.karma = '0'"
                    class="ml-1 rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                <label for="infiniteKarma" class="mx-3">infinite karma</label>
                <TextInput v-if="!infiniteKarma" id="karma" v-model="form.karma" type="number" class="mt-1 block w-full" />
                <InputError :message="form.errors.karma" class="mt-2" />

                <InputLabel for="team" value="Team" class="mt-3" />
                <p>
                    <select v-model="form.team_id" id="team"
                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option disabled value=null>Please select one</option>
                        <option v-for="team in teams" :value="team.id">{{ team.name }}</option>
                    </select>
                </p>
                <InputError :message="form.errors.team_id" class="mt-2" />

                <div class="flex flex-row gap-2 mt-4">
                    <PrimaryButton type="submit" class="">Submit</PrimaryButton>
                    <Link :href="route('users.index')">
                        <SecondaryButton class="">Cancel</SecondaryButton>
                    </Link>
                </div>
            </form>
        </Box>

        </PageContent>
    </AppLayout>
</template>
