<script setup>
import { ref } from 'vue';
import PublicSidebar from '../Components/Public/PublicSidebar.vue';
import PublicNavbar from '../Components/Public/PublicNavbar.vue';

const props = defineProps({
  currentRoute: {
    type: String,
    default: 'competitions',
  },
  user: {
    type: Object,
    default: () => ({
      name: 'James',
      role: 'Peserta',
    }),
  },
});

const emit = defineEmits(['navigate', 'logout']);
const isSidebarOpen = ref(false);

const handleNavigate = (item) => {
  emit('navigate', item);
};
</script>

<template>
  <div class="min-h-screen bg-[#F7F6FD] text-slate-800 flex flex-col font-sans selection:bg-[#5E17EB] selection:text-white">
    <!-- Participant Sidebar (Purple Sidebar from Figma) -->
    <PublicSidebar
      :is-open="isSidebarOpen"
      :current-route="currentRoute"
      @close="isSidebarOpen = false"
      @navigate="handleNavigate"
    />

    <!-- Main Content Area (Shifted left margin for sidebar on lg screens) -->
    <div class="lg:pl-64 flex flex-col flex-1 min-w-0 transition-all duration-300">
      <!-- Participant Header / Navbar -->
      <PublicNavbar
        :user="user"
        @toggle-sidebar="isSidebarOpen = !isSidebarOpen"
        @logout="$emit('logout')"
      />

      <!-- Dynamic Page Content Slot -->
      <main class="flex-1 p-6 sm:p-8 lg:p-10">
        <slot />
      </main>
    </div>
  </div>
</template>
