import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    { path: '/', name: 'home', component: () => import('./Pages/Home.vue') },
    { path: '/courses', name: 'courses.index', component: () => import('./Pages/Courses/Index.vue') },
    { path: '/courses/:id', name: 'courses.show', component: () => import('./Pages/Courses/Show.vue') },
    { path: '/courses/:id/edit', name: 'courses.edit', component: () => import('./Pages/Courses/Edit.vue') },
    { path: '/courses/create', name: 'courses.create', component: () => import('./Pages/Courses/Create.vue') },

    { path: '/lessons', name: 'lessons.index', component: () => import('./Pages/Lessons/Index.vue') },
    { path: '/lessons/:id', name: 'lessons.show', component: () => import('./Pages/Lessons/Show.vue') },
    { path: '/lessons/:id/edit', name: 'lessons.edit', component: () => import('./Pages/Lessons/Edit.vue') },
    { path: '/lessons/create', name: 'lessons.create', component: () => import('./Pages/Lessons/Create.vue') },

    { path: '/users', name: 'users.index', component: () => import('./Pages/Users/Index.vue') },
    { path: '/users/:id', name: 'users.show', component: () => import('./Pages/Users/Show.vue') },
    { path: '/users/:id/edit', name: 'users.edit', component: () => import('./Pages/Users/Edit.vue') },
    { path: '/users/create', name: 'users.create', component: () => import('./Pages/Users/Create.vue') },

    { path: '/profile', name: 'profile', component: () => import('./Pages/Profile.vue') },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
