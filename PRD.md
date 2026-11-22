# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Frontend Development — Web Pendaftaran Event

**Technology Stack:** Vue 3, Inertia.js, Tailwind CSS, Laravel Blade  
**Developer:** Aji  
**Role:** Frontend Developer  
**Project:** Web Pendaftaran Event

---

# 1. Project Overview

Web Pendaftaran Event merupakan aplikasi berbasis web yang dikembangkan untuk memfasilitasi proses pengelolaan dan pendaftaran kompetisi atau event. Aplikasi memiliki dua jenis pengguna utama, yaitu Admin/Panitia dan Peserta.

Dalam proyek ini, AJI bertanggung jawab pada pengembangan sisi frontend berdasarkan pembagian jobdesk tim. Fokus pekerjaan meliputi pengembangan reusable UI components, layout utama, landing page, serta halaman administrasi untuk pengelolaan akun, event, dan tim peserta.

Pengembangan frontend dilakukan menggunakan Vue 3, Inertia.js, dan Tailwind CSS dengan Laravel sebagai framework backend. Desain UI/UX yang telah disediakan oleh tim akan menjadi acuan utama implementasi antarmuka.

PRD ini secara khusus mendokumentasikan ruang lingkup pekerjaan AJI dan tidak mencakup keseluruhan pengembangan aplikasi.

# 2. Objectives

Tujuan pengembangan frontend adalah:

1. Mengimplementasikan desain UI/UX menjadi antarmuka website yang fungsional dan responsif.
2. Membangun reusable components agar tampilan antarhalaman konsisten dan mudah dipelihara.
3. Menyediakan struktur layout yang dapat digunakan kembali oleh halaman-halaman aplikasi.
4. Mengembangkan halaman landing page sesuai desain UI/UX.
5. Mengembangkan antarmuka administrasi untuk pengelolaan akun dan event.
6. Menyediakan antarmuka bagi admin untuk memeriksa dokumen dan memantau progress tim.
7. Mengintegrasikan seluruh halaman yang menjadi tanggung jawab frontend dengan backend Laravel melalui Inertia.js.

# 3. Technology Stack

| Teknologi | Fungsi |
|---|---|
| Vue 3 | Framework utama untuk membangun halaman dan komponen frontend |
| Inertia.js | Penghubung antara Laravel sebagai backend dan Vue sebagai frontend |
| Tailwind CSS | Styling, layout, responsive design, dan konsistensi tampilan |
| Laravel Blade | Root HTML template untuk aplikasi Inertia |
| JavaScript | Logika interaksi dan pengolahan state pada frontend |

## 3.1. Batasan Penggunaan Teknologi

- Vue 3 digunakan untuk membuat halaman, layout frontend, dan reusable components.
- Inertia.js digunakan untuk navigasi halaman, menerima data dari Laravel, serta mengirimkan form.
- Tailwind CSS digunakan untuk seluruh kebutuhan styling.
- Blade digunakan sebagai template utama aplikasi Inertia, bukan sebagai template utama setiap halaman.
- Tidak menggunakan Bootstrap atau framework CSS tambahan.
- Tidak membangun backend, database, atau business logic Laravel sebagai bagian dari jobdesk frontend.

# 4. Development Scope

## 4.1. Scope Pekerjaan AJI

| No. | Jobdesk | Deliverable |
|---|---|---|
| 1 | Komponen UI Reusable | Kumpulan komponen frontend yang dapat digunakan berulang |
| 2 | Layout Blade Utama | Root template dan struktur layout aplikasi |
| 3 | Layout Landing Page sesuai UI/UX | Halaman landing page yang sesuai desain |
| 4 | Halaman CRUD Akun (Admin) | Antarmuka pengelolaan akun oleh admin |
| 5 | Halaman CRUD Event (Admin) | Antarmuka pengelolaan event oleh admin |
| 6 | Halaman CRUD Kelola Tim untuk Verifikasi Berkas dan Melihat Progress Tim (Admin) | Antarmuka pengelolaan tim, verifikasi dokumen, dan pemantauan progress |

## 4.2. Out of Scope

Pekerjaan berikut tidak termasuk tanggung jawab implementasi AJI berdasarkan pembagian jobdesk:

- Implementasi autentikasi dan registrasi backend.
- Middleware dan role authorization backend.
- Database, migration, model, dan seeder.
- Controller dan business logic Laravel.
- CRUD backend untuk akun, event, dan tim.
- Implementasi halaman peserta yang ditugaskan kepada anggota tim lain.
- Pengelolaan penyimpanan file pada server.
- Penentuan kebijakan dan aturan bisnis aplikasi.

