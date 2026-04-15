<template>
  <div class="bg-[#EEF0F7] min-h-screen py-12">
    <!-- BREADCRUMB -->
    <div class="max-w-7xl mx-auto px-6 mb-8">
      <nav class="flex items-center gap-2 text-[#657691]">
        <RouterLink to="/" class="hover:text-[#FB2B4A] transition">Home</RouterLink>
        <span>/</span>
        <RouterLink to="/products" class="hover:text-[#FB2B4A] transition">Products</RouterLink>
        <span>/</span>
        <span class="text-[#363851] font-semibold">{{ product.name }}</span>
      </nav>
    </div>

    <div v-if="product" class="max-w-7xl mx-auto px-6 space-y-16">
      <!-- PRODUCT DETAILS SECTION -->
      <section class="grid lg:grid-cols-2 gap-12 items-start">
        <!-- GALLERY -->
        <div class="sticky top-24">
          <!-- MAIN IMAGE -->
          <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="aspect-square bg-gradient-to-br from-[#EEF0F7] to-[#DFE2E9] flex items-center justify-center relative group">
              <img
                :src="`https://via.placeholder.com/500x500?text=${encodeURIComponent(product.name)}`"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div v-if="product.discount" class="absolute top-4 right-4 bg-[#FB2B4A] text-white px-4 py-2 rounded-lg font-bold shadow-lg">
                -{{ product.discount }}%
              </div>
              <div v-if="product.isNew" class="absolute top-4 left-4 bg-[#363851] text-white px-4 py-2 rounded-lg text-xs font-bold shadow-lg">
                NEW
              </div>
            </div>
          </div>

          <!-- IMAGE THUMBNAILS -->
          <div class="flex gap-3">
            <button
              v-for="i in 4"
              :key="i"
              @click="selectedImageIndex = i"
              class="w-20 h-20 rounded-lg overflow-hidden bg-white shadow-md transition-all duration-300"
              :class="selectedImageIndex === i ? 'ring-2 ring-[#FB2B4A]' : 'hover:shadow-lg'"
            >
              <div class="w-full h-full bg-gradient-to-br from-[#EEF0F7] to-[#DFE2E9] flex items-center justify-center">
                <span class="text-xs text-[#A0ACC0]">{{ i }}</span>
              </div>
            </button>
          </div>
        </div>

        <!-- PRODUCT INFO -->
        <div class="space-y-6">
          <!-- CATEGORY BADGE -->
          <div>
            <span class="inline-block px-4 py-2 bg-[#DFE2E9] text-[#FB2B4A] text-xs font-bold rounded-full uppercase">
              {{ product.category }}
            </span>
          </div>

          <!-- TITLE -->
          <h1 class="text-5xl font-bold text-[#363851] leading-tight">
            {{ product.name }}
          </h1>

          <!-- RATING & REVIEWS -->
          <div class="flex items-center gap-4 pb-4 border-b-2 border-[#DFE2E9]">
            <div class="flex gap-1">
              <span v-for="i in 5" :key="i" class="text-2xl" :class="i <= Math.floor(product.rating) ? 'text-[#FB2B4A]' : 'text-[#A0ACC0]'">
                ★
              </span>
            </div>
            <span class="text-lg font-bold text-[#363851]">{{ product.rating.toFixed(1) }}</span>
            <span class="text-[#657691]">({{ product.reviews }} reviews)</span>
          </div>

          <!-- PRICE -->
          <div class="space-y-3">
            <div class="flex items-baseline gap-3">
              <span class="text-4xl font-bold text-[#363851]">${{ product.priceAfterDiscount }}</span>
              <span v-if="product.discount" class="text-2xl text-[#A0ACC0] line-through">${{ product.price.toFixed(2) }}</span>
            </div>
            <p class="text-[#657691] text-sm">
              <span v-if="product.discount" class="text-[#FB2B4A] font-bold">Save ${{ (product.price - product.priceAfterDiscount).toFixed(2) }} ({{ product.discount }}%)</span>
              <span v-else>Free shipping on orders over $100</span>
            </p>
          </div>

          <!-- DESCRIPTION -->
          <p class="text-lg text-[#657691] leading-relaxed pt-4">
            {{ product.description }}
          </p>

          <!-- QUANTITY SELECTOR -->
          <div class="flex items-center gap-4 pt-4">
            <label class="text-[#657691] font-semibold">Quantity:</label>
            <div class="flex items-center border-2 border-[#DFE2E9] rounded-lg overflow-hidden">
              <button
                @click="quantity = Math.max(1, quantity - 1)"
                class="px-4 py-2 text-[#657691] hover:bg-[#EEF0F7] transition"
              >
                −
              </button>
              <input
                v-model="quantity"
                type="number"
                min="1"
                class="w-12 text-center border-0 bg-white text-[#363851] font-bold focus:outline-none"
              />
              <button
                @click="quantity++"
                class="px-4 py-2 text-[#657691] hover:bg-[#EEF0F7] transition"
              >
                +
              </button>
            </div>
          </div>

          <!-- ADD TO CART BUTTON -->
          <Button
            variant="primary"
            size="lg"
            class="w-full mt-6"
            @click="handleAddToCart"
          >
            Add to Cart
          </Button>

          <!-- SHARE & WISHLIST -->
          <div class="flex gap-4 pt-4">
            <button class="flex-1 px-6 py-3 border-2 border-[#DFE2E9] text-[#363851] font-bold rounded-lg hover:border-[#FB2B4A] hover:text-[#FB2B4A] transition">
              ♡ Add to Wishlist
            </button>
            <button class="flex-1 px-6 py-3 border-2 border-[#DFE2E9] text-[#657691] font-bold rounded-lg hover:border-[#FB2B4A] hover:text-[#FB2B4A] transition">
              Share
            </button>
          </div>

          <!-- PAYMENT INFO -->
          <div class="bg-[#DFE2E9] p-6 rounded-lg space-y-3 text-sm text-[#657691]">
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5 text-[#FB2B4A]" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 1a1 1 0 011-1h8a1 1 0 011 1v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v6h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H8v1a1 1 0 11-2 0v-1H4a2 2 0 01-2-2v-2H1a1 1 0 110-2h1V9H1a1 1 0 010-2h1V5a2 2 0 012-2h2V1zM4 5h12v8H4V5z" />
              </svg>
              Secure checkout with SSL encryption
            </div>
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5 text-[#FB2B4A]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
              </svg>
              30-day money-back guarantee
            </div>
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5 text-[#FB2B4A]" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" />
              </svg>
              Shipping & returns available
            </div>
          </div>
        </div>
      </section>

      <!-- REVIEWS SECTION -->
      <section class="bg-white rounded-xl shadow-md p-8">
        <h2 class="text-3xl font-bold text-[#363851] mb-8">Customer Reviews</h2>

        <div class="grid md:grid-cols-2 gap-6 mb-8">
          <!-- REVIEW CARD 1 -->
          <div class="border-2 border-[#DFE2E9] rounded-lg p-6">
            <div class="flex items-start justify-between mb-4">
              <div>
                <h4 class="font-bold text-[#363851]">Absolutely Beautiful!</h4>
                <p class="text-sm text-[#657691]">Verified Buyer • 2 weeks ago</p>
              </div>
              <div class="flex gap-1">
                <span v-for="i in 5" :key="i" class="text-[#FB2B4A]">★</span>
              </div>
            </div>
            <p class="text-[#657691] leading-relaxed">
              "This product exceeded my expectations. The craftsmanship is impeccable and the attention to detail is remarkable. Highly recommended for anyone looking for authentic Sri Lankan handmade items."
            </p>
          </div>

          <!-- REVIEW CARD 2 -->
          <div class="border-2 border-[#DFE2E9] rounded-lg p-6">
            <div class="flex items-start justify-between mb-4">
              <div>
                <h4 class="font-bold text-[#363851]">Perfect Gift</h4>
                <p class="text-sm text-[#657691]">Verified Buyer • 1 month ago</p>
              </div>
              <div class="flex gap-1">
                <span v-for="i in 4" :key="i" class="text-[#FB2B4A]">★</span>
                <span class="text-[#A0ACC0]">★</span>
              </div>
            </div>
            <p class="text-[#657691] leading-relaxed">
              "Sent this to my mother and she loves it. The quality is outstanding and packaging was beautiful. Package arrived safely and quickly."
            </p>
          </div>

          <!-- REVIEW CARD 3 -->
          <div class="border-2 border-[#DFE2E9] rounded-lg p-6">
            <div class="flex items-start justify-between mb-4">
              <div>
                <h4 class="font-bold text-[#363851]">Unique & Authentic</h4>
                <p class="text-sm text-[#657691]">Verified Buyer • 1 month ago</p>
              </div>
              <div class="flex gap-1">
                <span v-for="i in 5" :key="i" class="text-[#FB2B4A]">★</span>
              </div>
            </div>
            <p class="text-[#657691] leading-relaxed">
              "This is exactly what I was looking for. Each piece tells a story of Sri Lankan craftsmanship. Great value for the quality and authenticity."
            </p>
          </div>
        </div>

        <!-- WRITE REVIEW CTA -->
        <button class="w-full px-6 py-3 border-2 border-[#FB2B4A] text-[#FB2B4A] font-bold rounded-lg hover:bg-[#FB2B4A] hover:text-white transition">
          Write a Review
        </button>
      </section>

      <!-- RELATED PRODUCTS SECTION -->
      <section>
        <h2 class="text-3xl font-bold text-[#363851] mb-8">Related Products</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard
            v-for="relatedProduct in relatedProducts"
            :key="relatedProduct.id"
            :product="relatedProduct"
            @add-to-cart="handleAddToCart"
          />
        </div>
      </section>
    </div>

    <!-- LOADING STATE -->
    <div v-else class="text-center py-20">
      <p class="text-2xl text-[#657691]">Loading product details...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute, RouterLink } from 'vue-router';
import Button from '@/components/Button.vue';
import ProductCard from '@/components/ProductCard.vue';

const router = useRouter();
const route = useRoute();
const product = ref(null);
const relatedProducts = ref([]);
const selectedImageIndex = ref(1);
const quantity = ref(1);

const categories = ['Home Decor', 'Art', 'Textiles', 'Crafts', 'Jewelry', 'Ceramics'];

onMounted(async () => {
  try {
    // Fetch all products
    const response = await fetch('/api/products');
    const json = await response.json();
    const data = json.data || json;
    
    // Find product by slug or ID
    const productId = route.params.id || route.params.slug;
    const foundProduct = data.find(p => p.id == productId || p.name.toLowerCase().replace(/\s+/g, '-') === productId);
    
    if (foundProduct) {
      const categoryIndex = foundProduct.id % categories.length;
      product.value = {
        ...foundProduct,
        slug: foundProduct.name.toLowerCase().replace(/\s+/g, '-'),
        category: categories[categoryIndex],
        reviews: Math.floor(Math.random() * 200) + 30,
        rating: Math.floor(Math.random() * 2) + 4.2,
        discount: Math.random() > 0.6 ? Math.floor(Math.random() * 30) + 10 : null,
        isNew: foundProduct.id <= 2,
        description: 'Experience authentic Sri Lankan craftsmanship with this beautifully handmade product. Each piece is created by skilled artisans using traditional techniques passed down through generations. Perfect for those who appreciate quality, uniqueness, and cultural heritage.'
      };

      product.value.priceAfterDiscount = product.value.discount 
        ? (parseFloat(product.value.price) * (1 - product.value.discount / 100)).toFixed(2)
        : parseFloat(product.value.price).toFixed(2);

      // Fetch related products (same category)
      relatedProducts.value = data
        .filter(p => p.id !== foundProduct.id)
        .slice(0, 4)
        .map((p, index) => ({
          ...p,
          slug: p.name.toLowerCase().replace(/\s+/g, '-'),
          category: categories[p.id % categories.length],
          reviews: Math.floor(Math.random() * 200) + 30,
          rating: Math.floor(Math.random() * 2) + 4.2,
          discount: Math.random() > 0.7 ? Math.floor(Math.random() * 30) + 10 : null,
          isNew: index < 2
        }));
    } else {
      router.push('/products');
    }
  } catch (error) {
    console.error('Error fetching product:', error);
    router.push('/products');
  }
});

const handleAddToCart = () => {
  console.log(`Added ${quantity.value} of ${product.value.name} to cart`);
  quantity.value = 1;
};
</script>
