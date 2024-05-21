import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import { registerSW } from 'virtual:pwa-register';
import router from './router'
import App from './App.vue'

const updateSW = registerSW({
    onOfflineReady() { },
})

createApp(App)
    .use(createPinia()
        .use(piniaPluginPersistedstate))
    .use(router)
    .mount('#app')
