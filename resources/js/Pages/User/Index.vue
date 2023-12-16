<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

defineProps({ users: Object })

function destroy(id) {
    if (confirm("Are you sure you want to delete?")) {
        router.delete(route('users.destroy', id));
    }
}
</script>

<template>
    <AppLayout template="Users">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Link :href="route('users.create')">
                <PrimaryButton>Add User</PrimaryButton>
                </Link>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <table v-if="users.length">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Teams</th>
                                <!--<th>Income Group</th>-->
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users">
                                <td>{{ user.first_name }}</td>
                                <td>{{ user.last_name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.role }}</td>
                                <td>{{ user.team.name }}</td>
                                <!--<td>{{ user.income_group.name }}</td>-->
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
    </AppLayout>
</template>
