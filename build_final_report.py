import docx
from docx.shared import Pt, RGBColor
import copy

input_path = r'C:\Users\ghozali\.gemini\antigravity\brain\124a777d-4978-44f6-a145-f4a40d5ae959\.user_uploaded\media_1794887910007_977afa9e.docx'
output_path = r'e:\SEMESTER 5\PEMROGRAMAN WEB LANJUT\lungsur-in\Laporan_Kontribusi_Individu_Akhmad_Ghozali.docx'

doc = docx.Document(input_path)

# Capture all original table references before adding new tables
t0 = doc.tables[0]   # Identitas
t1 = doc.tables[1]   # Petunjuk
t2 = doc.tables[2]   # A instruction
t3 = doc.tables[3]   # B instruction
t4 = doc.tables[4]   # Table B
t5 = doc.tables[5]   # C instruction
t6 = doc.tables[6]   # C1 Table
t7 = doc.tables[7]   # C1 Box
t8 = doc.tables[8]   # C2 Table
t9 = doc.tables[9]   # C2 Box
t10 = doc.tables[10] # C3 Table
t11 = doc.tables[11] # C3 Box
t12 = doc.tables[12] # D instruction
t13 = doc.tables[13] # Table D
t14 = doc.tables[14] # E instruction
t15 = doc.tables[15] # Table E
t16 = doc.tables[16] # F instruction
t17 = doc.tables[17] # G instruction
t18 = doc.tables[18] # Table G
t19 = doc.tables[19] # H instruction
t20 = doc.tables[20] # Table H1 (Pernyataan)
t21 = doc.tables[21] # Table H2 (Konfirmasi Rekan)
t22 = doc.tables[22] # I instruction
t23 = doc.tables[23] # Table I (Lampiran Bukti)

def set_cell_text(cell, text, font_size=10, bold=False, italic=False):
    cell.text = ''
    p = cell.paragraphs[0]
    lines = text.split('\n')
    for i, line in enumerate(lines):
        r = p.add_run(line)
        r.font.name = 'Arial'
        r.font.size = Pt(font_size)
        r.bold = bold
        r.italic = italic
        r.font.color.rgb = RGBColor(0, 0, 0)
        if i < len(lines) - 1:
            r.add_break()

def set_para_text(p, text, font_size=10, bold=False, italic=False):
    p.text = ''
    lines = text.split('\n')
    for i, line in enumerate(lines):
        r = p.add_run(line)
        r.font.name = 'Arial'
        r.font.size = Pt(font_size)
        r.bold = bold
        r.italic = italic
        r.font.color.rgb = RGBColor(0, 0, 0)
        if i < len(lines) - 1:
            r.add_break()

# ==========================================
# 1. Identitas (Table 0)
# ==========================================
identitas_vals = [
    'Akhmad Ghozali',
    '244107060112',
    'Kelompok 5 — Kelas SIB-3A',
    'Project Manager / Lead / Fullstack Developer',
    'Minggu 1 sampai Minggu 12',
    '17 November 2026',
    'https://drive.google.com/drive/folders/1Lb0tNXWMgbIkyHBwAhEwZq0vtywNRPtz?usp=sharing'
]
for idx, val in enumerate(identitas_vals):
    set_cell_text(t0.rows[idx].cells[1], val, font_size=10)

# ==========================================
# 2. Judul Proyek
# ==========================================
for p in doc.paragraphs:
    if '[Judul proyek' in p.text:
        set_para_text(p, 'Platform Web Katalog, Lelang Terbuka, dan Promosi Perlengkapan Mahasiswa di Kota Malang (Lungsur.in)', font_size=11, bold=True)
        break

# ==========================================
# 3. Bagian A (Ringkasan Kontribusi)
# ==========================================
p1_text = ('Dalam proyek Lungsur.in, saya menjalankan peran ganda sebagai Project Manager sekaligus Fullstack Developer. '
           'Pada tata kelola manajemen proyek dan analisis, saya memimpin penyusunan Project Charter, pendefinisian ruang '
           'lingkup (Scope Statement) dan WBS 3-level, perancangan diagram Use Case sistem (17 skenario), penjadwalan proyek '
           'dan linimasa kerja di Notion, perancangan matriks RACI dan alokasi sumber daya, hingga penyusunan rencana '
           'anggaran proyek (cost baseline). Pada aspek teknis pengembangan sistem, saya bertanggung jawab atas inisiasi '
           'arsitektur repositori Laravel, implementasi modul verifikasi berkas KTM dan kontrol akses admin (RBAC), '
           'pengembangan modul Wanted Ads (papan pencarian barang), integrasi sistem monetisasi (fee buka lelang dan '
           'boost listing via QRIS statis), serta mengawal code review dan penyelesaian merge conflict pada repositori tim.')

p2_text = ('Estimasi porsi kontribusi saya terhadap keseluruhan pekerjaan tim adalah sebesar 23% (dari 5 anggota tim). '
           'Penilaian ini didasari oleh beban tanggung jawab ganda yang saya emban secara konsisten, yaitu memimpin '
           'kelengkapan artefak manajerial tim dari inisiasi hingga anggaran, serta aktif mengimplementasikan fitur-fitur '
           'teknis inti pada backend dan frontend aplikasi.')

for p in doc.paragraphs:
    if '[Paragraf 1:' in p.text:
        set_para_text(p, p1_text, font_size=10)
    elif '[Paragraf 2:' in p.text:
        set_para_text(p, p2_text, font_size=10)

