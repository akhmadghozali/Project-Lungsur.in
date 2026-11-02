# LAPORAN PRAKTIKUM PENJAMINAN MUTU PERANGKAT LUNAK (PMPL)
## PERTEMUAN 9 (BAB 7): IMPLEMENTASI AUTOMATIC END-TO-END TESTING
### Studi Kasus: Platform Marketplace Mahasiswa — Lungsur.in

| Informasi | Keterangan |
|---|---|
| **Mata Kuliah** | Penjaminan Mutu Perangkat Lunak (SIB216101) |
| **Pertemuan / Bab** | Pertemuan 9 / Bab 7: Implementasi Automatic End-to-End Testing |
| **Aplikasi Studi Kasus** | Lungsur.in (PBL Kelompok 5 - D4 Sistem Informasi Bisnis Polinema) |
| **Branch Git** | `pmpl/pertemuan-9-e2e` |
| **Perkakas Otomasi** | Python 3.13, Pytest 9.2, Playwright 1.44 (Chromium Engine) |
| **Lingkungan Backend** | Laravel 11/13, PHP 8.4.5 NTS, PostgreSQL 15 |
| **Basis Data Pengujian** | `lungsurin_test_db` (Terisolasi dari database utama `lungsurin_db`) |

---

## 1. Jawaban Lembar Kerja Latihan Bab 7

### Latihan 1 — Memperbaiki Locator Rapuh
| Locator Rapuh | Pengganti yang Dianjurkan | Alasan Ketahanan Perubahan |
|---|---|---|
| `page.click("div.row > button:nth-child(2)")` | `page.get_by_role("button", name="Simpan")` atau `page.get_by_test_id("btn-simpan")` | Rantai CSS hirarkis sangat rapuh karena akan patah seketika saat tata letak (*layout*) atau pembungkus baris diubah. `get_by_role` mencerminkan bagaimana pengguna dan screen-reader berinteraksi dengan elemen. |
| `page.click("#btn-7f3a")` | `page.get_by_test_id("btn-submit")` atau `page.get_by_role("button", name="Kirim")` | ID `#btn-7f3a` merupakan ID hash yang digenerate otomatis oleh bundler/framework UI dan akan berubah pada setiap proses *build/compile* aset baru. |
| `page.click("text=Bayar Sekarang Juga")` | `page.get_by_role("button", name="Bayar")` | Locator berbasis teks panjang rapuh terhadap *copywriting*, perbaikan tanda baca, maupun translasi bahasa antarmuka. |

### Latihan 2 — Menghapus Sleep & Auto-Waiting
**Kode Asli dengan Jeda Tetap (Flaky):**
```python
import time
page.goto("http://localhost:8000/riwayat")
time.sleep(3)  # SALAH: menebak waktu
assert page.locator("tr.baris-pesanan").count() == 3
```
**Kode Perbaikan Berstandar Playwright (Auto-waiting):**
```python
page.goto("http://127.0.0.1:8000/riwayat")
expect(page.locator("tr.baris-pesanan")).to_have_count(3)
```
* **Penjelasan**: Yang sebenarnya perlu ditunggu bukanlah berlalunya waktu 3 detik, melainkan selesainya proses rendering baris tabel oleh peramban setelah respons jaringan diterima. `expect(...).to_have_count(3)` memanfaatkan *auto-waiting* dan *web-first retry assertion*, yang langsung berhasil begitu elemen siap tanpa membuang waktu tunggu yang tidak perlu.

### Latihan 3 — Menentukan Lapisan Pengujian (Unit vs End-to-End)
1. **Perhitungan diskon**: **Unit Testing**. Alasan: Merupakan logika komputasi murni tanpa ketergantungan pada browser, DOM, atau database; jauh lebih cepat dan murah diuji di level fungsi unit.
2. **Login dengan kata sandi salah**: **End-to-End Testing**. Alasan: Melintasi antarmuka peramban, transmisi HTTP POST, middleware otentikasi, enkripsi bcrypt, serta penanganan error session/flash di antarmuka.
3. **Pembatalan pesanan PROCESSING**: **End-to-End Testing**. Alasan: Melintasi pemeriksaan hak akses pengguna, pembaruan status transaksi atomik pada basis data, dan verifikasi tampilan status terkini setelah halaman dimuat ulang.
4. **Validasi format surel**: **Unit Testing**. Alasan: Merupakan aturan validasi regex/string deterministik yang cukup diuji pada level form request atau unit validator.
5. **Pemesanan sampai pembayaran berhasil**: **End-to-End Testing**. Alasan: Merupakan alur pengguna kritis bernilai utama (*core user journey*) yang menghubungkan banyak halaman, sesi pengguna, dan beberapa tabel database.

