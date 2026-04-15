<template>
  <div class="bg-[#EEF0F7] min-h-screen">
    <!-- HERO SECTION -->
    <section class="relative py-20 md:py-32 overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 right-10 w-72 h-72 bg-[#FB2B4A] rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-20 w-96 h-96 bg-[#657691] rounded-full blur-3xl"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <!-- Hero Content -->
          <div class="space-y-6">
            <h1 class="text-5xl md:text-6xl font-bold text-[#363851] leading-tight">
              Handmade with <span class="text-[#FB2B4A]">Heart</span> from Sri Lanka
            </h1>
            <p class="text-xl text-[#657691] leading-relaxed">
              Discover authentic, handcrafted products created by talented Sri Lankan artisans. Every piece tells a story of tradition, passion, and excellence.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
              <RouterLink to="/products" class="px-6 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg text-center">
                Shop Now
              </RouterLink>
              <button class="px-6 py-3 border-2 border-[#FB2B4A] text-[#FB2B4A] hover:bg-[#FB2B4A] hover:text-white font-bold rounded-xl transition-all duration-300">
                Learn More
              </button>
            </div>
          </div>
          
          <!-- Hero Image -->
          <div class="relative h-96 md:h-full">
            <div class="absolute inset-0 bg-gradient-to-br from-[#DFE2E9] to-[#A0ACC0] rounded-2xl"></div>
            <img
              src="https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=600&h=600&fit=crop"
              alt="Hero"
              class="absolute inset-0 w-full h-full object-cover rounded-2xl"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURED PRODUCTS -->
    <section class="py-20 px-6">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-[#363851] mb-4">
            Featured Collections
          </h2>
          <p class="text-xl text-[#657691]">
            Curated selection from our most talented artisans
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard
            v-for="product in featuredProducts"
            :key="product.id"
            :product="product"
            @add-to-cart="addToCart"
          />
        </div>

        <div class="text-center mt-12">
          <RouterLink to="/products" class="text-[#FB2B4A] font-bold text-lg hover:text-[#E91B3D] transition">
            View All Products →
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- CATEGORIES -->
    <section class="py-20 px-6 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-[#363851] mb-4">
            Shop by Category
          </h2>
          <p class="text-xl text-[#657691]">
            Find exactly what you're looking for
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          <RouterLink
            v-for="category in categories"
            :key="category.id"
            :to="`/category/${category.slug}`"
            class="group relative h-64 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all"
          >
            <img
              :src="category.image"
              :alt="category.name"
              class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
              <h3 class="text-2xl font-bold text-white">{{ category.name }}</h3>
              <p class="text-white/80 text-sm mt-2">{{ category.itemCount }} items</p>
            </div>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- WHY CHOOSE US -->
    <section class="py-20 px-6 bg-[#EEF0F7]">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-[#363851] mb-4">
            Why Choose Ceylon Craft?
          </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div v-for="feature in features" :key="feature.id" class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition-all">
            <div class="w-16 h-16 bg-gradient-to-br from-[#FB2B4A] to-[#E91B3D] rounded-lg flex items-center justify-center mb-6">
              <svg class="w-8 h-8 text-white" :viewBox="feature.icon.viewBox" fill="none" stroke="currentColor">
                <path :d="feature.icon.d" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-[#363851] mb-3">{{ feature.title }}</h3>
            <p class="text-[#657691] leading-relaxed">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="py-20 px-6 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-[#363851] mb-4">
            What Our Customers Say
          </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div v-for="testimonial in testimonials" :key="testimonial.id" class="bg-[#EEF0F7] p-8 rounded-xl shadow-md">
            <div class="flex items-center gap-1 mb-4">
              <span v-for="i in 5" :key="i" class="text-[#FB2B4A]">★</span>
            </div>
            <p class="text-[#657691] leading-relaxed mb-6 italic">
              "{{ testimonial.text }}"
            </p>
            <div class="flex items-center gap-4">
              <img :src="testimonial.avatar" :alt="testimonial.author" class="w-12 h-12 rounded-full object-cover" />
              <div>
                <p class="font-bold text-[#363851]">{{ testimonial.author }}</p>
                <p class="text-sm text-[#657691]">{{ testimonial.role }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- NEWSLETTER -->
    <section class="py-20 px-6 bg-gradient-to-r from-[#FB2B4A] to-[#E91B3D]">
      <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
          Subscribe to Our Newsletter
        </h2>
        <p class="text-white/90 text-lg mb-8">
          Get exclusive updates on new arrivals, artisan stories, and special offers
        </p>

        <div class="flex flex-col sm:flex-row gap-4">
          <input
            type="email"
            placeholder="Enter your email"
            class="flex-1 px-6 py-4 rounded-xl bg-white text-[#363851] placeholder-[#A0ACC0] focus:outline-none focus:ring-4 focus:ring-white/30"
          />
          <button class="px-8 py-4 bg-white text-[#FB2B4A] font-bold rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-md hover:shadow-lg">
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
import ProductCard from '@/components/ProductCard.vue';

const featuredProducts = ref([]);
const categories = ref([
  {
    id: 1,
    name: 'Home Decor',
    slug: 'home-decor',
    itemCount: 24,
    image: 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=600&h=400&fit=crop'
  },
  {
    id: 2,
    name: 'Art & Paintings',
    slug: 'art',
    itemCount: 18,
    image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=400&fit=crop'
  },
  {
    id: 3,
    name: 'Journals & Books',
    slug: 'journals',
    itemCount: 12,
    image: 'https://images.unsplash.com/photo-1507842217343-583f20270319?w=600&h=400&fit=crop'
  },
  {
    id: 4,
    name: 'Gifts & Accessories',
    slug: 'gifts',
    itemCount: 30,
    image: 'https://images.unsplash.com/photo-1525909002651-b8f576fd611d?w=600&h=400&fit=crop'
  }
]);

const features = ref([
  {
    id: 1,
    title: 'Authentic & Handmade',
    description: 'Every product is carefully crafted by skilled Sri Lankan artisans using traditional techniques passed down through generations.',
    icon: {
      viewBox: '0 0 24 24',
      d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    }
  },
  {
    id: 2,
    title: 'Fair Trade & Direct',
    description: 'We work directly with artisans to ensure they receive fair compensation and cultural heritage is preserved.',
    icon: {
      viewBox: '0 0 24 24',
      d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    }
  },
  {
    id: 3,
    title: 'Sustainable Practices',
    description: 'All products use eco-friendly materials and sustainable production methods to protect our environment.',
    icon: {
      viewBox: '0 0 24 24',
      d: 'M13 10V3L4 14h7v7l9-11h-7z'
    }
  }
]);

const testimonials = ref([
  {
    id: 1,
    text: 'The quality and craftsmanship are absolutely exceptional. Each piece feels unique and tells a story. Highly recommend!',
    author: 'Sarah Anderson',
    role: 'Art Collector',
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop'
  },
  {
    id: 2,
    text: 'Supporting local artisans while getting beautiful, high-quality products. This is exactly what I was looking for!',
    author: 'James Mitchell',
    role: 'Design Professional',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop'
  },
  {
    id: 3,
    text: 'Fast shipping, perfect packaging, and the products exceeded my expectations. Will definitely be ordering again!',
    author: 'Emily Chen',
    role: 'Home Decorator',
    avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop'
  }
]);

onMounted(async () => {
  try {
    const response = await fetch('/api/products');
    const json = await response.json();
    const data = json.data || json;
    featuredProducts.value = data.slice(0, 4).map((product, index) => ({
      ...product,
      slug: product.name.toLowerCase().replace(/\s+/g, '-'),
      category: ['Home Decor', 'Art', 'Textiles', 'Crafts'][index % 4],
      reviews: Math.floor(Math.random() * 200) + 50,
      discount: index % 3 === 0 ? 15 : null,
      isNew: index < 2
    }));
  } catch (error) {
    console.error('Error fetching products:', error);
  }
});

const addToCart = () => {
  console.log('Product added to cart');
};
</script>
