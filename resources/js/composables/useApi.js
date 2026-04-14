import { ref } from 'vue';
import { useUIStore } from '@/stores/uiStore';
import API from '@/services/api';

/**
 * Composable for making API calls with automatic error handling and loading states
 */
export const useApi = () => {
  const uiStore = useUIStore();
  const loading = ref(false);
  const error = ref(null);

  const call = async (apiMethod, ...args) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await apiMethod(...args);
      return response;
    } catch (err) {
      error.value = err.message || 'An error occurred';
      uiStore.error(error.value);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    loading,
    error,
    call,
  };
};

/**
 * Fetch wrapper with automatic error handling
 */
export const useFetch = async (url, options = {}) => {
  const uiStore = useUIStore();
  
  try {
    const response = await API.get(url, options);
    return { data: response, error: null };
  } catch (err) {
    const errorMessage = err.message || 'Failed to fetch data';
    uiStore.error(errorMessage);
    return { data: null, error: err };
  }
};

/**
 * Post/Mutate wrapper with automatic error handling
 */
export const useMutate = async (url, data, method = 'post', options = {}) => {
  const uiStore = useUIStore();
  
  try {
    const response = await API[method](url, data, options);
    return { data: response, error: null };
  } catch (err) {
    const errorMessage = err.message || `Failed to ${method} data`;
    uiStore.error(errorMessage);
    return { data: null, error: err };
  }
};
