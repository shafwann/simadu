<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\User;
use App\Models\AboutSetting;
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
            'certificate_date' => ['required', 'date'],

            'aspek_1' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_2' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_3' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_4' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_5' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_6' => ['required', 'integer', 'min:0', 'max:100'],

            // ✅ TAMBAHAN: apresiasi khusus (opsional)
            'apresiasi_khusus' => ['nullable', 'string', 'max:500'],
        ]);

        [$total, $avg, $grade] = $this->computeTotalAvgGrade($validated);

        $assessment = Assessment::updateOrCreate(
            ['user_id' => $user->id],
            [
                'certificate_no' => $validated['certificate_no'],
                'certificate_date' => $validated['certificate_date'],

                'aspek_1' => (int)$validated['aspek_1'],
                'aspek_2' => (int)$validated['aspek_2'],
                'aspek_3' => (int)$validated['aspek_3'],
                'aspek_4' => (int)$validated['aspek_4'],
                'aspek_5' => (int)$validated['aspek_5'],
                'aspek_6' => (int)$validated['aspek_6'],

                'total' => $total,
                'average' => $avg,
                'grade_letter' => $grade,

                // ✅ SIMPAN apresiasi khusus
                'apresiasi_khusus' => $validated['apresiasi_khusus'] ?? null,

                'assessed_at' => now(),
            ]
        );

        // logic lama: user jadi nonaktif setelah dinilai
        $user->update(['status' => 'nonaktif']);

        return back()->with('success', 'Penilaian berhasil disimpan & mahasiswa ditandai selesai magang.');
    }

    // UPDATE
    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'certificate_no' => ['required', 'string', 'max:100'],
            'certificate_date' => ['required', 'date'],

            'aspek_1' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_2' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_3' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_4' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_5' => ['required', 'integer', 'min:0', 'max:100'],
            'aspek_6' => ['required', 'integer', 'min:0', 'max:100'],

            // ✅ TAMBAHAN: apresiasi khusus (opsional)
            'apresiasi_khusus' => ['nullable', 'string', 'max:500'],
        ]);

        [$total, $avg, $grade] = $this->computeTotalAvgGrade($validated);

        $assessment->update([
            'certificate_no' => $validated['certificate_no'],
            'certificate_date' => $validated['certificate_date'],

            'aspek_1' => (int)$validated['aspek_1'],
            'aspek_2' => (int)$validated['aspek_2'],
            'aspek_3' => (int)$validated['aspek_3'],
            'aspek_4' => (int)$validated['aspek_4'],
            'aspek_5' => (int)$validated['aspek_5'],
            'aspek_6' => (int)$validated['aspek_6'],

            'total' => $total,
            'average' => $avg,
            'grade_letter' => $grade,

            // ✅ UPDATE apresiasi khusus
            'apresiasi_khusus' => $validated['apresiasi_khusus'] ?? null,

            'assessed_at' => now(),
        ]);

        // logic lama: user jadi nonaktif setelah dinilai
        $assessment->user?->update(['status' => 'nonaktif']);

        return back()->with('success', 'Penilaian berhasil diperbarui.');
    }

    // DELETE
    public function destroy(Assessment $assessment)
    {
        $user = $assessment->user;
        $assessment->delete();

        if ($user) {
            $user->update(['status' => 'aktif']);
        }

        return back()->with('success', 'Penilaian berhasil dihapus.');
    }

    // DOWNLOAD PDF 2 halaman (SUDAH DINAMIS DARI ABOUT)
    public function certificate(User $user)
    {
        $assessment = $user->assessment;

        if (!$assessment) {
            abort(404, 'Penilaian belum ada.');
        }

        // tetap ada (logic lama)
        $today = Carbon::now();

        // ✅ label hasil dari grade_letter (logic lama)
        $hasilLabel = $this->labelFromGrade($assessment->grade_letter);

        // ✅ ambil data about setting per kategori_instansi (logic lama)
        $about = AboutSetting::where('kategori_instansi', $user->kategori_instansi)->first();

        // F4: 210 x 330 mm (landscape => 330 x 210)
        $w = 330 / 25.4 * 72;
        $h = 210 / 25.4 * 72;

        // locale (logic lama kamu)
        app()->setLocale('id');
        \Carbon\Carbon::setLocale('id');
        \Illuminate\Support\Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'id', 'Indonesian');

        $pdf = Pdf::loadView('admin.assessments.certificate-pdf', [
            'student' => $user,
            'assessment' => $assessment,

            // tetap dikirim (logic lama)
            'today' => $today,

            // ✅ lebih relevan untuk sertifikat: pakai tanggal yang diinput dari form
            // (kalau view kamu belum pakai ini, aman—tinggal pakai saat dibutuhkan)
            'certificateDate' => $assessment->certificate_date
                ? Carbon::parse($assessment->certificate_date)
                : $today,

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
