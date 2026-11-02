"""
test_wanted_ads.py — Alur 3: Papan Wanted Ads dan Pembatasan Peran
===================================================================
PMPL Pertemuan 9 | Bab 7 — E2E Testing Lungsur.in

Skenario:
  TC-WAD-001 (+) Pembeli mempublikasikan permintaan barang kebutuhan kos baru melalui UI.
                 - Precondition: Akun dalam Mode Pembeli (buyer_page).
                 - Form diisi dan disubmit secara utuh melalui modal UI browser.
                 - Verifikasi 3 Lapisan:
                   * UI: Notifikasi flash sukses muncul, kartu kebutuhan baru tampil di papan.
                   * Data: Setelah reload (page.reload()), kartu tetap tampil di papan.
                   * Database: Record tersimpan di tabel `wanted_ads` dengan status 'active'.
                 - Teardown: Record yang baru dibuat dihapus dari database testing.

  TC-WAD-002 (-) Akun dalam Mode Penjual dilarang memasang iklan kebutuhan kos.
                 - Di antarmuka UI: Tombol "Pasang Kebutuhan Baru" tidak ditampilkan bagi penjual.
                 - Di level Controller (WantedAdController@store): Upaya submit langsung ditolak
                   dengan pesan peringatan bisnis dan data tidak disimpan ke basis data.
"""

import pytest
from playwright.sync_api import Page, expect
from conftest import (
    BASE_URL,
    get_test_db_connection,
    get_first_category_id,
    take_screenshot,
)

E2E_WANTED_TITLE = "e2e_Kipas Angin Meja Kos Uji PMPL"


# ─────────────────────────────────────────────────────────────────────────────
# TC-WAD-001: POSITIF — Pembeli mempublikasikan kebutuhan via antarmuka UI
# ─────────────────────────────────────────────────────────────────────────────

@pytest.mark.wanted_ads
@pytest.mark.positive
def test_pembeli_berhasil_memasang_kebutuhan_barang(buyer_page: Page):
    """
    Skenario Positif: Mahasiswa dalam Mode Pembeli memasang pengumuman
    kebutuhan barang kos melalui antarmuka formulir modal di halaman /cari-barang.

    Three-Layer Assertion:
      - Layer 1 (UI)  : Flash pesan sukses muncul, kartu kebutuhan tampil di grid.
      - Layer 2 (Data): Setelah page.reload(), kartu tetap tampil dengan judul & budget benar.
      - Layer 3 (DB)  : Record baru tersimpan di tabel `wanted_ads` dengan status 'active'.

    Pembersihan (Teardown): Record dihapus di blok finally agar database tetap bersih.
    """
    page = buyer_page
    conn = get_test_db_connection()

    try:
        # ── ARRANGE: Pembeli membuka papan Wanted Ads ──
        page.goto(f"{BASE_URL}/cari-barang")
        expect(page).to_have_title("Papan Barang Dicari (Wanted Ads) - Lungsur.in")

        # Tombol buka modal harus ada untuk pembeli
        btn_open = page.locator("[data-testid='btn-open-create-modal']")
        expect(btn_open).to_be_visible()

        take_screenshot(page, "TC-WAD-001_01_halaman_papan_kebutuhan")

        # ── ACT: Klik tombol untuk membuka modal form ──
        btn_open.click()

        # Modal harus terbuka (judul modal terlihat)
        expect(page.get_by_role("heading", name="Pasang Iklan Kebutuhan Barang")).to_be_visible()

        # Isi formulir modal sesuai aturan validasi aktual
        page.locator("#title").fill(E2E_WANTED_TITLE)

        # Pilih kategori pertama yang valid dari dropdown
        category_id = str(get_first_category_id())
        page.locator("#category_id").select_option(category_id)

        # Anggaran maksimal wajib kelipatan 1.000 (aturan bisnis Lungsur.in)
        page.locator("#budget_max").fill("150000")

        # Alamat / lokasi serah terima
        page.locator("#addresses").fill("Jalan Bendungan Sutami, Sumbersari, Malang")

        # Deskripsi minimal 10 karakter
        page.locator("#description").fill(
            "Mencari kipas angin meja mini ukuran 8-12 inci yang masih berfungsi normal untuk kos."
        )

        take_screenshot(page, "TC-WAD-001_02_form_modal_terisi")

        # Submit formulir
        page.locator("[data-testid='btn-submit-wanted-ad']").click()

        # ── ASSERT LAPISAN 1: UI / LAYAR ──
        # a. Notifikasi sukses harus tampil
        expect(page.locator("body")).to_contain_text(
            "berhasil dipublikasikan ke papan Wanted Ads", timeout=10_000
        )

        # b. Kartu kebutuhan baru harus langsung terlihat pada grid papan
        expect(page.get_by_role("heading", name=E2E_WANTED_TITLE)).to_be_visible()

        take_screenshot(page, "TC-WAD-001_03_kartu_muncul_di_papan")

        # ── ASSERT LAPISAN 2: DATA PERSISTENCE (Setelah Reload Halaman) ──
        # Muat ulang halaman secara paksa untuk membuktikan data tersimpan di server
        page.reload()

        # Kartu dan judul kebutuhan harus tetap terlihat
        expect(page.get_by_role("heading", name=E2E_WANTED_TITLE)).to_be_visible()
        # Budget maksimal Rp 150.000 harus tampil
        expect(page.locator("body")).to_contain_text("150.000")

        # ── ASSERT LAPISAN 3: BASIS DATA ──
        with conn.cursor() as cur:
            cur.execute("""
                SELECT id, budget_max, status, addresses
                FROM wanted_ads
                WHERE title = %s;
            """, (E2E_WANTED_TITLE,))
            row = cur.fetchone()

        assert row is not None, f"Record '{E2E_WANTED_TITLE}' tidak ditemukan di tabel wanted_ads!"
        created_id, budget_max, status, addresses = row
        assert int(budget_max) == 150000, f"Budget di DB tidak cocok: {budget_max}"
        assert status == "active", f"Status di DB diharapkan 'active', ditemukan '{status}'"
        assert "Bendungan Sutami" in addresses

        take_screenshot(page, "TC-WAD-001_04_data_terverifikasi_di_database")

    finally:
        # ── TEARDOWN: Hapus record kebutuhan uji yang baru saja dibuat ──
        with conn.cursor() as cur:
            cur.execute("DELETE FROM wanted_ads WHERE title = %s;", (E2E_WANTED_TITLE,))
        conn.commit()
        conn.close()


