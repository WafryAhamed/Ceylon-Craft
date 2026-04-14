import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import API from '@/services/api';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('authToken') || null);
  const loading = ref(false);
  const error = ref(null);

  // Computed properties
  const isAuthenticated = computed(() => !!token.value && !!user.value);
  const isAdmin = computed(() => user.value?.role === 'admin');

  // Load user from localStorage on init
  const initializeAuth = () => {
    const storedUser = localStorage.getItem('user');
    if (storedUser && token.value) {
      user.value = JSON.parse(storedUser);
    }
  };

  // Register
  const register = async (credentials) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.post('/auth/register', credentials);
      token.value = response.data.token;
      user.value = response.data.user;
      
      localStorage.setItem('authToken', token.value);
      localStorage.setItem('user', JSON.stringify(user.value));
      
      return user.value;
    } catch (err) {
      error.value = err.message || 'Registration failed';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Login
  const login = async (email, password) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.post('/auth/login', { email, password });
      token.value = response.data.token;
      user.value = response.data.user;
      
      localStorage.setItem('authToken', token.value);
      localStorage.setItem('user', JSON.stringify(user.value));
      
      return user.value;
    } catch (err) {
      error.value = err.message || 'Login failed';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Logout
  const logout = async () => {
    try {
      await API.post('/auth/logout');
    } catch (err) {
      console.error('Logout error:', err);
    } finally {
      user.value = null;
      token.value = null;
      localStorage.removeItem('authToken');
      localStorage.removeItem('user');
    }
  };

  // Get current user
  const fetchMe = async () => {
    if (!token.value) return null;
    
    loading.value = true;
    try {
      const response = await API.get('/auth/me');
      user.value = response.data;
      localStorage.setItem('user', JSON.stringify(user.value));
      return user.value;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Update profile
  const updateProfile = async (profileData) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.put('/auth/profile', profileData);
      user.value = response.data;
      localStorage.setItem('user', JSON.stringify(user.value));
      return user.value;
    } catch (err) {
      error.value = err.message || 'Update failed';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    // State
    user,
    token,
    loading,
    error,
    
    // Computed
    isAuthenticated,
    isAdmin,
    
    // Actions
    initializeAuth,
    register,
    login,
    logout,
    fetchMe,
    updateProfile,
  };
});
