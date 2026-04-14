<template>
  <div class="w-full">
    <!-- Page Header -->
    <section class="bg-gradient-to-b from-[#F9F9F9] to-white py-12 md:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold text-[#5A7184] mb-4">Shop by Category</h1>
        <p class="text-gray-600">Explore our diverse collection organized by category</p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <RouterLink
          v-for="category in categories"
          :key="category.id"
          :to="`/category/${category.slug}`"
          class="group rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 bg-white transform hover:scale-105"
        >
          <div class="relative h-64 overflow-hidden bg-gray-200">
            <img
              :src="category.image"
              :alt="category.name"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition"></div>
            <div class="absolute inset-0 flex items-center justify-center">
              <h3 class="text-2xl md:text-3xl font-bold text-white text-center px-4">{{ category.name }}</h3>
            </div>
          </div>
          <div class="p-6">
            <p class="text-[#5A7184] font-semibold">{{ category.itemCount }} items</p>
            <p class="text-gray-600 text-sm mt-2">{{ category.description }}</p>
          </div>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

const categories = ref([
  {
    id: 1,
    name: 'Home Decor',
    slug: 'home-decor',
    itemCount: 24,
    image: 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=600&h=400&fit=crop',
    description: 'Vases, bowls, wall art, and decorative pieces'
  },
  {
    id: 2,
    name: 'Art & Paintings',
    slug: 'art',
    itemCount: 18,
    image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=400&fit=crop',
    description: 'Hand-painted canvas, art prints, and sculptures'
  },
  {
    id: 3,
    name: 'Journals & Books',
    slug: 'journals',
    itemCount: 12,
    image: 'https://images.unsplash.com/photo-1507842217343-583f20270319?w=600&h=400&fit=crop',
    description: 'Leather journals, sketchbooks, and notebooks'
  },
  {
    id: 4,
    name: 'Gifts & Accessories',
    slug: 'gifts',
    itemCount: 30,
    image: 'https://images.unsplash.com/photo-1525909002651-b8f576fd611d?w=600&h=400&fit=crop',
    description: 'Dream catchers, pouches, and gift sets'
  },
  {
    id: 5,
    name: 'Textiles',
    slug: 'textiles',
    itemCount: 15,
    image: 'https://images.unsplash.com/photo-1609522898422-a7b27dc5ec46?w=600&h=400&fit=crop',
    description: 'Woven tapestries, cushions, and fabrics'
  },
  {
    id: 6,
    name: 'Ceramics',
    slug: 'ceramics',
    itemCount: 20,
    image: 'https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=600&h=400&fit=crop',
    description: 'Handmade pottery, tiles, and ceramic art'
  }
]);
</script>
