<script setup>
import { ref } from 'vue';
import Avatar from '../UI/Avatar.vue';
import Badge from '../UI/Badge.vue';

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard',
  },
  subtitle: {
    type: String,
    default: '',
  },
  user: {
    type: Object,
    default: () => ({
      name: 'James',
      role: 'Panitia Admin',
      email: 'james@iterate.id',
    }),
  },
});

defineEmits(['toggle-sidebar', 'logout']);

const isDropdownOpen = ref(false);
</script>

<template>
  <header class="h-16 bg-white border-b border-slate-100 sticky top-0 z-30 shadow-2xs backdrop-blur-md bg-white/95">
    <div class="h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
      <!-- Left: Mobile Toggle & Page Title -->
      <div class="flex items-center gap-3 min-w-0">
        <!-- Mobile Sidebar Hamburger Toggle -->
        <button
          type="button"
          class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-purple-700 hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-colors"
          aria-label="Buka menu sidebar"
          @click="$emit('toggle-sidebar')"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <!-- Title & Subtitle / Breadcrumbs -->
        <div class="truncate">
          <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight leading-tight truncate">
            {{ title }}
          </h1>
          <p v-if="subtitle" class="text-xs text-slate-400 truncate hidden sm:block">
            {{ subtitle }}
          </p>
        </div>
      </div>

      <!-- Right: Notification Bell & Admin Identity -->
      <div class="flex items-center gap-3 shrink-0">
        <!-- Notification Bell -->
        <button
          type="button"
          class="relative p-2 rounded-xl text-slate-400 hover:text-[#5E17EB] hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition-colors"
          aria-label="Notifikasi"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <!-- Active Notification Red Dot -->
          <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white" />
        </button>

        <div class="h-6 w-px bg-slate-200 hidden sm:block" />

        <!-- User Identity Section (Matches Iterate 2026 Figma Style) -->
        <div class="relative">
          <button
            type="button"
            class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-purple-50/60 focus:outline-none transition-colors group cursor-pointer"
            @click="isDropdownOpen = !isDropdownOpen"
          >
            <div class="text-right hidden sm:block">
              <p class="text-xs font-bold text-slate-800 leading-tight group-hover:text-[#5E17EB] transition-colors">
                {{ user.name }}
              </p>
              <p class="text-[11px] text-purple-600 font-medium">
                {{ user.role }}
              </p>
            </div>

            <!-- Avatar Component from Jobdesk 1 -->
            <Avatar :name="user.name" size="md" status="online" />

            <svg class="w-4 h-4 text-slate-400 group-hover:text-purple-600 transition-colors hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Dropdown Menu -->
          <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div
              v-if="isDropdownOpen"
              class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-lg border border-slate-100 py-1.5 z-50 text-xs"
              @mouseleave="isDropdownOpen = false"
            >
              <div class="px-4 py-2 border-b border-slate-100">
                <p class="font-bold text-slate-800">{{ user.name }}</p>
                <p class="text-slate-400 truncate">{{ user.email }}</p>
              </div>

              <a
                href="#profile"
                class="block px-4 py-2 text-slate-700 hover:bg-purple-50 hover:text-[#5E17EB] transition-colors"
                @click="isDropdownOpen = false"
              >
                Pengaturan Profil
              </a>

              <button
                type="button"
                class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 transition-colors"
                @click="$emit('logout'); isDropdownOpen = false"
              >
                Keluar (Logout)
              </button>
            </div>
          </Transition>
        </div>
      </div>
    </div>
  </header>
</template>
