<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Box from '@/Components/Box.vue';
import PageContent from '@/Components/PageContent.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({ lesson: Object, participants: Array, lessonteachers: Array });

function goBack() {
    window.history.back();
}

</script>

<template>

    <AppLayout title="Show Lesson">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex flex-row items-center gap-1">
                Lesson

                <div class="flex-1" />

                <Link :href="route('lessons.edit', [lesson.id])">
                    <SecondaryButton small>Edit</SecondaryButton>
                </Link>
                <Link @click="goBack">
                    <SecondaryButton small>Back</SecondaryButton>
                </Link>
            </h2>
        </template>

        <PageContent>

        <Box>
            <h2 class="font-semibold text-xl">{{ lesson.title }}</h2>
            <h3 class="font-semibold text-l">{{ new Date(lesson.start).toLocaleString(undefined, {
                weekday: "short",
                month: "short",
                day: "2-digit",
                year: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
            }) }}</h3>
            <div v-if="lesson.notes">
                <div class="text-sm bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg"
                    v-html="lesson.notes" />
            </div>
        </Box>

        <!-- Show Participants -->
        <Box>
                <h2 class="font-semibold text-xl">Teachers</h2>
                <div v-if="lessonteachers.length">
                    <div v-for="teacher in lessonteachers" class="flex mt-3">
                        <div class="mr-3"><img class="h-8 w-8 rounded-full object-cover"
                                :src="teacher.profile_photo_url"></div>
                        <div>{{ teacher.first_name }} {{ teacher.last_name }}</div>
                    </div>
                </div>
                <p v-else>No Teachers</p>

                <h2 class="font-semibold text-xl">Participants</h2>
                <div v-if="participants.length">
                    <div v-for="participant in participants" class="flex mt-3">
                        <div class="mr-3"><img class="h-8 w-8 rounded-full object-cover"
                                :src="participant.profile_photo_url"></div>
                        <div>{{ participant.first_name }} {{ participant.last_name }}</div>
                    </div>
                </div>
                <p v-else>No Participants</p>
        </Box>

        </PageContent>
    </AppLayout>
</template>
