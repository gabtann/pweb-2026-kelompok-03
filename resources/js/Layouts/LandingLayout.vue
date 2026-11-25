<script setup>
import LandingNavbar from '@/Components/Landing/LandingNavbar.vue';
import LandingFooter from '@/Components/Landing/LandingFooter.vue';
</script>

<template>
  <div class="min-h-screen bg-[#110224] text-slate-100 flex flex-col font-sans selection:bg-pink-500 selection:text-white">
    <!-- White Topbar -->
    <LandingNavbar />

    <!-- Main Content Area -->
    <main class="grow pt-16 sm:pt-20">
      <slot />
    </main>

    <!-- Dark Footer -->
    <LandingFooter />
  </div>
</template>
