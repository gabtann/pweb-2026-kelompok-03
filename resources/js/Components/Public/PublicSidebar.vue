<script setup>
import { ref } from 'vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  currentRoute: {
    type: String,
    default: 'competitions', // Default active: All Competitions
  },
});

const emit = defineEmits(['close', 'navigate']);

// Event sub-menu toggle
const isEventExpanded = ref(true);

const isActive = (id) => props.currentRoute === id;

const handleNavClick = (id, href) => {
  emit('navigate', { id, href });
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

    <!-- Sidebar Container (Iterate 2026 Purple Brand) -->
    <aside
      :class="[
        'fixed top-0 bottom-0 left-0 z-50 w-64 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0',
        'bg-[#5E17EB] text-white shadow-xl shadow-purple-950/20',
        isOpen ? 'translate-x-0' : '-translate-x-full',
      ]"
    >
      <!-- Top Brand Logo (Matching Figma Screenshot) -->
      <div class="h-16 flex items-center justify-between px-6 border-b border-white/10 shrink-0">
        <a href="/" class="flex items-center gap-3 group">
          <!-- Emblem / Logo Icon -->
          <div class="h-9 w-9 rounded-xl bg-white/15 flex items-center justify-center text-white font-extrabold text-base border border-white/20 group-hover:scale-105 transition-transform shadow-2xs">
            IT
          </div>
          <span class="text-xl font-extrabold tracking-tight text-white block leading-tight">
            Iterate 2026
          </span>
        </a>

        <!-- Mobile Close Button -->
        <button
          type="button"
          class="lg:hidden text-purple-200 hover:text-white p-1.5 rounded-lg hover:bg-white/10 focus:outline-none transition-colors"
          aria-label="Tutup menu sidebar"
          @click="$emit('close')"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Navigation Links for Peserta (Matching Figma Screenshot 1 & 2) -->
      <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2 select-none">
        <!-- 1. Profile -->
        <a
          href="/profile"
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all group',
            isActive('profile')
              ? 'bg-white/20 text-white font-bold shadow-sm'
              : 'text-purple-100 hover:bg-white/10 hover:text-white',
          ]"
          @click.prevent="handleNavClick('profile', '/profile')"
        >
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span>Profile</span>
        </a>

        <!-- 2. Event (Accordion Group) -->
        <div class="space-y-1">
          <button
            type="button"
            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold text-purple-100 hover:bg-white/10 hover:text-white transition-all cursor-pointer group"
            @click="isEventExpanded = !isEventExpanded"
          >
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Event</span>
            </div>
            <svg
              class="w-4 h-4 text-purple-200 transition-transform duration-200"
              :class="isEventExpanded ? 'rotate-180' : ''"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Sub-menu: All Competitions & My Events -->
          <div v-show="isEventExpanded" class="pl-7 space-y-1 pt-0.5">
            <!-- All Competitions -->
            <a
              href="/competitions"
              :class="[
                'flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm transition-all',
                isActive('competitions')
                  ? 'bg-white/20 text-white font-extrabold shadow-sm'
                  : 'text-purple-100/80 hover:bg-white/10 hover:text-white font-medium',
              ]"
              @click.prevent="handleNavClick('competitions', '/competitions')"
            >
              <span>All Competitions</span>
            </a>

            <!-- My Events -->
            <a
              href="/my-events"
              :class="[
                'flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm transition-all',
                isActive('my-events')
                  ? 'bg-white/20 text-white font-extrabold shadow-sm'
                  : 'text-purple-100/80 hover:bg-white/10 hover:text-white font-medium',
              ]"
              @click.prevent="handleNavClick('my-events', '/my-events')"
            >
              <span>My Events</span>
            </a>
          </div>
        </div>

        <!-- 3. Announcement -->
        <a
          href="/announcements"
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all group',
            isActive('announcements')
              ? 'bg-white/20 text-white font-bold shadow-sm'
              : 'text-purple-100 hover:bg-white/10 hover:text-white',
          ]"
          @click.prevent="handleNavClick('announcements', '/announcements')"
        >
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
          </svg>
          <span>Announcement</span>
        </a>
      </nav>

      <!-- Bottom System Info -->
      <div class="p-4 border-t border-white/10 bg-black/10 shrink-0">
        <p class="text-xs text-purple-200 text-center font-medium">
          Iterate 2026 &bull; Portal Peserta
        </p>
      </div>
    </aside>
  </div>
</template>
