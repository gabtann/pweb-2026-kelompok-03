<script setup>
const quickLinks = [
  { name: 'Home', href: '#home' },
  { name: 'About', href: '#about' },
  { name: 'Timeline', href: '#timeline' },
  { name: 'FAQ', href: '#faq' },
];

const importantLinks = [
  { name: 'Guidebook', href: '#' },
];
</script>

<template>
  <footer class="bg-[#0B0118] text-slate-300 py-14 border-t border-purple-900/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row items-start justify-between gap-10">
        <!-- Brand & Copyright -->
        <div class="space-y-4 max-w-sm">
          <div class="inline-block">
            <span class="text-3xl font-black tracking-tight text-white drop-shadow-[0_2px_10px_rgba(255,255,255,0.2)]">
              Iterate <span class="bg-gradient-to-r from-pink-400 to-purple-400 bg-clip-text text-transparent">2026</span>
            </span>
          </div>
          <p class="text-xs text-slate-400 font-medium">
            &copy; Iterate 2026. All Rights Reserved.
          </p>
        </div>

        <!-- Links Columns -->
        <div class="flex items-start gap-16 sm:gap-24">
          <!-- Quick Links -->
          <div class="space-y-3">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider">
              Quick Links
            </h4>
            <ul class="space-y-2 text-xs">
              <li v-for="link in quickLinks" :key="link.name">
                <a :href="link.href" class="text-slate-400 hover:text-pink-400 transition-colors">
                  {{ link.name }}
                </a>
              </li>
            </ul>
          </div>

          <!-- Important Links -->
          <div class="space-y-3">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider">
              Important Links
            </h4>
            <ul class="space-y-2 text-xs">
              <li v-for="link in importantLinks" :key="link.name">
                <a :href="link.href" class="text-slate-400 hover:text-pink-400 transition-colors">
                  {{ link.name }}
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
