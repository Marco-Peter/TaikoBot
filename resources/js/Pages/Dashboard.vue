<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const props = defineProps({
    user: Object,
    studentLessons: Object,
    teacherLessons: Object,
    coursesSignedUp: Object,
    coursesNotSignedUp: Object,
    dashboardGreeting: String,
    pushServerPublicKey: String,
});

const showSubscribeToPushNotificationsPropt = ref(false);

function isPushNotificationSupported() {
    return "serviceWorker" in navigator && "PushManager" in window;
}

function updatePushSubscription() {
    Notification.requestPermission().then((consent) => {
        if (consent === "granted") {
            createNotificationSubscription().then((subscription) => {
                console.log("Subscription created!");
                router.post(route('users.updatePushSubscription', props.user.id), subscription.toJSON());
            })
                .catch((error) => {
                    console.error(`Create subscription error (${error})`);
                })
                .catch((error) => {
                    console.error(`Request permission error (${error})`);
                });
            showSubscribeToPushNotificationsPropt.value = false;
        }
    });
}

async function deletePushSubscription() {
    const subscription = await getUserSubscription();
    router.post(route('users.deletePushSubscription', props.user.id),
        {
            endpoint: subscription.endpoint,
        });
    subscription.unsubscribe()
        .then(() => {
            console.log('Unsubscribed from push notifications successfully');
        })
        .catch((error) => {
            console.error(`Unsubscription failed (${error})`);
        });
}

async function createNotificationSubscription() {
    return navigator.serviceWorker.ready.then(function (serviceWorker) {
        return serviceWorker.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: props.pushServerPublicKey,
        }).then(function (subscription) {
            console.log(`${props.user.first_name} is subscribed.`, subscription);
            return subscription;
        });
    });
}

async function getUserSubscription() {
    return navigator.serviceWorker.ready.then(function (serviceWorker) {
        return serviceWorker.pushManager.getSubscription();
    }).then(function (pushSubscription) {
        return pushSubscription;
    });
}

function signOut(lesson) {
    let message = prompt("Optional message to teachers", "");
    if (message != null) {
        router.post(route('lessons.signout', lesson.id), { 'message': message });
    }
}

function signIn(lesson) {
    let message = prompt("Optional message to teachers", "");
    if (message != null) {
        router.post(route('lessons.signin', lesson.id), { 'message': message });
    }
}

function compensate(lesson) {
    let message = prompt("Optional message to teachers", "");
    if (message != null) {
        router.post(route('lessons.compensate', lesson.id), { 'message': message });
    }
}

function assist(lesson) {
    let message = prompt("Optional message to teachers", "");
    if (message != null) {
        router.post(route('lessons.assist', lesson.id), { 'message': message });
    }
}

function sendMessage(lesson) {
    let target = lesson.pivot.participation == 'teacher' ? "students" : "teachers";
    let message = prompt(`Message to ${target}`, "");
    if (message != null) {
        router.post(route('lessons.sendMessage', lesson.id), { 'message': message });
    }
}

