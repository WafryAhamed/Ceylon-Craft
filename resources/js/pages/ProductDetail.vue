<template>
  <div class="w-full">
    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex items-center gap-2 text-sm text-gray-600">
        <RouterLink to="/" class="hover:text-[#5A7184]">Home</RouterLink>
        <span>/</span>
        <RouterLink to="/products" class="hover:text-[#5A7184]">Products</RouterLink>
        <span>/</span>
        <span class="text-[#5A7184] font-semibold">{{ product.name }}</span>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Image Gallery -->
        <div>
          <div class="bg-gray-100 rounded-2xl overflow-hidden mb-4">
            <img :src="product.image" :alt="product.name" class="w-full h-[500px] object-cover" />
          </div>
          <div class="grid grid-cols-4 gap-4">
            <div v-for="i in 4" :key="i" class="bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:ring-2 hover:ring-[#5A7184]">
              <img :src="product.image" :alt="`Gallery ${i}`" class="w-full h-24 object-cover" />
            </div>
          </div>
        </div>

        <!-- Product Details -->
        <div>
          <p class="text-sm font-semibold text-[#D1E8E2] uppercase tracking-wider mb-4">{{ product.category }}</p>
          
          <h1 class="text-4xl font-bold text-[#5A7184] mb-4">{{ product.name }}</h1>

          <div class="flex items-center gap-4 mb-6">
            <div class="flex text-yellow-400">
              <span v-for="n in 5" :key="n">★</span>
            </div>
            <span class="text-gray-600">(128 reviews)</span>
          </div>

          <div class="flex items-baseline gap-4 mb-6">
            <span class="text-4xl font-bold text-[#5A7184]">${{ Number(product.price).toFixed(2) }}</span>
            <span class="text-lg line-through text-gray-400">${{ (Number(product.price) * 1.2).toFixed(2) }}</span>
          </div>

          <p class="text-gray-700 mb-6 leading-relaxed">{{ product.description }}</p>

          <!-- Quantity Selector -->
          <div class="flex items-center gap-4 mb-8">
            <div class="flex items-center border border-gray-300 rounded-lg w-fit">
              <button @click="quantity = Math.max(1, quantity - 1)" class="px-4 py-2 text-gray-600 hover:bg-gray-100">-</button>
              <input v-model.number="quantity" type="number" class="w-16 text-center border-0 focus:outline-none" />
              <button @click="quantity++" class="px-4 py-2 text-gray-600 hover:bg-gray-100">+</button>
            </div>
            <span class="text-sm text-gray-600">{{ 12 }} in stock</span>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-4 mb-8">
            <button
              @click="addToCart"
              class="flex-1 bg-[#5A7184] hover:bg-[#4a5f70] text-white py-4 rounded-lg font-semibold transition-colors"
            >
              Add to Cart
            </button>
            <button class="w-14 border-2 border-[#5A7184] text-[#5A7184] hover:bg-[#F9F9F9] rounded-lg font-semibold transition-colors">
              ❤️
            </button>
          </div>

          <!-- Product Info Tabs -->
          <div class="border-t border-gray-200 pt-8">
            <div class="flex gap-8 mb-8 border-b border-gray-200">
              <button
                @click="activeTab = 'details'"
                :class="['pb-4 font-semibold transition', activeTab === 'details' ? 'text-[#5A7184] border-b-2 border-[#5A7184]' : 'text-gray-600']"
              >
                Details
              </button>
              <button
                @click="activeTab = 'shipping'"
                :class="['pb-4 font-semibold transition', activeTab === 'shipping' ? 'text-[#5A7184] border-b-2 border-[#5A7184]' : 'text-gray-600']"
              >
                Shipping
              </button>
            </div>
            <div v-show="activeTab === 'details'" class="space-y-3">
              <p><strong class="text-[#5A7184]">Material:</strong> Handmade ceramic with glaze</p>
              <p><strong class="text-[#5A7184]">Dimensions:</strong> 12" H x 8" W x 8" D</p>
              <p><strong class="text-[#5A7184]">Weight:</strong> 2.5 lbs</p>
              <p><strong class="text-[#5A7184]">Care:</strong> Hand wash recommended</p>
            </div>
            <div v-show="activeTab === 'shipping'" class="space-y-3">
              <p>🚚 Free shipping on orders over $100</p>
              <p>📦 Standard shipping: 5-7 business days</p>
              <p>✈️ Express shipping available</p>
              <p>🌍 Ships worldwide</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Reviews Section -->
      <div class="mt-20">
        <h2 class="text-3xl font-bold text-[#5A7184] mb-8">Customer Reviews</h2>
        <div class="space-y-6">
          <div v-for="review in reviews" :key="review.id" class="border border-gray-200 rounded-lg p-6">
            <div class="flex justify-between items-start mb-4">
              <div>
                <p class="font-semibold text-[#5A7184]">{{ review.author }}</p>
                <div class="flex text-yellow-400 text-sm">
                  <span v-for="n in review.rating" :key="n">★</span>
                </div>
              </div>
              <p class="text-sm text-gray-600">{{ review.date }}</p>
            </div>
            <p class="text-gray-700">{{ review.message }}</p>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div class="mt-20">
        <h2 class="text-3xl font-bold text-[#5A7184] mb-8">Related Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard
            v-for="product in relatedProducts"
            :key="product.id"
            :product="product"
            @add-to-cart="handleAddToCart"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { RouterLink } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';

