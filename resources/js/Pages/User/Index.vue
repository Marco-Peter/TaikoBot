<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Box from '@/Components/Box.vue';
import PageContent from '@/Components/PageContent.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

defineProps({ users: Object })

function destroy(id) {
    if (confirm("Are you sure you want to delete?")) {
        router.delete(route('users.destroy', id), { preserveScroll: true });
    }
}

function migrate() {
    if (confirm("Are you sure to run the migrations?")) {
        router.post(route('users.doMigrations'));
    }
}
</script>

<template>
    <AppLayout title="Users">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Users
            </h2>
        </template>

        <PageContent>

        <Box>
            <table v-if="users.length">
                <thead>
                    <tr>
                        <th></th>
                        <th class="px-2 text-left">Nickname</th>
                        <th class="px-2 text-left">First Name</th>
                        <th class="px-2 text-left">Last Name</th>
                        <th class="px-2 text-left">Email</th>
                        <th class="px-2 text-left">Role</th>
                        <th class="px-2 text-left">Teams</th>
                        <th class="px-2 text-left">Karma</th>
                        <th class="px-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users">
                        <td class="px-2"><img class="h-8 w-8 rounded-full object-cover"
                                :src="user.profile_photo_url"
                                :alt="user.nickname"></td>
                        <td class="px-2">{{ user.nickname }}</td>
                        <td class="px-2">{{ user.first_name }}</td>
                        <td class="px-2">{{ user.last_name }}</td>
                        <td class="px-2">{{ user.email }}</td>
                        <td class="px-2">{{ user.role }}</td>
                        <td class="px-2">{{ user.team ? user.team.name : 'NO GROUP' }}</td>
                        <td class="px-2">{{ user.karma === null ? '\u{221E}' : user.karma }}</td>
                        <td>
                            <div class="flex flex-row items-center gap-2">
                            <Link :href="route('users.edit', user.id)">
                                <SecondaryButton small>Edit</SecondaryButton>
                            </Link>
                            <DangerButton small @click="destroy(user.id)">Delete</DangerButton>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-else>No Users available</p>
            <Link :href="route('users.create')">
                <PrimaryButton class="mt-4">Add User</PrimaryButton>
            </Link>
        </Box>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl mb-2">Danger Zone</h2>
                <PrimaryButton class="mb-2" @click="migrate()">Run Migrations</PrimaryButton>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                </div>
            </div>
        </div>

        </PageContent>
</AppLayout>
</template>
