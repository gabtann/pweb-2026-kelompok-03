<script setup>
const stats = [
  { value: '490', label: 'Academy Participants' },
  { value: '153', label: 'Event Participants' },
  { value: '90', label: 'Media Partner' },
];
</script>

<template>
  <section id="about" class="relative py-20 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
    <!-- Section Heading -->
    <div class="text-center sm:text-left mb-10">
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
        What Is?
      </h2>
    </div>

    <!-- Main Content: Emblem Left + Text Card Right -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center mb-12">
      <!-- Left: Decorative Emblem Placeholder -->
      <div class="lg:col-span-5 flex justify-center">
        <div class="relative w-48 h-48 sm:w-64 sm:h-64 flex items-center justify-center">
          <!-- Radiant Backlight -->
          <div class="absolute inset-0 bg-gradient-to-tr from-pink-600/30 to-purple-600/40 blur-2xl rounded-full"></div>
          
          <!-- Detailed SVG Emblem Placeholder -->
          <svg viewBox="0 0 200 200" fill="none" class="relative w-full h-full drop-shadow-[0_10px_25px_rgba(181,23,158,0.4)]">
            <!-- Outer Floral Wings -->
            <path d="M100 15 C115 45, 160 50, 180 85 C160 110, 130 110, 100 145 C70 110, 40 110, 20 85 C40 50, 85 45, 100 15 Z" fill="url(#about-emblem-grad)" stroke="#FFFFFF" stroke-opacity="0.25" stroke-width="2"/>
            <path d="M100 140 C115 160, 140 165, 150 185 C130 195, 110 185, 100 200 C90 185, 70 195, 50 185 C60 165, 85 160, 100 140 Z" fill="url(#about-emblem-grad-2)" opacity="0.9"/>
            
            <!-- Inner Glowing Circle -->
            <circle cx="100" cy="88" r="34" fill="#17042E" stroke="#E63956" stroke-width="3.5" />
            
            <!-- Cyclic Arrows (Iteration Symbol) -->
            <path d="M85 82 A 16 16 0 0 1 115 82" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
            <polygon points="113,76 119,82 113,88" fill="#FFFFFF"/>
            <path d="M115 94 A 16 16 0 0 1 85 94" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
            <polygon points="87,100 81,94 87,88" fill="#FFFFFF"/>

            <defs>
              <linearGradient id="about-emblem-grad" x1="20" y1="15" x2="180" y2="145" gradientUnits="userSpaceOnUse">
                <stop stop-color="#E63956"/>
                <stop offset="0.4" stop-color="#B5179E"/>
                <stop offset="1" stop-color="#5E17EB"/>
              </linearGradient>
              <linearGradient id="about-emblem-grad-2" x1="50" y1="140" x2="150" y2="200" gradientUnits="userSpaceOnUse">
                <stop stop-color="#B5179E"/>
                <stop offset="1" stop-color="#5E17EB"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
      </div>

      <!-- Right: Glassmorphic Narrative Card -->
      <div class="lg:col-span-7">
        <div class="relative bg-purple-950/40 backdrop-blur-md border border-purple-500/25 rounded-2xl p-6 sm:p-8 shadow-xl shadow-purple-950/50">
          <p class="text-sm sm:text-base text-purple-100/90 leading-relaxed font-normal text-justify">
            <span class="font-bold text-white">Iterate</span> merupakan kegiatan perlombaan di bidang teknologi berskala nasional yang diadakan oleh divisi Pendidikan, dibawah naungan Organisasi Kemahasiswaan Informatika, Fakultas Teknik, Institute Teknologi Bontomarannu. Pada tahun 2026 ini, Iterate hadir membawa visi pembaruan melalui tema besar <span class="text-pink-300 font-semibold">"Youth Powering a New Era"</span>. Tema ini merepresentasikan langkah berani generasi muda dalam merancang ulang fondasi ekosistem digital guna menghasilkan era baru yang benar-benar berbeda dari sebelumnya.
          </p>
        </div>
      </div>
    </div>

    <!-- 3 Stat Cards Below -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <div
        v-for="stat in stats"
        :key="stat.label"
        class="bg-purple-950/30 backdrop-blur-md border border-purple-500/20 rounded-2xl py-8 px-6 text-center hover:border-purple-400/40 transition-all hover:-translate-y-1 group"
      >
        <div class="text-4xl sm:text-5xl font-black text-white tracking-tight mb-2 group-hover:text-pink-300 transition-colors">
          {{ stat.value }}
        </div>
        <div class="text-xs sm:text-sm font-medium text-purple-200/80 uppercase tracking-wider">
          {{ stat.label }}
        </div>
      </div>
    </div>
  </section>
</template>
