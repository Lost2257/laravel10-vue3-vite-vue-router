<template>
    <div class="map-container">
        <div class="row">
            <div class="col-md-6">

      <div ref="map" class="leaflet-map"></div>
      <form @submit.prevent="addNewPoint">
        <div class="mb-3">
          <label for="newPointTitle" class="form-label">Title:</label>
          <input type="text" id="newPointTitle" v-model="newPoint.title" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="newPointLat" class="form-label">Latitude:</label>
          <input type="text" id="newPointLat" v-model="newPoint.lat" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="newPointLng" class="form-label">Longitude:</label>
          <input type="text" id="newPointLng" v-model="newPoint.lng" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Add New Point</button>
      </form>
      </div>

      <div class="col-md-6 points-list-container">
      <div class="points-list mt-4">
        <h2>Points List</h2>
        <table class="table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Latitude</th>
              <th>Longitude</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(point, index) in points" :key="index">
              <td>{{ point.title }}</td>
              <td>{{ point.lat }}</td>
              <td>{{ point.lng }}</td>
              <td>
                  <button @click="deleteLocation(index)" class="btn btn-danger">Delete</button>
                </td>
            </tr>
          </tbody>
        </table>
      </div>
      </div>
      </div>
    </div>
  </template>

  <script>
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css'; // Import Leaflet CSS

  export default {
    name: 'MapComponent',
    data() {
      return {
        map: null,
        userMarker: null,
        newPoint: {
          title: '',
          lat: '',
          lng: '',
        },
        points: [],
        markers: [],
      };
    },
    methods: {
        addNewPoint() {
        const { title, lat, lng } = this.newPoint;
        if (title && lat && lng) {
            // Send a POST request to your API endpoint
            axios.post('/api/points', {
            title,
            lat,
            lng,
            })
            .then(response => {
            // Do something with the response if needed
            console.log(response.data);

            // Add the new point to the local points array
            this.points.push({
                id: response.data.id, // Make sure your API response includes the new point's ID
                title,
                lat,
                lng,
            });

            // Add a marker for the new point
            this.addMarker(response.data.id, lat, lng, title);
            })
            .catch(error => {
            console.error('Error adding new point:', error);
            });

            // Clear the form fields
            this.newPoint.title = '';
            this.newPoint.lat = '';
            this.newPoint.lng = '';
        }
    },
    handleMapClick(event) {
      const { lat, lng } = event.latlng;

      // Prompt the user for a title for the new point
      const title = prompt('Enter a title for the new point:');

      if (title) {
        // Send a POST request to your API endpoint
        axios.post('/api/points', {
          title,
          lat,
          lng,
        })
        .then(response => {
          // Do something with the response if needed
          console.log(response.data);

          // Add the new point to the local points array
          this.points.push({
            id: response.data.id,
            title,
            lat,
            lng,
          });

          // Add a marker for the new point
          this.addMarker(response.data.id, lat, lng, title);
        })
        .catch(error => {
          console.error('Error adding new point:', error);
        });
      }
    },
    addMarker(locationId, lat, lng, title) {
      // Create a marker and add it to the map
      const marker = L.marker([lat, lng]).addTo(this.map)
        .bindPopup(title)
        .openPopup();

      // Add the marker to the markers array
      this.markers.push({
        locationId,
        marker,
      });
    },

    deleteLocation(index) {
  const deletedLocation = this.points[index];
  axios.delete(`/api/points/${deletedLocation.id}`)
    .then(response => {
      console.log('Location deleted:', response.data);

      this.points.splice(index, 1);
      this.removeMarker(deletedLocation.id);
    })
    .catch(error => {
      console.error('Error deleting location:', error);
    });
},
removeMarker(locationId) {
  // Find the marker with the given locationId and remove it from the map
  const markerIndex = this.markers.findIndex(marker => marker.locationId === locationId);
  if (markerIndex !== -1) {
    this.map.removeLayer(this.markers[markerIndex].marker);
    this.markers.splice(markerIndex, 1);
  }
},

    fetchPoints() {
  return axios.get('/api/points')
    .then(response => {
      this.points = response.data;  // Update the points data property
    })
    .catch(error => {
      console.error('Error fetching points:', error);
    });
},
    },
    mounted() {
  // Initialize the map
  this.map = L.map(this.$refs.map).setView([54.8985, 23.9036], 12);
  this.map.on('click', this.handleMapClick);

  // Use OpenStreetMap tile layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
  }).addTo(this.map);

  // Get user's geolocation
  if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(position => {
      const userLat = position.coords.latitude;
      const userLng = position.coords.longitude;

      // Add a marker for the user's location
      this.userMarker = L.marker([userLat, userLng]).addTo(this.map)
        .bindPopup('Your Location')
        .openPopup();

      // Set the map view to the user's location
      this.map.setView([userLat, userLng], 12);

      // Fetch points from the API
      this.fetchPoints().then(() => {
  // Add markers for each point
  this.points.forEach(point => {
    L.marker([point.lat, point.lng]).addTo(this.map)
      .bindPopup(point.title);
  });
});
    }, error => {
      console.error('Error getting geolocation:', error);

      // Fetch points from the API
      this.fetchPoints().then(() => {
  // Add markers for each point
  this.points.forEach(point => {
    L.marker([point.lat, point.lng]).addTo(this.map)
      .bindPopup(point.title);
  });
});
    });
  }
},

    beforeDestroy() {
      // Ensure to remove the map and user marker when the component is destroyed
      if (this.map) {
        this.map.remove();
        this.map.off('click', this.handleMapClick);
      }
    },
  };
  </script>


<style scoped>
.map-container {
  height: 600px;
  width: 100%;
}

.leaflet-map {
  height: 100%;
}

.row {
  display: flex;
}

.col-md-6 {
  flex: 0 0 50%;
  max-width: 50%;
}

.points-list-container {
  padding: 20px;
}

.points-list {
  margin-top: 20px;
}

/* Adjust styling for Bootstrap classes */
.form-control {
  width: 100%;
}

.delete-button-container {
  text-align: center;
  margin-top: 20px;
}
</style>
