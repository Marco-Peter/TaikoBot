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
                <Link :href="route('users.create')">
                    <PrimaryButton class="mt-4">Add User</PrimaryButton>
                </Link>
            </Box>

            <Box v-if="users.length" v-for="user in users">
                <div class="flex flex-row items-center">
                    <div class="w-full">
                        <h3 class="font-semibold leading-tight">{{ user.nickname }}</h3>
                        <div>{{ user.first_name }} {{ user.last_name }}</div>
                        <div>{{ user.email }}</div>
                    </div>
                    <div class="w-1/2">
                        <div>Role: {{ user.role }}</div>
                        <div>Group: {{ user.team ? user.team.name : 'NO GROUP' }}</div>
                        <div>Karma: {{ user.karma === null ? '\u{221E}' : user.karma }}</div>
                    </div>
                    <div class="w-1/8">
                        <Link :href="route('users.edit', user.id)">
                            <SecondaryButton small>Edit</SecondaryButton>
                        </Link>
                        <DangerButton small @click="destroy(user.id)">Delete</DangerButton>
                    </div>
                </div>
            </Box>
            <Box v-else>
                <p>No Users available</p>
            </Box>

            <Box>
                <h2 class="font-semibold text-xl mb-2">Danger Zone</h2>
                <PrimaryButton class="w-40" @click="migrate()">Run Migrations</PrimaryButton>
            </Box>
        </PageContent>
    </AppLayout>
</template>
