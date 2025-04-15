// src/plugins/axios.js
import axios from 'axios';

const instance = axios.create({
    baseURL: 'http://localhost:8000',
    headers: {
        'Content-Type': 'application/json',
    },
});

// Request interceptor to add the token
instance.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
}, error => {
    return Promise.reject(error);
});

// Response interceptor to handle errors
instance.interceptors.response.use(response => {
    return response;
}, error => {
    if (error.response && error.response.status === 401) {
        // Token is invalid or expired, redirect to login
        localStorage.removeItem('token');
        window.location.href = '/login';
    }
    return Promise.reject(error);
});

export default instance;