AJI tetap bertanggung jawab mengintegrasikan antarmuka dengan endpoint dan data yang disediakan anggota tim backend.

---

# 5. Functional Requirements Berdasarkan Jobdesk

# 5.1. JOBDESK 1 — Komponen UI Reusable

**Tujuan:** Membuat kumpulan komponen antarmuka yang dapat digunakan kembali pada halaman landing page dan halaman administrasi.

Komponen harus memiliki tampilan konsisten, mudah digunakan, serta dapat dikembangkan tanpa harus menulis ulang elemen UI yang sama.

## 5.1.1. Daftar Komponen

### A. Button

Komponen tombol untuk menjalankan aksi pengguna.

Requirement:
- Mendukung variasi tombol Primary, Secondary, Danger, dan Outline sesuai kebutuhan desain.
- Mendukung ukuran tombol yang berbeda.
- Memiliki kondisi normal, hover, focus, disabled, dan loading.
- Mendukung ikon jika diperlukan.
- Dapat menerima teks atau label tombol melalui props.
- Dapat menjalankan aksi melalui event handler.

Contoh penggunaan:
- Tambah akun.
- Edit data.
- Hapus data.
- Simpan perubahan.
- Verifikasi dokumen.
- Tolak dokumen.

### B. Input Field

Komponen input untuk menerima data dari pengguna.

Requirement:
- Mendukung label.
- Mendukung placeholder.
- Mendukung tipe input seperti text, email, password, date, dan number sesuai kebutuhan.
- Menampilkan pesan error validasi.
- Mendukung kondisi disabled dan readonly.
- Memiliki tampilan focus dan error.
- Dapat menerima nilai dan perubahan data melalui props dan event.

### C. Textarea

Komponen input untuk data berbentuk teks panjang.

Requirement:
- Mendukung label dan placeholder.
- Mendukung pengaturan jumlah baris.
- Menampilkan pesan error.
- Mendukung disabled dan readonly.

### D. Select / Dropdown

Komponen pemilihan data dari beberapa pilihan.

Requirement:
- Mendukung label dan placeholder.
- Menampilkan daftar opsi.
- Mendukung pemilihan nilai.
- Menampilkan error validasi.
- Digunakan untuk pilihan role, kategori, status, atau data lain sesuai kebutuhan halaman.

### E. Badge / Status Label

Komponen untuk menampilkan status suatu data.

Requirement:
- Mendukung variasi warna berdasarkan status.
- Memiliki ukuran yang konsisten.
- Menampilkan teks status.

Contoh status:
- Aktif.
- Tidak Aktif.
- Menunggu Verifikasi.
- Terverifikasi.
- Ditolak.
- Dalam Proses.

### F. Card

Komponen pembungkus informasi.

Requirement:
- Memiliki struktur yang fleksibel.
- Mendukung judul, deskripsi, konten, dan aksi.
- Menggunakan styling yang konsisten.
- Dapat digunakan pada dashboard, ringkasan data, atau informasi event.

### G. Modal / Dialog

Komponen untuk menampilkan interaksi yang membutuhkan perhatian pengguna.

Requirement:
- Mendukung judul dan konten dinamis.
- Memiliki tombol konfirmasi dan pembatalan.
- Dapat ditutup melalui tombol close atau pembatalan.
- Memiliki tampilan yang konsisten.
- Mendukung dialog konfirmasi penghapusan dan verifikasi.

### H. Table

Komponen untuk menampilkan data dalam bentuk tabel.

Requirement:
- Mendukung header dan baris data dinamis.
- Mendukung kolom aksi.
- Mendukung tampilan data kosong.
- Mendukung tampilan loading.
- Mendukung overflow horizontal pada layar kecil.
- Dapat digunakan pada halaman akun, event, dan tim.

### I. Pagination

Komponen navigasi halaman data.

Requirement:
- Menampilkan informasi halaman aktif.
- Mendukung navigasi halaman sebelumnya dan berikutnya.
- Menampilkan nomor halaman jika disediakan backend.
- Menyesuaikan data berdasarkan pagination dari backend.

### J. Alert / Notification

Komponen untuk memberikan feedback kepada pengguna.

Requirement:
- Mendukung pesan sukses.
- Mendukung pesan error.
- Mendukung pesan informasi.
- Dapat menampilkan feedback setelah tindakan berhasil atau gagal.

## 5.1.2. Acceptance Criteria

- Komponen dapat digunakan kembali pada beberapa halaman.
- Tampilan komponen konsisten.
- Komponen mendukung variasi sesuai kebutuhan.
- Komponen tidak bergantung pada data statis tertentu.
- Komponen memiliki kondisi interaksi yang sesuai.
- Komponen dapat ditampilkan dengan baik pada desktop dan mobile.

