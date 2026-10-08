# 📦 Lungsur.in - Platform Katalog & Lelang Perlengkapan Kos Mahasiswa Malang

Repository resmi pengembangan proyek **Project-Based Learning (PBL) Kelompok 5**  
Program Studi D-IV Sistem Informasi Bisnis, Jurusan Teknologi Informasi, Politeknik Negeri Malang.

---

## 👥 Tim Pengembang (Kelompok 5)

| Nama | NIM | Peran Utama |
| :--- | :--- | :--- |
| **Akhmad Ghozali** | 244107060112 | Project Manager / Lead |
| **Feby Rahmawati Ahmad** | 244107060139 | System Analyst / UI/UX Designer |
| **Khoirun Nisa Fitriani** | 244107060030 | Frontend Developer |
| **Muhammad Aklilul Hikam** | 244107060059 | Fullstack / Tester |
| **Nabila Nur 'Abidah Putri Valiandra** | 244107060086 | Quality Assurance / Tester |

---

## 🛠️ Tech Stack & Prasyarat Sistem

Sebelum menjalankan proyek di laptop masing-masing, pastikan sudah terpasang:
- **PHP** minimal versi 8.2 atau 8.3 (dengan ekstensi `pdo_pgsql` aktif)
- **Composer** (cek dengan: `composer -V`)
- **Node.js** minimal versi 18 atau 20 & NPM (cek dengan: `node -v` dan `npm -v`)
- **PostgreSQL** versi 14 atau 15 (cek dengan: `psql --version` atau buka pgAdmin)
- **Git**

---

## 🚀 PANDUAN 1: Baru Pertama Kali Clone (Khusus 3 Anggota Tim Baru)

Ikuti langkah-langkah berikut secara berurutan:

### 1. Buka Terminal / PowerShell dan Clone Repository
```bash
git clone <URL_REPOSITORY_GITHUB>
cd Project-Lungsur.in
```

### 2. Install Dependensi PHP & Node.js
```bash
composer install
npm install
```

### 3. Setup File Konfigurasi `.env`
Salin template konfigurasi:
```bash
# Windows PowerShell
copy .env.example .env

# Atau Command Prompt / Git Bash
cp .env.example .env
```

Buka file `.env` di VS Code / editor teks, cari bagian database, lalu sesuaikan dengan PostgreSQL lokal Anda:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=lungsurin_db
DB_USERNAME=postgres
DB_PASSWORD=password_postgres_kamu
```

> ⚠️ **Catatan Penting:** Pastikan database bernama `lungsurin_db` sudah Anda buat terlebih dahulu di pgAdmin atau terminal psql (`CREATE DATABASE lungsurin_db;`) sebelum menjalankan perintah berikutnya.

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Hubungkan Storage Link (Wajib untuk Akses Upload Foto & Bukti Bayar)
```bash
php artisan storage:link
```

### 6. Jalankan Migrasi Database
```bash
php artisan migrate
```
*(Atau `php artisan migrate --seed` jika seeder data master sudah tersedia).*

### 7. Jalankan Server Pengembangan
Buka **dua jendela terminal**:
- **Terminal 1 (Asset compiler / Vite):**
  ```bash
  npm run dev
  ```
- **Terminal 2 (Server backend Laravel):**
  ```bash
  php artisan serve
  ```

Buka peramban di: `http://127.0.0.1:8000`

---

## 🔄 PANDUAN 2: Rutinitas Harian / Saat Mau Mulai Ngoding (`git pull`)

Setiap kali Anda hendak melanjutkan pekerjaan atau sebelum membuat fitur baru, **selalu sinkronkan perubahan terbaru dari tim**:

```bash
# 1. Pindah ke branch dev (atau branch kerja Anda)
git checkout dev

# 2. Tarik update terbaru
git pull origin dev

# 3. Jalankan jika ada penambahan paket atau file migrasi baru dari anggota lain
composer install
npm install
php artisan migrate
```

---

## 🌿 Aturan Kolaborasi Git Tim (Sangat Penting!)

Agar pekerjaan kita tidak saling menimpa atau merusak source code:

1. **JANGAN PERNAH `git push` langsung ke branch `main`!** Branch `main` hanya untuk kode stabil/rilis.
2. **Selalu Buat Branch Fitur Baru:**
   Sebelum membuat fitur baru, buat branch dari `dev`:
   ```bash
   git checkout -b feature/nama-fitur
   ```
   *Contoh penamaan branch:*
   - `feature/auth-ktm-verifikasi`
   - `feature/katalog-etalase`
   - `feature/lelang-bidding`
   - `feature/in-app-chat`
   - `feature/admin-moderasi`
   - `test/unit-test-qa`

3. **Cara Simpan & Kirim Kode ke GitHub:**
   ```bash
   git add .
   git commit -m "feat: menambah validasi upload berkas KTM"
   git push origin feature/nama-fitur
   ```

4. **Buat Pull Request (PR):**
   Buka halaman repository di GitHub, klik tombol **Compare & pull request** dengan target branch **`dev`**. Beritahu PM (**Akhmad Ghozali**) atau Web Dev (**Muhammad Aklilul Hikam**) untuk di-review dan di-merge.

---

## 📂 Struktur Database Proyek (13 Tabel Relasional)
1. `users` — Data pengguna (Mahasiswa Penjual, Pembeli, Admin) dengan jalur verifikasi KTM & kampus
2. `campus_domains` — Daftar putih domain email resmi perguruan tinggi di Kota Malang
3. `categories` — Kategori perlengkapan kos
4. `listings` — Etalase katalog barang bekas (validasi modulo seribu)
5. `listings_images` — Galeri foto barang dagangan
6. `auctions` — Konfigurasi sesi lelang terbuka
7. `auctions_bids` — Riwayat penawaran harga penawar lelang
8. `wanted_ads` — Papan pengumuman barang yang sedang dicari
9. `conversations` — Wadah ruang obrolan privat antar pengguna
10. `messages` — Pesan teks obrolan real-time
11. `boost_payments` — Pencatatan transaksi manual bayar boost promosi
12. `reports` — Tiket pengaduan dan pelaporan akun/iklan bermasalah
13. `auction_payments` — Pencatatan transaksi manual biaya pembukaan lelang
