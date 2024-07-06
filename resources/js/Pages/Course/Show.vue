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

        <div class="pt-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full sm:max-w-2xl mt-6 bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg"
                        v-html="course.description" />
                    <p class="mt-4">{{ course.capacity - course.participants_count }} places available.</p>
                </div>
            </div>
        </div>

        <!-- Show course material -->
        <div v-if="course.material && course.material.length" class="pt-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-3 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="font-semibold text-xl">Course Material</h1>
                </div>
            </div>
        </div>

        <div v-for="mat in course.material" class="pt-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-3 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="mt-3 text-xl">{{ mat.name }}</h1>
                    <p v-if="mat.notes" class="mt-5">{{ mat.notes }}</p>
                    <a v-if="mat.external" :href="mat.path" target="_blank">
                        <SecondaryButton class="mt-3">Open</SecondaryButton>
                    </a>
                    <a v-else :href="route('courses.downloadMaterial', mat.id)" download>
                        <SecondaryButton class="mt-3">Download</SecondaryButton>
                    </a>
                </div>
            </div>
        </div>

        <!-- Show lessons-->
        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-3 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl mb-2 mt-3">Lessons</h2>
                    <table v-if="course.lessons.length">
                        <thead>
                            <tr>
                                <th class="pr-5 text-left">Date</th>
                                <th class="pr-5 text-left">Title</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lesson in course.lessons">
                                <td class="pr-5">{{ new Date(lesson.start).toLocaleString(undefined, {
                    weekday: "short",
                    month: "short",
                    day: "2-digit",
                    year: "2-digit",
                    hour: "2-digit",
                    minute: "2-digit",
                }) }} to {{ new Date(lesson.finish).toLocaleString(undefined, {
                    hour: "2-digit",
                    minute: "2-digit",
                                    }) }}</td>
                                <td>{{ lesson.title }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No Lessons available</p>
                    <PrimaryButton v-if="!signedIn && (course.participants_count < course.capacity)"
                        @click="signUp(course)" class="mt-3">Sign Up</PrimaryButton>

                    <Link :href="route('dashboard')">
                    <SecondaryButton class="mt-3">Back</SecondaryButton>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
