<template>
  <div class="bg-[#EEF0F7] min-h-screen py-12">
    <!-- Page Header -->
    <section class="bg-white py-12 md:py-16 border-b-2 border-[#DFE2E9]">
      <div class="max-w-4xl mx-auto px-6 text-center">
        <h1 class="text-5xl font-bold text-[#363851] mb-4">Frequently Asked Questions</h1>
        <p class="text-xl text-[#657691]">Everything you need to know about Ceylon Craft and our handmade products</p>
      </div>
    </section>

    <div class="max-w-4xl mx-auto px-6 py-12">
      <!-- FAQ Categories Navigation -->
      <div class="mb-12 flex flex-wrap gap-3">
        <button
          v-for="cat in categories"
          :key="cat"
          @click="activeCategory = cat"
          :class="[
            'px-4 py-2 rounded-lg font-semibold transition-colors duration-200',
            activeCategory === cat
              ? 'bg-[#FB2B4A] text-white shadow-md'
              : 'bg-white text-[#657691] border-2 border-[#DFE2E9] hover:border-[#FB2B4A]'
          ]"
        >
          {{ cat }}
        </button>
      </div>

      <!-- FAQ Accordion -->
      <div class="space-y-4">
        <div
          v-for="(item, idx) in filteredFAQ"
          :key="idx"
          class="bg-white rounded-xl shadow-md overflow-hidden border-2 border-[#DFE2E9] transition-all duration-200 hover:shadow-lg"
        >
          <button
            @click="toggleFAQ(idx)"
            class="w-full px-6 py-4 flex items-center justify-between hover:bg-[#EEF0F7] transition-colors duration-200"
          >
            <h3 class="text-lg font-semibold text-[#363851] text-left">{{ item.question }}</h3>
            <svg
              :class="[
                'w-6 h-6 text-[#FB2B4A] transition-transform duration-300 flex-shrink-0',
                expandedFAQ[idx] ? 'transform rotate-180' : ''
              ]"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
          </button>

          <!-- Answer -->
          <transition
            enter-active-class="transition-all duration-300"
            leave-active-class="transition-all duration-300"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-96"
            leave-from-class="opacity-100 max-h-96"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-if="expandedFAQ[idx]" class="px-6 py-4 border-t-2 border-[#DFE2E9] bg-gradient-to-br from-white to-[#EEF0F7]">
              <p class="text-[#657691] leading-relaxed">{{ item.answer }}</p>
            </div>
          </transition>
        </div>
      </div>

      <!-- Contact CTA -->
      <div class="mt-16 bg-gradient-to-r from-[#FB2B4A] to-[#E91B3D] rounded-xl p-8 text-center shadow-lg">
        <h2 class="text-2xl font-bold text-white mb-3">Still have questions?</h2>
        <p class="text-white/90 mb-6">Our customer support team is ready to help you</p>
        <RouterLink
          to="/contact"
          class="inline-block px-8 py-3 bg-white text-[#FB2B4A] font-bold rounded-lg hover:bg-[#EEF0F7] transition-colors duration-200 shadow-md"
        >
          Contact Us
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { RouterLink } from 'vue-router';

const activeCategory = ref('All');
const expandedFAQ = ref({});

const categories = ['All', 'Products', 'Shipping & Returns', 'Orders', 'Account', 'Policies'];

