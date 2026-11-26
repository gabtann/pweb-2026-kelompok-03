<script setup>
import { computed } from 'vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  currentRoute: {
    type: String,
    default: 'dashboard',
  },
});

const emit = defineEmits(['close', 'navigate']);

const navigationItems = [
  {
    name: 'Dashboard',
    id: 'dashboard',
    href: '/admin/dashboard',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
  },
  {
    name: 'Kelola Akun',
    id: 'accounts',
    href: '/admin/accounts',
    icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
  },
  {
    name: 'Kelola Event',
    id: 'events',
    href: '/panitia/event',
    icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
  },
  {
    name: 'Kelola Tim & Berkas',
    id: 'teams',
    href: '/admin/teams',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
  },
  {
    name: 'Pengumuman',
    id: 'announcements',
    href: '/admin/announcements',
    icon: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
  },
];

const isActive = (id) => {
  return props.currentRoute === id;
};

const handleNavClick = (item) => {
  emit('navigate', item);
  emit('close');
};
</script>

<template>
  <div>
    <!-- Mobile Backdrop -->
    <Transition
      enter-active-class="transition-opacity duration-300 ease-linear"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-300 ease-linear"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden"
        @click="$emit('close')"
      />
    </Transition>

    <!-- Sidebar Container -->
    <aside
      :class="[
        'fixed top-0 bottom-0 left-0 z-50 w-64 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0',
        'bg-gradient-to-b from-[#4A159D] via-[#5E17EB] to-[#450EA8] text-white shadow-xl shadow-purple-950/20',
        isOpen ? 'translate-x-0' : '-translate-x-full',
      ]"
    >
      <!-- Brand Logo Header -->
      <div class="h-16 flex items-center justify-between px-6 border-b border-white/10 shrink-0">
        <a href="/admin/dashboard" class="flex items-center gap-3 group">
          <div class="h-9 w-9 rounded-xl bg-white/15 flex items-center justify-center text-white font-extrabold text-base border border-white/20 group-hover:scale-105 transition-transform shadow-2xs">
            IT
          </div>
          <div>
            <span class="text-base font-extrabold tracking-tight text-white block leading-tight">
              Iterate 2026
            </span>
            <span class="text-[10px] tracking-widest text-purple-200 uppercase font-semibold">
              Admin Portal
            </span>
          </div>
        </a>

        <!-- Mobile Close Button -->
        <button
          type="button"
          class="lg:hidden text-purple-200 hover:text-white p-1.5 rounded-lg hover:bg-white/10 focus:outline-none transition-colors"
          aria-label="Tutup sidebar"
          @click="$emit('close')"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Navigation Links -->
      <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">
        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-purple-200/70 mb-3 select-none">
          Menu Utama
        </p>

        <a
          v-for="item in navigationItems"
          :key="item.id"
          :href="item.href"
          :class="[
            'flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all group',
            isActive(item.id)
              ? 'bg-white/20 text-white font-bold shadow-sm shadow-purple-950/20 translate-x-1'
              : 'text-purple-100/80 hover:bg-white/10 hover:text-white',
          ]"
          @click.prevent="handleNavClick(item)"
        >
          <svg
            class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110"
            :class="isActive(item.id) ? 'text-white' : 'text-purple-200/80'"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
          </svg>
          <span class="truncate">{{ item.name }}</span>
        </a>
      </div>

      <!-- Bottom Profile / System Info -->
      <div class="p-4 border-t border-white/10 bg-black/10 shrink-0">
        <div class="flex items-center justify-between text-xs text-purple-200">
          <span class="font-medium">Iterate v1.0</span>
          <a
            href="/"
            class="text-purple-200 hover:text-white underline underline-offset-2 transition-colors flex items-center gap-1"
          >
            <span>Landing Page</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
          </a>
        </div>
      </div>
    </aside>
  </div>
</template>
