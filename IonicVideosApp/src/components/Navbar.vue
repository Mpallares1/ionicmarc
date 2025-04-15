<template>
  <header class="navbar">
    <div class="navbar-content">
      <h1 class="navbar-title">MarcIonicMultimedia</h1>
      <nav class="navbar-links">
        <router-link to="/" class="navbar-link">Home</router-link>
        <router-link to="/profile" class="navbar-link">Profile</router-link>
        <router-link to="/upload" class="navbar-link">Upload</router-link>
      </nav>
    </div>
  </header>
</template>

<script>
export default {
  name: "Navbar",
};
</script>

<style scoped>
.navbar {
  background: linear-gradient(to right, #9333ea, #3b82f6);
  padding: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.navbar-content {
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.navbar-title {
  color: white;
  font-size: 1.5rem;
  font-weight: bold;
}

.navbar-links {
  display: flex;
  gap: 1rem;
}

.navbar-link {
  color: white;
  text-decoration: none;
  font-size: 1rem;
  transition: color 0.2s;
}

.navbar-link:hover {
  color: #d1d5db;
}
</style>