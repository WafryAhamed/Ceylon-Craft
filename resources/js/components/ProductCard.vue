<template>
  <div class="group rounded-2xl bg-white overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-[#D1E8E2]">
    <!-- Image -->
    <RouterLink :to="`/product/${product.slug}`" class="block relative overflow-hidden bg-gray-100">
      <div class="aspect-square overflow-hidden">
        <img
          :src="product.image"
          :alt="product.name"
          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
        />
      </div>
      <div v-if="product.isNew" class="absolute top-4 right-4 bg-[#5A7184] text-white px-3 py-1 rounded-full text-xs font-semibold">
        New
      </div>
    </RouterLink>

    <!-- Content -->
    <div class="p-5">
      <p class="text-xs font-semibold text-[#D1E8E2] uppercase tracking-wider mb-2">
        {{ product.category }}
      </p>
      
      <RouterLink :to="`/product/${product.slug}`" class="text-lg font-semibold text-gray-900 hover:text-[#5A7184] transition line-clamp-2 mb-3">
        {{ product.name }}
      </RouterLink>

      <p class="text-sm text-gray-600 line-clamp-2 mb-4">
        {{ product.description }}
      </p>

      <div class="flex items-center justify-between mb-4">
        <div class="flex items-baseline gap-2">
          <span class="text-2xl font-bold text-[#5A7184]">${{ Number(product.price).toFixed(2) }}</span>
          <span v-if="product.originalPrice" class="text-sm line-through text-gray-400">
            ${{ Number(product.originalPrice).toFixed(2) }}
          </span>
        </div>
        <div v-if="product.rating" class="flex items-center gap-1">
          <span class="text-yellow-400">★</span>
          <span class="text-xs font-semibold text-gray-700">{{ product.rating }}</span>
        </div>
      </div>

      <!-- Add to Cart Button -->
      <button
        @click="addToCart"
        class="w-full bg-[#5A7184] hover:bg-[#4a5f70] text-white py-3 rounded-lg font-semibold transition-colors duration-200"
      >
        Add to Cart
      </button>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router';

defineProps({
  product: {
    type: Object,
    required: true,
    properties: {
      id: Number,
      slug: String,
      name: String,
      description: String,
      price: [Number, String],
      originalPrice: [Number, String],
      image: String,
      category: String,
      rating: [Number, String],
      isNew: Boolean
    }
  }
});

const emit = defineEmits(['add-to-cart']);

const addToCart = () => {
  emit('add-to-cart');
};
</script>
