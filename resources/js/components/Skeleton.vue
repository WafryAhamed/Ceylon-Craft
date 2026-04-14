<template>
  <div :class="[variant === 'card' && 'bg-white rounded-xl shadow-md p-6']">
    <!-- Card Skeleton -->
    <div v-if="variant === 'card'" class="space-y-4">
      <div class="h-48 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded-lg animate-pulse"></div>
      <div class="space-y-2">
        <div class="h-6 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded w-3/4 animate-pulse"></div>
        <div class="h-4 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded w-1/2 animate-pulse"></div>
      </div>
    </div>

    <!-- Grid Skeleton -->
    <div v-else-if="variant === 'grid'" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="i in 6" :key="i" class="bg-white rounded-xl shadow-md p-6 space-y-4">
        <div class="h-48 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded-lg animate-pulse"></div>
        <div class="space-y-2">
          <div class="h-6 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded w-3/4 animate-pulse"></div>
          <div class="h-4 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded w-1/2 animate-pulse"></div>
          <div class="h-8 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded w-2/3 animate-pulse mt-4"></div>
        </div>
      </div>
    </div>

    <!-- List Item Skeleton -->
    <div v-else-if="variant === 'list'" class="space-y-4">
      <div v-for="i in count" :key="i" class="flex gap-4 bg-white p-4 rounded-lg">
        <div class="w-20 h-20 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded-lg flex-shrink-0 animate-pulse"></div>
        <div class="flex-1 space-y-2">
          <div class="h-5 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded w-1/2 animate-pulse"></div>
          <div class="h-4 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded w-2/3 animate-pulse"></div>
        </div>
      </div>
    </div>

    <!-- Profile Skeleton -->
    <div v-else-if="variant === 'profile'" class="space-y-6">
      <div class="flex items-center gap-4">
        <div class="w-20 h-20 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded-full animate-pulse"></div>
        <div class="flex-1 space-y-2">
          <div class="h-6 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded w-1/2 animate-pulse"></div>
          <div class="h-4 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded w-1/3 animate-pulse"></div>
        </div>
      </div>
      <div class="space-y-3">
        <div v-for="i in 4" :key="i" class="h-10 bg-gradient-to-r from-[#DFE2E9] to-[#A0ACC0] rounded animate-pulse"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'card',
    validator: v => ['card', 'grid', 'list', 'profile'].includes(v)
  },
  count: {
    type: Number,
    default: 3
  }
});
</script>

<style scoped>
@keyframes shimmer {
  0% {
    background-position: -1000px 0;
  }
  100% {
    background-position: 1000px 0;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
</style>
