<script setup>
import TitleBar from '@/Components/TitleBar.vue';
import Modal from '@/Components/Modal.vue';
import { useCoursesStore } from '@/stores/coursesStore';
import { storeToRefs } from 'pinia';
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const coursesStore = useCoursesStore();
const course = ref(coursesStore.course(Number(route.params.id)));
const teams = ref(course.value.teams.map(({ id }) => id));
</script>

<template>
    <TitleBar>Edit Course - {{ course.name }}</TitleBar>
    <div class="fieldgroup">
        <label for="name">Name</label>
        <input type="text" required v-model="course.name">
    </div>

    <div class="fieldgroup">
        <label for="description">Description</label>
        <textarea v-model="course.description" id="description" cols="30" rows="10" />
    </div>

    <div class="fieldgroup">
        <label for="capacity">Capacity</label>
        <input type="number" v-model="course.capacity" id="capacity">
    </div>

    <h3>Publish to Groups</h3>
    <div v-for="team in coursesStore.teams" :key="team.id">
        <input type="checkbox" :id="team.name" v-model="teams" :value="String(team.id)" @click="coursesStore.setTeams(course.id, teams)">
        <label :for="team.name">{{ team.name }}</label>
    </div>
</template>

<style>
.fieldgroup {
    display: flex;
    flex-direction: column;
    margin: 1em;
}
</style>
