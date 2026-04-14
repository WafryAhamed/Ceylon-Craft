<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
        <p class="text-gray-600 mt-2">Manage customer orders</p>
      </div>

      <!-- Filter -->
      <div class="bg-white rounded-lg shadow p-6 my-6">
        <div class="flex gap-4 flex-wrap">
          <select v-model="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <button @click="loadOrders" class="btn btn-primary">Filter</button>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden" v-if="orderStore.orders.length">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b">
              <tr>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Order ID</th>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Customer</th>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Total</th>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Status</th>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Date</th>
                <th class="text-left py-3 px-6 font-semibold text-gray-900">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in orderStore.orders" :key="order.id" class="border-b hover:bg-gray-50">
                <td class="py-4 px-6 font-medium text-gray-900">#{{ order.id }}</td>
                <td class="py-4 px-6">
                  <div>
                    <p class="font-medium text-gray-900">{{ order.user?.name }}</p>
                    <p class="text-sm text-gray-600">{{ order.user?.email }}</p>
                  </div>
                </td>
                <td class="py-4 px-6 font-medium text-gray-900">${{ order.total }}</td>
                <td class="py-4 px-6">
                  <select v-model="order.status" @change="updateStatus(order.id, order.status)" :class="`px-3 py-1 rounded-full text-sm font-medium border-0 cursor-pointer ${getStatusColor(order.status)}`">
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </td>
                <td class="py-4 px-6 text-gray-600">{{ formatDate(order.created_at) }}</td>
                <td class="py-4 px-6">
                  <router-link :to="`/admin/orders/${order.id}`" class="btn btn-sm btn-secondary">
                    View Details
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <p class="text-gray-600">No orders found</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useOrderStore } from '@/stores/orderStore';
import { useUIStore } from '@/stores/uiStore';

const orderStore = useOrderStore();
const uiStore = useUIStore();

const statusFilter = ref('');

const loadOrders = async () => {
  try {
    const filters = {};
    if (statusFilter.value) {
      filters.status = statusFilter.value;
    }
    await orderStore.fetchAllOrders(filters);
  } catch (error) {
    console.error('Error loading orders:', error);
  }
};

const updateStatus = async (orderId, status) => {
  try {
    await orderStore.updateOrderStatus(orderId, status);
    uiStore.success('Order status updated');
  } catch (error) {
    uiStore.error('Failed to update order status');
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};

onMounted(() => {
  loadOrders();
});
</script>
