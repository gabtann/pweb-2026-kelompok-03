<script setup>
import { ref } from 'vue';

const faqs = [
  {
    question: 'Siapa saja yang dapat mengikuti perlombaan di Iterate 2026?',
    answer: 'Iterate 2026 terbuka untuk seluruh mahasiswa aktif jenjang D3/D4/S1 dari seluruh perguruan tinggi negeri maupun swasta di Indonesia, dibuktikan dengan Kartu Tanda Mahasiswa (KTM) atau surat keterangan aktif.',
  },
  {
    question: 'Berapa jumlah anggota dalam satu tim?',
    answer: 'Setiap tim beranggotakan 1 hingga 3 orang mahasiswa dari perguruan tinggi yang sama. Anggota diperbolehkan berasal dari program studi atau angkatan yang berbeda.',
  },
  {
    question: 'Apakah satu peserta boleh mendaftar di lebih dari satu cabang lomba?',
    answer: 'Setiap peserta hanya diperkenankan menjadi ketua di satu cabang lomba, namun dapat menjadi anggota di maksimal dua cabang lomba yang berbeda selama jadwal presentasi tidak bentrok.',
  },
  {
    question: 'Bagaimana alur pengumpulan berkas dan karya?',
    answer: 'Pengumpulan proposal dan karya dilakukan secara online melalui Portal Peserta (My Events) setelah tim dinyatakan terverifikasi oleh panitia.',
  },
];

const openIndex = ref(null);

const toggleFaq = (index) => {
  openIndex.value = openIndex.value === index ? null : index;
};
</script>

<template>
  <section id="faq" class="relative py-20 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <!-- Section Heading -->
    <div class="text-center sm:text-left mb-10">
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
        Frequently Asked Questions
      </h2>
      <p class="text-sm text-purple-200/70 mt-2">
        Pertanyaan umum seputar pendaftaran, ketentuan tim, dan teknis kompetisi.
      </p>
    </div>

    <!-- FAQ Accordion List -->
    <div class="space-y-4">
      <div
        v-for="(faq, idx) in faqs"
        :key="faq.question"
        class="rounded-2xl border border-purple-500/25 bg-purple-950/30 backdrop-blur-md overflow-hidden transition-all duration-200"
      >
        <button
          type="button"
          @click="toggleFaq(idx)"
          class="w-full py-5 px-6 flex items-center justify-between text-left focus:outline-hidden hover:bg-white/5 transition-colors"
        >
          <span class="text-sm sm:text-base font-bold text-white tracking-tight">
            {{ faq.question }}
          </span>
          <span class="ml-4 shrink-0 text-pink-400 transition-transform duration-300" :class="{ 'rotate-180': openIndex === idx }">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>
          </span>
        </button>

        <transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="max-h-0 opacity-0"
          enter-to-class="max-h-96 opacity-100"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="max-h-96 opacity-100"
          leave-to-class="max-h-0 opacity-0"
        >
          <div v-show="openIndex === idx" class="px-6 pb-6 text-xs sm:text-sm text-purple-100/80 leading-relaxed border-t border-purple-500/10 pt-4">
            {{ faq.answer }}
          </div>
        </transition>
      </div>
    </div>
  </section>
</template>
