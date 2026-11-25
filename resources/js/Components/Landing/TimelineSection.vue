<script setup>
const timelineSteps = [
  {
    title: 'Grand Launching',
    date: '20 Nov 2026',
    position: 'top', // For desktop alternating rail
  },
  {
    title: 'Open Registration',
    date: '30 Nov 2026',
    position: 'bottom',
  },
  {
    title: 'Proposal Submission',
    date: '02 Jan 2027',
    position: 'top',
  },
  {
    title: 'Presentation Submission',
    date: '28 Jan 2027',
    position: 'bottom',
  },
];
</script>

<template>
  <section id="timeline" class="relative py-20 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
    <!-- Section Heading -->
    <div class="text-center sm:text-left mb-12">
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
        Timeline
      </h2>
    </div>

    <!-- Timeline Outer Frame -->
    <div class="rounded-3xl bg-purple-950/30 border border-purple-500/25 p-6 sm:p-10 lg:p-12 backdrop-blur-md shadow-xl shadow-purple-950/50">
      
      <!-- DESKTOP TIMELINE (Alternating Top / Bottom) -->
      <div class="hidden lg:block relative py-12">
        <!-- Horizontal Neon Line Rail -->
        <div class="absolute top-1/2 left-4 right-4 h-1 bg-gradient-to-r from-purple-700 via-pink-500 to-purple-600 rounded-full -translate-y-1/2"></div>

        <!-- 4 Steps Grid -->
        <div class="grid grid-cols-4 gap-6 relative">
          <div
            v-for="(step, idx) in timelineSteps"
            :key="step.title"
            class="flex flex-col items-center"
          >
            <!-- Card Above (if position === 'top') -->
            <div class="h-28 w-full flex flex-col justify-end items-center mb-6">
              <div
                v-if="step.position === 'top'"
                class="w-full bg-[#18042F]/90 border border-purple-400/40 rounded-2xl p-4 text-center shadow-lg shadow-purple-950/60 hover:border-pink-400 transition-all hover:-translate-y-1"
              >
                <h4 class="text-sm font-bold text-white mb-1 tracking-tight">
                  {{ step.title }}
                </h4>
                <p class="text-xs font-semibold text-pink-300">
                  {{ step.date }}
                </p>
              </div>
            </div>

            <!-- Central Neon Node -->
            <div class="relative flex items-center justify-center my-1 z-10">
              <div class="w-6 h-6 rounded-full bg-pink-500 flex items-center justify-center ring-4 ring-[#18042F] shadow-[0_0_15px_#E63956]">
                <div class="w-2 h-2 rounded-full bg-white"></div>
              </div>
            </div>

            <!-- Card Below (if position === 'bottom') -->
            <div class="h-28 w-full flex flex-col justify-start items-center mt-6">
              <div
                v-if="step.position === 'bottom'"
                class="w-full bg-[#18042F]/90 border border-purple-400/40 rounded-2xl p-4 text-center shadow-lg shadow-purple-950/60 hover:border-pink-400 transition-all hover:translate-y-1"
              >
                <h4 class="text-sm font-bold text-white mb-1 tracking-tight">
                  {{ step.title }}
                </h4>
                <p class="text-xs font-semibold text-pink-300">
                  {{ step.date }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- MOBILE & TABLET TIMELINE (Vertical Stream) -->
      <div class="lg:hidden relative pl-6 sm:pl-8 border-l-2 border-pink-500/70 space-y-6 my-2">
        <div
          v-for="step in timelineSteps"
          :key="step.title"
          class="relative group"
        >
          <!-- Vertical Dot Node -->
          <div class="absolute -left-[31px] sm:-left-[39px] top-4 w-5 h-5 rounded-full bg-pink-500 flex items-center justify-center ring-4 ring-[#110224]">
            <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
          </div>

          <!-- Step Box -->
          <div class="bg-[#18042F]/90 border border-purple-400/30 rounded-2xl p-4 sm:p-5 shadow-md">
            <h4 class="text-base font-bold text-white mb-1">
              {{ step.title }}
            </h4>
            <p class="text-xs font-semibold text-pink-300">
              {{ step.date }}
            </p>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>
