<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);

const navLinks = [
  { name: 'Home', href: '#home' },
  { name: 'About', href: '#about' },
  { name: 'Timeline', href: '#timeline' },
  { name: 'FAQ', href: '#faq' },
];

const checkScroll = () => {
  const scrollPos = window.scrollY || window.pageYOffset || document.documentElement.scrollTop || 0;
  // Threshold with slight hysteresis to avoid rapid jitter
  if (!isScrolled.value && scrollPos > 30) {
    isScrolled.value = true;
  } else if (isScrolled.value && scrollPos < 10) {
    isScrolled.value = false;
  }
};

onMounted(() => {
  window.addEventListener('scroll', checkScroll, { passive: true });
  checkScroll();
});

onUnmounted(() => {
  window.removeEventListener('scroll', checkScroll);
});

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};
</script>

<template>
  <!-- Fixed Header Outer Wrapper with Smooth Fluid Spring Transition -->
  <header
    class="fixed top-0 inset-x-0 z-50 flex justify-center pointer-events-none transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
    :class="[
      isScrolled
        ? 'pt-3 sm:pt-4 px-3 sm:px-6'
        : 'pt-0 px-0'
    ]"
  >
    <!-- Inner Navbar Card -->
    <div
      class="pointer-events-auto transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] w-full"
      :class="[
        isScrolled
          ? 'max-w-5xl lg:max-w-6xl rounded-2xl sm:rounded-full bg-white/90 backdrop-blur-xl border border-purple-200/70 shadow-xl shadow-purple-950/20 px-5 sm:px-8'
          : 'max-w-none rounded-none bg-white border-b border-slate-100 shadow-xs px-4 sm:px-8 lg:px-12'
      ]"
    >
      <div
        class="flex items-center justify-between transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
        :class="[
          isScrolled
            ? 'h-14 sm:h-16'
            : 'h-16 sm:h-20 max-w-7xl mx-auto'
        ]"
      >
        <!-- Brand Logo (Placeholder floral emblem) -->
        <a href="#home" class="flex items-center gap-2.5 sm:gap-3 group focus:outline-hidden">
          <div
            class="rounded-xl bg-gradient-to-tr from-[#5E17EB] to-[#B5179E] p-0.5 shadow-sm group-hover:scale-105 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] flex items-center justify-center shrink-0"
            :class="isScrolled ? 'h-9 w-9 sm:h-10 sm:w-10' : 'h-10 w-10 sm:h-11 sm:w-11'"
          >
            <div class="w-full h-full bg-white rounded-[10px] flex items-center justify-center">
              <svg
                class="text-[#5E17EB] transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
                :class="isScrolled ? 'w-5 h-5' : 'w-6 h-6'"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L9.5 8.5 3 9.5 8 14.5 6.5 21 12 17.5 17.5 21 16 14.5 21 9.5 14.5 8.5 12 2z" />
                <circle cx="12" cy="12" r="3" fill="#5E17EB" fill-opacity="0.2" />
              </svg>
            </div>
          </div>
          <div class="flex flex-col">
            <span
              class="font-black tracking-tight bg-gradient-to-r from-[#5E17EB] to-[#B5179E] bg-clip-text text-transparent transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
              :class="isScrolled ? 'text-lg sm:text-xl' : 'text-xl sm:text-2xl'"
            >
              Iterate 2026
            </span>
          </div>
        </a>

        <!-- Desktop Navigation Links -->
        <nav
          class="hidden md:flex items-center transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
          :class="isScrolled ? 'gap-6 lg:gap-8' : 'gap-8 lg:gap-10'"
        >
          <a
            v-for="link in navLinks"
            :key="link.name"
            :href="link.href"
            class="text-sm font-semibold text-slate-700 hover:text-[#5E17EB] transition-colors py-1 relative after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-[#5E17EB] hover:after:w-full after:transition-all after:duration-300"
          >
            {{ link.name }}
          </a>
        </nav>

        <!-- Right Action Button: Masuk -->
        <div class="hidden md:flex items-center gap-4">
          <a
            href="/login"
            class="inline-flex items-center justify-center border-2 border-[#5E17EB] font-bold text-[#5E17EB] hover:bg-[#5E17EB] hover:text-white transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] shadow-xs hover:shadow-md hover:shadow-purple-500/20 active:scale-95"
            :class="[
              isScrolled
                ? 'px-5 py-1.5 rounded-full text-xs'
                : 'px-6 py-2 rounded-full text-sm'
            ]"
          >
            Masuk
          </a>
        </div>

        <!-- Mobile Hamburger Button -->
        <div class="flex md:hidden items-center">
          <button
            type="button"
            @click="toggleMobileMenu"
            class="p-2 rounded-lg text-slate-600 hover:text-[#5E17EB] hover:bg-purple-50 focus:outline-hidden transition-colors"
            aria-label="Toggle Navigation Menu"
          >
            <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-3 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 -translate-y-3 scale-95"
    >
      <div
        v-if="isMobileMenuOpen"
        class="pointer-events-auto md:hidden absolute top-full left-4 right-4 mt-2 bg-white/95 backdrop-blur-xl border border-purple-100 rounded-2xl px-5 pt-3 pb-6 space-y-3 shadow-2xl shadow-purple-950/25"
      >
        <a
          v-for="link in navLinks"
          :key="link.name"
          :href="link.href"
          @click="isMobileMenuOpen = false"
          class="block px-3 py-2.5 rounded-xl text-base font-semibold text-slate-700 hover:bg-purple-50 hover:text-[#5E17EB] transition-colors"
        >
          {{ link.name }}
        </a>
        <div class="pt-3 border-t border-slate-100">
          <a
            href="/login"
            @click="isMobileMenuOpen = false"
            class="block w-full text-center px-4 py-2.5 rounded-full border-2 border-[#5E17EB] text-sm font-bold text-[#5E17EB] hover:bg-[#5E17EB] hover:text-white transition-all shadow-xs"
          >
            Masuk
          </a>
        </div>
      </div>
    </transition>
  </header>
</template>
