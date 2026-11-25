<script setup>
import { ref } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import {
  Button,
  Input,
  Textarea,
  Select,
  Badge,
  Avatar,
  Skeleton,
  Card,
  Modal,
  Table,
  Pagination,
  Alert,
} from './index.js';

// Active View Tab: 'components' | 'admin-layout' | 'public-layout'
const activeTab = ref('components');
const adminRoute = ref('dashboard');
const publicRoute = ref('competitions');
const showDeleteModal = ref(false);
const showVerifyModal = ref(false);
const isTableLoading = ref(false);
const showEmptyTable = ref(false);
const alertDismissed = ref(false);

// Form mock state
const form = ref({
  teamName: 'Tim Phinisi Tech',
  email: 'andi.pratama@student.unhas.ac.id',
  whatsapp: '081234567890',
  password: 'SecretPassword123',
  category: 'uiux',
  description: 'Proyek inovasi maritim berbasis AI dan computer vision.',
  file: null,
  errorInput: 'format_salah',
});

const categoryOptions = [
  { value: 'uiux', label: 'UI/UX Design Competition' },
  { value: 'ict', label: 'ICT Business Development' },
  { value: 'software', label: 'Software Development' },
  { value: 'seminar', label: 'Seminar Nasional AI' },
];

// Table mock data
const tableColumns = [
  { key: 'name', label: 'Nama Tim' },
  { key: 'leader', label: 'Ketua Tim' },
  { key: 'competition', label: 'Kompetisi' },
  { key: 'status', label: 'Status Dokumen' },
];

const mockTeams = ref([
  {
    id: 1,
    name: 'Phinisi Tech',
    leader: 'Andi Pratama',
    competition: 'UI/UX Design',
    status: 'terverifikasi',
    email: 'andi.pratama@student.unhas.ac.id',
  },
  {
    id: 2,
    name: 'Celebes Dev',
    leader: 'Siti Nurhaliza',
    competition: 'Software Development',
    status: 'menunggu',
    email: 'siti.nur@student.unhas.ac.id',
  },
  {
    id: 3,
    name: 'Nusantara AI',
    leader: 'James Michael',
    competition: 'ICT Business',
    status: 'ditolak',
    email: 'james71n@gmail.com',
  },
  {
    id: 4,
    name: 'Garuda Innovators',
    leader: 'Budi Santoso',
    competition: 'UI/UX Design',
    status: 'terverifikasi',
    email: 'budi.santoso@univ.ac.id',
  },
]);

// Pagination mock
const currentPage = ref(1);
const totalPages = ref(5);
const totalItems = ref(48);

const toggleButtonLoading = () => {
  isLoadingBtn.value = true;
  setTimeout(() => {
    isLoadingBtn.value = false;
  }, 2500);
};
</script>

