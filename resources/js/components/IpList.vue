<template>
    <div class="container mt-4">
      <h1 class="mb-4">IP List</h1>

      <!-- Create Form -->
      <form @submit.prevent="createIp" class="mb-4">
        <div class="row">
          <div class="col-md-4">
            <label for="name" class="form-label">Name:</label>
            <input v-model="newIp.name" type="text" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label for="ipAddress" class="form-label">IP Address:</label>
            <input v-model="newIp.ip_address" type="text" class="form-control" required>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Create</button>
          </div>
        </div>
      </form>

      <!-- IP List -->
      <ul class="list-group">
        <li v-for="ip in ipList" :key="ip.id" class="list-group-item">
          <strong>{{ ip.name }}</strong> - {{ ip.ip_address }}
          <button @click="editIp(ip)" class="btn btn-warning btn-sm ms-2">Edit</button>
          <button @click="deleteIp(ip.id)" class="btn btn-danger btn-sm ms-2">Delete</button>
        </li>
      </ul>

      <!-- Edit Form -->
      <form v-if="editingIp" @submit.prevent="updateIp" class="mt-4">
        <div class="row">
          <div class="col-md-4">
            <label for="name" class="form-label">Name:</label>
            <input v-model="editingIp.name" type="text" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label for="ipAddress" class="form-label">IP Address:</label>
            <input v-model="editingIp.ip_address" type="text" class="form-control" required>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-success">Update</button>
          </div>
        </div>
      </form>
      <router-link to="/">i pradzia</router-link>
    </div>
  </template>


  <script>
  import { ref } from 'vue';

  export default {
    data() {
      return {
        ipList: [],
        newIp: { name: '', ip_address: '' },
        editingIp: null,
      };
    },
    created() {
      this.fetchIpList();
    },
    methods: {
      async fetchIpList() {
        try {
          const response = await fetch('/api/ip-lists');
          const data = await response.json();
          this.ipList = data;
        } catch (error) {
          console.error('Error fetching IP list:', error);
        }
      },
      async createIp() {
        try {
          const response = await fetch('/api/ip-lists', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(this.newIp),
          });

          const data = await response.json();
          this.ipList.push(data);
          this.newIp = { name: '', ip_address: '' };
        } catch (error) {
          console.error('Error creating IP:', error);
        }
      },
      editIp(ip) {
        this.editingIp = { ...ip };
      },
      async updateIp() {
      try {
        const response = await fetch(`/api/ip-lists/${this.editingIp.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(this.editingIp),
        });

        const data = await response.json();

        // Instead of $set, you can use the spread operator or Vue's reactive function
        const index = this.ipList.findIndex((ip) => ip.id === this.editingIp.id);
        this.ipList[index] = { ...data };

        // Alternatively, you can use reactive to update the array
        // this.ipList = ref(this.ipList);
        // this.ipList[index] = { ...data };

        this.editingIp = null;
      } catch (error) {
        console.error('Error updating IP:', error);
      }
},
      async deleteIp(ipId) {
        try {
          await fetch(`/api/ip-lists/${ipId}`, {
            method: 'DELETE',
          });

          this.ipList = this.ipList.filter((ip) => ip.id !== ipId);
        } catch (error) {
          console.error('Error deleting IP:', error);
        }
      },
    },
  };
  </script>

  <style scoped>
  /* Component styles go here */
  </style>
