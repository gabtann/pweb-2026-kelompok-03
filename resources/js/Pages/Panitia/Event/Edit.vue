<script setup>
import { useForm } from '@inertiajs/vue3';
import PanitiaPage from '../Partials/PanitiaPage.vue';
import EventForm from './Partials/EventForm.vue';

const props = defineProps({
  event: { type: Object, required: true },
  jenisLomba: { type: Array, default: () => [] }, // [{ value, label }]
});

const form = useForm({
  jenis_lomba: props.event.jenis_lomba,
  nama_event: props.event.nama_event,
  tema: props.event.tema,
  deskripsi: props.event.deskripsi,
  gambar_pendukung: null, // opsional: kosong = pertahankan gambar lama
  link_guidebook: props.event.link_guidebook,
  syarat_ketentuan: props.event.syarat_ketentuan,
  registrasi_mulai: props.event.registrasi_mulai,
  registrasi_selesai: props.event.registrasi_selesai,
  waktu_mulai: props.event.waktu_mulai,
  waktu_selesai: props.event.waktu_selesai,
  biaya_pendaftaran: props.event.biaya_pendaftaran ?? '',
  min_anggota: props.event.min_anggota,
  max_anggota: props.event.max_anggota,
});

// Update yang membawa file harus POST + _method=put (PHP tidak membaca multipart di PUT).
const submit = () => {
  form
    .transform((data) => ({ ...data, _method: 'put' }))
    .post(`/panitia/event/${props.event.id}`, { forceFormData: true });
};
</script>

<template>
  <PanitiaPage title="Edit Event" :subtitle="event.nama_event">
    <EventForm
      :form="form"
      :jenis-lomba="jenisLomba"
      :gambar-url="event.gambar_url"
      :cancel-href="`/panitia/event/${event.id}`"
      submit-label="Simpan Perubahan"
      @submit="submit"
    />
  </PanitiaPage>
</template>
