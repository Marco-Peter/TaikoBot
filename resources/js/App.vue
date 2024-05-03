<script setup>
import { RouterLink } from 'vue-router';

</script>

<template>
    <nav>
        <RouterLink :to="{ name: 'home' }">Home</RouterLink>
        <RouterLink :to="{ name: 'courses.index' }">Courses</RouterLink>
        <RouterLink :to="{ name: 'users.index' }">Users</RouterLink>
        <RouterLink :to="{ name: 'profile' }">Profile</RouterLink>
    </nav>

    <main>
        <RouterView />
    </main>
</template>

<style>
:root {
    font-family: sans-serif;
    color-scheme: light dark;
}

a {
    margin-right: 2em;
    color: inherit;
    text-decoration: none;
    font-weight: bold;
    border: black;
    border-bottom: 1em;
}



nav {
    padding-top: 1em;
    padding-bottom: 1em;
    padding-left: 2em;
    background-color: #94ab67;
    color: white;
}
</style>
