<template>
  <div class="bg-[#EEF0F7] min-h-screen flex items-center justify-center py-12">
    <div class="max-w-2xl w-full mx-auto px-6">
      <!-- 404 Content -->
      <div class="text-center">
        <!-- Large 404 -->
        <div class="mb-8">
          <h1 class="text-9xl md:text-[150px] font-bold text-[#FB2B4A] opacity-10">404</h1>
          <div class="relative -mt-24 md:-mt-32">
            <!-- Illustration -->
            <svg class="w-32 h-32 md:w-48 md:h-48 mx-auto text-[#FB2B4A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>

        <!-- Text -->
        <h2 class="text-4xl md:text-5xl font-bold text-[#363851] mb-4">Oops! Page Not Found</h2>
        <p class="text-xl text-[#657691] mb-8 leading-relaxed">
          The page you're looking for doesn't exist or has been moved. <br />
          Let's get you back to creating something beautiful.
        </p>

        <!-- Search Bar -->
        <div class="mb-10 max-w-md mx-auto">
          <div class="relative">
            <input
              v-model="searchQuery"
              @keyup.enter="handleSearch"
              type="text"
              placeholder="Search for products..."
              class="w-full px-6 py-3 bg-white border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
            />
            <button
              @click="handleSearch"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 text-[#FB2B4A] hover:text-[#E91B3D] transition"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
          <RouterLink
            to="/"
            class="px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg inline-block"
          >
            Back to Home
          </RouterLink>
          <RouterLink
            to="/products"
            class="px-8 py-3 border-2 border-[#DFE2E9] text-[#657691] hover:border-[#FB2B4A] hover:text-[#FB2B4A] font-bold rounded-lg transition-colors duration-200 inline-block"
          >
            Browse Products
          </RouterLink>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-xl p-8 border-2 border-[#DFE2E9]">
          <h3 class="text-lg font-bold text-[#363851] mb-6">Quick Links</h3>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <RouterLink
              to="/products"
              class="p-4 bg-[#EEF0F7] hover:bg-[#DFE2E9] rounded-lg text-[#363851] font-semibold text-sm transition-colors duration-200 text-center"
            >
              Shop All
            </RouterLink>
            <RouterLink
              to="/categories"
              class="p-4 bg-[#EEF0F7] hover:bg-[#DFE2E9] rounded-lg text-[#363851] font-semibold text-sm transition-colors duration-200 text-center"
            >
              Categories
            </RouterLink>
            <RouterLink
              to="/blog"
              class="p-4 bg-[#EEF0F7] hover:bg-[#DFE2E9] rounded-lg text-[#363851] font-semibold text-sm transition-colors duration-200 text-center"
            >
              Blog
            </RouterLink>
            <RouterLink
              to="/about"
              class="p-4 bg-[#EEF0F7] hover:bg-[#DFE2E9] rounded-lg text-[#363851] font-semibold text-sm transition-colors duration-200 text-center"
            >
              About Us
            </RouterLink>
            <RouterLink
              to="/contact"
              class="p-4 bg-[#EEF0F7] hover:bg-[#DFE2E9] rounded-lg text-[#363851] font-semibold text-sm transition-colors duration-200 text-center"
            >
              Contact
            </RouterLink>
            <RouterLink
              to="/help"
              class="p-4 bg-[#EEF0F7] hover:bg-[#DFE2E9] rounded-lg text-[#363851] font-semibold text-sm transition-colors duration-200 text-center"
            >
              Help & FAQs
            </RouterLink>
          </div>
        </div>

        <!-- Help Text -->
        <p class="text-[#657691] text-sm mt-8">
          Still need help? <RouterLink to="/contact" class="text-[#FB2B4A] hover:underline font-semibold">Contact our support team</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';

const router = useRouter();
const searchQuery = ref('');

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push(`/search?q=${encodeURIComponent(searchQuery.value)}`);
  }
};
</script>
