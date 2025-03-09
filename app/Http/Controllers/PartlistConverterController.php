<?php
// app/Http/Controllers/PartlistConverterController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PartlistConverterController extends Controller
{
    public function convert(Request $request)
    {
        // Validasi file yang diupload
        $request->validate([
            'list' => 'required|file|mimes:list',
            'lst' => 'required|file|mimes:lst',
            'csv' => 'required|file|mimes:csv',
            'output' => 'required|string'
        ]);

        // Simpan file yang diupload ke direktori sementara
        $listFile = $request->file('list')->store('temp');
        $lstFile = $request->file('lst')->store('temp');
        $csvFile = $request->file('csv')->store('temp');
        $outputFile = $request->input('output');

        // Path absolut ke file yang disimpan
        $listPath = storage_path('app/' . $listFile);
        $lstPath = storage_path('app/' . $lstFile);
        $csvPath = storage_path('app/' . $csvFile);
        $outputPath = public_path($outputFile); // Simpan output di public agar bisa di-download

        // Path ke script Python
        $pythonScript = base_path('scripts/partlist_converter.py');

        // Jalankan script Python dengan argumen
        $process = new Process(['python3', $pythonScript, $listPath, $lstPath, $csvPath, $outputPath]);
        $process->run();

        // Periksa apakah proses gagal
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Hapus file sementara
        unlink($listPath);
        unlink($lstPath);
        unlink($csvPath);

        // Kembalikan file Excel sebagai respons
        return response()->download($outputPath)->deleteFileAfterSend(true);
    }
}