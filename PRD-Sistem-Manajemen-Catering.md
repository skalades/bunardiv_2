# PRD — Sistem Manajemen Catering
**Versi:** 0.1 (Draft)
**Tanggal:** 26 September 2026
**Status:** Untuk direview

---

## 1. Latar Belakang

Bisnis catering saat ini dijalankan dengan struktur operasional sebagai berikut (berdasarkan dokumen internal existing):

**Divisi:**
- Admin
- Crew Decor
- Crew Catering
- Laundry
- Tukang Bangunan

**Jam Kerja:**
- Crew Catering: 06.00 – 16.00
- Crew Decor: 07.00 – 18.00
- Crew Laundry: 06.00 – 16.00
- Admin: 3 shift (06.00–15.00 / 09.00–18.00 / 11.00–20.00, masing-masing 9 jam)
- Tukang Bangunan: 07.00 – 17.00 (4 orang freelance, 3 orang tetap)
- Piket (lembur/visit/keluar kota) diinput manual sebagai tambahan jam kerja

**Skema Gaji:**
- Denda telat: Rp5.000 per 30 menit
- Lembur & bonus diinput manual
- Decor: Rp60rb–100rb/hari (freelance per panggilan)
- Kitchen: Rp70rb/hari rata, kecuali lembur piket
- Laundry: Rp70rb/hari rata
- Admin: Rp70rb/hari rata (di luar visit & keluar kota) + bonus visit Rp20rb/hari
- Tukang Bangunan: Rp120rb minimal, Rp150rb maksimal

**Pengeluaran yang dicatat manual:**
- Operasional decor (rokok, bensin, dll)
- Belanja bahan baku & operasional catering
- Gaji
- Listrik
- WiFi

Saat ini semua proses di atas dicatat **manual**, dan yang menjadi masalah utama adalah **inventori peralatan catering**: jumlah aktual barang tidak diketahui karena terlalu banyak, dan selama ini hanya dicatat saat barang **keluar** (dipakai untuk acara), tanpa pencatatan stok awal yang akurat.

---

## 2. Masalah yang Ingin Diselesaikan

1. Tidak ada visibilitas stok peralatan catering yang akurat (piring, gelas, meja, kursi, dll) karena jumlahnya sangat banyak dan tidak pernah dilakukan stock opname penuh.
2. Pencatatan keluar-masuk barang masih manual (kertas/WA/ingatan), rawan hilang, rawan barang tidak balik dari acara.
3. Proses order dari klien ke invoice belum tersistem — potensi salah hitung, salah kirim, atau lupa tagih.
4. Data gaji, jam kerja, dan pengeluaran tercecer di banyak media manual, sulit direkap untuk laporan bulanan.
5. Tidak ada satu sumber data (single source of truth) yang menghubungkan: **order → barang apa saja yang dipakai → barang balik atau tidak → invoice ke klien**.

---

## 3. Tujuan Produk

1. Membuat sistem yang mencatat **semua pergerakan barang (masuk/keluar)** secara konsisten, walau jumlah stok awal tidak diketahui persis — prinsip: **"yang penting setiap pergerakan tercatat"**, bukan **"stok harus 100% akurat sejak hari pertama."**
2. Mendukung proses bisnis end-to-end: **Order → Alokasi Barang → Pelaksanaan Acara → Barang Kembali → Invoice → Pembayaran.**
3. Memberi visibilitas bertahap: sistem membangun "estimasi stok" dari histori transaksi, dan bisa dikoreksi lewat **stok opname periodik** (manual count) tanpa mengganggu operasional.
4. Mempermudah rekap gaji, jam kerja, dan pengeluaran (opsional fase 2) agar terhubung dengan modul order/inventori yang sama.
5. Mengurangi barang hilang/tidak balik dari lokasi acara dengan pencatatan yang jelas siapa yang bertanggung jawab (PIC) atas setiap pengeluaran barang.

---

## 4. Target Pengguna & Peran

