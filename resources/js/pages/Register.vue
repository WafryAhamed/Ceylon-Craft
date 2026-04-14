<template>
  <div class="w-full min-h-screen flex items-center justify-center bg-gradient-to-br from-[#F9F9F9] to-white py-12 px-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <RouterLink to="/" class="flex justify-center mb-8">
        <div class="flex items-center gap-2 font-semibold text-xl text-[#5A7184]">
          <div class="w-10 h-10 bg-[#5A7184] rounded-lg flex items-center justify-center">
            <span class="text-white font-bold">CC</span>
          </div>
          Ceylon Craft
        </div>
      </RouterLink>

      <!-- Form Card -->
      <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
        <h1 class="text-3xl font-bold text-[#5A7184] mb-2 text-center">Create Account</h1>
        <p class="text-center text-gray-600 mb-8">Join the Ceylon Craft community</p>

        <form @submit.prevent="handleRegister" class="space-y-5">
          <!-- Full Name -->
          <div>
            <label for="name" class="block text-sm font-semibold text-[#5A7184] mb-2">Full Name</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2] focus:border-transparent"
              placeholder="Your full name"
            />
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-semibold text-[#5A7184] mb-2">Email Address</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2] focus:border-transparent"
              placeholder="you@example.com"
            />
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-semibold text-[#5A7184] mb-2">Password</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2] focus:border-transparent"
              placeholder="••••••••"
            />
            <p class="text-xs text-gray-600 mt-1">At least 8 characters</p>
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="confirmPassword" class="block text-sm font-semibold text-[#5A7184] mb-2">Confirm Password</label>
            <input
              id="confirmPassword"
              v-model="form.confirmPassword"
              type="password"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D1E8E2] focus:border-transparent"
              placeholder="••••••••"
            />
          </div>

          <!-- Terms & Conditions -->
          <label class="flex items-start cursor-pointer">
            <input type="checkbox" v-model="form.agreeToTerms" required class="w-4 h-4 mt-1" />
            <span class="ml-2 text-sm text-gray-700">
              I agree to the
              <a href="#" class="text-[#5A7184] hover:text-[#4a5f70] font-semibold">Terms of Service</a>
              and
              <a href="#" class="text-[#5A7184] hover:text-[#4a5f70] font-semibold">Privacy Policy</a>
            </span>
          </label>

          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full bg-[#5A7184] hover:bg-[#4a5f70] text-white py-3 rounded-lg font-semibold transition-colors"
          >
            Create Account
          </button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-4 my-6">
          <div class="flex-1 border-t border-gray-300"></div>
          <span class="text-gray-600 text-sm">or</span>
          <div class="flex-1 border-t border-gray-300"></div>
        </div>

        <!-- Social Sign Up -->
        <div class="grid grid-cols-2 gap-4 mb-6">
          <button type="button" class="border-2 border-gray-300 hover:border-[#5A7184] text-gray-700 hover:text-[#5A7184] py-2.5 rounded-lg font-semibold transition">
            Google
          </button>
          <button type="button" class="border-2 border-gray-300 hover:border-[#5A7184] text-gray-700 hover:text-[#5A7184] py-2.5 rounded-lg font-semibold transition">
            Facebook
          </button>
        </div>

        <!-- Login Link -->
        <p class="text-center text-gray-700">
          Already have an account?
          <RouterLink to="/login" class="text-[#5A7184] hover:text-[#4a5f70] font-semibold">
            Sign in
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';

const router = useRouter();
const form = ref({
  name: '',
  email: '',
  password: '',
  confirmPassword: '',
  agreeToTerms: false
});

const handleRegister = () => {
  if (!form.value.agreeToTerms) {
    alert('Please agree to the terms and conditions');
    return;
  }
  if (form.value.password !== form.value.confirmPassword) {
    alert('Passwords do not match');
    return;
  }
  if (form.value.name && form.value.email && form.value.password) {
    alert('Registration successful! (Demo mode)');
    router.push('/');
  }
};
</script>
