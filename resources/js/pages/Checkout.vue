<template>
  <div class="w-full">
    <!-- Page Header -->
    <section class="bg-gradient-to-b from-[#F9F9F9] to-white py-12 md:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold text-[#5A7184] mb-4">Checkout</h1>
        <p class="text-gray-600">Complete your purchase</p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Step Indicator -->
          <div class="flex gap-4 mb-8">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-[#5A7184] text-white rounded-full flex items-center justify-center font-bold text-sm">1</div>
                <p class="font-semibold text-[#5A7184]">Shipping</p>
              </div>
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold text-sm">2</div>
                <p class="font-semibold text-gray-600">Payment</p>
              </div>
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold text-sm">3</div>
                <p class="font-semibold text-gray-600">Confirmation</p>
              </div>
            </div>
          </div>

          <!-- Shipping Information -->
          <div class="bg-white p-8 rounded-2xl border border-gray-100">
            <h2 class="text-2xl font-bold text-[#5A7184] mb-6">Shipping Information</h2>
            <form class="space-y-5">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                  <label for="firstName" class="block text-sm font-semibold text-[#5A7184] mb-2">First Name</label>
                  <input
                    id="firstName"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
                    placeholder="John"
                  />
                </div>
                <div>
                  <label for="lastName" class="block text-sm font-semibold text-[#5A7184] mb-2">Last Name</label>
                  <input
                    id="lastName"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
                    placeholder="Doe"
                  />
                </div>
              </div>

              <div>
                <label for="email" class="block text-sm font-semibold text-[#5A7184] mb-2">Email Address</label>
                <input
                  id="email"
                  type="email"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
                  placeholder="john@example.com"
                />
              </div>

              <div>
                <label for="phone" class="block text-sm font-semibold text-[#5A7184] mb-2">Phone Number</label>
                <input
                  id="phone"
                  type="tel"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
                  placeholder="+1 (555) 000-0000"
                />
              </div>

              <div>
                <label for="address" class="block text-sm font-semibold text-[#5A7184] mb-2">Street Address</label>
                <input
                  id="address"
                  type="text"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
                  placeholder="123 Main Street"
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                  <label for="city" class="block text-sm font-semibold text-[#5A7184] mb-2">City</label>
                  <input
                    id="city"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
                    placeholder="New York"
                  />
                </div>
                <div>
                  <label for="state" class="block text-sm font-semibold text-[#5A7184] mb-2">State</label>
                  <input
                    id="state"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
                    placeholder="NY"
                  />
                </div>
                <div>
                  <label for="zip" class="block text-sm font-semibold text-[#5A7184] mb-2">ZIP Code</label>
                  <input
                    id="zip"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
                    placeholder="10001"
                  />
                </div>
              </div>

              <div>
                <label for="country" class="block text-sm font-semibold text-[#5A7184] mb-2">Country</label>
                <select
                  id="country"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2]"
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
          <div class="bg-white p-8 rounded-2xl border border-gray-100">
            <h2 class="text-2xl font-bold text-[#5A7184] mb-6">Shipping Method</h2>
            <div class="space-y-4">
              <label class="flex items-center p-4 border-2 border-[#5A7184] rounded-lg cursor-pointer bg-[#F9F9F9]">
                <input type="radio" v-model="shippingMethod" value="express" class="w-4 h-4" />
                <div class="ml-4 flex-1">
                  <p class="font-semibold text-[#5A7184]">Express Shipping</p>
                  <p class="text-sm text-gray-600">Arrive in 2-3 business days</p>
                </div>
                <span class="font-bold text-[#5A7184]">$10.00</span>
              </label>
              <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer">
                <input type="radio" v-model="shippingMethod" value="standard" class="w-4 h-4" />
                <div class="ml-4 flex-1">
                  <p class="font-semibold text-gray-700">Standard Shipping</p>
                  <p class="text-sm text-gray-600">Arrive in 5-7 business days</p>
                </div>
                <span class="font-semibold text-gray-700">FREE</span>
              </label>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-4">
            <RouterLink to="/cart" class="flex-1 border-2 border-[#5A7184] text-[#5A7184] hover:bg-[#F9F9F9] py-3 rounded-lg font-semibold transition text-center">
              Back to Cart
            </RouterLink>
            <button @click="proceedToPayment" class="flex-1 bg-[#5A7184] hover:bg-[#4a5f70] text-white py-3 rounded-lg font-semibold transition">
              Continue to Payment
            </button>
          </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="lg:col-span-1">
          <div class="bg-[#F9F9F9] p-6 rounded-2xl border border-gray-100 sticky top-20">
            <h2 class="text-lg font-semibold text-[#5A7184] mb-6">Order Summary</h2>

            <div class="space-y-4 mb-6 pb-6 border-b border-gray-300">
              <div class="flex justify-between text-gray-700">
                <span>Subtotal</span>
                <span>$143.49</span>
              </div>
              <div class="flex justify-between text-gray-700">
                <span>Shipping</span>
                <span v-if="shippingMethod === 'express'" class="text-right">$10.00</span>
                <span v-else class="text-green-600 font-semibold">FREE</span>
              </div>
              <div class="flex justify-between text-gray-700">
                <span>Tax</span>
                <span>$14.35</span>
              </div>
            </div>

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
