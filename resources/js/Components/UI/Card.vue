<script setup>
defineProps({
  title: {
    type: String,
    default: '',
  },
  subtitle: {
    type: String,
    default: '',
  },
  tag: {
    type: String,
    default: '', // Uppercase tag like "PROFILE", "DATA TIM", "DATA KETUA" in Iterate 2026
  },
  noPadding: {
    type: Boolean,
    default: false,
  },
  bordered: {
    type: Boolean,
    default: true,
  },
  shadow: {
    type: Boolean,
    default: true,
  },
});
</script>

<template>
  <div
    :class="[
      'bg-white rounded-2xl transition-all duration-200 overflow-hidden',
      bordered ? 'border border-slate-100' : '',
      shadow ? 'shadow-2xs hover:shadow-xs' : '',
    ]"
  >
    <!-- Header / Tag & Title Section -->
    <div
      v-if="$slots.header || title || tag || $slots.actions"
      class="px-6 py-4.5 border-b border-slate-100 flex items-start justify-between gap-4"
    >
      <slot name="header">
        <div class="space-y-1">
          <!-- Tag / Category Header (Iterate 2026 style: text-purple-600 uppercase tracking-wider text-xs font-semibold) -->
          <p
            v-if="tag || $slots.tag"
            class="text-xs font-semibold uppercase tracking-wider text-[#5E17EB]"
          >
            <slot name="tag">{{ tag }}</slot>
          </p>

          <h3 v-if="title" class="text-lg font-bold text-slate-800 tracking-tight">
            {{ title }}
          </h3>

          <p v-if="subtitle" class="text-xs text-slate-500">
            {{ subtitle }}
          </p>
        </div>
      </slot>

      <!-- Actions slot on top-right -->
      <div v-if="$slots.actions" class="shrink-0 flex items-center gap-2">
        <slot name="actions" />
      </div>
    </div>

    <!-- Body / Content -->
    <div :class="noPadding ? '' : 'p-6'">
      <slot />
    </div>

    <!-- Footer -->
    <div
      v-if="$slots.footer"
      class="px-6 py-3.5 bg-slate-50/70 border-t border-slate-100 flex items-center justify-end gap-3"
    >
      <slot name="footer" />
    </div>
  </div>
</template>

