<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const name = usePage().props.name;
</script>

<template>
    <img src="/images/logo_TZHFR_white_small.png" alt="Taiko Zürich">
</template>
