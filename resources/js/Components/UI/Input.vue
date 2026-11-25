<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number, File, FileList],
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  label: {
    type: String,
    default: '',
  },
  id: {
    type: String,
    default: () => `input-${Math.random().toString(36).substring(2, 9)}`,
  },
  placeholder: {
    type: String,
    default: '',
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
  readonly: {
    type: Boolean,
    default: false,
  },
  accept: {
    type: String,
    default: '',
  },
  multiple: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue', 'change', 'blur', 'focus']);

const showPassword = ref(false);
const fileInputRef = ref(null);
const selectedFileName = ref('');

const inputType = computed(() => {
  if (props.type === 'password') {
    return showPassword.value ? 'text' : 'password';
  }
  return props.type;
});

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const handleInput = (event) => {
  if (props.type === 'file') {
    const files = event.target.files;
    selectedFileName.value = files.length > 1 ? `${files.length} file dipilih` : (files[0]?.name || '');
    emit('update:modelValue', props.multiple ? files : files[0]);
    emit('change', event);
  } else {
    emit('update:modelValue', event.target.value);
  }
};

const triggerFileInput = () => {
  if (fileInputRef.value && !props.disabled) {
    fileInputRef.value.click();
  }
};
</script>

<template>
  <div class="w-full space-y-1.5">
    <!-- Label -->
    <div v-if="label" class="flex items-center justify-between">
      <label :for="id" class="block text-sm font-medium text-slate-700">
        {{ label }}
        <span v-if="required" class="text-rose-500 font-bold ml-0.5">*</span>
      </label>
    </div>

    <!-- File Input Custom Style (Iterate 2026 Style) -->
    <div v-if="type === 'file'" class="relative">
      <input
        :id="id"
        ref="fileInputRef"
        type="file"
        class="sr-only"
        :accept="accept"
        :multiple="multiple"
        :disabled="disabled"
        @change="handleInput"
      />
      <div
        class="flex items-center justify-between w-full px-3.5 py-2.5 rounded-xl border transition-all duration-200 cursor-pointer bg-purple-50/50 hover:bg-purple-100/50 text-slate-600"
        :class="[
          error ? 'border-rose-400 bg-rose-50/30' : 'border-purple-200 hover:border-purple-300',
          disabled ? 'opacity-60 cursor-not-allowed bg-slate-100 border-slate-200' : '',
        ]"
        @click="triggerFileInput"
      >
        <div class="flex items-center space-x-2 truncate">
          <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
          </svg>
          <span class="text-sm truncate">
            {{ selectedFileName || placeholder || 'Pilih Berkas / File Dokumen...' }}
          </span>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 bg-white text-[#5E17EB] rounded-lg border border-purple-200 shadow-2xs shrink-0">
          Choose File
        </span>
      </div>
    </div>

    <!-- Regular Text / Email / Password / Tel / Number / Date Input -->
    <div v-else class="relative rounded-xl shadow-2xs">
      <!-- Icon Left -->
      <div
        v-if="$slots['icon-left']"
        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"
      >
        <slot name="icon-left" />
      </div>

      <input
        :id="id"
        :type="inputType"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :readonly="readonly"
        :class="[
          'block w-full rounded-xl text-sm transition-all duration-150',
          'placeholder:text-slate-400 text-slate-800 bg-white',
          'border focus:outline-none focus:ring-2',
          error
            ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-200'
            : 'border-slate-200 hover:border-slate-300 focus:border-[#5E17EB] focus:ring-purple-100',
          disabled ? 'bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed' : '',
          readonly ? 'bg-slate-50 cursor-default' : '',
          $slots['icon-left'] ? 'pl-10' : 'pl-3.5',
          $slots['icon-right'] || type === 'password' ? 'pr-10' : 'pr-3.5',
          'py-2.5',
        ]"
        @input="handleInput"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
      />

      <!-- Password Eye Toggle -->
      <button
        v-if="type === 'password' && !disabled"
        type="button"
        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
        tabindex="-1"
        @click="togglePassword"
      >
        <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
        </svg>
      </button>

      <!-- Custom Icon Right Slot -->
      <div
        v-else-if="$slots['icon-right']"
        class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400"
      >
        <slot name="icon-right" />
      </div>
    </div>

    <!-- Error Message -->
    <p v-if="error" class="text-xs text-rose-500 font-medium flex items-center gap-1 mt-1">
      <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      {{ error }}
    </p>

    <!-- Hint Text -->
    <p v-else-if="hint" class="text-xs text-slate-400 mt-1">
      {{ hint }}
    </p>
  </div>
</template>

