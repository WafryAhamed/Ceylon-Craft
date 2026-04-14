<template>
  <div class="fixed top-4 right-4 z-50 space-y-4">
    <transition-group name="toast" tag="div">
      <div
        v-for="notification in uiStore.notifications"
        :key="notification.id"
        :class="`p-4 rounded-lg shadow-lg text-white flex items-center gap-3 min-w-80 animate-slide-in ${getNotificationClass(notification.type)}`"
      >
        <component :is="getNotificationIcon(notification.type)" class="w-5 h-5 flex-shrink-0" />
        <span class="flex-1">{{ notification.message }}</span>
        <button @click="uiStore.removeNotification(notification.id)" class="text-white hover:opacity-80">
          ✕
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useUIStore } from '@/stores/uiStore';

const uiStore = useUIStore();

const getNotificationClass = (type) => {
  const classes = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    warning: 'bg-yellow-500',
    info: 'bg-blue-500',
  };
  return classes[type] || 'bg-blue-500';
};

const getNotificationIcon = (type) => {
  const icons = {
    success: 'CheckCircleIcon',
    error: 'ExclamationCircleIcon',
    warning: 'ExclamationIcon',
    info: 'InformationCircleIcon',
  };
  return icons[type] || 'InformationCircleIcon';
};

// Icon components would go here - for now using SVG inline
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-slide-in {
  animation: slide-in 0.3s ease;
}
</style>
