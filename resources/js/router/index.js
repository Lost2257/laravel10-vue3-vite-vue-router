import { createRouter, createWebHistory } from "vue-router";

import home from '../components/home.vue'
import testing from '../components/Testing.vue'

import notFound from '../components/NotFound.vue'

const routes = [
    {
        path:'/',
        component: home
    },
    {
        path:'/testing',
        component: testing
    },
    {
        path: '/pathMatch(.*)*',
        component: notFound
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
