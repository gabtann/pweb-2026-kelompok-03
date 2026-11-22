<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean],
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  id: {
    type: String,
    default: () => `select-${Math.random().toString(36).substring(2, 9)}`,
  },
  options: {
    type: Array,
    default: () => [],
  },
  placeholder: {
    type: String,
    default: 'Pilih opsi...',
  },
  error: {
    type: String,
    default: '',
  },
  hint: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['update:modelValue', 'change']);

const normalizedOptions = computed(() => {
  return props.options.map((opt) => {
    if (typeof opt === 'object' && opt !== null) {
      return {
        value: opt.value ?? opt.id ?? '',
        label: opt.label ?? opt.name ?? String(opt.value),
      };
    }
    return {
      value: opt,
      label: String(opt),
    };
  });
});
</script>

<template>
  <div class="w-full space-y-1.5">
    <div v-if="label" class="flex items-center justify-between">
      <label :for="id" class="block text-sm font-medium text-slate-700">
        {{ label }}
        <span v-if="required" class="text-rose-500 font-bold ml-0.5">*</span>
      </label>
    </div>

    <div class="relative rounded-xl shadow-2xs">
      <select
        :id="id"
        :value="modelValue"
        :required="required"
        :disabled="disabled"
        :class="[
          'block w-full rounded-xl text-sm transition-all duration-150 appearance-none bg-white py-2.5 pl-3.5 pr-10',
          'border focus:outline-none focus:ring-2',
          error
            ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-200'
            : 'border-slate-200 hover:border-slate-300 focus:border-[#5E17EB] focus:ring-purple-100',
          disabled ? 'bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed' : '',
          !modelValue ? 'text-slate-400' : 'text-slate-800',
        ]"
        @change="$emit('update:modelValue', $event.target.value); $emit('change', $event.target.value)"
      >
        <option value="" disabled>{{ placeholder }}</option>
        <option
          v-for="opt in normalizedOptions"
          :key="opt.value"
          :value="opt.value"
          class="text-slate-800"
        >
          {{ opt.label }}
        </option>
      </select>

      <!-- Dropdown Chevron Icon -->
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
    </div>

    <p v-if="error" class="text-xs text-rose-500 font-medium flex items-center gap-1 mt-1">
      <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      {{ error }}
    </p>

    <p v-else-if="hint" class="text-xs text-slate-400 mt-1">
      {{ hint }}
    </p>
  </div>
</template>

