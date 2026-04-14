<template>
  <footer class="bg-[#5A7184] text-white mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        <!-- About -->
        <div>
          <h3 class="text-lg font-semibold mb-4">About Ceylon Craft</h3>
          <p class="text-sm text-gray-300">Supporting local Sri Lankan artisans by bringing their handmade creations to the world.</p>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
          <ul class="space-y-2 text-sm">
            <li><RouterLink to="/" class="text-gray-300 hover:text-white transition">Home</RouterLink></li>
            <li><RouterLink to="/products" class="text-gray-300 hover:text-white transition">Products</RouterLink></li>
            <li><RouterLink to="/about" class="text-gray-300 hover:text-white transition">About</RouterLink></li>
            <li><RouterLink to="/contact" class="text-gray-300 hover:text-white transition">Contact</RouterLink></li>
          </ul>
        </div>

        <!-- Categories -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Categories</h3>
          <ul class="space-y-2 text-sm">
            <li><RouterLink to="/category/home-decor" class="text-gray-300 hover:text-white transition">Home Decor</RouterLink></li>
            <li><RouterLink to="/category/art" class="text-gray-300 hover:text-white transition">Art</RouterLink></li>
            <li><RouterLink to="/category/journals" class="text-gray-300 hover:text-white transition">Journals</RouterLink></li>
            <li><RouterLink to="/category/gifts" class="text-gray-300 hover:text-white transition">Gifts</RouterLink></li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Get in Touch</h3>
          <p class="text-sm text-gray-300 mb-2">📧 info@ceyloncraft.com</p>
          <p class="text-sm text-gray-300 mb-4">📍 Colombo, Sri Lanka</p>
          <div class="flex gap-4">
            <a href="#" class="text-gray-300 hover:text-white transition">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.39v-1.2h-2.47v8.5h2.47v-4.1c0-.55.49-1.08 1.1-1.08.61 0 1.1.53 1.1 1.08v4.1h2.47M5.9 8.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/></svg>
            </a>
            <a href="#" class="text-gray-300 hover:text-white transition">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.85.38-1.78.64-2.75.76 1--.6 1.76-1.55 2.12-2.68-.93.55-1.96.95-3.06 1.17-.88-.94-2.13-1.53-3.51-1.53-2.66 0-4.81 2.16-4.81 4.81 0 .38.04.75.15 1.1C7.7 8.94 4.1 7.12 1.67 4.18c-.4.69-.64 1.49-.64 2.35 0 1.67.85 3.14 2.14 4.01-.79-.03-1.54-.24-2.19-.6v.06c0 2.33 1.66 4.28 3.86 4.72-.4.11-.83.17-1.27.17-.31 0-.62-.03-.92-.08.62 1.91 2.41 3.3 4.53 3.34-1.65 1.29-3.73 2.06-5.99 2.06-.39 0-.77-.02-1.15-.07 2.14 1.37 4.68 2.17 7.39 2.17 8.87 0 13.7-7.35 13.7-13.7 0-.21 0-.42-.01-.62.94-.68 1.76-1.53 2.41-2.5"/></svg>
            </a>
            <a href="#" class="text-gray-300 hover:text-white transition">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5h15z"/></svg>
            </a>
          </div>
        </div>
      </div>

      <div class="border-t border-gray-400 pt-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <p class="text-sm text-gray-300">&copy; 2026 Ceylon Craft. All rights reserved. Supporting local artisans from Sri Lanka.</p>
          <div class="flex gap-6 mt-4 md:mt-0 text-sm">
            <a href="#" class="text-gray-300 hover:text-white transition">Privacy Policy</a>
            <a href="#" class="text-gray-300 hover:text-white transition">Terms of Service</a>
            <a href="#" class="text-gray-300 hover:text-white transition">Shipping Info</a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { RouterLink } from 'vue-router';
</script>