---

# 5.2. JOBDESK 2 — Layout Blade Utama

**Tujuan:** Menyiapkan struktur utama aplikasi yang menjadi fondasi bagi halaman-halaman frontend.

Karena proyek menggunakan Vue 3 dan Inertia.js, Blade digunakan sebagai root template aplikasi, sedangkan layout visual halaman dibuat menggunakan Vue.

## 5.2.1. Root Blade Template

File utama:
- `resources/views/app.blade.php`

Requirement:
- Menyediakan struktur dasar HTML.
- Memuat metadata dan konfigurasi dasar halaman.
- Menyediakan elemen root untuk aplikasi Vue/Inertia.
- Memuat asset frontend melalui mekanisme Vite.
- Mendukung rendering halaman Inertia.
- Memastikan aplikasi dapat memuat halaman Vue dengan benar.

## 5.2.2. Vue Layout

Layout digunakan sebagai pembungkus halaman yang memiliki struktur tampilan serupa.

Requirement:
- Menyediakan struktur layout utama.
- Memiliki area konten dinamis.
- Mendukung penggunaan komponen secara berulang.
- Memisahkan layout halaman publik dan layout admin apabila diperlukan.
- Memastikan setiap halaman menggunakan layout yang sesuai.

## 5.2.3. Admin Layout

Admin Layout digunakan pada halaman administrasi.

Elemen:
- Sidebar navigasi.
- Navbar/header.
- Area konten utama.
- Area notifikasi.
- Identitas pengguna atau akun admin sesuai desain.

Requirement:
- Sidebar memiliki navigasi menuju modul administrasi yang tersedia.
- Navigasi aktif memiliki indikator visual.
- Area konten menyesuaikan halaman yang dibuka.
- Sidebar dapat menyesuaikan tampilan pada layar kecil.
- Layout dapat digunakan oleh halaman CRUD akun, CRUD event, dan pengelolaan tim.

## 5.2.4. Landing Page Layout

Layout landing page memiliki struktur berbeda dari halaman admin.

Requirement:
- Memiliki navigasi publik.
- Memiliki area konten utama.
- Memiliki footer.
- Mengikuti desain UI/UX landing page.
- Tidak menggunakan sidebar admin.

## 5.2.5. Acceptance Criteria

- Aplikasi dapat memuat halaman Inertia melalui root Blade template.
- Layout dapat digunakan kembali.
- Layout admin dan publik tidak tercampur.
- Navigasi berfungsi sesuai route yang tersedia.
- Layout dapat menyesuaikan ukuran layar.

---

# 5.3. JOBDESK 3 — Layout Landing Page sesuai UI/UX

**Tujuan:** Mengimplementasikan tampilan landing page berdasarkan desain UI/UX yang disediakan oleh tim.

Landing page merupakan halaman publik yang menjadi titik awal pengguna dalam mengenal event dan kompetisi yang diselenggarakan.

## 5.3.1. Struktur Halaman

Implementasi mengikuti bagian yang tersedia pada desain UI/UX.

Bagian yang perlu diperhatikan:

### A. Navbar

Requirement:
- Menampilkan identitas atau logo website.
- Menyediakan navigasi menuju bagian halaman.
- Menyediakan tombol Login atau Register sesuai desain.
- Memiliki navigasi responsif.

### B. Hero Section

Requirement:
- Menampilkan headline utama.
- Menampilkan deskripsi singkat event.
- Menampilkan visual utama sesuai desain.
- Menyediakan CTA jika tersedia.
- CTA mengarahkan pengguna ke halaman atau bagian yang sesuai.

### C. About Us

Requirement:
- Menampilkan informasi mengenai penyelenggara atau event.
- Menyajikan teks dan visual sesuai desain.
- Memiliki struktur konten yang mudah dibaca.

### D. Competition List

Requirement:
- Menampilkan daftar kompetisi yang tersedia.
- Setiap item menampilkan informasi sesuai desain.
- Menyediakan navigasi menuju detail kompetisi jika route tersedia.
- Mendukung data dinamis dari backend.

### E. Timeline

Requirement:
- Menampilkan tahapan atau jadwal pelaksanaan kompetisi.
- Menyajikan urutan waktu secara jelas.
- Menyesuaikan tampilan dengan data yang disediakan backend.

### F. Registration Time

Requirement:
- Menampilkan periode pendaftaran.
- Menampilkan informasi tanggal mulai dan berakhir jika tersedia.
- Menampilkan status periode pendaftaran berdasarkan data backend.

### G. FAQ

Requirement:
- Menampilkan daftar pertanyaan dan jawaban.
- Mendukung interaksi expand/collapse jika sesuai desain.
- Menjaga keterbacaan konten.