const faqData = [
  {
    category: 'Products',
    question: 'Are all your products handmade?',
    answer: 'Yes, every product at Ceylon Craft is handcrafted by talented Sri Lankan artisans. We work directly with local craftspeople to preserve traditional techniques and support their livelihoods. Each piece is unique and made with care.'
  },
  {
    category: 'Products',
    question: 'What materials are used in your products?',
    answer: 'We use premium natural and sustainable materials including wood, ceramic, metal, textiles, and stone. We source locally when possible and prioritize eco-friendly, ethically-sourced materials. Each product listing specifies the exact materials used.'
  },
  {
    category: 'Products',
    question: 'Can I customize products or request a specific design?',
    answer: 'Absolutely! We love custom orders. Contact our team at support@ceyloncraft.com with your specifications, budget, and timeline. Our artisans can create bespoke pieces tailored to your needs. Custom orders typically take 2-4 weeks depending on complexity.'
  },
  {
    category: 'Products',
    question: 'How do you ensure product quality?',
    answer: 'Each piece is inspected by our quality team before shipping. We maintain high standards through direct relationships with artisans and regular quality audits. If you receive a damaged item, we offer a full replacement or refund.'
  },
  {
    category: 'Shipping & Returns',
    question: 'What are the shipping options within Sri Lanka?',
    answer: 'We offer two shipping methods: Standard (5-7 business days, free for orders over LKR 2,500) and Express (2-3 business days, LKR 350). Orders are packed securely and tracked. Free shipping applies nationwide for standard delivery on qualifying orders.'
  },
  {
    category: 'Shipping & Returns',
    question: 'Do you ship internationally?',
    answer: 'Yes! We ship worldwide. International shipping rates vary by destination. Please enter your address at checkout to see exact costs. Most international orders arrive within 2-3 weeks. We partner with reliable couriers and provide tracking for all shipments.'
  },
  {
    category: 'Shipping & Returns',
    question: 'What is your return and exchange policy?',
    answer: 'We offer a hassle-free 30-day return policy. If you\'re not satisfied, return the item in original condition within 30 days for a full refund or exchange. Return shipping within Sri Lanka is free. Please contact us at support@ceyloncraft.com to initiate a return.'
  },
  {
    category: 'Shipping & Returns',
    question: 'How do I track my order?',
    answer: 'After your order ships, you\'ll receive a tracking number via email. Use this number on the courier\'s website to track your package in real-time. You can also view tracking information in your account under "My Orders" on our website.'
  },
  {
    category: 'Orders',
    question: 'How long does it take to process my order?',
    answer: 'Orders are typically processed within 1-2 business days. During peak seasons (holidays), processing may take up to 3 business days. Rush processing is available for an additional fee. You\'ll receive a shipping confirmation email once your order is ready.'
  },
  {
    category: 'Orders',
    question: 'Can I modify or cancel my order?',
    answer: 'If your order hasn\'t shipped yet, we can modify or cancel it free of charge. Once shipped, you\'ll need to use our return process. Contact support@ceyloncraft.com immediately if you need to make changes to a recent order.'
  },
  {
    category: 'Account',
    question: 'Is my personal information secure?',
    answer: 'Yes, we use industry-standard SSL encryption to protect all transactions and personal data. Your information is never shared with third parties. We comply with international data protection standards. See our Privacy Policy for complete details.'
  },
  {
    category: 'Account',
    question: 'How do I save items to my wishlist?',
    answer: 'Click the heart icon on any product page to add it to your wishlist. You can view your wishlist anytime by clicking your profile icon and selecting "My Wishlist". Create an account to sync your wishlist across devices.'
  },
  {
    category: 'Policies',
    question: 'What payment methods do you accept?',
    answer: 'We accept all major credit cards (Visa, Mastercard, American Express), PayPal, and bank transfers. Payment processing is secure with PCI-DSS compliance. For bank transfers, our details are provided after checkout.'
  },
  {
    category: 'Policies',
    question: 'Do you offer bulk discounts for corporate orders?',
    answer: 'Yes! For orders of 20+ items, we offer special discounts. Contact our B2B team at wholesale@ceyloncraft.com with your requirements. We\'re happy to discuss custom pricing, unique packaging, and dedicated support for larger orders.'
  }
];

const filteredFAQ = computed(() => {
  if (activeCategory.value === 'All') {
    return faqData;
  }
  return faqData.filter(item => item.category === activeCategory.value);
});

const toggleFAQ = (idx) => {
  expandedFAQ.value[idx] = !expandedFAQ.value[idx];
};
</script>
