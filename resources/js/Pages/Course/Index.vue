<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Box from '@/Components/Box.vue';
import PageContent from '@/Components/PageContent.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { formatDate } from '@/utils';

const props = defineProps({ courses: Object });

// Partition courses into running/upcoming/past
const now = new Date();

const pastCourses = computed(() => {
    return props.courses.filter(course => {
        if (course.last_lesson?.finish) {
            return new Date(course.last_lesson.finish) < now;
        }
        return false;
    });
});

const runningCourses = computed(() => {
    return props.courses.filter(course => {
        if (course.first_lesson?.start && course.last_lesson?.finish) {
            const started = new Date(course.first_lesson.start) <= now;
            const notFinished = new Date(course.last_lesson.finish) >= now;
            return started && notFinished;
        }
        return false;
    });
});

const upcomingCourses = computed(() => {
    return props.courses.filter(course => {
        if (course.first_lesson?.start) {
            return new Date(course.first_lesson.start) > now;
        }
        // Upcoming if no lessons yet
        return !course.first_lesson;
    });
});

const courseInfo = computed(() => {
    return [
        { title: "Running Courses", courses: runningCourses },
        { title: "Upcoming Courses", courses: upcomingCourses },
        { title: "Past Courses", courses: pastCourses },
    ];
});

function showDates(course) {
    const startDate = course.first_lesson?.start && formatDate(course.first_lesson.start);
    const lastDate = course.last_lesson?.finish && formatDate(course.last_lesson.finish);

    if (startDate && startDate !== lastDate) {
        return `${startDate} - ${lastDate}`;
    } else if (startDate) {
        return startDate;
    } else {
        return "-";
    }
}

function destroy(id, title) {
    if (confirm(`Are you sure you want to delete "${title}"?`)) {
        router.delete(route('courses.destroy', id), { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout title="Courses">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Courses
            </h2>
        </template>

        <PageContent>
            <template v-for="info in courseInfo" :key="info.title">
                <Box v-if="info.courses.value.length">
                <h2 class="font-semibold text-xl mb-4">{{ info.title }} </h2>
                <table>
                    <thead>
                        <tr>
                            <th class="px-2 text-left">Name</th>
                            <th class="px-2 text-left">Participants</th>
                            <th class="px-2 text-left">Dates</th>
                            <th class="px-2 w-full"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="course in info.courses.value" :key="course.id">
                            <td class="px-2 whitespace-nowrap">{{ course.name }}</td>
                            <td class="px-2">{{ course.participants_count }} / {{ course.capacity }}</td>
                            <td class="px-2 whitespace-nowrap text-xs">{{ showDates(course) }}</td>
                            <td class="px-2">
                                <div class="flex flex-row items-center gap-2">
                                <Link :href="route('courses.edit', course.id)">
                                    <SecondaryButton small>Edit</SecondaryButton>
                                </Link>
                                <DangerButton small @click="destroy(course.id, course.name)">Delete</DangerButton>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </Box>
            </template>

            <Box v-if="!courses.length">
                <p>No Courses available</p>
            </Box>

            <Box>
                <Link :href="route('courses.create')">
                    <PrimaryButton class="mb-2">Add Course</PrimaryButton>
                </Link>
            </Box>

        </PageContent>
    </AppLayout>
</template>
