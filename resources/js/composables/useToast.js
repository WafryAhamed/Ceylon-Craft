import { ref } from 'vue';

const toasts = ref([]);
let toastIdCounter = 0;

export const useToast = () => {
  const show = (message, type = 'success', duration = 3000) => {
    const id = toastIdCounter++;
    const toast = {
      id,
      message,
      type, // 'success', 'error', 'warning', 'info'
      createdAt: Date.now()
    };
    
    toasts.value.push(toast);
    
    if (duration) {
      setTimeout(() => {
        remove(id);
      }, duration);
    }
    
    return id;
  };

  const remove = (id) => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index !== -1) {
      toasts.value.splice(index, 1);
    }
  };

  const success = (message, duration = 3000) => show(message, 'success', duration);
  const error = (message, duration = 5000) => show(message, 'error', duration);
  const warning = (message, duration = 4000) => show(message, 'warning', duration);
  const info = (message, duration = 3000) => show(message, 'info', duration);

  return {
    toasts,
    show,
    remove,
    success,
    error,
    warning,
    info
  };
};
