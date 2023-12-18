<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({ course: Object, teams: Object });
</script>

<template>
    <AppLayout title="Show Course">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ course.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <p>{{ course.description }}</p>
                    <p>Capacity: {{ course.capacity }}</p>
                </div>
            </div>
        </div>

        <div class="py-1">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl mb-2 mt-3">Lessons</h2>
                    <table v-if="course.lessons.length">
                        <thead>
                            <tr>
                                <th>Start</th>
                                <th>Finish</th>
                                <th>Title</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lesson in course.lessons">
                                <td>{{ lesson.start }}</td>
                                <td>{{ lesson.finish }}</td>
                                <td>{{ lesson.title }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No Lessons available</p>
                    <Link :href="route('dashboard')">
                    <PrimaryButton>Sign Up</PrimaryButton>
                    </Link>

                    <Link :href="route('dashboard')">
                    <SecondaryButton>Back</SecondaryButton>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
