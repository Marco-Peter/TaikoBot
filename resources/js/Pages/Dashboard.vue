<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({ user: Object, coursesSignedUp: Object, coursesNotSignedUp: Object });

function signOut(id) {
    let message = prompt("Message to teachers", "");
    router.post(route('lessons.signout', id), { 'message': message });
}

function signIn(id) {
    let message = prompt("Message to teachers", "");
    router.post(route('lessons.signin', id), { 'message': message});
}

function sendMessage(id) {
    let message = prompt("Message to teachers", "");
    if (message != null) {
        router.post(route('lessons.sendMessage', id), { 'message': message});
    }
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Hello {{ user.first_name }}, let's play some Taiko!
            </h2>
        </template>

        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        News and Messages
                    </h2>
                    <p>Hi, the most recent messages will be presented here.</p>
                </div>
            </div>
        </div>

        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Your next Lessons
                    </h2>
                    <table v-if="user.lessons.length">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Course</th>
                                <th colspan="2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lesson in user.lessons">
                                <td>{{ lesson.start }}</td>
                                <td>{{ lesson.title }}</td>
                                <td>{{ lesson.course.name }}</td>
                                <td>
                                    <DangerButton v-if="lesson.pivot.participation == 'signed_in'" @click="signOut(lesson.id)">Sign Out</DangerButton>
                                    <SecondaryButton v-else @click="signIn(lesson.id)">Sign In</SecondaryButton>
                                </td>
                                <td>
                                    <SecondaryButton @click="sendMessage(lesson.id)">Send Message</SecondaryButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>Nothing to play</p>
                </div>
            </div>
        </div>

        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Your Signed Up Courses
                    </h2>
                    <p>Those are the courses and workshops you are signed up for. Looking forward to see you!</p>
                    <table v-if="coursesSignedUp.length">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Begin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="course in coursesSignedUp">
                                <td>{{ course.name }}</td>
                                <td>{{ course.lessons[0].start }}</td>
                                <td>
                                    <Link :href="route('courses.show', [course.id])">
                                    <SecondaryButton>Details</SecondaryButton>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>Not signed up to any courses</p>
                </div>
            </div>
        </div>

        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Your Available Courses
                    </h2>
                    <p>Looking for new challenges? Here you can sign up to new courses and workshops.</p>
                    <table v-if="coursesNotSignedUp.length">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Begin</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="course in coursesNotSignedUp">
                                <td>{{ course.name }}</td>
                                <td>{{ course.lessons[0].start }}</td>
                                <td>
                                    <Link :href="route('courses.show', [course.id])">
                                    <SecondaryButton>Details</SecondaryButton>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No courses available</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>