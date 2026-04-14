<template>
  <div class="bg-[#EEF0F7] min-h-screen py-12">
    <!-- Page Header -->
    <section class="bg-white py-12 md:py-16 border-b-2 border-[#DFE2E9]">
      <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-5xl font-bold text-[#363851] mb-2">Checkout</h1>
        <p class="text-xl text-[#657691]">Complete your purchase securely</p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Step Indicator -->
          <div class="flex gap-4 mb-8">
            <div v-for="(step, idx) in steps" :key="idx" class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <div :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center font-bold text-white transition',
                  currentStep >= idx ? 'bg-[#FB2B4A]' : 'bg-[#A0ACC0]'
                ]">
                  {{ idx + 1 }}
                </div>
                <p :class="['font-semibold transition', currentStep >= idx ? 'text-[#363851]' : 'text-[#A0ACC0]']">{{ step }}</p>
              </div>
            </div>
          </div>

          <!-- Shipping Information -->
          <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-[#363851] mb-6">Shipping Information</h2>
            <form class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">First Name</label>
                  <input
                    v-model="shippingForm.firstName"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">Last Name</label>
                  <input
                    v-model="shippingForm.lastName"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-[#363851] mb-2">Email Address</label>
                <input
                  v-model="shippingForm.email"
                  type="email"
                  required
                  class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-[#363851] mb-2">Phone Number</label>
                <input
                  v-model="shippingForm.phone"
                  type="tel"
                  required
                  class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-[#363851] mb-2">Street Address</label>
                <input
                  v-model="shippingForm.address"
                  type="text"
                  required
                  class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">City</label>
                  <input
                    v-model="shippingForm.city"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">State</label>
                  <input
                    v-model="shippingForm.state"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">ZIP Code</label>
                  <input
                    v-model="shippingForm.zip"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-[#363851] mb-2">Country</label>
                <select
                  v-model="shippingForm.country"
                  required
                  class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                >
                  <option>Select a country</option>
                  <option>United States</option>
                  <option>United Kingdom</option>
                  <option>Canada</option>
                  <option>Australia</option>
                </select>
              </div>
            </form>
          </div>

          <!-- Shipping Method -->
          <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-[#363851] mb-6">Shipping Method</h2>
            <div class="space-y-4">
              <label class="flex items-center p-4 border-2 border-[#FB2B4A] rounded-lg cursor-pointer bg-[#FEF5F6] transition-all">
                <input
                  type="radio"
                  v-model="shippingMethod"
                  value="express"
                  class="w-5 h-5 accent-[#FB2B4A] cursor-pointer"
                />
                <div class="ml-4 flex-1">
                  <p class="font-semibold text-[#363851]">Express Shipping</p>
                  <p class="text-sm text-[#657691]">Arrive in 2-3 business days</p>
                </div>
                <span class="font-bold text-[#FB2B4A]">$10.00</span>
              </label>
              <label class="flex items-center p-4 border-2 border-[#DFE2E9] rounded-lg cursor-pointer hover:border-[#FB2B4A] transition">
                <input
                  type="radio"
                  v-model="shippingMethod"
                  value="standard"
                  class="w-5 h-5 accent-[#FB2B4A] cursor-pointer"
                />
                <div class="ml-4 flex-1">
                  <p class="font-semibold text-[#363851]">Standard Shipping</p>
                  <p class="text-sm text-[#657691]">Arrive in 5-7 business days</p>
                </div>
                <span class="font-semibold text-green-600">FREE</span>
              </label>
            </div>
          </div>

          <!-- Payment Section -->
          <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-[#363851] mb-6">Payment Method</h2>
            <div class="space-y-4">
              <label class="flex items-center p-4 border-2 border-[#FB2B4A] rounded-lg cursor-pointer bg-[#FEF5F6]">
                <input
                  type="radio"
                  v-model="paymentMethod"
                  value="card"
                  class="w-5 h-5 accent-[#FB2B4A] cursor-pointer"
                />
                <div class="ml-4 flex items-center gap-3">
                  <svg class="w-6 h-6 text-[#FB2B4A]" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                  </svg>
                  <span class="font-semibold text-[#363851]">Credit / Debit Card</span>
                </div>
              </label>
              <label class="flex items-center p-4 border-2 border-[#DFE2E9] rounded-lg cursor-pointer hover:border-[#FB2B4A] transition">
                <input
                  type="radio"
                  v-model="paymentMethod"
                  value="paypal"
                  class="w-5 h-5 accent-[#FB2B4A] cursor-pointer"
                />
                <div class="ml-4">
                  <span class="font-semibold text-[#363851]">PayPal</span>
                </div>
              </label>
            </div>

            <!-- Card Details (if card selected) -->
            <div v-if="paymentMethod === 'card'" class="mt-8 space-y-6 pt-8 border-t-2 border-[#DFE2E9]">
              <div>
                <label class="block text-sm font-semibold text-[#363851] mb-2">Cardholder Name</label>
                <input
                  type="text"
                  class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  placeholder="John Doe"
                />
              </div>
              <div>
                <label class="block text-sm font-semibold text-[#363851] mb-2">Card Number</label>
                <input
                  type="text"
                  placeholder="•••• •••• •••• ••••"
                  class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                />
              </div>
              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">Expiry Date</label>
                  <input
                    type="text"
                    placeholder="MM/YY"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">CVC</label>
                  <input
                    type="text"
                    placeholder="•••"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-4">
            <RouterLink to="/cart" class="flex-1 px-6 py-3 border-2 border-[#DFE2E9] text-[#657691] hover:border-[#FB2B4A] hover:text-[#FB2B4A] font-bold rounded-lg transition text-center">
              Back to Cart
            </RouterLink>
            <button
              @click="proceedToPayment"
              class="flex-1 px-6 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
            >
              Complete Purchase
            </button>
          </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-md p-8 sticky top-24 space-y-6">
            <h2 class="text-2xl font-bold text-[#363851]">Order Summary</h2>

            <div class="space-y-4 pb-6 border-b-2 border-[#DFE2E9]">
              <div class="flex justify-between text-[#657691]">
                <span>Subtotal</span>
                <span>$143.49</span>
              </div>
              <div class="flex justify-between text-[#657691]">
                <span>Shipping</span>
                <span v-if="shippingMethod === 'express'" class="text-right">$10.00</span>
                <span v-else class="text-green-600 font-semibold">FREE</span>
              </div>
              <div class="flex justify-between text-[#657691]">
                <span>Tax (10%)</span>
                <span>$15.35</span>
              </div>
            </div>

            <div class="flex justify-between text-2xl font-bold text-[#363851]">
              <span>Total</span>
              <span v-if="shippingMethod === 'express'">$168.84</span>
              <span v-else>$158.84</span>
            </div>

            <!-- SECURITY INFO -->
            <div class="bg-[#EEF0F7] p-4 rounded-lg space-y-2 text-sm">
              <div class="flex items-center gap-2 text-[#657691]">
                <svg class="w-4 h-4 text-[#FB2B4A]" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
                </svg>
                Secure checkout with SSL
              </div>
              <div class="flex items-center gap-2 text-[#657691]">
                <svg class="w-4 h-4 text-[#FB2B4A]" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.72a1.066 1.066 0 01-1.065 1.066c-1.297 0-2.56.425-3.6 1.128a6.147 6.147 0 01-.996.58 1.065 1.065 0 01-.992 0 6.147 6.147 0 01-.996-.58c-1.04-.703-2.303-1.128-3.6-1.128a1.066 1.066 0 01-1.065-1.066V6.517a3.066 3.066 0 012.812-3.062z"/>
                </svg>
                30-day money-back guarantee
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { useToast } from '@/composables/useToast';

const router = useRouter();
const { success, error } = useToast();

const currentStep = ref(0);
const steps = ['Shipping', 'Payment', 'Confirmation'];

const shippingForm = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  zip: '',
  country: ''
});

const shippingMethod = ref('standard');
const paymentMethod = ref('card');

const proceedToPayment = () => {
  if (!shippingForm.value.firstName || !shippingForm.value.email) {
    error('Please fill in all required fields');
    return;
  }
  success('Order placed successfully! Redirecting...');
  setTimeout(() => {
    router.push('/orders');
  }, 2000);
};
</script>

            <div class="flex justify-between text-xl font-bold text-[#5A7184] mb-6">
              <span>Total</span>
              <span v-if="shippingMethod === 'express'">$167.84</span>
              <span v-else>$157.84</span>
            </div>

            <!-- Order Items Preview -->
            <div class="space-y-3 mb-6">
              <h3 class="font-semibold text-[#5A7184] text-sm">Items (3)</h3>
              <div class="space-y-2">
                <div class="flex justify-between text-sm text-gray-700">
                  <span>Ceramic Vase x1</span>
                  <span>$45.99</span>
                </div>
                <div class="flex justify-between text-sm text-gray-700">
                  <span>Wooden Box x2</span>
                  <span>$71.00</span>
                </div>
                <div class="flex justify-between text-sm text-gray-700">
                  <span>Wall Tapestry x1</span>
                  <span>$62.00</span>
                </div>
              </div>
            </div>

            <button class="w-full text-center px-4 py-2 bg-white border-2 border-[#5A7184] text-[#5A7184] rounded-lg font-semibold hover:bg-[#F9F9F9] transition text-sm">
              Edit Cart
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

const shippingMethod = ref('standard');

const proceedToPayment = () => {
  alert('Payment step coming soon! (UI Demo)');
};
</script>
