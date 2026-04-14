<template>
  <div class="group rounded-xl bg-[#DFE2E9] overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">
    <!-- Image Container -->
    <RouterLink :to="`/product/${product.slug}`" class="block relative overflow-hidden bg-[#A0ACC0] h-64">
      <img
        :src="product.image"
        :alt="product.name"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
      />
      <div v-if="product.isNew" class="absolute top-3 right-3 bg-[#FB2B4A] text-white px-3 py-1 rounded-full text-xs font-bold">
        NEW
      </div>
      <div v-if="product.discount" class="absolute top-3 left-3 bg-[#FB2B4A] text-white px-2 py-1 rounded text-xs font-bold">
        -{{ product.discount }}%
      </div>
    </RouterLink>

    <!-- Content -->
    <div class="p-4 space-y-3">
      <p class="text-xs font-semibold text-[#657691] uppercase tracking-widest">
        {{ product.category }}
      </p>
      
      <RouterLink :to="`/product/${product.slug}`" class="text-base font-bold text-[#363851] hover:text-[#FB2B4A] transition line-clamp-2 block">
        {{ product.name }}
      </RouterLink>

      <!-- Rating -->
      <div v-if="product.rating" class="flex items-center gap-1">
        <span class="text-sm">★★★★☆</span>
        <span class="text-xs text-[#657691]">({{ product.reviews }})</span>
      </div>

      <!-- Price -->
      <div class="flex items-baseline gap-2">
        <span class="text-xl font-bold text-[#FB2B4A]">${{ Number(product.price).toFixed(2) }}</span>
        <span v-if="product.originalPrice" class="text-xs line-through text-[#A0ACC0]">
          ${{ Number(product.originalPrice).toFixed(2) }}
        </span>
      </div>

      <!-- Add to Cart Button -->
      <button
        @click="addToCart"
        class="w-full bg-[#FB2B4A] hover:bg-[#E91B3D] text-white py-3 rounded-lg font-semibold transition-all duration-300 shadow-md hover:shadow-lg"
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
      reviews: Number,
      discount: Number,
      isNew: Boolean
    }
  }
});

const emit = defineEmits(['add-to-cart']);

const addToCart = () => {
  emit('add-to-cart');
};
</script>
