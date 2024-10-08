<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ user: Object, });
const page = usePage();
const form = useForm({
    lessonNotificationTime: props.user.settings ? props.user.settings.lessonNotificationTime : "0",
    waitlistCancelTime: props.user.settings ? props.user.settings.waitlistCancelTime :
        page.props.env.app.waitlistAutocancelTime,
});

const updateSettings = () => {
    form.put(route('user-settings.update'), {
        errorBag: 'user-settings',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="updateSettings">
        <template #title>
            User Settings
        </template>

        <template #description>
            Notification and general settings.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="lesson_notification"
                    value="How many hours before the lessons should you be reminded (0 to disable)?" />
                <TextInput id="lesson_notification" v-model="form.lessonNotificationTime" type="number" min="0"
                    class="mt-1 block w-full" />
                <InputError :message="form.errors.lessonNotificationTime" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="waitlistCancelTime"
                    value="How many hours before the lessons should we remove you from the waitlist (this will free your karma)?" />
                <TextInput id="waitlistCancelTime" v-model="form.waitlistCancelTime" type="number" min="0"
                    class="mt-1 block w-full" />
                <InputError :message="form.errors.waitlistCancelTime" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
