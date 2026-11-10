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
        padding: 9px 15px;
        color: #ffffff;
        background: var(--ld-blue); /* Menggunakan warna biru utama Lungsur.in (#2563eb) */
        border: 1px solid transparent;
        border-radius: 9px;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.15);
        transition: all 0.2s ease;
    }

    .ld-back-link:hover {
        color: #ffffff;
        background: #1d4ed8; /* Warna biru sedikit lebih gelap saat kursor diarahkan (hover) */
        border-color: #1e40af;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        transform: translateY(-1px); /* Efek sedikit terangkat saat di-hover */
    }

    .ld-back-link:active {
        transform: translateY(0); /* Efek membal saat tombol benar-benar diklik */
        background: #1e40af;
        box-shadow: none;
    }

  /* Efek Border Neon Hijau Berjalan secara Otomatis saat di Halaman */
    .ld-crumbs {
        position: relative;
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        background: #ffffff;
        border: 1px solid var(--ld-border);
        border-radius: 12px;
        /* Kotak langsung memancarkan cahaya hijau lembut secara permanen */
        box-shadow: 0 0 15px #1e40af(16, 185, 129, 0.3);
        overflow: hidden;
        z-index: 1;
    }

    /* Lapisan animasi cahaya biru yang berputar otomatis */
    .ld-crumbs::before {
        content: '';
        position: absolute;
        inset: -50%;
        background: conic-gradient(from 0deg at 50% 50%, transparent 0deg, transparent 270deg, #10b981 360deg);
        animation: rotateGlow 3s linear infinite;
        z-index: -2;
    }

    /* Menutup bagian tengah agar tetap berwarna putih bersih dan hanya pinggirannya yang bercahaya */
    .ld-crumbs::after {
        content: '';
        position: absolute;
        inset: 1.5px;
        background: #ffffff;
        border-radius: 10.5px;
        z-index: -1;
    }

    @keyframes rotateGlow {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    .ld-crumbs:hover {
        box-shadow: 0 0 15px #1e40af(16, 185, 129, 0.35);
        color: var(--ld-blue);
    }

    
  .ld-crumb-current { 
        color: #172033; 
        font-weight: 700;
        max-width: 280px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

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
        overflow: visible !important;
        border-radius: 9px;
        background: #eef2f7;
    }

    .ld-main-photo img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 9px;
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
        z-index: 10;
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
        z-index: 10;
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

    $categoryName = $listing->category->name ?? 'Perabot';
    $conditionName = $listing->condition ?? 'Bekas';
    $sellerName = $listing->user->name ?? 'Mahasiswa UB (Fakultas Terverifikasi)';
@endphp

<main class="listing-detail-page">
    <div class="ld-container">
        <nav class="ld-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="ld-back-link">
                <span aria-hidden="true">←</span> Kembali ke Katalog
            </a>

            <div class="ld-crumbs">
                <a href="{{ url('/') }}">Home</a>
                <span>/</span>
                <span class="ld-crumb-category" style="color: #64748b; font-weight: 500;">{{ $categoryName }}</span>
                <span class="ld-crumb-current">{{ $listing->name }}</span>
            </div>
        </nav>

        <div class="ld-layout">
            {{-- KOLOM KIRI: GALERI DAN SPESIFIKASI --}}
            <section class="ld-left" aria-label="Foto dan spesifikasi barang">
                <div class="ld-card ld-gallery">
                    {{-- FOTO UTAMA DENGAN TOMBOL NAVIGASI KIRI & KANAN --}}
                    <div class="ld-main-photo position-relative">
                        @if ($images->count() > 0)
                            @php
                                $firstUrl = $images->first()->image_url ?? '';
                                $resolvedFirstUrl = Str::startsWith($firstUrl, ['http://', 'https://']) 
                                    ? $firstUrl 
                                    : asset('storage/' . ltrim($firstUrl, '/'));
                            @endphp
                            <img
                                id="ldMainImage"
                                src="{{ $resolvedFirstUrl }}"
                                alt="{{ $listing->title }}"
                            >
                            
                            <div class="ld-original-badge">
                                  <span>●</span> Foto Asli Penjual (Bukan Katalog)
                            </div>

                          <div class="ld-photo-count" id="ldPhotoCount">
                              1 / {{ $images->count() }}
                          </div>

                          {{-- Tombol Navigasi Kiri & Kanan --}}
                          @if($images->count() > 1)
                            <button type="button" onclick="prevSlide()" style="position: absolute; top: 50%; left: 12px; transform: translateY(-50%); width: 40px; height: 40px; background: white; color: #172033; border: none; border-radius: 50%; font-weight: bold; font-size: 18px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 30; display: flex; align-items: center; justify-content: center;">
                              ❮
                            </button>
                            <button type="button" onclick="nextSlide()" style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%); width: 40px; height: 40px; background: white; color: #172033; border: none; border-radius: 50%; font-weight: bold; font-size: 18px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 30; display: flex; align-items: center; justify-content: center;">
                              ❯
                            </button>
                          @endif
                        @else
                            <div class="ld-no-photo">
                                <span>Foto belum tersedia</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($images->count() > 0)
                    <div class="ld-thumbnails" aria-label="Galeri foto barang">
                        @foreach($images as $index => $image)
                            @php
                                $caption = $thumbnailTitles[$index] ?? ['Foto ' . ($index + 1), 'Detail barang'];
                                $rawUrl = $image->image_url ?? '';
                                $imageUrl = Str::startsWith($rawUrl, ['http://', 'https://']) 
                                    ? $rawUrl 
                                    : asset('storage/' . ltrim($rawUrl, '/'));
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
                        @if($listing->category)
                            <span class="ld-badge ld-badge-primary">{{ $listing->category->name }}</span>
                        @endif
                        <span class="ld-badge ld-badge-primary">Kayu Solid</span>
                        <span class="ld-badge">Kondisi {{ $conditionName ?? 'Bekas' }}</span>
                        <span class="ld-badge">Nego Tipis COD</span>
                    </div>

                    <h1 class="ld-product-title">{{ $listing->title }}</h1>

                    <p class="ld-price">
                        Rp {{ number_format((float) $listing->price, 0, ',', '.') }}
                    </p>

                    <div class="ld-location">
                        <span class="ld-location-icon" aria-hidden="true">⌖</span>
                        <div>
                            <strong>{{ $listing->location ?? 'Sekitar Kampus UB & Polinema' }}</strong>
                            <small>Kecamatan Lowokwaru, Kota Malang</small>
                        </div>
                    </div>
                </section>

                <section class="ld-card ld-description-card">
                    <h2 class="ld-card-heading">Deskripsi &amp; Kondisi Fisik</h2>
                    <p class="ld-description">{{ $listing->description }}</p>
                </section>

                <section class="ld-card ld-seller-card">
                    @guest
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
                                        {{ strtoupper(substr($sellerName ?? 'B', 0, 1)) }}
                                    </div>
                                    <div style="min-width:0">
                                        <strong class="ld-seller-name">{{ $sellerName ?? 'Budi Santoso' }}</strong>
                                        <span class="ld-sso-status">● SSO Kampus Aktif</span>
                                    </div>
                                </div>
                                <span class="ld-verified" title="Identitas dilindungi">♙</span>
                            </div>

                            <div class="ld-seller-contact">
                                <span class="ld-contact-masked">▤ &nbsp;0812••••••••</span>
                                <span class="ld-contact-masked">⌖ &nbsp;Kos Tersembunyi [Tidak di tampilkan]</span>
                            </div>
                        </div>

                        <a href="{{ route('login') }}" class="ld-contact-action">
                            <span aria-hidden="true">♧</span>
                            <span>Masuk dengan Email Kampus untuk<br> Hubungi Penjual</span>
                        </a>
                    @else
                        <div class="ld-secure-heading">
                            <span class="ld-lock-icon" aria-hidden="true">✓</span>
                            <span>Informasi Penjual</span>
                        </div>
                        <p class="ld-secure-subtitle">Akun Mahasiswa Terverifikasi</p>

                        <div class="ld-seller-inner mb-3">
                            <div class="ld-seller-top">
                                <div class="ld-seller-identity">
                                    <div class="ld-avatar" aria-hidden="true">
                                        {{ strtoupper(substr($listing->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div style="min-width:0">
                                        <strong class="ld-seller-name">{{ $listing->user->name ?? 'Penjual' }}</strong>
                                        <span class="ld-sso-status text-success">● Terverifikasi</span>
                                    </div>
                                </div>
                            </div>

                            <div class="ld-seller-contact mt-2">
                                <div>📞 {{ $listing->user->phone ?? '08123456789' }}</div>
                                <div>📍 {{ $listing->location }}</div>
                            </div>
                        </div>

                        <a href="#" onclick="alert('Fitur chat sedang dalam tahap pengembangan.'); return false;" class="ld-contact-action btn-primary text-center" style="display: block; background: #2563eb; color: #fff; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                            <span>💬 Hubungi Penjual (Chat)</span>
                        </a>
                    @endguest

                    <p class="ld-campus-domains mt-3">
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
    let currentImageIndex = 0;
    const listingImages = [
        @foreach($images as $image)
            @php
                $rawUrl = $image->image_url ?? '';
                $resolvedUrl = Str::startsWith($rawUrl, ['http://', 'https://']) 
                    ? $rawUrl 
                    : asset('storage/' . ltrim($rawUrl, '/'));
            @endphp
            "{{ $resolvedUrl }}",
        @endforeach
    ];

    function changeListingImage(src, button, imageNumber) {
        const mainImage = document.getElementById('ldMainImage');
        if (!mainImage) return;

        mainImage.src = src;
        currentImageIndex = imageNumber - 1;

        const thumbnails = document.querySelectorAll('.ld-thumb');
        thumbnails.forEach((thumbnail, idx) => {
            if (idx === currentImageIndex) {
                thumbnail.classList.add('is-active');
                thumbnail.setAttribute('aria-pressed', 'true');
            } else {
                thumbnail.classList.remove('is-active');
                thumbnail.setAttribute('aria-pressed', 'false');
            }
        });

        updatePhotoCounter(imageNumber);
    }

    function updatePhotoCounter(imageNumber) {
        const counter = document.getElementById('ldPhotoCount');
        if (counter && listingImages.length > 0) {
            counter.textContent = `${imageNumber} / ${listingImages.length}`;
        }
    }

    function nextSlide() {
        if (listingImages.length === 0) return;
        currentImageIndex = (currentImageIndex + 1) % listingImages.length;
        triggerSlideChange();
    }

    function prevSlide() {
        if (listingImages.length === 0) return;
        currentImageIndex = (currentImageIndex - 1 + listingImages.length) % listingImages.length;
        triggerSlideChange();
    }

    function triggerSlideChange() {
        const nextUrl = listingImages[currentImageIndex];
        const thumbnails = document.querySelectorAll('.ld-thumb');
        if (thumbnails[currentImageIndex]) {
            thumbnails[currentImageIndex].click();
        } else {
            const mainImage = document.getElementById('ldMainImage');
            if (mainImage) mainImage.src = nextUrl;
            updatePhotoCounter(currentImageIndex + 1);
        }
    }
</script>
@endpush