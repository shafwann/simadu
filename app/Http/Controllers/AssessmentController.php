<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\User;
use App\Models\AboutSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    private function gradeFromAverage(float $avg): string
    {
        if ($avg <= 30) return 'E';
        if ($avg <= 55) return 'D';
        if ($avg <= 75) return 'C';
        if ($avg <= 90) return 'B';
        return 'A';
    }

    private function labelFromGrade(string $grade): string
    {
        return match ($grade) {
            'A' => 'SANGAT BAIK',
            'B' => 'BAIK',
            'C' => 'CUKUP',
            'D' => 'KURANG',
            default => 'SANGAT KURANG',
        };
    }

    private function computeTotalAvgGrade(array $v): array
    {
        $total = (int)$v['aspek_1']
            + (int)$v['aspek_2']
            + (int)$v['aspek_3']
            + (int)$v['aspek_4']
            + (int)$v['aspek_5']
            + (int)$v['aspek_6'];

        $avg = round($total / 6, 2);
        $grade = $this->gradeFromAverage($avg);

        return [$total, $avg, $grade];
    }

    // ===========================
    // CREATE (penilaian)
    // ===========================
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'certificate_no'   => ['required', 'string', 'max:100'],
            'certificate_date' => ['required', 'date'],

            'aspek_1' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_2' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_3' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_4' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_5' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_6' => ['required', 'integer', 'min:0', 'max:100'],

            'apresiasi_khusus' => ['nullable', 'string', 'max:500'],

            // scan optional di form penilaian
            'certificate_scan' => ['nullable', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        [$total, $avg, $grade] = $this->computeTotalAvgGrade($validated);

        $scanPath = null;
        $scanOriginalName = null;

        if ($request->hasFile('certificate_scan')) {
            $file = $request->file('certificate_scan');
            $scanOriginalName = $file->getClientOriginalName();              // ✅ nama asli
            $scanPath = $file->store('certificates/scans', 'public');        // path (boleh random)
        }

        Assessment::updateOrCreate(
            ['user_id' => $user->id],
            [
                'certificate_no'   => $validated['certificate_no'],
                'certificate_date' => $validated['certificate_date'],

                'certificate_scan' => $scanPath,
                'certificate_scan_original_name' => $scanOriginalName, // ✅ simpan

                'aspek_1' => (int)$validated['aspek_1'],
                'aspek_2' => (int)$validated['aspek_2'],
                'aspek_3' => (int)$validated['aspek_3'],
                'aspek_4' => (int)$validated['aspek_4'],
                'aspek_5' => (int)$validated['aspek_5'],
                'aspek_6' => (int)$validated['aspek_6'],

                'total'        => $total,
                'average'      => $avg,
                'grade_letter' => $grade,

                'apresiasi_khusus' => $validated['apresiasi_khusus'] ?? null,
                'assessed_at' => now(),
            ]
        );

        $user->update(['status' => 'nonaktif']);

        return back()->with('success', 'Penilaian berhasil disimpan.');
    }

    // ===========================
    // UPDATE (penilaian)
    // ===========================
    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'certificate_no'   => ['required', 'string', 'max:100'],
            'certificate_date' => ['required', 'date'],

            'aspek_1' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_2' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_3' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_4' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_5' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_6' => ['required', 'integer', 'min:0', 'max:100'],

            'apresiasi_khusus' => ['nullable', 'string', 'max:500'],

            'certificate_scan' => ['nullable', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        [$total, $avg, $grade] = $this->computeTotalAvgGrade($validated);

        $newScanPath = $assessment->certificate_scan;
        $newOriginalName = $assessment->certificate_scan_original_name;

        if ($request->hasFile('certificate_scan')) {
            // hapus lama
            if ($assessment->certificate_scan) {
                Storage::disk('public')->delete($assessment->certificate_scan);
            }

            $file = $request->file('certificate_scan');
            $newOriginalName = $file->getClientOriginalName();           // ✅ nama asli
            $newScanPath = $file->store('certificates/scans', 'public'); // path (boleh random)
        }

        $assessment->update([
            'certificate_no'   => $validated['certificate_no'],
            'certificate_date' => $validated['certificate_date'],

            'certificate_scan' => $newScanPath,
            'certificate_scan_original_name' => $newOriginalName,

            'aspek_1' => (int)$validated['aspek_1'],
            'aspek_2' => (int)$validated['aspek_2'],
            'aspek_3' => (int)$validated['aspek_3'],
            'aspek_4' => (int)$validated['aspek_4'],
            'aspek_5' => (int)$validated['aspek_5'],
            'aspek_6' => (int)$validated['aspek_6'],

            'total'        => $total,
            'average'      => $avg,
            'grade_letter' => $grade,

            'apresiasi_khusus' => $validated['apresiasi_khusus'] ?? null,
            'assessed_at' => now(),
        ]);

        $assessment->user?->update(['status' => 'nonaktif']);

        return back()->with('success', 'Penilaian berhasil diperbarui.');
    }

    // ===========================
    // DELETE (penilaian)
    // ===========================
    public function destroy(Assessment $assessment)
    {
        $user = $assessment->user;

        if ($assessment->certificate_scan) {
            Storage::disk('public')->delete($assessment->certificate_scan);
        }

        $assessment->delete();

        if ($user) {
            $user->update(['status' => 'aktif']);
        }

        return back()->with('success', 'Penilaian berhasil dihapus.');
    }

    public function uploadScan(Request $request, Assessment $assessment)
    {
        $request->validate([
            'certificate_scan' => [
                'required',
                'file',
                'max:20480',
                'mimes:pdf,jpg,jpeg,png,webp',
            ],
        ]);

        // hapus file lama jika ada
        if ($assessment->certificate_scan) {
            Storage::disk('public')->delete($assessment->certificate_scan);
        }

        // ambil file
        $file = $request->file('certificate_scan');

        // simpan file baru
        $path = $file->store('certificates/scans', options: 'public');

        $originalName = $file->getClientOriginalName();

        $assessment->update([
            'certificate_scan' => $path,
            'certificate_scan_original_name' => $originalName,
        ]);

        return back()->with('success', 'Scan sertifikat berhasil diupload.');
    }

    public function destroyScan(Assessment $assessment)
    {
        if ($assessment->certificate_scan) {
            Storage::disk('public')->delete($assessment->certificate_scan);
        }

        $assessment->update([
            'certificate_scan' => null,
            'certificate_scan_original_name' => null,
        ]);

        return back()->with('success', 'Scan sertifikat berhasil dihapus.');
    }

    // ===========================
    // DOWNLOAD PDF SERTIFIKAT
    // ===========================
    public function certificate(User $user)
    {
        $assessment = $user->assessment;
        if (!$assessment) abort(404, 'Penilaian belum ada.');

        $today = Carbon::now();
        $hasilLabel = $this->labelFromGrade($assessment->grade_letter);

        $about = AboutSetting::where('kategori_instansi', $user->kategori_instansi)->first();

        // F4 landscape
        $w = 330 / 25.4 * 72;
        $h = 210 / 25.4 * 72;

        app()->setLocale('id');
        \Carbon\Carbon::setLocale('id');
        \Illuminate\Support\Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'id', 'Indonesian');

        $pdf = Pdf::loadView('admin.assessments.certificate-pdf', [
            'student' => $user,
            'assessment' => $assessment,
            'today' => $today,
            'certificateDate' => $assessment->certificate_date ? Carbon::parse($assessment->certificate_date) : $today,
            'hasilLabel' => $hasilLabel,
            'about' => $about,
        ])
            ->setPaper([0, 0, $w, $h], 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        $filename = 'Sertifikat_Magang_' . preg_replace('/\s+/', '_', $user->name) . '.pdf';
        return $pdf->download($filename);
    }
}
