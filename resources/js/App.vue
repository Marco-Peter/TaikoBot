<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import axios from 'axios';
import { useUserStore } from '@/stores/userStore';
import Modal from '@/Components/Modal.vue';

const router = useRouter();
const modalText = ref('');
const modalShow = ref(false);

axios.defaults.withCredentials = true;
axios.defaults.baseURL = window.location + 'local-api';
axios.defaults.responseType = 'json';

const userStore = useUserStore();

function logout() {
    axios.post(window.location + 'logout').then(response => {
        router.go()
    });
}

const closeModal = () => {
    modalShow.value = false;
}

onMounted(async () => {
    userStore.pull()
        .catch(function (error) {
            modalText.value = error.response.data.message;
            modalShow.value = true;
        });

})
</script>

<template>
    <nav class="navBar">
        <img src="/images/taikobot-logo-white.png" alt="TaikoBot" height="60px" id="tb-logo">
        <RouterLink :to="{ name: 'home' }">Home</RouterLink>
        <RouterLink v-if="userStore.can_editCourses" :to="{ name: 'courses.index' }">Courses</RouterLink>
        <RouterLink v-if="userStore.can_editUsers" :to="{ name: 'users.index' }">Users</RouterLink>
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

<style></style>
