<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
          <p class="text-gray-600 mt-2">Manage product categories</p>
        </div>
        <button @click="openAddModal" class="btn btn-primary">
          + Add Category
        </button>
      </div>

      <!-- Categories Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6" v-if="categories.length">
        <div v-for="category in categories" :key="category.id" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
          <div v-if="category.image" class="mb-4 rounded-lg overflow-hidden h-40 bg-gray-200">
            <img :src="category.image" :alt="category.name" class="w-full h-full object-cover" />
          </div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">{{ category.name }}</h3>
          <p class="text-gray-600 text-sm mb-4">{{ category.description }}</p>
          <div class="flex gap-2">
            <button @click="editCategory(category)" class="btn btn-sm btn-secondary flex-1">
              Edit
            </button>
            <button @click="deleteCategory(category.id)" class="btn btn-sm btn-danger flex-1">
              Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 01.586 1.414v5c0 1.1-.9 2-2 2H9c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2z" />
        </svg>
        <p class="text-gray-600 mb-4">No categories found</p>
        <button @click="openAddModal" class="btn btn-primary">
          Create your first category
        </button>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">
            {{ editingCategory ? 'Edit Category' : 'Add Category' }}
          </h2>
          
          <form @submit.prevent="saveCategory">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              ></textarea>
            </div>

            <div class="flex gap-2">
              <button type="submit" class="btn btn-primary flex-1">
                {{ editingCategory ? 'Update' : 'Create' }}
              </button>
              <button type="button" @click="closeModal" class="btn btn-secondary flex-1">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useUIStore } from '@/stores/uiStore';
import API from '@/services/api';

const uiStore = useUIStore();

const categories = ref([]);
const showModal = ref(false);
const editingCategory = ref(null);
const form = ref({
  name: '',
  description: '',
});

const loadCategories = async () => {
  try {
    const response = await API.get('/categories');
    categories.value = response.data || [];
  } catch (error) {
    console.error('Error loading categories:', error);
  }
};

const openAddModal = () => {
  editingCategory.value = null;
  form.value = { name: '', description: '' };
  showModal.value = true;
};

const editCategory = (category) => {
  editingCategory.value = category;
  form.value = {
    name: category.name,
    description: category.description,
  };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingCategory.value = null;
  form.value = { name: '', description: '' };
};

const saveCategory = async () => {
  if (!form.value.name.trim()) {
    uiStore.error('Category name is required');
    return;
  }

  try {
    if (editingCategory.value) {
      await API.put(`/categories/${editingCategory.value.id}`, form.value);
      uiStore.success('Category updated successfully');
    } else {
      await API.post('/categories', form.value);
      uiStore.success('Category created successfully');
    }
    
    closeModal();
    await loadCategories();
  } catch (error) {
    uiStore.error(error.message || 'Failed to save category');
  }
};

const deleteCategory = async (categoryId) => {
  if (!confirm('Are you sure?')) return;

  try {
    await API.delete(`/categories/${categoryId}`);
    uiStore.success('Category deleted successfully');
    await loadCategories();
  } catch (error) {
    uiStore.error('Failed to delete category');
  }
};

onMounted(() => {
  loadCategories();
});
</script>
