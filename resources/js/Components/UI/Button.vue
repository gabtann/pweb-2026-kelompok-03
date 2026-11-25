<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'secondary', 'outline', 'danger', 'ghost'].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  type: {
    type: String,
    default: 'button',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  block: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['click']);

const baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer active:scale-[0.98]';

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'bg-[#5E17EB] hover:bg-[#4E10C8] text-white shadow-sm focus:ring-[#5E17EB]/40 disabled:hover:bg-[#5E17EB]';
    case 'secondary':
      return 'bg-purple-50 hover:bg-purple-100 text-[#5E17EB] border border-purple-200 focus:ring-purple-400 disabled:hover:bg-purple-50';
    case 'outline':
      return 'bg-white hover:bg-purple-50 text-[#5E17EB] border border-purple-300 hover:border-purple-400 focus:ring-purple-400 disabled:hover:bg-white';
    case 'danger':
      return 'bg-rose-600 hover:bg-rose-700 text-white shadow-sm focus:ring-rose-400 disabled:hover:bg-rose-600';
    case 'ghost':
      return 'text-slate-600 hover:text-[#5E17EB] hover:bg-purple-50 focus:ring-purple-300 disabled:hover:bg-transparent';
    default:
      return 'bg-[#5E17EB] hover:bg-[#4E10C8] text-white';
  }
});

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'text-xs px-3 py-1.5 rounded-lg gap-1.5';
    case 'lg':
      return 'text-base px-6 py-3 rounded-xl gap-2.5';
    case 'md':
    default:
      return 'text-sm px-4 py-2.5 rounded-xl gap-2';
  }
});

const handleClick = (event) => {
  if (!props.disabled && !props.loading) {
    emit('click', event);
  }
};
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      baseClasses,
      variantClasses,
      sizeClasses,
      block ? 'w-full' : '',
    ]"
    @click="handleClick"
  >
    <!-- Loading Spinner -->
    <svg
      v-if="loading"
      class="animate-spin -ml-0.5 h-4 w-4 text-current"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      aria-hidden="true"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      />
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
      />
    </svg>

    <!-- Icon Left Slot (Hidden if loading) -->
    <span v-if="$slots['icon-left'] && !loading" class="inline-flex shrink-0">
      <slot name="icon-left" />
    </span>

    <!-- Default Button Content -->
    <span class="truncate">
      <slot />
    </span>

    <!-- Icon Right Slot -->
    <span v-if="$slots['icon-right'] && !loading" class="inline-flex shrink-0">
      <slot name="icon-right" />
    </span>
  </button>
</template>

