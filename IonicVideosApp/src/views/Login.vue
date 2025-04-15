<template>
  <div class="container">
    <div class="login-card">
      <div class="header">
        <h2>Welcome Back</h2>
        <p>Sign in to continue to your account</p>
      </div>
      <div class="form-container">
        <form @submit.prevent="login">
          <div class="input-group">
            <label for="email">Email Address</label>
            <div class="input-wrapper">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
              </svg>
              <input
                  id="email"
                  type="email"
                  v-model="email"
                  required
                  placeholder="you@example.com"
              />
            </div>
          </div>

          <div class="input-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <input
                  id="password"
                  type="password"
                  v-model="password"
                  required
                  placeholder="••••••••"
              />
            </div>
          </div>

          <div class="forgot-password">
            <a href="#">Forgot your password?</a>
          </div>

          <div v-if="error" class="error-message">{{ error }}</div>

          <button type="submit" class="submit-button">Sign in</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  data() {
    return {
      email: "",
      password: "",
      error: "",
    };
  },
  methods: {
    async login() {
      this.error = "";

      try {
        const response = await axios.post("/api/login", {
          email: this.email,
          password: this.password,
        });

        localStorage.setItem("token", response.data.token);
        this.$router.push("/"); // Redirige al HomePage
      } catch (error) {
        this.error = error.response?.data?.message || "Login failed. Please try again.";
      }
    }
  },
};
</script>

<style scoped>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: linear-gradient(to bottom right, #f8f9fa, #e9ecef);
}

.login-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  width: 100%;
  max-width: 400px;
}

.header {
  background: linear-gradient(to right, #6b46c1, #3182ce);
  padding: 20px;
  color: white;
  text-align: center;
}

.form-container {
  padding: 20px;
}

.input-group {
  margin-bottom: 15px;
}

.input-group label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
}

.input-wrapper {
  display: flex;
  align-items: center;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 8px;
  background: white;
}

.input-wrapper .icon {
  width: 20px;
  height: 20px;
  margin-right: 8px;
  color: #888;
}

.input-wrapper input {
  border: none;
  outline: none;
  width: 100%;
  padding: 5px;
}

.forgot-password {
  text-align: right;
  margin-bottom: 15px;
}

.forgot-password a {
  color: #6b46c1;
  text-decoration: none;
  font-size: 14px;
}

.error-message {
  background: #ffe5e5;
  color: #d32f2f;
  padding: 10px;
  border-radius: 8px;
  text-align: center;
  margin-bottom: 10px;
}

.submit-button {
  width: 100%;
  background: linear-gradient(to right, #6b46c1, #3182ce);
  color: white;
  padding: 10px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s;
}

.submit-button:hover {
  background: linear-gradient(to right, #5a37b3, #276fbf);
}
</style>