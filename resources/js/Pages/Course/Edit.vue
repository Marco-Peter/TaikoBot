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
import DialogModal from '@/Components/DialogModal.vue';
import { Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    course: Object, teams: Object, users: Array, compCourses: Array,
    compCoursesSelected: Array, teachers: Array, lessonTeachers: Array,
    lessonAssistants: Array
});
const page = usePage();
router.reload();

const form = useForm({
    name: props.course.name,
    description: props.course.description,
    capacity: String(props.course.capacity),
    signout_limit: String(props.course.signout_limit),
    teacher_payment: String(props.course.teacher_payment),
    assist_payment: String(props.course.assist_payment),
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
const newCompensation = ref("");

const showTeacherModal = ref(false);
const selectedLesson = ref(null);

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

function addCompCourse(course) {
    router.post(route('courses.addCompCourse', props.course.id),
        { 'compCourse': course.id }, { preserveScroll: true });
}

function removeCompCourse(course) {
    if (confirm(`Are you sure you want to remove ${course.name} as a compensation course?`)) {
        router.post(route('courses.removeCompCourse', props.course.id),
            { 'compCourse': course.id }, { preserveScroll: true });
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

function chooseTeacher(lesson) {
    if (!page.props.auth.canEditCourses) {
        return;
    }

    selectedLesson.value = lesson;
    showTeacherModal.value = true;
}

function setTeacher(teacherId) {
    router.post(route('lessons.setTeacher', selectedLesson.value.id), {
        teacher: teacherId
    }, {
        onSuccess: () => {
            showTeacherModal.value = false;
        }
    });
}

function addTeacher(teacherId) {
    router.post(route('lessons.addTeacher', selectedLesson.value.id), {
        teacher: teacherId
    }, {
        onSuccess: () => {
            showTeacherModal.value = false;
        }
    });
}

function closeModal() {
    showTeacherModal.value = false;
    selectedLesson.value = null;
}

function goBack() {
    window.history.back();
}
</script>

<template>
    <AppLayout title="Edit Course">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex flex-row items-center gap-1">
                Edit Course

                <div class="flex-1" />

                <Link :href="route('courses.show', [course.id])">
                <SecondaryButton small>Open</SecondaryButton>
                </Link>
                <Link @click="goBack">
                <SecondaryButton small>Back</SecondaryButton>
                </Link>
            </h2>
        </template>

        <PageContent>

            <!-- Basic Properties -->
            <Box>
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

                        <!-- Sign Out time limit to gain TaikoKarma -->
                        <div>
                            <InputLabel for="signoutLimit" value="Sign Out time limit (hours)" />
                            <TextInput id="signoutLimit" v-model="form.signout_limit" type="number" min="0"
                                title="Minimum hours to sign out before lessons to gain TaikoKarma"
                                class="mt-1 block w-full" />
                            <InputError :message="form.errors.signout_limit" class="mt-2" />
                        </div>

                        <!-- Teacher payment -->
                        <div>
                            <InputLabel for="teacherPayment" value="Teacher Payment" />
                            <TextInput id="teacherPayment" v-model="form.teacher_payment" type="number" min="0"
                                title="Financial compensation for teachers per lesson" class="mt-1 block w-full" />
                            <InputError :message="form.errors.teacher_payment" class="mt-2" />
                        </div>

                        <!-- Assistants payment -->
                        <div>
                            <InputLabel for="assistantPayment" value="Assistant Payment" />
                            <TextInput id="assistantPayment" v-model="form.assist_payment" type="number" min="0"
                                title="Financial compensation for assistants per lesson" class="mt-1 block w-full" />
                            <InputError :message="form.errors.assist_payment" class="mt-2" />
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
                        <div class="flex flex-row items-center gap-2 mt-2">
                            <Link :href="route('courses.index')">
                            <!-- Return without Changing Data-->
                            <SecondaryButton>Cancel</SecondaryButton>
                            </Link>
                            <PrimaryButton type="submit">Save</PrimaryButton>
                        </div>
                    </div>
                </form>
            </Box>

            <!-- Course Material -->
            <Box>
                <h1 class="font-semibold text-xl mb-2 mt-3">Material</h1>
                <form name="uploadForm" @submit.prevent="uploadMaterial">

                    <!-- Material is accessed via external Link -->
                    <input type="checkbox" id="external" v-model="uploadForm.external" @click="uploadForm.path = null"
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

                <div v-for="mat in course.material">
                    <div class="max-w-7xl">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden">
                            <h1 class="mt-3 text-xl">{{ mat.name }}</h1>
                            <p v-if="mat.notes">{{ mat.notes }}</p>

                            <div class="flex flex-row gap-1 items-center mt-1">
                                <a v-if="mat.external" :href="mat.path" target="_blank" class="leading-none">
                                    <SecondaryButton small class="">Open</SecondaryButton>
                                </a>

                                <a v-else :href="route('courses.downloadMaterial', mat.id)" download
                                    class="leading-none">
                                    <SecondaryButton small class="">Download</SecondaryButton>
                                </a>

                                <DangerButton small class="" @click="deleteMaterial(mat)">Delete</DangerButton>
                            </div>
                        </div>
                    </div>
                </div>
            </Box>

            <!-- Compensations List -->
            <Box>
                <h1 class="font-semibold text-xl mb-2 mt-3">Compensation Possibilities</h1>
                <select v-model="newCompensation"
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                    <option value="" disabled>--- Select a Compensation Class ---</option>
                    <option v-for="compCourse in compCourses" :key="compCourse.id" :value="compCourse">{{
                        compCourse.name }}
                    </option>
                </select>
                <PrimaryButton :disabled="newCompensation === ''" @click="addCompCourse(newCompensation)">Add
                    Compensation
                </PrimaryButton>
                <table v-if="compCoursesSelected.length" class="mt-3">
                    <thead>
                        <tr>
                            <th class="pr-5">Name</th>
                            <th class="pr-5"></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="compCourseSelected in compCoursesSelected" :key="compCourseSelected.id">
                            <td class="pr-5">{{ compCourseSelected.name }}</td>
                            <td class="pr-5">
                                <DangerButton @click="removeCompCourse(compCourseSelected)">Remove</DangerButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-else>No Compensations added</p>
            </Box>

            <!-- Lesson Listing -->
            <Box>
                <!-- Lesson Listing -->
                <h1 class="font-semibold text-xl mb-2 mt-3">Lessons</h1>
                <!-- Lesson Table -->
                <table v-if="course.lessons.length">
                    <thead>
                        <tr>
                            <th class="px-2 text-left">Date</th>
                            <th class="px-2 text-left">Teacher</th>
                            <th class="px-2 text-left w-full">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lesson in course.lessons">
                            <td class="px-2 text-sm whitespace-nowrap"
                                :class="new Date(lesson.finish) < Date.now() ? 'line-through pr-5' : 'pr-5'">{{
                                    lesson.start.slice(0, 10) }}</td>
                            <td class="px-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap"
                                @click="page.props.auth.canEditCourses ? chooseTeacher(lesson) : null">
                                <span v-for="(teacher, index) in lesson.teachers" :key="teacher.id">
                                    {{ index === 0 ? '' : ', ' }}
                                    {{ teacher.first_name }}
                                    {{ teacher.last_name[0] }}
                                </span>
                                <span v-if="lesson.teachers.length === 0">
                                    &ndash;
                                </span>
                            </td>
                            <td class="px-2">
                                <div class="flex flex-row items-center gap-2">
                                    <!-- Edit Button -->
                                    <Link :href="route('lessons.edit', lesson.id)" class="leading-none">
                                    <SecondaryButton small>Edit</SecondaryButton>
                                    </Link>

                                    <!-- Delete Button -->
                                    <DangerButton small @click="destroyLesson(lesson.id)">Delete</DangerButton>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Alternate Text, when no Lessons are defined -->
                <p v-else>No Lessons available</p>

                <!-- Add Lesson Button -->
                <Link :href="route('lessons.create', { course_id: props.course.id })">
                <PrimaryButton>Add Lesson</PrimaryButton>
                </Link>
            </Box>

            <!-- Participants Listing -->
            <Box>
                <h1 class="font-semibold text-xl mb-2 mt-3">Participants</h1>

                <div class="flex flex-col md:flex-row gap-2">

                    <!-- Add Participant Selection -->
                    <!-- Participant Select Box -->
                    <select id="ParticipantSelect" v-model="newParticipant"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <option value="" disabled>--- Select a Participant ---</option>
                        <option v-for="user in users" :value="user">
                            {{ user.first_name }} {{ user.last_name }}</option>
                    </select>

                    <!-- Add Participant Button -->
                    <PrimaryButton @click="addUser(newParticipant)">Add Participant</PrimaryButton>
                </div>

                <table v-if="course.participants.length">
                    <thead>
                        <tr>
                            <th class="px-2 text-left">First Name</th>
                            <th class="px-2 text-left">Last Name</th>
                            <th class="px-2 text-left">Paid</th>
                            <th class="px-2 text-left w-full">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="participant in props.course.participants" :key="participant.id">
                            <td class="px-2 whitespace-nowrap">{{ participant.first_name }}</td>
                            <td class="px-2 whitespace-nowrap">{{ participant.last_name }}</td>
                            <td class="px-2">
                                <input :id="'paid_' + participant.id" type="checkbox" v-model="participant.pivot.paid"
                                    true-value="1" false-value="0"
                                    @change="updatePaid(participant.id, participant.pivot.paid)"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                            </td>
                            <td class="px-2">
                                <div class="flex flex-row items-center gap-2">
                                    <DangerButton small @click="removeUser(participant)">Remove</DangerButton>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-else>No Participants signed up</p>
            </Box>

            <!-- Teacher Listing -->
            <Box>
                <h1 class="font-semibold text-xl mb-2 mt-3">Teachers</h1>

                <table v-if="props.lessonTeachers.length">
                    <thead>
                        <tr>
                            <th class="px-2 text-left">First Name</th>
                            <th class="px-2 text-left">Last Name</th>
                            <th class="px-2 text-left w-full">Lessons</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lessonTeacher in props.lessonTeachers">
                            <td class="px-2 whitespace-nowrap">{{ lessonTeacher.first_name }}</td>
                            <td class="px-2 whitespace-nowrap">{{ lessonTeacher.last_name }}</td>
                            <td class="px-2 whitespace-nowrap">{{ lessonTeacher.lesson_count }}</td>
                        </tr>
                    </tbody>
                </table>
                <p v-else>No Teachers registered</p>
            </Box>

            <!-- Assistants Listing -->
            <Box>
                <h1 class="font-semibold text-xl mb-2 mt-3">Assistants</h1>

                <table v-if="props.lessonAssistants.length">
                    <thead>
                        <tr>
                            <th class="px-2 text-left">First Name</th>
                            <th class="px-2 text-left">Last Name</th>
                            <th class="px-2 text-left w-full">Lessons</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lessonAssistant in props.lessonAssistants">
                            <td class="px-2 whitespace-nowrap">{{ lessonAssistant.first_name }}</td>
                            <td class="px-2 whitespace-nowrap">{{ lessonAssistant.last_name }}</td>
                            <td class="px-2 whitespace-nowrap">{{ lessonAssistant.lesson_count }}</td>
                        </tr>
                    </tbody>
                </table>
                <p v-else>No Assistants registered</p>
            </Box>
        </PageContent>

        <!-- Teacher Selection Modal -->
        <DialogModal :show="showTeacherModal" @close="closeModal">
            <template #title>
                Select Teacher for {{ selectedLesson?.title }}
            </template>

            <template #content>
                <div class="space-y-3">
                    <div v-for="teacher in teachers" :key="teacher.id"
                        class="flex flex-row items-center justify-between px-2 py-1 rounded hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div>
                            <span class="font-medium">{{ teacher.first_name }} {{ teacher.last_name[0] }}</span>
                        </div>
                        <div class="flex gap-2">
                            <PrimaryButton small @click="setTeacher(teacher.id)">
                                Set
                            </PrimaryButton>
                            <SecondaryButton small @click="addTeacher(teacher.id)">
                                Add
                            </SecondaryButton>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
