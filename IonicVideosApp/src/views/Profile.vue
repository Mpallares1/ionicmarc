<template>
  <div class="profile-container">
    <!-- Header -->
    <Navbar />

    <main class="profile-content">
      <header class="profile-header">
        <div class="header-content">
          <h1 class="header-title">Welcome, {{ userName }}</h1>
          <p class="header-subtitle">{{ userEmail }}</p>
        </div>
      </header>

      <section class="user-details">
        <h2>Your Profile</h2>
        <p><strong>Name:</strong> {{ userName }}</p>
        <p><strong>Email:</strong> {{ userEmail }}</p>
      </section>

      <section class="upload-section">
        <h3>Upload Multimedia</h3>
        <form @submit.prevent="uploadMultimedia" class="upload-form">
          <input type="file" @change="handleFileChange" accept="image/*,video/*" class="file-input" />
          <button type="submit" :disabled="!selectedFile" class="upload-button">Upload</button>
        </form>
        <p v-if="uploadMessage" class="upload-message">{{ uploadMessage }}</p>
      </section>

      <section class="media-section">
        <h3>Your Media Files</h3>
        <div v-if="loading" class="loading">Loading...</div>
        <div v-if="error" class="error-message">{{ error }}</div>
        <div v-if="mediaFiles.length" class="media-grid">
          <div v-for="file in mediaFiles" :key="file.id" class="media-item">
            <img v-if="file.type === 'image'" :src="file.url" :alt="file.name" class="media-thumbnail" />
            <video v-else controls class="media-video">
              <source :src="file.url" type="video/mp4" />
              Your browser does not support the video tag.
            </video>
            <p class="media-name">{{ file.name }}</p>
          </div>
        </div>
        <div v-else-if="!loading && !error" class="no-files">No media files found.</div>
      </section>
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script>
import Navbar from "@/components/Navbar.vue";
import Footer from "@/components/Footer.vue";
import axios from "@/plugins/axios";

export default {
  components: {
    Navbar,
    // eslint-disable-next-line vue/no-reserved-component-names
    Footer,
  },
  data() {
    return {
      userName: "",
      userEmail: "",
      mediaFiles: [],
      selectedFile: null,
      uploadMessage: "",
      loading: false,
      error: null,
    };
  },
  methods: {
    async fetchUserData() {
      try {
        const token = localStorage.getItem("token");
        if (!token) {
          this.$router.push("/login");
          return;
        }

        const response = await axios.get("/api/user/profile", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.userName = response.data.name;
        this.userEmail = response.data.email;
      } catch (err) {
        this.error = "Failed to load user data. Please try again later.";
      }
    },

    async fetchMediaFiles() {
      this.loading = true;
      this.error = null;
      try {
        const token = localStorage.getItem("token");
        const response = await axios.get("/api/user/media", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.mediaFiles = response.data;
      } catch (err) {
        this.error = "Failed to load media files. Please try again later.";
      } finally {
        this.loading = false;
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
        this.fetchMediaFiles(); // Refresh the media files
      } catch (error) {
        this.uploadMessage = "Failed to upload file: " + (error.response?.data?.message || error.message);
      }
    },
  },
  async mounted() {
    await this.fetchUserData();
    await this.fetchMediaFiles();
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
  color: white;
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
  color: white;
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
  background: white;
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
.success-message {
  color: #10b981; /* Verde para éxito */
}

.error-message {
  color: #ef4444; /* Rojo para errores */
}
</style>