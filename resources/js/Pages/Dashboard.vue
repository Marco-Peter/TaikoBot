<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({ user: Object, coursesSignedUp: Object, coursesNotSignedUp: Object });

function signOut(lesson) {
    let message = prompt("Message to teachers", "");
    router.post(route('lessons.signout', lesson.id), { 'message': message });
}

function signIn(lesson) {
    let message = prompt("Message to teachers", "");
    router.post(route('lessons.signin', lesson.id), { 'message': message });
}

function sendMessage(lesson) {
    let target = lesson.pivot.participation == 'teacher' ? "students" : "teachers";
    let message = prompt(`Message to ${target}`, "");
    if (message != null) {
        router.post(route('lessons.sendMessage', lesson.id), { 'message': message });
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
                <div class="overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Your next Lessons
                    </h2>
                    <div v-if="user.lessons.length">
                        <div v-for="lesson in user.lessons" class="my-3 py-1 bg-white dark:bg-gray-800"
                            :class="lesson.pivot.participation == 'signed_out' ? 'text-gray-500' : ''">
                            <div class="pb-2">
                                <h3 class="font-bold text-lg"
                                    :class="lesson.pivot.participation == 'signed_out' ? 'line-through' : ''">{{ new
                                        Date(lesson.start).toLocaleString(undefined, {
                                            weekday: "short",
                                            month: "short",
                                            day: "2-digit",
                                            hour: "2-digit",
                                            minute: "2-digit",
                                        }) }}</h3>
                                <p class="pb-2">{{ lesson.title }} of {{ lesson.course.name }}</p>
                                <p v-for="teacher in lesson.teachers" :key="teacher.id" class="italic">{{ teacher.first_name
                                }} {{ teacher.last_name }}</p>
                            </div>
                            <Link v-if="lesson.pivot.participation == 'teacher'" :href="route('lessons.edit', [lesson.id])">
                            <SecondaryButton>Edit Lesson</SecondaryButton>
                            </Link>
                            <DangerButton v-else-if="lesson.pivot.participation == 'signed_in'" @click="signOut(lesson)">
                                Sign Out</DangerButton>
                            <SecondaryButton v-else-if="lesson.pivot.participation == 'signed_out'" @click="signIn(lesson)">
                                Sign In</SecondaryButton>
                            <SecondaryButton @click="sendMessage(lesson)">Send Message</SecondaryButton>
                        </div>
                    </div>
                    <p v-else>Nothing to play</p>
                </div>
            </div>
        </div>

        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Your Signed Up Courses and Workshops
                    </h2>
                    <p>Those are the courses and workshops you are signed up for. Looking forward to see you!</p>
                    <div v-if="coursesSignedUp.length">
                        <div v-for="course in coursesSignedUp" class="my-3 py-1 bg-white dark:bg-gray-800">
                            <h3 class="font-bold text-lg">{{ course.name }}</h3>
                            <p class="pb-2">From
                                {{ new Date(course.first_lesson.start).toLocaleString(undefined, {
                                    weekday: "long",
                                    month: "long",
                                    day: "2-digit",
                                    year: "numeric",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                }) }} to
                                {{ new Date(course.last_lesson.finish).toLocaleString(undefined, {
                                    weekday: "long",
                                    month: "long", day: "2-digit"
                                }) }}</p>
                            <Link :href="route('courses.show', [course.id])">
                            <SecondaryButton>Details</SecondaryButton>
                            </Link>
                        </div>
                    </div>
                    <p v-else>Not signed up to any courses</p>
                </div>
            </div>
        </div>

        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Your Available Courses and Workshops
                    </h2>
                    <p>Looking for new challenges? Here you can sign up to new courses and workshops.</p>
                    <div v-if="coursesNotSignedUp.length">
                        <div v-for="course in coursesNotSignedUp" class="my-3 py-1 bg-white dark:bg-gray-800">
                            <h3 class="font-bold text-lg">{{ course.name }}</h3>
                            <p class="pb-2">From {{ new Date(course.first_lesson.start).toLocaleString(undefined, {
                                weekday: "long",
                                month: "long",
                                day: "2-digit",
                                year: "numeric",
                                hour: "2-digit",
                                minute: "2-digit",
                            }) }} to {{ new Date(course.last_lesson.finish).toLocaleString(undefined, {
    weekday: "long",
    month: "long",
    day: "2-digit",
})
}}</p>
                            <Link :href="route('courses.show', [course.id])">
                            <SecondaryButton>Details</SecondaryButton>
                            </Link>
                        </div>
                    </div>
                    <p v-else>No courses available</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
