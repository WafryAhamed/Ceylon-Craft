<template>
  <footer class="bg-[#363851] text-white mt-20">
    <!-- Main Footer Content -->
    <div class="max-w-7xl mx-auto px-6 py-16">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-12">
        <!-- Brand -->
        <div class="lg:col-span-1">
          <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-[#FB2B4A] rounded-lg flex items-center justify-center">
              <span class="text-white font-bold">CC</span>
            </div>
            <span class="text-xl font-bold">Ceylon Craft</span>
          </div>
          <p class="text-white/80 text-sm leading-relaxed">Supporting Sri Lankan artisans by bringing authentic handmade crafts to the world with pride and purpose.</p>
        </div>

        <!-- Shop -->
        <div>
          <h3 class="text-lg font-semibold mb-4 text-white">Shop</h3>
          <ul class="space-y-2 text-sm">
            <li><RouterLink to="/products" class="text-white/80 hover:text-[#FB2B4A] transition">All Products</RouterLink></li>
            <li><RouterLink to="/categories" class="text-white/80 hover:text-[#FB2B4A] transition">Categories</RouterLink></li>
            <li><RouterLink to="/wishlist" class="text-white/80 hover:text-[#FB2B4A] transition">My Wishlist</RouterLink></li>
            <li><RouterLink to="/orders" class="text-white/80 hover:text-[#FB2B4A] transition">My Orders</RouterLink></li>
          </ul>
        </div>

        <!-- Company -->
        <div>
          <h3 class="text-lg font-semibold mb-4 text-white">Company</h3>
          <ul class="space-y-2 text-sm">
            <li><RouterLink to="/about" class="text-white/80 hover:text-[#FB2B4A] transition">About Us</RouterLink></li>
            <li><RouterLink to="/blog" class="text-white/80 hover:text-[#FB2B4A] transition">Blog</RouterLink></li>
            <li><RouterLink to="/contact" class="text-white/80 hover:text-[#FB2B4A] transition">Contact</RouterLink></li>
            <li><RouterLink to="/faq" class="text-white/80 hover:text-[#FB2B4A] transition">FAQ</RouterLink></li>
          </ul>
        </div>

        <!-- Support -->
        <div>
          <h3 class="text-lg font-semibold mb-4 text-white">Support</h3>
          <ul class="space-y-2 text-sm">
            <li><RouterLink to="/shipping-returns" class="text-white/80 hover:text-[#FB2B4A] transition">Shipping & Returns</RouterLink></li>
            <li><RouterLink to="/privacy-policy" class="text-white/80 hover:text-[#FB2B4A] transition">Privacy Policy</RouterLink></li>
            <li><RouterLink to="/terms" class="text-white/80 hover:text-[#FB2B4A] transition">Terms & Conditions</RouterLink></li>
            <li><a href="mailto:support@ceyloncraft.com" class="text-white/80 hover:text-[#FB2B4A] transition">Support</a></li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h3 class="text-lg font-semibold mb-4 text-white">Get in Touch</h3>
          <div class="space-y-3 text-sm">
            <p><span class="block text-white/80">Email</span><a href="mailto:support@ceyloncraft.com" class="text-[#FB2B4A] hover:text-white transition font-semibold">support@ceyloncraft.com</a></p>
            <p><span class="block text-white/80">Phone</span><a href="tel:+94112345678" class="text-[#FB2B4A] hover:text-white transition font-semibold">+94 (0) 11 234 5678</a></p>
            <p><span class="block text-white/80 mb-2">Location</span><span class="font-semibold">Colombo, Sri Lanka</span></p>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="border-t border-white/20 pt-8 pb-8">
        <!-- Newsletter -->
        <div class="mb-8 p-6 bg-[#1a1f2e] rounded-lg">
          <h3 class="font-semibold text-white mb-2">Subscribe to Updates</h3>
          <p class="text-white/80 text-sm mb-4">Get exclusive offers and handmade craft inspiration delivered to your inbox</p>
          <div class="flex gap-2">
            <input
              type="email"
              placeholder="Your email"
              class="flex-1 px-4 py-2 bg-white/10 border border-white/20 text-white placeholder-white/50 rounded-lg focus:outline-none focus:border-[#FB2B4A] text-sm"
            />
            <button class="px-6 py-2 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-semibold rounded-lg transition-colors text-sm">
              Subscribe
            </button>
          </div>
        </div>

        <!-- Bottom Links -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <p class="text-sm text-white/80">&copy; 2026 Ceylon Craft. All rights reserved. | Supporting local artisans from Sri Lanka.</p>
          <div class="flex gap-6 text-sm">
            <RouterLink to="/privacy-policy" class="text-white/80 hover:text-[#FB2B4A] transition">Privacy</RouterLink>
            <RouterLink to="/terms" class="text-white/80 hover:text-[#FB2B4A] transition">Terms</RouterLink>
            <RouterLink to="/shipping-returns" class="text-white/80 hover:text-[#FB2B4A] transition">Shipping</RouterLink>
          </div>
        </div>
      </div>

      <!-- Social Links -->
      <div class="flex justify-center gap-6 pt-4 border-t border-white/20">
        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="text-white/80 hover:text-[#FB2B4A] transition p-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.39v-1.2h-2.47v8.5h2.47v-4.1c0-.55.49-1.08 1.1-1.08.61 0 1.1.53 1.1 1.08v4.1h2.47M5.9 8.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/></svg>
        </a>
        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="text-white/80 hover:text-[#FB2B4A] transition p-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" fill="none" stroke="currentColor" stroke-width="2"/></svg>
        </a>
        <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="text-white/80 hover:text-[#FB2B4A] transition p-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7"/></svg>
        </a>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { RouterLink } from 'vue-router';
</script>
