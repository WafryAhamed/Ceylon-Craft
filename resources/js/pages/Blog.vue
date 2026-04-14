<template>
  <div class="w-full">
    <!-- Page Header -->
    <section class="bg-gradient-to-b from-[#F9F9F9] to-white py-12 md:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold text-[#5A7184] mb-4">Blog</h1>
        <p class="text-gray-600">Stories and insights from the world of handmade crafts</p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
      <!-- Featured Post -->
      <div class="mb-16 rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition bg-white">
        <div class="grid grid-cols-1 md:grid-cols-2">
          <div class="h-64 md:h-full">
            <img
              src="https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=600&h=400&fit=crop"
              alt="Featured post"
              class="w-full h-full object-cover"
            />
          </div>
          <div class="p-8 flex flex-col justify-center">
            <span class="text-xs font-semibold text-[#D1E8E2] uppercase tracking-wider mb-3">Featured</span>
            <h2 class="text-2xl md:text-3xl font-bold text-[#5A7184] mb-4">Top Handmade Gifts in Sri Lanka</h2>
            <p class="text-gray-600 mb-6">Discover the best handmade gift ideas sourced directly from talented Sri Lankan artisans. Perfect for any occasion.</p>
            <RouterLink to="/blog/top-handmade-gifts" class="text-[#5A7184] font-semibold hover:text-[#4a5f70] transition">
              Read More →
            </RouterLink>
          </div>
        </div>
      </div>

      <!-- Blog Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <RouterLink
          v-for="post in posts"
          :key="post.id"
          :to="`/blog/${post.slug}`"
          class="rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 bg-white group cursor-pointer"
        >
          <div class="relative h-48 overflow-hidden bg-gray-200">
            <img
              :src="post.image"
              :alt="post.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />
          </div>
          <div class="p-6">
            <p class="text-xs font-semibold text-[#D1E8E2] uppercase tracking-wider mb-2">{{ post.category }}</p>
            <h3 class="text-lg font-semibold text-[#5A7184] mb-3 group-hover:text-[#4a5f70] transition line-clamp-2">
              {{ post.title }}
            </h3>
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ post.excerpt }}</p>
            <div class="flex justify-between items-center text-xs text-gray-600">
              <span>{{ post.author }}</span>
              <span>{{ post.date }}</span>
            </div>
          </div>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

const posts = ref([
  {
    id: 1,
    title: 'Why Handmade Products Are Better',
    slug: 'why-handmade-products-are-better',
    category: 'Craftsmanship',
    excerpt: 'Explore the unique qualities and benefits of choosing handmade products over mass-produced alternatives.',
    image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=400&fit=crop',
    author: 'Priya Kumari',
    date: 'Mar 10, 2026'
  },
  {
    id: 2,
    title: 'How Crafts Are Made: Behind the Scenes',
    slug: 'how-crafts-are-made',
    category: 'Process',
    excerpt: 'Take a look at the intricate process behind creating handmade crafts, from concept to completion.',
    image: 'https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=600&h=400&fit=crop',
    author: 'Rajith Silva',
    date: 'Mar 5, 2026'
  },
  {
    id: 3,
    title: 'Supporting Local: The Impact of Fair Trade',
    slug: 'supporting-local-fair-trade',
    category: 'Community',
    excerpt: 'Learn how fair trade practices are empowering local communities and preserving cultural heritage.',
    image: 'https://images.unsplash.com/photo-1525909002651-b8f576fd611d?w=600&h=400&fit=crop',
    author: 'Amara Dissanayake',
    date: 'Mar 1, 2026'
  },
  {
    id: 4,
    title: 'Sustainable Crafting: Eco-Friendly Materials',
    slug: 'sustainable-crafting',
    category: 'Sustainability',
    excerpt: 'Discover how artisans are using sustainable materials to create beautiful, environmentally conscious products.',
    image: 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=600&h=400&fit=crop',
    author: 'Lakshmi Perera',
    date: 'Feb 25, 2026'
  },
  {
    id: 5,
    title: 'Ceramic Art: Traditions Meet Innovation',
    slug: 'ceramic-art-traditions',
    category: 'Art',
    excerpt: 'How traditional ceramic techniques are being reimagined by modern artisans in Sri Lanka.',
    image: 'https://images.unsplash.com/photo-1507842217343-583f20270319?w=600&h=400&fit=crop',
    author: 'Vikram Patel',
    date: 'Feb 20, 2026'
  },
  {
    id: 6,
    title: 'Textile Weaving: The Art of Patience',
    slug: 'textile-weaving-art',
    category: 'Craftsmanship',
    excerpt: 'Explore the meticulous art of textile weaving and the stories woven into every piece.',
    image: 'https://images.unsplash.com/photo-1609522898422-a7b27dc5ec46?w=600&h=400&fit=crop',
    author: 'Kalpana Singh',
    date: 'Feb 15, 2026'
  }
]);
</script>