# ─────────────────────────────────────────────────────────────────────────────
# TC-WAD-002: NEGATIF — Penjual dilarang memasang iklan kebutuhan
# ─────────────────────────────────────────────────────────────────────────────

@pytest.mark.wanted_ads
@pytest.mark.negative
def test_penjual_dilarang_memasang_iklan_kebutuhan(seller_page: Page):
    """
    Skenario Negatif (Pembatasan Peran): Akun dalam Mode Penjual
    dilarang keras memasang iklan kebutuhan pada papan Wanted Ads.

    Aturan Bisnis (WantedAdController@store):
      "Papan Wanted Ads khusus diperuntukkan bagi pembeli yang mencari barang.
       Akun dalam Mode Penjual tidak dapat memasang kebutuhan..."

    Expected:
      1. Pada UI: Tombol "+ Pasang Kebutuhan Baru" TIDAK MUNCUL bagi penjual.
      2. Pada Controller: Jika form disubmit secara paksa via request POST,
         sistem menolak dan menampilkan pesan peringatan bisnis.
      3. Basis Data: Tidak ada record kebutuhan baru yang bertambah di basis data.
    """
    page = seller_page

    # ── ARRANGE: Penjual membuka papan Wanted Ads ──
    page.goto(f"{BASE_URL}/cari-barang")

    # ── ASSERT 1 (UI): Tombol modal untuk pasang kebutuhan tidak boleh ada ──
    expect(page.locator("[data-testid='btn-open-create-modal']")).not_to_be_visible()

    # Sebaliknya, bagi penjual harus tampil pill indikator penjual
    expect(page.get_by_text("Mode Penjual Aktif", exact=False)).to_be_visible()

    take_screenshot(page, "TC-WAD-002_01_penjual_tidak_ada_tombol_pasang")

    # ── ACT: Penjual mencoba submit form langsung ke endpoint /cari-barang ──
    # Meniru upaya bypass validasi antarmuka peramban
    csrf_token = page.locator('meta[name="csrf-token"]').get_attribute("content")
    category_id = get_first_category_id()
    negative_title = "e2e_Percobaan Ilegal Penjual Pasang Kebutuhan"

    response = page.request.post(
        f"{BASE_URL}/cari-barang",
        headers={
            "X-CSRF-TOKEN": csrf_token or "",
            "Accept": "text/html,application/xhtml+xml",
        },
        form={
            "title": negative_title,
            "category_id": str(category_id),
            "budget_max": "50000",
            "addresses": "Jalan Soekarno Hatta",
            "description": "Percobaan pasang kebutuhan oleh akun penjual yang harus ditolak.",
        },
    )

    # ── ASSERT 2: Sistem merespons dengan penolakan bisnis bagi penjual ──
    # Karena Playwright APIRequestContext mengikuti redirect 302, respon HTML langsung
    # memuat pesan peringatan aturan bisnis
    assert "Papan Wanted Ads khusus diperuntukkan bagi pembeli" in response.text(), (
        "Pesan penolakan bisnis tidak ditemukan dalam respons sistem!"
    )

    take_screenshot(page, "TC-WAD-002_02_penolakan_peran_penjual")

    # ── ASSERT 3: Verifikasi data ilegal TIDAK PERNAH masuk ke basis data ──
    conn = get_test_db_connection()
    try:
        with conn.cursor() as cur:
            cur.execute("SELECT COUNT(*) FROM wanted_ads WHERE title = %s;", (negative_title,))
            count = cur.fetchone()[0]
    finally:
        conn.close()

    assert count == 0, (
        f"Pelanggaran Aturan Bisnis! Record '{negative_title}' masuk ke tabel wanted_ads "
        f"padahal dibuat oleh akun dalam Mode Penjual."
    )
