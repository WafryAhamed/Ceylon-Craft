<template>
  <div id="app" class="min-h-screen flex flex-col bg-[#F9F9F9]">
    <Navbar />
    <main class="flex-1">
      <RouterView />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router';
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
</style>
