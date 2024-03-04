// router/index.js
import { createRouter, createWebHistory } from "vue-router";
import home from '../components/Home.vue'
import testing from '../components/Testing.vue'
import IpList from '../components/IpList.vue';
import Login from '../components/Login.vue';
import notFound from '../components/NotFound.vue'
import Unauthorized from '../components/Unauthorized.vue'
import MapComponent from '../components/MapComponent.vue'
import store from '../store/auth.js';
import { useStore } from 'vuex';


const routes = [
  {
    path: '/',
    component: home,
    meta: { requiresAuth: false },
  },
  {
    path:'/testing',
    component: testing,
    meta: { requiresAuth: true },
  },
  {
    path: '/pathMatch(.*)*',
    component: notFound
  },
  {
    path: '/ip-list',
    name: 'IpList',
    component: IpList,
    meta: { requiresAuth: true, requiredRoles: ['admin'] },
  },
  {
    path: '/simple-login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/map',
    name: 'Map',
    component: MapComponent,


  },
  {
    path: '/unauthorized',
    name: 'Unauthorized',
    component: Unauthorized,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
    const store = useStore();
    const isAuthenticated = store.state.auth.loggedIn;

    if (to.meta.requiresAuth && !isAuthenticated) {
      next('/simple-login');
    } else {
      if (to.meta.requiresAuth && to.meta.requiredRoles) {
        await store.dispatch('auth/autoLogin');

        const requiredRoles = to.meta.requiredRoles;
        const userRoles = store.state.auth.roles || [];
        const hasRequiredRoles = requiredRoles.every(role => userRoles.includes(role));

        if (!hasRequiredRoles) {
          next('/unauthorized');
        } else {
          next();
        }
      } else {
        next();
      }
    }
  });

export default router;