| Peran | Kebutuhan Utama |
|---|---|
| **Admin/Owner** | Input order baru, buat invoice, lihat laporan stok & keuangan, approve pengeluaran barang besar |
| **Crew Catering / Decor** | Input barang keluar-masuk saat persiapan & pembongkaran acara, lapor barang rusak/hilang |
| **Gudang/Penanggung Jawab Barang** | Melakukan stok opname berkala, approve/reject permintaan barang, catat barang masuk (pembelian baru) |
| **Klien (opsional, fase lanjut)** | Terima invoice, lihat status pesanan (read-only, via link) |

---

## 5. Ruang Lingkup (Scope)

### 5.1 Fase 1 — MVP (Prioritas Utama)
- **Manajemen Order**: buat order baru (data klien, tanggal acara, jumlah pax, paket/menu, lokasi, catatan khusus)
- **Penugasan Kru (Order Assignment)**: assign PIC dan staff yang bertugas untuk suatu order berdasarkan divisi (Crew Catering, Crew Decor, dll) sebagai dasar tanggung jawab lapangan dan persiapan sinkronisasi *payroll* di Fase 2.
- **Manajemen Inventori Fleksibel**:
  - Daftar master barang (nama, kategori, satuan) — tanpa wajib isi jumlah stok awal
  - Catat **barang keluar** (dikaitkan ke order tertentu), termasuk qty dan PIC yang bawa
  - Catat **barang masuk/kembali** (dari acara selesai, atau pembelian baru), termasuk kondisi (baik/rusak/hilang)
  - Estimasi stok berjalan (running balance) dihitung otomatis dari histori transaksi
  - **Stok opname manual**: fitur untuk "set ulang" angka stok aktual sebagai checkpoint, sistem lanjut menghitung dari titik itu
  - Riwayat/log pergerakan barang per item (siapa, kapan, untuk order apa, keluar/masuk)
- **Invoice**:
  - Generate invoice dari data order (item, harga, pajak/diskon jika ada)
  - Status invoice: Draft → Terkirim → Lunas/Belum Lunas
  - Export invoice ke PDF
- **Dashboard ringkas**: order mendatang, barang yang belum kembali, invoice belum lunas

### 5.2 Fase 2 — Pengembangan Lanjutan (Nice to Have)
- Modul absensi & jam kerja per divisi
- Modul payroll otomatis mengikuti skema gaji per divisi (termasuk denda telat & bonus)
- Modul pengeluaran operasional (rokok, bensin, listrik, wifi, dll) dengan kategori
- Laporan keuangan bulanan (rekap gaji + pengeluaran + pemasukan invoice)
- Notifikasi barang belum kembali H+X dari tanggal acara
- Barcode/QR per barang untuk mempercepat input keluar-masuk

### 5.3 Di Luar Scope (Sementara)
- Integrasi pembayaran online (payment gateway) — bisa menyusul
- Aplikasi mobile native (fase awal cukup web responsif)
- Multi-cabang/multi-gudang (asumsi 1 lokasi gudang dulu)

---

## 6. Kebutuhan Fungsional Detail

### 6.1 Modul Order
- FR-1: User dapat membuat order baru dengan data: nama klien, kontak, tanggal & jam acara, lokasi, jumlah pax, jenis paket/menu, catatan tambahan.
- FR-2: User dapat menambahkan daftar barang yang direncanakan dipakai untuk order tersebut (opsional saat pembuatan, wajib sebelum H-1 acara).
- FR-3: Admin dapat menugaskan staf/kru ke dalam suatu order beserta peran/divisinya (siapa PIC Decor, siapa Crew Catering, dll).
- FR-4: Order memiliki status: Baru → Dikonfirmasi → Persiapan → Berlangsung → Selesai → Dibatalkan.
- FR-5: Saat status order menjadi "Selesai", sistem menampilkan checklist barang yang belum tercatat kembali.

