<template>
  <div class="w-full">
    <!-- Breadcrumb -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex items-center gap-2 text-sm text-gray-600">
        <RouterLink to="/" class="hover:text-[#5A7184]">Home</RouterLink>
        <span>/</span>
        <RouterLink to="/blog" class="hover:text-[#5A7184]">Blog</RouterLink>
        <span>/</span>
        <span class="text-[#5A7184] font-semibold">{{ post.title }}</span>
      </div>
    </div>

    <!-- Article Header -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <h1 class="text-4xl md:text-5xl font-bold text-[#5A7184] mb-6">{{ post.title }}</h1>
      <div class="flex flex-wrap items-center gap-6 text-gray-600 pb-8 border-b border-gray-200">
        <span class="text-sm font-semibold text-[#D1E8E2] uppercase tracking-wider">{{ post.category }}</span>
        <span>By <strong>{{ post.author }}</strong></span>
        <span>{{ post.date }}</span>
        <span>{{ post.readTime }} min read</span>
      </div>
    </section>

    <!-- Featured Image -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="rounded-2xl overflow-hidden shadow-lg">
        <img :src="post.image" :alt="post.title" class="w-full h-96 object-cover" />
      </div>
    </section>

    <!-- Article Content -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="prose prose-lg max-w-none">
        <p class="text-lg text-gray-700 mb-6 leading-relaxed">{{ post.excerpt }}</p>

        <h2 class="text-2xl font-bold text-[#5A7184] mt-12 mb-6">Understanding Handmade Craftsmanship</h2>
        <p class="text-gray-700 mb-6 leading-relaxed">
          Handmade products represent more than just items; they embody the passion, expertise, and cultural heritage of their creators. Each piece tells a unique story of dedication and artistry.
        </p>

        <h3 class="text-xl font-bold text-[#5A7184] mt-8 mb-4">The Art of Traditional Techniques</h3>
        <p class="text-gray-700 mb-6 leading-relaxed">
          Traditional techniques passed down through generations continue to thrive in the hands of skilled artisans. These methods ensure quality, authenticity, and a deep connection to cultural roots.
        </p>

        <blockquote class="border-l-4 border-[#D1E8E2] pl-6 py-4 my-8 bg-[#F9F9F9] rounded">
          "Handmade products are not just purchases—they are investments in culture, community, and sustainability."
        </blockquote>

        <h3 class="text-xl font-bold text-[#5A7184] mt-8 mb-4">Supporting Local Communities</h3>
        <p class="text-gray-700 mb-6 leading-relaxed">
          By choosing handmade products from Ceylon Craft, you directly support local artisans and their families. Your purchase helps preserve traditional crafts and enables artisans to continue their work with pride and purpose.
        </p>

        <h3 class="text-xl font-bold text-[#5A7184] mt-8 mb-4">Key Benefits</h3>
        <ul class="list-disc pl-6 space-y-3 mb-6">
          <li class="text-gray-700">Unique, one-of-a-kind pieces you won't find elsewhere</li>
          <li class="text-gray-700">Superior quality and superior craftsmanship</li>
          <li class="text-gray-700">Supporting fair wages and sustainable practices</li>
          <li class="text-gray-700">Environmental benefits over mass production</li>
          <li class="text-gray-700">Preserving cultural heritage and traditions</li>
        </ul>

        <h2 class="text-2xl font-bold text-[#5A7184] mt-12 mb-6">Conclusion</h2>
        <p class="text-gray-700 leading-relaxed">
          Choosing handmade is choosing quality, ethics, and culture. It's a conscious decision to support artisans and contribute to a more sustainable world. At Ceylon Craft, we believe in the power of handmade products to transform lives and spaces.
        </p>
      </div>
    </section>

    <!-- Author Info -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 border-t border-gray-200">
      <div class="bg-[#F9F9F9] p-8 rounded-2xl">
        <p class="text-sm font-semibold text-[#D1E8E2] uppercase tracking-wider mb-3">About the Author</p>
        <h3 class="text-lg font-bold text-[#5A7184] mb-3">{{ post.author }}</h3>
        <p class="text-gray-700">
          {{ post.authorBio }}
        </p>
      </div>
    </section>

    <!-- Related Posts -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 mt-12 border-t border-gray-200">
      <h2 class="text-3xl font-bold text-[#5A7184] mb-8">Related Articles</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <RouterLink
          v-for="relatedPost in relatedPosts"
          :key="relatedPost.id"
          :to="`/blog/${relatedPost.slug}`"
          class="rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition bg-white group cursor-pointer"
        >
          <div class="h-48 overflow-hidden bg-gray-200">
            <img
              :src="relatedPost.image"
              :alt="relatedPost.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />
          </div>
          <div class="p-6">
            <h3 class="font-semibold text-[#5A7184] group-hover:text-[#4a5f70] transition line-clamp-2">
              {{ relatedPost.title }}
            </h3>
            <p class="text-sm text-gray-600 mt-2">{{ relatedPost.date }}</p>
          </div>
        </RouterLink>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

const post = ref({
  title: 'Why Handmade Products Are Better',
  slug: 'why-handmade-products-are-better',
  category: 'Craftsmanship',
  author: 'Priya Kumari',
  date: 'March 10, 2026',
  readTime: 5,
  image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=1200&h=600&fit=crop',
  excerpt: 'Quality matters. When you invest in a handmade product, you\'re not just buying an item—you\'re acquiring a piece of art crafted with care, expertise, and passion. Learn why handmade products stand out in a world of mass production.',
  authorBio: 'Priya Kumari is a passionate advocate for handmade crafts and sustainable living. She regularly writes about the intersection of tradition and modern commerce.'
});

const relatedPosts = ref([
  {
    id: 1,
    title: 'How Crafts Are Made: Behind the Scenes',
    slug: 'how-crafts-are-made',
    image: 'https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=400&h=300&fit=crop',
    date: 'March 5, 2026'
  },
  {
    id: 2,
    title: 'Sustainable Crafting: Eco-Friendly Materials',
    slug: 'sustainable-crafting',
    image: 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=400&h=300&fit=crop',
    date: 'February 25, 2026'
  },
  {
    id: 3,
    title: 'Supporting Local: The Impact of Fair Trade',
    slug: 'supporting-local-fair-trade',
    image: 'https://images.unsplash.com/photo-1525909002651-b8f576fd611d?w=400&h=300&fit=crop',
    date: 'March 1, 2026'
  }
]);
</script>
