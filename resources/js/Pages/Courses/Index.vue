<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import TitleBar from '@/Components/TitleBar.vue';
import Modal from '@/Components/Modal.vue';
import { useCoursesStore } from '@/stores/coursesStore';

const modalText = ref('');
const modalShow = ref(false);
const coursesStore = useCoursesStore();


const closeModal = () => {
    modalShow.value = false;
}
/*onMounted(async () => {
    coursesStore.pull()
        .catch(function (error) {
            console.error(error);
            modalText.value = error.response;
            modalShow.value = true;
        });

})*/
</script>

<template>
    <TitleBar>Courses</TitleBar>

    <div class="table">
        <div class="table-row">
            <div class="table-cell">Name</div>
            <div class="table-cell">Participants</div>
        </div>
        <div v-for="course in coursesStore.courses" class="table-row">
            <div class="table-cell">{{ course.name }}</div>
            <div class="table-cell">{{ course.participants_count }}/{{ course.capacity }}</div>
            <div class="table-cell">
                <RouterLink :to="{ name: 'courses.edit', params: { id: course.id } }">Edit</RouterLink>
            </div>
        </div>
    </div>

    <Modal :show="modalShow" @close="closeModal">
        <template #default>
            <div v-html="modalText"></div>
        </template>
    </Modal>
</template>

<style></style>