### 6.2 Modul Inventori Fleksibel
- FR-5: Admin dapat menambahkan master barang baru kapan saja tanpa perlu isi stok awal (default: "belum diketahui" / null).
- FR-6: Setiap transaksi keluar/masuk barang wajib mencatat: nama barang, qty, tanggal, PIC, dikaitkan ke order (jika keluar untuk acara) atau alasan lain (pembelian, servis, hilang, dll).
- FR-7: Sistem menghitung **estimasi stok** = stok checkpoint terakhir + total masuk − total keluar sejak checkpoint tersebut.
- FR-8: User dengan hak akses (Gudang/Admin) dapat melakukan **stok opname**: input jumlah fisik aktual, sistem mencatatnya sebagai checkpoint baru dan menghitung selisih (untuk analisis susut/hilang).
- FR-9: Sistem menampilkan indikator "akurasi stok" per barang: Belum Pernah Opname / Terverifikasi (tanggal opname terakhir).
- FR-10: Barang yang keluar untuk order tapi belum tercatat kembali setelah tanggal acara lewat, muncul di daftar "Perlu Ditindaklanjuti".

### 6.3 Modul Invoice
- FR-11: Invoice dibuat dari data order, otomatis narik nama klien, tanggal, dan rincian biaya yang diinput admin.
- FR-12: Admin dapat menambahkan item biaya manual (jasa, bahan, sewa alat, dll) beserta harga satuan.
- FR-13: Invoice dapat diberi status pembayaran (Belum Lunas/DP/Lunas) dan tanggal pembayaran.
- FR-14: Invoice dapat diekspor sebagai PDF dengan template yang bisa disesuaikan (logo, no. rekening, dll).

### 6.4 Modul Laporan (Fase 1 minimal)
- FR-15: Laporan barang paling sering keluar tapi jarang balik (indikasi rawan hilang).
- FR-16: Laporan order per periode (jumlah order, total nilai invoice, status lunas/belum).

---

## 7. Panduan Desain UI/UX (UI/UX Guidelines)

Untuk menjaga konsistensi antarmuka pengguna (UI) secara keseluruhan (mendukung prinsip modular dan mudah di-maintenance), pengembangan aplikasi harus mematuhi panduan desain visual (berdasarkan referensi mockup N7 Decoration) berikut:

### 7.1 Tema dan Warna (Color Palette)
- **Tema Utama:** Elegan Dark Mode (Mode Gelap).
- **Warna Latar Belakang (Background):** Gelap pekat (hitam/abu-abu sangat gelap, misal: *Zinc-900* atau `#0f1115`) untuk menjaga fokus dan memberikan kesan premium.
- **Warna Aksen (Accent Color):** Kuning Emas (Gold) digunakan pada tombol aksi utama, status aktif pada menu, dan teks penekanan.
- **Warna Teks:** Putih atau *Off-white* untuk data utama; Abu-abu (*Grey*) untuk label kolom, menu tidak aktif, dan teks pendukung (placeholder).
- **Indikator Status (Badges):**
  - **Pesanan Masuk:** Latar abu-abu gelap dengan teks putih.
  - **Proses / DP Dibayar:** Latar kecoklatan/kuning gelap dengan teks emas.
  - **Selesai:** Latar hijau gelap dengan teks hijau terang.

### 7.2 Tipografi (Typography)
- **Font Judul (Heading):** Menggunakan font *Serif* elegan (misalnya Playfair Display) khusus untuk judul halaman utama (seperti "Pesanan") guna memperkuat identitas brand.
- **Font Data & UI (Body):** Menggunakan font *Sans-serif* bersih (misalnya Inter, Roboto, atau Poppins) untuk kemudahan membaca di tabel, form, dan menu navigasi.
- **Label Kategori/Sub-judul:** Menggunakan huruf kapital seluruhnya (UPPERCASE) dengan spasi antar huruf (*letter-spacing*) yang renggang (misal: "MANAJEMEN ORDER").