# ==========================================
# 4. Bagian B (Table 4: Daftar Pekerjaan yang Diklaim)
# ==========================================
table_b_data = [
    ['1', 'Menyusun Project Charter dan memimpin penetapan tata kelola tim (Governance Log)', 'M05 (Project Charter) & GOV-01', 'Minggu 3–5', 'Sendiri', '85%', 'B1'],
    ['2', 'Menyusun Work Breakdown Structure (WBS 3-Level) dan Scope Statement proyek', 'M06a (Scope Statement) & M06b (WBS)', 'Minggu 4–6', 'Bersama', '70%', 'B2'],
    ['3', 'Merancang Model Kebutuhan Sistem dan Diagram Use Case (17 skenario fungsional)', 'M06c (Model Kebutuhan / Use Case Diagram)', 'Minggu 4', 'Bersama', '60%', 'B3'],
    ['4', 'Menyusun Jadwal Proyek, Linimasa Kerja (Timeline), dan Analisis Jalur Kritis di Notion', 'M07 (Jadwal Proyek di Notion)', 'Minggu 6–7', 'Sendiri', '85%', 'B4'],
    ['5', 'Menyusun Matriks Tanggung Jawab RACI dan Perencanaan Alokasi Sumber Daya (Resource Plan)', 'Deliverable Pertemuan 9 (RACI & Resource Plan)', 'Minggu 9', 'Sendiri', '90%', 'B5'],
    ['6', 'Menyusun Rencana Anggaran Proyek (Budget Baseline & Estimasi Biaya)', 'Deliverable Pertemuan 10 (Anggaran Proyek)', 'Minggu 10', 'Sendiri', '90%', 'B6'],
    ['7', 'Inisialisasi struktur repositori Git dan instalasi fondasi arsitektur Laravel monolith', 'WBS 0.1 (Fondasi Infrastruktur Repositori)', 'Minggu 4', 'Sendiri', '100%', 'B7'],
    ['8', 'Mengembangkan modul panel admin verifikasi berkas KTM dan isolasi hak akses (RBAC)', 'WBS 4.1 (Panel Verifikasi KTM) & WBS 1.3 (RBAC)', 'Minggu 7–8', 'Sendiri', '100%', 'B8'],
    ['9', 'Mengembangkan modul papan barang dicari (Wanted Ads) beserta filter multi-kriteria katalog', 'WBS 2.4 (Wanted Ads) & WBS 2.2 (Filter Katalog)', 'Minggu 9', 'Sendiri', '90%', 'B9'],
    ['10', 'Mengimplementasikan sistem monetisasi (fee buka lelang Rp5.000 & boost iklan via QRIS statis)', 'WBS 3.3 & WBS 4.2 (Monetisasi & Verifikasi Bayar)', 'Minggu 11', 'Sendiri', '90%', 'B10'],
    ['11', 'Melakukan code review, resolusi konflik merge branch, dan perbaikan integrasi navigasi antarmuka', 'Manajemen Integrasi Proyek & WBS 0.1 / Sprint 2', 'Minggu 8–12', 'Mereview', '80%', 'B11']
]

for row_idx, row_data in enumerate(table_b_data):
    if row_idx + 1 < len(t4.rows):
        row = t4.rows[row_idx + 1]
    else:
        row = t4.add_row()
    for c_idx, val in enumerate(row_data):
        set_cell_text(row.cells[c_idx], val, font_size=9.5)

