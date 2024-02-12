import { createRouter, createWebHistory } from "vue-router";

import home from '../components/home.vue'
import testing from '../components/Testing.vue'
import IpList from '../components/IpList.vue';

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
    },
    {
        path: '/ip-list',
        name: 'IpList',
        component: IpList,
      },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
