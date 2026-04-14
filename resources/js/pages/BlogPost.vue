<template>
  <div class="bg-[#EEF0F7] min-h-screen">
    <!-- Breadcrumb -->
    <nav class="bg-white border-b-2 border-[#DFE2E9]" aria-label="Breadcrumb">
      <div class="max-w-4xl mx-auto px-6 py-3">
        <ol class="flex items-center gap-2 text-sm">
          <li><RouterLink to="/" class="text-[#657691] hover:text-[#FB2B4A] transition">Home</RouterLink></li>
          <li class="text-[#DFE2E9]">/</li>
          <li><RouterLink to="/blog" class="text-[#657691] hover:text-[#FB2B4A] transition">Blog</RouterLink></li>
          <li class="text-[#DFE2E9]">/</li>
          <li class="text-[#FB2B4A] font-semibold">{{ post.title }}</li>
        </ol>
      </div>
    </nav>

    <!-- Article -->
    <article class="max-w-3xl mx-auto px-6 py-12">
      <!-- Article Header -->
      <header class="mb-12">
        <div class="mb-4">
          <span class="inline-block px-3 py-1 bg-[#FB2B4A] text-white text-xs font-bold uppercase tracking-wider rounded-full">{{ post.category }}</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-[#363851] mb-6 leading-tight">{{ post.title }}</h1>
        
        <!-- Article Meta -->
        <div class="flex flex-wrap items-center gap-4 pb-6 border-b-2 border-[#DFE2E9]">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-[#657691]" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
            </svg>
            <span class="text-[#657691]"><strong class="text-[#363851]">{{ post.author }}</strong></span>
          </div>
          <span class="text-[#A0ACC0]">•</span>
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-[#657691]" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v2H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v2H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            <time class="text-[#657691]">{{ post.date }}</time>
          </div>
          <span class="text-[#A0ACC0]">•</span>
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-[#657691]" fill="currentColor" viewBox="0 0 20 20">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
            </svg>
            <span class="text-[#657691]">{{ post.readTime }} min read</span>
          </div>
        </div>
      </header>

      <!-- Featured Image -->
      <figure class="mb-12">
        <img 
          :src="post.image" 
          :alt="post.title + ' - handmade crafts article image'" 
          class="w-full h-96 object-cover rounded-xl border-2 border-[#DFE2E9]"
        />
        <figcaption class="text-center text-[#657691] text-sm mt-3">Featured image showing handmade craft products and artisan work</figcaption>
      </figure>

      <!-- Article Content -->
      <div class="prose prose-lg max-w-none">
        <!-- Intro -->
        <p class="text-lg text-[#657691] mb-8 leading-relaxed first-letter:text-3xl first-letter:font-bold first-letter:text-[#FB2B4A]">
          {{ post.excerpt }}
        </p>

        <!-- Section 1 -->
        <h2 class="text-3xl font-bold text-[#363851] mt-12 mb-6 pt-8 border-t-2 border-[#DFE2E9]">Understanding Handmade Craftsmanship</h2>
        <p class="text-[#657691] mb-6 leading-relaxed">
          Handmade products represent more than just items; they embody the passion, expertise, and cultural heritage of their creators. Each piece tells a unique story of dedication and artistry that mass production could never replicate.
        </p>

        <!-- Subsection -->
        <h3 class="text-2xl font-bold text-[#363851] mt-8 mb-4">The Art of Traditional Techniques</h3>
        <p class="text-[#657691] mb-6 leading-relaxed">
          Traditional techniques passed down through generations continue to thrive in the hands of skilled artisans. These time-tested methods ensure quality, authenticity, and a deep connection to cultural roots that defines Ceylon Craft's commitment to excellence.
        </p>

        <!-- Blockquote -->
        <blockquote class="border-l-4 border-[#FB2B4A] pl-6 py-4 my-8 bg-[#EEF0F7] rounded-lg text-[#363851] font-semibold italic">
          "Handmade products are not just purchases—they are investments in culture, community, and sustainability."
        </blockquote>

        <!-- Subsection -->
        <h3 class="text-2xl font-bold text-[#363851] mt-8 mb-4">Supporting Local Communities</h3>
        <p class="text-[#657691] mb-6 leading-relaxed">
          By choosing handmade products from Ceylon Craft, you directly support local artisans and their families. Your purchase helps preserve traditional crafts and enables artisans to continue their work with pride and purpose, creating positive social impact.
        </p>

        <!-- List with Icons -->
        <h3 class="text-2xl font-bold text-[#363851] mt-8 mb-4">Key Benefits of Handmade Products</h3>
        <ul class="space-y-3 mb-8">
          <li class="flex gap-3 text-[#657691]">
            <span class="text-[#FB2B4A] font-bold text-lg">✓</span>
            <span><strong class="text-[#363851]">Unique & One-of-a-Kind:</strong> Each piece is distinct, ensuring you own something truly special</span>
          </li>
          <li class="flex gap-3 text-[#657691]">
            <span class="text-[#FB2B4A] font-bold text-lg">✓</span>
            <span><strong class="text-[#363851]">Superior Quality:</strong> Handcrafted with expertise and attention to detail that surpasses mass production</span>
          </li>
          <li class="flex gap-3 text-[#657691]">
            <span class="text-[#FB2B4A] font-bold text-lg">✓</span>
            <span><strong class="text-[#363851]">Fair Wages:</strong> Supporting fair wages and sustainable practices for artisans</span>
          </li>
          <li class="flex gap-3 text-[#657691]">
            <span class="text-[#FB2B4A] font-bold text-lg">✓</span>
            <span><strong class="text-[#363851]">Environmental Benefits:</strong> Eco-friendly production compared to industrial manufacturing</span>
          </li>
          <li class="flex gap-3 text-[#657691]">
            <span class="text-[#FB2B4A] font-bold text-lg">✓</span>
            <span><strong class="text-[#363851]">Cultural Preservation:</strong> Preserving heritage and traditional techniques for future generations</span>
          </li>
        </ul>

        <!-- Conclusion -->
        <h2 class="text-3xl font-bold text-[#363851] mt-12 mb-6 pt-8 border-t-2 border-[#DFE2E9]">Conclusion</h2>
        <p class="text-[#657691] leading-relaxed">
          Choosing handmade is choosing quality, ethics, and culture. It's a conscious decision to support artisans and contribute to a more sustainable world. At Ceylon Craft, we believe in the power of handmade products to transform lives and spaces. Every purchase helps create opportunity, preserve tradition, and celebrate human creativity.
        </p>
      </div>

      <!-- CTA -->
      <div class="mt-12 p-8 bg-gradient-to-r from-[#FB2B4A] to-[#E91B3D] rounded-xl text-center text-white">
        <h3 class="text-2xl font-bold mb-3">Ready to Discover Handmade?</h3>
        <p class="mb-6">Explore our collection of authentic handcrafted products</p>
        <RouterLink 
          to="/products" 
          class="inline-block px-8 py-3 bg-white text-[#FB2B4A] font-bold rounded-lg hover:bg-[#EEF0F7] transition-colors"
        >
          Shop Now
        </RouterLink>
      </div>
    </article>

    <!-- Author Info -->
    <section class="max-w-3xl mx-auto px-6 py-12 mt-12">
      <div class="bg-white rounded-xl p-8 border-2 border-[#DFE2E9]">
        <h3 class="text-sm font-bold text-[#FB2B4A] uppercase tracking-wider mb-3">About the Author</h3>
        <h2 class="text-2xl font-bold text-[#363851] mb-3">{{ post.author }}</h2>
        <p class="text-[#657691] leading-relaxed">
          {{ post.authorBio }}
        </p>
      </div>
    </section>

    <!-- Related Posts -->
    <section class="bg-white border-t-2 border-[#DFE2E9] py-12 mt-12">
      <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-[#363851] mb-8">Read More Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <router-link
            v-for="relatedPost in relatedPosts"
            :key="relatedPost.id"
            :to="`/blog/${relatedPost.slug}`"
            class="group bg-[#EEF0F7] rounded-xl overflow-hidden border-2 border-[#DFE2E9] hover:border-[#FB2B4A] transition-all duration-300 hover:shadow-lg"
          >
            <div class="h-48 overflow-hidden bg-[#DFE2E9]">
              <img
                :src="relatedPost.image"
                :alt="relatedPost.title + ' - Ceylon Craft blog'"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
            </div>
            <div class="p-6">
              <h3 class="font-bold text-[#363851] group-hover:text-[#FB2B4A] transition line-clamp-2 mb-2">
                {{ relatedPost.title }}
              </h3>
              <p class="text-sm text-[#657691]">{{ relatedPost.date }}</p>
            </div>
          </router-link>
        </div>
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
  readTime: 8,
  image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=1200&h=600&fit=crop',
  excerpt: 'Quality matters. When you invest in a handmade product, you\'re not just buying an item—you\'re acquiring a masterpiece crafted with care, expertise, and passion. Discover why handmade products stand out as superior investments in a world of mass production.',
  authorBio: 'Priya Kumari is a passionate advocate for handmade crafts, sustainable living, and artisan entrepreneurship. With over 10 years of experience in the craft industry, she regularly contributes insights about the intersection of tradition and modern commerce to Ceylon Craft.'
});

const relatedPosts = ref([
  {
    id: 1,
    title: 'How Handmade Products Are Made: Behind the Scenes',
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
