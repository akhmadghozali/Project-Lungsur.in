"""
conftest.py - Konfigurasi & Fixture Pytest untuk E2E Testing Lungsur.in
========================================================================
PMPL Pertemuan 9 - Automatic End-to-End Testing (Bab 7)

Strategi:
- Database yang digunakan: lungsurin_test_db (BUKAN lungsurin_db utama)
- Semua data uji menggunakan prefix e2e_ agar mudah diidentifikasi
- Setiap fixture menggunakan yield + teardown otomatis
- Tidak ada time.sleep() — gunakan auto-waiting Playwright
"""

import os
import pytest
import psycopg2
from datetime import datetime
from playwright.sync_api import Page, BrowserContext


# ─────────────────────────────────────────────────────────────────────────────
# KONFIGURASI GLOBAL
# ─────────────────────────────────────────────────────────────────────────────

BASE_URL = "http://127.0.0.1:8000"

# Konfigurasi koneksi ke database TESTING (lungsurin_test_db)
TEST_DB_CONFIG = {
    "host": "127.0.0.1",
    "port": 5432,
    "dbname": "lungsurin_test_db",
    "user": "postgres",
    "password": "12345",
}

# Data akun uji — menggunakan prefix e2e_ agar terpisah dari data asli
E2E_SELLER_EMAIL    = "e2e_seller@lungsurin.test"
E2E_SELLER_PASSWORD = "E2ePassword123!"
E2E_BUYER_EMAIL     = "e2e_buyer@lungsurin.test"
E2E_BUYER_PASSWORD  = "E2ePassword123!"
E2E_PENDING_EMAIL   = "e2e_pending@lungsurin.test"
E2E_PENDING_PASSWORD = "E2ePassword123!"

# Judul iklan uji — prefix e2e_ memudahkan teardown
E2E_LISTING_TITLE   = "e2e_Meja Belajar Lipat Uji PMPL"


# ─────────────────────────────────────────────────────────────────────────────
# HELPER DATABASE
# ─────────────────────────────────────────────────────────────────────────────

def get_test_db_connection():
    """
    Kembalikan koneksi psycopg2 ke database TESTING (lungsurin_test_db).
    Fungsi ini memvalidasi nama database sebelum terhubung untuk mencegah
    operasi tidak sengaja ke database utama.
    """
    conn = psycopg2.connect(**TEST_DB_CONFIG)
    # Validasi ganda: pastikan database yang aktif adalah database testing
    with conn.cursor() as cur:
        cur.execute("SELECT current_database();")
        active_db = cur.fetchone()[0]
    assert active_db == "lungsurin_test_db", (
        f"BAHAYA: Koneksi mengarah ke database '{active_db}', bukan 'lungsurin_test_db'! "
        f"Operasi dihentikan untuk melindungi database utama."
    )
    return conn


def get_first_category_id():
    """Ambil ID kategori pertama yang tersedia di database testing."""
    conn = get_test_db_connection()
    try:
        with conn.cursor() as cur:
            cur.execute("SELECT id FROM categories ORDER BY id LIMIT 1;")
            row = cur.fetchone()
            if not row:
                raise RuntimeError("Tabel 'categories' kosong. Jalankan CategorySeeder terlebih dahulu.")
            return row[0]
    finally:
        conn.close()


# ─────────────────────────────────────────────────────────────────────────────
# FIXTURE KONFIGURASI PLAYWRIGHT
# ─────────────────────────────────────────────────────────────────────────────

@pytest.fixture(scope="session")
def browser_context_args(browser_context_args):
    """Override viewport dan locale untuk semua context pengujian."""
    return {
        **browser_context_args,
        "viewport": {"width": 1280, "height": 800},
        "locale": "id-ID",
    }


# ─────────────────────────────────────────────────────────────────────────────
# FIXTURE AKUN PENGGUNA UJI (scope=session: dibuat 1x per sesi pengujian)
# ─────────────────────────────────────────────────────────────────────────────