### 7.3 Layout dan Komponen (Konsistensi UI)
- **Sidebar Navigasi (Kiri):** Dikelompokkan secara modular per konteks (OPERASIONAL, KEUANGAN, SDM, INVENTORY, LAPORAN). Item aktif diberi highlight emas halus.
- **Header:** Berisi breadcrumb konteks (contoh: MANAJEMEN ORDER) di atas judul utama, dan tombol aksi (Primary Action) dengan sudut membulat di kanan atas.
- **Pencarian (Search Bar):** Kolom input panjang dengan gaya *rounded* (melengkung) dan bergaris luar halus di atas tabel data.
- **Tabel Data:** 
  - Tabel menggunakan garis bawah (*bottom border*) tipis per baris tanpa garis pemisah vertikal yang kaku.
  - Kolom nominal/mata uang diratakan dan diformat seragam.
  - Kolom aksi/status menggunakan komponen *dropdown* minimalis berwarna gelap.

---

## 8. Kebutuhan Non-Fungsional

- **Kemudahan input**: proses catat barang keluar/masuk harus bisa dilakukan cepat di lapangan (idealnya dari HP, form ringkas).
- **Format UI Mata Uang**: Semua *input field* maupun tampilan tabel yang berkaitan dengan harga/nominal uang harus menggunakan format Rupiah (Rp) dan pemisah ribuan secara visual (UI), meskipun di database tetap disimpan dalam bentuk *decimal* murni.
- **Auditabilitas**: setiap perubahan data (terutama stok & invoice) tercatat siapa & kapan melakukannya (log/history), tidak bisa dihapus, hanya bisa dikoreksi dengan entri baru.
- **Toleransi ketidakpastian data**: sistem tidak boleh "memaksa" data stok 100% akurat sejak awal — harus tetap bisa dipakai walau banyak barang belum pernah di-opname.
- **Aksesibilitas**: web responsif, bisa diakses dari HP oleh crew di lokasi acara maupun admin di kantor.
- **Keamanan**: role-based access — crew lapangan hanya bisa input keluar/masuk, tidak bisa ubah invoice/harga.
- **Modular & Scalable**: Arsitektur sistem (dan UI) harus dibangun secara modular agar penambahan fitur/divisi baru di masa depan tidak merusak modul eksisting.

---

## 9. Metrik Keberhasilan

- ≥ 90% transaksi barang keluar untuk acara tercatat di sistem (bukan lagi manual/WA) dalam 2 bulan setelah rilis.
- Waktu pembuatan invoice dari order berkurang signifikan (target: dari manual/hitung ulang → di bawah 10 menit per invoice).
- Berkurangnya jumlah barang hilang tidak terlacak (diukur dari laporan opname periode berjalan).
- Semua order dalam 1 bulan berjalan punya invoice tercatat di sistem (tidak ada acara tanpa invoice).

---

## 10. Asumsi & Pertanyaan Terbuka

- Asumsi: satu lokasi gudang/basecamp utama (belum multi-cabang).
- Asumsi: pengguna sistem adalah tim internal (klien tidak login langsung ke sistem di Fase 1).
- Perlu konfirmasi: apakah modul payroll & pengeluaran (Fase 2) prioritas menyusul cepat, atau benar-benar fokus dulu ke order/invoice/inventori?
- Perlu konfirmasi: siapa yang akan jadi "penanggung jawab opname" — apakah admin, atau ada peran gudang khusus?
- Perlu konfirmasi: apakah dibutuhkan multi-user dengan login terpisah per crew, atau cukup 1 akun per divisi?

---

## 11. Lampiran — Referensi Dokumen Internal
Struktur divisi, jam kerja, skema gaji, dan kategori pengeluaran di atas diambil dari dokumen operasional internal (periode Januari–Agustus) sebagai konteks bisnis, dan menjadi dasar pertimbangan untuk modul Fase 2 (payroll & pengeluaran).
