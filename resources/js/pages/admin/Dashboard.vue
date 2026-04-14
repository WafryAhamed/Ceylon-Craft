<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600 mt-2">Welcome back! Here's an overview of your store.</p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" v-if="!statsLoading">
        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Products</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalProducts || 0 }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m0 0L4 7m8 4v10l8-4v-10L12 11zm0 0L4 7" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Orders</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalOrders || 0 }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">${{ stats.totalRevenue || 0 }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Pending Orders</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.pendingOrders || 0 }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div v-for="i in 4" :key="i" class="bg-gray-200 rounded-lg h-32 animate-pulse"></div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-4">
          <router-link to="/admin/products/new" class="btn btn-primary">
            Add New Product
          </router-link>
          <router-link to="/admin/categories" class="btn btn-secondary">
            Manage Categories
          </router-link>
          <router-link to="/admin/orders" class="btn btn-secondary">
            View All Orders
          </router-link>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="bg-white rounded-lg shadow p-6" v-if="recentOrders.length">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-gray-900">Recent Orders</h2>
          <router-link to="/admin/orders" class="text-blue-600 hover:text-blue-800">View All</router-link>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left py-3 px-4 font-semibold">Order ID</th>
                <th class="text-left py-3 px-4 font-semibold">Customer</th>
                <th class="text-left py-3 px-4 font-semibold">Total</th>
                <th class="text-left py-3 px-4 font-semibold">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in recentOrders.slice(0, 5)" :key="order.id" class="border-b hover:bg-gray-50">
                <td class="py-3 px-4">#{{ order.id }}</td>
                <td class="py-3 px-4">{{ order.user?.name }}</td>
                <td class="py-3 px-4">${{ order.total }}</td>
                <td class="py-3 px-4">
                  <span :class="`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(order.status)}`">
                    {{ order.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useOrderStore } from '@/stores/orderStore';
import { useProductStore } from '@/stores/productStore';

const orderStore = useOrderStore();
const productStore = useProductStore();

const stats = ref({
  totalProducts: 0,
  totalOrders: 0,
  totalRevenue: 0,
  pendingOrders: 0,
});

const recentOrders = ref([]);
const statsLoading = ref(true);

const loadStats = async () => {
  try {
    statsLoading.value = true;
    
    // Fetch stats
    const orderStats = await orderStore.getOrderStats();
    stats.value = orderStats;
    
    // Fetch recent orders
    await orderStore.fetchAllOrders({ limit: 10 });
    recentOrders.value = orderStore.orders;
  } catch (error) {
    console.error('Error loading stats:', error);
  } finally {
    statsLoading.value = false;
  }
};

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    paid: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  loadStats();
});
</script>
