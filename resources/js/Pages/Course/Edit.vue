<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ course: Object, teams: Object });

const form = useForm({
    name: props.course.name,
    description: props.course.description,
    capacity: String(props.course.capacity),
    teams: props.course.teams.map(({ id }) => id),
});

const newParticipantTeam = ref("");
const newParticipant = ref("");

const submit = () => {
    form.put(route("courses.update", props.course.id));
}

function destroyLesson(id) {
    if (confirm("Are you sure you want to delete this lesson?")) {
        router.delete(route('lessons.destroy', id));
    }
}

function addUser(user) {
    router.post(route('courses.addParticipant', props.course.id), { 'user': user.id });
}

function removeUser(user) {
    if (confirm(`Are you sure you want to remove ${user.first_name} ${user.last_name} from the course?`)) {
        router.post(route("courses.removeParticipant", props.course.id), { 'user': user.id });
    }
}

function updatePaid(user, paid) {
    router.post(route("courses.setPaid", props.course.id), { 'user': user, 'paid': paid });
}
</script>

<template>
    <AppLayout title="Edit Course">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Course
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.name" class="mt-2" />

                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="description" value="Description" />
                            <textarea id="description" v-model="form.description" cols="30" rows="10"
                                placeholder="Public course description - make it catchy"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <InputLabel for="capacity" value="Capacity" />
                        <TextInput id="capacity" v-model="form.capacity" type="number" class="mt-1 block w-full" />
                        <InputError :message="form.errors.capacity" class="mt-2" />

                        <h1 class="font-semibold text-xl mb-2 mt-3">Publish to Groups</h1>
                        <div v-for="team in props.teams" :key="team.id">
                            <input type="checkbox" :id="team.name" v-model="form.teams" :value="String(team.id)"
                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                            <label :for="team.name" class="ml-3">{{ team.name }}</label>
                        </div>

                        <PrimaryButton type="submit" class="mt-3">Submit</PrimaryButton>
                        <Link :href="route('courses.index')">
                        <SecondaryButton>Cancel</SecondaryButton>
                        </Link>
                    </form>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Link :href="route('lessons.create', { course_id: props.course.id })">
                <PrimaryButton>Add Lesson</PrimaryButton>
                </Link>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="font-semibold text-xl mb-2 mt-3">Lessons</h1>
                    <table v-if="course.lessons.length">
                        <thead>
                            <tr>
                                <th>Start</th>
                                <th>Finish</th>
                                <th>Title</th>
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lesson in course.lessons">
                                <td>{{ lesson.start }}</td>
                                <td>{{ lesson.finish }}</td>
                                <td>{{ lesson.title }}</td>
                                <td>
                                    <Link :href="route('lessons.edit', lesson.id)">
                                    <SecondaryButton>Edit</SecondaryButton>
                                    </Link>
                                </td>
                                <td>
                                    <DangerButton @click="destroyLesson(lesson.id)">Delete</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No Lessons available</p>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="font-semibold text-xl mb-2 mt-3">Participants</h1>
                    <select v-model="newParticipantTeam"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <option value="" disabled>--- Select a Group ---</option>
                        <option v-for="(team, index) in teams" :value="index">{{ team.name }}</option>
                    </select>

                    <select v-model="newParticipant" :disabled="newParticipantTeam === ''"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <option value="" disabled>--- Select a Participant ---</option>
                        <option v-if="newParticipantTeam !== ''" v-for="user in teams[newParticipantTeam].users"
                            :value="user">{{ user.first_name }} {{ user.last_name }}</option>
                    </select>
                    <PrimaryButton @click="addUser(newParticipant)">Add Participant</PrimaryButton>

                    <table v-if="course.participants.length">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Paid</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="participant in props.course.participants" :key="participant.id">
                                <td>{{ participant.first_name }}</td>
                                <td>{{ participant.last_name }}</td>
                                <td>
                                    <input type="checkbox" v-model="participant.pivot.paid" true-value="1" false-value="0"
                                        @change="updatePaid(participant.id, participant.pivot.paid)"
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                                </td>
                                <td>
                                    <DangerButton @click="removeUser(participant)">Remove</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else>No Participants signed up</p>
                </div>
            </div>
        </div>
        <p>{{ teams }}</p>
    </AppLayout>
</template>
