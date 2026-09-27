# Laporan Audit Sistem: BUNARDI V2

**Tanggal:** 27 September 2026
**Proyek:** Sistem Manajemen Catering (BUNARDI V2)
**Fokus Audit:** Skalabilitas, Modularitas, Kemudahan Pemeliharaan (Maintenance), dan Konsistensi UI berdasarkan Dokumen PRD.

---

## 1. Arsitektur & Teknologi (Scalable, Modular, Maintainable)
**Status:** 🟢 **Sangat Baik (Fondasi Tepat)**

- **Framework yang dipilih:** Anda menggunakan **Laravel 12** dipadukan dengan **Filament PHP 5.9** (TALL Stack). Ini adalah pilihan arsitektur yang sangat tepat untuk proyek Admin Panel internal seperti catering ini. 
- **Modularitas & Pemeliharaan:** Filament bekerja berdasarkan *Resource-based routing*, di mana setiap modul (Order, Produk, Inventori) dipisahkan ke dalam filenya masing-masing di dalam `app/Filament/Resources`. Ini memastikan sistem sangat *modular* dan kodenya tidak bertumpuk di satu *Controller* raksasa. Jika nanti ada penambahan modul SDM atau Payroll (Fase 2), Anda tinggal membuat Resource baru tanpa mengganggu yang lama.

## 2. Struktur Database & Model (Kesesuaian dengan MVP PRD)
**Status:** 🔴 **Perlu Perbaikan (Banyak Modul Inti Belum Ada)**

Dari pengecekan struktur _migrations_ database, saat ini baru tersedia tabel dasar: `users`, `customers`, `products`, `orders`, `order_items`. Hal ini belum memenuhi pilar utama yang tertulis di PRD:

- **Modul Inventori (Masalah Utama di PRD belum terjawab):** 
  - ❌ Tidak ada tabel `inventories` (Master Barang). Tabel `products` saat ini sepertinya lebih ditujukan untuk paket menu katering, bukan piring/gelas.
  - ❌ Tidak ada tabel `inventory_transactions` (untuk mencatat barang keluar/masuk, tanggal, qty, status, PIC, dan terhubung ke `order_id`). Ini adalah nyawa dari PRD Anda untuk melacak barang.
- **Penugasan Kru (Order Assignment - FR-3):** 
  - ❌ Belum ada pivot table (misalnya `order_crew`) untuk menugaskan kru lapangan/dekor ke dalam suatu pesanan.
- **Manajemen Invoice:** 
  - ⚠️ Saat ini data harga menyatu di tabel `orders`. Agar lebih _scalable_ saat klien mencicil (DP/Lunas), disarankan membuat tabel `invoices` yang terpisah (atau menambahkan kolom status pembayaran, tanggal bayar, dll di tabel `orders`).

## 3. Konsistensi UI/UX (Sesuai Panduan PRD Bab 7)
**Status:** 🟡 **Cukup (Perlu Penyesuaian Konfigurasi)**

- **Warna:** Konfigurasi di `AdminPanelProvider.php` sudah menggunakan warna utama *Gold* (`#cfa24b`). Ini sudah sesuai dengan pedoman desain.
- **Tema:** PRD mengamanatkan **Elegan Dark Mode (Zinc-900)**. Saat ini Filament belum dikonfigurasi untuk _force dark mode_ atau menjadikan dark mode sebagai default bawaan yang tidak bisa diganti user.
- **Tipografi:** PRD meminta kombinasi **Playfair Display (Serif)** untuk Heading dan **Inter/Poppins** untuk Body. Saat ini konfigurasi font khusus belum ditambahkan di `AdminPanelProvider`.

---

## Rekomendasi Langkah Selanjutnya

Untuk menyelaraskan kode saat ini dengan PRD agar proyek siap dan kokoh, saya merekomendasikan kita melakukan eksekusi berikut secara bertahap:

1. **Tahap 1 (Database & Models):** Membuat _Migration_ dan _Model_ untuk `Inventory`, `InventoryTransaction`, dan tabel pivot `order_crew`.
2. **Tahap 2 (UI Consistency):** Mengupdate `AdminPanelProvider.php` agar memaksa penggunaan *Dark Mode* dan meng-inject font Playfair Display & Poppins/Inter.
3. **Tahap 3 (Filament Resources):** Membuat halaman CRUD di Filament (Resource) untuk mengelola Inventaris dan Penugasan Kru.

Apakah Anda ingin saya langsung mengeksekusi **Tahap 1 (Membuat struktur tabel Inventaris & Kru)** terlebih dahulu?
