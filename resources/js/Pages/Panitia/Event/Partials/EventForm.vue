<script setup>
// Form event bersama untuk Create dan Edit. `form` adalah objek useForm dari parent.
import { router } from '@inertiajs/vue3';
import Input from '../../../../Components/UI/Input.vue';
import Select from '../../../../Components/UI/Select.vue';
import Textarea from '../../../../Components/UI/Textarea.vue';
import Button from '../../../../Components/UI/Button.vue';

defineProps({
  form: { type: Object, required: true },
  jenisLomba: { type: Array, default: () => [] }, // [{ value, label }]
  submitLabel: { type: String, default: 'Simpan' },
  gambarUrl: { type: String, default: null }, // gambar saat ini (Edit)
  cancelHref: { type: String, default: '/panitia/event' },
});

defineEmits(['submit']);
</script>

<template>
  <form class="space-y-5 bg-white rounded-2xl border border-slate-200 p-6" @submit.prevent="$emit('submit')">
    <Select
      v-model="form.jenis_lomba"
      label="Jenis Lomba"
      placeholder="Pilih jenis lomba..."
      :options="jenisLomba"
      :error="form.errors.jenis_lomba"
      required
    />

    <Input v-model="form.nama_event" label="Nama Event" :error="form.errors.nama_event" required />
    <Input v-model="form.tema" label="Tema" :error="form.errors.tema" required />
    <Textarea v-model="form.deskripsi" label="Deskripsi" :error="form.errors.deskripsi" required />

    <div class="space-y-2">
      <img v-if="gambarUrl" :src="gambarUrl" alt="Gambar saat ini" class="h-32 rounded-xl border border-slate-200 object-cover" />
      <Input
        v-model="form.gambar_pendukung"
        type="file"
        label="Gambar Pendukung"
        accept=".jpg,.jpeg,.png,.webp"
        :hint="gambarUrl ? 'Kosongkan jika tidak ingin mengganti gambar. JPG/PNG/WEBP, maks 2 MB.' : 'JPG/PNG/WEBP, maks 2 MB.'"
        :error="form.errors.gambar_pendukung"
        :required="!gambarUrl"
      />
    </div>

    <Input v-model="form.link_guidebook" type="url" label="Link Guidebook" placeholder="https://..." :error="form.errors.link_guidebook" required />
    <Textarea v-model="form.syarat_ketentuan" label="Syarat dan Ketentuan" :error="form.errors.syarat_ketentuan" required />

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <Input v-model="form.registrasi_mulai" type="datetime-local" label="Registrasi Mulai" :error="form.errors.registrasi_mulai" required />
      <Input v-model="form.registrasi_selesai" type="datetime-local" label="Registrasi Selesai" :error="form.errors.registrasi_selesai" required />
      <Input v-model="form.waktu_mulai" type="datetime-local" label="Waktu Mulai Lomba" :error="form.errors.waktu_mulai" required />
      <Input v-model="form.waktu_selesai" type="datetime-local" label="Waktu Selesai Lomba" :error="form.errors.waktu_selesai" required />
    </div>

    <Input
      v-model="form.biaya_pendaftaran"
      type="number"
      label="Biaya Pendaftaran (Rp)"
      hint="Kosongkan jika gratis."
      :error="form.errors.biaya_pendaftaran"
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <Input v-model="form.min_anggota" type="number" label="Minimal Anggota (termasuk ketua)" :error="form.errors.min_anggota" required />
      <Input v-model="form.max_anggota" type="number" label="Maksimal Anggota (termasuk ketua)" :error="form.errors.max_anggota" required />
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
      <Button variant="outline" type="button" :disabled="form.processing" @click="router.visit(cancelHref)">Batal</Button>
      <Button type="submit" :loading="form.processing">{{ submitLabel }}</Button>
    </div>
  </form>
</template>
