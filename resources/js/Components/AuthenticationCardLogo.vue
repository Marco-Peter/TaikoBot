<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
    <picture>
        <source srcset="/images/favicon_white.png" media="(prefers-color-scheme: dark)">
        <img src="/images/favicon_black.png" width="50">
    </picture>
    </Link>
</template>
