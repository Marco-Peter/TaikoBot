<script setup>
import { ref } from 'vue';
import { useUserStore } from '@/stores/userStore';
import TitleBar from '@/Components/TitleBar.vue';

const userStore = useUserStore();
const response = ref();
</script>

<template>
    <TitleBar>Hello {{ userStore.user.first_name }}, let's play some Taiko!</TitleBar>
    <div>
        <h2>Your Signed Up Courses and Workshops</h2>
        {{ userStore.user }}
    </div>
</template>

<style>
</style>
