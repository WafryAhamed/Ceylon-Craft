<template>
  <nav class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <RouterLink to="/" class="flex items-center gap-2 font-semibold text-xl text-[#5A7184]">
          <div class="w-8 h-8 bg-[#5A7184] rounded-lg flex items-center justify-center">
            <span class="text-white font-bold">CC</span>
          </div>
          Ceylon Craft
        </RouterLink>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center gap-8">
          <RouterLink to="/products" active-class="text-[#5A7184] font-semibold" class="text-gray-600 hover:text-[#5A7184] transition">
            Products
          </RouterLink>
          <RouterLink to="/categories" active-class="text-[#5A7184] font-semibold" class="text-gray-600 hover:text-[#5A7184] transition">
            Categories
          </RouterLink>
          <RouterLink to="/blog" active-class="text-[#5A7184] font-semibold" class="text-gray-600 hover:text-[#5A7184] transition">
            Blog
          </RouterLink>
          <RouterLink to="/about" active-class="text-[#5A7184] font-semibold" class="text-gray-600 hover:text-[#5A7184] transition">
            About
          </RouterLink>
          <RouterLink to="/contact" active-class="text-[#5A7184] font-semibold" class="text-gray-600 hover:text-[#5A7184] transition">
            Contact
          </RouterLink>
        </div>

        <!-- Right Actions -->
        <div class="hidden md:flex items-center gap-4">
          <div class="relative">
            <input
              type="text"
              placeholder="Search..."
              class="px-4 py-2 rounded-lg bg-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
            />
          </div>
          <RouterLink to="/cart" class="relative text-gray-600 hover:text-[#5A7184] transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
          </RouterLink>
          <RouterLink to="/login" class="px-4 py-2 rounded-lg bg-[#5A7184] text-white text-sm hover:bg-[#4a5f70] transition">
            Sign In
          </RouterLink>
        </div>

        <!-- Mobile Menu Toggle -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-100 py-4">
        <RouterLink to="/products" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Products</RouterLink>
        <RouterLink to="/categories" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Categories</RouterLink>
        <RouterLink to="/blog" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Blog</RouterLink>
        <RouterLink to="/about" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">About</RouterLink>
        <RouterLink to="/contact" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Contact</RouterLink>
        <RouterLink to="/login" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Sign In</RouterLink>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

const mobileMenuOpen = ref(false);
</script>
