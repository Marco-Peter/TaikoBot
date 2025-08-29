<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import Box from '@/Components/Box.vue';
import PageContent from '@/Components/PageContent.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ course_id: String });

const form = useForm({
    title: '',
    start: null,
    finish: null,
    notes: '',
    id: props.course_id,
});

const dateVal = defineModel('dateVal');
const startVal = defineModel('startVal');
const endVal = defineModel('endVal');

const submit = () => {
    form.start = dateVal.value + "T" + startVal.value;
    form.finish = dateVal.value + "T" + endVal.value;
    form.post(route("lessons.store"));
}
</script>

<template>
    <AppLayout title="Create Lesosn">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create Lesson
            </h2>
        </template>

        <PageContent>

        <Box>
            <form @submit.prevent="submit">
                <InputLabel for="title" value="Title" />
                <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" />
                <InputError :message="form.errors.title" class="mt-2" />

                <InputLabel for="date" value="Date" class="mt-2" />
                <TextInput id="date" v-model="dateVal" type="date" class="mt-1 block w-full" />

                <div class="flex flex-row gap-2 mt-2">
                    <div>
                        <InputLabel for="start" value="Start" />
                        <TextInput id="start" v-model="startVal" type="time" class="block w-full p-1 text-sm" />
                    </div>

                    <div>
                        <InputLabel for="finish" value="Finish" />
                        <TextInput id="finish" v-model="endVal" type="time" class="block w-full p-1 text-sm" />
                    </div>
                </div>
                <InputError :message="form.errors.start" class="" />
                <InputError :message="form.errors.finish" class="" />

                <div class="col-span-6 sm:col-span-4 mt-2">
                    <InputLabel for="notes" value="Notes" />
                    <textarea id="notes" v-model="form.notes" cols="30" rows="10"
                        placeholder="Private lesson notes, only visible for teachers"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                    <InputError :message="form.errors.notes" class="mt-2" />
                </div>

                <div class="flex flex-row gap-2 mt-4">
                    <PrimaryButton type="submit">Create</PrimaryButton>
                    <Link :href="route('courses.edit', course_id)">
                        <SecondaryButton>Cancel</SecondaryButton>
                    </Link>
                </div>
            </form>
        </Box>

        </PageContent>
    </AppLayout>
</template>