# ==========================================
# 5. Bagian C (Uraian dan Bukti Per Pekerjaan)
# ==========================================
c_blocks_data = [
    {
        'no': 1,
        'title': 'Menyusun Project Charter dan memimpin penetapan tata kelola tim (Governance Log)',
        'apa': 'Saya merumuskan tujuan strategis platform Lungsur.in, batasan awal proyek, asumsi, kriteria keberhasilan, dan struktur organisasi tim dalam dokumen Project Charter, serta memimpin rapat Governance Log untuk mengesahkan perubahan ruang lingkup (Change Request).',
        'kaitan': 'Artefak M05 (Project Charter) dan dokumen GOV-01. Menjadi kontrak dasar komitmen kerja tim dan otorisasi formal proyek bersama dosen pembimbing.',
        'cara': 'Saya mengadakan diskusi internal tim, mengumpulkan parameter ruang lingkup, menuangkannya ke dalam halaman Notion proyek, mengunggah berkas Kelompok_5_Jobsheet_5_Project_Charter.pdf ke LMS, dan mencatat keputusan perubahan ruang lingkup (seperti peniadaan sistem escrow dan penambahan jalur registrasi KTM).',
        'hasil': 'Dokumen Project Charter formal yang disetujui serta aturan tata kelola tim (ground rules, jadwal evaluasi berkala tiap Jumat pukul 19.30 WIB).',
        'bukti': 'B1: Berkas PDF Jobsheet 5 Project Charter yang diserahkan di LMS pada 28 September 2026 dan tautan halaman Notion M05.',
        'kendala': 'Terjadi perdebatan awal mengenai kompleksitas model transaksi rekening bersama (escrow). Saya memutuskan untuk melakukan scope reduction menjadi platform etalase katalog dan promosi COD hiper-lokal agar proyek realistis selesai dalam 16 minggu.',
        'bersama': 'Feby Rahmawati Ahmad (memberikan masukan skenario pengguna). Porsi saya sebesar 85% dalam penulisan dokumen charter dan memimpin otorisasi governance.',
        'placeholder': '[Tempelkan cuplikan bukti B1: Screenshot submission LMS Jobsheet 5 Project Charter & Halaman Notion M05 di sini]'
    },
    {
        'no': 2,
        'title': 'Menyusun Work Breakdown Structure (WBS 3-Level) dan Scope Statement proyek',
        'apa': 'Saya membedah seluruh kebutuhan platform ke dalam struktur hierarkis WBS 3 level (Level 0, 1, 2) dari Modul 0 hingga Modul 4, serta merumuskan batasan tegas (exclusions & constraints) dalam dokumen Scope Statement.',
        'kaitan': 'Artefak M06a (Scope Statement) dan M06b (WBS). Menjadi acuan dasar dalam pembagian paket kerja tim, estimasi waktu sprint, dan pencegahan perluasan lingkup (scope creep).',
        'cara': 'Saya menganalisis deliverable utama aplikasi, membagi paket kerja menjadi unit-unit independen di Notion, mengintegrasikan Change Request CR-04 (penambahan Modul 0 Fondasi Infrastruktur), dan menyusun berkas laporan Jobsheet 6.',
        'hasil': 'Dokumen WBS 3 level berisi 24 paket kerja terstruktur dan dokumen Scope Statement yang selaras dengan pendekatan Hybrid (Predictive & Adaptive).',
        'bukti': 'B2: Berkas PDF Jobsheet Minggu 6 Kelompok 5.pdf yang diserahkan di LMS pada 5 Oktober 2026 dan tabel WBS di Notion.',
        'kendala': 'Pada rancangan awal, WBS belum memetakan fase fondasi infrastruktur (setup git, database 13 tabel, seeder). Saya menginisiasi CR-04 untuk menambahkan Modul 0 agar alur eksekusi Sprint 1 terpetakan secara presisi.',
        'bersama': 'Feby Rahmawati Ahmad (turut mendefinisikan rincian modul katalog). Porsi saya sebesar 70% dalam merancang hierarki WBS dan penulisan batasan scope statement.',
        'placeholder': '[Tempelkan cuplikan bukti B2: Screenshot submission LMS Jobsheet 6 Scope Statement & WBS Notion di sini]'
    },
    {
        'no': 3,
        'title': 'Merancang Model Kebutuhan Sistem dan Diagram Use Case (17 skenario fungsional)',
        'apa': 'Saya merancang diagram Use Case sistem Lungsur.in yang memetakan interaksi 4 aktor pengguna ke dalam 17 skenario fungsional terintegrasi.',
        'kaitan': 'Artefak M06c (Model Kebutuhan / Use Case Diagram). Menjadi fondasi visual dalam memahami alur kerja pengguna, batas kewenangan hak akses, dan acuan perancangan pengujian (test cases).',
        'cara': 'Saya menganalisis peran aktor (Mahasiswa Penjual, Mahasiswa Pencari, Admin, dan Guest), mendefinisikan batasan interaksi, memetakan relasi <<include>> pada transaksi pembayaran mikro, serta menggambar diagram Use Case menggunakan kakas pemodelan diagram.',
        'hasil': 'Diagram Use Case sistem Lungsur.in yang memuat 17 skenario fungsional, disematkan pada Bab V Proposal PBL (Gambar 1) dan Lampiran C dokumen proyek.',
        'bukti': 'B3: Gambar 1 Use Case Diagram pada berkas Proposal PBL (Halaman 12 dan Halaman 75) serta dokumen M06c di Google Drive.',
        'kendala': 'Menentukan batasan akses bagi pengguna Guest (publik) agar tetap bisa meninjau barang tanpa membuka privasi kontak mahasiswa. Saya merancang relasi pembatasan katalog maksimal 10 barang acak tanpa informasi penjual bagi Guest.',
        'bersama': 'Feby Rahmawati Ahmad (membantu merinci narasi skenario penggunaan teks use case). Porsi saya 60% dalam perancangan diagram dan isolasi interaksi antar-aktor.',
        'placeholder': '[Tempelkan cuplikan bukti B3: Gambar Use Case Diagram Lungsur.in dari Proposal di sini]'
    },
    {
        'no': 4,
        'title': 'Menyusun Jadwal Proyek, Linimasa Kerja (Timeline), dan Analisis Jalur Kritis di Notion',
        'apa': 'Saya menyusun jadwal kerja 16 minggu proyek, memetakan ketergantungan antar-aktivitas, mengidentifikasi Jalur Kritis (Critical Path), serta mengatur papan pelacakan Kanban di Notion.',
        'kaitan': 'Artefak M07 (Jadwal Proyek di Notion). Berperan sebagai kompas waktu tim yang menjaga ritme eksekusi dan mencegah keterlambatan antar-sprint.',
        'cara': 'Saya menghitung estimasi waktu tiap aktivitas WBS, menetapkan jalur kritis: Form Iklan -> Wanted Ads -> Logika Lelang -> Verifikasi Pembayaran -> Dasbor Analitik, serta menyusun jadwal berkas Jobsheet 7.',
        'hasil': 'Linimasa proyek interaktif di Notion dan metrik pelacakan progres (menghasilkan evaluasi SPI 2,22 / On Track pada Milestone 1).',
        'bukti': 'B4: Berkas PDF Jobsheet Minggu 7 Kelompok 5.pdf yang diserahkan di LMS pada 11 Oktober 2026 dan tautan linimasa Notion M07.',
        'kendala': 'Potensi penundaan pada pengujian fitur lelang jika antarmuka iklan tertunda. Saya mengisolasi fitur chat Pusher sebagai aktivitas dengan kelonggaran waktu (float) agar tim pengembang backend dan frontend dapat bekerja simultan.',
        'bersama': '– (Dikerjakan sendiri, porsi saya 85% dalam kalkulasi linimasa dan konfigurasi sistem pelacak di Notion).',
        'placeholder': '[Tempelkan cuplikan bukti B4: Screenshot submission LMS Jobsheet 7 Jadwal Proyek & Linimasa Notion di sini]'
    },
    {
        'no': 5,
        'title': 'Menyusun Matriks Tanggung Jawab RACI dan Perencanaan Alokasi Sumber Daya (Resource Plan)',
        'apa': 'Saya merumuskan matriks RACI (Responsible, Accountable, Consulted, Informed) untuk seluruh anggota kelompok dan menyusun perencanaan alokasi sumber daya teknis serta pengadaan (Resource & Procurement Plan).',
        'kaitan': 'Deliverable Pertemuan 9 (Sumber Daya, RACI & Pengadaan). Menjamin transparansi kepemilikan tugas dan mencegah tumpang tindih wewenang antar-anggota tim.',
        'cara': 'Saya memetakan 24 aktivitas WBS ke dalam 5 anggota kelompok, menetapkan peran saya sebagai Accountable pada modul admin dan tata kelola, serta mendaftar pengadaan lisensi layanan pihak ketiga (GitHub, SMTP Mailtrap, Pusher Channels Free Tier).',
        'hasil': 'Matriks RACI terperinci dan dokumen Resource Plan di Notion yang menjadi panduan operasional tim pada Sprint 2.',
        'bukti': 'B5: Halaman Resource Plan & RACI Matrix pada ruang kerja Notion kelompok dan bukti pengumpulan LMS Pertemuan 9 (27 Oktober 2026).',
        'kendala': 'Risiko ketimpangan beban kerja antara backend, frontend, dan QA. Saya menetapkan peran Fullstack Support bagi semua anggota agar siap saling mem-backup saat terjadi kendala teknis (blocker).',
        'bersama': '– (Dikerjakan sendiri, porsi saya 90% dalam perumusan matriks RACI dan perencanaan alokasi pengadaan).',
        'placeholder': '[Tempelkan cuplikan bukti B5: Screenshot Resource Plan & Matriks RACI di Notion / LMS Pertemuan 9 di sini]'
    },
    {
        'no': 6,
        'title': 'Menyusun Rencana Anggaran Proyek (Budget Baseline & Estimasi Biaya)',
        'apa': 'Saya menghitung dan menyusun struktur anggaran biaya proyek Lungsur.in, mencakup biaya tenaga kerja (person-hours), biaya sewa infrastruktur server/domain, dan dana cadangan kontinjensi (management reserve).',
        'kaitan': 'Deliverable Pertemuan 10 (Anggaran Proyek / Cost Baseline). Memberikan gambaran kelayakan nilai ekonomi proyek serta acuan batas maksimal pengeluaran tim.',
        'cara': 'Saya mengalkulasi estimasi alokasi jam kerja 5 orang anggota selama 16 minggu perkuliahan, meneliti tarif sewa layanan cloud hosting dan domain, serta menyusun rincian cost baseline pada tabel Notion.',
        'hasil': 'Dokumen Budget Plan / Estimasi Biaya terperinci di Notion dan laporan Jobsheet Pertemuan 10 (3 November 2026).',
        'bukti': 'B6: Halaman Estimasi Biaya & Budget Baseline di Notion dan submission tugas LMS Pertemuan 10.',
        'kendala': 'Keterbatasan anggaran pada proyek akademik mahasiswa. Saya menyusun strategi efisiensi dengan memanfaatkan paket gratis (Free Tier) pada SMTP, Pusher, dan SQLite/PostgreSQL lokal sehingga biaya proyek tetap terkendali.',
        'bersama': '– (Dikerjakan sendiri, porsi saya 90% dalam perhitungan finansial dan analisis estimasi biaya).',
        'placeholder': '[Tempelkan cuplikan bukti B6: Screenshot dokumen Estimasi Biaya & Budget Baseline di Notion / LMS Pertemuan 10 di sini]'
    },
    {
        'no': 7,
        'title': 'Inisialisasi struktur repositori Git dan instalasi fondasi arsitektur Laravel monolith',
        'apa': 'Saya menginisialisasi repositori Git tim di GitHub, menyusun kerangka direktori awal Laravel 11 monolith, mengonfigurasi berkas lingkungan (.env.example), serta menetapkan aturan alur percabangan (branching strategy).',
        'kaitan': 'WBS 0.1 (Fondasi Infrastruktur Repositori). Menjadi pondasi teknis utama bagi seluruh anggota pengembang untuk mengunggah, menggabungkan, dan menguji kode program.',
        'cara': 'Saya mengeksekusi pembuatan proyek Laravel baru, mengonfigurasi pustaka dependensi Composer, menghubungkan remote origin GitHub akhmadghozali/Project-Lungsur.in, serta membuat branch utama main dan branch integrasi dev.',
        'hasil': 'Repositori GitHub resmi proyek Lungsur.in yang bersih dan siap digunakan bersama seluruh tim.',
        'bukti': 'B7: Commit inisialisasi 1c92cf9 ("Initial commit: Instalasi dasar Laravel untuk Lungsur.in") tertanggal 22 September 2026.',
        'kendala': 'Mencegah anggota tim melakukan push langsung ke branch produksi. Saya menetapkan branch protection di GitHub dan menginstruksikan seluruh fitur baru harus dikerjakan di branch turunan dev.',
        'bersama': '– (Dikerjakan sendiri, porsi 100%).',
        'placeholder': '[Tempelkan cuplikan bukti B7: Screenshot riwayat commit awal 1c92cf9 di GitHub di sini]'
    },
    {
        'no': 8,
        'title': 'Mengembangkan modul panel admin verifikasi berkas KTM dan isolasi hak akses (RBAC)',
        'apa': 'Saya membangun panel moderasi verifikasi foto KTM mahasiswa pendaftar email publik pada sisi administrator, mencakup tabel antrean berkas, fungsi persetujuan (approve), penolakan (reject) dengan catatan alasan, serta isolasi rute peran Admin via middleware.',
        'kaitan': 'WBS 4.1 (Panel Verifikasi KTM Admin) dan WBS 1.3 (Role-Based Access Control). Kritis untuk menyaring pendaftar email non-kampus dan mencegah penipuan akun bodong (mitigasi risiko R-03 dan R-04).',
        'cara': 'Saya menyusun tampilan Blade verifikasi KTM, menulis metode pemrosesan status akun di controller admin, menghubungkan penyimpanan berkas KTM, dan menambahkan penguncian hak akses pada middleware autentikasi.',
        'hasil': 'Panel admin verifikasi berkas KTM yang fungsional penuh dan sistem keamanan login yang menahan akun berstatus pending hingga disetujui admin.',
        'bukti': 'B8: Commit 902a41a ("feat: panel admin verifikasi KTM dan redirect login berdasarkan role"), commit 35a1e6c ("fix: isolasi hak akses role admin"), dan tangkapan layar halaman verifikasi KTM.',
        'kendala': 'Ditemukan celah di mana akun Admin sempat bisa membuka rute pasang iklan mahasiswa. Saya memperketat logika penguncian middleware dan mengisolasi antarmuka layout admin secara mandiri.',
        'bersama': '– (Dikerjakan sendiri, porsi 100%).',
        'placeholder': '[Tempelkan cuplikan bukti B8: Screenshot Panel Admin Verifikasi KTM & Commit 902a41a di sini]'
    },
    {
        'no': 9,
        'title': 'Mengembangkan modul papan barang dicari (Wanted Ads) beserta filter multi-kriteria katalog',
        'apa': 'Saya merancang dan mengimplementasikan modul Wanted Ads (papan permintaan kebutuhan mahasiswa) lengkap dari sisi controller backend, form posting kebutuhan dengan batas pagu anggaran, serta penyaringan multi-kriteria pada katalog barang.',
        'kaitan': 'WBS 2.4 (Wanted Ads) dan WBS 2.2 (Filter Multi-Kriteria Katalog). Menjawab kebutuhan pencari perlengkapan kos yang belum menemukan barang di katalog utama.',
        'cara': 'Saya membangun WantedAdController, blade view wanted_ads/index.blade.php, menghubungkan tombol "Tawarkan Barang" ke percakapan obrolan internal, dan menambahkan query filter dinamis berdasarkan kategori serta rentang harga.',
        'hasil': 'Halaman papan Wanted Ads publik yang aktif, fitur penawaran barang langsung ke penjual, dan sistem filter pencarian katalog yang responsif.',
        'bukti': 'B9: Rangkaian commit Git: 5c53738 ("feat: implementasi filter multi-kriteria katalog dan papan barang dicari"), 10ef4c1, 7507970, dan 15789c3 (29–30 Oktober 2026).',
        'kendala': 'Menjaga agar pembeli tidak menawar iklannya sendiri dan memastikan alur tawaran barang langsung terhubung ke jendela percakapan. Saya menyematkan validasi kepemilikan akun pada tombol aksi penawaran.',
        'bersama': '– (Dikerjakan sendiri, porsi 90%, desain UI mengacu pada arahan mockup Figma tim).',
        'placeholder': '[Tempelkan cuplikan bukti B9: Screenshot Halaman Wanted Ads di browser & Commit 5c53738 di sini]'
    },
    {
        'no': 10,
        'title': 'Mengimplementasikan sistem monetisasi (fee buka lelang Rp5.000 & boost iklan via QRIS statis)',
        'apa': 'Saya membangun alur transaksi bisnis freemium pada platform, yaitu pembayaran biaya promosi sundul iklan (boost listing) selama 30 hari dan biaya pembukaan postingan lelang terbuka sebesar Rp5.000 melalui pemindaian QRIS statis pengelola.',
        'kaitan': 'WBS 3.3 (Form Pembayaran Manual) dan WBS 4.2 (Verifikasi Pembayaran Admin). Menjadi pilar keberlanjutan operasional (business model) platform Lungsur.in.',
        'cara': 'Saya mengintegrasikan skema pembayaran manual via QRIS statis, memisahkan penayangan katalog antara barang reguler dan barang boost, mengaktifkan masa berlaku boost, serta menyiapkan antarmuka unggah bukti transfer pendaftar.',
        'hasil': 'Alur transaksi biaya layanan mikro yang berfungsi penuh, di mana iklan berstatus boost secara otomatis diprioritaskan di posisi teratas katalog.',
        'bukti': 'B10: Commit a2b1304 ("feat(monetisasi): implementasi boost 30 hari & buka lelang 5k via QRIS statis...") tertanggal 11 November 2026.',
        'kendala': 'Memilih metode pembayaran yang tidak membebani biaya gateway tinggi pada tahap simulasi kampus. Saya menerapkan mekanisme transfer QRIS statis dengan verifikasi unggahan bukti bayar yang aman dan terkontrol.',
        'bersama': '– (Dikerjakan sendiri, porsi 90%).',
        'placeholder': '[Tempelkan cuplikan bukti B10: Screenshot Formulir Pembayaran QRIS Boost & Commit a2b1304 di sini]'
    },
    {
        'no': 11,
        'title': 'Melakukan code review, resolusi konflik merge branch, dan perbaikan integrasi navigasi antarmuka',
        'apa': 'Saya mengawal proses penggabungan kode (code integration) seluruh anggota tim di branch dev, mereview setiap Pull Request (PR), menyelesaikan konflik baris kode (merge conflict), serta merestorasi rute navigasi dan konsistensi sidebar admin.',
        'kaitan': 'Manajemen Integrasi Proyek & WBS Sprint 2. Menjaga kestabilan basis kode tim agar rilis aplikasi tetap berfungsi mulus dan tidak ada fitur anggota lain yang tertimpa/hilang.',
        'cara': 'Saya memeriksa setiap PR di GitHub, menyelesaikan konflik kode secara manual di VS Code (khususnya pada file rute web.php dan model), merestorasi rute moderasi admin, serta menambahkan badge counter notifikasi yang dinamis.',
        'hasil': 'Repositori tim yang stabil dan konsisten, ditandai dengan diterimanya 7 Pull Request penting serta tampilan navbar dan sidebar yang terintegrasi rapi.',
        'bukti': 'B11: Riwayat Pull Request Merge GitHub (#11, #12, #14, #16, #18, #19, #25) serta commit c67f21b dan 1bbf28b (13 November 2026).',
        'kendala': 'Bentrok kode yang kerap terjadi pada model Listing dan rute web akibat beberapa anggota mengedit file yang sama. Saya menyelesaikan konflik secara teliti baris demi baris dan menginstruksikan tim untuk selalu melakukan git pull origin dev sebelum membuat branch baru.',
        'bersama': 'Seluruh anggota tim (Feby, Nisa, Hikam, Nabila). Porsi saya sebesar 80% dalam tanggung jawab peninjauan (review), penggabungan (merge), dan pemecahan konflik kode.',
        'placeholder': '[Tempelkan cuplikan bukti B11: Screenshot riwayat Closed Pull Requests di GitHub / Commit c67f21b di sini]'
    }
]