@pytest.fixture(scope="session")
def e2e_users():
    """
    Buat akun-akun uji di database lungsurin_test_db.
    Teardown: hapus semua akun dengan prefix e2e_ setelah seluruh sesi selesai.

    Akun yang dibuat:
    1. e2e_seller — role=seller, verification_status=active → untuk uji Alur 2
    2. e2e_buyer  — role=buyer,  verification_status=active → untuk uji Alur 3
    3. e2e_pending — role=buyer, verification_status=pending → untuk uji negatif Alur 1
    """
    conn = get_test_db_connection()
    # Hash password bcrypt untuk "E2ePassword123!" dengan rounds=4 (cepat untuk testing)
    # Nilai hash ini di-generate sekali dan di-hardcode agar tidak perlu PHP di sini.
    # Verifikasi: password_verify("E2ePassword123!", hash) == true dengan bcrypt
    pw_hash = "$2y$04$TestHashForE2eTesting.SaltAndHashE2ePassword123Bang!"

    # Gunakan PHP untuk generate hash yang valid secara Laravel
    import subprocess
    php_exe = r"C:\laragon\bin\php\php-8.4.5-nts-Win32-vs17-x64\php.exe"
    result = subprocess.run(
        [php_exe, "-r", f"echo password_hash('E2ePassword123!', PASSWORD_BCRYPT, ['cost' => 4]);"],
        capture_output=True, text=True
    )
    pw_hash = result.stdout.strip()

    try:
        with conn.cursor() as cur:
            cur.execute("DELETE FROM users WHERE email LIKE 'e2e_%';")

            # Buat akun penjual aktif
            cur.execute("""
                INSERT INTO users (name, email, password, role, status, verification_status, campus, created_at, updated_at)
                VALUES (%s, %s, %s, 'seller', 'active', 'active', 'Politeknik Negeri Malang', NOW(), NOW())
                RETURNING id;
            """, ("e2e Penjual Test", E2E_SELLER_EMAIL, pw_hash))
            seller_id = cur.fetchone()[0]

            # Buat akun pembeli aktif
            cur.execute("""
                INSERT INTO users (name, email, password, role, status, verification_status, campus, created_at, updated_at)
                VALUES (%s, %s, %s, 'buyer', 'active', 'active', 'Universitas Brawijaya', NOW(), NOW())
                RETURNING id;
            """, ("e2e Pembeli Test", E2E_BUYER_EMAIL, pw_hash))
            buyer_id = cur.fetchone()[0]

            # Buat akun pending KTM (untuk skenario negatif)
            cur.execute("""
                INSERT INTO users (name, email, password, role, status, verification_status, campus, created_at, updated_at)
                VALUES (%s, %s, %s, 'buyer', 'active', 'pending', 'Universitas Negeri Malang', NOW(), NOW())
                RETURNING id;
            """, ("e2e Pending KTM", E2E_PENDING_EMAIL, pw_hash))
            pending_id = cur.fetchone()[0]

        conn.commit()
        yield {
            "seller_id": seller_id,
            "buyer_id": buyer_id,
            "pending_id": pending_id,
        }
    finally:
        # ── TEARDOWN: Hapus HANYA data e2e_ dari database testing ──
        with conn.cursor() as cur:
            cur.execute("DELETE FROM sessions WHERE user_id IN (SELECT id FROM users WHERE email LIKE 'e2e_%');")
            cur.execute("DELETE FROM users WHERE email LIKE 'e2e_%';")
        conn.commit()
        conn.close()


# ─────────────────────────────────────────────────────────────────────────────
# FIXTURE SESI LOGIN BROWSER
# ─────────────────────────────────────────────────────────────────────────────

def _login_as(page: Page, email: str, password: str):
    """
    Helper: Login melalui form UI dan kembalikan page yang sudah terautentikasi.
    Menggunakan locator spesifik ID form login untuk menghindari ambiguitas SPA.
    """
    page.goto(f"{BASE_URL}/login")
    page.locator("#login_email").fill(email)
    page.locator("#login_password").fill(password)
    page.get_by_role("button", name="Masuk", exact=True).click()
    # Tunggu navigasi ke dashboard (auto-waiting Playwright)
    page.wait_for_url(f"{BASE_URL}/dashboard", timeout=10_000)
    return page


