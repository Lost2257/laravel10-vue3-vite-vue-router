<template>
    <div class="unauthorized-container">
      <h1>Unauthorized Access</h1>
      <p>You do not have the required roles to access this page.</p>
      <router-link to="/">Go to Home</router-link>
    </div>
  </template>

  <style scoped>
  .unauthorized-container {
    text-align: center;
    margin-top: 50px;
  }

  h1 {
    color: red;
  }

  p {
    margin: 20px 0;
  }
  </style>
