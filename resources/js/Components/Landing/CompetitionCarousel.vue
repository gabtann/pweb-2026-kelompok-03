<script setup>
import { ref } from 'vue';

const competitions = [
  {
    id: 'uiux',
    title: 'UI/UX',
    fullName: 'UI/UX Design Competition',
    description: 'UI/UX Design adalah kompetisi antartim untuk merancang solusi antarmuka dan pengalaman pengguna dari suatu permasalahan melalui pendekatan empati, riset, dan estetika dalam waktu terbatas. Tujuannya adalah menciptakan prototipe produk digital yang fungsional, intuitif, dan solutif sesuai kebutuhan pengguna.',
    iconType: 'wireframe',
  },
  {
    id: 'ict',
    title: 'ICT Business Development',
    fullName: 'ICT Business Development',
    description: 'ICT Business Development menguji kemampuan tim dalam mengidentifikasi peluang pasar, memvalidasi model bisnis teknologi yang berkelanjutan, serta merancang strategi monetisasi dan presentasi produk inovatif untuk industri masa depan.',
    iconType: 'chart',
  },
  {
    id: 'swd',
    title: 'Software Development',
    fullName: 'Software Development',
    description: 'Software Development menantang talenta muda menciptakan solusi perangkat lunak yang tangguh, terukur, dan berdampak nyata bagi penyelesaian persoalan ekosistem digital nasional dan industri maritim.',
    iconType: 'code',
  },
];

const currentIndex = ref(0);

const nextSlide = () => {
  currentIndex.value = (currentIndex.value + 1) % competitions.length;
};

const prevSlide = () => {
  currentIndex.value = (currentIndex.value - 1 + competitions.length) % competitions.length;
};

const goToSlide = (index) => {
  currentIndex.value = index;
};
</script>

<template>
  <section id="competitions" class="relative py-20 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
    <!-- Section Heading -->
    <div class="text-center sm:text-left mb-12">
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
        Whats On Iterate 2026?
      </h2>
    </div>

    <!-- Carousel Container -->
    <div class="relative flex items-center justify-center">
      <!-- Left Arrow Button -->
      <button
        type="button"
        @click="prevSlide"
        aria-label="Previous Competition"
        class="absolute -left-2 sm:-left-6 lg:-left-10 z-20 h-10 w-10 sm:h-12 sm:w-12 rounded-full border border-white/30 bg-purple-950/70 hover:bg-[#5E17EB] text-white flex items-center justify-center transition-all shadow-lg hover:scale-105 active:scale-95 focus:outline-hidden backdrop-blur-md"
      >
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <!-- Active Slide Card -->
      <div class="w-full max-w-3xl mx-auto">
        <transition
          mode="out-in"
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-2"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 -translate-y-2"
        >
          <div
            :key="competitions[currentIndex].id"
            class="relative rounded-3xl bg-gradient-to-b from-[#2E0B5B]/80 via-[#1C053B]/90 to-[#120227]/95 border-2 border-purple-400/40 p-8 sm:p-12 text-center backdrop-blur-xl shadow-[0_0_45px_rgba(181,23,158,0.35)]"
          >
            <!-- Glowing Backlight behind emblem -->
            <div class="absolute top-12 left-1/2 -translate-x-1/2 w-40 h-40 bg-pink-500/25 blur-3xl rounded-full pointer-events-none"></div>

            <!-- Category Emblem Icon -->
            <div class="relative flex justify-center mb-6">
              <div class="w-24 h-24 sm:w-28 sm:h-28 text-white drop-shadow-[0_5px_20px_rgba(230,57,86,0.5)]">
                <svg viewBox="0 0 120 120" fill="none" class="w-full h-full">
                  <path d="M60 10 C70 30, 95 35, 105 60 C90 75, 75 75, 60 95 C45 75, 30 75, 15 60 C25 35, 50 30, 60 10 Z" fill="url(#comp-emblem-grad)" stroke="#FFFFFF" stroke-opacity="0.3" stroke-width="2"/>
                  <circle cx="60" cy="58" r="22" fill="#150228" stroke="#E63956" stroke-width="2.5" />
                  
                  <!-- Dynamic inner icon based on iconType -->
                  <g v-if="competitions[currentIndex].iconType === 'wireframe'" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="50" y="49" width="20" height="18" rx="2" fill="none"/>
                    <line x1="50" y1="55" x2="70" y2="55"/>
                    <circle cx="54" cy="52" r="1" fill="#FFFFFF"/>
                  </g>
                  <g v-else-if="competitions[currentIndex].iconType === 'chart'" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="51" y1="67" x2="51" y2="59"/>
                    <line x1="57" y1="67" x2="57" y2="53"/>
                    <line x1="63" y1="67" x2="63" y2="49"/>
                    <line x1="69" y1="67" x2="69" y2="55"/>
                  </g>
                  <g v-else stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="54,54 49,59 54,64"/>
                    <polyline points="66,54 71,59 66,64"/>
                    <line x1="62" y1="52" x2="58" y2="66"/>
                  </g>

                  <defs>
                    <linearGradient id="comp-emblem-grad" x1="15" y1="10" x2="105" y2="95" gradientUnits="userSpaceOnUse">
                      <stop stop-color="#E63956"/>
                      <stop offset="0.5" stop-color="#B5179E"/>
                      <stop offset="1" stop-color="#5E17EB"/>
                    </linearGradient>
                  </defs>
                </svg>
              </div>
            </div>

            <!-- Title -->
            <h3 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-4">
              {{ competitions[currentIndex].title }}
            </h3>

            <!-- Description -->
            <p class="max-w-xl mx-auto text-sm sm:text-base text-purple-100/90 leading-relaxed font-normal mb-8">
              {{ competitions[currentIndex].description }}
            </p>

            <!-- CTA Button -->
            <div>
              <a
                href="/register"
                class="inline-flex items-center gap-2 px-8 py-3 rounded-full font-bold text-sm text-white bg-gradient-to-r from-[#D92662] via-[#E63956] to-[#FA5575] hover:opacity-95 shadow-lg shadow-pink-600/40 hover:scale-105 active:scale-95 transition-all ring-1 ring-white/20"
              >
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10" stroke-width="2"/>
                  <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor"/>
                </svg>
                Let's Explore
              </a>
            </div>
          </div>
        </transition>
      </div>

      <!-- Right Arrow Button -->
      <button
        type="button"
        @click="nextSlide"
        aria-label="Next Competition"
        class="absolute -right-2 sm:-right-6 lg:-right-10 z-20 h-10 w-10 sm:h-12 sm:w-12 rounded-full border border-white/30 bg-purple-950/70 hover:bg-[#5E17EB] text-white flex items-center justify-center transition-all shadow-lg hover:scale-105 active:scale-95 focus:outline-hidden backdrop-blur-md"
      >
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>

    <!-- Dot Indicators -->
    <div class="flex items-center justify-center gap-2.5 mt-8">
      <button
        v-for="(comp, idx) in competitions"
        :key="comp.id"
        @click="goToSlide(idx)"
        type="button"
        :aria-label="`Slide ${idx + 1}`"
        class="h-2.5 rounded-full transition-all duration-300"
        :class="currentIndex === idx ? 'w-8 bg-pink-500' : 'w-2.5 bg-purple-700/60 hover:bg-purple-600'"
      ></button>
    </div>
  </section>
</template>