### H. Rules

Requirement:
- Menampilkan informasi persyaratan atau peraturan kompetisi.
- Menyajikan konten dalam struktur yang mudah dipahami.
- Mendukung tautan dokumen panduan jika tersedia.

### I. Footer

Requirement:
- Menampilkan informasi penyelenggara.
- Menampilkan informasi kontak atau tautan yang tersedia.
- Menampilkan informasi tambahan sesuai desain.

## 5.3.2. Interaction Requirements

- Navigasi mengarahkan pengguna ke bagian atau halaman yang sesuai.
- CTA dapat diklik dan menjalankan tujuan navigasinya.
- FAQ dapat dibuka dan ditutup jika menggunakan accordion.
- Kartu kompetisi dapat mengarahkan pengguna ke detail kompetisi.
- Menu navigasi mobile dapat dibuka dan ditutup.

## 5.3.3. Data Requirements

Data yang kemungkinan dibutuhkan dari backend:
- Informasi event.
- Daftar kompetisi.
- Informasi timeline.
- Periode pendaftaran.
- FAQ.
- Informasi aturan kompetisi.

Struktur data final mengikuti kesepakatan dengan Backend Developer.

## 5.3.4. Acceptance Criteria

- Tampilan sesuai desain UI/UX.
- Seluruh section yang tersedia pada desain telah diimplementasikan.
- Navigasi dan CTA berfungsi.
- Data dinamis dapat ditampilkan.
- Halaman responsif pada desktop, tablet, dan mobile.
- Tidak terdapat elemen yang keluar dari batas layout.

---

# 5.4. JOBDESK 4 — Halaman CRUD Akun (Admin)

**Tujuan:** Mengimplementasikan antarmuka administrasi yang memungkinkan admin melihat dan mengelola data akun pengguna.

Catatan: Jobdesk AJI adalah implementasi halaman frontend. Proses CRUD dan pengelolaan data tetap ditangani backend sesuai pembagian tugas tim.

## 5.4.1. Halaman Daftar Akun

Requirement:
- Menampilkan daftar akun dalam bentuk tabel.
- Menampilkan informasi akun berdasarkan field yang disediakan backend.
- Menampilkan nama dan email jika tersedia.
- Menampilkan role pengguna.
- Menampilkan status akun jika tersedia.
- Menyediakan kolom aksi.
- Menyediakan pencarian.
- Menyediakan pagination.

## 5.4.2. Form Tambah Akun

Requirement:
- Menampilkan form input akun.
- Menyediakan field sesuai kontrak data backend.
- Menampilkan label dan placeholder.
- Menampilkan validasi input.
- Menampilkan feedback ketika proses berhasil atau gagal.
- Mengirim data menggunakan Inertia.

Field aktual mengikuti struktur akun yang ditentukan backend.

## 5.4.3. Form Edit Akun

Requirement:
- Menampilkan data akun yang akan diedit.
- Memungkinkan perubahan field yang diperbolehkan.
- Memiliki tombol simpan dan batal.
- Menampilkan error validasi dari backend.
- Memperbarui tampilan setelah perubahan berhasil.

## 5.4.4. Hapus Akun

Requirement:
- Menyediakan aksi hapus pada setiap akun.
- Menampilkan dialog konfirmasi sebelum penghapusan.
- Mengirim permintaan penghapusan ke backend.
- Menampilkan notifikasi keberhasilan atau kegagalan.

## 5.4.5. Interaction Requirements

- Tombol tambah membuka halaman atau modal tambah akun sesuai desain.
- Tombol edit membuka form dengan data yang sesuai.
- Tombol hapus memunculkan dialog konfirmasi.
- Pencarian mengubah daftar data sesuai mekanisme backend.
- Pagination mengarahkan pengguna ke halaman data berikutnya.
- Form menampilkan error validasi ketika backend mengembalikan kesalahan.

## 5.4.6. Acceptance Criteria

- Daftar akun dapat ditampilkan dari backend.
- Form tambah dan edit dapat digunakan.
- Aksi hapus memiliki konfirmasi.
- Pencarian dan pagination berfungsi sesuai dukungan backend.
- Loading, empty, success, dan error state tersedia.
- Tampilan mengikuti desain UI/UX.

---

# 5.5. JOBDESK 5 — Halaman CRUD Event (Admin)

**Tujuan:** Mengimplementasikan antarmuka administrasi untuk melihat dan mengelola data event atau kompetisi.

## 5.5.1. Halaman Daftar Event

