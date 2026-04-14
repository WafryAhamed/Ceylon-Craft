<template>
  <div class="bg-[#EEF0F7] min-h-screen py-12">
    <!-- Page Header -->
    <section class="bg-white py-12 md:py-16 border-b-2 border-[#DFE2E9]">
      <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-5xl font-bold text-[#363851] mb-2">Shopping Cart</h1>
        <p class="text-xl text-[#657691]">{{ cartItems.length }} item{{ cartItems.length !== 1 ? 's' : '' }} in your cart</p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
          <div v-if="cartItems.length > 0" class="space-y-6">
            <div v-for="item in cartItems" :key="item.id" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300 flex gap-6">
              <!-- Product Image -->
              <div class="w-28 h-28 flex-shrink-0 bg-[#EEF0F7] rounded-lg overflow-hidden">
                <img
                  :src="`https://via.placeholder.com/112x112?text=${encodeURIComponent(item.name)}`"
                  :alt="item.name"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Product Details -->
              <div class="flex-1">
                <h3 class="text-lg font-bold text-[#363851]">{{ item.name }}</h3>
                <p class="text-sm text-[#657691] mt-1">{{ item.category }}</p>
                <p class="text-2xl font-bold text-[#FB2B4A] mt-3">${{ Number(item.price).toFixed(2) }}</p>
              </div>

              <!-- Quantity & Actions -->
              <div class="flex flex-col items-end justify-between">
                <button
                  @click="removeItem(item.id)"
                  class="text-red-500 hover:text-red-700 font-semibold text-sm mb-4"
                >
                  ✕
                </button>
                <div class="flex items-center border-2 border-[#DFE2E9] rounded-lg overflow-hidden">
                  <button
                    @click="decreaseQuantity(item.id)"
                    class="px-3 py-2 text-[#657691] hover:bg-[#EEF0F7] transition"
                  >
                    −
                  </button>
                  <span class="px-4 py-2 text-[#363851] font-bold">{{ item.quantity }}</span>
                  <button
                    @click="increaseQuantity(item.id)"
                    class="px-3 py-2 text-[#657691] hover:bg-[#EEF0F7] transition"
                  >
                    +
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-16 bg-white rounded-xl shadow-md">
            <svg class="w-20 h-20 mx-auto text-[#A0ACC0] mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-xl text-[#657691] mb-6">Your cart is empty</p>
            <RouterLink to="/products" class="inline-block px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg">
              Continue Shopping
            </RouterLink>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-md p-8 sticky top-24 space-y-6">
            <h2 class="text-2xl font-bold text-[#363851]">Order Summary</h2>

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

            <div class="flex justify-between text-2xl font-bold text-[#363851]">
              <span>Total</span>
              <span>${{ total.toFixed(2) }}</span>
            </div>

            <RouterLink
              to="/checkout"
              class="w-full block text-center px-6 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
            >
              Proceed to Checkout
            </RouterLink>

            <RouterLink
              to="/products"
              class="w-full block text-center px-6 py-3 border-2 border-[#DFE2E9] text-[#657691] hover:border-[#FB2B4A] hover:text-[#FB2B4A] font-bold rounded-lg transition"
            >
              Continue Shopping
            </RouterLink>

            <!-- Promo Code -->
            <div class="pt-6 border-t-2 border-[#DFE2E9] space-y-3">
              <p class="text-sm font-semibold text-[#363851]">Promo Code</p>
              <div class="flex gap-2">
                <input
                  v-model="promoCode"
                  type="text"
                  placeholder="Enter code"
                  class="flex-1 px-4 py-2 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                />
                <button class="px-4 py-2 bg-[#DFE2E9] hover:bg-[#A0ACC0] text-[#363851] font-semibold rounded-lg transition">
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
