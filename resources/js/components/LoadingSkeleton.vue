<template>
  <div class="space-y-4">
    <div v-for="i in count" :key="i" class="bg-gray-200 rounded-lg h-12 animate-pulse"></div>
  </div>
</template>

<script setup>
defineProps({
  count: {
    type: Number,
    default: 3,
  },
});
</script>