Requirement:
- Menampilkan daftar event atau kompetisi dalam tabel atau bentuk tampilan sesuai desain.
- Menampilkan informasi event.
- Menampilkan nama event.
- Menampilkan kategori jika tersedia.
- Menampilkan tanggal atau periode pendaftaran jika tersedia.
- Menampilkan status event jika tersedia.
- Menyediakan kolom aksi.
- Menyediakan pencarian dan pagination apabila didukung backend.

## 5.5.2. Form Tambah Event

Requirement:
- Menyediakan form penambahan event.
- Menyediakan input sesuai struktur data backend.
- Mendukung input teks, deskripsi, tanggal, dropdown, dan field lainnya sesuai kebutuhan.
- Menampilkan validasi.
- Menampilkan feedback proses.
- Mengirim data melalui Inertia.

## 5.5.3. Form Edit Event

Requirement:
- Menampilkan data event yang dipilih.
- Mengisi form dengan nilai sebelumnya.
- Memungkinkan perubahan data.
- Menampilkan error validasi.
- Memiliki tombol simpan dan batal.

## 5.5.4. Detail Event

Requirement:
- Menampilkan informasi event yang dipilih.
- Menampilkan detail berdasarkan data yang diberikan backend.
- Menyediakan navigasi kembali ke daftar event.
- Menyediakan aksi edit jika diizinkan oleh desain dan hak akses.

## 5.5.5. Hapus Event

Requirement:
- Menyediakan tombol hapus.
- Menampilkan dialog konfirmasi.
- Mengirim permintaan penghapusan ke backend.
- Menampilkan feedback hasil tindakan.

## 5.5.6. Interaction Requirements

- Tombol tambah mengarahkan ke form tambah.
- Tombol edit mengarahkan ke form edit.
- Tombol detail membuka informasi event.
- Tombol hapus memunculkan konfirmasi.
- Form dapat dikirim dan menampilkan feedback.
- Pencarian dan pagination bekerja sesuai dukungan backend.

## 5.5.7. Acceptance Criteria

- Daftar event ditampilkan secara dinamis.
- Form tambah dan edit berfungsi.
- Detail event dapat ditampilkan.
- Penghapusan memiliki konfirmasi.
- Validasi backend dapat ditampilkan pada frontend.
- Seluruh tampilan sesuai desain UI/UX.

---

# 5.6. JOBDESK 6 — Halaman CRUD Kelola Tim untuk Verifikasi Berkas dan Melihat Progress Tim (Admin)

**Tujuan:** Mengimplementasikan antarmuka administrasi untuk mengelola data tim peserta, melakukan pemeriksaan dokumen, dan melihat perkembangan tim dalam kompetisi.

Pekerjaan ini mencakup dua kebutuhan utama: pengelolaan tim yang telah terdaftar dan pemeriksaan dokumen/progress tim.

## 5.6.1. Halaman Daftar Tim Terdaftar

Requirement:
- Menampilkan daftar tim yang telah mendaftar.
- Menampilkan nama tim.
- Menampilkan institusi atau asal peserta jika tersedia.
- Menampilkan kategori kompetisi.
- Menampilkan jumlah atau informasi anggota tim.
- Menampilkan status pendaftaran.
- Menampilkan status dokumen jika tersedia.
- Menyediakan kolom aksi.

## 5.6.2. Detail Tim

Requirement:
- Menampilkan identitas tim.
- Menampilkan informasi institusi.
- Menampilkan kategori kompetisi.
- Menampilkan daftar anggota tim.
- Menampilkan status pendaftaran.
- Menampilkan dokumen yang telah diunggah.
- Menampilkan progress atau tahapan yang telah dicapai.

## 5.6.3. Verifikasi Berkas

Requirement:
- Menampilkan daftar dokumen milik tim.
- Menampilkan nama atau jenis dokumen.
- Menampilkan status dokumen.
- Menyediakan akses untuk melihat atau mengunduh dokumen.
- Menyediakan tombol verifikasi.
- Menyediakan tombol penolakan.
- Menyediakan tempat untuk memberikan alasan penolakan jika mekanisme backend mendukungnya.
- Menampilkan status terbaru setelah tindakan berhasil.

Jenis dokumen mengikuti kebutuhan aplikasi dan data yang disediakan backend, misalnya:
- Identitas mahasiswa atau KTM.
- Dokumen anggota tim.
- Bukti transaksi.
- Proposal kompetisi.

## 5.6.4. Melihat Progress Tim

Requirement:
- Menampilkan tahapan perkembangan tim.
- Menampilkan status setiap tahapan.
- Menampilkan informasi tahap yang sedang berlangsung.
- Menampilkan informasi tahap yang telah selesai.
- Menampilkan informasi tahap yang belum dipenuhi.
- Menampilkan hasil atau status seleksi apabila disediakan backend.

