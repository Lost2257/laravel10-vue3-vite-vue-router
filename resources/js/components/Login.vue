<!-- Login.vue -->
<template>
    <div class="container mt-4">
      <h1>Login</h1>
      <form @submit.prevent="login">
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input v-model="email" type="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input v-model="password" type="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
      </form>
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        email: '',
        password: '',
      };
    },
    methods: {
      async login() {
        try {
          // Send a login request to your API endpoint
          const response = await axios.post('/api/login', {
            email: this.email,
            password: this.password,
          });

          // Handle successful login
          console.log('Login successful:', response.data);

          // Perform actions like setting loggedIn, fetching data, etc.
          // Example: this.loggedIn = true;

        } catch (error) {
          console.error('Error during login:', error);
          // Handle login error
        }
      },
    },
  };
  </script>
