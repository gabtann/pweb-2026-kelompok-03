<script setup>
import { useForm } from '@inertiajs/vue3';
import PanitiaPage from '../Partials/PanitiaPage.vue';
import TahapForm from './Partials/TahapForm.vue';

const props = defineProps({
  event: { type: Object, required: true }, // { id, nama_event }
  tahap: { type: Object, required: true },
  jenisBerkas: { type: Array, default: () => [] }, // [{ value, label }]
});

const form = useForm({
  nomor_tahap: props.tahap.nomor_tahap,
  nama_tahap: props.tahap.nama_tahap,
  jenis_berkas: props.tahap.jenis_berkas,
  batas_mulai: props.tahap.batas_mulai,
  batas_akhir: props.tahap.batas_akhir,
  kuota_lolos: props.tahap.kuota_lolos,
});

const submit = () => form.put(`/panitia/event/${props.event.id}/tahap/${props.tahap.id}`);
</script>

<template>
  <PanitiaPage title="Edit Tahap" :subtitle="`${event.nama_event} — ${tahap.nama_tahap}`">
    <TahapForm
      :form="form"
      :jenis-berkas="jenisBerkas"
      :cancel-href="`/panitia/event/${event.id}`"
      submit-label="Simpan Perubahan"
      @submit="submit"
    />
  </PanitiaPage>
</template>
