<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PdfNumberingController extends Controller
{
    public function show()
    {
        return view('number');
    }

    public function number(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
            'page_data' => 'required|json',
        ]);

        $tempDir = storage_path('app\temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $inputFile = $tempDir . '\\' . uniqid() . '.pdf';
        $request->file('pdf')->move($tempDir, basename($inputFile));
        \Log::info('Input file saved: ' . $inputFile);

        $outputFile = $tempDir . '\numbered_' . time() . '.pdf';
        $pageDataJson = $request->input('page_data');
        \Log::info('Received page_data: ' . $pageDataJson);

        $jsonFile = $tempDir . '\\temp_json_' . uniqid() . '.json';
        file_put_contents($jsonFile, $pageDataJson);
        \Log::info('JSON file saved: ' . $jsonFile);

        $pythonScript = base_path('scripts\pdf_numbering.py');
        $pythonCmd = 'python';
        $command = "$pythonCmd \"$pythonScript\" \"$inputFile\" \"$outputFile\" \"$jsonFile\"";
        $output = shell_exec($command . " 2>&1");
        \Log::info('Python command: ' . $command);
        \Log::error('Python script output: ' . $output);

        File::delete($jsonFile);

        if (!file_exists($outputFile)) {
            \Log::error('Output file not created: ' . $outputFile);
            File::delete($inputFile);
            return response()->json(['error' => 'Failed to number PDF: ' . $output], 500);
        }

        $filesize = filesize($outputFile);
        \Log::info('Output file exists, size before download: ' . $filesize . ' bytes, path: ' . $outputFile);

        File::delete($inputFile);

        // Kirim file dengan header manual
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        $bytesSent = readfile($outputFile);
        \Log::info('Bytes sent to browser: ' . $bytesSent . ' for file: ' . $outputFile);
        File::delete($outputFile);
        exit;
    }
}