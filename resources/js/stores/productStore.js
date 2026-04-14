import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import API from '@/services/api';

export const useProductStore = defineStore('product', () => {
  const products = ref([]);
  const currentProduct = ref(null);
  const loading = ref(false);
  const error = ref(null);
  
  // Filters
  const filters = ref({
    search: '',
    category: null,
    minPrice: 0,
    maxPrice: 1000,
    sortBy: 'created_at',
    sortOrder: 'desc',
    page: 1,
    perPage: 12,
  });

  const pagination = ref({
    total: 0,
    perPage: 12,
    currentPage: 1,
    lastPage: 1,
  });

  // Fetch products
  const fetchProducts = async (filterParams = {}) => {
    loading.value = true;
    error.value = null;
    
    const params = { ...filters.value, ...filterParams };

    try {
      const response = await API.get('/products', { params });
      products.value = response.data.data || [];
      pagination.value = {
        total: response.data.total,
        perPage: response.data.per_page,
        currentPage: response.data.current_page,
        lastPage: response.data.last_page,
      };
      return products.value;
    } catch (err) {
      error.value = err.message || 'Failed to fetch products';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Fetch single product by slug
  const fetchProductBySlug = async (slug) => {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await API.get(`/products/${slug}`);
      currentProduct.value = response.data;
      return currentProduct.value;
    } catch (err) {
      error.value = err.message || 'Product not found';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Fetch featured products
  const fetchFeaturedProducts = async () => {
    try {
      const response = await API.get('/products/featured');
      return response.data || [];
    } catch (err) {
      console.error('Failed to fetch featured products:', err);
      return [];
    }
  };

  // Search products
  const searchProducts = async (searchTerm) => {
    if (!searchTerm || searchTerm.trim().length < 2) {
      error.value = 'Search term must be at least 2 characters';
      return [];
    }

    loading.value = true;
    error.value = null;

    try {
      const response = await API.get('/products/search', {
        params: { q: searchTerm },
      });
      return response.data || [];
    } catch (err) {
      error.value = err.message || 'Search failed';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Update filters
  const updateFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters, page: 1 };
  };

  // Set page
  const setPage = (page) => {
    filters.value.page = page;
  };

  // Reset filters
  const resetFilters = () => {
    filters.value = {
      search: '',
      category: null,
      minPrice: 0,
      maxPrice: 1000,
      sortBy: 'created_at',
      sortOrder: 'desc',
      page: 1,
      perPage: 12,
    };
  };

  // Create product (admin)
  const createProduct = async (productData) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.post('/products', productData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      await fetchProducts();
      return response.data;
    } catch (err) {
      error.value = err.message || 'Failed to create product';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Update product (admin)
  const updateProduct = async (productId, productData) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.post(`/products/${productId}`, productData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      await fetchProducts();
      return response.data;
    } catch (err) {
      error.value = err.message || 'Failed to update product';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Delete product (admin)
  const deleteProduct = async (productId) => {
    loading.value = true;
    error.value = null;
    try {
      await API.delete(`/products/${productId}`);
      await fetchProducts();
    } catch (err) {
      error.value = err.message || 'Failed to delete product';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    // State
    products,
    currentProduct,
    loading,
    error,
    filters,
    pagination,
    
    // Actions
    fetchProducts,
    fetchProductBySlug,
    fetchFeaturedProducts,
    searchProducts,
    updateFilters,
    setPage,
    resetFilters,
    createProduct,
    updateProduct,
    deleteProduct,
  };
});
