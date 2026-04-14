<template>
  <nav class="sticky top-0 z-50 bg-white border-b-2 border-[#DFE2E9] shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <RouterLink to="/" class="flex items-center gap-2 font-semibold text-xl text-[#363851]">
          <div class="w-8 h-8 bg-[#FB2B4A] rounded-lg flex items-center justify-center">
            <span class="text-white font-bold">CC</span>
          </div>
          Ceylon Craft
        </RouterLink>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center gap-8">
          <RouterLink to="/products" active-class="text-[#FB2B4A] font-semibold" class="text-[#657691] hover:text-[#FB2B4A] transition">
            Products
          </RouterLink>
          <RouterLink to="/categories" active-class="text-[#FB2B4A] font-semibold" class="text-[#657691] hover:text-[#FB2B4A] transition">
            Categories
          </RouterLink>
          <RouterLink to="/blog" active-class="text-[#FB2B4A] font-semibold" class="text-[#657691] hover:text-[#FB2B4A] transition">
            Blog
          </RouterLink>
          <RouterLink to="/about" active-class="text-[#FB2B4A] font-semibold" class="text-[#657691] hover:text-[#FB2B4A] transition">
            About
          </RouterLink>
          <RouterLink to="/contact" active-class="text-[#FB2B4A] font-semibold" class="text-[#657691] hover:text-[#FB2B4A] transition">
            Contact
          </RouterLink>
          <RouterLink to="/faq" active-class="text-[#FB2B4A] font-semibold" class="text-[#657691] hover:text-[#FB2B4A] transition">
            FAQ
          </RouterLink>
        </div>

        <!-- Right Actions -->
        <div class="hidden md:flex items-center gap-4">
          <!-- Search Bar -->
          <div class="relative">
            <input
              v-model="searchQuery"
              @keyup.enter="handleSearch"
              type="text"
              placeholder="Search products..."
              class="px-4 py-2 rounded-lg bg-[#EEF0F7] border-2 border-[#DFE2E9] text-sm text-[#363851] focus:outline-none focus:border-[#FB2B4A] transition"
            />
            <button
              @click="handleSearch"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 text-[#A0ACC0] hover:text-[#FB2B4A] transition"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>

          <!-- Wishlist -->
          <RouterLink to="/wishlist" class="relative text-[#657691] hover:text-[#FB2B4A] transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </RouterLink>

          <!-- Cart -->
          <RouterLink to="/cart" class="relative text-[#657691] hover:text-[#FB2B4A] transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="absolute top-0 right-0 bg-[#FB2B4A] text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
          </RouterLink>

          <!-- Profile Dropdown -->
          <div class="relative">
            <button
              @click="profileMenuOpen = !profileMenuOpen"
              class="flex items-center gap-2 text-[#657691] hover:text-[#FB2B4A] transition px-3 py-2"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span class="text-sm hidden lg:inline">Account</span>
            </button>
            <div v-if="profileMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border-2 border-[#DFE2E9] z-50">
              <RouterLink to="/profile" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7] text-sm border-b border-[#DFE2E9]">
                My Profile
              </RouterLink>
              <RouterLink to="/orders" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7] text-sm border-b border-[#DFE2E9]">
                My Orders
              </RouterLink>
              <RouterLink to="/wishlist" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7] text-sm border-b border-[#DFE2E9]">
                Wishlist
              </RouterLink>
              <button @click="logout" class="block w-full text-left px-4 py-2 text-[#FB2B4A] hover:bg-[#FEF5F6] text-sm font-semibold">
                Logout
              </button>
            </div>
          </div>
        </div>

        <!-- Mobile Menu Toggle -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-[#657691]">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileMenuOpen" class="md:hidden border-t-2 border-[#DFE2E9] py-4 space-y-2">
        <div class="px-4 pb-4">
          <input
            v-model="searchQuery"
            @keyup.enter="handleSearch"
            type="text"
            placeholder="Search products..."
            class="w-full px-4 py-2 rounded-lg bg-[#EEF0F7] border-2 border-[#DFE2E9] text-sm text-[#363851] focus:outline-none focus:border-[#FB2B4A] transition"
          />
        </div>
        <RouterLink to="/products" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7]">Products</RouterLink>
        <RouterLink to="/categories" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7]">Categories</RouterLink>
        <RouterLink to="/blog" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7]">Blog</RouterLink>
        <RouterLink to="/about" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7]">About</RouterLink>
        <RouterLink to="/contact" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7]">Contact</RouterLink>
        <RouterLink to="/faq" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7]">FAQ</RouterLink>
        <RouterLink to="/profile" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7]">Profile</RouterLink>
        <RouterLink to="/orders" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7]">Orders</RouterLink>
        <RouterLink to="/wishlist" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7]">Wishlist</RouterLink>
        <RouterLink to="/login" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7]">Sign In</RouterLink>
        <div class="border-t border-[#DFE2E9] pt-2 mt-2">
          <p class="px-4 py-2 text-xs font-semibold text-[#A0ACC0] uppercase">Help & Policies</p>
          <RouterLink to="/privacy-policy" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7] text-sm">Privacy Policy</RouterLink>
          <RouterLink to="/terms" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7] text-sm">Terms & Conditions</RouterLink>
          <RouterLink to="/shipping-returns" class="block px-4 py-2 text-[#657691] hover:bg-[#EEF0F7] text-sm">Shipping & Returns</RouterLink>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';

const router = useRouter();
const mobileMenuOpen = ref(false);
const profileMenuOpen = ref(false);
const searchQuery = ref('');

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push(`/search?q=${encodeURIComponent(searchQuery.value)}`);
    searchQuery.value = '';
    mobileMenuOpen.value = false;
  }
};

const logout = () => {
  // Handle logout logic here
  router.push('/login');
  profileMenuOpen.value = false;
};
</script>
