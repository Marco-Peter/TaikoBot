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
                            <th class="pr-5 text-left">Name</th>
                            <th class="pr-5">Participants</th>
                            <th class="pr-5" colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="course in courses">
                            <td class="pr-5">{{ course.name }}</td>
                            <td class="pr-5">{{ course.participants_count }} / {{ course.capacity }}</td>
                            <td class="pr-1 py-px">
                                <Link :href="route('courses.edit', course.id)">
                                    <SecondaryButton small>Edit</SecondaryButton>
                                </Link>
                            </td>
                            <td class="py-px">
                                <DangerButton small @click="destroy(course.id, course.name)">Delete</DangerButton>
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
