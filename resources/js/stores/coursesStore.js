import { defineStore } from "pinia";
import { computed, ref } from "vue";

export const useCoursesStore = defineStore('courses', () => {
    const courses = ref({})
    const teams = ref({})

    async function pull() {
        await axios.get('/courses')
            .then(function (response) {
                courses.value = response.data.courses;
                teams.value = response.data.teams;
            });
    }

    const course = computed(() => {
        return (courseId) => courses.value.find((course) => course.id === courseId)
    })

    const team = computed(() => {
        return (teamId) => teams.value.find((team) => team.id === teamId)
    })

    function setTeams(courseId, teams) {
        console.info(courses.value)
    }

    return {
        courses,
        teams,
        pull,
        course,
        team,
        setTeams,
    }
},
    {
        persist: true
    })
