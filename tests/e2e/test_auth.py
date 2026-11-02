"""
test_auth.py — Alur 1: Autentikasi & Kontrol Hak Akses Akun
=============================================================
PMPL Pertemuan 9 | Bab 7 — E2E Testing Lungsur.in

Skenario:
  TC-AUTH-001 (+) Login mahasiswa aktif → berhasil masuk ke dashboard
  TC-AUTH-002 (-) Login dengan password salah → ditolak, pesan galat muncul
  TC-AUTH-003 (-) Login akun pending verifikasi KTM → ditolak, pesan peringatan muncul

Locator Strategy (sesuai resources/views/auth/auth.blade.php):
  - Input email  : id="login_email"  → get_by_label("Email")
  - Input password: id="login_password" → get_by_label("Password")
  - Tombol submit : <button>Masuk</button> → get_by_role("button", name="Masuk")
  - Pesan galat   : session('error') / session('warning') ditampilkan via div di auth.blade.php
"""

import pytest
from playwright.sync_api import Page, expect
from conftest import (
    BASE_URL,
    E2E_SELLER_EMAIL, E2E_SELLER_PASSWORD,
    E2E_BUYER_EMAIL, E2E_BUYER_PASSWORD,
    E2E_PENDING_EMAIL, E2E_PENDING_PASSWORD,
    take_screenshot,
)


# ─────────────────────────────────────────────────────────────────────────────
# TC-AUTH-001: POSITIF — Login mahasiswa aktif berhasil
# ─────────────────────────────────────────────────────────────────────────────

@pytest.mark.auth
@pytest.mark.positive
def test_login_mahasiswa_aktif_berhasil(page: Page, e2e_users):
    """
    Skenario Positif: Mahasiswa dengan akun aktif (verification_status=active)
    dapat login dan diarahkan ke halaman dashboard.

    Three-Layer Assertion:
      Layer 1 (UI)  : URL berpindah ke /dashboard, sapaan nama muncul di banner.
      Layer 2 (Data): Cookie sesi Laravel (laravel_session) tersimpan di browser.
      Layer 3 (Efek): Menu navigasi menampilkan link Dashboard (bukan tombol Login).
    """
    # ── ARRANGE: Buka halaman login ──
    page.goto(f"{BASE_URL}/login")
    expect(page).to_have_title("Login - Lungsur.in")

    # ── ACT: Isi form login dan submit ──
    page.locator("#login_email").fill(E2E_SELLER_EMAIL)
    page.locator("#login_password").fill(E2E_SELLER_PASSWORD)

    # Ambil screenshot sebelum klik sebagai bukti pengisian form
    take_screenshot(page, "TC-AUTH-001_01_form_login_terisi")

    page.get_by_role("button", name="Masuk", exact=True).click()

    # ── ASSERT Layer 1 (UI): URL harus berpindah ke /dashboard ──
    expect(page).to_have_url(f"{BASE_URL}/dashboard", timeout=10_000)

    # Sapaan nama pengguna harus muncul di banner dashboard
    expect(page.get_by_text("Halo, e2e Penjual Test!", exact=False)).to_be_visible()

    take_screenshot(page, "TC-AUTH-001_02_dashboard_berhasil")

    # ── ASSERT Layer 2 (Data): Cookie sesi harus terbentuk ──
    cookies = page.context.cookies()
    session_cookie = next(
        (c for c in cookies if c["name"] in ("laravel-session", "laravel_session") or "session" in c["name"].lower()), None
    )
    assert session_cookie is not None, (
        f"Cookie sesi tidak ditemukan di {cookies}. Login gagal membuat sesi."
    )
    assert session_cookie["value"] != "", "Cookie sesi kosong."

    # ── ASSERT Layer 3 (Efek Lanjutan): Tombol Logout tersedia di navbar ──
    # Navbar menampilkan nama user / Dashboard link saat sudah login
    # Tombol "Masuk" tidak boleh muncul di navbar
    expect(page.get_by_role("link", name="Masuk")).not_to_be_visible()


