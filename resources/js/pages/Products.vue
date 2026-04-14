<template>
  <div class="w-full">
    <!-- Page Header -->
    <section class="bg-gradient-to-b from-[#F9F9F9] to-white py-12 md:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold text-[#5A7184] mb-4">Our Products</h1>
        <p class="text-gray-600">Explore our complete collection of handmade products</p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1">
          <div class="sticky top-20 space-y-6">
            <!-- Search -->
            <div>
              <h3 class="text-lg font-semibold text-[#5A7184] mb-4">Search</h3>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search products..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
              />
            </div>

            <!-- Category Filter -->
            <div>
              <h3 class="text-lg font-semibold text-[#5A7184] mb-4">Category</h3>
              <div class="space-y-2">
                <label v-for="cat in categories" :key="cat" class="flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    :value="cat"
                    v-model="selectedCategories"
                    class="w-4 h-4 rounded border-gray-300"
                  />
                  <span class="ml-3 text-gray-700">{{ cat }}</span>
                </label>
              </div>
            </div>

            <!-- Price Filter -->
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

            <!-- Sort -->
            <div>
              <h3 class="text-lg font-semibold text-[#5A7184] mb-4">Sort By</h3>
              <select v-model="sortBy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]">
                <option value="newest">Newest</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="popular">Most Popular</option>
              </select>
            </div>

            <!-- Clear Filters -->
            <button
              @click="clearFilters"
              class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-lg font-semibold transition"
            >
              Clear All Filters
            </button>
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
            <p class="text-lg text-gray-600">No products found matching your criteria.</p>
            <button @click="clearFilters" class="mt-4 text-[#5A7184] hover:text-[#4a5f70] font-semibold">
              Clear filters and try again
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import ProductCard from '../components/ProductCard.vue';

const searchQuery = ref('');
const selectedCategories = ref([]);
const priceRange = ref('all');
const sortBy = ref('newest');
const products = ref([]);

const categories = ref(['Home Decor', 'Art', 'Journals', 'Gifts', 'Textiles', 'Ceramics']);

const filteredProducts = computed(() => {
  let filtered = products.value;

  // Filter by search
  if (searchQuery.value) {
    filtered = filtered.filter(p =>
      p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      p.description?.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  // Filter by category
  if (selectedCategories.value.length > 0) {
    filtered = filtered.filter(p => selectedCategories.value.includes(p.category));
  }

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

  // Sort
  if (sortBy.value === 'price-low') {
    filtered.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
  } else if (sortBy.value === 'price-high') {
    filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
  } else if (sortBy.value === 'popular') {
    filtered.sort((a, b) => (b.rating || 0) - (a.rating || 0));
  }

  return filtered;
});

const clearFilters = () => {
  searchQuery.value = '';
  selectedCategories.value = [];
  priceRange.value = 'all';
  sortBy.value = 'newest';
};

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
      category: categories.value[Math.floor(Math.random() * categories.value.length)],
      description: 'Beautiful handmade item crafted with care',
      rating: (4 + Math.random()).toFixed(1),
      isNew: Math.random() > 0.7
    }));
  } catch (error) {
    console.error('Error fetching products:', error);
  }
});
</script>