Contoh representasi progress:
- Pendaftaran.
- Pengumpulan dokumen.
- Verifikasi dokumen.
- Pengumpulan proposal.
- Seleksi atau pengumuman hasil.

Daftar tahapan final mengikuti aturan kompetisi dan data backend.

## 5.6.5. Interaction Requirements

- Admin dapat membuka detail tim.
- Admin dapat melihat dokumen yang diunggah.
- Admin dapat membuka atau mengunduh dokumen.
- Admin dapat melakukan verifikasi dokumen.
- Admin dapat menolak dokumen.
- Dialog konfirmasi ditampilkan jika diperlukan.
- Status dokumen diperbarui setelah backend mengonfirmasi tindakan.
- Admin dapat melihat progress tim tanpa harus membuka setiap dokumen satu per satu apabila data ringkasan tersedia.

## 5.6.6. Acceptance Criteria

- Daftar tim dapat ditampilkan dari backend.
- Detail tim dan anggota dapat ditampilkan.
- Dokumen dapat diakses sesuai izin.
- Aksi verifikasi dan penolakan berfungsi sesuai endpoint backend.
- Status dokumen diperbarui setelah proses berhasil.
- Progress tim ditampilkan sesuai data backend.
- Tampilan memiliki loading, empty, success, dan error state.

---

# 6. Frontend Architecture

## 6.1. Prinsip Arsitektur

Struktur frontend dibuat modular agar halaman, layout, dan komponen tidak saling bercampur.

Prinsip:
- Pemisahan halaman dan reusable components.
- Layout digunakan kembali.
- Komponen dibuat berdasarkan fungsi.
- Data halaman diterima melalui props Inertia.
- Interaksi dan state dikelola melalui Vue.
- Styling menggunakan Tailwind CSS.

## 6.2. Rekomendasi Struktur Folder

```text
resources/
├── js/
│   ├── app.js
│   ├── Pages/
│   │   ├── Landing/
│   │   │   └── Index.vue
│   │   └── Admin/
│   │       ├── Accounts/
│   │       │   ├── Index.vue
│   │       │   ├── Create.vue
│   │       │   ├── Edit.vue
│   │       │   └── Show.vue
│   │       ├── Events/
│   │       │   ├── Index.vue
│   │       │   ├── Create.vue
│   │       │   ├── Edit.vue
│   │       │   └── Show.vue
│   │       └── Teams/
│   │           ├── Index.vue
│   │           └── Show.vue
│   ├── Layouts/
│   │   ├── AdminLayout.vue
│   │   └── PublicLayout.vue
│   └── Components/
│       ├── UI/
│       │   ├── Button.vue
│       │   ├── Input.vue
│       │   ├── Textarea.vue
│       │   ├── Select.vue
│       │   ├── Badge.vue
│       │   ├── Card.vue
│       │   ├── Modal.vue
│       │   ├── Table.vue
│       │   ├── Pagination.vue
│       │   └── Alert.vue
│       └── Admin/
│           ├── AdminSidebar.vue
│           └── AdminNavbar.vue
└── views/
    └── app.blade.php
```

Struktur tersebut merupakan rekomendasi awal. Penyesuaian dapat dilakukan berdasarkan struktur repository dan konvensi tim.

---

# 7. UI/UX Interaction Requirements

Seluruh halaman yang menjadi tanggung jawab AJI harus memiliki perilaku antarmuka yang jelas.

## 7.1. Navigation

- Link mengarah ke route yang benar.
- Navigasi aktif memiliki indikator visual.
- Navigasi tidak menghasilkan halaman kosong.
- Navigasi mobile dapat digunakan dengan baik.

## 7.2. Form Interaction

- Input dapat menerima data.
- Form dapat disubmit.
- Tombol submit memiliki loading state.
- Input wajib diberi penanda yang jelas.
- Error validasi ditampilkan dekat field terkait.
- Nilai input dipertahankan apabila terjadi kegagalan validasi jika memungkinkan.

## 7.3. Confirmation

- Aksi penting seperti penghapusan dan penolakan dokumen memiliki konfirmasi.
- Pengguna dapat membatalkan tindakan.
- Tindakan hanya dikirim setelah konfirmasi.

## 7.4. Feedback State

Setiap halaman yang mengambil atau mengubah data perlu mempertimbangkan:

- Loading: ketika data sedang dimuat atau form sedang dikirim.
- Success: ketika tindakan berhasil.
- Error: ketika tindakan gagal.
- Empty: ketika tidak terdapat data.
- Disabled: ketika aksi tidak dapat dilakukan.
- Validation: ketika terdapat kesalahan input.

## 7.5. Data Display

