<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'text',
    validator: (v) => ['text', 'circular', 'rectangular', 'card', 'table-row'].includes(v),
  },
  width: {
    type: String,
    default: '',
  },
  height: {
    type: String,
    default: '',
  },
  count: {
    type: Number,
    default: 1,
  },
});

const baseClasses = 'animate-pulse bg-slate-200/80 shrink-0';

const style = computed(() => {
  const styles = {};
  if (props.width) styles.width = props.width;
  if (props.height) styles.height = props.height;
  return styles;
});
</script>

<template>
  <div class="space-y-2.5">
    <template v-for="i in count" :key="i">
      <!-- Text Variant -->
      <div
        v-if="variant === 'text'"
        :class="[baseClasses, 'h-4 rounded-md w-full']"
        :style="style"
      />

      <!-- Circular Variant (Avatar) -->
      <div
        v-else-if="variant === 'circular'"
        :class="[baseClasses, 'rounded-full h-10 w-10']"
        :style="style"
      />

      <!-- Rectangular Variant -->
      <div
        v-else-if="variant === 'rectangular'"
        :class="[baseClasses, 'rounded-xl h-24 w-full']"
        :style="style"
      />

      <!-- Card Skeleton Variant -->
      <div
        v-else-if="variant === 'card'"
        class="border border-slate-100 rounded-2xl p-5 bg-white shadow-2xs space-y-4"
      >
        <div class="h-44 bg-slate-200/80 rounded-xl animate-pulse" />
        <div class="space-y-2">
          <div class="h-5 bg-slate-200/80 rounded-md w-3/4 animate-pulse" />
          <div class="h-3.5 bg-slate-200/70 rounded-md w-full animate-pulse" />
          <div class="h-3.5 bg-slate-200/70 rounded-md w-2/3 animate-pulse" />
        </div>
        <div class="pt-2 flex gap-3">
          <div class="h-9 bg-slate-200/80 rounded-xl w-1/2 animate-pulse" />
          <div class="h-9 bg-purple-200/60 rounded-xl w-1/2 animate-pulse" />
        </div>
      </div>

      <!-- Table Row Skeleton Variant -->
      <div
        v-else-if="variant === 'table-row'"
        class="flex items-center space-x-4 py-3.5 px-4 border-b border-slate-100 animate-pulse"
      >
        <div class="h-9 w-9 bg-slate-200/80 rounded-full shrink-0" />
        <div class="flex-1 space-y-1.5">
          <div class="h-4 bg-slate-200/80 rounded-md w-1/3" />
          <div class="h-3 bg-slate-200/60 rounded-md w-1/4" />
        </div>
        <div class="h-6 w-20 bg-slate-200/70 rounded-full" />
        <div class="h-8 w-16 bg-slate-200/80 rounded-lg" />
      </div>
    </template>
  </div>
</template>

