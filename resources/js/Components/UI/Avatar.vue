<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  src: {
    type: String,
    default: '',
  },
  name: {
    type: String,
    default: '',
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(v),
  },
  shape: {
    type: String,
    default: 'circle',
    validator: (v) => ['circle', 'rounded'].includes(v),
  },
  status: {
    type: String,
    default: '',
    validator: (v) => ['', 'online', 'offline', 'busy', 'away'].includes(v),
  },
});

const imageError = ref(false);

const handleImageError = () => {
  imageError.value = true;
};

// Generates 1 or 2 letter initials, e.g. "James" -> "J", "Andi Pratama" -> "AP"
const initials = computed(() => {
  if (!props.name) return 'U';
  const parts = props.name.trim().split(/\s+/);
  if (parts.length === 1) {
    return parts[0].substring(0, 2).toUpperCase();
  }
  return (parts[0][0] + parts[1][0]).toUpperCase();
});

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'xs':
      return 'h-6 w-6 text-[10px]';
    case 'sm':
      return 'h-8 w-8 text-xs';
    case 'lg':
      return 'h-12 w-12 text-base';
    case 'xl':
      return 'h-16 w-16 text-xl';
    case 'md':
    default:
      return 'h-10 w-10 text-sm';
  }
});

const statusSize = computed(() => {
  switch (props.size) {
    case 'xs':
      return 'h-1.5 w-1.5';
    case 'sm':
      return 'h-2 w-2';
    case 'lg':
    case 'xl':
      return 'h-3.5 w-3.5';
    case 'md':
    default:
      return 'h-2.5 w-2.5';
  }
});

const statusColor = computed(() => {
  switch (props.status) {
    case 'online':
      return 'bg-emerald-500';
    case 'busy':
      return 'bg-rose-500';
    case 'away':
      return 'bg-amber-500';
    case 'offline':
    default:
      return 'bg-slate-400';
  }
});
</script>

<template>
  <div class="relative inline-flex shrink-0">
    <!-- Image Profile -->
    <img
      v-if="src && !imageError"
      :src="src"
      :alt="name || 'User Avatar'"
      :class="[
        'object-cover border border-purple-200/60 shadow-2xs',
        sizeClasses,
        shape === 'circle' ? 'rounded-full' : 'rounded-xl',
      ]"
      @error="handleImageError"
    />

    <!-- Fallback Initials Avatar (Iterate 2026 Purple Brand Style) -->
    <div
      v-else
      :class="[
        'flex items-center justify-center font-bold text-white bg-[#5E17EB] select-none shadow-2xs',
        sizeClasses,
        shape === 'circle' ? 'rounded-full' : 'rounded-xl',
      ]"
    >
      <slot name="icon">
        <svg
          v-if="!name"
          class="w-1/2 h-1/2 text-white/90"
          fill="currentColor"
          viewBox="0 0 24 24"
        >
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
        </svg>
        <span v-else>{{ initials }}</span>
      </slot>
    </div>

    <!-- Status Indicator Dot -->
    <span
      v-if="status"
      :class="[
        'absolute bottom-0 right-0 block rounded-full ring-2 ring-white',
        statusSize,
        statusColor,
      ]"
    />
  </div>
</template>

