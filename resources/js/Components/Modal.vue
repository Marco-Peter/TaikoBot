<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    closeable: { type: Boolean, default: true },
});
const emit = defineEmits([ 'close' ]);

watch(() => props.show, () => {
    if (props.show) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = null;
    }
    });

const close = () => {
    if (props.closeable) {
        emit('close');
    }
}

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
}

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = null;
});
</script>

<template>
    <div v-show="show" class="modal" scroll-region>
        <div class="modal-content">
            <slot v-if="show" />
        </div>
    </div>
</template>

<style>
.modal {
    position: fixed;
    z-index: 1;
    padding-top: 100px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: black;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}
</style>
