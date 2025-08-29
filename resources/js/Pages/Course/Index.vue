<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Box from '@/Components/Box.vue';
import PageContent from '@/Components/PageContent.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({ courses: Object });

function destroy(id, title) {
    if (confirm(`Are you sure you want to delete "${title}"?`)) {
        router.delete(route('courses.destroy', id), { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout title="Courses">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Courses
            </h2>
        </template>

        <PageContent>
            <Box>
                <table v-if="courses.length">
                    <thead>
                        <tr>
                            <th class="px-2 text-left">Name</th>
                            <th class="px-2 text-left">Participants</th>
                            <th class="px-2 w-full"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="course in courses">
                            <td class="px-2 whitespace-nowrap">{{ course.name }}</td>
                            <td class="px-2">{{ course.participants_count }} / {{ course.capacity }}</td>
                            <td class="px-2">
                                <div class="flex flex-row items-center gap-2">
                                <Link :href="route('courses.edit', course.id)">
                                    <SecondaryButton small>Edit</SecondaryButton>
                                </Link>
                                <DangerButton small @click="destroy(course.id, course.name)">Delete</DangerButton>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-else>No Courses available</p>

                <Link :href="route('courses.create')">
                    <PrimaryButton class="mb-2">Add Course</PrimaryButton>
                </Link>
            </Box>

        </PageContent>
    </AppLayout>
</template>
