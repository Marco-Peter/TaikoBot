<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({ course: Object, signedIn: Boolean });

function signUp(id) {
    if (confirm("By signing up you confirm having read and agree with the terms of service as well as the code of conduct.")) {
        router.post(route('courses.signup', id));
    }
}
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
                    <PrimaryButton v-if="!signedIn" @click="signUp(course)">Sign Up</PrimaryButton>

                    <Link :href="route('dashboard')">
                    <SecondaryButton>Back</SecondaryButton>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