- Data ditampilkan berdasarkan props dari backend.
- Format tanggal dan informasi lainnya konsisten.
- Status menggunakan badge atau indikator visual.
- Data kosong tidak menyebabkan tampilan rusak.

---

# 8. Responsive Design Requirements

Seluruh halaman frontend wajib dapat digunakan pada desktop, tablet, dan mobile.

## 8.1. Desktop

- Sidebar admin ditampilkan sesuai desain.
- Tabel dapat menampilkan seluruh kolom yang diperlukan.
- Konten memiliki lebar dan spacing yang proporsional.
- Form memanfaatkan ruang layar secara efektif.

## 8.2. Tablet

- Layout menyesuaikan lebar layar.
- Sidebar dapat disesuaikan menjadi navigasi yang lebih ringkas.
- Tabel tetap dapat digunakan.
- Form tetap memiliki ukuran input yang nyaman.

## 8.3. Mobile

- Sidebar dapat diakses melalui menu navigasi mobile.
- Konten tidak menyebabkan horizontal overflow.
- Tabel dapat menggunakan horizontal scrolling atau representasi alternatif.
- Tombol dan input mudah disentuh.
- Modal menyesuaikan ukuran layar.
- Teks tetap mudah dibaca.

Breakpoint mengikuti konfigurasi responsive Tailwind CSS.

---

# 9. Backend Integration Requirements

Frontend menggunakan Inertia.js untuk berkomunikasi dengan backend Laravel.

## 9.1. Data Integration

Backend menyediakan data yang diperlukan halaman melalui props Inertia.

Contoh data:
- Daftar akun.
- Detail akun.
- Daftar event.
- Detail event.
- Daftar tim.
- Detail tim.
- Informasi dokumen.
- Status verifikasi.
- Progress tim.

Frontend bertanggung jawab menampilkan data tersebut sesuai desain.

## 9.2. Form Submission

- Menggunakan mekanisme form Inertia, seperti `useForm`.
- Mengirim data ke route backend yang telah disediakan.
- Menampilkan loading ketika request berlangsung.
- Menampilkan error validasi dari Laravel.
- Memperbarui halaman atau data setelah request berhasil.

## 9.3. File Handling

Untuk dokumen tim:
- Frontend menampilkan informasi dokumen yang diberikan backend.
- Frontend menyediakan tautan atau mekanisme akses dokumen.
- Frontend mengirim aksi verifikasi atau penolakan.
- Pengaturan penyimpanan, akses file, dan validasi keamanan dilakukan backend.

## 9.4. Backend Contract

Sebelum integrasi, AJI perlu mendapatkan informasi dari Backend Developer mengenai:

- Daftar route dan HTTP method.
- Nama field dan struktur data.
- Format props yang dikirim ke halaman.
- Aturan validasi.
- Format error response.
- Mekanisme pagination dan pencarian.
- Endpoint verifikasi dokumen.
- Mekanisme akses dokumen.
- Format data progress tim.

Informasi yang belum disepakati diberi status **Pending Backend Confirmation** dan tidak boleh diasumsikan sebagai kontrak final.

---

# 10. Testing Requirements

Pengujian dilakukan pada setiap deliverable sebelum dianggap selesai.

## 10.1. Component Testing

- Komponen dapat dirender tanpa error.
- Props diterima dengan benar.
- Event berjalan sesuai fungsi.
- Variasi tampilan berfungsi.
- Komponen dapat digunakan pada halaman berbeda.

## 10.2. Page Testing

- Halaman dapat dibuka melalui route yang benar.
- Data tampil sesuai props.
- Tombol dan navigasi berfungsi.
- Form dapat disubmit.
- State loading, empty, error, dan success dapat ditampilkan.
- Tidak terdapat elemen yang bertabrakan atau terpotong.

## 10.3. Integration Testing

- Data dari backend berhasil ditampilkan.
- Form berhasil mengirim data.
- Error validasi backend ditampilkan.
- Penghapusan dan perubahan data memberikan feedback.
- Dokumen dapat diakses sesuai izin.
- Status verifikasi dapat diperbarui.
- Progress tim sesuai data backend.

## 10.4. Responsive Testing

Pengujian dilakukan pada:
- Desktop.
- Tablet.
- Mobile.

Hal yang diperiksa:
- Tidak ada horizontal overflow yang tidak diinginkan.
- Navigasi berfungsi.
- Form dapat digunakan.
- Tabel tetap terbaca.
- Modal tidak keluar dari viewport.
- Konten tetap proporsional.

## 10.5. Code Quality

