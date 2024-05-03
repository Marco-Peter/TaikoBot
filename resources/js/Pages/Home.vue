<script setup>
import axios from 'axios';
import { ref } from 'vue';
import TitleBar from '@/Components/TitleBar.vue';

const response = ref();

const getValue = async () => {
    try {
        response.value = await axios.get("/api/test-me");
    } catch (error) {
        console.log(error);
    }
};
</script>

<template>
    <TitleBar>Home</TitleBar>
    <div>
        <p>Welcome home!</p>
        <button @click.prevent="getValue">Trigger Endpoint</button>
        <p v-if="response">{{ response.data }}</p>
    </div>
</template>

<style>
</style>
