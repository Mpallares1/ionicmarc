<template>
  <div class="container">
    <Navbar />
    <main class="main">
      <div class="action-bar">
        <div>
          <h2 class="section-title">Welcome, {{ userProfile.name }}</h2>
          <p class="description">Choose the content you want to upload</p>
        </div>
        <button @click="fetchMultimedia" class="refresh-button">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Refresh Content
        </button>
      </div>

      <!-- Upload Form -->
      <div class="upload-section">
        <h3>Upload Multimedia</h3>
        <form @submit.prevent="uploadMultimedia" class="upload-form">
          <input type="file" @change="handleFileChange" accept="image/*,video/*" class="file-input" />
          <button type="submit" :disabled="!selectedFile" class="upload-button">Upload</button>
        </form>
        <p v-if="uploadMessage" class="upload-message">{{ uploadMessage }}</p>
      </div>

      <!-- Multimedia Gallery -->
      <div class="gallery">
        <div v-if="loading" class="loading">Loading...</div>
        <div v-if="error" class="error">{{ error }}</div>
        <div v-if="multimedia.length === 0 && !loading" class="no-content">No multimedia content available.</div>
        <div v-else class="gallery-grid">
          <div v-for="item in multimedia" :key="item.id" class="gallery-item">
            <img v-if="item.type === 'image'" :src="item.url" :alt="item.name" class="gallery-image" />
            <video v-else controls class="gallery-video">
              <source :src="item.url" type="video/mp4" />
              Your browser does not support the video tag.
            </video>
            <p class="item-name">{{ item.name }}</p>
          </div>
        </div>
      </div>
      <Footer />
    </main>
  </div>
</template>

<script>
import axios from '@/plugins/axios';
import Navbar from "@/components/Navbar.vue";
import Footer from "@/components/Footer.vue";

export default {
  data() {
    return {
      multimedia: [],
      userProfile: {},
      selectedFile: null,
      uploadMessage: "",
      loading: false,
      error: null,
    };
  },
  components: {
    Navbar,
    Footer,
  },
  methods: {
    async fetchMultimedia() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get('/api/multimedia');
        this.multimedia = response.data;
      } finally {
        this.loading = false;
      }
    },
    async fetchUserProfile() {
      const token = localStorage.getItem('token');
      if (!token) {
        this.$router.push('/login');
        return;
      }

      try {
        const response = await axios.get('/api/user/profile', {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.userProfile = response.data; // Ensure `name` exists in the response
      } catch (error) {
        this.error = 'Failed to load user profile: ' + (error.response?.data?.message || error.message);
      }
    },
    handleFileChange(event) {
      this.selectedFile = event.target.files[0];
    },
    async uploadMultimedia() {
      if (!this.selectedFile) return;

      const formData = new FormData();
      formData.append("file", this.selectedFile);

      try {
        const token = localStorage.getItem("token");
        await axios.post("/api/user/upload", formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        });
        this.uploadMessage = "File uploaded successfully!";
        this.selectedFile = null;
        this.fetchMultimedia(); // Refresh the gallery
      } catch (error) {
        this.uploadMessage = "Failed to upload file: " + (error.response?.data?.message || error.message);
      }
    },
  },
  async mounted() {
    await this.fetchUserProfile();
    this.fetchMultimedia();
  },
};
</script>

<style scoped>
.container {
  min-height: 100vh;
  background-color: #f9fafb;
}

.main {
  max-width: 1280px;
  margin: 2rem auto;
  padding: 1rem;
}

.action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: bold;
  color: #1f2937;
}

.description {
  font-size: 0.875rem;
  color: #6b7280;
}

.refresh-button {
  display: flex;
  align-items: center;
  padding: 0.5rem 1rem;
  background: linear-gradient(to right, #9333ea, #3b82f6);
  color: #ffffff;
  border-radius: 0.375rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: background 0.2s, transform 0.2s;
}

.refresh-button:hover {
  background: linear-gradient(to right, #7e22ce, #2563eb);
}

.upload-section {
  margin: 2rem auto;
  text-align: center;
}

.upload-form {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.file-input {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  width: 100%;
  max-width: 300px;
}

.upload-button {
  padding: 0.5rem 1rem;
  background: linear-gradient(to right, #10b981, #059669);
  color: #ffffff;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: background 0.2s, transform 0.2s;
}

.upload-button:hover {
  background: linear-gradient(to right, #059669, #047857);
}

.upload-message {
  margin-top: 1rem;
  font-size: 1rem;
  color: #4caf50;
}

.gallery {
  margin-top: 2rem;
}

.loading,
.error,
.no-content {
  text-align: center;
  font-size: 1rem;
  color: #6b7280;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.gallery-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  background: #000000;
  padding: 1rem;
  border-radius: 0.375rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.gallery-item:hover {
  transform: scale(1.05);
}

.gallery-image,
.gallery-video {
  max-width: 100%;
  border-radius: 0.375rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.item-name {
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: #1f2937;
}
</style>