- Tidak terdapat error pada browser console.
- Tidak terdapat error saat navigasi Inertia.
- Tidak terdapat komponen duplikat yang sebenarnya dapat digunakan kembali.
- Penamaan file dan komponen konsisten.
- Struktur folder tetap terorganisasi.
- Kode dapat dipelihara dan dikembangkan.

---

# 11. Acceptance Criteria Global

Pekerjaan frontend AJI dianggap selesai apabila:

1. Keenam jobdesk telah diimplementasikan sesuai ruang lingkup.
2. Komponen reusable dapat digunakan pada halaman terkait.
3. Root Blade template dan layout Vue berjalan dengan benar.
4. Landing page sesuai desain UI/UX.
5. Halaman CRUD akun admin berfungsi pada sisi frontend.
6. Halaman CRUD event admin berfungsi pada sisi frontend.
7. Halaman pengelolaan tim, verifikasi dokumen, dan progress dapat digunakan.
8. Seluruh interaksi utama berjalan sesuai requirement.
9. Data terintegrasi dengan backend berdasarkan kontrak yang disepakati.
10. Halaman responsif pada berbagai ukuran layar.
11. Tidak terdapat error frontend yang mengganggu penggunaan aplikasi.

---

# 12. Development Workflow

Urutan pengerjaan mengikuti jobdesk yang telah ditetapkan.

| Urutan | Jobdesk | Target pada pembagian tim |
|---|---|---|
| 1 | Komponen UI Reusable | 20 November 2026 |
| 2 | Layout Blade Utama | 20 November 2026 |
| 3 | Layout Landing Page sesuai UI/UX | 23 November 2026 |
| 4 | Halaman CRUD Akun (Admin) | 21 November 2026 |
| 5 | Halaman CRUD Event (Admin) | 23 November 2026 |
| 6 | Halaman CRUD Kelola Tim, Verifikasi Berkas, dan Progress Tim (Admin) | 26 November 2026 — Sprint 2 |

Tanggal tersebut mengikuti tabel pembagian jobdesk tim dan bukan urutan pengerjaan teknis yang wajib diikuti secara kaku.

## 12.1. Alur Pengerjaan Setiap Jobdesk

1. Periksa desain UI/UX yang tersedia.
2. Identifikasi komponen dan elemen halaman.
3. Tentukan interaksi dan state yang dibutuhkan.
4. Periksa kebutuhan data dan kontrak backend.
5. Implementasikan menggunakan Vue 3 dan Tailwind CSS.
6. Integrasikan menggunakan Inertia.js.
7. Lakukan pengujian.
8. Perbaiki hasil berdasarkan evaluasi desain dan fungsionalitas.

---

# 13. AI Agent Development Guidelines

AI Agent digunakan sebagai alat bantu implementasi frontend. Seluruh hasil implementasi tetap harus diperiksa oleh AJI.

Ketentuan pengerjaan:

- Ikuti PRD dan desain UI/UX yang diberikan.
- Gunakan Vue 3, Inertia.js, dan Tailwind CSS.
- Jangan mengganti stack teknologi.
- Jangan mengubah backend atau file milik anggota tim lain tanpa persetujuan.
- Jangan mengarang endpoint, field, atau struktur database.
- Gunakan reusable components apabila memungkinkan.
- Jangan membuat komponen duplikat tanpa alasan.
- Pisahkan logic dan tampilan secara terorganisasi.
- Pertahankan konsistensi visual antarhalaman.
- Pastikan semua tombol dan interaksi memiliki fungsi nyata.
- Jangan menggunakan data dummy sebagai pengganti integrasi final tanpa penandaan yang jelas.
- Jika ada informasi yang belum tersedia, tandai sebagai pending dan minta konfirmasi.
- Setelah implementasi, berikan ringkasan file yang dibuat atau diubah serta hasil pengujian.

---

# 14. Pending Confirmation

Beberapa detail perlu dikonfirmasi sebelum implementasi final:

| No. | Hal yang perlu dikonfirmasi | Pihak terkait |
|---|---|---|
| 1 | Detail komponen dan interaksi pada desain UI/UX | UI/UX Designer |
| 2 | Struktur field akun dan event | Backend Developer |
| 3 | Route dan props Inertia | Backend Developer |
| 4 | Format pagination dan pencarian | Backend Developer |
| 5 | Jenis dokumen dan mekanisme aksesnya | Backend Developer |
| 6 | Aturan verifikasi dan penolakan dokumen | Backend Developer |
| 7 | Struktur tahapan progress tim | Backend Developer |
| 8 | Struktur folder dan konfigurasi proyek aktual | Tim Developer |

**Dokumen ini menjadi acuan pengembangan frontend AJI.** Perubahan ruang lingkup, desain, dan kontrak backend harus diperbarui dalam dokumen sebelum memengaruhi implementasi.
