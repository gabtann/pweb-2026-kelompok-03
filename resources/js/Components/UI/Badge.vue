<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'purple',
    validator: (v) => ['purple', 'primary', 'success', 'warning', 'danger', 'info', 'gray', 'white'].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md'].includes(v),
  },
  dot: {
    type: Boolean,
    default: false,
  },
  rounded: {
    type: Boolean,
    default: true, // rounded-full (pill) as seen in Iterate 2026 design
  },
});

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'purple':
    case 'primary':
      // Iterate 2026 "Peserta" style badge
      return 'bg-[#EBE5FF] text-[#5E17EB] border border-purple-200/60 font-semibold';
    case 'white':
      // Iterate 2026 "Seminar" / "Lomba" floating badge
      return 'bg-white/95 text-[#5E17EB] shadow-2xs border border-purple-100 font-medium backdrop-blur-xs';
    case 'success':
      // Terverifikasi / Aktif
      return 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium';
    case 'warning':
      // Menunggu Verifikasi
      return 'bg-amber-50 text-amber-700 border border-amber-200 font-medium';
    case 'danger':
      // Ditolak / Tidak Aktif
      return 'bg-rose-50 text-rose-700 border border-rose-200 font-medium';
    case 'info':
      // Dalam Proses
      return 'bg-sky-50 text-sky-700 border border-sky-200 font-medium';
    case 'gray':
    default:
      return 'bg-slate-100 text-slate-700 border border-slate-200 font-medium';
  }
});

const dotColor = computed(() => {
  switch (props.variant) {
    case 'purple':
    case 'primary':
    case 'white':
      return 'bg-[#5E17EB]';
    case 'success':
      return 'bg-emerald-500';
    case 'warning':
      return 'bg-amber-500';
    case 'danger':
      return 'bg-rose-500';
    case 'info':
      return 'bg-sky-500';
    case 'gray':
    default:
      return 'bg-slate-400';
  }
});

const sizeClasses = computed(() => {
  return props.size === 'sm'
    ? 'text-[11px] px-2 py-0.5 gap-1'
    : 'text-xs px-2.5 py-1 gap-1.5';
});
</script>

<template>
  <span
    :class="[
      'inline-flex items-center tracking-tight transition-colors',
      variantClasses,
      sizeClasses,
      rounded ? 'rounded-full' : 'rounded-lg',
    ]"
  >
    <span
      v-if="dot"
      :class="['h-1.5 w-1.5 rounded-full shrink-0', dotColor]"
      aria-hidden="true"
    />
    <slot />
  </span>
</template>

