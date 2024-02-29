<template>
    <div class="map-container">
        <div class="row">
            <div class="col-md-6">

      <div ref="map" class="leaflet-map"></div>
      <form @submit.prevent="addNewPoint" enctype="multipart/form-data">
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

        <div class="mb-3">
  <label for="newPointImage" class="form-label">Image:</label>
  <input type="file" id="newPointImage" @change="handleImageChange" class="form-control" accept="image/*">
  <!-- Add image preview container -->
  <div v-if="imagePreview" class="mt-2">
    <img :src="imagePreview" alt="Image Preview" style="max-width: 100%;">
  </div>
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
              <th>Foto</th>
              <th>Title</th>
              <th>Latitude</th>
              <th>Longitude</th>
              <th>Atstumas</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(point, index) in points" :key="index">
              <td>
                <img :src="point.image" alt="Location Image" class="location-image">
              </td>
              <td>{{ point.title }}</td>
              <td>{{ point.lat }}</td>
              <td>{{ point.lng }}</td>
              <td>
                <span class="distance">{{ calculateDistance(point.lat, point.lng) }} meters</span>
            </td>

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

  L.Popup.prototype._animateZoom = function (e) {
  if (!this._map) {
    return;
  }
  var pos = this._map._latLngToNewLayerPoint(this._latlng, e.zoom, e.center),
    anchor = this._getAnchor();
  L.DomUtil.setPosition(this._container, pos.add(anchor));
};