const route = useRoute();
const quantity = ref(1);
const activeTab = ref('details');

const product = reactive({
  id: 1,
  name: 'Handmade Ceramic Vase',
  slug: 'ceramic-vase',
  price: 45.99,
  originalPrice: 55.00,
  category: 'Home Decor',
  image: 'https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=600&h=600&fit=crop',
  description: 'A stunning handmade ceramic vase crafted by skilled artisans in Sri Lanka. Each piece is unique and made with traditional techniques passed down through generations. Perfect for displaying flowers or as a standalone decorative piece.',
  rating: 4.8
});

const reviews = ref([
  {
    id: 1,
    author: 'Maria Santos',
    rating: 5,
    date: '2 weeks ago',
    message: 'Absolutely beautiful! The craftsmanship is incredible. Arrived safely and is even prettier in person than the photos.'
  },
  {
    id: 2,
    author: 'John Davis',
    rating: 4,
    date: '1 month ago',
    message: 'Great quality product. Very happy with my purchase. Perfect gift for my mother.'
  }
]);

const relatedProducts = ref([
  {
    id: 2,
    name: 'Ceramic Bowl Set',
    slug: 'ceramic-bowl-set',
    price: 35.50,
    image: 'https://images.unsplash.com/photo-1595521624277-d9ef250e3f1c?w=300&h=300&fit=crop',
    category: 'Home Decor',
    rating: '4.6',
    isNew: false
  },
  {
    id: 3,
    name: 'Woven Wall Tapestry',
    slug: 'woven-wall-tapestry',
    price: 62.00,
    image: 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=300&h=300&fit=crop',
    category: 'Art',
    rating: '4.9',
    isNew: true
  },
  {
    id: 4,
    name: 'Hand-Painted Canvas',
    slug: 'hand-painted-canvas',
    price: 55.75,
    image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=300&h=300&fit=crop',
    category: 'Art',
    rating: '4.7',
    isNew: false
  },
  {
    id: 5,
    name: 'Leather Journal',
    slug: 'leather-journal',
    price: 28.99,
    image: 'https://images.unsplash.com/photo-1507842217343-583f20270319?w=300&h=300&fit=crop',
    category: 'Journals',
    rating: '4.5',
    isNew: true
  }
]);

const addToCart = () => {
  alert(`Added ${quantity.value} item(s) to cart!`);
};

const handleAddToCart = () => {
  alert('Added to cart!');
};

onMounted(() => {
  // Fetch product details based on slug
  console.log('Product slug:', route.params.slug);
});
</script>
