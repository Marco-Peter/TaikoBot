<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import Modal from './Components/Modal.vue';
import axios from 'axios';

const router = useRouter();
const modalText = ref('');
const modalShow = ref(false);

axios.defaults.withCredentials = true;
axios.defaults.baseURL = window.location + 'local-api';
axios.defaults.responseType = 'json';

const userdata = ref({});

function logout() {
    axios.post(window.location + 'logout').then(response => {
        router.go()
    });
}

const closeModal = () => {
    modalShow.value = false;
}

onMounted(async () => {
    userdata.value = await axios.get('/user')
        .then(function (response) {
            return response.data;
        })
        .catch(function (error) {
            modalText.value = error.response.data;
            modalShow.value = true;
        });

})
</script>

<template>
    <nav class="navBar">
        <img src="/images/taikobot-logo-white.png" alt="TaikoBot" height="60px" id="tb-logo">
        <RouterLink :to="{ name: 'home' }">Home</RouterLink>
        <RouterLink :to="{ name: 'courses.index' }">Courses</RouterLink>
        <RouterLink :to="{ name: 'users.index' }">Users</RouterLink>
        <RouterLink :to="{ name: 'profile' }">Profile</RouterLink>
        <button @click="logout">Logout</button>
    </nav>

    <main>
        <RouterView />
    </main>

    <Modal :show="modalShow" @close="closeModal">
        <template #default>
            <div v-html="modalText"></div>
        </template>
    </Modal>
</template>

<style>
</style>
