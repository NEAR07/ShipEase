<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PartlistConverterController extends Controller
{
    public function convert(Request $request)
    {
        // Validasi file yang diupload
        $request->validate([
            'list' => 'required|file|mimes:list',
            'lst' => 'required|file|mimes:lst',
            'csv' => 'required|file|mimes:csv',
            'output' => 'required|string|regex:/^.+\.xlsx$/'
        ]);

        try {
            // Simpan file sementara di storage/app/temp
            $listFile = $request->file('list')->store('temp');
            $lstFile = $request->file('lst')->store('temp');
            $csvFile = $request->file('csv')->store('temp');
            $outputFileName = $request->input('output');

            // Path absolut ke file yang disimpan
            $listPath = storage_path('app/' . $listFile);
            $lstPath = storage_path('app/' . $lstFile);
            $csvPath = storage_path('app/' . $csvFile);

            // Pastikan nama output unik dengan timestamp
            $outputBaseName = pathinfo($outputFileName, PATHINFO_FILENAME);
            $outputExtension = pathinfo($outputFileName, PATHINFO_EXTENSION);
            $uniqueOutputFile = $outputBaseName . '_' . time() . '.' . $outputExtension;
            $outputPath = public_path('converted_files/' . $uniqueOutputFile);

            // Buat direktori jika belum ada
            if (!file_exists(public_path('converted_files'))) {
                mkdir(public_path('converted_files'), 0755, true);
            }

            // Path ke script Python
            $pythonScript = base_path('scripts/partlist_converter.py');

            // Deteksi perintah Python yang sesuai dengan sistem operasi
            $pythonCommand = (strtoupper(PHP_OS) === 'WINNT') ? 'python' : 'python3';

            // Log argumen untuk debugging
            Log::info('Running Python script with arguments:', [
                'python' => $pythonCommand,
                'script' => $pythonScript,
                'list' => $listPath,
                'lst' => $lstPath,
                'csv' => $csvPath,
                'output' => $outputPath
            ]);

            // Jalankan script Python dengan argumen
            $process = new Process([$pythonCommand, $pythonScript, $listPath, $lstPath, $csvPath, $outputPath]);
            $process->setTimeout(300); // Timeout 5 menit
            $process->run();

            // Log output dan error dari proses
            Log::info('Python script output: ' . $process->getOutput());
            if (!$process->isSuccessful()) {
                Log::error('Python script failed: ' . $process->getErrorOutput());
                throw new ProcessFailedException($process);
            }

            // Periksa apakah file output ada
            if (!file_exists($outputPath)) {
                Log::error('Output file not found at: ' . $outputPath);
                throw new \Exception('Output file was not generated.');
            }

            // Hapus file sementara dengan aman
            Storage::delete([$listFile, $lstFile, $csvFile]);

            // Kembalikan file Excel sebagai respons
            return response()->download($outputPath, $outputFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            return response()->json(['message' => 'Conversion failed: ' . $e->getMessage()], 500);
        }
    }
}