if (isPushNotificationSupported()) {
    getUserSubscription().then((subscription) => {
        if (subscription == null) {
            showSubscribeToPushNotificationsPropt.value = true;
        }
    });
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Hello {{ user.first_name }}, let's play some Taiko!
            </h2>
            Your TaikoKarma is {{ user.karma === null ? '\u{221E}' : user.karma }}
        </template>

        <div v-if="dashboardGreeting">
            <div class="py-3">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg">
                        <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg prose dark:prose-invert"
                            v-html="dashboardGreeting" />
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <!-- Prompt for allowing push notifications -->
            <div class="py-3 max-w-7xl mx-auto sm:px-6 lg:px-8" v-if="showSubscribeToPushNotificationsPropt">
                <p>Please subscribe for Push Notifications, so that you will be updated automatically.</p>
                <SecondaryButton class="mt-5" @click="updatePushSubscription">Subscribe</SecondaryButton>
            </div>

            <!-- Available courses and workshop to sign up -->
            <div v-if="coursesNotSignedUp.length" class="py-3">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-xl sm:rounded-lg">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Your Available Courses and Workshops
                        </h2>
                        <p>Looking for new challenges? Here you can sign up to new courses and workshops.</p>
                        <div v-for="course in coursesNotSignedUp" class="my-3 px-4 py-2 bg-white dark:bg-gray-800">
                            <h3 class="font-bold text-lg">{{ course.name }} ({{ course.capacity -
                                course.participants_count }} places available)</h3>
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
                            <SecondaryButton>Details / Sign In</SecondaryButton>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List of the signed up courses and workshops -->
            <div v-if="coursesSignedUp.length" class="py-3">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="overflow-hidden sm:rounded-lg">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Your Signed Up Courses and Workshops
                        </h2>
                        <p>Those are the courses and workshops you are signed up for. Looking forward to see you!</p>
                        <div v-for="course in coursesSignedUp" class="my-3 px-4 py-2 bg-white dark:bg-gray-800">
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
                </div>
            </div>

            <!-- List of the upcoming teaching lessons -->
            <div v-if="teacherLessons.length" class="py-3">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="overflow-hidden sm:rounded-lg">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Your Teaching Lessons
                        </h2>
                        <p>Those are the lessons where you will be teaching. ありがとうございます!</p>
                        <div v-for="lesson in teacherLessons" class="my-3 px-4 py-2 bg-white dark:bg-gray-800">
                            <div class="pb-2">
                                <h3 class="font-bold text-lg">{{ new
                                    Date(lesson.start).toLocaleString(undefined, {
                                        weekday: "short",
                                        month: "short",
                                        day: "2-digit",
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    }) }}</h3>
                                <p>{{ lesson.title }} of {{ lesson.course.name }}</p>
                                <p>{{ lesson.participants_count }} students signed in.</p>
                            </div>
                            <Link :href="route('lessons.edit', [lesson.id])">
                            <SecondaryButton class="my-2 mr-2">Edit Lesson</SecondaryButton>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List of the upcoming student lessons -->
            <div v-if="studentLessons.length" class="py-3">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="overflow-hidden sm:rounded-lg">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Your Next Lessons
                        </h2>
                        <div v-for="lesson in studentLessons" class="my-3 px-4 py-2 bg-white dark:bg-gray-800"
                            :class="lesson.pivot === undefined || lesson.pivot.participation === 'signed_out' ? 'text-gray-500' : ''">
                            <div class="pb-2">
                                <h3 class="font-bold text-lg"
                                    :class="lesson.pivot === undefined || lesson.pivot.participation === 'signed_out' ? 'line-through' : ''">
                                    {{ new
                                        Date(lesson.start).toLocaleString(undefined, {
                                            weekday: "short",
                                            month: "short",
                                            day: "2-digit",
                                            hour: "2-digit",
                                            minute: "2-digit",
                                        }) }}
                                    <span
                                        v-if="lesson.pivot && lesson.pivot.participation === 'assistance'">(Assisting)</span>
                                    <span v-if="lesson.pivot && lesson.pivot.participation === 'waitlist'">(on
                                        waitlist)</span>
                                </h3>
                                <span v-if="lesson.pivot && lesson.pivot.participation === 'waitlist'">You will be
                                    notified as
                                    soon as your space gets available</span>
                                <p>{{ lesson.title }} of {{ lesson.course.name }}</p>
                                <p v-for="teacher in lesson.teachers" :key="teacher.id" class="italic">{{
                                    teacher.first_name
                                    }} {{ teacher.last_name }}</p>
                                <p>{{ lesson.participants_count }} students signed in ({{ lesson.course.capacity -
                                    lesson.participants_count }} spaces free).</p>
                            </div>
                            <SecondaryButton class="my-2 mr-2" v-if="page.props.auth.canAssistLessons &&
                                (lesson.pivot === undefined || lesson.pivot.participation === 'signed_out')"
                                @click="assist(lesson)">Assist</SecondaryButton>
                            <SecondaryButton class="my-2 mr-2" v-if="lesson.pivot === undefined"
                                @click="compensate(lesson)">
                                {{ lesson.course.capacity - lesson.participants_count <= 0 ? 'Compensate (Waitlist)'
                                    : 'Compensate' }}</SecondaryButton>
                                    <DangerButton class="my-2 mr-2" v-else-if="lesson.pivot.participation === 'signed_in' ||
                                        lesson.pivot.participation === 'waitlist' ||
                                        lesson.pivot.participation === 'assistance'" @click="signOut(lesson)">
                                        Sign Out</DangerButton>
                                    <SecondaryButton class="my-2 mr-2"
                                        v-else-if="lesson.pivot.participation === 'signed_out'" @click="signIn(lesson)">
                                        Sign In</SecondaryButton>
                                    <SecondaryButton class="mr-2" v-if="lesson.pivot !== undefined"
                                        @click="sendMessage(lesson)">Send
                                        Message</SecondaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
