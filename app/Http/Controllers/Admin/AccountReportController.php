<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountReportController extends Controller
{
    // Menampilkan daftar laporan akun
    public function index()
    {
        $reports = Report::where('report_type', 'account')
            ->with([
                'reporter',
                'reportedUser',
                'conversation',
                'reportedMessage',
            ])
            ->latest()
            ->paginate(10);

        return view('admin.reports.accounts.index', compact('reports'));
    }

    // Menampilkan detail laporan akun beserta bukti percakapan
    public function show(Report $report)
    {
        abort_if($report->report_type !== 'account', 404);

        $report->load([
            'reporter',
            'reportedUser',
            'resolver',
            'conversation.buyer',
            'conversation.seller',
            'conversation.listing',
            'conversation.messages.sender',
            'reportedMessage.sender',
        ]);

        return view('admin.reports.accounts.show', compact('report'));
    }

    // Mengabaikan laporan akun
    public function ignore(Report $report)
    {
        abort_if($report->report_type !== 'account', 404);
        abort_if($report->status !== 'pending', 422, 'Laporan ini sudah ditangani.');

        $report->update([
            'status' => 'reviewed',
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        return redirect()->route('admin.account_reports.index')
            ->with('success', 'Laporan akun berhasil diabaikan.');
    }

    // Memberikan ban sementara kepada akun yang dilaporkan
    public function temporaryBan(Request $request, Report $report)
    {
        abort_if($report->report_type !== 'account', 404);
        abort_if($report->status !== 'pending', 422, 'Laporan ini sudah ditangani.');

        $validated = $request->validate([
            'ban_reason' => 'required|string|max:1000',
            'ban_days' => 'required|integer|min:1|max:365',
        ]);

        $reportedUser = $report->reportedUser;

        abort_if(!$reportedUser, 404, 'Akun yang dilaporkan tidak ditemukan.');

        // Admin tidak dapat dikenai ban melalui laporan akun.
        abort_if($reportedUser->role === 'admin', 422, 'Akun admin tidak dapat diblokir melalui fitur ini.');

        // Admin tidak dapat memblokir akunnya sendiri.
        abort_if($reportedUser->id === Auth::id(), 422, 'Anda tidak dapat memblokir akun sendiri.');

        DB::transaction(function () use ($report, $reportedUser, $validated) {
            // Memblokir akun sementara sesuai durasi yang dipilih.
            $reportedUser->update([
                'status' => 'suspended',
                'ban_reason' => $validated['ban_reason'],
                'banned_at' => now(),
                'ban_expires_at' => now()->addDays((int) $validated['ban_days']),
                'banned_by' => Auth::id(),
            ]);

            // Menandai laporan sebagai selesai ditangani.
            $report->update([
                'status' => 'resolved',
                'resolved_by' => Auth::id(),
                'resolved_at' => now(),
            ]);
        });

        return redirect()->route('admin.account_reports.index')
            ->with('success', 'Akun berhasil diblokir sementara selama ' . $validated['ban_days'] . ' hari.');
    }

    // Memberikan ban permanen kepada akun yang dilaporkan
    public function permanentBan(Request $request, Report $report)
    {
        abort_if($report->report_type !== 'account', 404);
        abort_if($report->status !== 'pending', 422, 'Laporan ini sudah ditangani.');

        $validated = $request->validate([
            'ban_reason' => 'required|string|max:1000',
        ]);

        $reportedUser = $report->reportedUser;

        abort_if(!$reportedUser, 404, 'Akun yang dilaporkan tidak ditemukan.');

        // Admin tidak dapat dikenai ban melalui laporan akun.
        abort_if($reportedUser->role === 'admin', 422, 'Akun admin tidak dapat diblokir melalui fitur ini.');

        // Admin tidak dapat memblokir akunnya sendiri.
        abort_if($reportedUser->id === Auth::id(), 422, 'Anda tidak dapat memblokir akun sendiri.');

        DB::transaction(function () use ($report, $reportedUser, $validated) {
            // Memblokir akun secara permanen.
            $reportedUser->update([
                'status' => 'banned',
                'ban_reason' => $validated['ban_reason'],
                'banned_at' => now(),
                'ban_expires_at' => null,
                'banned_by' => Auth::id(),
            ]);

            // Menandai laporan sebagai selesai ditangani.
            $report->update([
                'status' => 'resolved',
                'resolved_by' => Auth::id(),
                'resolved_at' => now(),
            ]);
        });

        return redirect()->route('admin.account_reports.index')
            ->with('success', 'Akun berhasil diblokir secara permanen.');
    }
}