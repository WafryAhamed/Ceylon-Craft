<template>
  <div class="w-full">
    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-[#F9F9F9] to-white py-20 md:py-32">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
          <h1 class="text-4xl md:text-6xl font-bold text-[#5A7184] mb-6 leading-tight">
            Handmade with Love in Sri Lanka
          </h1>
          <p class="text-lg md:text-xl text-gray-600 mb-8">
            Discover unique, authentic handcrafted products created by talented Sri Lankan artisans. Each piece tells a story of passion, tradition, and craftsmanship.
          </p>
          <RouterLink
            to="/products"
            class="inline-block bg-[#5A7184] hover:bg-[#4a5f70] text-white px-8 py-4 rounded-lg font-semibold transition-colors duration-200"
          >
            Shop Now
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Featured Products Section -->
    <section class="py-16 md:py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-[#5A7184] mb-4">Featured Products</h2>
          <p class="text-gray-600">Handpicked selections from our artisans</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard
            v-for="product in featuredProducts"
            :key="product.id"
            :product="product"
            @add-to-cart="handleAddToCart"
          />
        </div>
        <div class="text-center mt-12">
          <RouterLink
            to="/products"
            class="inline-block text-[#5A7184] font-semibold hover:text-[#4a5f70] transition"
          >
            View All Products →
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 md:py-24 bg-[#F9F9F9]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-[#5A7184] mb-4">Shop by Category</h2>
          <p class="text-gray-600">Explore our diverse collection</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <RouterLink
            v-for="category in categories"
            :key="category.id"
            :to="`/category/${category.slug}`"
            class="group rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer bg-white"
          >
            <div class="relative h-48 overflow-hidden bg-gray-200">
              <img
                :src="category.image"
                :alt="category.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              />
            </div>
            <div class="p-6">
              <h3 class="text-lg font-semibold text-[#5A7184] group-hover:text-[#4a5f70] transition">
                {{ category.name }}
              </h3>
              <p class="text-sm text-gray-600 mt-2">{{ category.itemCount }} items</p>
            </div>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-16 md:py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-[#5A7184] mb-4">Why Choose Ceylon Craft?</h2>
          <p class="text-gray-600">Experience the difference of authentic handmade products</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#D1E8E2] rounded-full mb-6">
              <svg class="w-8 h-8 text-[#5A7184]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-[#5A7184] mb-3">Authentic Quality</h3>
            <p class="text-gray-600">100% handmade by skilled artisans. Every piece is unique with superior craftsmanship and attention to detail.</p>
          </div>

          <!-- Feature 2 -->
          <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#D1E8E2] rounded-full mb-6">
              <svg class="w-8 h-8 text-[#5A7184]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-[#5A7184] mb-3">Fair Trade</h3>
            <p class="text-gray-600">We ensure fair compensation for our artisans, supporting local communities and sustainable livelihoods in Sri Lanka.</p>
          </div>

          <!-- Feature 3 -->
          <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#D1E8E2] rounded-full mb-6">
              <svg class="w-8 h-8 text-[#5A7184]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h-2m0 0H8m4 0v4m4-11a8 8 0 11-16 0 8 8 0 0116 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-[#5A7184] mb-3">Eco-Friendly</h3>
            <p class="text-gray-600">Created using sustainable materials and traditional methods that respect our environment and preserve cultural heritage.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 md:py-24 bg-[#F9F9F9]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-[#5A7184] mb-4">Customer Testimonials</h2>
          <p class="text-gray-600">What our happy customers say</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="testimonial in testimonials" :key="testimonial.id" class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-[#D1E8E2] rounded-full mr-4 flex items-center justify-center font-bold text-[#5A7184]">
                {{ testimonial.author.charAt(0) }}
              </div>
              <div>
                <p class="font-semibold text-[#5A7184]">{{ testimonial.author }}</p>
                <p class="text-sm text-gray-600">{{ testimonial.role }}</p>
              </div>
            </div>
            <div class="flex text-yellow-400 mb-4">
              <span v-for="n in 5" :key="n">★</span>
            </div>
            <p class="text-gray-700">{{ testimonial.message }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-16 md:py-24 bg-[#5A7184] text-white">
      <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Stay Updated</h2>
        <p class="text-gray-200 mb-8">Subscribe to receive updates on new products and exclusive offers from Ceylon Craft.</p>
        <div class="flex gap-2">
          <input
            v-model="email"
            type="email"
            placeholder="Enter your email"
            class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
          />
          <button class="bg-[#D1E8E2] hover:bg-white text-[#5A7184] px-6 py-3 rounded-lg font-semibold transition-colors">
            Subscribe
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';

const email = ref('');
const featuredProducts = ref([]);
const categories = ref([
  {
    id: 1,
    name: 'Home Decor',
    slug: 'home-decor',
    itemCount: 24,
    image: 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=500&h=500&fit=crop'
  },
  {
    id: 2,
    name: 'Art & Paintings',
    slug: 'art',
    itemCount: 18,
    image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=500&h=500&fit=crop'
  },
  {
    id: 3,
    name: 'Journals & Books',
    slug: 'journals',
    itemCount: 12,
    image: 'https://images.unsplash.com/photo-1507842217343-583f20270319?w=500&h=500&fit=crop'
  },
  {
    id: 4,
    name: 'Gifts & Accessories',
    slug: 'gifts',
    itemCount: 30,
    image: 'https://images.unsplash.com/photo-1525909002651-b8f576fd611d?w=500&h=500&fit=crop'
  }
]);

const testimonials = ref([
  {
    id: 1,
    author: 'Sarah Mitchell',
    role: 'Interior Designer',
    message: 'Ceylon Craft\'s products are absolutely stunning! The quality and attention to detail are unmatched. I\'ve recommended them to all my clients.'
  },
  {
    id: 2,
    author: 'James Chen',
    role: 'Art Collector',
    message: 'Every piece I\'ve purchased has been a masterpiece. Supporting artisans while getting beautiful products - this is what ethical shopping looks like.'
  },
  {
    id: 3,
    author: 'Emma Rodriguez',
    role: 'Gift Enthusiast',
    message: 'The handwritten thank you notes and packaging are so thoughtful. This is not just a purchase, it\'s an experience. Highly recommend!'
  }
]);

onMounted(async () => {
  try {
    const response = await fetch('/api/products?limit=4');
    const data = await response.json();
    featuredProducts.value = data.map(product => ({
      ...product,
      slug: product.name.toLowerCase().replace(/\s+/g, '-'),
      category: 'Home Decor',
      description: 'Beautiful handmade item',
      rating: '4.8',
      isNew: Math.random() > 0.5
    }));
  } catch (error) {
    console.error('Error fetching products:', error);
  }
});

const handleAddToCart = () => {
  // TODO: Implement cart functionality
  alert('Added to cart!');
};
</script>
