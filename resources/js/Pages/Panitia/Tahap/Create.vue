<script setup>
import { useForm } from '@inertiajs/vue3';
import PanitiaPage from '../Partials/PanitiaPage.vue';
import TahapForm from './Partials/TahapForm.vue';

const props = defineProps({
  event: { type: Object, required: true }, // { id, nama_event }
  nomorTahapBerikutnya: { type: Number, default: 1 },
  jenisBerkas: { type: Array, default: () => [] }, // [{ value, label }]
});

const form = useForm({
  nomor_tahap: props.nomorTahapBerikutnya,
  nama_tahap: '',
  jenis_berkas: '',
  batas_mulai: '',
  batas_akhir: '',
  kuota_lolos: 10,
});

const submit = () => form.post(`/panitia/event/${props.event.id}/tahap`);
</script>

<template>
  <PanitiaPage title="Tambah Tahap" :subtitle="event.nama_event">
    <TahapForm
      :form="form"
      :jenis-berkas="jenisBerkas"
      :cancel-href="`/panitia/event/${event.id}`"
      submit-label="Simpan Tahap"
      @submit="submit"
    />
  </PanitiaPage>
</template>
