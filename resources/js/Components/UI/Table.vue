<script setup>
import Skeleton from './Skeleton.vue';

const props = defineProps({
  columns: {
    type: Array,
    required: true,
    // Format: [{ key: 'name', label: 'Nama Lengkap', align?: 'left'|'center'|'right', width?: string }]
  },
  rows: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  loadingRows: {
    type: Number,
    default: 4,
  },
  emptyText: {
    type: String,
    default: 'Belum ada data yang tersedia.',
  },
  hoverable: {
    type: Boolean,
    default: true,
  },
  striped: {
    type: Boolean,
    default: false,
  },
});

const getAlignClass = (align) => {
  switch (align) {
    case 'center':
      return 'text-center';
    case 'right':
      return 'text-right';
    case 'left':
    default:
      return 'text-left';
  }
};
</script>

<template>
  <div class="w-full bg-white rounded-2xl border border-slate-100 shadow-2xs overflow-hidden">
    <!-- Responsive Table Container -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <!-- Table Head -->
        <thead>
          <tr class="bg-slate-50/80 border-b border-slate-100">
            <th
              v-for="col in columns"
              :key="col.key"
              scope="col"
              :style="col.width ? { width: col.width } : {}"
              :class="[
                'px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-500',
                getAlignClass(col.align),
              ]"
            >
              <slot :name="`head(${col.key})`" :column="col">
                {{ col.label }}
              </slot>
            </th>

            <!-- Actions Header Column if slot exists -->
            <th
              v-if="$slots.actions"
              scope="col"
              class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-500 text-right"
            >
              <slot name="actions-header">Aksi</slot>
            </th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
          <!-- Loading State with Skeleton -->
          <template v-if="loading">
            <tr v-for="i in loadingRows" :key="`skeleton-${i}`">
              <td
                v-for="col in columns"
                :key="`skel-col-${col.key}`"
                class="px-5 py-4"
              >
                <div class="h-4 bg-slate-200/80 rounded-md animate-pulse w-3/4" />
              </td>
              <td v-if="$slots.actions" class="px-5 py-4 text-right">
                <div class="h-7 w-16 bg-slate-200/80 rounded-lg animate-pulse inline-block" />
              </td>
            </tr>
          </template>

          <!-- Empty State -->
          <tr v-else-if="!rows || rows.length === 0">
            <td
              :colspan="columns.length + ($slots.actions ? 1 : 0)"
              class="px-5 py-12 text-center text-slate-400"
            >
              <slot name="empty">
                <div class="flex flex-col items-center justify-center space-y-2">
                  <div class="p-3 bg-purple-50 rounded-2xl text-[#5E17EB]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                  </div>
                  <p class="text-sm font-medium text-slate-600">{{ emptyText }}</p>
                </div>
              </slot>
            </td>
          </tr>

          <!-- Normal Data Rows -->
          <template v-else>
            <tr
              v-for="(row, index) in rows"
              :key="row.id || index"
              :class="[
                'transition-colors duration-150',
                hoverable ? 'hover:bg-purple-50/30' : '',
                striped && index % 2 === 1 ? 'bg-slate-50/40' : '',
              ]"
            >
              <!-- Dynamic Columns -->
              <td
                v-for="col in columns"
                :key="col.key"
                :class="['px-5 py-4', getAlignClass(col.align)]"
              >
                <!-- Dynamic scoped slot `#cell(key)` -->
                <slot
                  :name="`cell(${col.key})`"
                  :row="row"
                  :index="index"
                  :value="row[col.key]"
                >
                  <span class="truncate">{{ row[col.key] ?? '-' }}</span>
                </slot>
              </td>

              <!-- Dedicated Actions Column -->
              <td
                v-if="$slots.actions"
                class="px-5 py-4 text-right whitespace-nowrap font-medium"
              >
                <div class="inline-flex items-center justify-end gap-1.5">
                  <slot name="actions" :row="row" :index="index" />
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Table Footer / Pagination Slot -->
    <div
      v-if="$slots.footer"
      class="px-5 py-3.5 bg-slate-50/60 border-t border-slate-100"
    >
      <slot name="footer" />
    </div>
  </div>
</template>