def fill_c_table(tbl, item):
    set_cell_text(tbl.rows[0].cells[1], item['apa'], font_size=9.5)
    set_cell_text(tbl.rows[1].cells[1], item['kaitan'], font_size=9.5)
    set_cell_text(tbl.rows[2].cells[1], item['cara'], font_size=9.5)
    set_cell_text(tbl.rows[3].cells[1], item['hasil'], font_size=9.5)
    set_cell_text(tbl.rows[4].cells[1], item['bukti'], font_size=9.5)
    set_cell_text(tbl.rows[5].cells[1], item['kendala'], font_size=9.5)
    set_cell_text(tbl.rows[6].cells[1], item['bersama'], font_size=9.5)

# C.1 (t6, t7)
fill_c_table(t6, c_blocks_data[0])
set_cell_text(t7.rows[0].cells[0], c_blocks_data[0]['placeholder'], font_size=9, italic=True)

# C.2 (t8, t9)
fill_c_table(t8, c_blocks_data[1])
set_cell_text(t9.rows[0].cells[0], c_blocks_data[1]['placeholder'], font_size=9, italic=True)

# C.3 (t10, t11)
fill_c_table(t10, c_blocks_data[2])
set_cell_text(t11.rows[0].cells[0], c_blocks_data[2]['placeholder'], font_size=9, italic=True)

