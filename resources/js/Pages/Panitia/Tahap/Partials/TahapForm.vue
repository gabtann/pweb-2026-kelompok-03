<script setup>
// Form tahap bersama untuk Create dan Edit. `form` adalah objek useForm dari parent.
import { router } from '@inertiajs/vue3';
import Input from '../../../../Components/UI/Input.vue';
import Select from '../../../../Components/UI/Select.vue';
import Button from '../../../../Components/UI/Button.vue';

defineProps({
  form: { type: Object, required: true },
  jenisBerkas: { type: Array, default: () => [] }, // [{ value, label }]
  submitLabel: { type: String, default: 'Simpan' },
  cancelHref: { type: String, required: true },
});

defineEmits(['submit']);
</script>

<template>
  <form class="space-y-5 bg-white rounded-2xl border border-slate-200 p-6" @submit.prevent="$emit('submit')">
    <Input v-model="form.nomor_tahap" type="number" label="Nomor Tahap" :error="form.errors.nomor_tahap" required />
    <Input v-model="form.nama_tahap" label="Nama Tahap" placeholder="mis. Proposal" :error="form.errors.nama_tahap" required />
    <Select
      v-model="form.jenis_berkas"
      label="Jenis Berkas"
      placeholder="Pilih jenis berkas..."
      :options="jenisBerkas"
      :error="form.errors.jenis_berkas"
      required
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <Input v-model="form.batas_mulai" type="datetime-local" label="Batas Mulai" :error="form.errors.batas_mulai" required />
      <Input v-model="form.batas_akhir" type="datetime-local" label="Batas Akhir" :error="form.errors.batas_akhir" required />
    </div>

    <Input v-model="form.kuota_lolos" type="number" label="Kuota Lolos" :error="form.errors.kuota_lolos" required />

    <div class="flex items-center justify-end gap-3 pt-2">
      <Button variant="outline" type="button" :disabled="form.processing" @click="router.visit(cancelHref)">Batal</Button>
      <Button type="submit" :loading="form.processing">{{ submitLabel }}</Button>
    </div>
  </form>
</template>
