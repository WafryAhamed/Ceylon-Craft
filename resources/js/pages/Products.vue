<template>
  <div class="bg-[#EEF0F7] min-h-screen">
    <!-- PAGE HEADER -->
    <section class="bg-white py-12 md:py-16 border-b-2 border-[#DFE2E9]">
      <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-5xl font-bold text-[#363851] mb-2">
          Our Collections
        </h1>
        <p class="text-xl text-[#657691]">
          Discover {{ totalProducts }} handcrafted products from Sri Lanka
        </p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 py-12">
      <div class="grid lg:grid-cols-4 gap-8">
        <!-- SIDEBAR - FILTERS -->
        <aside class="lg:col-span-1">
          <div class="sticky top-24 space-y-6">
            <!-- SEARCH BAR -->
            <div class="bg-white p-6 rounded-xl shadow-md">
              <h3 class="text-lg font-bold text-[#363851] mb-4">Search</h3>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Find product..."
                class="w-full px-4 py-3 bg-[#EEF0F7] rounded-lg text-[#363851] placeholder-[#A0ACC0] focus:outline-none focus:ring-2 focus:ring-[#FB2B4A] transition"
              />
            </div>

            <!-- CATEGORY FILTER -->
            <div class="bg-white p-6 rounded-xl shadow-md">
              <h3 class="text-lg font-bold text-[#363851] mb-4">Category</h3>
              <div class="space-y-2">
                <label v-for="cat in categories" :key="cat" class="flex items-center cursor-pointer group">
                  <input
                    type="checkbox"
                    :value="cat"
                    v-model="selectedCategories"
                    class="w-4 h-4 rounded accent-[#FB2B4A] cursor-pointer"
                  />
                  <span class="ml-3 text-[#657691] group-hover:text-[#FB2B4A] transition">{{ cat }}</span>
                </label>
              </div>
            </div>

            <!-- PRICE FILTER -->
            <div class="bg-white p-6 rounded-xl shadow-md">
              <h3 class="text-lg font-bold text-[#363851] mb-4">Price Range</h3>
              <div class="space-y-2">
                <label v-for="range in priceRanges" :key="range.id" class="flex items-center cursor-pointer group">
                  <input
                    type="radio"
                    :value="range.id"
                    v-model="selectedPriceRange"
                    class="w-4 h-4 accent-[#FB2B4A] cursor-pointer"
                  />
                  <span class="ml-3 text-[#657691] group-hover:text-[#FB2B4A] transition">{{ range.label }}</span>
                </label>
              </div>
            </div>

            <!-- RATING FILTER -->
            <div class="bg-white p-6 rounded-xl shadow-md">
              <h3 class="text-lg font-bold text-[#363851] mb-4">Rating</h3>
              <div class="space-y-2">
                <label v-for="rating in [5, 4, 3, 2, 1]" :key="rating" class="flex items-center cursor-pointer group">
                  <input
                    type="checkbox"
                    :value="rating"
                    v-model="selectedRatings"
                    class="w-4 h-4 rounded accent-[#FB2B4A] cursor-pointer"
                  />
                  <span class="ml-3 text-[#FB2B4A]">
                    <span v-for="i in rating" :key="i">★</span>
                    <span v-for="i in (5 - rating)" :key="`empty-${i}`" class="text-[#A0ACC0]">★</span>
                  </span>
                </label>
              </div>
            </div>

            <!-- CLEAR FILTERS -->
            <button
              @click="clearFilters"
              class="w-full px-4 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
            >
              Clear Filters
            </button>
          </div>
        </aside>

        <!-- MAIN CONTENT -->
        <section class="lg:col-span-3">
          <!-- TOOLBAR -->
          <div class="bg-white p-6 rounded-xl shadow-md mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-[#657691] font-semibold">
              Showing <span class="text-[#FB2B4A] font-bold">{{ filteredProducts.length }}</span> of {{ totalProducts }} products
            </p>

            <div class="flex items-center gap-4 w-full sm:w-auto">
              <label class="text-[#657691] font-semibold whitespace-nowrap">Sort by:</label>
              <select
                v-model="sortBy"
                class="px-4 py-2 bg-[#EEF0F7] text-[#363851] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FB2B4A] cursor-pointer flex-1 sm:flex-none"
              >
                <option value="newest">Newest</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="popular">Most Popular</option>
                <option value="rating">Highest Rated</option>
              </select>
            </div>
          </div>

          <!-- PRODUCTS GRID -->
          <div v-if="filteredProducts.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <ProductCard
              v-for="product in sortedProducts"
              :key="product.id"
              :product="product"
              @add-to-cart="handleAddToCart"
            />
          </div>

          <!-- NO RESULTS -->
          <div v-else class="bg-white p-12 rounded-xl shadow-md text-center">
            <svg class="w-20 h-20 mx-auto mb-6 text-[#A0ACC0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 21l-4.35-4.35m0 0A7.5 7.5 0 103.65 3.65a7.5 7.5 0 0012.3 12.3z" />
            </svg>
            <h3 class="text-2xl font-bold text-[#363851] mb-2">No Products Found</h3>
            <p class="text-[#657691]">Try adjusting your filters or search terms</p>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import ProductCard from '@/components/ProductCard.vue';

const searchQuery = ref('');
const selectedCategories = ref([]);
const selectedPriceRange = ref('all');
const selectedRatings = ref([]);
const sortBy = ref('newest');
const allProducts = ref([]);

const categories = ['Home Decor', 'Art', 'Textiles', 'Crafts', 'Jewelry'];
const priceRanges = [
  { id: 'all', label: 'All Prices', min: 0, max: Infinity },
  { id: 'under25', label: 'Under $25', min: 0, max: 25 },
  { id: '25-50', label: '$25 - $50', min: 25, max: 50 },
  { id: '50-100', label: '$50 - $100', min: 50, max: 100 },
  { id: 'over100', label: 'Over $100', min: 100, max: Infinity }
];

const totalProducts = computed(() => allProducts.value.length);

const filteredProducts = computed(() => {
  let result = allProducts.value;

  // Search filter  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(p => 
      p.name.toLowerCase().includes(query) ||
      p.category.toLowerCase().includes(query)
    );
  }

  // Category filter
  if (selectedCategories.value.length > 0) {
    result = result.filter(p => selectedCategories.value.includes(p.category));
  }

  // Price filter
  const priceRange = priceRanges.find(r => r.id === selectedPriceRange.value);
  if (priceRange) {
    result = result.filter(p => p.price >= priceRange.min && p.price <= priceRange.max);
  }

  // Rating filter
  if (selectedRatings.value.length > 0) {
    result = result.filter(p => selectedRatings.value.includes(Math.floor(p.rating)));
  }

  return result;
});