# Update headings for C.1, C.2, C.3
for p in doc.paragraphs:
    if p.text.startswith('C.1 Pekerjaan 1:'):
        set_para_text(p, f'C.1 Pekerjaan 1: {c_blocks_data[0]["title"]}', font_size=11, bold=True)
    elif p.text.startswith('C.2 Pekerjaan 2:'):
        set_para_text(p, f'C.2 Pekerjaan 2: {c_blocks_data[1]["title"]}', font_size=11, bold=True)
    elif p.text.startswith('C.3 Pekerjaan 3:'):
        set_para_text(p, f'C.3 Pekerjaan 3: {c_blocks_data[2]["title"]}', font_size=11, bold=True)

# Insert C.4 through C.11 before Table 12
for item in c_blocks_data[3:]:
    # Heading
    p_heading_xml = copy.deepcopy(doc.paragraphs[20]._element)
    new_p_heading = docx.text.paragraph.Paragraph(p_heading_xml, doc)
    set_para_text(new_p_heading, f'C.{item["no"]} Pekerjaan {item["no"]}: {item["title"]}', font_size=11, bold=True)
    t12._element.addprevious(new_p_heading._element)

    # 7x2 Table
    t_xml = copy.deepcopy(t6._element)
    new_t = docx.table.Table(t_xml, doc)
    fill_c_table(new_t, item)
    t12._element.addprevious(new_t._element)

    # Caption
    p_cap_xml = copy.deepcopy(doc.paragraphs[22]._element)
    new_p_cap = docx.text.paragraph.Paragraph(p_cap_xml, doc)
    set_para_text(new_p_cap, 'Cuplikan bukti (screenshot / gambar / tabel):', font_size=10, italic=True)
    t12._element.addprevious(new_p_cap._element)

    # 1x1 Box
    t_box_xml = copy.deepcopy(t7._element)
    new_t_box = docx.table.Table(t_box_xml, doc)
    set_cell_text(new_t_box.rows[0].cells[0], item['placeholder'], font_size=9, italic=True)
    t12._element.addprevious(new_t_box._element)

    # Spacer
    p_space = doc.add_paragraph()
    set_para_text(p_space, '', font_size=6)
    t12._element.addprevious(p_space._element)

