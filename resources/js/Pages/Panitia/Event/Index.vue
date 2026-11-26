<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import PanitiaPage from '../Partials/PanitiaPage.vue';
import Table from '../../../Components/UI/Table.vue';
import Select from '../../../Components/UI/Select.vue';
import Button from '../../../Components/UI/Button.vue';
import Badge from '../../../Components/UI/Badge.vue';
import Pagination from '../../../Components/UI/Pagination.vue';
import Modal from '../../../Components/UI/Modal.vue';
import { formatRupiah, formatTanggal } from '../../../Composables/format';

const props = defineProps({
  // Paginator Laravel: { data, links, current_page, last_page, total, per_page, ... }
  events: { type: Object, required: true },
  filters: { type: Object, default: () => ({ jenis: null }) },
  jenisLomba: { type: Array, default: () => [] }, // [{ value, label }]
});

const columns = [
  { key: 'nama_event', label: 'Nama Event' },
  { key: 'jenis_lomba', label: 'Jenis' },
  { key: 'registrasi', label: 'Periode Registrasi' },
  { key: 'biaya_pendaftaran', label: 'Biaya' },
  { key: 'jumlah_tim', label: 'Tim', align: 'center' },
];

const jenis = ref(props.filters.jenis ?? '');
const opsiFilter = computed(() => [{ value: '', label: 'Semua jenis lomba' }, ...props.jenisLomba]);

const terapkanFilter = (nilai) => {
  router.get('/panitia/event', nilai ? { jenis: nilai } : {}, { preserveState: true, replace: true });
};

const eventDihapus = ref(null);
const menghapus = ref(false);

const konfirmasiHapus = () => {
  menghapus.value = true;
  router.delete(`/panitia/event/${eventDihapus.value.id}`, {
    onFinish: () => {
      menghapus.value = false;
      eventDihapus.value = null;
    },
  });
};
</script>

<template>
  <PanitiaPage title="Kelola Event" subtitle="Daftar event lomba">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
      <div class="w-full sm:w-64">
        <Select v-model="jenis" label="Jenis Lomba" :options="opsiFilter" @change="terapkanFilter" />
      </div>
      <Button @click="router.visit('/panitia/event/create')">Tambah Event</Button>
    </div>

    <Table :columns="columns" :rows="events.data" empty-text="Belum ada event.">
      <template #cell(nama_event)="{ row }">
        <span class="font-semibold text-slate-800">{{ row.nama_event }}</span>
      </template>
      <template #cell(jenis_lomba)="{ row }">
        <Badge>{{ row.label_jenis_lomba }}</Badge>
      </template>
      <template #cell(registrasi)="{ row }">
        {{ formatTanggal(row.registrasi_mulai) }} &ndash; {{ formatTanggal(row.registrasi_selesai) }}
      </template>
      <template #cell(biaya_pendaftaran)="{ row }">
        {{ formatRupiah(row.biaya_pendaftaran) }}
      </template>

      <template #actions="{ row }">
        <Button size="sm" variant="ghost" @click="router.visit(`/panitia/event/${row.id}`)">Lihat</Button>
        <Button size="sm" variant="secondary" @click="router.visit(`/panitia/event/${row.id}/edit`)">Edit</Button>
        <Button size="sm" variant="danger" @click="eventDihapus = row">Hapus</Button>
      </template>

      <template #footer>
        <Pagination
          :links="events.links"
          :current-page="events.current_page"
          :total-pages="events.last_page"
          :total-items="events.total"
          :per-page="events.per_page"
        />
      </template>
    </Table>

    <Modal :show="eventDihapus !== null" title="Hapus Event" variant="danger" @close="eventDihapus = null">
      <p class="text-sm text-slate-600">
        Yakin ingin menghapus event <strong>{{ eventDihapus?.nama_event }}</strong>? Event yang sudah punya tim tidak bisa dihapus.
      </p>
      <template #footer>
        <div class="flex justify-end gap-3">
          <Button variant="outline" :disabled="menghapus" @click="eventDihapus = null">Batal</Button>
          <Button variant="danger" :loading="menghapus" @click="konfirmasiHapus">Hapus</Button>
        </div>
      </template>
    </Modal>
  </PanitiaPage>
</template>