---

## 2. Penyelesaian Tugas Praktikum (Tugas No. 11 – 17)

### Tugas 11 — Tiga Alur Kritis Terpilih Lungsur.in
1. **Alur 1: Autentikasi dan Kontrol Hak Akses Akun Mahasiswa**
   * *Alasan Kelayakan*: Merupakan gerbang keamanan utama seluruh transaksi di platform Lungsur.in yang melintasi validasi form peramban, enkripsi password bcrypt, pengecekan status verifikasi KTM di basis data, dan manajemen sesi peramban.
2. **Alur 2: Siklus Hidup Status Barang Dagangan di Dasbor Penjual**
   * *Alasan Kelayakan*: Alur ini memvalidasi integritas operasional jual-beli barang bekas kampus, di mana tindakan penjual mengubah status barang (*Tersedia* $\leftrightarrow$ *Terjual*) berdampak permanen pada basis data dan secara langsung mengubah visibilitas barang pada katalog pencarian publik pembeli.
3. **Alur 3: Papan Permintaan Kebutuhan Kos (Wanted Ads) dan Pembatasan Peran**
   * *Alasan Kelayakan*: Merupakan fitur diferensiasi utama Lungsur.in yang menegakkan aturan bisnis berbasis peran (*role-based rule*): hanya mahasiswa dalam Mode Pembeli yang diizinkan mempublikasikan kebutuhan, sedangkan Mode Penjual dibatasi dari membuat permintaan barang.

---

### Tugas 12 & 13 — Implementasi Test E2E dan Fixture Precondition

Seluruh test diimplementasikan menggunakan Python Playwright dan Pytest pada folder `tests/e2e/`:

#### A. Strategi Isolasi Database & Fixture Precondition (`conftest.py`)
* Sesuai ketentuan tugas, pengujian **tidak menyentuh database utama `lungsurin_db`**.
* Pengujian menggunakan database khusus `lungsurin_test_db` yang dikonfigurasi melalui `.env.testing`. Sebelum menjalankan query apa pun, helper `get_test_db_connection()` menjalankan validasi ketat:
  ```python
  assert active_db == "lungsurin_test_db"
  ```
* Seluruh data uji menggunakan prefix identitas `e2e_*`.
* Fixture `e2e_users` dan `sample_listing` menerapkan mekanisme `yield` dengan **teardown otomatis**, sehingga record uji selalu dibersihkan kembali setelah test selesai dijalankan:
  ```python
  @pytest.fixture
  def sample_listing(e2e_users):
      # SETUP: Insert record via database connection (bukan lewat klik UI)
      cur.execute("INSERT INTO listings (...) VALUES (...) RETURNING id;")
      listing_id = cur.fetchone()[0]
      conn.commit()
      yield {"listing_id": listing_id, "title": E2E_LISTING_TITLE}
      # TEARDOWN: Bersihkan data uji setelah pengujian selesai
      cur.execute("DELETE FROM listings WHERE id = %s;", (listing_id,))
      conn.commit()
  ```

---

### Tugas 14 — Pembuktian Tiga Lapisan Expected Result (Three-Layer Assertion)

Diimplementasikan pada `test_listing_status.py::test_penjual_mengubah_status_iklan_menjadi_terjual`:

