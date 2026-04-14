<template>
  <div class="w-full">
    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex items-center gap-2 text-sm text-gray-600">
        <RouterLink to="/" class="hover:text-[#5A7184]">Home</RouterLink>
        <span>/</span>
        <RouterLink to="/categories" class="hover:text-[#5A7184]">Categories</RouterLink>
        <span>/</span>
        <span class="text-[#5A7184] font-semibold capitalize">{{ route.params.slug }}</span>
      </div>
    </div>

    <!-- Page Header -->
    <section class="bg-gradient-to-b from-[#F9F9F9] to-white py-12 md:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold text-[#5A7184] mb-4 capitalize">{{ categoryName }}</h1>
        <p class="text-gray-600">Discover handmade {{ categoryName.toLowerCase() }} products</p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
          <div class="sticky top-20 space-y-6">
            <!-- Price Range -->
            <div>
              <h3 class="text-lg font-semibold text-[#5A7184] mb-4">Price Range</h3>
              <div class="space-y-2">
                <label class="flex items-center cursor-pointer">
                  <input type="radio" v-model="priceRange" value="all" class="w-4 h-4" />
                  <span class="ml-3 text-gray-700">All Prices</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="radio" v-model="priceRange" value="0-50" class="w-4 h-4" />
                  <span class="ml-3 text-gray-700">Under $50</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="radio" v-model="priceRange" value="50-100" class="w-4 h-4" />
                  <span class="ml-3 text-gray-700">$50 - $100</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="radio" v-model="priceRange" value="100+" class="w-4 h-4" />
                  <span class="ml-3 text-gray-700">Over $100</span>
                </label>
              </div>
            </div>

            <!-- Rating -->
            <div>
              <h3 class="text-lg font-semibold text-[#5A7184] mb-4">Rating</h3>
              <div class="space-y-2">
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" v-model="selectedRating" value="5" class="w-4 h-4" />
                  <span class="ml-3 text-yellow-400">★★★★★</span>
                  <span class="ml-2 text-gray-600">(5 star)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" v-model="selectedRating" value="4" class="w-4 h-4" />
                  <span class="ml-3 text-yellow-400">★★★★☆</span>
                  <span class="ml-2 text-gray-600">(4+ star)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" v-model="selectedRating" value="3" class="w-4 h-4" />
                  <span class="ml-3 text-yellow-400">★★★☆☆</span>
                  <span class="ml-2 text-gray-600">(3+ star)</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Products Grid -->
        <div class="lg:col-span-3">
          <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <ProductCard
              v-for="product in filteredProducts"
              :key="product.id"
              :product="product"
              @add-to-cart="handleAddToCart"
            />
          </div>
          <div v-else class="text-center py-12">
            <p class="text-lg text-gray-600">No products found in this category.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { RouterLink } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';

const route = useRoute();
const priceRange = ref('all');
const selectedRating = ref([]);
const products = ref([]);

const categoryName = computed(() => {
  const slug = route.params.slug;
  const names = {
    'home-decor': 'Home Decor',
    'art': 'Art & Paintings',
    'journals': 'Journals & Books',
    'gifts': 'Gifts & Accessories',
    'textiles': 'Textiles',
    'ceramics': 'Ceramics'
  };
  return names[slug] || 'Category';
});

const filteredProducts = computed(() => {
  let filtered = products.value;

  // Filter by price
  if (priceRange.value !== 'all') {
    filtered = filtered.filter(p => {
      const price = parseFloat(p.price);
      if (priceRange.value === '0-50') return price <= 50;
      if (priceRange.value === '50-100') return price >= 50 && price <= 100;
      if (priceRange.value === '100+') return price >= 100;
      return true;
    });
  }

  // Filter by rating
  if (selectedRating.value.length > 0) {
    filtered = filtered.filter(p => {
      const rating = parseFloat(p.rating || '0');
      return selectedRating.value.some(r => rating >= parseFloat(r));
    });
  }

  return filtered;
});

const handleAddToCart = () => {
  alert('Added to cart!');
};

onMounted(async () => {
  try {
    const response = await fetch('/api/products');
    const data = await response.json();
    products.value = data.map(product => ({
      ...product,
      slug: product.name.toLowerCase().replace(/\s+/g, '-'),
      category: categoryName.value,
      description: 'Beautiful handmade item',
      rating: (4 + Math.random()).toFixed(1),
      isNew: Math.random() > 0.7
    }));
  } catch (error) {
    console.error('Error fetching products:', error);
  }
});
</script>
