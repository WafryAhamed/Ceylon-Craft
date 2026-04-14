<template>
  <button
    :class="[
      'font-semibold rounded-xl transition-all duration-300 inline-flex items-center justify-center gap-2',
      sizeClasses,
      variantClasses,
      className
    ]"
    :type="type"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (val) => ['sm', 'md', 'lg', 'xl'].includes(val)
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (val) => ['primary', 'secondary', 'outline', 'ghost'].includes(val)
  },
  type: {
    type: String,
    default: 'button'
  },
  disabled: Boolean,
  className: String
});

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'px-4 py-2 text-sm',
    md: 'px-6 py-3 text-base',
    lg: 'px-8 py-4 text-lg',
    xl: 'px-10 py-5 text-xl'
  };
  return sizes[props.size];
});

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-[#FB2B4A] text-white hover:bg-[#E91B3D] shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed',
    secondary: 'bg-[#657691] text-white hover:bg-[#505A6E] shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed',
    outline: 'border-2 border-[#FB2B4A] text-[#FB2B4A] hover:bg-[#FB2B4A] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed',
    ghost: 'text-[#657691] hover:bg-[#EEF0F7] disabled:opacity-50 disabled:cursor-not-allowed'
  };
  return variants[props.variant];
});
</script>
