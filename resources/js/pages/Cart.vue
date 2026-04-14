<template>
  <div class="w-full">
    <!-- Page Header -->
    <section class="bg-gradient-to-b from-[#F9F9F9] to-white py-12 md:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold text-[#5A7184] mb-4">Shopping Cart</h1>
        <p class="text-gray-600">Review your items before checkout</p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
          <div v-if="cartItems.length > 0" class="space-y-6">
            <div v-for="item in cartItems" :key="item.id" class="flex gap-6 bg-white p-6 rounded-2xl border border-gray-100">
              <!-- Product Image -->
              <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
              </div>

              <!-- Product Details -->
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-[#5A7184]">{{ item.name }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ item.category }}</p>
                <p class="text-lg font-bold text-[#5A7184] mt-3">${{ Number(item.price).toFixed(2) }}</p>
              </div>

              <!-- Quantity & Actions -->
              <div class="flex flex-col items-end gap-4">
                <div class="flex items-center border border-gray-300 rounded-lg">
                  <button @click="decreaseQuantity(item.id)" class="px-3 py-1 text-gray-600 hover:bg-gray-100">-</button>
                  <span class="px-4 py-1">{{ item.quantity }}</span>
                  <button @click="increaseQuantity(item.id)" class="px-3 py-1 text-gray-600 hover:bg-gray-100">+</button>
                </div>
                <button @click="removeItem(item.id)" class="text-red-500 hover:text-red-700 font-semibold text-sm">
                  Remove
                </button>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-12 bg-white rounded-2xl">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-lg text-gray-600 mb-6">Your cart is empty</p>
            <RouterLink to="/products" class="inline-block bg-[#5A7184] hover:bg-[#4a5f70] text-white px-6 py-3 rounded-lg font-semibold transition">
              Continue Shopping
            </RouterLink>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-[#F9F9F9] p-6 rounded-2xl border border-gray-100 sticky top-20">
            <h2 class="text-lg font-semibold text-[#5A7184] mb-6">Order Summary</h2>

            <div class="space-y-4 mb-6 pb-6 border-b border-gray-300">
              <div class="flex justify-between text-gray-700">
                <span>Subtotal</span>
                <span>${{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-gray-700">
                <span>Shipping</span>
                <span class="text-green-600 font-semibold">FREE</span>
              </div>
              <div class="flex justify-between text-gray-700">
                <span>Tax</span>
                <span>${{ tax.toFixed(2) }}</span>
              </div>
            </div>

            <div class="flex justify-between text-xl font-bold text-[#5A7184] mb-6">
              <span>Total</span>
              <span>${{ total.toFixed(2) }}</span>
            </div>

            <RouterLink
              to="/checkout"
              class="w-full bg-[#5A7184] hover:bg-[#4a5f70] text-white py-3 rounded-lg font-semibold transition-colors text-center block mb-3"
            >
              Proceed to Checkout
            </RouterLink>

            <button class="w-full border-2 border-[#5A7184] text-[#5A7184] hover:bg-[#F9F9F9] py-3 rounded-lg font-semibold transition">
              Continue Shopping
            </button>

            <!-- Promo Code -->
            <div class="mt-6 pt-6 border-t border-gray-300">
              <p class="text-sm font-semibold text-gray-700 mb-2">Promo Code</p>
              <div class="flex gap-2">
                <input
                  v-model="promoCode"
                  type="text"
                  placeholder="Enter code"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none"
                />
                <button class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg text-sm font-semibold transition">
                  Apply
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { RouterLink } from 'vue-router';

const cartItems = ref([
  {
    id: 1,
    name: 'Handmade Ceramic Vase',
    category: 'Home Decor',
    price: 45.99,
    image: 'https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=100&h=100&fit=crop',
    quantity: 1
  },
  {
    id: 2,
    name: 'Wooden Craft Box',
    category: 'Gifts',
    price: 35.50,
    image: 'https://images.unsplash.com/photo-1595521624277-d9ef250e3f1c?w=100&h=100&fit=crop',
    quantity: 2
  },
  {
    id: 3,
    name: 'Woven Wall Tapestry',
    category: 'Art',
    price: 62.00,
    image: 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=100&h=100&fit=crop',
    quantity: 1
  }
]);

const promoCode = ref('');

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

const tax = computed(() => {
  return subtotal.value * 0.1; // 10% tax
});

const total = computed(() => {
  return subtotal.value + tax.value;
});

const increaseQuantity = (id) => {
  const item = cartItems.value.find(i => i.id === id);
  if (item) item.quantity++;
};

const decreaseQuantity = (id) => {
  const item = cartItems.value.find(i => i.id === id);
  if (item && item.quantity > 1) item.quantity--;
};

const removeItem = (id) => {
  cartItems.value = cartItems.value.filter(item => item.id !== id);
};
</script>
