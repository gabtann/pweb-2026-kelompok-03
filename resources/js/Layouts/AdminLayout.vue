<script setup>
import { ref } from 'vue';
import AdminSidebar from '../Components/Admin/AdminSidebar.vue';
import AdminNavbar from '../Components/Admin/AdminNavbar.vue';

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard Administrasi',
  },
  subtitle: {
    type: String,
    default: '',
  },
  currentRoute: {
    type: String,
    default: 'dashboard',
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

const emit = defineEmits(['navigate', 'logout']);
const isSidebarOpen = ref(false);

const handleNavigate = (item) => {
  emit('navigate', item);
};
</script>

<template>
  <div class="min-h-screen bg-[#F7F6FD] text-slate-800 flex flex-col font-sans">
    <!-- Admin Sidebar -->
    <AdminSidebar
      :is-open="isSidebarOpen"
      :current-route="currentRoute"
      @close="isSidebarOpen = false"
      @navigate="handleNavigate"
    />

    <!-- Main Wrapper (Shifted left margin on lg screen) -->
    <div class="lg:pl-64 flex flex-col flex-1 min-w-0 transition-all duration-300">
      <!-- Admin Navbar -->
      <AdminNavbar
        :title="title"
        :subtitle="subtitle"
        :user="user"
        @toggle-sidebar="isSidebarOpen = !isSidebarOpen"
        @logout="$emit('logout')"
      />

      <!-- Main Content Slot -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6">
          <slot />
        </div>
      </main>

      <!-- Minimal Admin Footer -->
      <footer class="py-4 px-6 border-t border-slate-200/60 bg-white/50 text-center text-xs text-slate-400">
        &copy; 2026 Iterate Event Management — Panitia & Pengelola Kompetisi
      </footer>
    </div>
  </div>
</template>
