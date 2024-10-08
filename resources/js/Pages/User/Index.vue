<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
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

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Link :href="route('users.create')">
                <PrimaryButton class="mb-2">Add User</PrimaryButton>
                </Link>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <table v-if="users.length">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="pr-5">Nickname</th>
                                <th class="pr-5">First Name</th>
                                <th class="pr-5">Last Name</th>
                                <th class="pr-5">Email</th>
                                <th class="pr-5">Role</th>
                                <th class="pr-5">Teams</th>
                                <th class="pr-5">Karma</th>
                                <th colspan="2" class="pr-5"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users">
                                <td class="pr-5"><img class="h-8 w-8 rounded-full object-cover"
                                        :src="user.profile_photo_url"
                                        :alt="user.nickname"></td>
                                <td class="pr-5">{{ user.nickname }}</td>
                                <td class="pr-5">{{ user.first_name }}</td>
                                <td class="pr-5">{{ user.last_name }}</td>
                                <td class="pr-5">{{ user.email }}</td>
                                <td class="pr-5">{{ user.role }}</td>
                                <td class="pr-5">{{ user.team ? user.team.name : 'NO GROUP' }}</td>
                                <td class="pr-5">{{ user.karma === null ? '\u{221E}' : user.karma }}</td>
                                <td>
                                    <Link :href="route('users.edit', user.id)">
                                    <SecondaryButton>Edit</SecondaryButton>
                                    </Link>
                                </td>
                                <td>
                                    <DangerButton @click="destroy(user.id)">Delete</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No Users available</p>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl mb-2">Danger Zone</h2>
                <PrimaryButton class="mb-2" @click="migrate()">Run Migrations</PrimaryButton>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                </div>
            </div>
        </div>
</AppLayout>
</template>