var LeafIcon = L.Icon.extend({
  options: {
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40],
  },

});

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
          image: null,
          imagePreview: null,
        },
        points: [],
        markers: [],
      };
    },
    watch: {
  points: {
    handler(newPoints) {
      if (this.map) {
        this.markers.forEach(marker => this.map.removeLayer(marker.marker));
        this.markers = [];
        newPoints.forEach(point => {
          this.addMarker(point.id, point.lat, point.lng, point.title);
        });
      }
    },
    deep: true,
  },
},
    methods: {
        calculateDistance(lat, lng) {
    if (this.userMarker) {
      const userLatLng = this.userMarker.getLatLng();
      const pointLatLng = L.latLng(lat, lng);
      const distance = userLatLng.distanceTo(pointLatLng);
      return distance.toFixed(2); // Return distance rounded to two decimal places
    }
    return '';
  },
  addNewPoint() {
  const { title, lat, lng, image } = this.newPoint;

  if (title && lat && lng && image) {
    const formData = new FormData();
    formData.append('title', title);
    formData.append('lat', lat);
    formData.append('lng', lng);
    formData.append('image', image);

    // Send a POST request to your API endpoint
    axios.post('/api/points', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
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
          image: response.data.image,
        });

        // Add a marker for the new point
        this.addMarker(response.data.id, lat, lng, title, response.data.image);
      })
      .catch(error => {
        console.error('Error adding new point:', error);
        // Handle error or show a user-friendly message
      });

    // Clear the form fields
    this.newPoint.title = '';
    this.newPoint.lat = '';
    this.newPoint.lng = '';
    this.newPoint.image = null;
  } else {
    // Handle case where not all required fields are filled
    console.error('Please fill in all required fields.');
  }
},
handleMapClick(event) {
  const { lat, lng } = event.latlng;

  // Prompt the user for a title for the new point
  const title = prompt('Enter a title for the new point:');
  if (!title) return;

  // Prompt the user to select an image for the new point
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/*';
  input.click();

  input.addEventListener('change', () => {
    const image = input.files[0];

    if (!image) {
      alert('Please select an image.');
      return;
    }

    // Upload the image and create the point
    const formData = new FormData();
    formData.append('title', title);
    formData.append('lat', lat);
    formData.append('lng', lng);
    formData.append('image', image);

    // Send a POST request to your API endpoint
    axios.post('/api/points', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
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
        image: response.data.image,
      });

      // Add a marker for the new point
      this.addMarker(response.data.id, lat, lng, title, response.data.image);
    })
    .catch(error => {
      console.error('Error adding new point:', error);
    });
  });
},
    addMarker(locationId, lat, lng, title, image) {
  if (this.map && image) {
    const customMarkerIcon = new LeafIcon({ iconUrl: image });

    const marker = L.marker([lat, lng], { icon: customMarkerIcon }).addTo(this.map);

    this.markers.push({
      locationId,
      marker,
      icon: customMarkerIcon,
    });

    marker.bindPopup(`<div><img src="${image}" alt="${title}" class="popup-image" /><br>${title}</div>`);

    marker.on('click', () => marker.openPopup());
  }
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
  // Check if the map and markers array exist before proceeding
  if (this.map && this.markers) {
    // Iterate over the markers array to find and remove the marker
    this.markers.forEach((markerInfo, index) => {
      if (markerInfo.locationId === locationId) {
        // Remove the marker from the map
        this.map.removeLayer(markerInfo.marker);
        // Remove the marker from the markers array
        this.markers.splice(index, 1);
      }
    });
  }
},


  fetchPoints() {
      return axios.get('/api/points')
        .then(response => {
          console.log('API response:', response.data);  // Log the API response
          this.points = response.data;

          // Add markers for each point
          this.points.forEach(point => {
            this.addMarker(point.id, point.lat, point.lng, point.title, point.image);
          });
        })
        .catch(error => {
          console.error('Error fetching points:', error);
        });
    },
watchUserLocation() {
    this.watchId = navigator.geolocation.watchPosition(position => {
      this.updateUserLocation(position);
    }, error => {
      console.error('Error watching geolocation:', error);
    });
  },
fetchUserLocation() {
    if ('geolocation' in navigator) {
      navigator.geolocation.getCurrentPosition(position => {
        this.updateUserLocation(position);
      }, error => {
        console.error('Error getting geolocation:', error);
      });
    }
    },
    updateUserLocation(position) {
    const userLat = position.coords.latitude;
    const userLng = position.coords.longitude;

    // Update the user's marker location
    if (this.userMarker) {
      this.userMarker.setLatLng([userLat, userLng]);
    } else {
      // Add a marker for the user's location
      this.userMarker = L.marker([userLat, userLng]).addTo(this.map)
        .bindPopup('Your Location')
        .openPopup();
    }

    // Set the map view to the user's location
    this.map.setView([userLat, userLng], 12);
  },
  handleImageChange(event) {
  const file = event.target.files[0];

  // Update newPoint.image
  this.newPoint.image = file;

  // Optionally, you can also display a preview of the selected image
  const reader = new FileReader();
  reader.onload = () => {
    this.imagePreview = reader.result;
  };
  reader.readAsDataURL(file);
}

  // ... rest of your component code
},
    mounted() {
  // Initialize the map
  this.map = L.map(this.$refs.map).setView([54.8985, 23.9036], 12);
  this.map.on('click', this.handleMapClick);

  // Use OpenStreetMap tile layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
  }).addTo(this.map);

  // Fetch initial user location
  this.fetchUserLocation();

  // Continuously track user location
  if ('geolocation' in navigator) {
    this.watchUserLocation();
  }

  // Fetch points from the API
  this.fetchPoints().then(() => {
    // Add markers for each point
    this.points.forEach(point => {
        this.addMarker(point.id, point.lat, point.lng, point.title, point.image);
    });
  });
},

beforeDestroy() {
  if (this.watchId) {
    navigator.geolocation.clearWatch(this.watchId);
  }
  // Ensure to remove the map and user marker when the component is destroyed
  if (this.map) {
    this.map.off('click', this.handleMapClick);
    this.map.remove();
    this.map = null;  // Set the map instance to null when removing it
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

.location-image{
    width: 40px;
}
.custom-marker {
  display: inline-block;
  text-align: center;
  line-height: 0;
}
</style>
