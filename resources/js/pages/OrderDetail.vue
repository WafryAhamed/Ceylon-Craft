<template>
  <div class="bg-[#EEF0F7] min-h-screen py-12">
    <!-- BREADCRUMB -->
    <div class="max-w-7xl mx-auto px-6 mb-8">
      <nav class="flex items-center gap-2 text-[#657691]">
        <RouterLink to="/orders" class="hover:text-[#FB2B4A] transition">My Orders</RouterLink>
        <span>/</span>
        <span class="text-[#363851] font-semibold">Order Details</span>
      </nav>
    </div>

    <div v-if="order" class="max-w-7xl mx-auto px-6 space-y-8">
      <!-- ORDER HEADER -->
      <div class="bg-white rounded-xl shadow-md p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8 pb-8 border-b-2 border-[#DFE2E9]">
          <div>
            <h1 class="text-4xl font-bold text-[#363851] mb-2">Order #{{ order.id }}</h1>
            <p class="text-lg text-[#657691]">{{ formatDate(order.date) }}</p>
          </div>
          <div class="flex items-center gap-4">
            <div :class="['px-6 py-3 rounded-full font-bold text-white text-lg', statusColor(order.status)]">
              {{ order.status }}
            </div>
            <button
              @click="printOrder"
              class="px-6 py-3 border-2 border-[#DFE2E9] text-[#657691] hover:border-[#FB2B4A] hover:text-[#FB2B4A] font-bold rounded-lg transition"
            >
              Print Order
            </button>
          </div>
        </div>

        <!-- ORDER TIMELINE -->
        <div class="mb-8">
          <h3 class="text-lg font-bold text-[#363851] mb-6">Order Status</h3>
          <div class="flex items-center justify-between">
            <div v-for="(step, idx) in timeline" :key="idx" class="flex flex-col items-center flex-1">
              <div :class="[
                'w-10 h-10 rounded-full flex items-center justify-center font-bold text-white mb-2 transition',
                step.completed ? 'bg-green-500' : 'bg-[#A0ACC0]'
              ]">
                <svg v-if="step.completed" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                </svg>
                <span v-else>{{ idx + 1 }}</span>
              </div>
              <p class="text-sm font-semibold text-center text-[#657691]">{{ step.label }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ORDER CONTENT GRID -->
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- MAIN CONTENT -->
        <div class="lg:col-span-2 space-y-8">
          <!-- PRODUCTS SECTION -->
          <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-[#363851] mb-6">Order Items</h2>
            <div class="space-y-6">
              <div v-for="product in order.products" :key="product.id" class="flex gap-6 pb-6 border-b-2 border-[#DFE2E9] last:border-0 last:pb-0">
                <div class="w-24 h-24 bg-[#EEF0F7] rounded-lg flex-shrink-0">
                  <img
                    :src="`https://via.placeholder.com/96x96?text=${encodeURIComponent(product.name)}`"
                    :alt="product.name"
                    class="w-full h-full object-cover rounded-lg"
                  />
                </div>
                <div class="flex-1">
                  <h3 class="text-lg font-bold text-[#363851] mb-2">{{ product.name }}</h3>
                  <div class="flex items-center gap-4 text-[#657691]">
                    <span>Qty: <strong>{{ product.qty }}</strong></span>
                    <span>Unit Price: <strong>${{ product.price.toFixed(2) }}</strong></span>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm text-[#657691]">Subtotal</p>
                  <p class="text-2xl font-bold text-[#363851]">${{ (product.qty * product.price).toFixed(2) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- SHIPPING INFO -->
          <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-[#363851] mb-6">Shipping Information</h2>
            <div class="grid md:grid-cols-2 gap-8">
              <div>
                <h3 class="font-bold text-[#363851] mb-4">Delivery Address</h3>
                <p class="text-[#657691] leading-relaxed">
                  123 Main Street<br/>
                  New York, NY 10001<br/>
                  United States
                </p>
              </div>
              <div>
                <h3 class="font-bold text-[#363851] mb-4">Tracking Information</h3>
                <p class="text-[#657691] mb-2">
                  <strong>Carrier:</strong> FedEx
                </p>
                <p class="text-[#657691] mb-2">
                  <strong>Tracking #:</strong> 765432109876
                </p>
                <button class="px-4 py-2 border-2 border-[#FB2B4A] text-[#FB2B4A] hover:bg-[#FB2B4A] hover:text-white font-bold rounded-lg transition text-sm">
                  Track Package
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- SIDEBAR -->
        <div class="lg:col-span-1 space-y-6">
          <!-- ORDER SUMMARY -->
          <div class="bg-white rounded-xl shadow-md p-8">
            <h3 class="text-xl font-bold text-[#363851] mb-6">Order Summary</h3>
            <div class="space-y-4 pb-6 border-b-2 border-[#DFE2E9]">
              <div class="flex justify-between text-[#657691]">
                <span>Subtotal</span>
                <span>${{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-[#657691]">
                <span>Shipping</span>
                <span class="text-green-600 font-semibold">FREE</span>
              </div>
              <div class="flex justify-between text-[#657691]">
                <span>Tax (10%)</span>
                <span>${{ tax.toFixed(2) }}</span>
              </div>
            </div>
            <div class="flex justify-between text-2xl font-bold text-[#363851] pt-6">
              <span>Total</span>
              <span>${{ order.total.toFixed(2) }}</span>
            </div>
          </div>

          <!-- PAYMENT INFO -->
          <div class="bg-white rounded-xl shadow-md p-8">
            <h3 class="text-xl font-bold text-[#363851] mb-6">Payment Method</h3>
            <div class="flex items-center gap-3 p-4 bg-[#EEF0F7] rounded-lg">
              <svg class="w-8 h-8 text-[#FB2B4A]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
              </svg>
              <div>
                <p class="text-sm text-[#657691]">Credit Card</p>
                <p class="font-bold text-[#363851]">•••• •••• •••• 4242</p>
              </div>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="space-y-3">
            <button class="w-full px-4 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg">
              Download Invoice
            </button>
            <button
              @click="reorderProducts"
              class="w-full px-4 py-3 border-2 border-[#FB2B4A] text-[#FB2B4A] hover:bg-[#FB2B4A] hover:text-white font-bold rounded-lg transition-all duration-300"
            >
              Reorder
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- LOADING STATE -->
    <div v-else class="text-center py-20">
      <p class="text-2xl text-[#657691]">Loading order details...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { useToast } from '@/composables/useToast';

const route = useRoute();
const router = useRouter();
const { success } = useToast();

const order = ref(null);

const timeline = ref([
  { label: 'Order Placed', completed: true },
  { label: 'Processing', completed: true },
  { label: 'Shipped', completed: true },
  { label: 'Delivered', completed: false }
]);

const subtotal = computed(() => {
  if (!order.value) return 0;
  return order.value.products.reduce((sum, p) => sum + (p.qty * p.price), 0);
});

const tax = computed(() => subtotal.value * 0.1);

onMounted(() => {
  const orderId = route.params.orderId;
  order.value = {
    id: orderId,
    date: new Date(2026, 3, 14),
    status: 'Shipped',
    total: 143.49,
    products: [
      { id: 1, name: 'Ceramic Vase', qty: 1, price: 45.99 },
      { id: 2, name: 'Wooden Craft Box', qty: 2, price: 35.50 }
    ]
  };
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
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

const printOrder = () => {
  window.print();
  success('Printing order details...');
};

const reorderProducts = () => {
  success('Items added to cart! Redirecting to checkout...');
  setTimeout(() => {
    router.push('/cart');
  }, 1500);
};
</script>
