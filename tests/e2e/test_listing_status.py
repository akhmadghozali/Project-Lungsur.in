"""
test_listing_status.py — Alur 2: Siklus Hidup Status Iklan Penjual
===================================================================
PMPL Pertemuan 9 | Bab 7 — E2E Testing Lungsur.in

Skenario:
  TC-LST-001 (+) Penjual mengubah status iklan dari Tersedia menjadi Terjual
                 (Menguji Tiga Lapisan Expected Result secara penuh):
                 - Lapisan UI   : Badge status berubah teks/warna menjadi "Terjual",
                                  dan notifikasi flash sukses tampil di layar.
                 - Lapisan Data : Setelah halaman di-reload (page.reload()), badge status
                                  tetap "Terjual", dan query database membuktikan
                                  kolom `status` pada tabel `listings` bernilai 'sold'.
                 - Lapisan Efek : Pada katalog publik pembeli (/katalog?q=...), barang yang
                                  sudah berstatus 'sold' otomatis TIDAK MUNCUL di daftar
                                  barang yang tersedia (karena filter Listing::available()).

  TC-LST-002 (-) Pengguna lain dilarang mengubah status iklan milik penjual lain
                 (Otorisasi: status code 403 Forbidden).
"""

import pytest
from playwright.sync_api import Page, expect
from conftest import (
    BASE_URL,
    get_test_db_connection,
    take_screenshot,
)


# ─────────────────────────────────────────────────────────────────────────────
# TC-LST-001: POSITIF — Penjual menandai iklan sebagai "Terjual" (3 Lapisan)
# ─────────────────────────────────────────────────────────────────────────────

@pytest.mark.listing
@pytest.mark.positive
def test_penjual_mengubah_status_iklan_menjadi_terjual(
    sample_listing: dict, seller_page: Page
):
    """
    Skenario Positif: Penjual mengubah status barang dagangan miliknya
    dari 'available' menjadi 'sold' langsung dari dasbor kelola barang.

    Pengujian Tiga Lapisan Lengkap (Sesuai Bab 7 & Tugas No. 14):
      1. Lapisan Layar (UI)
      2. Lapisan Ketahanan Data (setelah page.reload() & verifikasi basis data)
      3. Lapisan Efek Lanjutan (dampak pada katalog pencarian publik pembeli)
    """
    page = seller_page
    listing_id = sample_listing["listing_id"]
    listing_title = sample_listing["title"]

    # ── ARRANGE: Muat ulang dasbor agar listing yang baru di-insert muncul ──
    page.goto(f"{BASE_URL}/dashboard")
    page.reload()

    # Pastikan kartu listing uji terlihat di dasbor
    card_locator = page.locator(f"#listing-{listing_id}")
    expect(card_locator).to_be_visible(timeout=10_000)

    # Verifikasi kondisi awal (precondition): status awal adalah 'Tersedia'
    badge_locator = card_locator.locator("[data-testid='listing-status-badge']")
    expect(badge_locator).to_have_text("Tersedia")

    take_screenshot(page, "TC-LST-001_01_status_awal_tersedia")

    # ── ACT: Klik tombol "Tandai Terjual" ──
    toggle_button = card_locator.locator("[data-testid='btn-toggle-status']")
    expect(toggle_button).to_have_text("Tandai Terjual")
    toggle_button.click()

    # ── ASSERT LAPISAN 1: UI / LAYAR ──
    # a. Badge status harus segera berubah menjadi "Terjual"
    expect(badge_locator).to_have_text("Terjual", timeout=10_000)

    # b. Flash notification sukses harus muncul di layar
    flash_msg = page.locator("body")
    expect(flash_msg).to_contain_text("berhasil diubah menjadi Terjual")

    # c. Tombol aksi berubah menjadi "Jadikan Tersedia"
    expect(toggle_button).to_have_text("Jadikan Tersedia")

    take_screenshot(page, "TC-LST-001_02_lapisan1_ui_berubah_terjual")

    # ── ASSERT LAPISAN 2: DATA PERSISTENCE (Reload Halaman & Verifikasi DB) ──
    # Sesuai Bab 7.7: "Muat ulang halaman, jangan percaya layar saja"
    page.reload()

    # a. Setelah reload, badge harus TETAP bertuliskan "Terjual"
    card_after_reload = page.locator(f"#listing-{listing_id}")
    badge_after_reload = card_after_reload.locator("[data-testid='listing-status-badge']")
    expect(badge_after_reload).to_have_text("Terjual", timeout=10_000)

    # b. Verifikasi langsung ke database testing: kolom status harus 'sold'
    conn = get_test_db_connection()
    try:
        with conn.cursor() as cur:
            cur.execute("SELECT status FROM listings WHERE id = %s;", (listing_id,))
            db_status = cur.fetchone()[0]
    finally:
        conn.close()

    assert db_status == "sold", (
        f"Data di basis data tidak sesuai! Diharapkan 'sold', tetapi ditemukan '{db_status}'."
    )

    take_screenshot(page, "TC-LST-001_03_lapisan2_data_reload_tetap_terjual")

    # ── ASSERT LAPISAN 3: EFEK LANJUTAN (DOWNSTREAM EFFECT PADA FITUR LAIN) ──
    # Berdasarkan implementasi HomeController.php:
    #   $query = Listing::available()->with(...)
    # Scope available() memfilter hanya barang dengan status == 'available'.
    # Barang yang sudah 'sold' otomatis hilang dari katalog publik pembeli.
    page.goto(f"{BASE_URL}/katalog?q={listing_title}")

    # Verifikasi bahwa barang yang baru saja ditandai terjual TIDAK MUNCUL di katalog pembeli
    # Judul barang tidak boleh ditemukan dalam daftar katalog aktif
    expect(page.get_by_role("heading", name=listing_title)).not_to_be_visible()

    take_screenshot(page, "TC-LST-001_04_lapisan3_efek_hilang_dari_katalog")


