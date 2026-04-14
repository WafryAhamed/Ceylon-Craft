<template>
  <div class="w-full">
    <!-- Page Header -->
    <section class="bg-gradient-to-b from-[#F9F9F9] to-white py-12 md:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold text-[#5A7184] mb-4">About Ceylon Craft</h1>
        <p class="text-gray-600">Our story of supporting local artisans</p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
      <!-- Brand Story -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
        <div>
          <h2 class="text-3xl md:text-4xl font-bold text-[#5A7184] mb-6">Our Story</h2>
          <p class="text-gray-700 mb-4 leading-relaxed">
            Ceylon Craft was founded with a simple mission: to connect the world with the exceptional handmade products created by talented artisans in Sri Lanka. What started as a passion project has grown into a thriving community of makers and collectors.
          </p>
          <p class="text-gray-700 mb-4 leading-relaxed">
            Every product on our platform is handcrafted with love and expertise. We believe in the power of traditional craftsmanship and the importance of supporting local communities. By purchasing from Ceylon Craft, you're not just buying a beautiful product—you're supporting a family and preserving centuries-old traditions.
          </p>
          <p class="text-gray-700 leading-relaxed">
            We partner directly with artisans to ensure fair compensation and sustainable practices. Our commitment to ethical business means every purchase makes a real difference in the lives of our makers.
          </p>
        </div>
        <div class="rounded-2xl overflow-hidden shadow-lg">
          <img
            src="https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=600&h=600&fit=crop"
            alt="Artisan at work"
            class="w-full h-full object-cover"
          />
        </div>
      </div>

      <!-- Mission & Values -->
      <div class="mb-20">
        <h2 class="text-3xl md:text-4xl font-bold text-[#5A7184] mb-12 text-center">Our Mission & Values</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center p-8 rounded-2xl bg-[#F9F9F9] hover:bg-white transition border border-transparent hover:border-[#D1E8E2]">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-[#D1E8E2] rounded-full mb-6">
              <svg class="w-7 h-7 text-[#5A7184]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-[#5A7184] mb-3">Fair Trade</h3>
            <p class="text-gray-700">We ensure every artisan receives fair compensation for their work and expertise.</p>
          </div>

          <div class="text-center p-8 rounded-2xl bg-[#F9F9F9] hover:bg-white transition border border-transparent hover:border-[#D1E8E2]">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-[#D1E8E2] rounded-full mb-6">
              <svg class="w-7 h-7 text-[#5A7184]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-[#5A7184] mb-3">Sustainability</h3>
            <p class="text-gray-700">We promote eco-friendly practices and support artisans using sustainable materials.</p>
          </div>

          <div class="text-center p-8 rounded-2xl bg-[#F9F9F9] hover:bg-white transition border border-transparent hover:border-[#D1E8E2]">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-[#D1E8E2] rounded-full mb-6">
              <svg class="w-7 h-7 text-[#5A7184]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-[#5A7184] mb-3">Quality</h3>
            <p class="text-gray-700">Every product is carefully crafted with meticulous attention to detail and tradition.</p>
          </div>
        </div>
      </div>

      <!-- Team Section -->
      <div>
        <h2 class="text-3xl md:text-4xl font-bold text-[#5A7184] mb-12 text-center">Meet Our Artisans</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="artisan in artisans" :key="artisan.id" class="text-center">
            <div class="mb-4 rounded-2xl overflow-hidden">
              <img :src="artisan.image" :alt="artisan.name" class="w-full h-64 object-cover" />
            </div>
            <h3 class="text-lg font-semibold text-[#5A7184] mb-2">{{ artisan.name }}</h3>
            <p class="text-gray-600">{{ artisan.specialty }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const artisans = ref([
  {
    id: 1,
    name: 'Lakshmi Perera',
    specialty: 'Ceramic Artist',
    image: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=300&h=300&fit=crop'
  },
  {
    id: 2,
    name: 'Rajith Silva',
    specialty: 'Textile Weaver',
    image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop'
  },
  {
    id: 3,
    name: 'Amara Dissanayake',
    specialty: 'Leather Craftsman',
    image: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=300&h=300&fit=crop'
  }
]);
</script>
