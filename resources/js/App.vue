<script setup>
</script>

<template>
    <h1>TaikoBot</h1>
    <p><strong>Current route path:</strong> {{ $route.fullPath }}</p>
    <nav>
        <p><RouterLink to="/">HOME</RouterLink></p>
        <p><RouterLink to="/test">TEST</RouterLink></p>
    </nav>

    <main>
        <RouterView />
    </main>
</template>

<style>
</style>
