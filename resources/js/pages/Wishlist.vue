<template>
  <div class="bg-[#EEF0F7] min-h-screen py-12">
    <!-- PAGE HEADER -->
    <section class="bg-white py-12 md:py-16 border-b-2 border-[#DFE2E9]">
      <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-5xl font-bold text-[#363851] mb-2">
          My Wishlist
        </h1>
        <p class="text-xl text-[#657691]">
          {{ wishlistItems.length }} items saved
        </p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 py-12">
      <!-- WISHLIST ITEMS -->
      <div v-if="wishlistItems.length > 0" class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="product in wishlistItems"
          :key="product.id"
          class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-xl transition-shadow duration-300 relative"
        >
          <!-- REMOVE BUTTON -->
          <button
            @click="removeFromWishlist(product.id)"
            class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center cursor-pointer hover:bg-red-50 transition z-10"
          >
            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
            </svg>
          </button>

          <!-- IMAGE -->
          <div class="h-52 bg-gradient-to-br from-[#EEF0F7] to-[#DFE2E9] flex items-center justify-center overflow-hidden">
            <img
              :src="`https://via.placeholder.com/300x300?text=${encodeURIComponent(product.name)}`"
              :alt="product.name"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
            />
          </div>

          <!-- CONTENT -->
          <div class="p-6">
            <!-- CATEGORY -->
            <span class="inline-block px-3 py-1 bg-[#DFE2E9] text-[#FB2B4A] text-xs font-bold rounded-full uppercase mb-3">
              {{ product.category }}
            </span>

            <!-- NAME -->
            <h3 class="text-lg font-bold text-[#363851] mb-2 line-clamp-2">
              {{ product.name }}
            </h3>

            <!-- RATING -->
            <div class="flex items-center gap-2 mb-3">
              <div class="flex gap-1">
                <span v-for="i in 5" :key="i" class="text-sm" :class="i <= Math.floor(product.rating) ? 'text-[#FB2B4A]' : 'text-[#A0ACC0]'">★</span>
              </div>
              <span class="text-xs text-[#657691]">({{ product.reviews }} reviews)</span>
            </div>

            <!-- PRICE -->
            <div class="flex items-baseline gap-2 mb-4">
              <span class="text-2xl font-bold text-[#363851]">${{ product.priceAfterDiscount }}</span>
              <span v-if="product.discount" class="text-sm text-[#A0ACC0] line-through">${{ product.price }}</span>
            </div>

            <!-- ACTIONS -->
            <div class="flex gap-2">
              <button
                @click="addToCart(product)"
                class="flex-1 px-4 py-2 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg text-sm"
              >
                Add to Cart
              </button>
              <RouterLink
                :to="`/product/${product.slug}`"
                class="flex-1 px-4 py-2 border-2 border-[#DFE2E9] text-[#657691] hover:border-[#FB2B4A] hover:text-[#FB2B4A] font-bold rounded-lg transition-all duration-300 text-center text-sm"
              >
                View
              </RouterLink>
            </div>
          </div>
        </div>
      </div>

      <!-- EMPTY STATE -->
      <div v-else class="bg-white rounded-xl shadow-md p-16 text-center">
        <svg class="w-24 h-24 mx-auto mb-6 text-[#A0ACC0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        <h3 class="text-3xl font-bold text-[#363851] mb-3">Your wishlist is empty</h3>
        <p class="text-lg text-[#657691] mb-8">Start saving your favorite products to view them later!</p>
        <RouterLink
          to="/products"
          class="inline-block px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
        >
          Explore Products
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { useToast } from '@/composables/useToast';

const { success, error } = useToast();
const wishlistItems = ref([]);

onMounted(async () => {
  try {
    const response = await fetch('/api/products');
    const data = await response.json();
    
    wishlistItems.value = data.slice(0, 4).map((product, index) => ({
      ...product,
      slug: product.name.toLowerCase().replace(/\s+/g, '-'),
      category: ['Home Decor', 'Art', 'Textiles', 'Crafts'][index % 4],
      reviews: Math.floor(Math.random() * 200) + 30,
      rating: Math.floor(Math.random() * 2) + 4.2,
      discount: Math.random() > 0.6 ? Math.floor(Math.random() * 30) + 10 : null,
      price: parseFloat(product.price).toFixed(2)
    }));

    wishlistItems.value = wishlistItems.value.map(item => ({
      ...item,
      priceAfterDiscount: item.discount 
        ? (parseFloat(item.price) * (1 - item.discount / 100)).toFixed(2)
        : parseFloat(item.price).toFixed(2)
    }));
  } catch (err) {
    console.error('Error fetching wishlist:', err);
    error('Failed to load wishlist');
  }
});

const removeFromWishlist = (productId) => {
  wishlistItems.value = wishlistItems.value.filter(item => item.id !== productId);
  success('Removed from wishlist');
};

const addToCart = (product) => {
  success(`${product.name} added to cart!`);
  console.log('Added to cart:', product);
};
</script>
