<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Upload Multimedia</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content class="upload-container">
      <!-- FilePond Upload -->
      <div class="upload-section">
        <h3 class="upload-title">Select Files to Upload</h3>
        <file-pond
            ref="pond"
            name="file"
            label-idle="Drag & Drop your files or <span class='filepond--label-action'>Browse</span>"
            accepted-file-types="['image/*', 'video/*']"
            allow-multiple
            @processfile="handleFileUpload"
        />
        <p v-if="message" :class="{'success-message': isSuccess, 'error-message': !isSuccess}">
          {{ message }}
        </p>
      </div>

      <!-- Multimedia Gallery -->
      <div class="gallery">
        <h3>Your Multimedia</h3>
        <div v-if="loading" class="loading">Loading...</div>
        <div v-if="error" class="error">{{ error }}</div>
        <div v-if="multimedia.length === 0 && !loading" class="no-content">No multimedia content available.</div>
        <div v-else class="gallery-grid">
          <div v-for="item in multimedia" :key="item.id" class="gallery-item">
            <img v-if="item.type === 'image'" :src="item.url" :alt="item.name" class="gallery-image" />
            <video v-else controls class="gallery-video">
              <source :src="item.url" type="video/mp4" />
            </video>
            <div class="actions">
              <ion-button color="danger" @click="deleteMedia(item.id)">Delete</ion-button>
            </div>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script>import vueFilePond from "vue-filepond";
import * as FilePond from "filepond"; // Import FilePond global object
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import "filepond/dist/filepond.min.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import axios from "@/plugins/axios";

// Register FilePond plugins with the global FilePond object
FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType);

export default {
  components: {
    FilePond: vueFilePond,
  },
  data() {
    return {
      multimedia: [],
      loading: false,
      error: null,
      message: "",
      isSuccess: false,
    };
  },
  methods: {
    async fetchMultimedia() {
      this.loading = true;
      this.error = null;
      try {
        const token = localStorage.getItem("token");
        const response = await axios.get("/api/user/media", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.multimedia = response.data;
      } catch (err) {
        this.error = "Failed to load multimedia.";
      } finally {
        this.loading = false;
      }
    },
    async handleFileUpload(error, file) {
      if (error) {
        this.message = "An error occurred while processing the file.";
        this.isSuccess = false;
        return;
      }

      const formData = new FormData();
      formData.append("file", file.file);

      try {
        const token = localStorage.getItem("token");
        await axios.post("/api/user/upload", formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        });
        this.message = "File uploaded successfully!";
        this.isSuccess = true;
        this.fetchMultimedia(); // Refresh the gallery
      } catch (err) {
        this.message = err.response?.data?.message || "Failed to upload file. Please try again.";
        this.isSuccess = false;
      }
    },
    async deleteMedia(id) {
      try {
        const token = localStorage.getItem("token");
        await axios.delete(`/api/user/media/${id}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.fetchMultimedia(); // Refresh the gallery
      } catch (err) {
        this.error = "Failed to delete media.";
      }
    },
  },
  async mounted() {
    await this.fetchMultimedia();
  },
};
</script>

<style scoped>
.upload-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem;
  background-color: #f9fafb; /* Fondo claro */
}

.upload-section {
  text-align: center;
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 600px;
  width: 100%;
  margin-bottom: 2rem;
}

.upload-title {
  font-size: 1.25rem;
  font-weight: bold;
  margin-bottom: 1rem;
  color: #1f2937;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.gallery-item {
  text-align: center;
  background: white;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.gallery-image,
.gallery-video {
  max-width: 100%;
  border-radius: 8px;
}

.success-message {
  color: #10b981; /* Verde para éxito */
}

.error-message {
  color: #ef4444; /* Rojo para errores */
}
</style>