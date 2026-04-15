<template>
  <div class="bg-[#EEF0F7] min-h-screen py-12">
    <!-- PAGE HEADER -->
    <section class="bg-white py-12 md:py-16 border-b-2 border-[#DFE2E9]">
      <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-5xl font-bold text-[#363851] mb-2">
          Search Results
        </h1>
        <p class="text-xl text-[#657691]">
          <span v-if="filteredProducts.length > 0">
            Found {{ filteredProducts.length }} product{{ filteredProducts.length !== 1 ? 's' : '' }} for "<strong>{{ searchQuery }}</strong>"
          </span>
          <span v-else>
            No results for "<strong>{{ searchQuery }}</strong>"
          </span>
        </p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 py-12">
      <!-- SEARCH REFINEMENT -->
      <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search again..."
              class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
            />
          </div>
          <button
            @click="performSearch"
            class="px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
          >
            Search
          </button>
        </div>
      </div>

      <!-- RESULTS GRID -->
      <div v-if="filteredProducts.length > 0" class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in filteredProducts"
          :key="product.id"
          :product="product"
          @add-to-cart="handleAddToCart"
        />
      </div>

      <!-- EMPTY STATE -->
      <div v-else class="bg-white rounded-xl shadow-md p-16 text-center">
        <svg class="w-24 h-24 mx-auto mb-6 text-[#A0ACC0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <h3 class="text-3xl font-bold text-[#363851] mb-3">No results found</h3>
        <p class="text-lg text-[#657691] mb-3">Sorry, we couldn't find any products matching your search.</p>
        <div class="space-y-4">
          <p class="text-[#657691]">Here are some tips:</p>
          <ul class="text-left inline-block text-[#657691] space-y-2">
            <li>• Try using different keywords</li>
            <li>• Check your spelling</li>
            <li>• Try more general terms</li>
            <li>• Browse our categories</li>
          </ul>
          <div class="mt-8 space-y-4 sm:space-y-0 sm:space-x-4">
            <RouterLink
              to="/products"
              class="inline-block px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
            >
              Browse All Products
            </RouterLink>
            <RouterLink
              to="/"
              class="inline-block px-8 py-3 border-2 border-[#DFE2E9] text-[#657691] hover:border-[#FB2B4A] hover:text-[#FB2B4A] font-bold rounded-lg transition"
            >
              Go to Homepage
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { RouterLink } from 'vue-router';
import ProductCard from '@/components/ProductCard.vue';
import { useToast } from '@/composables/useToast';

const route = useRoute();
const { success } = useToast();

const searchQuery = ref('');
const allProducts = ref([]);
const categories = ['Home Decor', 'Art', 'Textiles', 'Crafts', 'Jewelry'];

const filteredProducts = computed(() => {
  if (!searchQuery.value) return [];
  
  const query = searchQuery.value.toLowerCase();
  return allProducts.value.filter(product =>
    product.name.toLowerCase().includes(query) ||
    product.category.toLowerCase().includes(query)
  );
});

onMounted(async () => {
  try {
    const response = await fetch('/api/products');
    const json = await response.json();
    const data = json.data || json;
    
    allProducts.value = data.map((product, index) => ({
      ...product,
      slug: product.name.toLowerCase().replace(/\s+/g, '-'),
      category: categories[index % categories.length],
      reviews: Math.floor(Math.random() * 200) + 30,
      rating: Math.floor(Math.random() * 2) + 4.2,
      discount: Math.random() > 0.6 ? Math.floor(Math.random() * 30) + 10 : null,
      price: parseFloat(product.price).toFixed(2)
    }));

    allProducts.value = allProducts.value.map(item => ({
      ...item,
      priceAfterDiscount: item.discount 
        ? (parseFloat(item.price) * (1 - item.discount / 100)).toFixed(2)
        : parseFloat(item.price).toFixed(2)
    }));

    // Get search query from URL
    if (route.query.q) {
      searchQuery.value = route.query.q;
    }
  } catch (error) {
    console.error('Error fetching products:', error);
  }
});

watch(() => route.query.q, (newQuery) => {
  if (newQuery) {
    searchQuery.value = newQuery;
  }
});

const performSearch = () => {
  // Update URL with search query
  if (searchQuery.value) {
    window.history.pushState(null, '', `?q=${encodeURIComponent(searchQuery.value)}`);
  }
};

const handleAddToCart = (product) => {
  success(`${product.name} added to cart!`);
};
</script>
