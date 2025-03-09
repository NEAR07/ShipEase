<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\File;

class PdfController extends Controller
{
    public function merge(Request $request)
    {
    $request->validate([
        'pdfs.*' => 'required|file|mimes:pdf|max:10240', // 10MB max per file
    ]);

    // Pastikan direktori temp ada
    $tempDir = storage_path('app\temp'); // Gunakan backslash untuk Windows
    if (!File::exists($tempDir)) {
        File::makeDirectory($tempDir, 0755, true);
    }

    $outputFile = $tempDir . '\merged_' . time() . '.pdf';
    
    // Simpan file upload sementara
    $tempFiles = [];
    foreach ($request->file('pdfs') as $index => $file) {
        $tempPath = $tempDir . '\\' . uniqid() . '.pdf'; // Gunakan backslash
        $file->move($tempDir, basename($tempPath));
        $tempFiles[] = $tempPath;
    }

    // Jalankan Python script
    $pythonScript = base_path('scripts\pdf_merger.py'); // Sesuaikan path dengan backslash
    // Gunakan path absolut ke python.exe jika perlu, misalnya: "C:\\Python310\\python.exe"
    $pythonCmd = 'python'; // Ganti dengan '"C:\\Python310\\python.exe"' jika perlu
    $command = "\"{$pythonCmd}\" \"{$pythonScript}\" \"{$tempDir}\" \"{$outputFile}\"";
    $output = shell_exec($command . " 2>&1");
    \Log::info('Python command: ' . $command); // Log perintah untuk debugging
    \Log::error('Python script output: ' . $output);

    // Periksa apakah file output berhasil dibuat
    if (!file_exists($outputFile)) {
        foreach ($tempFiles as $file) {
            File::delete($file);
        }
        return response()->json(['error' => 'Failed to merge PDFs: ' . $output], 500);
    }

    // Bersihkan file sementara
    foreach ($tempFiles as $file) {
        File::delete($file);
    }

    return response()->download($outputFile)->deleteFileAfterSend(true);
    }
}