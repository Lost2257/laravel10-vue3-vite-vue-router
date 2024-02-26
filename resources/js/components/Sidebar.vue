<template>
    <div class="d-flex justify-content-between sidebar bg-dark p-3">
    <div class="d-flex">
      <router-link to="/" class="text-white">Home</router-link>
      <p v-if="loggedIn" class="text-white ">Hello {{ name }}</p>
    </div>

    <div>
      <button v-if="loggedIn" @click="logout" class="btn btn-danger">Logout</button>
    </div>
    </div>

  </template>

  <script>
  import { mapState, mapMutations } from 'vuex';
  import axios from 'axios';

  export default {
    data() {
      return {
        isHovered: false,
      };
    },
    computed: {
      ...mapState('auth', ['loggedIn', 'name']),
    },
    methods: {
      ...mapMutations('auth', ['setLoggedIn']),

      handleMouseOver() {
        this.isHovered = true;
      },
      handleMouseOut() {
        this.isHovered = false;
      },

      logout() {
        console.log('Logging out...');
        this.setLoggedIn(false);

        axios.post('/update-session', { loggedIn: true, name: '' })
          .then(response => {
            console.log('Updated session data:', response.data);
          })
          .catch(error => {
            console.error('Error updating session data:', error);
          });
      },
    },
  };
  </script>
