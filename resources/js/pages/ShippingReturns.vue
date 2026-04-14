<template>
  <div class="bg-[#EEF0F7] min-h-screen py-12">
    <!-- Page Header -->
    <section class="bg-white py-12 md:py-16 border-b-2 border-[#DFE2E9]">
      <div class="max-w-4xl mx-auto px-6">
        <h1 class="text-5xl font-bold text-[#363851] mb-2">Shipping & Returns</h1>
        <p class="text-xl text-[#657691]">Fast, reliable delivery across Sri Lanka and worldwide</p>
      </div>
    </section>

    <div class="max-w-4xl mx-auto px-6 py-12">
      <!-- Shipping Section -->
      <section class="mb-12">
        <h2 class="text-3xl font-bold text-[#363851] mb-6 flex items-center gap-3">
          <svg class="w-8 h-8 text-[#FB2B4A]" fill="currentColor" viewBox="0 0 24 24">
            <path d="M13 6v2h-2V6h2zm0 10h-2v2h2v-2zm6-7h1V7h-1V4H1v14h8v3h6v-3h1v-3h-1v2h-2v-5h2v2h1V9zm-11 9H2V5h16v8h-4v-1h2V6H4v12h4v1z"/>
          </svg>
          Shipping Information
        </h2>

        <!-- Domestic Shipping -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
          <div class="bg-white rounded-xl p-6 border-2 border-[#DFE2E9] hover:shadow-lg transition-shadow">
            <h3 class="text-xl font-bold text-[#363851] mb-4 flex items-center gap-2">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#FB2B4A] text-white font-bold">⚡</span>
              Express Delivery
            </h3>
            <div class="space-y-3 text-[#657691]">
              <p><span class="font-semibold text-[#363851]">Coverage:</span> All of Sri Lanka (Colombo metro, Western Province, and nationwide)</p>
              <p><span class="font-semibold text-[#363851]">Delivery Time:</span> 2-3 business days</p>
              <p><span class="font-semibold text-[#363851]">Cost:</span> LKR 350 / USD 1.20</p>
              <p><span class="font-semibold text-[#363851]">Courier:</span> DHL, FedEx, Local couriers</p>
              <ul class="list-disc list-inside text-sm mt-2 space-y-1">
                <li>Real-time tracking available</li>
                <li>Signature on delivery</li>
                <li>Insurance included</li>
              </ul>
            </div>
          </div>

          <div class="bg-white rounded-xl p-6 border-2 border-[#DFE2E9] hover:shadow-lg transition-shadow">
            <h3 class="text-xl font-bold text-[#363851] mb-4 flex items-center gap-2">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#657691] text-white font-bold">✓</span>
              Standard Delivery
            </h3>
            <div class="space-y-3 text-[#657691]">
              <p><span class="font-semibold text-[#363851]">Coverage:</span> All of Sri Lanka</p>
              <p><span class="font-semibold text-[#363851]">Delivery Time:</span> 5-7 business days</p>
              <p><span class="font-semibold text-[#363851]">Cost:</span> FREE on orders over LKR 2,500</p>
              <p><span class="font-semibold text-[#363851]">Courier:</span> Sri Lanka Post, JCE</p>
              <ul class="list-disc list-inside text-sm mt-2 space-y-1">
                <li>Tracking number provided</li>
                <li>Perfect for non-urgent items</li>
                <li>Eco-friendly option</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Specific Regions -->
        <div class="bg-[#EEF0F7] rounded-xl p-6 border-2 border-[#DFE2E9]">
          <h3 class="text-lg font-bold text-[#363851] mb-4">Delivery by District (Sri Lanka)</h3>
          <div class="grid md:grid-cols-3 gap-4 text-sm text-[#657691]">
            <div><span class="font-semibold text-[#363851]">Western Province:</span> Colombo, Gampaha, Kalutara - Same day to next day</div>
            <div><span class="font-semibold text-[#363851]">Southern Province:</span> Galle, Matara, Hambantota - 2-3 days</div>
            <div><span class="font-semibold text-[#363851]">Central Province:</span> Kandy, Matale, Nuwara Eliya - 2-3 days</div>
            <div><span class="font-semibold text-[#363851]">Northern Province:</span> Jaffna, Mullaitivu, Vavuniya - 4-5 days</div>
            <div><span class="font-semibold text-[#363851]">Eastern Province:</span> Batticaloa, Trincomalee, Ampara - 3-4 days</div>
            <div><span class="font-semibold text-[#363851]">Uva & Sabaragamuwa:</span> Badulla, Ratnapura - 3-4 days</div>
          </div>
        </div>
      </section>

      <!-- International Shipping -->
      <section class="mb-12">
        <h2 class="text-3xl font-bold text-[#363851] mb-6 flex items-center gap-3">
          <svg class="w-8 h-8 text-[#FB2B4A]" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
          International Shipping
        </h2>

        <div class="bg-white rounded-xl p-8 border-2 border-[#DFE2E9]">
          <div class="space-y-4 text-[#657691]">
            <p><span class="font-semibold text-[#363851]">Available to:</span> 190+ countries worldwide</p>
            <p><span class="font-semibold text-[#363851]">Average Delivery Time:</span> 10-21 business days (depending on destination)</p>
            <p><span class="font-semibold text-[#363851]">How to calculate shipping:</span> Enter your address at checkout to see real-time shipping costs</p>
            <p><span class="font-semibold text-[#363851]">Carriers:</span> DHL International, FedEx International, UPS</p>
            
            <div class="mt-6 p-4 bg-[#EEF0F7] rounded-lg border-l-4 border-[#FB2B4A]">
              <p class="font-semibold text-[#363851] mb-2">Popular Destinations:</p>
              <div class="grid md:grid-cols-3 gap-2 text-sm">
                <p>🇺🇸 United States - 12-15 days</p>
                <p>🇬🇧 United Kingdom - 10-14 days</p>
                <p>🇦🇺 Australia - 14-21 days</p>
                <p>🇨🇦 Canada - 12-18 days</p>
                <p>🇩🇪 Europe - 10-16 days</p>
                <p>🇯🇵 Asia Pacific - 7-14 days</p>
              </div>
            </div>

            <p class="mt-4"><span class="font-semibold text-[#363851]">Customs & Duties:</span> International orders may be subject to customs duties and local taxes. You are responsible for any applicable fees upon delivery.</p>

            <p><span class="font-semibold text-[#363851]">Tracking:</span> All international shipments include full tracking and insurance.</p>
          </div>
        </div>
      </section>

      <!-- Returns Section -->
      <section class="mb-12">
        <h2 class="text-3xl font-bold text-[#363851] mb-6 flex items-center gap-3">
          <svg class="w-8 h-8 text-[#FB2B4A]" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20 5H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-4h2c1.66 0 3-1.34 3-3V7c0-1.66-1.34-3-3-3zm-3 6h-4V5h4v6z"/>
          </svg>
          Return Policy
        </h2>

        <!-- Return Window -->
        <div class="bg-gradient-to-r from-[#FB2B4A] to-[#E91B3D] rounded-xl p-8 text-white mb-8">
          <h3 class="text-2xl font-bold mb-3">30-Day Money-Back Guarantee</h3>
          <p class="text-white/90">If you're not completely satisfied, return your item within 30 days of delivery for a full refund or exchange.</p>
        </div>

        <!-- Return Process -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl p-6 border-2 border-[#DFE2E9] text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#FB2B4A] text-white font-bold mb-4">1</div>
            <h4 class="font-bold text-[#363851] mb-2">Contact Us</h4>
            <p class="text-[#657691] text-sm">Email support@ceyloncraft.com with your order number</p>
          </div>
          <div class="bg-white rounded-xl p-6 border-2 border-[#DFE2E9] text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#FB2B4A] text-white font-bold mb-4">2</div>
            <h4 class="font-bold text-[#363851] mb-2">Ship Back</h4>
            <p class="text-[#657691] text-sm">Get your return label (free for SL orders)</p>
          </div>
          <div class="bg-white rounded-xl p-6 border-2 border-[#DFE2E9] text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#FB2B4A] text-white font-bold mb-4">3</div>
            <h4 class="font-bold text-[#363851] mb-2">We Inspect</h4>
            <p class="text-[#657691] text-sm">Verify item condition within 3-5 days</p>
          </div>
          <div class="bg-white rounded-xl p-6 border-2 border-[#DFE2E9] text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#FB2B4A] text-white font-bold mb-4">4</div>
            <h4 class="font-bold text-[#363851] mb-2">Refund Issued</h4>
            <p class="text-[#657691] text-sm">Full refund processed in 7-10 days</p>
          </div>
        </div>

        <!-- Return Conditions -->
        <div class="bg-white rounded-xl p-8 border-2 border-[#DFE2E9]">
          <h3 class="text-xl font-bold text-[#363851] mb-4">Return Conditions</h3>
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <h4 class="font-semibold text-[#363851] mb-3 flex items-center gap-2">
                <span class="text-[#FB2B4A]">✓</span> Returnable Items
              </h4>
              <ul class="space-y-2 text-[#657691] text-sm">
                <li>✓ Unused items in original condition</li>
                <li>✓ Within 30 days of delivery</li>
                <li>✓ With original packaging</li>
                <li>✓ Defective or damaged items</li>
              </ul>
            </div>
            <div>
              <h4 class="font-semibold text-[#363851] mb-3 flex items-center gap-2">
                <span class="text-red-500">✗</span> Non-Returnable
              </h4>
              <ul class="space-y-2 text-[#657691] text-sm">
                <li>✗ Custom-made items</li>
                <li>✗ Clearance or final sale items</li>
                <li>✗ Items showing signs of use</li>
                <li>✗ Gift cards or vouchers</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- Damaged Items -->
      <section class="mb-12">
        <h2 class="text-3xl font-bold text-[#363851] mb-6">Received Damaged or Wrong Item?</h2>
        <div class="bg-white rounded-xl p-8 border-2 border-[#DFE2E9]">
          <div class="space-y-4 text-[#657691]">
            <p>
              <span class="font-semibold text-[#363851]">Report Immediately:</span> Contact us within 48 hours of delivery if your item arrived damaged or if you received the wrong product.
            </p>
            <p>
              <span class="font-semibold text-[#363851]">Provide Evidence:</span> Send photos of the damaged item or packaging from multiple angles.
            </p>
            <p>
              <span class="font-semibold text-[#363851]">Resolution:</span> We'll send a replacement immediately or process a full refund at no additional cost.
            </p>
            <p>
              <span class="font-semibold text-[#363851]">Contact:</span> Email support@ceyloncraft.com with photos and your order number.
            </p>
          </div>
        </div>
      </section>

      <!-- Contact -->
      <section class="mb-12 bg-[#EEF0F7] rounded-xl p-8 border-2 border-[#DFE2E9]">
        <h2 class="text-2xl font-bold text-[#363851] mb-4">Need Help?</h2>
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <p class="text-[#657691] mb-3">📧 Email us:</p>
            <p class="text-[#363851] font-semibold">support@ceyloncraft.com</p>
          </div>
          <div>
            <p class="text-[#657691] mb-3">☎️ Call us:</p>
            <p class="text-[#363851] font-semibold">+94 (0) 11 234 5678</p>
          </div>
          <div>
            <p class="text-[#657691] mb-3">⏰ Business Hours:</p>
            <p class="text-[#363851] font-semibold">Monday - Friday: 9 AM - 6 PM (GMT+5:30)</p>
          </div>
          <div>
            <p class="text-[#657691] mb-3">📍 Location:</p>
            <p class="text-[#363851] font-semibold">Colombo, Sri Lanka</p>
          </div>
        </div>
      </section>

      <!-- Footer CTA -->
      <div class="text-center">
        <RouterLink
          to="/contact"
          class="inline-block px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-colors duration-200 shadow-md"
        >
          Contact Our Support Team
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router';
</script>
