<!-- app.vue -->
<template>
    <div>
      <Sidebar />
      <router-view />
    </div>
  </template>

  <script>
  import Sidebar from './Sidebar.vue';
  import { onMounted } from 'vue';
    import axios from 'axios';
    import { useStore } from 'vuex';

  export default {
    components: {
      Sidebar,
    },
    setup() {
    const store = useStore();

    onMounted(async () => {
      try {
        const loginResponse = await axios.post('/auto-login');

        console.log('Auto-login response:', loginResponse);
        console.log('Auto-login successful:', loginResponse.data);

        store.dispatch('auth/autoLogin', {
          loggedIn: true,
          name: loginResponse.data.name,
        });
      } catch (error) {
        console.error('Error during auto-login:', error);
      }
    });
  },
  };
  </script>

  <style>

  </style>
