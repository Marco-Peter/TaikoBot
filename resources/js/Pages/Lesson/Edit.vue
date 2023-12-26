<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ lesson: Object, participants: Object, lessonteachers: Object, teachers: Array });

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
    router.post(route('lessons.addTeacher', props.lesson.id), { 'teacher': teacher.id });
}

function removeTeacher(teacher) {
    if (confirm(`Are you sure you want to remove ${teacher.first_name} ${teacher.last_name} as teacher from this lesson?`)) {
        router.post(route('lessons.removeTeacher', props.lesson.id), { 'teacher': teacher.id });
    }
}

function setLate(participant) {
    router.post(route('lessons.setLate', props.lesson.id), { 'participant': participant.id});
}

function setNoShow(participant) {
    router.post(route('lessons.setNoShow', props.lesson.id), { 'participant': participant.id});
}
</script>

<template>
    <AppLayout title="Edit Lesosn">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Lesson
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <InputLabel for="title" value="Title" />
                        <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.title" class="mt-2" />

                        <InputLabel for="start" value="Start" />
                        <TextInput id="start" v-model="form.start" type="datetime-local" class="mt-1 block w-full" />
                        <InputError :message="form.errors.start" class="mt-2" />

                        <InputLabel for="finish" value="Finish" />
                        <TextInput id="finish" v-model="form.finish" type="datetime-local" class="mt-1 block w-full" />
                        <InputError :message="form.errors.finish" class="mt-2" />

                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="notes" value="Notes" />
                            <textarea id="notes" v-model="form.notes" cols="30" rows="10"
                                placeholder="Private lesson notes, only visible for teachers"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                            <InputError :message="form.errors.notes" class="mt-2" />
                        </div>

                        <PrimaryButton type="submit" class="mt-3">Submit</PrimaryButton>
                        <Link :href="route('courses.edit', props.lesson.course_id)">
                        <SecondaryButton>Cancel</SecondaryButton>
                        </Link>
                    </form>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="font-semibold text-xl mb-2 mt-3">Teachers</h1>
                    <select v-model="newTeacher"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <option value="" disabled>--- Select a Teacher ---</option>
                        <option v-for="teacher in teachers" :key="teacher.id" :value="teacher">{{ teacher.first_name }} {{
                            teacher.last_name }}</option>
                    </select>
                    <PrimaryButton :disabled="newTeacher === ''" @click="addTeacher(newTeacher)">Add Teacher</PrimaryButton>
                    <table v-if="lessonteachers.length">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lessonteacher in lessonteachers" :key="lessonteacher.id">
                                <td>{{ lessonteacher.first_name }}</td>
                                <td>{{ lessonteacher.last_name }}</td>
                                <td>
                                    <DangerButton @click="removeTeacher(lessonteacher)">Remove</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No Teachers added</p>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="font-semibold text-xl leading-tight">Participants</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th colspan="3">Status</th>
                                <th>Message from Student</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="participant in participants">
                                <td>{{ participant.first_name }}</td>
                                <td>{{ participant.last_name }}</td>
                                <td>{{ participant.pivot.participation }}</td>
                                <td v-if="participant.pivot.participation == 'signed_in'">
                                    <SecondaryButton @click="setLate(participant)">Late</SecondaryButton>
                                </td>
                                <td v-if="participant.pivot.participation == 'signed_in'">
                                    <SecondaryButton @click="setNoShow(participant)">No Show</SecondaryButton>
                                </td>
                                <td>{{ participant.message }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>