@pytest.fixture
def seller_page(page: Page, e2e_users):
    """
    Fixture: Kembalikan page yang sudah login sebagai penjual aktif (e2e_seller).
    Teardown: tidak perlu logout — context dihapus otomatis setelah test selesai.
    """
    return _login_as(page, E2E_SELLER_EMAIL, E2E_SELLER_PASSWORD)


@pytest.fixture
def buyer_page(page: Page, e2e_users):
    """
    Fixture: Kembalikan page yang sudah login sebagai pembeli aktif (e2e_buyer).
    """
    return _login_as(page, E2E_BUYER_EMAIL, E2E_BUYER_PASSWORD)


# ─────────────────────────────────────────────────────────────────────────────
# FIXTURE DATA UJI: IKLAN BARANG (Alur 2 — Status Listing)
# ─────────────────────────────────────────────────────────────────────────────

@pytest.fixture
def sample_listing(e2e_users):
    """
    Fixture Alur 2: Siapkan 1 record iklan (Listing) berstatus 'available'
    di database testing milik akun e2e_seller.

    SETUP  : INSERT 1 row ke tabel listings + listings_images
    YIELD  : dict berisi listing_id dan judul
    TEARDOWN: DELETE listing uji dan gambar terkait
    """
    conn = get_test_db_connection()
    seller_id = e2e_users["seller_id"]
    category_id = get_first_category_id()

    try:
        with conn.cursor() as cur:
            # Hapus iklan uji lama jika ada (idempoten)
            cur.execute(
                "DELETE FROM listings WHERE title = %s AND user_id = %s;",
                (E2E_LISTING_TITLE, seller_id)
            )
            # Insert iklan uji baru
            cur.execute("""
                INSERT INTO listings
                    (user_id, category_id, title, description, price,
                     condition_checklist, addresses, status, is_boosted, created_at, updated_at)
                VALUES
                    (%s, %s, %s,
                     'Meja belajar kayu minimalis lipat, kondisi sangat baik, jarang dipakai.',
                     75000,
                     '{"kondisi": "bekas"}',
                     'Jalan Soekarno-Hatta, Malang',
                     'available', false, NOW(), NOW())
                RETURNING id;
            """, (seller_id, category_id, E2E_LISTING_TITLE))
            listing_id = cur.fetchone()[0]

            # Insert gambar placeholder (wajib karena relasi)
            cur.execute("""
                INSERT INTO listings_images (listing_id, image_url, is_primary, created_at, updated_at)
                VALUES (%s, 'listings/e2e_placeholder.jpg', true, NOW(), NOW());
            """, (listing_id,))

        conn.commit()
        yield {"listing_id": listing_id, "title": E2E_LISTING_TITLE}

    finally:
        # ── TEARDOWN: Hapus HANYA data iklan e2e_ ──
        with conn.cursor() as cur:
            cur.execute("DELETE FROM listings_images WHERE listing_id = %s;", (listing_id,))
            cur.execute("DELETE FROM listings WHERE id = %s;", (listing_id,))
        conn.commit()
        conn.close()


# ─────────────────────────────────────────────────────────────────────────────
# HELPER SCREENSHOT MANUAL
# ─────────────────────────────────────────────────────────────────────────────

def take_screenshot(page: Page, name: str):
    """
    Ambil screenshot manual sebagai bukti pengujian.
    Disimpan ke tests/e2e/screenshots/<timestamp>_<name>.png
    """
    ts = datetime.now().strftime("%Y%m%d_%H%M%S")
    path = os.path.join(
        os.path.dirname(__file__), "screenshots", f"{ts}_{name}.png"
    )
    page.screenshot(path=path, full_page=True)
    return path

