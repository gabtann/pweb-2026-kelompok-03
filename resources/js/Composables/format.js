// Helper format tampilan (tanggal dan rupiah) untuk halaman panitia.

export function formatTanggal(iso) {
  if (!iso) return '-';
  return new Date(iso).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
}

export function formatRupiah(nilai) {
  if (nilai === null || nilai === undefined) return 'Gratis';
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(nilai);
}
