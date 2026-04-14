import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useUIStore = defineStore('ui', () => {
  const notifications = ref([]);
  const modals = ref({});
  const darkMode = ref(localStorage.getItem('darkMode') === 'true');

  // Add notification (toast)
  const addNotification = (message, type = 'info', duration = 3000) => {
    const id = Date.now();
    const notification = { id, message, type };
    
    notifications.value.push(notification);
    
    if (duration) {
      setTimeout(() => {
        removeNotification(id);
      }, duration);
    }
    
    return id;
  };

  // Remove notification
  const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id);
    if (index > -1) {
      notifications.value.splice(index, 1);
    }
  };

  // Clear all notifications
  const clearNotifications = () => {
    notifications.value = [];
  };

  // Success toast
  const success = (message, duration = 3000) => {
    return addNotification(message, 'success', duration);
  };

  // Error toast
  const error = (message, duration = 3000) => {
    return addNotification(message, 'error', duration);
  };

  // Warning toast
  const warning = (message, duration = 3000) => {
    return addNotification(message, 'warning', duration);
  };

  // Info toast
  const info = (message, duration = 3000) => {
    return addNotification(message, 'info', duration);
  };

  // Open modal
  const openModal = (modalName, data = null) => {
    modals.value[modalName] = { isOpen: true, data };
  };

  // Close modal
  const closeModal = (modalName) => {
    if (modals.value[modalName]) {
      modals.value[modalName].isOpen = false;
    }
  };

  // Toggle dark mode
  const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    localStorage.setItem('darkMode', darkMode.value);
    
    if (darkMode.value) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  };

  // Initialize dark mode from localStorage
  const initDarkMode = () => {
    if (darkMode.value) {
      document.documentElement.classList.add('dark');
    }
  };

  return {
    // State
    notifications,
    modals,
    darkMode,
    
    // Actions
    addNotification,
    removeNotification,
    clearNotifications,
    success,
    error,
    warning,
    info,
    openModal,
    closeModal,
    toggleDarkMode,
    initDarkMode,
  };
});