<template>
  <div class="min-h-screen bg-[#F7F6FD] text-slate-800 pb-20 font-sans">
    <!-- Top Header Bar -->
    <header class="bg-white border-b border-purple-100/80 sticky top-0 z-30 shadow-2xs backdrop-blur-md bg-white/95">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-[#4A159D] to-[#8E179E] flex items-center justify-center text-white font-black text-lg shadow-sm shadow-purple-500/20">
            IT
          </div>
          <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
              Iterate 2026
              <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-[#EBE5FF] text-[#5E17EB]">
                UI Components
              </span>
            </h1>
            <p class="text-xs text-slate-500">Jobdesk 1 Reusable Components Showcase</p>
          </div>
        </div>

        <!-- Tab Switcher Navigation -->
        <div class="flex items-center gap-1.5 p-1 bg-purple-50 rounded-xl border border-purple-100/80">
          <button
            type="button"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer',
              activeTab === 'components'
                ? 'bg-white text-[#5E17EB] shadow-2xs font-bold'
                : 'text-slate-600 hover:text-[#5E17EB]',
            ]"
            @click="activeTab = 'components'"
          >
            1. UI Components
          </button>
          <button
            type="button"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer',
              activeTab === 'admin-layout'
                ? 'bg-white text-[#5E17EB] shadow-2xs font-bold'
                : 'text-slate-600 hover:text-[#5E17EB]',
            ]"
            @click="activeTab = 'admin-layout'"
          >
            2. AdminLayout Preview
          </button>
          <button
            type="button"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer',
              activeTab === 'public-layout'
                ? 'bg-white text-[#5E17EB] shadow-2xs font-bold'
                : 'text-slate-600 hover:text-[#5E17EB]',
            ]"
            @click="activeTab = 'public-layout'"
          >
            2. PublicLayout Preview
          </button>
        </div>

        <!-- User Identity Preview (Iterate 2026 style) -->
        <div class="flex items-center gap-3">
          <div class="text-right hidden sm:block">
            <p class="text-xs font-bold text-slate-800 leading-tight">James</p>
            <p class="text-[11px] text-slate-400">Peserta</p>
          </div>
          <Avatar name="James" status="online" size="md" />
        </div>
      </div>
    </header>

    <!-- Main Container for Components Showcase -->
    <main v-if="activeTab === 'components'" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 space-y-10">
      <!-- Welcome Banner (Matching Figma Screenshot 1) -->
      <div class="rounded-2xl p-6 sm:p-8 bg-gradient-to-r from-[#4A159D] via-[#6B17B6] to-[#8E179E] text-white shadow-lg shadow-purple-900/15 relative overflow-hidden">
        <div class="relative z-10 max-w-xl space-y-2">
          <p class="text-xs font-bold tracking-widest uppercase text-purple-200">
            ITERATE 2026
          </p>
          <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
            Welcome, Developer.
          </h2>
          <p class="text-sm text-purple-100/90 leading-relaxed">
            Ini adalah galeri visual pengujian seluruh komponen UI reusable sesuai desain sistem Figma Iterate 2026.
          </p>
        </div>
        <!-- Decorative SVG badge -->
        <div class="absolute -right-6 -bottom-8 opacity-20 pointer-events-none">
          <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2L1 21h22L12 2zm0 4.5l7.5 13H4.5L12 6.5z" />
          </svg>
        </div>
      </div>

      <!-- SECTION 1: BUTTONS -->
      <section class="space-y-4">
        <div class="border-b border-purple-100 pb-2">
          <h2 class="text-lg font-bold text-slate-900">1. Button Components</h2>
          <p class="text-xs text-slate-500">Mendukung varian warna Iterate 2026, status loading dengan spinner, disabled, ukuran sm/md/lg, dan slot icon.</p>
        </div>

        <Card no-padding>
          <div class="p-6 space-y-6">
            <!-- Variants -->
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Variasi Gaya (Variants)</p>
              <div class="flex flex-wrap items-center gap-3">
                <Button variant="primary">Primary (Register Now)</Button>
                <Button variant="outline">Outline (See Details)</Button>
                <Button variant="secondary">Secondary</Button>
                <Button variant="danger">Danger (Hapus / Tolak)</Button>
                <Button variant="ghost">Ghost Button</Button>
                <Button disabled>Disabled State</Button>
              </div>
            </div>

            <!-- Loading State -->
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Status Loading Interaktif</p>
              <div class="flex flex-wrap items-center gap-3">
                <Button
                  variant="primary"
                  :loading="isLoadingBtn"
                  @click="toggleButtonLoading"
                >
                  {{ isLoadingBtn ? 'Menyimpan Data...' : 'Klik untuk Test Loading Spinner' }}
                </Button>
                <Button variant="outline" loading>Loading Outline</Button>
                <Button variant="danger" loading>Deleting...</Button>
              </div>
            </div>

            <!-- Sizes & Icons -->
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Ukuran & Slot Icon</p>
              <div class="flex flex-wrap items-center gap-3">
                <Button size="sm" variant="primary">
                  <template #icon-left>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </template>
                  Small Button
                </Button>

                <Button size="md" variant="primary">
                  <template #icon-left>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </template>
                  Medium (Default)
                </Button>

                <Button size="lg" variant="outline">
                  Large Button
                  <template #icon-right>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                  </template>
                </Button>
              </div>
            </div>
          </div>
        </Card>
      </section>

      <!-- SECTION 2: FORM CONTROLS -->
      <section class="space-y-4">
        <div class="border-b border-purple-100 pb-2">
          <h2 class="text-lg font-bold text-slate-900">2. Form Controls (Input, Textarea, Select)</h2>
          <p class="text-xs text-slate-500">Mendukung ikon di dalam input (seperti amplop di Figma), toggle intip password, file upload, dan pesan error validasi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Form Card 1: Pendaftaran Tim -->
          <Card tag="DATA TIM" title="Formulir Pendaftaran Kompetisi">
            <div class="space-y-4">
              <!-- Regular Input with Left Icon (Envelope Mail Icon as in Figma) -->
              <Input
                v-model="form.teamName"
                label="Nama Tim"
                placeholder="Contoh: Phinisi Hackers"
                required
              >
                <template #icon-left>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </template>
              </Input>

              <!-- Email with envelope icon -->
              <Input
                v-model="form.email"
                type="email"
                label="Email Ketua Tim"
                placeholder="andi.pratama@student.unhas.ac.id"
                required
              >
                <template #icon-left>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </template>
              </Input>

              <!-- Phone Input (tel) -->
              <Input
                v-model="form.whatsapp"
                type="tel"
                label="Nomor WhatsApp Ketua"
                placeholder="08xxxxxxxxxx"
                required
              >
                <template #icon-left>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </template>
              </Input>

              <!-- Password with toggle visibility -->
              <Input
                v-model="form.password"
                type="password"
                label="Password Akun"
                placeholder="Masukkan kata sandi..."
                hint="Klik ikon mata di kanan untuk melihat/sembunyikan sandi."
              />

              <!-- File Upload (Iterate 2026 Style) -->
              <Input
                type="file"
                label="Upload Bukti Pembayaran / KTM"
                placeholder="Pilih berkas PDF atau Foto KTM"
                accept=".pdf,.jpg,.png"
                required
              />
            </div>

            <template #footer>
              <Button variant="outline">Kembali</Button>
              <Button variant="primary">Kirim Pendaftaran</Button>
            </template>
          </Card>

          <!-- Form Card 2: Select, Textarea & Error State -->
          <Card tag="VALIDASI & STATUS" title="Dropdown, Textarea, & Error State">
            <div class="space-y-4">
              <!-- Select Component -->
              <Select
                v-model="form.category"
                label="Kategori Kompetisi"
                :options="categoryOptions"
                required
                hint="Pilih cabang lomba yang ingin diikuti tim."
              />

              <!-- Textarea Component -->
              <Textarea
                v-model="form.description"
                label="Ringkasan Proposal / Deskripsi Karya"
                placeholder="Ceritakan gambaran singkat inovasi yang diajukan..."
                rows="3"
                hint="Maksimal 500 kata."
              />

              <!-- Input with Validation Error -->
              <Input
                v-model="form.errorInput"
                label="Simulasi Validasi Error Laravel"
                placeholder="Coba ubah..."
                error="Format email tidak valid atau sudah terdaftar di sistem!"
              />

              <!-- Disabled Input -->
              <Input
                model-value="Akun Terverifikasi (Read Only)"
                label="Input Kondisi Disabled"
                disabled
              />
            </div>
          </Card>
        </div>
      </section>

      <!-- SECTION 3: BADGES & AVATARS -->
      <section class="space-y-4">
        <div class="border-b border-purple-100 pb-2">
          <h2 class="text-lg font-bold text-slate-900">3. Badges & Avatar Components</h2>
          <p class="text-xs text-slate-500">Label status untuk role/tahapan verifikasi dan avatar profil dengan inisial nama otomatis.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Badges -->
          <Card title="Status Badges (Pill & Dot)">
            <div class="space-y-4">
              <div>
                <p class="text-xs text-slate-400 mb-2">Varian Warna Status (Semantic)</p>
                <div class="flex flex-wrap items-center gap-2.5">
                  <Badge variant="purple">Peserta</Badge>
                  <Badge variant="purple" dot>Panitia Admin</Badge>
                  <Badge variant="success" dot>Terverifikasi</Badge>
                  <Badge variant="warning" dot>Menunggu Verifikasi</Badge>
                  <Badge variant="danger" dot>Ditolak</Badge>
                  <Badge variant="info" dot>Dalam Proses</Badge>
                  <Badge variant="gray">Nonaktif</Badge>
                </div>
              </div>

              <div>
                <p class="text-xs text-slate-400 mb-2">Floating Badge (Seminar / Lomba)</p>
                <div class="flex items-center gap-3 p-4 bg-slate-100 rounded-xl">
                  <Badge variant="white">Lomba</Badge>
                  <Badge variant="white">Seminar</Badge>
                  <Badge variant="white" dot>Live Now</Badge>
                </div>
              </div>
            </div>
          </Card>

          <!-- Avatars -->
          <Card title="Avatars & Inisial Fallback">
            <div class="space-y-4">
              <div>
                <p class="text-xs text-slate-400 mb-2">Fallback Inisial Otomatis dari Nama</p>
                <div class="flex items-center gap-3">
                  <Avatar name="James" size="xs" />
                  <Avatar name="Andi Pratama" size="sm" />
                  <Avatar name="Siti Nurhaliza" size="md" status="online" />
                  <Avatar name="Budi Santoso" size="lg" status="busy" />
                  <Avatar name="Iterate Panitia" size="xl" status="away" />
                </div>
              </div>

              <div>
                <p class="text-xs text-slate-400 mb-2">Bentuk Kotak (Rounded-xl) & Foto URL</p>
                <div class="flex items-center gap-3">
                  <Avatar
                    src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80"
                    name="Jane Doe"
                    size="lg"
                    status="online"
                  />
                  <Avatar
                    name="Admin Lab"
                    shape="rounded"
                    size="lg"
                    status="online"
                  />
                </div>
              </div>
            </div>
          </Card>
        </div>
      </section>

      <!-- SECTION 4: CARDS & EVENT PREVIEW (Figma Screenshot 3 Style) -->
      <section class="space-y-4">
        <div class="border-b border-purple-100 pb-2">
          <h2 class="text-lg font-bold text-slate-900">4. Cards — Event Competition Card Preview</h2>
          <p class="text-xs text-slate-500">Penerapan Card.vue, Badge.vue, dan Button.vue yang meniru kartu kompetisi pada Screenshot Figma.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Event Card 1: UI/UX -->
          <Card no-padding>
            <div class="relative h-44 bg-gradient-to-tr from-blue-600 via-indigo-600 to-purple-700 overflow-hidden">
              <img
                src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&auto=format&fit=crop&q=80"
                alt="UI/UX"
                class="w-full h-full object-cover opacity-80"
              />
              <div class="absolute top-3 left-3">
                <Badge variant="white">Lomba</Badge>
              </div>
            </div>
            <div class="p-5 space-y-3">
              <h3 class="text-base font-bold text-slate-900">UI/UX Iterate 2026</h3>
              <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                Kompetisi perancangan antarmuka aplikasi berorientasi pemecahan masalah pengguna dengan pengalaman visual modern.
              </p>
              <div class="space-y-2 pt-2">
                <Button variant="outline" block size="sm">See Details</Button>
                <Button variant="primary" block size="sm">Register Now</Button>
              </div>
            </div>
          </Card>

          <!-- Event Card 2: ICT Business -->
          <Card no-padding>
            <div class="relative h-44 bg-gradient-to-tr from-purple-800 via-purple-600 to-pink-600 overflow-hidden">
              <img
                src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&auto=format&fit=crop&q=80"
                alt="ICT"
                class="w-full h-full object-cover opacity-80"
              />
              <div class="absolute top-3 left-3">
                <Badge variant="white">Lomba</Badge>
              </div>
            </div>
            <div class="p-5 space-y-3">
              <h3 class="text-base font-bold text-slate-900">ICT Business Development</h3>
              <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                Kompetisi pengembangan model bisnis berbasis teknologi digital yang siap diimplementasikan dan bernilai pasar tinggi.
              </p>
              <div class="space-y-2 pt-2">
                <Button variant="outline" block size="sm">See Details</Button>
                <Button variant="primary" block size="sm">Register Now</Button>
              </div>
            </div>
          </Card>

          <!-- Event Card 3: Software Development -->
          <Card no-padding>
            <div class="relative h-44 bg-gradient-to-tr from-indigo-900 via-purple-900 to-slate-900 overflow-hidden">
              <img
                src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&auto=format&fit=crop&q=80"
                alt="Software"
                class="w-full h-full object-cover opacity-80"
              />
              <div class="absolute top-3 left-3">
                <Badge variant="white">Lomba</Badge>
              </div>
            </div>
            <div class="p-5 space-y-3">
              <h3 class="text-base font-bold text-slate-900">Software Development</h3>
              <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                Pengembangan aplikasi berbasis web atau mobile untuk menyelesaikan tantangan maritim dan industri kampus nusantara.
              </p>
              <div class="space-y-2 pt-2">
                <Button variant="outline" block size="sm">See Details</Button>
                <Button variant="primary" block size="sm">Register Now</Button>
              </div>
            </div>
          </Card>
        </div>
      </section>

      <!-- SECTION 5: DATA TABLE & SKELETON -->
      <section class="space-y-4">
        <div class="border-b border-purple-100 pb-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
          <div>
            <h2 class="text-lg font-bold text-slate-900">5. Table Component (Admin & Data Tim)</h2>
            <p class="text-xs text-slate-500">Mendukung konfigurasi kolom fleksibel, scoped slot `#cell`, status loading skeleton, dan empty state.</p>
          </div>
          <div class="flex items-center gap-2">
            <Button
              size="sm"
              variant="outline"
              @click="isTableLoading = !isTableLoading"
            >
              {{ isTableLoading ? 'Stop Loading' : 'Toggle Skeleton Loading' }}
            </Button>
            <Button
              size="sm"
              variant="outline"
              @click="showEmptyTable = !showEmptyTable"
            >
              {{ showEmptyTable ? 'Tampilkan Data' : 'Toggle Empty State' }}
            </Button>
          </div>
        </div>

        <!-- Table Component -->
        <Table
          :columns="tableColumns"
          :rows="showEmptyTable ? [] : mockTeams"
          :loading="isTableLoading"
          empty-text="Belum ada pendaftaran tim pada kompetisi ini."
        >
          <!-- Custom Cell: Nama Tim -->
          <template #cell(name)="{ row }">
            <div class="flex items-center gap-2.5">
              <Avatar :name="row.name" size="sm" />
              <div>
                <p class="font-bold text-slate-900 leading-tight">{{ row.name }}</p>
                <p class="text-xs text-slate-400">{{ row.email }}</p>
              </div>
            </div>
          </template>

          <!-- Custom Cell: Status -->
          <template #cell(status)="{ value }">
            <Badge v-if="value === 'terverifikasi'" variant="success" dot>Terverifikasi</Badge>
            <Badge v-else-if="value === 'menunggu'" variant="warning" dot>Menunggu</Badge>
            <Badge v-else-if="value === 'ditolak'" variant="danger" dot>Ditolak</Badge>
            <Badge v-else variant="gray">{{ value }}</Badge>
          </template>

          <!-- Dedicated Actions Slot -->
          <template #actions="{ row }">
            <Button
              size="sm"
              variant="outline"
              @click="showVerifyModal = true"
            >
              Periksa
            </Button>
            <Button
              size="sm"
              variant="danger"
              @click="showDeleteModal = true"
            >
              Hapus
            </Button>
          </template>

          <!-- Table Footer Pagination -->
          <template #footer>
            <Pagination
              :current-page="currentPage"
              :total-pages="totalPages"
              :total-items="totalItems"
              @page-change="currentPage = $event"
            />
          </template>
        </Table>
      </section>

      <!-- SECTION 6: MODALS & DIALOGS -->
      <section class="space-y-4">
        <div class="border-b border-purple-100 pb-2">
          <h2 class="text-lg font-bold text-slate-900">6. Modal / Dialog Component</h2>
          <p class="text-xs text-slate-500">Dialog konfirmasi hapus data / verifikasi dengan dukungan tombol Escape, tombol Close (X), dan backdrop click.</p>
        </div>

        <Card title="Live Interactive Dialogs">
          <div class="flex flex-wrap items-center gap-4">
            <Button variant="danger" @click="showDeleteModal = true">
              Buka Modal Konfirmasi Hapus (Danger)
            </Button>
            <Button variant="primary" @click="showVerifyModal = true">
              Buka Modal Verifikasi Berkas (Default)
            </Button>
          </div>
        </Card>

        <!-- Danger Modal (Delete Confirmation) -->
        <Modal
          v-model:show="showDeleteModal"
          title="Konfirmasi Penghapusan Akun"
          variant="danger"
          max-width="md"
        >
          <p class="text-sm text-slate-600 leading-relaxed">
            Apakah Anda yakin ingin menghapus data akun ini? Seluruh berkas pendaftaran dan riwayat tim yang terhubung akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
          </p>
          <template #footer>
            <Button variant="outline" @click="showDeleteModal = false">Batal</Button>
            <Button variant="danger" @click="showDeleteModal = false">Ya, Hapus Data</Button>
          </template>
        </Modal>

        <!-- Verify Modal -->
        <Modal
          v-model:show="showVerifyModal"
          title="Verifikasi Dokumen Tim"
          max-width="lg"
        >
          <div class="space-y-3">
            <p class="text-sm text-slate-600">
              Periksa kelengkapan berkas KTM dan Pakta Integritas sebelum menyetujui status pendaftaran tim.
            </p>
            <div class="p-3.5 bg-purple-50 rounded-xl border border-purple-100 space-y-1.5 text-xs">
              <p class="font-bold text-[#5E17EB]">Dokumen Tim Phinisi Tech:</p>
              <p class="text-slate-600">• Kartu Tanda Mahasiswa (KTM) — <span class="text-emerald-600 font-semibold">Valid</span></p>
              <p class="text-slate-600">• Bukti Pembayaran Pendaftaran — <span class="text-emerald-600 font-semibold">Valid</span></p>
            </div>
          </div>
          <template #footer>
            <Button variant="outline" @click="showVerifyModal = false">Tutup</Button>
            <Button variant="danger" @click="showVerifyModal = false">Tolak Berkas</Button>
            <Button variant="primary" @click="showVerifyModal = false">Verifikasi & Setujui</Button>
          </template>
        </Modal>
      </section>

      <!-- SECTION 7: ALERTS -->
      <section class="space-y-4">
        <div class="border-b border-purple-100 pb-2">
          <h2 class="text-lg font-bold text-slate-900">7. Alert / Notification Banner</h2>
          <p class="text-xs text-slate-500">Feedback flash message untuk notifikasi keberhasilan, peringatan, atau error.</p>
        </div>

        <div class="space-y-3">
          <Alert type="success" title="Pendaftaran Berhasil!" dismissible>
            Data tim dan dokumen persyaratan telah berhasil diunggah. Silakan menunggu proses verifikasi oleh panitia.
          </Alert>

          <Alert type="warning" title="Periode Pendaftaran Segera Berakhir" dismissible>
            Pendaftaran kompetisi UI/UX Iterate 2026 akan ditutup dalam 2 hari ke depan. Pastikan data tim sudah lengkap.
          </Alert>

          <Alert type="danger" title="Verifikasi Dokumen Ditolak" dismissible>
            Berkas bukti pembayaran buram dan tidak terbaca. Harap unggah ulang dokumen yang lebih jelas.
          </Alert>

          <Alert type="info" title="Informasi Penting Iterate 2026">
            Technical Meeting daring akan dilaksanakan pada hari Sabtu pukul 09.00 WITA melalui platform Zoom.
          </Alert>
        </div>
      </section>
    </main>

    <!-- TAB 2: AdminLayout Live Preview (Jobdesk 2) -->
    <div v-else-if="activeTab === 'admin-layout'" class="min-h-screen">
      <AdminLayout
        :title="adminRoute === 'dashboard' ? 'Dashboard Administrasi' : adminRoute === 'accounts' ? 'Kelola Akun Pengguna' : adminRoute === 'events' ? 'Kelola Event & Kategori' : adminRoute === 'teams' ? 'Kelola Tim & Verifikasi Berkas' : 'Pengumuman'"
        :subtitle="'Pratinjau Interaktif AdminLayout (Jobdesk 2) — Modul Aktif: ' + adminRoute"
        :current-route="adminRoute"
        @navigate="adminRoute = $event.id"
      >
        <!-- Notice Banner -->
        <Alert type="info" title="Preview Mode: AdminLayout (Jobdesk 2)">
          Sidebar kiri responsif dengan warna ungu khas Iterate 2026, navbar atas, identitas admin, dan layout ini siap digunakan oleh Jobdesk 4 (Akun), Jobdesk 5 (Event), dan Jobdesk 6 (Tim). Klik menu di sidebar untuk menguji navigasi aktif!
        </Alert>

        <!-- Stats Grid (Simulasi Konten Dashboard) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <Card no-padding>
            <div class="p-5 flex items-center justify-between">
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Akun</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">128</h3>
                <span class="text-[11px] text-emerald-600 font-semibold mt-1 inline-block">↑ 12 minggu ini</span>
              </div>
              <div class="p-3 bg-purple-50 text-[#5E17EB] rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
              </div>
            </div>
          </Card>

          <Card no-padding>
            <div class="p-5 flex items-center justify-between">
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Event</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">4</h3>
                <span class="text-[11px] text-purple-600 font-semibold mt-1 inline-block">3 Lomba, 1 Seminar</span>
              </div>
              <div class="p-3 bg-purple-50 text-[#5E17EB] rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
              </div>
            </div>
          </Card>

          <Card no-padding>
            <div class="p-5 flex items-center justify-between">
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tim Terdaftar</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">48</h3>
                <span class="text-[11px] text-amber-600 font-semibold mt-1 inline-block">16 Butuh Verifikasi</span>
              </div>
              <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
              </div>
            </div>
          </Card>

          <Card no-padding>
            <div class="p-5 flex items-center justify-between">
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Berkas Terverifikasi</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">32</h3>
                <span class="text-[11px] text-emerald-600 font-semibold mt-1 inline-block">67% Dari total pendaftar</span>
              </div>
              <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
            </div>
          </Card>
        </div>

        <!-- Section: Tim Terbaru (Menggunakan Table.vue) -->
        <Card title="Daftar Tim Terbaru untuk Verifikasi Berkas" tag="DATA TERKINI">
          <template #actions>
            <Button size="sm" variant="outline">Lihat Semua Tim</Button>
          </template>
          <Table
            :columns="tableColumns"
            :rows="mockTeams"
            empty-text="Belum ada data tim."
          >
            <template #cell(name)="{ row }">
              <div class="flex items-center gap-2.5">
                <Avatar :name="row.name" size="sm" />
                <div>
                  <p class="font-bold text-slate-900 leading-tight">{{ row.name }}</p>
                  <p class="text-xs text-slate-400">{{ row.email }}</p>
                </div>
              </div>
            </template>
            <template #cell(status)="{ value }">
              <Badge v-if="value === 'terverifikasi'" variant="success" dot>Terverifikasi</Badge>
              <Badge v-else-if="value === 'menunggu'" variant="warning" dot>Menunggu</Badge>
              <Badge v-else-if="value === 'ditolak'" variant="danger" dot>Ditolak</Badge>
              <Badge v-else variant="gray">{{ value }}</Badge>
            </template>
            <template #actions>
              <Button size="sm" variant="outline">Periksa Berkas</Button>
            </template>
          </Table>
        </Card>
      </AdminLayout>
    </div>

    <!-- TAB 3: PublicLayout (Portal Peserta) Live Preview (Jobdesk 2) -->
    <div v-else-if="activeTab === 'public-layout'" class="min-h-screen">
      <PublicLayout
        :current-route="publicRoute"
        @navigate="publicRoute = $event.id"
      >
        <!-- Notice Banner -->
        <Alert type="info" title="Preview Mode: PublicLayout (Portal Peserta)" class="mb-6">
          Layout ini 100% mengadopsi desain Figma Iterate 2026: Sidebar ungu peserta di kiri (Profile, Event: All Competitions & My Events, Announcement), header identitas James (Peserta), dan area konten kompetisi.
        </Alert>

        <!-- All Competitions View (Matches Figma Screenshot 1) -->
        <div class="space-y-6">
          <h2 class="text-xl font-bold text-slate-800 tracking-tight">
            All Competitions
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6 max-w-5xl">
            <!-- Card 1: UI/UX (Figma Design) -->
            <Card no-padding>
              <div class="h-48 bg-gradient-to-tr from-blue-600 via-indigo-600 to-purple-700 overflow-hidden relative">
                <img
                  src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&auto=format&fit=crop&q=80"
                  alt="UI/UX"
                  class="w-full h-full object-cover opacity-80"
                />
              </div>
              <div class="p-6 space-y-3">
                <h3 class="text-xl font-bold text-slate-900">UI/UX</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                  Menghadirkan pakar riset kelautan komputasional dan arsitek AI terkemuka....
                </p>
                <div class="space-y-2.5 pt-2">
                  <Button variant="outline" block size="md">See Details</Button>
                  <Button variant="primary" block size="md">Register Now</Button>
                </div>
              </div>
            </Card>

            <!-- Card 2: ICT Business Development (Figma Design) -->
            <Card no-padding>
              <div class="h-48 bg-gradient-to-tr from-purple-800 via-purple-600 to-pink-600 overflow-hidden relative">
                <img
                  src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&auto=format&fit=crop&q=80"
                  alt="ICT"
                  class="w-full h-full object-cover opacity-80"
                />
              </div>
              <div class="p-6 space-y-3">
                <h3 class="text-xl font-bold text-slate-900">ICT Business Development</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                  Menghadirkan pakar riset kelautan komputasional dan arsitek AI terkemuka....
                </p>
                <div class="space-y-2.5 pt-2">
                  <Button variant="outline" block size="md">See Details</Button>
                  <Button variant="primary" block size="md">Register Now</Button>
                </div>
              </div>
            </Card>

            <!-- Card 3: Software Development (Figma Design) -->
            <Card no-padding>
              <div class="h-48 bg-gradient-to-tr from-indigo-900 via-purple-900 to-slate-900 overflow-hidden relative">
                <img
                  src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&auto=format&fit=crop&q=80"
                  alt="Software"
                  class="w-full h-full object-cover opacity-80"
                />
              </div>
              <div class="p-6 space-y-3">
                <h3 class="text-xl font-bold text-slate-900">Software Development</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                  Menghadirkan pakar riset kelautan komputasional dan arsitek AI terkemuka....
                </p>
                <div class="space-y-2.5 pt-2">
                  <Button variant="outline" block size="md">See Details</Button>
                  <Button variant="primary" block size="md">Register Now</Button>
                </div>
              </div>
            </Card>
          </div>
        </div>
      </PublicLayout>
    </div>

  </div>
</template>

