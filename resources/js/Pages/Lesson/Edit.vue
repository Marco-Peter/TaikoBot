<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import Box from '@/Components/Box.vue';
import PageContent from '@/Components/PageContent.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({ lesson: Object, participants: Object, lessonteachers: Object, teachers: Array });
const page = usePage();

const form = useForm({
    title: props.lesson.title,
    start: props.lesson.start,
    finish: props.lesson.finish,
    notes: props.lesson.notes,
});

const newTeacher = ref("");

const submit = () => {
    form.put(route("lessons.update", props.lesson.id));
}

function addTeacher(teacher) {
    router.post(route('lessons.addTeacher', props.lesson.id),
        { 'teacher': teacher.id }, { preserveScroll: true });
}

function setTeacher(teacher) {
    router.post(route('lessons.setTeacher', props.lesson.id),
        { 'teacher': teacher.id }, { preserveScroll: true });
}

function removeTeacher(teacher) {
    if (confirm(`Are you sure you want to remove ${teacher.first_name} ${teacher.last_name} as teacher from this lesson?`)) {
        router.post(route('lessons.removeTeacher', props.lesson.id),
            { 'teacher': teacher.id }, { preserveScroll: true });
    }
}

function setSignedIn(participant) {
    router.post(route('lessons.setSignedIn', props.lesson.id),
        { 'participant': participant.id }, { preserveScroll: true });
}

function setExcused(participant) {
    router.post(route('lessons.setExcused', props.lesson.id),
        { 'participant': participant.id }, { preserveScroll: true });
}

function setLate(participant) {
    router.post(route('lessons.setLate', props.lesson.id),
        { 'participant': participant.id }, { preserveScroll: true });
}

function setNoShow(participant) {
    router.post(route('lessons.setNoShow', props.lesson.id),
        { 'participant': participant.id }, { preserveScroll: true });
}

function goBack() {
    window.history.back();
}
</script>

<template>
    <AppLayout title="Edit Lesson">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex flex-row items-center gap-1">
                Edit Lesson

                <div class="flex-1" />

                <Link :href="route('courses.edit', [lesson.course_id])" v-if="page.props.auth.canEditCourses">
                    <SecondaryButton small>Edit Course</SecondaryButton>
                </Link>
                <Link :href="route('lessons.show', [lesson.id])" v-if="page.props.auth.canEditCourses">
                    <SecondaryButton small>Open</SecondaryButton>
                </Link>
                <Link @click="goBack">
                    <SecondaryButton small>Back</SecondaryButton>
                </Link>
            </h2>
        </template>

        <PageContent>

        <!-- General Lesson Information -->
        <Box>
            <form @submit.prevent="submit">
                <InputLabel for="title" value="Title" />
                <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" />
                <InputError :message="form.errors.title" class="mt-2" />

                <div class="mt-4 flex flex-row gap-2">
                    <div>
                        <InputLabel for="start" value="Start" />
                        <TextInput id="start" v-model="form.start" type="datetime-local" class="mt-1 block w-full text-xs p-1" />
                        <InputError :message="form.errors.start" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="finish" value="Finish" />
                        <TextInput id="finish" v-model="form.finish" type="datetime-local" class="mt-1 block w-full text-xs p-1" />
                        <InputError :message="form.errors.finish" class="mt-2" />
                    </div>
                </div>

                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="notes" value="Notes" />
                    <textarea id="notes" v-model="form.notes" cols="30" rows="10"
                        placeholder="Private lesson notes, only visible for teachers"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                    <InputError :message="form.errors.notes" class="mt-2" />
                </div>

                <div class="flex flex-row gap-2 items-stretch mt-4">
                    <PrimaryButton type="submit">Update Lesson</PrimaryButton>
                    <Link :href="route('courses.edit', props.lesson.course_id)">
                        <SecondaryButton>Discard Changes</SecondaryButton>
                    </Link>
                </div>
            </form>
        </Box>

        <!-- Participants List -->
        <Box>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Participants</h2>
            <div v-for="participant in participants"
                class=""
                :class="participant.pivot.participation == 'signed_out' ? 'text-gray-500' : ''">
                <div class="font-bold text-base"
                    :class="participant.pivot.participation == 'signed_out' ? 'line-through' : ''">
                    <h3>
                        {{ participant.first_name }}
                        {{ participant.last_name }}
                    </h3>
                    <p v-if="participant.pivot.participation === 'assistance'">Assistant</p>
                </div>
                <div class="mb-2 italic text-sm">
                    {{ participant.message }}
                </div>
                <div v-if="participant.pivot.participation === 'signed_in'" class="flex flex-row gap-2">
                    <SecondaryButton :small="true" @click="setExcused(participant)">Excused</SecondaryButton>
                    <SecondaryButton :small="true" @click="setLate(participant)">Late</SecondaryButton>
                    <SecondaryButton :small="true" @click="setNoShow(participant)">No Show</SecondaryButton>
                </div>
                <div v-else-if="participant.pivot.participation === 'waitlist'">
                    On Waitlist
                    <SecondaryButton class="ml-1" :small="true" @click="setSignedIn(participant)">undo</SecondaryButton>
                </div>
                <div v-else-if="participant.pivot.participation === 'late'">
                    Was late
                    <SecondaryButton class="ml-1" :small="true" @click="setSignedIn(participant)">undo</SecondaryButton>
                </div>
                <div v-else-if="participant.pivot.participation === 'no_show'">
                    Did not show up
                    <SecondaryButton class="ml-1" :small="true" @click="setSignedIn(participant)">undo</SecondaryButton>
                </div>
                <div v-else-if="participant.pivot.participation === 'signed_out'">
                    Signed out / excused
                    <SecondaryButton class="ml-1" :small="true" @click="setSignedIn(participant)">undo</SecondaryButton>
                </div>
            </div>
        </Box>

        <!-- Teachers List -->
        <Box>
            <h1 class="font-semibold text-xl mb-2">Who's Teaching?</h1>
            <table v-if="lessonteachers.length" class="mt-3">
                <tbody>
                    <tr v-for="lessonteacher in lessonteachers" :key="lessonteacher.id">
                        <td class="pr-5">
                            {{ lessonteacher.first_name }}
                            {{ lessonteacher.last_name[0] }}.
                            {{ lessonteacher.first_name === "Mark" ? "😏" : "" }}
                        </td>
                        <td class="pr-5">
                            <DangerButton :small="true" @click="removeTeacher(lessonteacher)">Remove</DangerButton>
                        </td>
                        <td>{{ lessonteacher.message }}</td>
                    </tr>
                </tbody>
            </table>
            <p v-else>No Teachers added</p>

            <div class="flex flex-row items-center gap-2 mt-4">
                <select v-model="newTeacher"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800 p-1 text-sm">
                    <option value="" disabled>--- Select a Teacher ---</option>
                    <option v-for="teacher in teachers" :key="teacher.id" :value="teacher">{{ teacher.first_name }}
                    {{
                    teacher.last_name }}</option>
                </select>
                <PrimaryButton small :disabled="newTeacher === ''" @click="setTeacher(newTeacher)">
                    Set
                </PrimaryButton>
                <PrimaryButton small :disabled="newTeacher === ''" @click="addTeacher(newTeacher)">
                    Add
                </PrimaryButton>
            </div>
        </Box>

        </PageContent>
    </AppLayout>
</template>
