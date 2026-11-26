<script setup>
import { useForm } from '@inertiajs/vue3';
import PanitiaPage from '../Partials/PanitiaPage.vue';
import EventForm from './Partials/EventForm.vue';

defineProps({
  jenisLomba: { type: Array, default: () => [] }, // [{ value, label }]
});

const form = useForm({
  jenis_lomba: '',
  nama_event: '',
  tema: '',
  deskripsi: '',
  gambar_pendukung: null,
  link_guidebook: '',
  syarat_ketentuan: '',
  registrasi_mulai: '',
  registrasi_selesai: '',
  waktu_mulai: '',
  waktu_selesai: '',
  biaya_pendaftaran: '',
  min_anggota: 2,
  max_anggota: 3,
});

const submit = () => form.post('/panitia/event', { forceFormData: true });
</script>

<template>
  <PanitiaPage title="Tambah Event" subtitle="Buat event lomba baru">
    <EventForm :form="form" :jenis-lomba="jenisLomba" submit-label="Simpan Event" @submit="submit" />
  </PanitiaPage>
</template>
