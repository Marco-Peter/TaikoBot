import { createRouter, createWebHistory } from 'vue-router'

import HomeRoute from './Pages/HomeRoute.vue'
import TestRoute from './Pages/TestRoute.vue'

const routes = [
    { path: '/', component: HomeRoute },
    { path: '/test', component: TestRoute },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
