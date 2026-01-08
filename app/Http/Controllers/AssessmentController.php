<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

    // CREATE
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'certificate_no' => ['required', 'string', 'max:100'],
            'aspek_1' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_2' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_3' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_4' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_5' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_6' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        [$total, $avg, $grade] = $this->computeTotalAvgGrade($validated);

        // kalau sudah ada assessment, update aja
        $assessment = Assessment::updateOrCreate(
            ['user_id' => $user->id],
            [
                'certificate_no' => $validated['certificate_no'],
                'aspek_1' => (int)$validated['aspek_1'],
                'aspek_2' => (int)$validated['aspek_2'],
                'aspek_3' => (int)$validated['aspek_3'],
                'aspek_4' => (int)$validated['aspek_4'],
                'aspek_5' => (int)$validated['aspek_5'],
                'aspek_6' => (int)$validated['aspek_6'],
                'total' => $total,
                'average' => $avg,
                'grade_letter' => $grade,
                'assessed_at' => now(),
            ]
        );

        // set mahasiswa selesai magang
        $user->update(['status' => 'nonaktif']);

        return back()->with('success', 'Penilaian berhasil disimpan & mahasiswa ditandai selesai magang.');
    }

    // UPDATE
    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'certificate_no' => ['required', 'string', 'max:100'],
            'aspek_1' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_2' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_3' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_4' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_5' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_6' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        [$total, $avg, $grade] = $this->computeTotalAvgGrade($validated);

        $assessment->update([
            'certificate_no' => $validated['certificate_no'],
            'aspek_1' => (int)$validated['aspek_1'],
            'aspek_2' => (int)$validated['aspek_2'],
            'aspek_3' => (int)$validated['aspek_3'],
            'aspek_4' => (int)$validated['aspek_4'],
            'aspek_5' => (int)$validated['aspek_5'],
            'aspek_6' => (int)$validated['aspek_6'],
            'total' => $total,
            'average' => $avg,
            'grade_letter' => $grade,
            'assessed_at' => now(),
        ]);

        // pastikan user nonaktif
        $assessment->user?->update(['status' => 'nonaktif']);

        return back()->with('success', 'Penilaian berhasil diperbarui.');
    }

    // DELETE
    public function destroy(Assessment $assessment)
    {
        $user = $assessment->user;
        $assessment->delete();

        // kalau penilaian dihapus, boleh aktifkan lagi (opsional)
        if ($user) {
            $user->update(['status' => 'aktif']);
        }

        return back()->with('success', 'Penilaian berhasil dihapus.');
    }

    // DOWNLOAD PDF 2 halaman
    public function certificate(User $user)
{
    $assessment = $user->assessment;

    if (!$assessment) {
        abort(404, 'Penilaian belum ada.');
    }

    $today = Carbon::now();
    $hasilLabel = $this->labelFromGrade($assessment->grade_letter);

    // F4: 210 x 330 mm  (landscape => 330 x 210)
    // DomPDF pakai point: mm * 72 / 25.4
    $w = 330 / 25.4 * 72; // 935.433...
    $h = 210 / 25.4 * 72; // 595.276...

    $pdf = Pdf::loadView('admin.assessments.certificate-pdf', [
        'student' => $user,
        'assessment' => $assessment,
        'today' => $today,
        'hasilLabel' => $hasilLabel,
    ])
        ->setPaper([0, 0, $w, $h], 'landscape')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

    $filename = 'Sertifikat_Magang_' . preg_replace('/\s+/', '_', $user->name) . '.pdf';
    return $pdf->download($filename);
}

}
