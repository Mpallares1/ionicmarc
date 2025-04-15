import { createRouter, createWebHistory } from '@ionic/vue-router';
import HomePage from '@/views/HomePage.vue';
import TestConnection from '@/views/TestConnection.vue';
import Login from '@/views/Login.vue';
import Upload from '@/views/Upload.vue';
import Profile from '@/views/Profile.vue';
import Register from "@/views/Register.vue";

const routes = [
  {
    path: "/",
    name: "Home",
    component: HomePage, // Correctly assign the HomePage component
    meta: { requiresAuth: true },
  },
  {
    path: '/test-connection',
    component: TestConnection,
    meta: { requiresAuth: true }, // Protected route
  },
  {
    path: '/login',
    component: Login,
  },
  {
    path: '/upload',
    component: Upload,
    meta: { requiresAuth: true }, // Protected route
  },
  {
    path: '/profile',
    component: Profile,
    meta: { requiresAuth: true }, // Protected route
  },
  {
    path: '/register',
    component: Register,
    meta: { requiresAuth: false }, // Public route
  }
];

const router = createRouter({
  history: createWebHistory('/'), // Replace with your base URL if necessary
  routes,
});

// Navigation guard to protect routes
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem("token");

  if (to.matched.some((record) => record.meta.requiresAuth) && !isAuthenticated) {
    next("/login");
  } else {
    next();
  }
});

export default router;