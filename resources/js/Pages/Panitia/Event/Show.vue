<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import PanitiaPage from '../Partials/PanitiaPage.vue';
import Card from '../../../Components/UI/Card.vue';
import Table from '../../../Components/UI/Table.vue';
import Button from '../../../Components/UI/Button.vue';
import Badge from '../../../Components/UI/Badge.vue';
import Modal from '../../../Components/UI/Modal.vue';
import { formatRupiah, formatTanggal } from '../../../Composables/format';

const props = defineProps({
  event: { type: Object, required: true },
  tahap: { type: Array, default: () => [] },
});

const guidebookAman = computed(() => /^https?:\/\//i.test(props.event.link_guidebook ?? ''));

const columns = [
  { key: 'nomor_tahap', label: 'No', width: '60px' },
  { key: 'nama_tahap', label: 'Nama Tahap' },
  { key: 'jenis_berkas', label: 'Berkas' },
  { key: 'batas', label: 'Batas Pengumpulan' },
  { key: 'kuota_lolos', label: 'Kuota Lolos', align: 'center' },
  { key: 'jumlah_pengumpulan', label: 'Pengumpulan', align: 'center' },
  { key: 'sudah_diumumkan', label: 'Pengumuman' },
];

const tahapDihapus = ref(null);
const menghapus = ref(false);

const konfirmasiHapus = () => {
  menghapus.value = true;
  router.delete(`/panitia/event/${props.event.id}/tahap/${tahapDihapus.value.id}`, {
    onFinish: () => {
      menghapus.value = false;
      tahapDihapus.value = null;
    },
  });
};
</script>

<template>
  <PanitiaPage title="Detail Event" :subtitle="event.nama_event">
    <div class="flex items-center justify-between gap-3">
      <Button variant="outline" @click="router.visit('/panitia/event')">Kembali</Button>
      <Button @click="router.visit(`/panitia/event/${event.id}/edit`)">Edit Event</Button>
    </div>

    <Card :title="event.nama_event" :subtitle="event.tema" :tag="event.label_jenis_lomba">
      <img
        v-if="event.gambar_url"
        :src="event.gambar_url"
        :alt="event.nama_event"
        class="mb-5 max-h-64 rounded-xl border border-slate-200 object-cover"
      />

      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
        <div class="sm:col-span-2">
          <dt class="font-semibold text-slate-500">Deskripsi</dt>
          <dd class="whitespace-pre-line text-slate-800">{{ event.deskripsi }}</dd>
        </div>
        <div class="sm:col-span-2">
          <dt class="font-semibold text-slate-500">Syarat dan Ketentuan</dt>
          <dd class="whitespace-pre-line text-slate-800">{{ event.syarat_ketentuan }}</dd>
        </div>
        <div>
          <dt class="font-semibold text-slate-500">Registrasi</dt>
          <dd>{{ formatTanggal(event.registrasi_mulai) }} &ndash; {{ formatTanggal(event.registrasi_selesai) }}</dd>
        </div>
        <div>
          <dt class="font-semibold text-slate-500">Pelaksanaan</dt>
          <dd>{{ formatTanggal(event.waktu_mulai) }} &ndash; {{ formatTanggal(event.waktu_selesai) }}</dd>
        </div>
        <div>
          <dt class="font-semibold text-slate-500">Biaya Pendaftaran</dt>
          <dd>{{ formatRupiah(event.biaya_pendaftaran) }}</dd>
        </div>
        <div>
          <dt class="font-semibold text-slate-500">Jumlah Anggota per Tim (termasuk ketua)</dt>
          <dd>{{ event.min_anggota }} &ndash; {{ event.max_anggota }} orang</dd>
        </div>
        <div>
          <dt class="font-semibold text-slate-500">Jumlah Tim Terdaftar</dt>
          <dd>{{ event.jumlah_tim }}</dd>
        </div>
        <div>
          <dt class="font-semibold text-slate-500">Guidebook</dt>
          <dd>
            <!-- Defense in depth: hanya render link http(s), walau server sudah memvalidasi. -->
            <a v-if="guidebookAman" :href="event.link_guidebook" target="_blank" rel="noopener noreferrer" class="text-[#5E17EB] underline">Buka guidebook</a>
            <span v-else class="text-slate-400">-</span>
          </dd>
        </div>
      </dl>
    </Card>

    <Card title="Tahap Lomba" no-padding>
      <template #actions>
        <Button size="sm" @click="router.visit(`/panitia/event/${event.id}/tahap/create`)">Tambah Tahap</Button>
      </template>

      <Table :columns="columns" :rows="tahap" empty-text="Belum ada tahap untuk event ini.">
        <template #cell(batas)="{ row }">
          {{ formatTanggal(row.batas_mulai) }} &ndash; {{ formatTanggal(row.batas_akhir) }}
        </template>
        <template #cell(jenis_berkas)="{ row }">
          <Badge variant="info">{{ row.jenis_berkas.toUpperCase() }}</Badge>
        </template>
        <template #cell(sudah_diumumkan)="{ row }">
          <Badge :variant="row.sudah_diumumkan ? 'success' : 'gray'">
            {{ row.sudah_diumumkan ? 'Diumumkan' : 'Belum' }}
          </Badge>
        </template>

        <template #actions="{ row }">
          <Button size="sm" variant="secondary" @click="router.visit(`/panitia/event/${event.id}/tahap/${row.id}/edit`)">Edit</Button>
          <Button size="sm" variant="danger" @click="tahapDihapus = row">Hapus</Button>
        </template>
      </Table>
    </Card>

    <Modal :show="tahapDihapus !== null" title="Hapus Tahap" variant="danger" @close="tahapDihapus = null">
      <p class="text-sm text-slate-600">
        Yakin ingin menghapus tahap <strong>{{ tahapDihapus?.nama_tahap }}</strong>?
        Tahap yang sudah punya pengumpulan atau sudah diumumkan tidak bisa dihapus.
      </p>
      <template #footer>
        <div class="flex justify-end gap-3">
          <Button variant="outline" :disabled="menghapus" @click="tahapDihapus = null">Batal</Button>
          <Button variant="danger" :loading="menghapus" @click="konfirmasiHapus">Hapus</Button>
        </div>
      </template>
    </Modal>
  </PanitiaPage>
</template>