# ==========================================
# 6. Bagian D (Table 13: Kontribusi dalam Kerja Tim)
# ==========================================
table_d_data = [
    ['23 September 2026', 'Menyusun standarisasi alur kerja Git (Git Flow) kelompok, membuat panduan pembuatan branch fitur (feature/*), serta menetapkan aturan larangan push langsung ke branch utama (main).', 'Project Manager / Tech Lead', 'Seluruh anggota tim memiliki acuan alur kolaborasi kode yang seragam dan terstruktur, mencegah tertimpanya kode di branch produksi, serta mempermudah pelacakan riwayat pekerjaan anggota.', 'Tangkapan layar instruksi branching strategy di grup WhatsApp tim / Riwayat repositori Git awal'],
    ['4 Oktober 2026', 'Memimpin rapat internal tim untuk membahas penyesuaian ruang lingkup (Governance Log GOV-01) terkait perubahan registrasi KTM (CR-01), validasi harga (CR-02), dan lelang prabayar (CR-03).', 'Project Manager / Fasilitator Rapat', 'Anggota tim memiliki persepsi yang sama terhadap batasan fitur baru dan langsung mengetahui penyesuaian tugas masing-masing tanpa ada waktu tunggu yang terbuang (idle).', 'Dokumen notulen rapat GOV-01 di Notion / Riwayat percakapan grup koordinasi tim'],
    ['16 Oktober 2026', 'Membantu penanganan kendala teknis lokal (troubleshooting) anggota tim yang mengalami galat ViteManifestNotFound dan gagal seeder basis data saat menarik (pull) kode terbaru dari repositori.', 'Fullstack Support / Troubleshooter', 'Lingkungan pengembangan lokal rekan frontend dan QA kembali berjalan normal setelah diarahkan untuk menjalankan instalasi dependensi dan build aset, sehingga proses pengujian antarmuka tidak tertunda.', 'Riwayat diskusi bantuan teknis di grup koordinasi tim / Catatan mitigasi teknis'],
    ['27 Oktober 2026', 'Melakukan peninjauan (code review) pada Pull Request (PR #12) terkait modul manajemen iklan & validasi formulir dari anggota tim sebelum digabungkan ke branch dev.', 'Code Reviewer / Integrator', 'Memastikan baris kode yang ditulis rekan pengembang telah memenuhi aturan integritas bisnis (validasi harga minimal Rp1.000 dan kelipatan seribu) serta mencegah kebocoran error ke branch pengembangan utama.', 'Riwayat peninjauan dan merge Pull Request #12 pada repositori GitHub'],
    ['31 Oktober – 2 November 2026', 'Mengambil alih penyelesaian konflik kode (merge conflict) yang terjadi pada model Listing dan berkas rute web akibat penggabungan beberapa branch fitur anggota secara bersamaan.', 'Git Integrator / Conflict Resolver', 'Konflik kode berhasil diatasi secara aman baris per baris di lokal tanpa ada fitur rekan lain yang tertimpa/hilang, menjaga stabilitas branch dev tim.', 'Riwayat commit merge 0e76db4, 6847ca4, dan Closed PR #18 di GitHub'],
    ['13 November 2026', 'Mengoordinasikan integrasi akhir seluruh modul Sprint 2, merestorasi rute panel admin yang sempat bentrok, serta memperbaiki responsivitas navigasi navbar untuk persiapan demonstrasi Milestone 2 tim.', 'Lead Integrator / Fullstack', 'Tampilan antarmuka sistem dan navigasi peran terhubung secara konsisten, tidak ada rute moderasi yang broken, dan seluruh anggota tim siap melakukan demonstrasi aplikasi secara percaya diri.', 'Riwayat Pull Request #25 dan commit perbaikan rute c67f21b di GitHub']
]

for row_idx, row_data in enumerate(table_d_data):
    if row_idx + 1 < len(t13.rows):
        row = t13.rows[row_idx + 1]
    else:
        row = t13.add_row()
    for c_idx, val in enumerate(row_data):
        set_cell_text(row.cells[c_idx], val, font_size=9.5)

