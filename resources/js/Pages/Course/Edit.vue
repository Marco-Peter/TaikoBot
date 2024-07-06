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
router.reload();

const form = useForm({
    name: props.course.name,
    description: props.course.description,
    capacity: String(props.course.capacity),
    teams: props.course.teams.map(({ id }) => id),
});

const uploadForm = useForm({
    path: null,
    name: null,
    notes: null,
    external: false,
});
const uploadFileInput = ref("");

const newParticipantTeam = ref("");
const newParticipant = ref("");

const submit = () => {
    form.put(route("courses.update", props.course.id));
}

function uploadMaterial() {
    uploadForm.post(route("courses.uploadMaterial", props.course.id),
        { preserveScroll: true });

    uploadForm.path = null;
    uploadForm.name = null;
    uploadForm.notes = null;
    uploadForm.external = false;
}

function deleteMaterial(material) {
    if (confirm("Are you sure you wnat to delete this material?")) {
        router.post(route('courses.deleteMaterial', props.course.id),
            { 'material': material.id }, { preserveScroll: true });
    }
}

function destroyLesson(id) {
    if (confirm("Are you sure you want to delete this lesson?")) {
        router.delete(route('lessons.destroy', id), { preserveScroll: true });
    }
}

function addUser(user) {
    router.post(route('courses.addParticipant', props.course.id),
        { 'user': user.id }, { preserveScroll: true });
}

function removeUser(user) {
    if (confirm(`Are you sure you want to remove ${user.first_name} ${user.last_name} from the course?`)) {
        router.post(route("courses.removeParticipant", props.course.id),
            { 'user': user.id }, { preserveScroll: true });
    }
}

function updatePaid(user, paid) {
    router.post(route("courses.setPaid", props.course.id),
        { 'user': user, 'paid': paid }, { preserveScroll: true });
}
</script>

