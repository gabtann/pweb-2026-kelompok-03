<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'info',
    validator: (v) => ['info', 'success', 'warning', 'danger'].includes(v),
  },
  title: {
    type: String,
    default: '',
  },
  dismissible: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['dismiss']);
const isVisible = ref(true);

const handleDismiss = () => {
  isVisible.value = false;
  emit('dismiss');
};

const styles = computed(() => {
  switch (props.type) {
    case 'success':
      return {
        bg: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        iconColor: 'text-emerald-500',
        titleColor: 'text-emerald-900',
      };
    case 'warning':
      return {
        bg: 'bg-amber-50 border-amber-200 text-amber-800',
        iconColor: 'text-amber-500',
        titleColor: 'text-amber-900',
      };
    case 'danger':
      return {
        bg: 'bg-rose-50 border-rose-200 text-rose-800',
        iconColor: 'text-rose-500',
        titleColor: 'text-rose-900',
      };
    case 'info':
    default:
      return {
        bg: 'bg-purple-50 border-purple-200 text-purple-900',
        iconColor: 'text-[#5E17EB]',
        titleColor: 'text-[#5E17EB]',
      };
  }
});
</script>

<template>
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="opacity-0 -translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-2"
  >
    <div
      v-if="isVisible"
      :class="[
        'flex items-start p-4 rounded-2xl border transition-all shadow-2xs',
        styles.bg,
      ]"
      role="alert"
    >
      <!-- Icon Slot or Semantic Default Icon -->
      <div class="shrink-0 mr-3 mt-0.5">
        <slot name="icon">
          <!-- Success Icon -->
          <svg
            v-if="type === 'success'"
            class="w-5 h-5 text-emerald-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>

          <!-- Danger Icon -->
          <svg
            v-else-if="type === 'danger'"
            class="w-5 h-5 text-rose-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>

          <!-- Warning Icon -->
          <svg
            v-else-if="type === 'warning'"
            class="w-5 h-5 text-amber-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>

          <!-- Info Icon (Purple) -->
          <svg
            v-else
            class="w-5 h-5 text-[#5E17EB]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </slot>
      </div>

      <!-- Content -->
      <div class="flex-1 text-sm">
        <h4 v-if="title" :class="['font-bold mb-1', styles.titleColor]">
          {{ title }}
        </h4>
        <div class="leading-relaxed">
          <slot />
        </div>
      </div>

      <!-- Dismiss Button -->
      <button
        v-if="dismissible"
        type="button"
        class="shrink-0 -mr-1 -mt-1 ml-3 p-1.5 rounded-lg opacity-70 hover:opacity-100 hover:bg-black/5 focus:outline-none transition-colors"
        aria-label="Tutup alert"
        @click="handleDismiss"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </Transition>
</template>