# ==========================================
# 7. Bagian E (Table 15: Logbook Mingguan 1–12)
# ==========================================
table_e_data = [
    ['1', 'Menginisiasi pembentukan tim kelompok 5, merumuskan ide platform perlengkapan kos Lungsur.in, serta menyusun draf awal permasalahan (Problem Statement) dan identifikasi pemangku kepentingan.', '6 jam', 'B1'],
    ['2', 'Memimpin perumusan batasan ruang lingkup awal (scope reduction) untuk meniadakan sistem rekening bersama (escrow) perbankan dan menyepakati platform difokuskan pada etalase katalog & promosi COD hiper-lokal.', '6 jam', 'B1'],
    ['3', 'Menyusun draf dokumen Project Charter (M05) di Notion, menetapkan pendekatan pengembangan Hybrid, dan merumuskan aturan kesepakatan kerja internal tim (team ground rules).', '8 jam', 'B1'],
    ['4', 'Menginisialisasi repositori Git Laravel 11 monolith (1c92cf9), menetapkan aturan Git Flow, serta merancang diagram Use Case sistem (17 skenario fungsional) untuk proposal PBL.', '10 jam', 'B3, B7'],
    ['5', 'Menyelesaikan dokumen Project Charter (Jobsheet 5) di LMS dan memimpin rapat Governance Log (GOV-01) untuk mengesahkan Change Request registrasi KTM (CR-01), validasi harga (CR-02), dan lelang prabayar (CR-03).', '8 jam', 'B1'],
    ['6', 'Menyusun struktur Work Breakdown Structure (WBS 3-Level) dan batasan Scope Statement di Notion dengan menambahkan Modul 0 Fondasi Infrastruktur (CR-04), lalu mengunggahnya ke LMS (Jobsheet 6).', '9 jam', 'B2'],
    ['7', 'Menyusun linimasa proyek 16 minggu dan analisis Jalur Kritis di Notion (Jobsheet 7), serta mulai membangun controller antarmuka panel admin verifikasi berkas KTM (902a41a).', '11 jam', 'B4, B8'],
    ['8', 'Menyempurnakan panel verifikasi KTM admin, menutup celah otorisasi peran Admin (RBAC) via middleware (35a1e6c), mereview PR #11, serta menyusun evaluasi capaian fisik Laporan Tengah Semester (LTS M08a).', '12 jam', 'B8, B11'],
    ['9', 'Menyusun matriks tanggung jawab RACI dan Resource Plan (Jobsheet 9), serta membangun modul Wanted Ads (papan permintaan kebutuhan) dan filter katalog (5c53738, 10ef4c1, PR #14).', '13 jam', 'B5, B9'],
    ['10', 'Menyusun dokumen estimasi biaya dan rencana anggaran proyek (Budget Baseline) di Notion (Jobsheet 10), serta melakukan code review dan resolusi merge conflict modul iklan (PR #18, commit 0e76db4).', '10 jam', 'B6, B11'],
    ['11', 'Mengembangkan modul transaksi monetisasi sistem: mengimplementasikan fitur pembayaran fee buka lelang Rp5.000 dan promosi boost listing 30 hari via QRIS statis pengelola (a2b1304).', '12 jam', 'B10'],
    ['12', 'Mengoordinasikan integrasi rute sistem, merestorasi rute panel admin yang bentrok, memperbaiki responsivitas navbar (PR #25, commit c67f21b), dan menyiapkan laporan individu Milestone 2.', '11 jam', 'B11']
]

for row_idx, row_data in enumerate(table_e_data):
    row = t15.rows[row_idx + 1]
    for c_idx, val in enumerate(row_data):
        set_cell_text(row.cells[c_idx], val, font_size=9.5)

# ==========================================
# 8. Bagian F (Refleksi Pribadi)
# ==========================================
f_p1 = ('Menjalankan peran ganda sebagai Project Manager sekaligus Fullstack Developer memberikan pemahaman mendalam tentang '
        'pentingnya domain kinerja Scope dan Team dalam PMBOK 8. Saya belajar bahwa kepemimpinan proyek perangkat lunak bukan '
        'sekadar memantau linimasa kalender, melainkan seni mengendalikan ruang lingkup (scope management) agar tim tidak terjebak '
        'dalam scope creep. Penegasan batasan sistem melalui dokumen Scope Statement dan pengesahan penyesuaian fitur lewat '
        'Governance Log terbukti sangat krusial dalam menjaga fokus eksekusi tanpa mengorbankan kualitas produk.')

f_p2 = ('Kendala terbesar yang saya hadapi sebagai individu adalah tingginya beban peralihan konteks (context switching) antara '
        'merumuskan artefak manajerial (seperti WBS, RACI, dan anggaran proyek) dengan menuntaskan logika teknis koding serta '
        'menyelesaikan merge conflict pada repositori tim. Saya mengatasi hambatan ini dengan menerapkan alokasi blok waktu '
        'terstruktur: mendedikasikan awal pekan untuk koordinasi manajerial dan pembaruan dokumen, serta mengalokasikan pertengahan '
        'hingga akhir pekan khusus untuk penulisan kode program dan peninjauan Pull Request (code review).')

f_p3 = ('Bagian kerja tim yang menurut saya masih perlu ditingkatkan pada fase berikutnya adalah kedisiplinan seluruh anggota dalam '
        'memperbarui status tugas di papan Kanban Notion secara real-time, serta proaktivitas dalam mengomunikasikan hambatan teknis '
        '(blocker). Dengan komunikasi yang lebih dini dan transparan, tim dapat saling mem-backup kendala teknis jauh sebelum mendekati '
        'tenggat akhir milestone.')

for p in doc.paragraphs:
    if '[Pembelajaran utama' in p.text:
        set_para_text(p, f_p1, font_size=10)
    elif '[Kendala pribadi' in p.text:
        set_para_text(p, f_p2, font_size=10)
    elif '[Yang akan saya perbaiki' in p.text:
        set_para_text(p, f_p3, font_size=10)

# ==========================================
# 9. Bagian G (Table 18: Rencana Kontribusi Tahap Berikutnya Minggu 13–16)
# ==========================================
table_g_data = [
    ['1', 'Perancangan dan implementasi dasbor analitik bisnis admin (visualisasi statistik tren komoditas barang kos dan penerimaan biaya promosi) serta modul manajemen & pemblokiran akun pengguna bermasalah.', 'Minggu 13', 'Dasbor analitik interaktif berbasis Chart.js dengan fitur cetak laporan PDF, dan panel pemblokiran akun bermasalah yang teruji.', 'WBS 4.3 (Manajemen Akun/Banned) & WBS 4.4 (Dasbor Analitik Bisnis)'],
    ['2', 'Pengawalan pengujian fungsional terpadu (QA Black-box) bersama tester, penanganan cacat program tingkat mayor (bug fixing), dan optimasi kueri agregasi database.', 'Minggu 14', 'Seluruh temuan cacat program prioritas Critical/High terselesaikan dan kueri analitik berjalan optimal.', 'Manajemen Kualitas Proyek (QA Plan & Bug Tracking)'],
    ['3', 'Finalisasi pengujian regresi dan keamanan hak akses (RBAC), perapian navigasi antarmuka, serta penyusunan draf Laporan Akhir PBL.', 'Minggu 15', 'Sistem bebas dari regresi galat, dokumentasi laporan penjaminan mutu dan manpro rampung 90%.', 'Dokumen Laporan Akhir PBL & Penjaminan Mutu'],
    ['4', 'Penerapan (deployment) sistem Lungsur.in ke lingkungan hosting publik dan memimpin persiapan demonstrasi tim pada Demo Day / Expo PBL.', 'Minggu 16', 'Platform beroperasi secara stabil di server production, dokumentasi proyek tuntas, dan demonstrasi akhir terlaksana lancar.', 'Milestone 3 (Final Release & Expo Produk)']
]

