<template>
  <div class="bg-[#EEF0F7] min-h-screen py-12">
    <!-- PAGE HEADER -->
    <section class="bg-white py-12 md:py-16 border-b-2 border-[#DFE2E9]">
      <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-5xl font-bold text-[#363851] mb-2">
          My Orders
        </h1>
        <p class="text-xl text-[#657691]">
          {{ orders.length }} order{{ orders.length !== 1 ? 's' : '' }} found
        </p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 py-12">
      <!-- FILTERS -->
      <div class="mb-8 flex gap-4 flex-wrap">
        <button
          v-for="status in statuses"
          :key="status"
          @click="selectedStatus = selectedStatus === status ? null : status"
          :class="[
            'px-6 py-2 rounded-full font-semibold transition-all duration-300',
            selectedStatus === status
              ? 'bg-[#FB2B4A] text-white shadow-lg'
              : 'bg-white text-[#657691] border-2 border-[#DFE2E9] hover:border-[#FB2B4A]'
          ]"
        >
          {{ status === null ? 'All' : status }}
        </button>
      </div>

      <!-- ORDERS LIST -->
      <div v-if="filteredOrders.length > 0" class="space-y-6">
        <div
          v-for="order in filteredOrders"
          :key="order.id"
          class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 cursor-pointer"
          @click="navigateToDetail(order.id)"
        >
          <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <!-- ORDER INFO -->
            <div class="flex-1">
              <div class="flex items-center gap-4 mb-4">
                <div>
                  <h3 class="text-2xl font-bold text-[#363851]">Order #{{ order.id }}</h3>
                  <p class="text-[#657691]">{{ formatDate(order.date) }}</p>
                </div>
                <div :class="['px-4 py-2 rounded-full font-semibold text-white text-sm', statusColor(order.status)]">
                  {{ order.status }}
                </div>
              </div>

              <!-- PRODUCT THUMBNAILS -->
              <div class="flex gap-3 mb-4">
                <div v-for="(product, idx) in order.products.slice(0, 3)" :key="idx" class="w-12 h-12 rounded-lg bg-[#EEF0F7]">
                  <img
                    :src="`https://via.placeholder.com/48x48?text=${idx + 1}`"
                    :alt="product.name"
                    class="w-full h-full object-cover rounded-lg"
                  />
                </div>
                <div v-if="order.products.length > 3" class="w-12 h-12 rounded-lg bg-[#DFE2E9] flex items-center justify-center font-bold text-[#657691] text-sm">
                  +{{ order.products.length - 3 }}
                </div>
              </div>

              <!-- PRODUCT DETAILS -->
              <p class="text-[#657691]">
                {{ order.products.length }} item{{ order.products.length !== 1 ? 's' : '' }} • 
                <span class="text-[#363851] font-semibold">{{ order.products.map(p => p.name).join(', ') }}</span>
              </p>
            </div>

            <!-- PRICE & ACTION -->
            <div class="text-right flex flex-col items-end justify-between">
              <div class="mb-4">
                <p class="text-[#657691] text-sm mb-1">Total Amount</p>
                <p class="text-3xl font-bold text-[#363851]">${{ order.total.toFixed(2) }}</p>
              </div>
              <button
                @click.stop="navigateToDetail(order.id)"
                class="px-6 py-2 border-2 border-[#FB2B4A] text-[#FB2B4A] hover:bg-[#FB2B4A] hover:text-white font-bold rounded-lg transition-all duration-300"
              >
                View Details
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- EMPTY STATE -->
      <div v-else class="bg-white rounded-xl shadow-md p-16 text-center">
        <svg class="w-24 h-24 mx-auto mb-6 text-[#A0ACC0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <h3 class="text-3xl font-bold text-[#363851] mb-3">No orders yet</h3>
        <p class="text-lg text-[#657691] mb-8">Start shopping to place your first order!</p>
        <RouterLink
          to="/products"
          class="inline-block px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
        >
          Start Shopping
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { RouterLink } from 'vue-router';

const router = useRouter();
const selectedStatus = ref(null);

const statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

const orders = ref([
  {
    id: 'ORD-001',
    date: new Date(2026, 3, 14),
    status: 'Delivered',
    total: 143.49,
    products: [
      { id: 1, name: 'Ceramic Vase', qty: 1, price: 45.99 },
      { id: 2, name: 'Wooden Box', qty: 2, price: 35.50 }
    ]
  },
  {
    id: 'ORD-002',
    date: new Date(2026, 3, 10),
    status: 'Shipped',
    total: 62.00,
    products: [
      { id: 3, name: 'Woven Tapestry', qty: 1, price: 62.00 }
    ]
  },
  {
    id: 'ORD-003',
    date: new Date(2026, 3, 5),
    status: 'Processing',
    total: 89.99,
    products: [
      { id: 4, name: 'Hand-Painted Canvas', qty: 1, price: 55.75 },
      { id: 5, name: 'Leather Journal', qty: 1, price: 28.99 }
    ]
  },
  {
    id: 'ORD-004',
    date: new Date(2026, 2, 28),
    status: 'Delivered',
    total: 125.50,
    products: [
      { id: 6, name: 'Ceramic Bowl Set', qty: 1, price: 35.50 },
      { id: 7, name: 'Art Print', qty: 2, price: 45.00 }
    ]
  }
]);

const filteredOrders = computed(() => {
  if (!selectedStatus.value) return orders.value;
  return orders.value.filter(order => order.status === selectedStatus.value);
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const statusColor = (status) => {
  const colors = {
    'Pending': 'bg-yellow-500',
    'Processing': 'bg-blue-500',
    'Shipped': 'bg-purple-500',
    'Delivered': 'bg-green-500',
    'Cancelled': 'bg-red-500'
  };
  return colors[status] || 'bg-gray-500';
};

const navigateToDetail = (orderId) => {
  router.push(`/order/${orderId}`);
};
</script>
