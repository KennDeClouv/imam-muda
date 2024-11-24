<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Imam;
use App\Models\ListFee;
use App\Models\Schedule;
use App\Models\Shalat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RekapController extends Controller
{
    public function berdasarkanImam(Request $request)
    {
        // Validasi input bulan dan tahun
        $monthYear = $request->input('month');
        if (!$monthYear || !preg_match('/^\d{4}-\d{2}$/', $monthYear)) {
            $monthYear = Carbon::now()->format('Y-m'); // Default ke bulan dan tahun sekarang jika input tidak valid
        }

        [$year, $month] = explode('-', $monthYear);

        $imamId = $request->input('imam');

        $defaultImam = Imam::all();
        // Ambil data imam dengan jadwal (schedules dan badalSchedules) menggunakan eager loading
        $imams = Imam::with([
            'Schedules' => function ($query) use ($year, $month) {
                $query->whereYear('date', $year)->whereMonth('date', $month);
            },
            'BadalSchedules' => function ($query) use ($year, $month) {
                $query->whereYear('date', $year)->whereMonth('date', $month);
            },
        ])
            ->where(function ($query) use ($year, $month) {
                $query->whereHas('schedules', function ($query) use ($year, $month) {
                    $query->whereYear('date', $year)->whereMonth('date', $month);
                })->orWhereHas('badalSchedules', function ($query) use ($year, $month) {
                    $query->whereYear('date', $year)->whereMonth('date', $month);
                });
            })
            ->when($imamId, function ($query) use ($imamId) {
                $query->where('id', $imamId);
            })
            ->get();


        // Kirim data ke view
        return view('admin.rekap.berdasarkan-imam', compact('imams', 'monthYear', 'defaultImam'));
    }

    public function exportImam(Request $request)
    {
        $monthYear = $request->input('month');
        if (!$monthYear || !preg_match('/^\d{4}-\d{2}$/', $monthYear)) {
            $monthYear = Carbon::now()->format('Y-m'); // Default ke bulan dan tahun sekarang jika input tidak valid
        }

        [$year, $month] = explode('-', $monthYear);

        // Ambil data Imam beserta jadwalnya
        $imams = Imam::with(['Schedules' => function ($query) use ($year, $month) {
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }, 'BadalSchedules' => function ($query) use ($year, $month) {
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }, 'Fee'])->get();

        // Membuat Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Imam');

        // Header untuk file Excel
        $headers = ['Nama Imam', 'Total Jadwal', 'Jadwal Selesai', 'Total Jadwal Badal', 'Jadwal Selesai Badal', 'Total Bayaran (Rp)'];
        $column = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9E1F2']
                ],
                'alignment' => ['horizontal' => 'center'],
            ]);
            $column++;
        }

        // Isi data
        $row = 2;
        $totalSemuaBayaran = 0;

        foreach ($imams as $imam) {
            $totalJadwalReguler = $imam->Schedules->count();
            $totalJadwalBadal = $imam->BadalSchedules->count();

            $totalJadwalRegulerDone = $imam->Schedules->where('status', 'done')->count();
            $totalJadwalBadalDone = $imam->BadalSchedules->where('status', 'done')->count();

            $totalJadwalDone = $totalJadwalRegulerDone + $totalJadwalBadalDone;
            $totalJadwal = $totalJadwalReguler + $totalJadwalBadal;
            $totalBayaran = $totalJadwalDone * optional($imam->Fee)->fee;

            // Tambahkan bayaran ke total
            $totalSemuaBayaran += $totalBayaran;

            // Tulis data ke Excel
            $sheet->setCellValue("A$row", $imam->fullname);
            $sheet->setCellValue("B$row", $totalJadwal);
            $sheet->setCellValue("C$row", $totalJadwalDone);
            $sheet->setCellValue("D$row", $totalJadwalBadal);
            $sheet->setCellValue("E$row", $totalJadwalBadalDone);
            $sheet->setCellValue("F$row", $totalBayaran);

            // Pewarnaan baris (zebra stripes)
            $color = $row % 2 == 0 ? 'FFFFFF' : 'F2F2F2';
            $sheet->getStyle("A$row:F$row")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
            ]);

            $row++;
        }

        // Tambahkan Total Semua Bayaran
        $sheet->setCellValue("E$row", 'Total');
        $sheet->setCellValue("F$row", $totalSemuaBayaran);
        $sheet->getStyle("E$row:F$row")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E1F2'],
            ],
        ]);

        // Atur lebar kolom otomatis
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Simpan file ke output
        $fileName = 'Rekap_Imam_' . $monthYear . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Kirim file untuk diunduh
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$fileName\"");
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
        exit;
    }

    public function berdasarkanShalat(Request $request)
    {
        $monthYear = $request->input('month');
        if (!$monthYear || !preg_match('/^\d{4}-\d{2}$/', $monthYear)) {
            $monthYear = Carbon::now()->format('Y-m'); // default ke bulan dan tahun sekarang jika input tidak valid
        }

        [$year, $month] = explode('-', $monthYear);

        $defaultShalat = Shalat::all();
        $defaultImam = Imam::all();

        // ambil schedule untuk bulan dan tahun yang dipilih
        $schedules = Schedule::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        // group schedule berdasarkan imam
        $groupedSchedules = $schedules->groupBy('imam_id')->map(function ($imamSchedules, $imamId) use ($defaultShalat) {
            $totals = [];
            $grandTotal = 0; // total semua shalat
            $totalSalary = 0; // total gaji

            foreach ($defaultShalat as $shalat) {
                $count = $imamSchedules->where('shalat_id', $shalat->id)->count();

                // cek fee spesial untuk shalat ini
                $specialFee = ListFee::where('shalat_id', $shalat->id)
                    ->value('fee_id');

                // cek grade fee untuk imam ini
                $gradeFee = ListFee::where('imam_id', $imamId)
                    ->whereNull('shalat_id') // fee default
                    ->value('fee_id');
                $specialAmount = Fee::find($specialFee)->amount ?? null;
                $defaultAmount = $gradeFee ? Fee::find($gradeFee)->amount : 0;

                // gunakan fee spesial kalau ada, jika tidak, gunakan default fee
                $salary = ($specialAmount ?? $defaultAmount) * $count;

                $totals[$shalat->id] = [
                    'count' => $count,
                    'salary' => $salary,
                ];

                $grandTotal += $count;
                $totalSalary += $salary;
            }

            $totals['total'] = [
                'count' => $grandTotal,
                'salary' => $totalSalary,
            ];

            return $totals;
        });

        return view('admin.rekap.berdasarkan-shalat', compact('defaultShalat', 'defaultImam', 'groupedSchedules'));
    }
}