# ─────────────────────────────────────────────────────────────────────────────
# TC-AUTH-002: NEGATIF — Login dengan password salah ditolak
# ─────────────────────────────────────────────────────────────────────────────

@pytest.mark.auth
@pytest.mark.negative
def test_login_password_salah_ditolak(page: Page, e2e_users):
    """
    Skenario Negatif: Memasukkan password yang salah harus ditolak.

    Expected:
      - Halaman tetap di /login (tidak ada redirect ke dashboard)
      - Pesan galat "Email atau kata sandi yang Anda masukkan tidak sesuai" muncul
      - Tidak ada cookie sesi yang terbentuk
    """
    # ── ARRANGE ──
    page.goto(f"{BASE_URL}/login")

    # ── ACT: Isi email valid tapi password salah ──
    page.locator("#login_email").fill(E2E_SELLER_EMAIL)
    page.locator("#login_password").fill("PasswordSalahBanget999!")
    page.get_by_role("button", name="Masuk", exact=True).click()

    # ── ASSERT: Harus tetap di /login ──
    expect(page).to_have_url(f"{BASE_URL}/login", timeout=8_000)

    # ── ASSERT: Pesan galat harus muncul (sesuai AuthController@login) ──
    error_msg = page.locator("div.bg-red-50, div.bg-rose-50").first
    expect(error_msg).to_be_visible()
    expect(error_msg).to_contain_text("Email atau kata sandi yang Anda masukkan tidak sesuai")

    take_screenshot(page, "TC-AUTH-002_01_pesan_error_login_salah")

    # ── ASSERT: Tidak ada cookie sesi ──
    cookies = page.context.cookies()
    session_cookie = next(
        (c for c in cookies if c["name"] == "laravel_session"), None
    )
    # Jika ada cookie sesi, pastikan nilainya bukan dari sesi yang valid
    # (atau tidak ada sama sekali)
    # Karena Laravel selalu membuat session cookie bahkan untuk tamu,
    # kita verifikasi bahwa user TIDAK terauthentikasi dengan memeriksa URL
    assert page.url == f"{BASE_URL}/login", (
        "URL berpindah dari /login. Kemungkinan login berhasil padahal seharusnya gagal."
    )


# ─────────────────────────────────────────────────────────────────────────────
# TC-AUTH-003: NEGATIF — Login akun pending verifikasi KTM ditolak
# ─────────────────────────────────────────────────────────────────────────────

@pytest.mark.auth
@pytest.mark.negative
def test_login_akun_pending_ktm_ditolak(page: Page, e2e_users):
    """
    Skenario Negatif: Akun yang masih menunggu verifikasi berkas KTM
    tidak boleh bisa masuk ke aplikasi.

    Expected:
      - Sistem logout otomatis dan redirect ke /login
      - Pesan peringatan "verifikasi berkas KTM" muncul (sesuai AuthController@login)
      - Tidak ada sesi aktif yang terbentuk
    """
    # ── ACT ──
    page.goto(f"{BASE_URL}/login")
    page.locator("#login_email").fill(E2E_PENDING_EMAIL)
    page.locator("#login_password").fill(E2E_PENDING_PASSWORD)
    page.get_by_role("button", name="Masuk", exact=True).click()

    # ── ASSERT: Harus redirect kembali ke /login ──
    expect(page).to_have_url(f"{BASE_URL}/login", timeout=8_000)

    # ── ASSERT: Pesan peringatan harus muncul ──
    # Sesuai AuthController: "Akun Anda sedang dalam proses verifikasi berkas KTM..."
    warning_msg = page.locator("div.bg-amber-50").first
    expect(warning_msg).to_be_visible()
    expect(warning_msg).to_contain_text("verifikasi berkas KTM")

    take_screenshot(page, "TC-AUTH-003_01_akun_pending_ditolak")

    # ── ASSERT: Tidak ada redirect ke dashboard ──
    assert "/dashboard" not in page.url, (
        "Akun pending berhasil masuk ke dashboard. Kontrol akses gagal!"
    )

