import { defineStore } from "pinia";
import { computed, ref } from "vue";

export const useUserStore = defineStore('user', () => {
    const user = ref({})

    async function pull() {
        this.user = await axios.get('/user')
            .then(function (response) {
                return response.data;
            });
    }

    const can_editUsers = computed(() => user.value.role === "admin")
    const can_editLessons = computed(() => user.value.role === "admin" || user.value.role === "teacher")
    const can_editCourses = computed(() => user.value.role === "admin" || user.value.role === "teacher")

    return {
        user,
        pull,
        can_editUsers,
        can_editLessons,
        can_editCourses,
    }
},
    {
        persist: true
    })
