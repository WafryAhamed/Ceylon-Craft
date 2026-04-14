<template>
  <div class="bg-[#EEF0F7] min-h-screen">
    <!-- Page Header -->
    <section class="bg-white py-12 md:py-16 border-b-2 border-[#DFE2E9]">
      <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-5xl font-bold text-[#363851] mb-4">Ceylon Craft Blog</h1>
        <p class="text-xl text-[#657691]">Stories, insights, and inspiration from the world of handmade crafts</p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 py-16">
      <!-- Featured Post -->
      <article class="mb-16 bg-white rounded-xl overflow-hidden border-2 border-[#DFE2E9] shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="grid grid-cols-1 md:grid-cols-2">
          <div class="h-72 md:h-full overflow-hidden">
            <img
              src="https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=800&h=500&fit=crop"
              alt="Why handmade crafts matter in the modern world - featured article"
              class="w-full h-full object-cover"
            />
          </div>
          <div class="p-8 md:p-12 flex flex-col justify-center">
            <div class="mb-4">
              <span class="inline-block px-3 py-1 bg-[#FB2B4A] text-white text-xs font-bold uppercase tracking-wider rounded-full">Featured</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-[#363851] mb-4">Top Handmade Gifts in Sri Lanka</h2>
            <p class="text-lg text-[#657691] mb-3 leading-relaxed">Discover the best handmade gift ideas sourced directly from talented Sri Lankan artisans. Perfect for any occasion and budget.</p>
            <div class="flex items-center gap-4 mb-6 text-[#657691] text-sm">
              <span>By Priya Kumari</span>
              <span>•</span>
              <span>Mar 10, 2026</span>
              <span>•</span>
              <span>5 min read</span>
            </div>
            <RouterLink 
              to="/blog/top-handmade-gifts" 
              class="inline-block px-6 py-2 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-colors duration-200 w-max"
            >
              Read Full Article →
            </RouterLink>
          </div>
        </div>
      </article>

      <!-- Blog Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <router-link
          v-for="post in posts"
          :key="post.id"
          :to="`/blog/${post.slug}`"
          class="group bg-white rounded-xl overflow-hidden border-2 border-[#DFE2E9] hover:border-[#FB2B4A] shadow-md hover:shadow-xl transition-all duration-300"
        >
          <!-- Featured Image -->
          <div class="relative h-56 overflow-hidden bg-[#EEF0F7]">
            <img
              :src="post.image"
              :alt="post.title + ' - handmade crafts article'"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div class="absolute top-4 right-4">
              <span class="inline-block px-3 py-1 bg-white/95 text-[#FB2B4A] text-xs font-bold uppercase tracking-wider rounded-full">
                {{ post.category }}
              </span>
            </div>
          </div>

          <!-- Card Content -->
          <div class="p-6 flex flex-col">
            <h3 class="text-xl font-bold text-[#363851] mb-3 group-hover:text-[#FB2B4A] transition-colors line-clamp-2">
              {{ post.title }}
            </h3>
            <p class="text-[#657691] mb-4 line-clamp-3 flex-grow leading-relaxed">
              {{ post.excerpt }}
            </p>
            
            <!-- Footer -->
            <div class="flex items-center justify-between pt-4 border-t border-[#DFE2E9]">
              <div class="text-sm">
                <p class="text-[#657691]">{{ post.author }}</p>
                <p class="text-[#A0ACC0] text-xs">{{ post.date }}</p>
              </div>
              <span class="text-[#FB2B4A] font-bold group-hover:translate-x-1 transition-transform">→</span>
            </div>
          </div>
        </router-link>
      </div>

      <!-- Newsletter CTA -->
      <section class="mt-16 bg-gradient-to-r from-[#FB2B4A] to-[#E91B3D] rounded-xl p-8 md:p-12 text-center shadow-lg">
        <h2 class="text-3xl font-bold text-white mb-3">Never Miss a Story</h2>
        <p class="text-white/90 mb-6 text-lg">Subscribe to our blog for exclusive insights and handmade craft inspiration</p>
        <div class="max-w-md mx-auto flex gap-3">
          <input
            v-model="email"
            type="email"
            placeholder="Enter your email"
            class="flex-1 px-4 py-3 bg-white border-none text-[#363851] rounded-lg focus:outline-none focus:ring-2 focus:ring-white"
          />
          <button
            @click="subscribe"
            class="px-6 py-3 bg-white text-[#FB2B4A] font-bold rounded-lg hover:bg-[#EEF0F7] transition-colors duration-200"
          >
            Subscribe
          </button>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useToast } from '@/composables/useToast';

const { success } = useToast();
const email = ref('');

const posts = ref([
  {
    id: 1,
    title: 'Why Handmade Products Are Better',
    slug: 'why-handmade-products-are-better',
    category: 'Craftsmanship',
    excerpt: 'Explore the unique qualities and benefits of choosing handmade products over mass-produced alternatives. Learn how artisanal craftsmanship creates superior quality and lasting value.',
    image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=400&fit=crop',
    author: 'Priya Kumari',
    date: 'Mar 10, 2026'
  },
  {
    id: 2,
    title: 'How Handmade Products Are Made: Behind the Scenes',
    slug: 'how-crafts-are-made',
    category: 'Process',
    excerpt: 'Take a look at the intricate process behind creating handmade crafts, from initial concept to final quality inspection. Discover the craftsmanship behind every piece.',
    image: 'https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=600&h=400&fit=crop',
    author: 'Rajith Silva',
    date: 'Mar 5, 2026'
  },
  {
    id: 3,
    title: 'Supporting Local: The Impact of Fair Trade',
    slug: 'supporting-local-fair-trade',
    category: 'Community',
    excerpt: 'Learn how fair trade practices are empowering local communities and preserving cultural heritage. Your purchases directly support Sri Lankan artisans and their families.',
    image: 'https://images.unsplash.com/photo-1525909002651-b8f576fd611d?w=600&h=400&fit=crop',
    author: 'Amara Dissanayake',
    date: 'Mar 1, 2026'
  },
  {
    id: 4,
    title: 'Sustainable Crafting: Eco-Friendly Materials',
    slug: 'sustainable-crafting',
    category: 'Sustainability',
    excerpt: 'Discover how artisans are using sustainable materials and eco-friendly practices to create beautiful, environmentally conscious handmade products for a better future.',
    image: 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=600&h=400&fit=crop',
    author: 'Lakshmi Perera',
    date: 'Feb 25, 2026'
  },
  {
    id: 5,
    title: 'Ceramic Art: Traditions Meet Innovation',
    slug: 'ceramic-art-traditions',
    category: 'Art',
    excerpt: 'How traditional ceramic techniques passed down through generations are being reimagined by modern artisans in Sri Lanka to create contemporary masterpieces.',
    image: 'https://images.unsplash.com/photo-1507842217343-583f20270319?w=600&h=400&fit=crop',
    author: 'Vikram Patel',
    date: 'Feb 20, 2026'
  },
  {
    id: 6,
    title: 'Textile Weaving: The Art of Patience',
    slug: 'textile-weaving-art',
    category: 'Craftsmanship',
    excerpt: 'Explore the meticulous art of textile weaving and the stories woven into every piece. Understand the patience, skill, and dedication required to master this ancient craft.',
    image: 'https://images.unsplash.com/photo-1609522898422-a7b27dc5ec46?w=600&h=400&fit=crop',
    author: 'Kalpana Singh',
    date: 'Feb 15, 2026'
  }
]);

const subscribe = () => {
  if (email.value) {
    success('Subscription successful! Check your email.');
    email.value = '';
  }
};
</script>