for row_idx, row_data in enumerate(table_g_data):
    row = t18.rows[row_idx + 1]
    for c_idx, val in enumerate(row_data):
        set_cell_text(row.cells[c_idx], val, font_size=9.5)

# ==========================================
# 10. Bagian H (Table 20 & Table 21: Pernyataan dan Konfirmasi Rekan)
# ==========================================
h1_text = ('Pernyataan keaslian\n'
           'Saya menyatakan bahwa seluruh pekerjaan yang saya klaim dalam laporan ini benar saya kerjakan, '
           'bukti yang saya lampirkan asli, dan saya bersedia memberi penjelasan lisan bila diminta dosen. '
           'Saya memahami bahwa klaim yang tidak benar (termasuk mengklaim pekerjaan orang lain) akan memengaruhi nilai saya.\n\n'
           'Malang, 17 November 2026\n\n\n\n'
           'Akhmad Ghozali\n'
           'NIM. 244107060112')
set_cell_text(t20.rows[0].cells[0], h1_text, font_size=9.5)

table_h2_data = [
    ['Feby Rahmawati Ahmad', '244107060139', '1, 2, 3, 9, 11', 'Ya', 'Kolaborasi penyusunan Project Charter, WBS, diagram Use Case, dan modul wanted ads benar dikerjakan bersama.'],
    ['Muhammad Aklilul Hikam', '244107060059', '7, 8, 10, 11', 'Ya', 'Benar menginisiasi repo GitHub, merancang panel verifikasi KTM, sistem bayar QRIS, dan mengawal integrasi dev.'],
    ['Nabila Nur \'Abidah Putri Valiandra', '244107060086', '1, 4, 5, 6, 8', 'Ya', 'Benar memimpin jadwal proyek, RACI, anggaran manpro, dan mengimplementasikan modul admin KTM teruji.']
]

for row_idx, row_data in enumerate(table_h2_data):
    row = t21.rows[row_idx + 1]
    for c_idx, val in enumerate(row_data):
        set_cell_text(row.cells[c_idx], val, font_size=9.5)

# ==========================================
# 11. Bagian I (Table 23: Lampiran Bukti B1–B11)
# ==========================================
table_i_data = [
    ['B1', 'Berkas Dokumen', 'Halaman Notion M05 dan PDF Jobsheet 5 Project Charter (diserahkan di LMS 28 September 2026)', 'https://drive.google.com/file/d/1G8VmaapEhKr_5rxPUjlVvL1ofS9SDkXP/view?usp=drive_link'],
    ['B2', 'Berkas Dokumen', 'Halaman WBS di Notion dan PDF Jobsheet 6 Scope Statement & WBS (diserahkan di LMS 5 Oktober 2026)', 'https://drive.google.com/file/d/1y-bm9hv0GnYxhMGBRPft_Ub8hBdaALVW/view?usp=drive_link'],
    ['B3', 'Gambar Diagram', 'Diagram Use Case Lungsur.in 17 skenario pada Bab V / Lampiran Proposal PBL', 'https://drive.google.com/file/d/1dUj2gIrNcogzFSfIkfNXYra9LIBTy9CI/view?usp=sharing'],
    ['B4', 'Berkas Dokumen', 'Halaman Jadwal Proyek & Linimasa di Notion serta PDF Jobsheet 7 (diserahkan di LMS 11 Oktober 2026)', 'https://drive.google.com/file/d/1rtkCdS7oCFrChNBcYQeNe1Kz5q0BSBBB/view?usp=drive_link'],
    ['B5', 'Berkas Dokumen', 'Halaman Resource Plan & RACI Matrix di Notion serta submission LMS Pertemuan 9 (27 Oktober 2026)', 'https://app.notion.com/p/Resource-Plan-RACI-Procurement-Lungsur-in-406c1bddcecf8066842dc8e95b76692d?source=copy_link'],
    ['B6', 'Berkas Dokumen', 'Halaman Estimasi Biaya / Budget Baseline di Notion serta submission LMS Pertemuan 10 (3 November 2026)', 'https://app.notion.com/p/Estimasi-Biaya-Budget-Baseline-408c1bddcecf803ab7f3dc7a4cb42808?source=copy_link'],
    ['B7', 'Riwayat Commit Git', 'Commit inisialisasi basis kode Laravel 1c92cf9 pada branch main (22 September 2026)', 'https://github.com/akhmadghozali/Project-Lungsur.in/commit/1c92cf9'],
    ['B8', 'Riwayat Commit & UI', 'Commit panel admin verifikasi KTM 902a41a, isolasi RBAC 35a1e6c, dan tampilan antarmuka verifikasi KTM', 'https://github.com/akhmadghozali/Project-Lungsur.in/commit/902a41a'],
    ['B9', 'Riwayat Commit & UI', 'Commit modul Wanted Ads & filter katalog 5c53738, 10ef4c1, 7507970, dan Pull Request #14', 'https://github.com/akhmadghozali/Project-Lungsur.in/commit/5c53738'],
    ['B10', 'Riwayat Commit & UI', 'Commit implementasi sistem monetisasi Rp5.000 via QRIS statis a2b1304 (11 November 2026)', 'https://github.com/akhmadghozali/Project-Lungsur.in/commit/a2b1304'],
    ['B11', 'Riwayat PR & Commit', 'Closed Pull Request GitHub #11, #12, #14, #16, #18, #19, #25 dan commit rute c67f21b', 'https://github.com/akhmadghozali/Project-Lungsur.in/pulls?q=is%3Apr+is%3Aclosed']
]

for row_idx, row_data in enumerate(table_i_data):
    if row_idx + 1 < len(t23.rows):
        row = t23.rows[row_idx + 1]
    else:
        row = t23.add_row()
    for c_idx, val in enumerate(row_data):
        set_cell_text(row.cells[c_idx], val, font_size=9.5)

doc.save(output_path)
print('Document saved successfully to:', output_path)