const sortedProducts = computed(() => {
  const sorted = [...filteredProducts.value];

  switch (sortBy.value) {
    case 'price-low':
      return sorted.sort((a, b) => a.price - b.price);
    case 'price-high':
      return sorted.sort((a, b) => b.price - a.price);
    case 'rating':
      return sorted.sort((a, b) => b.rating - a.rating);
    case 'popular':
      return sorted.sort((a, b) => b.reviews - a.reviews);
    case 'newest':
    default:
      return sorted;
  }
});

onMounted(async () => {
  try {
    const response = await fetch('/api/products');
    const data = await response.json();
    allProducts.value = data.map((product, index) => ({
      ...product,
      slug: product.name.toLowerCase().replace(/\s+/g, '-'),
      category: categories[index % categories.length],
      reviews: Math.floor(Math.random() * 200) + 30,
      rating: Math.floor(Math.random() * 2) + 4.2,
      discount: Math.random() > 0.7 ? Math.floor(Math.random() * 30) + 10 : null,
      isNew: index < 2
    }));
  } catch (error) {
    console.error('Error fetching products:', error);
  }
});

const clearFilters = () => {
  searchQuery.value = '';
  selectedCategories.value = [];
  selectedPriceRange.value = 'all';
  selectedRatings.value = [];
};

const handleAddToCart = () => {
  console.log('Product added to cart');
};
</script>
