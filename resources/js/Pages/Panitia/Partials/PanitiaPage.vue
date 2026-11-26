<script setup>
// Pembungkus AdminLayout untuk semua halaman panitia: menghubungkan user dari shared props,
// navigasi sidebar ke Inertia, dan menampilkan flash success/error.
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Alert from '../../../Components/UI/Alert.vue';

defineProps({
  title: { type: String, default: 'Kelola Event' },
  subtitle: { type: String, default: '' },
  currentRoute: { type: String, default: 'events' },
});

const page = usePage();

const adminUser = computed(() => {
  const user = page.props.auth?.user;

  return {
    name: user?.nama_lengkap ?? '-',
    role: user?.role === 'panitia' ? 'Panitia Admin' : (user?.role ?? ''),
    email: user?.email ?? '',
  };
});

const flash = computed(() => page.props.flash ?? {});

const navigate = (item) => router.visit(item.href);
</script>

<template>
  <AdminLayout
    :title="title"
    :subtitle="subtitle"
    :current-route="currentRoute"
    :user="adminUser"
    @navigate="navigate"
  >
    <Alert v-if="flash.success" type="success" dismissible>{{ flash.success }}</Alert>
    <Alert v-if="flash.error" type="danger" dismissible>{{ flash.error }}</Alert>

    <slot />
  </AdminLayout>
</template>
