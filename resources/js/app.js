import './bootstrap';
import { createApp } from 'vue';
import { registerSW } from 'virtual:pwa-register';
import router from './router'
import App from './App.vue'

const updateSW = registerSW({
    onOfflineReady() {},
})

createApp(App).use(router).mount('#app')
