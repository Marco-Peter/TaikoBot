<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({ courses: Object });

function destroy(id) {
    if (confirm("Are you sure you want to delete?")) {
        router.delete(route('courses.destroy', id));
    }
}
</script>

<template>
    <AppLayout template="Courses">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Courses
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Link :href="route('courses.create')">
                <PrimaryButton>Add Course</PrimaryButton>
                </Link>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <table v-if="courses.length">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Capacity</th>
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="course in courses">
                                <td>{{ course.name }}</td>
                                <td>{{ course.capacity }}</td>
                                <td>
                                    <Link :href="route('courses.edit', course.id)">
                                    <SecondaryButton>Edit</SecondaryButton>
                                    </Link>
                                </td>
                                <td>
                                    <DangerButton @click="destroy(course.id)">Delete</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No Courses available</p>
                </div>
            </div>
        </div>
        {{ courses }}
    </AppLayout>
</template>
