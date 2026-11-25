<script setup>
import { computed } from 'vue';

const props = defineProps({
  currentPage: {
    type: Number,
    default: 1,
  },
  totalPages: {
    type: Number,
    default: 1,
  },
  totalItems: {
    type: Number,
    default: 0,
  },
  perPage: {
    type: Number,
    default: 10,
  },
  // Laravel / Inertia links format: [{ url: string|null, label: string, active: boolean }]
  links: {
    type: Array,
    default: null,
  },
});

const emit = defineEmits(['page-change']);

const isLaravelPagination = computed(() => {
  return Array.isArray(props.links) && props.links.length > 0;
});

const startItem = computed(() => {
  if (props.totalItems === 0) return 0;
  return (props.currentPage - 1) * props.perPage + 1;
});

const endItem = computed(() => {
  return Math.min(props.currentPage * props.perPage, props.totalItems);
});

const handlePageClick = (page) => {
  if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
    emit('page-change', page);
  }
};

const cleanLabel = (label) => {
  return label
    .replace('&laquo; Previous', 'Sebelumnya')
    .replace('Next &raquo;', 'Selanjutnya')
    .replace('&laquo;', '‹')
    .replace('&raquo;', '›');
};
</script>

<template>
  <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3">
    <!-- Info Display -->
    <div v-if="totalItems > 0" class="text-xs text-slate-500">
      Menampilkan
      <span class="font-semibold text-slate-700">{{ startItem }}</span>
      sampai
      <span class="font-semibold text-slate-700">{{ endItem }}</span>
      dari
      <span class="font-semibold text-slate-700">{{ totalItems }}</span>
      data
    </div>
    <div v-else />

    <!-- Laravel / Inertia Links Mode -->
    <nav v-if="isLaravelPagination" class="inline-flex rounded-xl shadow-2xs -space-x-px" aria-label="Pagination">
      <template v-for="(link, index) in links" :key="index">
        <!-- Disabled Link -->
        <span
          v-if="!link.url"
          class="relative inline-flex items-center px-3 py-2 text-xs font-medium text-slate-400 bg-slate-50 border border-slate-200 cursor-not-allowed select-none first:rounded-l-xl last:rounded-r-xl"
          v-html="cleanLabel(link.label)"
        />

        <!-- Active Page -->
        <span
          v-else-if="link.active"
          class="relative z-10 inline-flex items-center px-3.5 py-2 text-xs font-bold text-white bg-[#5E17EB] border border-[#5E17EB] select-none first:rounded-l-xl last:rounded-r-xl"
          v-html="cleanLabel(link.label)"
        />

        <!-- Clickable Link (Inertia Link or standard a/button) -->
        <a
          v-else
          :href="link.url"
          class="relative inline-flex items-center px-3 py-2 text-xs font-medium text-slate-600 bg-white border border-slate-200 hover:bg-purple-50 hover:text-[#5E17EB] transition-colors first:rounded-l-xl last:rounded-r-xl"
          v-html="cleanLabel(link.label)"
        />
      </template>
    </nav>

    <!-- Standard Client-Side / Emit Pagination Mode -->
    <nav v-else-if="totalPages > 1" class="inline-flex items-center gap-1.5" aria-label="Pagination">
      <button
        type="button"
        :disabled="currentPage <= 1"
        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-purple-50 hover:text-[#5E17EB] disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        @click="handlePageClick(currentPage - 1)"
      >
        ‹ Sebelumnya
      </button>

      <template v-for="page in totalPages" :key="page">
        <button
          v-if="Math.abs(page - currentPage) <= 2 || page === 1 || page === totalPages"
          type="button"
          :class="[
            'inline-flex items-center justify-center h-8 w-8 text-xs font-medium rounded-lg transition-colors',
            page === currentPage
              ? 'bg-[#5E17EB] text-white font-bold shadow-2xs'
              : 'bg-white text-slate-600 border border-slate-200 hover:bg-purple-50 hover:text-[#5E17EB]',
          ]"
          @click="handlePageClick(page)"
        >
          {{ page }}
        </button>
        <span
          v-else-if="Math.abs(page - currentPage) === 3"
          class="px-1 text-slate-400 text-xs"
        >
          ...
        </span>
      </template>

      <button
        type="button"
        :disabled="currentPage >= totalPages"
        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-purple-50 hover:text-[#5E17EB] disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        @click="handlePageClick(currentPage + 1)"
      >
        Selanjutnya ›
      </button>
    </nav>
  </div>
</template>

