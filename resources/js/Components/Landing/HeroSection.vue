<script setup>
</script>

<template>
  <section id="home" class="relative overflow-hidden pt-12 pb-24 lg:pt-16 lg:pb-32">
    <!-- Radiant Cosmic Flares / Glow Aura Background -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <!-- Top Center Flare -->
      <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[400px] bg-gradient-to-r from-purple-600/40 via-pink-600/30 to-purple-800/40 blur-[130px] rounded-full pointer-events-none"></div>
      <!-- Subtle Ambient Beam -->
      <div class="absolute top-10 left-1/2 -translate-x-1/2 w-[90%] max-w-5xl h-48 bg-gradient-to-b from-purple-500/20 to-transparent blur-3xl rounded-full"></div>
    </div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <!-- Decorative Emblem on Top of Hero -->
      <div class="flex justify-center mb-4">
        <div class="w-16 h-16 sm:w-20 sm:h-20 text-purple-300 opacity-90 drop-shadow-[0_0_15px_rgba(181,23,158,0.5)]">
          <svg viewBox="0 0 100 100" fill="none" class="w-full h-full">
            <path d="M50 10 C55 25, 75 30, 85 45 C75 55, 60 55, 50 70 C40 55, 25 55, 15 45 C25 30, 45 25, 50 10 Z" fill="url(#hero-emblem-grad)" />
            <circle cx="50" cy="45" r="14" fill="#1B053A" stroke="#E63956" stroke-width="2.5" />
            <path d="M45 42 L55 42 M55 42 L51 38 M55 42 L51 46" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M55 48 L45 48 M45 48 L49 44 M45 48 L49 52" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <defs>
              <linearGradient id="hero-emblem-grad" x1="15" y1="10" x2="85" y2="70" gradientUnits="userSpaceOnUse">
                <stop stop-color="#E63956"/>
                <stop offset="0.5" stop-color="#B5179E"/>
                <stop offset="1" stop-color="#5E17EB"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
      </div>

      <!-- Main Display Typography: Iterate 2026 -->
      <div class="relative inline-block mb-4 select-none">
        <h1 class="text-6xl sm:text-7xl md:text-8xl lg:text-9xl font-black tracking-tight text-white drop-shadow-[0_4px_25px_rgba(181,23,158,0.6)]">
          Iterate <span class="bg-gradient-to-r from-pink-300 via-purple-200 to-indigo-300 bg-clip-text text-transparent">2026</span>
        </h1>
      </div>

      <!-- Theme Badge Pill -->
      <div class="flex justify-center mb-6">
        <span class="inline-flex items-center px-6 py-2 rounded-full text-sm sm:text-base font-bold text-white bg-gradient-to-r from-[#D92662] via-[#E63956] to-[#FA5575] shadow-lg shadow-pink-600/30 ring-1 ring-white/20 tracking-wide">
          "Youth Powering a New Era"
        </span>
      </div>

      <!-- Institution & Event Subtitle -->
      <p class="max-w-2xl mx-auto text-sm sm:text-base md:text-lg text-purple-100/90 font-medium leading-relaxed mb-10">
        A National Technology Competition Festival by Teknik Informatika,<br class="hidden sm:inline" />
        Institute Teknologi Bontomarannu
      </p>

      <!-- Dual Action Buttons (Let's Explore & Daftar Sekarang) -->
      <div class="flex flex-wrap items-center justify-center gap-4">
        <!-- Let's Explore (Glass Outline) -->
        <a
          href="#competitions"
          class="inline-flex items-center gap-2.5 px-7 py-3 rounded-xl border border-white/30 text-white font-bold text-sm bg-white/5 hover:bg-white/15 hover:border-white/60 transition-all backdrop-blur-md shadow-sm hover:shadow-purple-500/20 active:scale-95"
        >
          <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-width="2"/>
            <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor"/>
          </svg>
          Let's Explore
        </a>

        <!-- Daftar Sekarang (Magenta Gradient Solid) -->
        <a
          href="/register"
          class="inline-flex items-center gap-2.5 px-7 py-3 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-[#D92662] via-[#E63956] to-[#FA5575] hover:opacity-95 shadow-lg shadow-pink-600/35 hover:shadow-pink-600/50 transition-all active:scale-95 ring-1 ring-white/20"
        >
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          Daftar Sekarang
        </a>
      </div>
    </div>
  </section>
</template>
