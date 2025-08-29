<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PageContent from '@/Components/PageContent.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({ course: Object, signedIn: Boolean });
const page = usePage();

function signUp(id) {
    if (confirm("By signing up you confirm having read and agree with the terms of service as well as the code of conduct.")) {
        router.post(route('courses.signup', id));
    }
}

function goBack() {
    window.history.back();
}
</script>

<template>
    <AppLayout title="Show Course">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex flex-row items-center gap-1">
                {{ course.name }}

                <div class="flex-1" />

                <Link :href="route('courses.edit', [course.id])" v-if="page.props.auth.canEditCourses">
                    <SecondaryButton small>Edit</SecondaryButton>
                </Link>
                <Link @click="goBack">
                    <SecondaryButton small>Back</SecondaryButton>
                </Link>
            </h2>
        </template>

        <PageContent>

        <div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white px-4 py-3 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full sm:max-w-2xl bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg"
                        v-html="course.description" />
                    <p class="mt-4 italic text-sm">{{ course.capacity - course.participants_count }} spots available</p>
                </div>
            </div>
        </div>

        <!-- Show course material -->
        <div v-if="course.material && course.material.length">
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
        <div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-3 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl mb-2 mt-3">Lessons</h2>
                    <table v-if="course.lessons.length">
                        <thead>
                            <tr>
                                <th class="px-2 text-left">Date</th>
                                <th class="px-2 text-left">Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lesson in course.lessons" :class="new Date(lesson.finish) < Date.now() ? 'line-through' : ''">
                                <td class="px-2 font-mono text-xs">
                                    {{ new Date(lesson.start).toLocaleDateString() }}
                                    {{ new Date(lesson.finish).toLocaleString(undefined, {
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    }) }}-{{ new Date(lesson.finish).toLocaleString(undefined, {
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    }) }}
                                </td>
                                <td class="px-2">
                                    <span v-for="teacher in lesson.teachers" :key="teacher.id" class="">
                                        {{ teacher.first_name }}
                                        {{ teacher.last_name[0] }}
                                    </span>
                                    <span v-if="lesson.teachers.length === 0">
                                        &ndash;
                                    </span>
                                </td>
                                <td class="px-2">
                                    <PrimaryButton
                                        small
                                    >
                                        <Link :href="page.props.auth.canEditCourses ? route('lessons.edit', [lesson.id]) : route('lessons.show', [lesson.id])">
                                        {{ page.props.auth.canEditCourses ? 'Edit' : 'Open' }}
                                        </Link>
                                    </PrimaryButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No Lessons available</p>
                    <div class="flex flex-row gap-2">
                        <PrimaryButton v-if="!signedIn && (course.participants_count < course.capacity)"
                            @click="signUp(course)" class="mt-3">Sign Up</PrimaryButton>

                        <SecondaryButton @click="goBack" class="mt-3">Back</SecondaryButton>
                    </div>
                </div>
            </div>
        </div>

        </PageContent>
    </AppLayout>
</template>
