@extends('layouts.app')

@section('title', ($listing->title ?? $listing->name ?? 'Detail Barang') . ' - Lungsur.in')

@push('styles')
<style>
    .listing-detail-page {
        --ld-blue: #2563eb;
        --ld-border: #dfe6ef;
        --ld-muted: #475569;
        --ld-ink: #0f172a;
        background: #f8fafc;
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
        gap: 12px;
        margin: 0 0 18px;
        font-size: 13px;
    }

    .ld-back-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 9px 16px;
        color: #ffffff;
        background: var(--ld-blue);
        border: 1px solid transparent;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.15);
        transition: all 0.2s ease;
    }

    .ld-back-link:hover {
        color: #ffffff;
        background: #1d4ed8;
        border-color: #1e40af;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.22);
    }

    .ld-back-link:focus-visible {
        outline: 2px solid #1d4ed8;
        outline-offset: 2px;
    }

    .ld-crumbs {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        padding: 9px 16px;
        background: #ffffff;
        border: 1px solid var(--ld-border);
        border-radius: 10px;
        color: #475569;
    }

    .ld-crumbs a {
        color: #334155;
        font-weight: 600;
        text-decoration: none;
    }

    .ld-crumbs a:hover {
        color: var(--ld-blue);
        text-decoration: underline;
    }

    .ld-crumb-current {
        color: #0f172a;
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
        background: #ffffff;
        border: 1px solid var(--ld-border);
        border-radius: 14px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .03);
    }

    .ld-gallery {
        padding: 8px;
    }

    .ld-main-photo {
        position: relative;
        height: 420px;
        overflow: hidden;
        border-radius: 10px;
        background: #eef2f7;
    }

    .ld-main-photo img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }

    .ld-no-photo {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        height: 100%;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
    }

    .ld-original-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        color: #0f172a;
        background: rgba(255, 255, 255, .96);
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .08);
        font-size: 11px;
        font-weight: 700;
        z-index: 10;
    }

    .ld-original-badge span {
        color: #047857;
        font-size: 13px;
    }

    .ld-photo-count {
        position: absolute;
        bottom: 12px;
        left: 12px;
        padding: 5px 10px;
        color: #ffffff;
        background: rgba(15, 23, 42, .85);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        z-index: 10;
    }

    .ld-slide-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.96);
        color: #0f172a;
        border: 1px solid #cbd5e1;
        border-radius: 9999px;
        font-weight: 800;
        font-size: 16px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.14);
        z-index: 30;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.18s ease, border-color 0.18s ease;
    }

    .ld-slide-btn:hover {
        background: #ffffff;
        border-color: var(--ld-blue);
        color: var(--ld-blue);
    }

    .ld-slide-btn:focus-visible {
        outline: 2px solid var(--ld-blue);
        outline-offset: 2px;
    }

    .ld-slide-btn-prev { left: 12px; }
    .ld-slide-btn-next { right: 12px; }

    .ld-thumbnails {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin-top: 12px;
    }

    .ld-thumb {
        min-width: 0;
        min-height: 44px;
        padding: 4px;
        overflow: hidden;
        text-align: left;
        color: #334155;
        background: #ffffff;
        border: 1px solid var(--ld-border);
        border-radius: 10px;
        cursor: pointer;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .ld-thumb:hover,
    .ld-thumb.is-active {
        border: 2px solid var(--ld-blue);
        padding: 3px;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .08);
    }

    .ld-thumb:focus-visible {
        outline: 2px solid var(--ld-blue);
        outline-offset: 2px;
    }

    .ld-thumb img {
        display: block;
        width: 100%;
        height: 112px;
        object-fit: cover;
        border-radius: 6px;
        background: #eef2f7;
    }

    .ld-thumb-title {
        display: block;
        margin: 7px 2px 2px;
        overflow: hidden;
        color: #0f172a;
        font-size: 11px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ld-thumb-subtitle {
        display: block;
        margin: 0 2px 3px;
        overflow: hidden;
        color: #475569;
        font-size: 10px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ld-spec-card {
        margin-top: 14px;
        padding: 16px;
    }

    .ld-card-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin: 0 0 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e8edf4;
        color: #0f172a;
        font-size: 15px;
        font-weight: 800;
    }

    .ld-heading-note {
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .ld-spec-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
    }

    .ld-spec-item {
        min-width: 0;
        padding: 11px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }

    .ld-spec-label {
        display: block;
        margin-bottom: 4px;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
    }

    .ld-spec-value {
        display: block;
        color: #0f172a;
        font-size: 12px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .ld-product-card { padding: 16px; }

    .ld-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
    }

    .ld-badge {
        padding: 5px 10px;
        color: #334155;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .ld-badge-primary {
        color: #1d4ed8;
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .ld-badge-verified {
        color: #065f46;
        background: #ecfdf5;
        border-color: #a7f3d0;
    }

    .ld-product-title {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: clamp(20px, 2.2vw, 26px);
        line-height: 1.25;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .ld-price {
        margin: 0 0 12px;
        padding-bottom: 10px;
        color: var(--ld-blue);
        border-bottom: 1px solid #e8edf4;
        font-size: 24px;
        line-height: 1.2;
        font-weight: 900;
    }

    .ld-location {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 11px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }

    .ld-location-icon {
        color: var(--ld-blue);
        flex-shrink: 0;
        margin-top: 2px;
    }

    .ld-location strong {
        display: block;
        color: #0f172a;
        font-size: 12px;
        font-weight: 700;
    }

    .ld-location small {
        display: block;
        margin-top: 3px;
        color: #475569;
        font-size: 11px;
        font-weight: 500;
    }

    .ld-description-card { padding: 15px 16px; }

    .ld-description {
        margin: 0;
        color: #334155;
        font-size: 13px;
        line-height: 1.65;
        white-space: pre-line;
    }

    .ld-seller-card { padding: 15px 16px; }

    .ld-secure-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
        color: #0f172a;
        font-size: 14px;
        font-weight: 800;
    }

    .ld-lock-icon {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        flex: 0 0 auto;
        color: var(--ld-blue);
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 9px;
    }

    .ld-secure-subtitle {
        margin: 0 0 10px 42px;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
    }

    .ld-secure-description {
        margin: 0 0 10px;
        color: #475569;
        font-size: 12px;
        line-height: 1.55;
    }

    .ld-seller-inner {
        padding: 11px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }

    .ld-seller-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }

    .ld-seller-identity {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .ld-avatar {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        flex: 0 0 auto;
        color: #1d4ed8;
        background: #dbeafe;
        border-radius: 50%;
        font-size: 14px;
        font-weight: 800;
    }

    .ld-seller-name {
        display: block;
        overflow: hidden;
        color: #0f172a;
        font-size: 12px;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ld-sso-status {
        display: block;
        margin-top: 2px;
        color: #047857;
        font-size: 11px;
        font-weight: 700;
    }

    .ld-seller-contact {
        display: grid;
        grid-template-columns: 1fr;
        gap: 6px;
        color: #334155;
        font-size: 12px;
        font-weight: 600;
    }

    .ld-contact-masked {
        min-width: 0;
        padding: 7px 9px;
        overflow: hidden;
        color: #475569;
        background: #ffffff;
        border: 1px solid #dce5ef;
        border-radius: 7px;
        font-size: 11px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ld-contact-action {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        width: 100%;
        min-height: 48px;
        margin-top: 12px;
        padding: 12px 16px;
        color: #ffffff;
        background: var(--ld-blue);
        border: 0;
        border-radius: 10px;
        text-align: center;
        text-decoration: none;
        font-size: 13px;
        line-height: 1.35;
        font-weight: 800;
        transition: background .2s ease;
    }

    .ld-contact-action:hover {
        color: #ffffff;
        background: #1d4ed8;
    }

    .ld-contact-action:focus-visible {
        outline: 2px solid #1d4ed8;
        outline-offset: 2px;
    }

    .ld-campus-domains {
        margin: 10px 0 12px;
        color: #475569;
        text-align: center;
        font-size: 11px;
        line-height: 1.5;
    }

    .ld-benefits {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .ld-benefit {
        padding: 9px 8px;
        color: #334155;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
    }

    .ld-tips-card {
        padding: 12px 14px;
        color: #334155;
        background: #f8fafc;
        border: 1px solid var(--ld-border);
        border-radius: 10px;
        font-size: 12px;
        line-height: 1.55;
    }

    @media (max-width: 900px) {
        .ld-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        .ld-right { gap: 12px; }
        .ld-main-photo { height: min(64vw, 420px); }
    }

    @media (max-width: 640px) {
        .listing-detail-page { padding: 12px 12px 96px; }
        .ld-breadcrumb { gap: 8px; align-items: stretch; }
        .ld-crumbs { display: none; }
        .ld-main-photo { height: 290px; }
        .ld-original-badge {
            top: 8px;
            right: 8px;
            padding: 5px 9px;
            font-size: 10px;
        }
        .ld-thumbnails {
            display: flex;
            overflow-x: auto;
            gap: 8px;
            padding-bottom: 4px;
            scroll-snap-type: x mandatory;
        }
        .ld-thumb {
            flex: 0 0 112px;
            scroll-snap-align: start;
        }
        .ld-thumb img { height: 76px; }
        .ld-spec-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ld-card-heading { align-items: flex-start; }
    }
</style>
@endpush

@section('content')
@php
    $images = $listing->images ?? collect();
    $firstImage = $images->first();
    $itemTitle = $listing->title ?? $listing->name ?? 'Barang Kos Mahasiswa';
    $categoryName = $listing->category->name ?? 'Perlengkapan Kos';

    $rawCond = is_array($listing->condition_checklist)
        ? ($listing->condition_checklist['kondisi'] ?? ($listing->condition_checklist[0] ?? 'Bekas Baik'))
        : ($listing->condition ?? 'Bekas Baik');

    $conditionLabel = match(strtolower((string) $rawCond)) {
        'baru', 'like_new', 'seperti baru' => 'Seperti Baru',
        'bekas', 'good', 'bekas baik' => 'Bekas Baik',
        'layak', 'fair', 'layak pakai' => 'Layak Pakai',
        default => ucfirst((string) $rawCond),
    };

    $sellerName = $listing->user->name ?? 'Mahasiswa Terverifikasi';
    $sellerUniversity = $listing->user->university ?? 'Kampus Malang';
    $locationText = $listing->addresses ?: ($listing->location ?? 'Sekitar Kampus Malang');
    $activeAuction = $listing->auction ?? null;
    $isOwner = auth()->check() && auth()->id() === $listing->user_id;
@endphp

<main class="listing-detail-page">
    <div class="ld-container mb-4">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="ld-container">
        <nav class="ld-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('listings.index') }}" class="ld-back-link">
                <span aria-hidden="true">&larr;</span>
                <span>Kembali ke Katalog</span>
            </a>

            <div class="ld-crumbs">
                <a href="{{ route('home') }}">Beranda</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('listings.index', ['category' => strtolower($categoryName)]) }}">{{ $categoryName }}</a>
                <span aria-hidden="true">/</span>
                <span class="ld-crumb-current">{{ $itemTitle }}</span>
            </div>
        </nav>

        <div class="ld-layout">
            <section class="ld-left" aria-label="Foto dan spesifikasi barang">
                <div class="ld-card ld-gallery">
                    <div class="ld-main-photo" id="ldMainPhotoContainer">
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
                                alt="{{ $itemTitle }}"
                            >
                            
                            <div class="ld-original-badge">
                                <span aria-hidden="true">●</span>
                                <span>Foto Asli Penjual</span>
                            </div>

                            <div class="ld-photo-count" id="ldPhotoCount">
                                1 / {{ $images->count() }}
                            </div>

                            @if($images->count() > 1)
                                <button type="button"
                                        onclick="prevSlide()"
                                        class="ld-slide-btn ld-slide-btn-prev"
                                        aria-label="Foto sebelumnya">
                                    &#10094;
                                </button>
                                <button type="button"
                                        onclick="nextSlide()"
                                        class="ld-slide-btn ld-slide-btn-next"
                                        aria-label="Foto berikutnya">
                                    &#10095;
                                </button>
                            @endif
                        @else
                            <div class="ld-no-photo">
                                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Foto belum tersedia</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($images->count() > 0)
                    <div class="ld-thumbnails" aria-label="Galeri foto barang">
                        @foreach($images as $index => $image)
                            @php
                                $caption = $thumbnailTitles[$index] ?? ['Foto ' . ($index + 1), 'Tampak barang'];
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
                                <img src="{{ $imageUrl }}" alt="Foto {{ $index + 1 }} {{ $itemTitle }}">
                                <span class="ld-thumb-title">{{ $caption[0] }}</span>
                                <span class="ld-thumb-subtitle">{{ $caption[1] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif

                <section class="ld-card ld-spec-card">
                    <div class="ld-card-heading">
                        <span>Ringkasan &amp; Status Barang</span>
                        <span class="ld-heading-note">Diperbarui {{ $listing->updated_at ? $listing->updated_at->diffForHumans() : 'baru-baru ini' }}</span>
                    </div>

                    <div class="ld-spec-grid">
                        <div class="ld-spec-item">
                            <span class="ld-spec-label">Kategori</span>
                            <span class="ld-spec-value">{{ $categoryName }}</span>
                        </div>
                        <div class="ld-spec-item">
                            <span class="ld-spec-label">Kondisi Fisik</span>
                            <span class="ld-spec-value">{{ $conditionLabel }}</span>
                        </div>
                        <div class="ld-spec-item">
                            <span class="ld-spec-label">Kampus Penjual</span>
                            <span class="ld-spec-value">{{ $sellerUniversity }}</span>
                        </div>
                        <div class="ld-spec-item">
                            <span class="ld-spec-label">Status Iklan</span>
                            <span class="ld-spec-value">
                                {{ $listing->status === 'sold' ? 'Terjual' : ($listing->status === 'reserved' ? 'Dipesan' : 'Tersedia') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        @if ($activeAuction && $activeAuction->status === 'active')
                            <div class="text-xs text-slate-700 font-semibold">
                                Barang ini sedang dibuka pada sesi <strong>Lelang Mahasiswa</strong>.
                            </div>
                            <a href="{{ route('auctions.show', $activeAuction->id) }}"
                               class="min-h-[44px] px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-extrabold inline-flex items-center justify-center gap-2 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600">
                                <span>Lihat Sesi Lelang</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        @else
                            <div class="text-xs text-slate-600 font-medium">
                                Transaksi langsung harga pas atau nego sopan lewat ruang obrolan Lungsur.in.
                            </div>
                            <a href="{{ route('auctions.index') }}"
                               class="min-h-[44px] px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span>Cek Barang Lelang Lainnya</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        @endif
                    </div>
                </section>
            </section>

            <aside class="ld-right">
                <section class="ld-card ld-product-card">
                    <div class="ld-badges">
                        <span class="ld-badge ld-badge-primary">{{ $categoryName }}</span>
                        <span class="ld-badge">Kondisi: {{ $conditionLabel }}</span>
                        @if ($listing->user && $listing->user->is_verified)
                            <span class="ld-badge ld-badge-verified">KTM Terverifikasi</span>
                        @endif
                        <span class="ld-badge">Nego &amp; COD Kampus</span>
                    </div>

                    <h1 class="ld-product-title">{{ $itemTitle }}</h1>

                    <p class="ld-price">
                        Rp {{ number_format((float) $listing->price, 0, ',', '.') }}
                    </p>

                    <div class="ld-location">
                        <svg class="w-4 h-4 ld-location-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <strong>{{ $locationText }}</strong>
                            <small>Area Kampus {{ $sellerUniversity }}, Kota Malang</small>
                        </div>
                    </div>
                </section>

                <section class="ld-card ld-description-card">
                    <h2 class="ld-card-heading">Deskripsi &amp; Kondisi Fisik</h2>
                    <p class="ld-description">{{ $listing->description ?: 'Penjual belum menambahkan catatan deskripsi tambahan. Silakan tanyakan detail kondisi fisik barang melalui fitur Chat.' }}</p>
                </section>

                <section class="ld-card ld-seller-card">
                    @guest
                        <div class="ld-secure-heading">
                            <span class="ld-lock-icon" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <span>Identitas Penjual Dilindungi</span>
                        </div>
                        <p class="ld-secure-subtitle">Keamanan Komunitas Mahasiswa Malang</p>
                        <p class="ld-secure-description">
                            Kontak dan titik temu kos penjual hanya dapat diakses oleh akun mahasiswa yang sudah masuk.
                        </p>

                        <div class="ld-seller-inner">
                            <div class="ld-seller-top">
                                <div class="ld-seller-identity">
                                    <div class="ld-avatar" aria-hidden="true">
                                        {{ strtoupper(substr($sellerName, 0, 1)) }}
                                    </div>
                                    <div style="min-width:0">
                                        <strong class="ld-seller-name">{{ $sellerName }}</strong>
                                        <span class="ld-sso-status">● Mahasiswa {{ $sellerUniversity }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="ld-seller-contact">
                                <span class="ld-contact-masked">Kontak Chat: Hanya untuk akun login</span>
                                <span class="ld-contact-masked">Titik COD: {{ $locationText }}</span>
                            </div>
                        </div>

                        <a href="{{ route('login') }}" class="ld-contact-action">
                            <span>Masuk Akun Mahasiswa untuk Hubungi Penjual</span>
                        </a>
                    @else
                        <div class="ld-secure-heading">
                            <span class="ld-lock-icon" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            <span>Informasi Penjual</span>
                        </div>
                        <p class="ld-secure-subtitle">Akun Mahasiswa {{ $sellerUniversity }}</p>

                        <div class="ld-seller-inner mb-3">
                            <div class="ld-seller-top">
                                <div class="ld-seller-identity">
                                    <div class="ld-avatar" aria-hidden="true">
                                        {{ strtoupper(substr($sellerName, 0, 1)) }}
                                    </div>
                                    <div style="min-width:0">
                                        <strong class="ld-seller-name">{{ $sellerName }}</strong>
                                        <span class="ld-sso-status">
                                            ● {{ ($listing->user && $listing->user->is_verified) ? 'KTM Terverifikasi' : 'Mahasiswa Terdaftar' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="ld-seller-contact mt-2">
                                <div>Titik COD: {{ $locationText }}</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            @if ($isOwner)
                                @if (auth()->user()->isSeller())
                                    <a href="{{ route('iklan.edit', $listing->id) }}"
                                       class="w-full min-h-[48px] py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-sm transition flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Kelola / Edit Iklan Saya</span>
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}"
                                       class="w-full min-h-[48px] py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-sm transition flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900">
                                        <span>Buka Dashboard Saya</span>
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('chat.index', ['listing_id' => $listing->id]) }}"
                                   class="w-full min-h-[48px] py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <span>Hubungi Penjual (Chat)</span>
                                </a>
                            @endif
                        </div>
                    @endguest

                    <p class="ld-campus-domains mt-3">
                        Mendukung civitas UB, Polinema, UM, UMM, dan UIN Maliki Malang
                    </p>

                    <div class="ld-benefits">
                        <div class="ld-benefit">COD Area Kampus &amp; Kos</div>
                        <div class="ld-benefit">Tanpa Biaya Perantara</div>
                    </div>
                </section>

                <div class="ld-tips-card">
                    <strong class="text-slate-900">Tips Transaksi Aman:</strong>
                    Utamakan COD di area ramai kampus seperti Gazebo UB, Kantin Polinema, atau gerbang kampus utama. Cek fungsi barang sebelum membayar.
                </div>

                @auth
                    @if (!$isOwner)
                        <button type="button"
                                onclick="openReportModal()"
                                class="min-h-[44px] flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-3 text-xs sm:text-sm font-bold text-red-700 transition hover:bg-red-50 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                            <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                            </svg>
                            <span>Laporkan Iklan Ini</span>
                        </button>

                        <div id="reportModal"
                             role="dialog"
                             aria-modal="true"
                             aria-labelledby="reportModalTitle"
                             class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">

                            <div class="w-full max-w-lg rounded-2xl bg-white p-5 sm:p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
                                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4">
                                    <div>
                                        <h2 id="reportModalTitle" class="text-lg sm:text-xl font-bold text-slate-900">Laporkan Barang</h2>
                                        <p class="mt-1 text-xs sm:text-sm text-slate-600">
                                            Bantu kami menjaga keamanan transaksi komunitas Lungsur.in.
                                        </p>
                                    </div>

                                    <button type="button"
                                            onclick="closeReportModal()"
                                            aria-label="Tutup modal laporan"
                                            class="w-11 h-11 shrink-0 rounded-xl flex items-center justify-center text-2xl leading-none text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                        &times;
                                    </button>
                                </div>

                                <form method="POST"
                                      action="{{ route('reports.store', $listing->id) }}"
                                      class="mt-5">
                                    @csrf

                                    <label for="reportReason" class="mb-2 block text-sm font-bold text-slate-800">
                                        Pilih alasan laporan <span class="text-red-600">*</span>
                                    </label>

                                    <select id="reportReason" name="reason" required
                                            class="w-full min-h-[44px] rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm text-slate-800 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                                        <option value="">Pilih alasan laporan</option>
                                        <option value="Indikasi penipuan">Indikasi penipuan</option>
                                        <option value="Foto tidak pantas / bukan barang asli">Foto tidak pantas / bukan barang asli</option>
                                        <option value="Harga tidak wajar">Harga tidak wajar</option>
                                        <option value="Spam / iklan ganda">Spam / iklan ganda</option>
                                        <option value="Barang terlarang">Barang terlarang</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>

                                    <label for="reportDetails" class="mb-2 mt-4 block text-sm font-bold text-slate-800">
                                        Detail tambahan <span class="font-normal text-slate-500">(Opsional)</span>
                                    </label>

                                    <textarea id="reportDetails" name="details" rows="4" maxlength="1000"
                                              placeholder="Jelaskan alasan kamu melaporkan barang ini..."
                                              class="w-full resize-none rounded-xl border border-slate-300 px-3.5 py-3 text-base sm:text-sm text-slate-800 placeholder-slate-500 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"></textarea>

                                    <p class="mt-2 text-xs text-slate-600">
                                        Laporan akan ditinjau oleh tim moderator Lungsur.in.
                                    </p>

                                    <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                                        <button type="button"
                                                onclick="closeReportModal()"
                                                class="min-h-[44px] rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-100 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600">
                                            Batal
                                        </button>

                                        <button type="submit"
                                                class="min-h-[44px] rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                            Kirim Laporan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
            </aside>
        </div>
    </div>

    <div class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-4 py-2.5 shadow-[0_-4px_16px_rgba(15,23,42,0.06)]">
        <div class="flex items-center justify-between gap-3 max-w-xl mx-auto">
            <div class="min-w-0">
                <span class="block text-[11px] font-semibold text-slate-600 truncate">{{ $itemTitle }}</span>
                <span class="block text-base font-black text-blue-600">Rp {{ number_format((float) $listing->price, 0, ',', '.') }}</span>
            </div>

            @guest
                <a href="{{ route('login') }}"
                   class="shrink-0 min-h-[44px] px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs inline-flex items-center justify-center gap-1.5 shadow-sm transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <span>Masuk &amp; Chat</span>
                </a>
            @else
                @if ($isOwner)
                    @if (auth()->user()->isSeller())
                        <a href="{{ route('iklan.edit', $listing->id) }}"
                           class="shrink-0 min-h-[44px] px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs inline-flex items-center justify-center gap-1.5 shadow-sm transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900">
                            <span>Edit Iklan</span>
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                           class="shrink-0 min-h-[44px] px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs inline-flex items-center justify-center gap-1.5 shadow-sm transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900">
                            <span>Dashboard</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('chat.index', ['listing_id' => $listing->id]) }}"
                       class="shrink-0 min-h-[44px] px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs inline-flex items-center justify-center gap-1.5 shadow-sm transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Hubungi Penjual</span>
                    </a>
                @endif
            @endguest
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

    (function initTouchSwipeGallery() {
        const container = document.getElementById('ldMainPhotoContainer');
        if (!container || listingImages.length <= 1) return;

        let touchStartX = 0;
        let touchEndX = 0;

        container.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        container.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 45) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }, { passive: true });
    })();

    function openReportModal() {
        const modal = document.getElementById('reportModal');

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeReportModal() {
        const modal = document.getElementById('reportModal');

        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.getElementById('reportModal')?.addEventListener('click', function(event) {
        if (event.target === this) {
            closeReportModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeReportModal();
        }
    });
</script>
@endpush
