<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    // Menyimpan laporan dari pengguna
    public function store(Request $request, Listing $listing)
    {
        abort_if(
            $listing->user_id === Auth::id(),
            403,
            'Anda tidak dapat melaporkan iklan sendiri.'
        );

        $validated = $request->validate([
            'reason' => [
                'required',
                'string',
                Rule::in([
                    'Indikasi penipuan',
                    'Foto tidak pantas / bukan barang asli',
                    'Harga tidak wajar',
                    'Spam / iklan ganda',
                    'Barang terlarang',
                    'Lainnya',
                ]),
            ],
            'details' => 'nullable|string|max:1000',
        ]);

        $duplicateReport = Report::where('reporter_id', Auth::id())
            ->where('reported_listing_id', $listing->id)
            ->where('status', 'pending')
            ->exists();

        if ($duplicateReport) {
            return back()->with('error', 'Anda sudah melaporkan iklan ini dan laporan masih menunggu pemeriksaan.');
        }

        Report::create([
            'reporter_id' => Auth::id(),
            'reported_listing_id' => $listing->id,
            'reported_user_id' => $listing->user_id,
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Laporan berhasil dikirim.');
    }

    public function index()
    {
        $reports = Report::with(['reporter', 'reportedListing', 'reportedUser'])
            ->latest()
            ->paginate(10);

        return view('admin.verifications.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load(['reporter', 'reportedListing.images', 'reportedUser', 'resolver']);

        $sameListingReports = Report::where('reported_listing_id', $report->reported_listing_id)->count();

        return view('admin.verifications.reports.show', compact('report', 'sameListingReports'));

    }

    public function ignore(Report $report)
    {
        abort_if($report->status !== 'pending', 422, 'Laporan ini sudah ditangani.');

        $report->update([
            'status'      => 'reviewed',
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Laporan berhasil diabaikan.');
    }

    public function takeDown(Report $report)
    {
        abort_if($report->status !== 'pending', 422, 'Laporan ini sudah ditangani.');

        $report->reportedListing?->delete(); // soft delete

        // Tutup semua laporan pending lain untuk iklan yang sama
        Report::where('reported_listing_id', $report->reported_listing_id)
            ->where('status', 'pending')
            ->update([
                'status'      => 'resolved',
                'resolved_by' => Auth::id(),
                'resolved_at' => now(),
            ]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Iklan berhasil diturunkan.');
    }

    /**
     * Blokir akun penjual terlapor sekaligus turunkan iklannya (WBS 4.2 / UC 14).
     */
    public function banSeller(Request $request, Report $report)
    {
        abort_if($report->status !== 'pending', 422, 'Laporan ini sudah ditangani.');

        $seller = $report->reportedUser ?? $report->reportedListing?->user;
        abort_if(!$seller, 404, 'Data penjual terlapor tidak ditemukan.');
        abort_if($seller->role === 'admin', 403, 'Tidak dapat memblokir administrator.');

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $banReason = !empty($validated['reason'])
            ? $validated['reason']
            : 'Diblokir admin akibat laporan terverifikasi: ' . $report->reason;

        $seller->update([
            'status' => 'banned',
            'rejection_reason' => $banReason,
        ]);

        // Turunkan seluruh iklan aktif milik penjual
        $seller->listings()->where('status', 'available')->update(['status' => 'archived']);
        $report->reportedListing?->delete();

        // Selesaikan semua laporan pending untuk user/iklan ini
        Report::where('reported_user_id', $seller->id)
            ->where('status', 'pending')
            ->update([
                'status'      => 'resolved',
                'resolved_by' => Auth::id(),
                'resolved_at' => now(),
            ]);

        return redirect()->route('admin.reports.index')
            ->with('success', "Akun penjual {$seller->name} berhasil diblokir permanen dan seluruh iklan aktif miliknya diturunkan.");
    }
}