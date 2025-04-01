<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class PartlistConverterController extends Controller
{
    public function convert(Request $request)
    {
        Log::info('Starting partlist conversion process');

        try {
            // Tingkatkan batas waktu eksekusi
            set_time_limit(300); // 5 menit

            // Validasi input
            $request->validate([
                'list' => 'required|file|extensions:list',
                'lst' => 'required|file|extensions:lst',
                'csv' => 'required|file|extensions:csv',
                'output' => 'required|string|regex:/\.xlsx$/'
            ]);
            Log::info('File validation passed');

            // Simpan file sementara dengan nama asli
            $listFile = $request->file('list')->storeAs('temp', $request->file('list')->getClientOriginalName());
            $lstFile = $request->file('lst')->storeAs('temp', $request->file('lst')->getClientOriginalName());
            $csvFile = $request->file('csv')->storeAs('temp', $request->file('csv')->getClientOriginalName());
            $outputFile = $request->input('output');

            $listPath = storage_path('app/' . $listFile);
            $lstPath = storage_path('app/' . $lstFile);
            $csvPath = storage_path('app/' . $csvFile);
            $outputPath = public_path($outputFile);

            Log::info("Temporary files stored: list=$listPath, lst=$lstPath, csv=$csvPath");
            Log::info("Output path set to: $outputPath");

            // Pastikan direktori public writable
            $outputDir = dirname($outputPath);
            if (!is_writable($outputDir)) {
                Log::error("Output directory not writable: $outputDir");
                return response()->json(['message' => 'Server error: Output directory not writable'], 500);
            }

            // Jalankan script Python
            $pythonScript = base_path('scripts/partlist_converter.py');
            if (!file_exists($pythonScript)) {
                Log::error("Python script not found: $pythonScript");
                return response()->json(['message' => 'Server error: Python script not found'], 500);
            }

            $process = new Process(['python', $pythonScript, $listPath, $lstPath, $csvPath, $outputPath]);
            $process->setTimeout(300); // Timeout 5 menit
            Log::info("Running command: " . $process->getCommandLine());

            $process->run();
            if (!$process->isSuccessful()) {
                Log::error('Python process failed: ' . $process->getErrorOutput());
                throw new ProcessFailedException($process);
            }
            Log::info('Python script executed successfully: ' . $process->getOutput());

            // Periksa file output
            if (!file_exists($outputPath)) {
                Log::error("Output file not found: $outputPath");
                return response()->json(['message' => 'Output file not generated'], 500);
            }
            if (filesize($outputPath) == 0) {
                Log::error("Output file is empty: $outputPath");
                return response()->json(['message' => 'Output file is empty'], 500);
            }
            Log::info("Output file generated: $outputPath, size=" . filesize($outputPath) . " bytes");

            // Hapus file sementara
            @unlink($listPath);
            @unlink($lstPath);
            @unlink($csvPath);
            Log::info('Temporary files deleted');

            // Kembalikan file Excel
            Log::info('Returning Excel file for download');
            return response()->download($outputPath, $outputFile, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Conversion failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Conversion failed: ' . $e->getMessage()], 500);
        }
    }
}