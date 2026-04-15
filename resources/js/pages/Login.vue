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
        <h1 class="text-3xl font-bold text-[#5A7184] mb-2 text-center">Welcome Back</h1>
        <p class="text-center text-gray-600 mb-8">Sign in to your Ceylon Craft account</p>

        <form @submit.prevent="handleLogin" class="space-y-6">
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
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer">
              <input type="checkbox" v-model="form.rememberMe" class="w-4 h-4 rounded" />
              <span class="ml-2 text-sm text-gray-700">Remember me</span>
            </label>
            <a href="#" class="text-sm text-[#5A7184] hover:text-[#4a5f70] font-semibold">Forgot password?</a>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full bg-[#5A7184] hover:bg-[#4a5f70] text-white py-3 rounded-lg font-semibold transition-colors"
          >
            Sign In
          </button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-4 my-8">
          <div class="flex-1 border-t border-gray-300"></div>
          <span class="text-gray-600 text-sm">or</span>
          <div class="flex-1 border-t border-gray-300"></div>
        </div>

        <!-- Social Login -->
        <div class="grid grid-cols-2 gap-4 mb-8">
          <button class="border-2 border-gray-300 hover:border-[#5A7184] text-gray-700 hover:text-[#5A7184] py-2.5 rounded-lg font-semibold transition">
            Google
          </button>
          <button class="border-2 border-gray-300 hover:border-[#5A7184] text-gray-700 hover:text-[#5A7184] py-2.5 rounded-lg font-semibold transition">
            Facebook
          </button>
        </div>

        <!-- Sign Up Link -->
        <p class="text-center text-gray-700">
          Don't have an account?
          <RouterLink to="/register" class="text-[#5A7184] hover:text-[#4a5f70] font-semibold">
            Create one
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
  email: '',
  password: '',
  rememberMe: false
});

const handleLogin = async () => {
  if (!form.value.email || !form.value.password) {
    return;
  }

  try {
    const response = await fetch('/api/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email: form.value.email,
        password: form.value.password,
      }),
    });

    const result = await response.json();

    if (response.ok && result.success) {
      // Store token and user data in localStorage
      localStorage.setItem('authToken', result.data.token);
      localStorage.setItem('user', JSON.stringify(result.data));
      // Redirect to home
      router.push('/');
    } else {
      alert(result.message || 'Login failed');
    }
  } catch (error) {
    console.error('Login error:', error);
    alert('Login error: ' + error.message);
  }
};
</script>
