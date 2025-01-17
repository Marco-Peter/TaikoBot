<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({ lesson: Object, participants: Array, lessonteachers: Array });

</script>

<template>

    <AppLayout title="Show Course">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ lesson.name }}
            </h2>
        </template>

        <div class="pt-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl mb-2 mt-3 ml-3">{{ lesson.title }}</h2>
                    <h3 class="font-semibold text-l mb-2 mt-3 ml-3">{{ new Date(lesson.start).toLocaleString(undefined, {
                    weekday: "short",
                    month: "short",
                    day: "2-digit",
                    year: "2-digit",
                    hour: "2-digit",
                    minute: "2-digit",
                }) }}</h3>
                    <div v-if="lesson.notes">
                        <div class="w-full sm:max-w-2xl my-6 ml-3 bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg"
                            v-html="lesson.notes" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Show Participants -->
        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-3 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl mb-2 mt-3">Teachers</h2>
                    <div v-if="lessonteachers.length">
                        <div v-for="teacher in lessonteachers" class="flex mt-3">
                            <div class="mr-3"><img class="h-8 w-8 rounded-full object-cover"
                                    :src="teacher.profile_photo_url" :alt="teacher.nickname"></div>
                            <div>{{ teacher.first_name }} {{ teacher.last_name }}</div>
                        </div>
                    </div>
                    <p v-else>No Teachers</p>

                    <h2 class="font-semibold text-xl mb-2 mt-3">Participants</h2>
                    <div v-if="participants.length">
                        <div v-for="participant in participants" class="flex mt-3">
                            <div class="mr-3"><img class="h-8 w-8 rounded-full object-cover"
                                    :src="participant.profile_photo_url" :alt="participant.nickname"></div>
                            <div>{{ participant.first_name }} {{ participant.last_name }}</div>
                        </div>
                    </div>
                    <p v-else>No Participants</p>

                    <Link :href="route('dashboard')">
                    <SecondaryButton class="mt-6">Back</SecondaryButton>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
