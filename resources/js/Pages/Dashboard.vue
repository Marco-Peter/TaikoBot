<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    unreadMessages: Array,
    coursesSignedUp: Object,
    coursesNotSignedUp: Object,
    dashboardGreeting: String,
});

const showSubscribeToPushNotificationsPropt = ref(false);

function isPushNotificationSupported() {
    return "serviceWorker" in navigator && "PushManager" in window;
}

function updatePushSubscription() {
    Notification.requestPermission().then(function (consent) {
        if (consent === "granted") {
            createNotificationSubscription().then(function (subscription) {
                console.log("Subscription created!");
                router.post(route('users.updatePushSubscription', props.user.id), subscription.toJSON());
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
        </template>

        <div v-if="dashboardGreeting">
            <div class="py-3">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg prose dark:prose-invert"
                            v-html="dashboardGreeting" />
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="py-3 max-w-7xl mx-auto sm:px-6 lg:px-8" v-if="showSubscribeToPushNotificationsPropt">
                <p>Please subscribe for Push Notifications, so that you will be updated automatically, when somebody get's in
                touch with you.</p>
                <SecondaryButton class="mt-5" @click="updatePushSubscription">Subscribe</SecondaryButton>
            </div>
            <div class="py-3">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            News and Messages
                        </h2>
                        <p v-for="um in unreadMessages">
                            <Link :href="route('channels.messages.index', um.id)">{{ um.count }} unread messages in {{
                                um.name }}</Link>
                        </p>
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
                                    <p v-for="teacher in lesson.teachers" :key="teacher.id" class="italic">{{
                                        teacher.first_name
                                    }} {{ teacher.last_name }}</p>
                                </div>
                                <Link v-if="lesson.pivot.participation == 'teacher'"
                                    :href="route('lessons.edit', [lesson.id])">
                                <SecondaryButton>Edit Lesson</SecondaryButton>
                                </Link>
                                <DangerButton v-else-if="lesson.pivot.participation == 'signed_in'"
                                    @click="signOut(lesson)">
                                    Sign Out</DangerButton>
                                <SecondaryButton v-else-if="lesson.pivot.participation == 'signed_out'"
                                    @click="signIn(lesson)">
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
                                <SecondaryButton>Details</SecondaryButton>
                                </Link>
                            </div>
                        </div>
                        <p v-else>No courses available</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