# ─────────────────────────────────────────────────────────────────────────────
# TC-LST-002: NEGATIF — Pengguna dilarang mengubah status listing orang lain
# ─────────────────────────────────────────────────────────────────────────────

@pytest.mark.listing
@pytest.mark.negative
def test_penjual_dilarang_mengubah_status_iklan_pengguna_lain(
    buyer_page: Page, sample_listing: dict
):
    """
    Skenario Negatif (Otorisasi): Akun buyer (atau akun lain yang bukan pemilik listing)
    mencoba mengirim request PATCH untuk mengubah status listing milik penjual.

    Expected:
      - Sistem menolak akses dengan HTTP 403 Forbidden (abort_if pada DashboardController)
      - Nilai status barang di database TIDAK BERUBAH (tetap 'available')
    """
    page = buyer_page
    listing_id = sample_listing["listing_id"]

    # Ambil CSRF token dari halaman yang sedang aktif
    csrf_token = page.locator('meta[name="csrf-token"]').get_attribute("content")

    # ACT: Buyer mencoba mengirim request PATCH status listing orang lain
    response = page.request.post(
        f"{BASE_URL}/dashboard/listings/{listing_id}/status",
        headers={
            "X-CSRF-TOKEN": csrf_token or "",
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json",
        },
        data={
            "_method": "PATCH",
            "status": "sold",
        },
    )

    # ASSERT: Harus ditolak dengan status 403 Forbidden
    assert response.status == 403, (
        f"Diharapkan status 403 Forbidden saat mengubah listing orang lain, "
        f"tetapi menerima HTTP {response.status}."
    )

    # ASSERT: Verifikasi status di database tetap 'available' (tidak bocor/berubah)
    conn = get_test_db_connection()
    try:
        with conn.cursor() as cur:
            cur.execute("SELECT status FROM listings WHERE id = %s;", (listing_id,))
            current_status = cur.fetchone()[0]
    finally:
        conn.close()

    assert current_status == "available", (
        f"Integritas data terlanggar! Status berubah menjadi '{current_status}' "
        f"padahal request berasal dari pengguna yang tidak berhak."
    )