```python
# ── LAPISAN 1: UI / LAYAR ──
# Memverifikasi teks badge langsung berubah menjadi "Terjual" dan notifikasi flash muncul
expect(badge_locator).to_have_text("Terjual", timeout=10_000)
expect(page.locator("body")).to_contain_text("berhasil diubah menjadi Terjual")
expect(toggle_button).to_have_text("Jadikan Tersedia")

# ── LAPISAN 2: DATA PERSISTENCE (Reload Halaman & Verifikasi DB) ──
# Jangan percaya layar saja; muat ulang halaman untuk membuktikan perubahan tersimpan di server
page.reload()
expect(badge_after_reload).to_have_text("Terjual", timeout=10_000)

# Verifikasi langsung ke tabel database
conn = get_test_db_connection()
with conn.cursor() as cur:
    cur.execute("SELECT status FROM listings WHERE id = %s;", (listing_id,))
    db_status = cur.fetchone()[0]
assert db_status == "sold"

# ── LAPISAN 3: EFEK LANJUTAN (DOWNSTREAM EFFECT PADA KATALOG PUBLIK) ──
# Buka katalog publik pembeli: barang yang berstatus 'sold' otomatis hilang dari katalog
page.goto(f"{BASE_URL}/katalog?q={listing_title}")
expect(page.get_by_role("heading", name=listing_title)).not_to_be_visible()
```

---

### Tugas 15 — Bukti Eksekusi, Artefak Trace, dan Tangkapan Layar

#### Ringkasan Hasil Eksekusi Suite Lengkap
```text
============================= test session starts =============================
platform win32 -- Python 3.13.0, pytest-9.2.0, pluggy-1.7.0
rootdir: C:\Laragon\www\Project-Lungsur.in
configfile: pytest.ini
plugins: playwright-0.10.0
collected 7 items

tests/e2e/test_auth.py::test_login_mahasiswa_aktif_berhasil[chromium] PASSED     [ 14%]
tests/e2e/test_auth.py::test_login_password_salah_ditolak[chromium] PASSED       [ 28%]
tests/e2e/test_auth.py::test_login_akun_pending_ktm_ditolak[chromium] PASSED     [ 42%]
tests/e2e/test_listing_status.py::test_penjual_mengubah_status_iklan_menjadi_terjual[chromium] PASSED [ 57%]
tests/e2e/test_listing_status.py::test_penjual_dilarang_mengubah_status_iklan_pengguna_lain[chromium] PASSED [ 71%]
tests/e2e/test_wanted_ads.py::test_pembeli_berhasil_memasang_kebutuhan_barang[chromium] PASSED [ 85%]
tests/e2e/test_wanted_ads.py::test_penjual_dilarang_memasang_iklan_kebutuhan[chromium] PASSED [100%]

======================== 7 passed, 1 warning in 53.49s ========================
```

#### Lokasi Artefak yang Disimpan
1. **Berkas Trace Playwright**:
   * Lokasi: `tests/e2e/traces/trace_listing_status_3layer.zip`
   * Cara membuka trace:
     ```bash
     "C:\laragon\bin\python\python-3.13\python.exe" -m playwright show-trace tests/e2e/traces/trace_listing_status_3layer.zip
     ```
2. **Tangkapan Layar (Screenshots)**:
   * Tersimpan di folder `tests/e2e/screenshots/` mencakup setiap tahap kritis:
     * `TC-AUTH-001`: Form login terisi & halaman dasbor berhasil login.
     * `TC-AUTH-002`: Kotak peringatan galat kata sandi salah.
     * `TC-AUTH-003`: Penolakan akun berstatus pending verifikasi KTM.
     * `TC-LST-001`: Status awal tersedia, mutasi visual terjual, konfirmasi reload, dan hilangnya barang dari katalog publik pembeli.
     * `TC-WAD-001`: Pembukaan modal form, pengisian form kebutuhan, kemunculan kartu di papan, serta verifikasi database.
     * `TC-WAD-002`: Tidak adanya tombol pasang kebutuhan untuk penjual & respon penolakan bisnis.

---

### Tugas 16 — Tabel Traceability Lengkap

