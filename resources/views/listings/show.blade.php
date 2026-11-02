@extends('layouts.app')

@section('title', ($listing->name ?? 'Detail Barang') . ' - Lungsur.in')

@push('styles')
<style>
    /* Halaman detail: header dan footer tetap berasal dari layouts.app */
    .listing-detail-page {
        --ld-blue: #2563eb;
        --ld-border: #dfe6ef;
        --ld-muted: #64748b;
        --ld-ink: #172033;
        background: #f7f9fc;
        color: var(--ld-ink);
        min-height: 70vh;
        padding: 18px 20px 42px;
        font-family: inherit;
    }

    .listing-detail-page *,
    .listing-detail-page *::before,
    .listing-detail-page *::after {
        box-sizing: border-box;
    }

    .ld-container {
        width: min(100%, 1170px);
        margin: 0 auto;
    }

    .ld-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin: 0 0 18px;
        font-size: 13px;
    }

    .ld-back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 13px;
        color: #263247;
        background: #fff;
        border: 1px solid var(--ld-border);
        border-radius: 9px;
        text-decoration: none;
        font-weight: 600;
        transition: .2s ease;
    }

    .ld-back-link:hover {
        color: var(--ld-blue);
        border-color: #b9cdfd;
        background: #f8fbff;
    }

    .ld-crumbs {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
        color: #64748b;
    }

    .ld-crumbs a {
        color: #64748b;
        text-decoration: none;
    }

    .ld-crumbs a:hover { color: var(--ld-blue); }
    .ld-crumb-current { color: #172033; font-weight: 600; }

    .ld-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(340px, .82fr);
        align-items: start;
        gap: 22px;
    }

    .ld-left,
    .ld-right {
        min-width: 0;
    }

    .ld-right {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .ld-card {
        background: #fff;
        border: 1px solid var(--ld-border);
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .025);
    }

    /* Galeri foto */
    .ld-gallery {
        padding: 7px;
    }

    .ld-main-photo {
        position: relative;
        height: 420px;
        overflow: hidden;
        border-radius: 9px;
        background: #eef2f7;
    }

    .ld-main-photo img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ld-no-photo {
        display: grid;
        place-items: center;
        width: 100%;
        height: 100%;
        color: #64748b;
        font-size: 14px;
    }

    .ld-original-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 11px;
        color: #334155;
        background: rgba(255,255,255,.96);
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(15,23,42,.08);
        font-size: 11px;
        font-weight: 700;
    }

    .ld-original-badge span { color: #059669; font-size: 14px; }

    .ld-photo-count {
        position: absolute;
        bottom: 11px;
        left: 11px;
        padding: 4px 9px;
        color: #fff;
        background: rgba(15,23,42,.82);
        border-radius: 4px;
        font-size: 12px;
        font-weight: 700;
    }

    .ld-thumbnails {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin-top: 13px;
    }

    .ld-thumb {
        min-width: 0;
        padding: 4px;
        overflow: hidden;
        text-align: left;
        color: #334155;
        background: #fff;
        border: 1px solid var(--ld-border);
        border-radius: 9px;
        cursor: pointer;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .ld-thumb:hover,
    .ld-thumb.is-active {
        border: 2px solid var(--ld-blue);
        padding: 3px;
        box-shadow: 0 0 0 2px rgba(37,99,235,.07);
    }

    .ld-thumb img {
        display: block;
        width: 100%;
        height: 112px;
        object-fit: cover;
        border-radius: 5px;
        background: #eef2f7;
    }

    .ld-thumb-title {
        display: block;
        margin: 7px 2px 2px;
        overflow: hidden;
        font-size: 11px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ld-thumb-subtitle {
        display: block;
        margin: 0 2px 3px;
        overflow: hidden;
        color: #94a3b8;
        font-size: 10px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Spesifikasi */
    .ld-spec-card {
        margin-top: 14px;
        padding: 15px;
    }

    .ld-card-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 0 0 11px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e8edf4;
        color: #172033;
        font-size: 15px;
        font-weight: 800;
    }

    .ld-heading-note {
        color: #8190a7;
        font-size: 10px;
        font-weight: 500;
        white-space: nowrap;
    }

    .ld-spec-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
    }

    .ld-spec-item {
        min-width: 0;
        padding: 10px;
        background: #f8fafc;
        border: 1px solid #e1e8f1;
        border-radius: 8px;
    }

    .ld-spec-label {
        display: block;
        margin-bottom: 4px;
        color: #718096;
        font-size: 10px;
    }

    .ld-spec-value {
        display: block;
        color: #172033;
        font-size: 12px;
        font-weight: 750;
        overflow-wrap: anywhere;
    }

    /* Informasi produk */
    .ld-product-card { padding: 15px; }

    .ld-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 8px;
    }

    .ld-badge {
        padding: 5px 9px;
        color: #52627a;
        background: #f1f5f9;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 650;
    }

    .ld-badge-primary {
        color: #1d4ed8;
        background: #eff6ff;
    }

    .ld-product-title {
        margin: 0 0 7px;
        color: #111827;
        font-size: clamp(22px, 2.2vw, 27px);
        line-height: 1.2;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .ld-price {
        margin: 0 0 10px;
        padding-bottom: 8px;
        color: var(--ld-blue);
        border-bottom: 1px solid #e8edf4;
        font-size: 24px;
        line-height: 1.2;
        font-weight: 850;
    }

    .ld-location {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 10px 11px;
        background: #f8fafc;
        border: 1px solid #e1e8f1;
        border-radius: 8px;
    }

    .ld-location-icon {
        color: var(--ld-blue);
        font-size: 17px;
        line-height: 1.2;
    }

    .ld-location strong {
        display: block;
        color: #172033;
        font-size: 12px;
    }

    .ld-location small {
        display: block;
        margin-top: 3px;
        color: #8290a6;
        font-size: 10px;
    }

    /* Deskripsi dan kondisi */
    .ld-description-card { padding: 14px 15px; }

    .ld-description {
        margin: 0;
        color: #526078;
        font-size: 13px;
        line-height: 1.6;
        white-space: pre-line;
    }

    .ld-condition-list {
        display: grid;
        gap: 6px;
        margin-top: 10px;
        padding: 8px;
        background: #f8fafc;
        border: 1px solid #e1e8f1;
        border-radius: 8px;
    }

    .ld-condition-row {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        color: #334155;
        font-size: 11px;
        line-height: 1.45;
    }

    .ld-check {
        flex: 0 0 auto;
        color: #059669;
        font-size: 14px;
        font-weight: 800;
    }

    .ld-minus {
        margin-top: 2px;
        padding: 8px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
    }

    .ld-info { color: #334155; }

    /* Identitas penjual */
    .ld-seller-card { padding: 14px 15px; }

    .ld-secure-heading {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 5px;
        color: #172033;
        font-size: 14px;
        font-weight: 800;
    }

    .ld-lock-icon {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        flex: 0 0 auto;
        color: var(--ld-blue);
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
    }

    .ld-secure-subtitle {
        margin: 0 0 9px 39px;
        color: #8290a6;
        font-size: 10px;
    }

    .ld-secure-description {
        margin: 0 0 9px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.55;
    }

    .ld-seller-inner {
        padding: 9px;
        background: #f8fafc;
        border: 1px solid #e1e8f1;
        border-radius: 8px;
    }

    .ld-seller-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 9px;
    }

    .ld-seller-identity {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 0;
    }

    .ld-avatar {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        flex: 0 0 auto;
        color: #475569;
        background: #e2e8f0;
        border-radius: 50%;
        font-size: 14px;
        font-weight: 800;
    }

    .ld-seller-name {
        display: block;
        overflow: hidden;
        color: #172033;
        font-size: 11px;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ld-sso-status {
        display: block;
        margin-top: 3px;
        color: #059669;
        font-size: 10px;
        font-weight: 650;
    }

    .ld-verified {
        color: #94a3b8;
        font-size: 14px;
    }

    .ld-seller-contact {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 7px;
    }

    .ld-contact-masked {
        min-width: 0;
        padding: 7px;
        overflow: hidden;
        color: #718096;
        background: #fff;
        border: 1px solid #dce5ef;
        border-radius: 5px;
        font-size: 10px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ld-contact-action {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        width: 100%;
        min-height: 52px;
        margin-top: 10px;
        padding: 11px 15px;
        color: #fff;
        background: var(--ld-blue);
        border: 0;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        font-size: 13px;
        line-height: 1.35;
        font-weight: 800;
        transition: background .2s ease;
    }

    .ld-contact-action:hover {
        color: #fff;
        background: #1d4ed8;
    }

    .ld-campus-domains {
        margin: 9px 0 12px;
        color: #8290a6;
        text-align: center;
        font-size: 9px;
        line-height: 1.6;
    }

    .ld-benefits {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 7px;
    }

    .ld-benefit {
        padding: 8px 5px;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e1e8f1;
        border-radius: 6px;
        text-align: center;
        font-size: 10px;
        font-weight: 650;
    }

    .ld-tips-card {
        padding: 11px 12px;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid var(--ld-border);
        border-radius: 8px;
        font-size: 11px;
        line-height: 1.55;
    }

    @media (max-width: 900px) {
        .ld-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        .ld-right { gap: 12px; }
        .ld-main-photo { height: min(58vw, 440px); }
    }

    @media (max-width: 560px) {
        .listing-detail-page { padding: 13px 12px 28px; }
        .ld-breadcrumb { gap: 9px; align-items: flex-start; }
        .ld-crumbs { padding-top: 8px; }
        .ld-main-photo { height: 300px; }
        .ld-thumbnails { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ld-thumb img { height: 115px; }
        .ld-spec-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ld-card-heading { align-items: flex-start; }
        .ld-heading-note { white-space: normal; text-align: right; }
    }
</style>
@endpush

@section('content')
@php
    $images = $listing->images ?? collect();
    $firstImage = $images->first();

    $thumbnailTitles = [
        ['Tampak Depan', 'Siap pakai'],
        ['Samping & Kaki', 'Engsel kokoh'],
        ['Detail Kondisi', 'Detail permukaan'],
        ['Saat Dilipat', 'Mudah disimpan'],
    ];

    $categoryName = $listing->category->name ?? 'Perabot';
    $conditionName = $listing->condition ?? 'Bekas';
    $sellerName = $listing->user->name ?? 'Mahasiswa UB (Fakultas Terverifikasi)';
@endphp

<main class="listing-detail-page">
    <div class="ld-container">
        <nav class="ld-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ url()->previous() }}" class="ld-back-link">
                <span aria-hidden="true">←</span> Kembali ke Katalog
            </a>

            <div class="ld-crumbs">
                <a href="{{ url('/') }}">Home</a>
                <span>/</span>
                <span>{{ $categoryName }}</span>
                <span>/</span>
                <span class="ld-crumb-current">{{ $listing->name }}</span>
            </div>
        </nav>

        <div class="ld-layout">
            {{-- KOLOM KIRI: GALERI DAN SPESIFIKASI --}}
            <section class="ld-left" aria-label="Foto dan spesifikasi barang">
                <div class="ld-card ld-gallery">
                    <div class="ld-main-photo">
                       @if ($listing->photo_url)
    <img
        src="{{ $listing->photo_url }}"
        alt="{{ $listing->title }}"
        class="w-full h-full object-cover"
    >
@else
    <div class="w-full h-full flex items-center justify-center bg-gray-100">
        <span class="text-gray-400">Foto belum tersedia</span>
    </div>
@endif

                        <div class="ld-original-badge">
                            <span>●</span> Foto Asli Penjual (Bukan Katalog)
                        </div>
                        @foreach ($listing->images as $image)
    <img
        src="{{ Str::startsWith($image->image_url, ['http://', 'https://'])
            ? $image->image_url
            : asset('storage/' . ltrim($image->image_url, '/')) }}"
        alt="{{ $listing->title }}"
        class="w-20 h-20 object-cover rounded-lg"
    >
@endforeach


                        @if($images->count() > 0)
                            <div class="ld-photo-count" id="ldPhotoCount">
                                1 / {{ $images->count() }}
                            </div>
                        @endif
                    </div>
                </div>

                @if($images->count() > 0)
                    <div class="ld-thumbnails" aria-label="Galeri foto barang">
                        @foreach($images as $index => $image)
                            @php
                                $caption = $thumbnailTitles[$index] ?? ['Foto ' . ($index + 1), 'Detail barang'];
                                $imageUrl = $image->image_url;
                            @endphp

                            <button
                                type="button"
                                class="ld-thumb {{ $index === 0 ? 'is-active' : '' }}"
                                onclick="changeListingImage(@js($imageUrl), this, {{ $index + 1 }})"
                                aria-label="Tampilkan foto {{ $index + 1 }}"
                                aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                            >
                                <img src="{{ $imageUrl }}" alt="Foto {{ $index + 1 }} {{ $listing->name }}">
                                <span class="ld-thumb-title">{{ $caption[0] }}</span>
                                <span class="ld-thumb-subtitle">{{ $caption[1] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif

                <section class="ld-card ld-spec-card">
                    <h2 class="ld-card-heading">
                        <span>Spesifikasi Meja</span>
                        <span class="ld-heading-note">Verifikasi Lapangan Malang</span>
                    </h2>

                    <div class="ld-spec-grid">
                        <div class="ld-spec-item">
                            <span class="ld-spec-label">Dimensi</span>
                            <strong class="ld-spec-value">60 × 40 cm</strong>
                        </div>
                        <div class="ld-spec-item">
                            <span class="ld-spec-label">Tinggi Kaki</span>
                            <strong class="ld-spec-value">30 cm (Lesehan)</strong>
                        </div>
                        <div class="ld-spec-item">
                            <span class="ld-spec-label">Bahan Utama</span>
                            <strong class="ld-spec-value">Kayu Pinus Solid</strong>
                        </div>
                        <div class="ld-spec-item">
                            <span class="ld-spec-label">Beban Maksimal</span>
                            <strong class="ld-spec-value">15 kg</strong>
                        </div>
                    </div>
                </section>
            </section>

            {{-- KOLOM KANAN: INFORMASI IKLAN DAN PENJUAL --}}
            <aside class="ld-right">
                <section class="ld-card ld-product-card">
                    <div class="ld-badges">
                        <span class="ld-badge ld-badge-primary">Perabot Kos</span>
                        <span class="ld-badge ld-badge-primary">Kayu Solid</span>
                        <span class="ld-badge">Kondisi {{ $conditionName }}</span>
                        <span class="ld-badge">Nego Tipis COD</span>
                    </div>

                    <h1 class="ld-product-title">{{ $listing->name ?? 'Meja Belajar Kayu Lipat' }}</h1>

                    <p class="ld-price">
                        Rp {{ number_format((float) ($listing->price ?? 75000), 0, ',', '.') }}
                    </p>

                    <div class="ld-location">
                        <span class="ld-location-icon" aria-hidden="true">⌖</span>
                        <div>
                            <strong>Sekitar Kampus UB &amp; Polinema</strong>
                            <small>Kecamatan Lowokwaru, Kota Malang</small>
                        </div>
                    </div>
                </section>

                <section class="ld-card ld-description-card">
                    <h2 class="ld-card-heading">Deskripsi &amp; Kondisi Fisik</h2>

                    <p class="ld-description">{{ $listing->description ?? 'Meja lipat kayu pinus solid ukuran 60 × 40 cm dengan tinggi 30 cm. Cocok untuk belajar di kos atau lesehan. Kondisi siap pakai dan engsel aman.' }}</p>

                    <div class="ld-condition-list">
                        <div class="ld-condition-row">
                            <span class="ld-check">●</span>
                            <span>Engsel normal dan kokoh tanpa goyang</span>
                        </div>
                        <div class="ld-condition-row">
                            <span class="ld-check">●</span>
                            <span>Permukaan kayu bebas dari noda cairan dan tinta</span>
                        </div>
                        <div class="ld-condition-row ld-minus">
                            <span class="ld-info">●</span>
                            <span><strong>Minus Fisik:</strong> Lecet pemakaian halus pada permukaan sudut kanan (cek foto thumbnail ke-3).</span>
                        </div>
                    </div>
                </section>

                <section class="ld-card ld-seller-card">
                    <div class="ld-secure-heading">
                        <span class="ld-lock-icon" aria-hidden="true">♙</span>
                        <span>Identitas Penjual Dilindungi</span>
                    </div>
                    <p class="ld-secure-subtitle">Protokol Keamanan Komunitas Mahasiswa</p>

                    <p class="ld-secure-description">
                        Informasi kontak dan lokasi kos hanya dapat diakses akun mahasiswa yang terverifikasi email kampus aktif.
                    </p>

                    <div class="ld-seller-inner">
                        <div class="ld-seller-top">
                            <div class="ld-seller-identity">
                                <div class="ld-avatar" aria-hidden="true">
                                    {{ strtoupper(substr($sellerName, 0, 1)) }}
                                </div>
                                <div style="min-width:0">
                                    <strong class="ld-seller-name">{{ $sellerName }}</strong>
                                    <span class="ld-sso-status">● SSO Kampus Aktif</span>
                                </div>
                            </div>
                            <span class="ld-verified" title="Identitas dilindungi">♙</span>
                        </div>

                        <div class="ld-seller-contact">
                            <span class="ld-contact-masked">▤ &nbsp;0812••••••••</span>
                            <span class="ld-contact-masked">⌖ &nbsp;Kos MT Haryono (Terkunci)</span>
                        </div>
                    </div>

                    <a href="{{ route('login') }}" class="ld-contact-action">
                        <span aria-hidden="true">♧</span>
                        <span>Masuk dengan Email Kampus untuk<br> Hubungi Penjual</span>
                    </a>

                    <p class="ld-campus-domains">
                        SSO resmi @student.ub.ac.id, @student.polinema.ac.id,
                        @student.um.ac.id, @webmail.umm.ac.id
                    </p>

                    <div class="ld-benefits">
                        <div class="ld-benefit">♧ &nbsp;COD Pos Satpam Kampus</div>
                        <div class="ld-benefit">⌁ &nbsp;Bebas Biaya Perantara</div>
                    </div>
                </section>

                <div class="ld-tips-card">
                    <strong>⛨ Tips Transaksi:</strong>
                    Utamakan COD di area kampus seperti Gazebo UB, Kantin Polinema,
                    atau gerbang utama demi keamanan bersama.
                </div>
            </aside>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    function changeListingImage(src, button, imageNumber) {
        const mainImage = document.getElementById('ldMainImage');
        if (!mainImage) return;

        mainImage.src = src;

        document.querySelectorAll('.ld-thumb').forEach((thumbnail) => {
            thumbnail.classList.remove('is-active');
            thumbnail.setAttribute('aria-pressed', 'false');
        });

        button.classList.add('is-active');
        button.setAttribute('aria-pressed', 'true');

        const counter = document.getElementById('ldPhotoCount');
        if (counter) {
            const total = document.querySelectorAll('.ld-thumb').length;
            counter.textContent = `${imageNumber} / ${total}`;
        }
    }
</script>
@endpush