<template>
    <AppLayout title="Edit Course">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Course
            </h2>
        </template>

        <!-- Basic Properties -->
        <div class="py-12">
            <div class="px-4 py-3 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <div class="flex flex-col items-stretch gap-2">
                            <!-- Course Name -->
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput id="name" v-model="form.name" type="text" autocomplete="off"
                                    class="mt-1 block w-full" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Course Description -->
                            <div class="">
                                <InputLabel for="description" value="Description (Markdown Tags possible)" />
                                <textarea
                                    title="This text will be formatted using GitHub style Markdown format.&#013;Check Google about what is possible!&#013;Double line breaks for paragraph&#013;#, ##, ### for hierarchic titles&#013;'-' for unorderet lists&#013;'1)', '2)', '3)' for ordered lists, ..."
                                    id="description" v-model="form.description" cols="30" rows="10"
                                    placeholder="Public course description - make it catchy"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Course Capacity (max number of participants) -->
                            <div>
                                <InputLabel for="capacity" value="Capacity" />
                                <TextInput id="capacity" v-model="form.capacity" type="number" class="mt-1 block w-full" />
                                <InputError :message="form.errors.capacity" class="mt-2" />
                            </div>

                            <!-- Groups to which the Course is Published -->
                            <div>
                                <h1 class="font-semibold text-xl mb-2 mt-3">Publish to Groups</h1>
                                <div v-for="team in props.teams" :key="team.id">
                                    <input type="checkbox" :id="team.name" v-model="form.teams" :value="String(team.id)"
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                                    <label :for="team.name" class="ml-3">{{ team.name }}</label>
                                </div>
                            </div>

                            <!-- Form Submission -->
                            <div class="flex flex-col items-center gap-2">
                                <Link :href="route('courses.index')">
                                    <!-- Return without Changing Data-->
                                    <SecondaryButton>Cancel</SecondaryButton>
                                </Link>
                                <PrimaryButton type="submit" class="mt-3">Save</PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Course Material -->
        <div class="pt-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="font-semibold text-xl mb-2 mt-3">Material</h1>
                    <form name="uploadForm" @submit.prevent="uploadMaterial">

                        <!-- Material is accessed via external Link -->
                        <input type="checkbox" id="external" v-model="uploadForm.external"
                            @click="uploadForm.path = null"
                            class="ml-1 rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                        <label for="external" class="mx-3">external link</label>

                        <!-- Material Source -->
                        <TextInput v-if="uploadForm.external" id="name" type="text" v-model="uploadForm.name"
                            placeholder="displayed name" autocomplete="off" size="40"></TextInput>
                        <TextInput v-if="uploadForm.external" id="extUrl" type="text" v-model="uploadForm.path"
                            placeholder="external url" autocomplete="off" size="80"></TextInput>
                        <span v-else>
                            <input id="uploadFile" class="hidden" ref="uploadFileInput" type="file"
                                @input="uploadForm.path = $event.target.files[0]">
                            <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="uploadFileInput.click()">
                                Select File
                            </SecondaryButton>
                            <span v-if="uploadForm.path">{{ uploadForm.path.name }}</span>
                            <progress v-if="uploadForm.progress" :value="uploadForm.progress.percentage" max="100">
                                {{ uploadForm.progress.percentage }}%
                            </progress>
                        </span>
                        <InputError :message="uploadForm.errors.path" class="mt-2" />

                        <!-- Notes (Description of the Material) -->
                        <textarea id="notes" v-model="uploadForm.notes" cols="30" rows="10"
                            placeholder="Material description"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                        <InputError :message="uploadForm.errors.notes" class="mt-2" />

                        <!-- Form Submission -->
                        <PrimaryButton type="submit" class="mt-3">Upload</PrimaryButton>
                    </form>
                </div>
            </div>
        </div>

        <!-- Course Material Listing -->
        <div v-for="mat in course.material" class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Link or File Name -->
                    <h1 class="mt-3 text-xl">{{ mat.name }}</h1>
                    <p v-if="mat.notes">{{ mat.notes }}</p>

                    <!-- Open or Download Button -->
                    <a v-if="mat.external" :href="mat.path" target="_blank">
                        <SecondaryButton class="mt-3">Open</SecondaryButton>
                    </a>
                    <a v-else :href="route('courses.downloadMaterial', mat.id)" download>
                        <SecondaryButton class="mt-3">Download</SecondaryButton>
                    </a>

                    <!-- Delete Button -->
                    <DangerButton class="mt-2" @click="deleteMaterial(mat)">Delete</DangerButton>
                </div>
            </div>
        </div>

        <!-- Lesson Listing -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Add Lesson Button -->
                <Link :href="route('lessons.create', { course_id: props.course.id })">
                <PrimaryButton>Add Lesson</PrimaryButton>
                </Link>

                <!-- Lesson Listing -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="font-semibold text-xl mb-2 mt-3">Lessons</h1>
                    <!-- Lesson Table -->
                    <table v-if="course.lessons.length">
                        <thead>
                            <tr>
                                <th class="pr-5">Start</th>
                                <th class="pr-5">Finish</th>
                                <th class="pr-5">Title</th>
                                <th class="pr-5">Teachers</th>
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lesson in course.lessons">
                                <td class="pr-5">{{ lesson.start }}</td>
                                <td class="pr-5">{{ lesson.finish }}</td>
                                <td class="pr-5">{{ lesson.title }}</td>
                                <td class="pr-5">
                                    <p v-for="teacher in lesson.teachers">{{ teacher.first_name }} {{ teacher.last_name
                                        }}
                                    </p>
                                </td>
                                <td>
                                    <!-- Edit Button -->
                                    <Link :href="route('lessons.edit', lesson.id)">
                                    <SecondaryButton>Edit</SecondaryButton>
                                    </Link>
                                </td>
                                <td>
                                    <!-- Delete Button -->
                                    <DangerButton @click="destroyLesson(lesson.id)">Delete</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Alternate Text, when no Lessons are defined -->
                    <p v-else>No Lessons available</p>
                </div>
            </div>
        </div>

        <!-- Participants Listing -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="font-semibold text-xl mb-2 mt-3">Participants</h1>

                    <!-- Add Participant Selection -->
                    <!-- Team Select Box -->
                    <select id="TeamSelect" v-model="newParticipantTeam"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <option value="" disabled>--- Select a Group ---</option>
                        <option v-for="(team, index) in teams" :value="index">{{ team.name }}</option>
                    </select>

                    <!-- Participant Select Box -->
                    <select id="ParticipantSelect" v-model="newParticipant" :disabled="newParticipantTeam === ''"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <option value="" disabled>--- Select a Participant ---</option>
                        <option v-if="newParticipantTeam !== ''" v-for="user in teams[newParticipantTeam].users"
                            :value="user">
                            {{ user.first_name }} {{ user.last_name }}</option>
                    </select>

                    <!-- Add Participant Button -->
                    <PrimaryButton @click="addUser(newParticipant)">Add Participant</PrimaryButton>

                    <table v-if="course.participants.length">
                        <thead>
                            <tr>
                                <th class="pr-5">First Name</th>
                                <th class="pr-5">Last Name</th>
                                <th class="pr-5">Paid</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="participant in props.course.participants" :key="participant.id">
                                <td class="pr-5">{{ participant.first_name }}</td>
                                <td class="pr-5">{{ participant.last_name }}</td>
                                <td class="pr-5">
                                    <input :id="'paid_' + participant.id" type="checkbox"
                                        v-model="participant.pivot.paid" true-value="1" false-value="0"
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
    </AppLayout>
</template>
