<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Products</h1>
          <p class="text-gray-600 mt-2">Manage your product catalog</p>
        </div>
        <router-link to="/admin/products/new" class="btn btn-primary">
          + Add Product
        </router-link>
      </div>

      <!-- Search and Filter -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex gap-4">
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Search products..."
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <button @click="handleSearch" class="btn btn-primary">Search</button>
        </div>
      </div>

      <!-- Products Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden" v-if="productStore.products.length">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b">
              <tr>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Product</th>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Price</th>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Stock</th>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Status</th>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in productStore.products" :key="product.id" class="border-b hover:bg-gray-50">
                <td class="py-4 px-6">
                  <div class="flex items-center gap-3">
                    <img v-if="product.image" :src="product.image" :alt="product.name" class="w-10 h-10 rounded object-cover" />
                    <div>
                      <p class="font-medium text-gray-900">{{ product.name }}</p>
                      <p class="text-sm text-gray-600">{{ product.slug }}</p>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-6 font-medium text-gray-900">${{ product.price }}</td>
                <td class="py-4 px-6">
                  <span :class="`px-3 py-1 rounded-full text-sm font-medium ${product.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`">
                    {{ product.stock }}
                  </span>
                </td>
                <td class="py-4 px-6">
                  <span :class="`px-3 py-1 rounded-full text-sm font-medium ${product.is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}`">
                    {{ product.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="py-4 px-6 flex gap-2">
                  <router-link :to="`/admin/products/${product.id}/edit`" class="btn btn-sm btn-secondary">
                    Edit
                  </router-link>
                  <button @click="deleteProduct(product.id)" class="btn btn-sm btn-danger">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m0 0L4 7m8 4v10l8-4v-10L12 11zm0 0L4 7" />
        </svg>
        <p class="text-gray-600 mb-4">No products found</p>
        <router-link to="/admin/products/new" class="btn btn-primary">
          Create your first product
        </router-link>
      </div>

      <!-- Pagination -->
      <div v-if="productStore.pagination.lastPage > 1" class="flex justify-center gap-2 mt-6">
        <button
          v-for="page in productStore.pagination.lastPage"
          :key="page"
          @click="productStore.setPage(page)"
          :class="`px-4 py-2 rounded ${page === productStore.pagination.currentPage ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300'}`"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useProductStore } from '@/stores/productStore';
import { useUIStore } from '@/stores/uiStore';

const productStore = useProductStore();
const uiStore = useUIStore();

const searchTerm = ref('');

const loadProducts = async () => {
  try {
    await productStore.fetchProducts();
  } catch (error) {
    console.error('Error loading products:', error);
  }
};

const handleSearch = async () => {
  if (searchTerm.value.trim() === '') {
    await loadProducts();
    return;
  }
  
  try {
    await productStore.searchProducts(searchTerm.value);
  } catch (error) {
    uiStore.error('Search failed');
  }
};

const deleteProduct = async (productId) => {
  if (!confirm('Are you sure you want to delete this product?')) return;
  
  try {
    await productStore.deleteProduct(productId);
    uiStore.success('Product deleted successfully');
  } catch (error) {
    uiStore.error('Failed to delete product');
  }
};

onMounted(() => {
  loadProducts();
});
</script>