| Business Rule (BR) | Test Scenario | Test Case ID | Jenis | Nama Fungsi Test E2E | File Test |
|---|---|---|---|---|---|
| **BR-AUTH-01**: Hanya akun mahasiswa dengan status aktif dan verifikasi KTM disetujui yang dapat mengakses sistem. | Login mahasiswa aktif dengan kredensial valid berhasil masuk ke dasbor. | `TC-AUTH-001` | Positif | `test_login_mahasiswa_aktif_berhasil` | `tests/e2e/test_auth.py` |
| **BR-AUTH-02**: Percobaan login dengan kata sandi tidak valid harus ditolak dan tidak boleh membuat sesi. | Login dengan password keliru menampilkan pesan galat otentikasi. | `TC-AUTH-002` | Negatif | `test_login_password_salah_ditolak` | `tests/e2e/test_auth.py` |
| **BR-AUTH-03**: Akun mahasiswa yang masih dalam antrean verifikasi KTM diblokir sementara hingga disetujui admin. | Login akun berstatus `pending` KTM menampilkan pesan peringatan antrean verifikasi. | `TC-AUTH-003` | Negatif | `test_login_akun_pending_ktm_ditolak` | `tests/e2e/test_auth.py` |
| **BR-LST-01**: Penjual berhak menandai barang dagangannya menjadi Terjual (*Sold*), yang secara otomatis menyembunyikan barang dari katalog publik pembeli. | Penjual mengubah status iklan menjadi terjual dengan pemeriksaan 3 lapisan penuh. | `TC-LST-001` | Positif | `test_penjual_mengubah_status_iklan_menjadi_terjual` | `tests/e2e/test_listing_status.py` |
| **BR-LST-02**: Pengguna dilarang mengubah status barang milik penjual lain (*Authorization barrier*). | Upaya manipulasi status barang orang lain diblokir dengan HTTP 403 Forbidden. | `TC-LST-002` | Negatif | `test_penjual_dilarang_mengubah_status_iklan_pengguna_lain` | `tests/e2e/test_listing_status.py` |
| **BR-WAD-01**: Mahasiswa dalam Mode Pembeli dapat mempublikasikan permintaan barang dengan budget kelipatan Rp1.000. | Pembeli mengisi formulir modal Wanted Ads dan mempublikasikan kebutuhan ke papan. | `TC-WAD-001` | Positif | `test_pembeli_berhasil_memasang_kebutuhan_barang` | `tests/e2e/test_wanted_ads.py` |
| **BR-WAD-02**: Akun dalam Mode Penjual dilarang memasang iklan kebutuhan di papan Wanted Ads. | Penjual dibatasi dari tombol pasang dan permintaannya ditolak oleh controller. | `TC-WAD-002` | Negatif | `test_penjual_dilarang_memasang_iklan_kebutuhan` | `tests/e2e/test_wanted_ads.py` |

---

### Tugas 17 — Refleksi Singkat Pengujian Flaky Test

> **1. Satu test yang sempat mengalami kendala dan penyebabnya:**  
> Pada pengujian awal `test_login_mahasiswa_aktif_berhasil`, locator `page.get_by_role("button", name="Masuk")` mengalami kegagalan *strict mode violation* karena halaman `auth.blade.php` dirancang sebagai SPA yang memuat beberapa tombol dengan substring serupa (tombol utama *"Masuk"* dan tombol bantuan *"Masukkan kode di sini"*). Selain itu, pada pengujian `test_penjual_mengubah_status_iklan_menjadi_terjual`, pemanggilan `page.goto("/dashboard#kelola-barang")` sempat tidak menampilkan listing baru karena peramban telah berada di URL `/dashboard` sebelumnya, sehingga pergantian hash URL tidak memicu *request reload* HTML dari server.
>
> **2. Bagaimana kendala tersebut diperbaiki:**  
> Untuk masalah tombol, locator diubah menjadi `page.get_by_role("button", name="Masuk", exact=True)` sehingga pencocokan nama bersifat eksak dan tidak ambigu. Untuk masalah navigasi dasbor, urutan fixture diperbaiki agar `sample_listing` diinsert terlebih dahulu sebelum peramban login, serta ditambahkan `page.reload()` eksplisit untuk memastikan peramban mengambil data listing terbaru secara deterministik.
>
> **3. Perbedaan mendasar antara Unit Test dan E2E Test yang dirasakan:**  
> Unit test hanya membuktikan bahwa sebuah fungsi atau metode controller mengembalikan respons yang diharapkan dalam memori terisolasi. Sebaliknya, E2E test membuktikan bahwa seluruh sambungan nyata aplikasi (antarmuka Blade, Alpine.js, transmisi HTTP, session cookie peramban, middleware keamanan, dan basis data PostgreSQL) benar-benar bekerja secara harmonis dari sudut pandang pengalaman pengguna sesungguhnya.

