<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted, ref } from 'vue';

const clients = ref(null)
const url = '/oauth/clients'

const form = useForm({
    name: '',
    redirect: ''
});

async function getClients() {

    clients.value = await (await fetch(url)).json()
    console.log('Fetched clients')
}

const submitClient = () => {
    form.post(url);
}

function migrate() {
    if (confirm("Are you sure to run the migrations?")) {
        router.post(route('users.doMigrations'));
    }
}

onMounted(async () => {
    getClients()
})
</script>

<template>
    <AppLayout title="Admin">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Admin
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl leading-tight">Registered OAuth2 Clients</h2>
                <div v-for="client in clients" class="py-3">
                    <h3 class="text-lg">Name: {{ client.name }}</h3>
                    <p>ID: {{ client.id }}</p>
                    <p>Redirect-URL: {{ client.redirect }}</p>
                    <p>Secret: {{ client.secret }}</p>
                </div>
                <div class="py-3">
                    <h2 class="font-semibold text-xl leading-tight">Add Client</h2>
                    <form @submit.prevent="submitClient">
                        <InputLabel for="name" value="Name" class="mt-3" />
                        <TextInput id="name" v-model="form.name" type="text" placeholder="Client Name" class="mt-1 block w-full" />
                        <InputError :message="form.errors.name" class="mt-2" />

                        <InputLabel for="redirect" value="Redirect" class="mt-3" />
                        <TextInput id="redirect" v-model="form.redirect" type="text" placeholder="https://my-url.com/callback" class="mt-1 block w-full" />
                        <InputError :message="form.errors.redirect" class="mt-2" />

                        <PrimaryButton type="submit" class="mt-3">Submit</PrimaryButton>
                    </form>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl mb-2">Run Database Migrations</h2>
                <PrimaryButton class="mb-2" @click="migrate()">Run Migrations</PrimaryButton>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                </div>
            </div>
        </div>
    </AppLayout>
</template>
