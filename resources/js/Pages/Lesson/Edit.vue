<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ lesson: Object });

const form = useForm({
    title: props.lesson.title,
    start: props.lesson.start,
    finish: props.lesson.finish,
    notes: props.lesson.notes,
});

const submit = () => {
    form.put(route("lessons.update", props.lesson.id));
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
    </AppLayout>
</template>
