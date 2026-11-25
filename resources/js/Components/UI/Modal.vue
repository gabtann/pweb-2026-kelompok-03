<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: '',
  },
  maxWidth: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg', 'xl', '2xl'].includes(v),
  },
  closeable: {
    type: Boolean,
    default: true,
  },
  variant: {
    type: String,
    default: 'default',
    validator: (v) => ['default', 'danger', 'warning', 'success'].includes(v),
  },
});

const emit = defineEmits(['close', 'confirm', 'update:show']);

const close = () => {
  if (props.closeable) {
    emit('close');
    emit('update:show', false);
  }
};

const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.show && props.closeable) {
    close();
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
  document.body.style.overflow = '';
});

watch(
  () => props.show,
  (val) => {
    if (val) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
  }
);

const maxWidthClass = computed(() => {
  switch (props.maxWidth) {
    case 'sm':
      return 'sm:max-w-sm';
    case 'lg':
      return 'sm:max-w-lg';
    case 'xl':
      return 'sm:max-w-xl';
    case '2xl':
      return 'sm:max-w-2xl';
    case 'md':
    default:
      return 'sm:max-w-md';
  }
});
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
        aria-modal="true"
        role="dialog"
      >
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"
          @click="close"
        />

        <!-- Dialog Container -->
        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to-class="opacity-100 translate-y-0 sm:scale-100"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 translate-y-0 sm:scale-100"
          leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <div
            v-if="show"
            :class="[
              'relative bg-white rounded-2xl shadow-xl overflow-hidden transform transition-all w-full my-8 z-10 border border-slate-100',
              maxWidthClass,
            ]"
          >
            <!-- Header -->
            <div
              v-if="title || $slots.header"
              class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between"
            >
              <slot name="header">
                <div class="flex items-center gap-2.5">
                  <span
                    v-if="variant === 'danger'"
                    class="p-2 rounded-xl bg-rose-50 text-rose-600"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </span>
                  <span
                    v-else-if="variant === 'warning'"
                    class="p-2 rounded-xl bg-amber-50 text-amber-600"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </span>
                  <h3 class="text-base font-bold text-slate-800">
                    {{ title }}
                  </h3>
                </div>
              </slot>

              <!-- Close Button (X) with accessible label -->
              <button
                v-if="closeable"
                type="button"
                class="rounded-lg p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-colors"
                aria-label="Tutup dialog"
                @click="close"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Body Content -->
            <div class="px-6 py-5 text-sm text-slate-600">
              <slot />
            </div>

            <!-- Footer Actions -->
            <div
              v-if="$slots.footer"
              class="px-6 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-end gap-2.5"
            >
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

