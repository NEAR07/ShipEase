<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PartlistConverterController extends Controller
{
    public function convert(Request $request)
    {
        Log::info('Convert method called');

        // Validasi input file dengan ekstensi kustom
        try {
            $request->validate([
                'list_file' => 'required|file', // Hilangkan mimes untuk fleksibilitas
                'lst_file' => 'required|file',
                'csv_file' => 'required|file',
            ]);

            // Validasi ekstensi secara manual
            $listFile = $request->file('list_file');
            $lstFile = $request->file('lst_file');
            $csvFile = $request->file('csv_file');

            $listExt = strtolower($listFile->getClientOriginalExtension());
            $lstExt = strtolower($lstFile->getClientOriginalExtension());
            $csvExt = strtolower($csvFile->getClientOriginalExtension());

            if (!in_array($listExt, ['list', 'lst'])) {
                throw new \Exception('The list file must have a .list or .lst extension.');
            }
            if (!in_array($lstExt, ['list', 'lst'])) {
                throw new \Exception('The lst file must have a .list or .lst extension.');
            }
            if ($csvExt !== 'csv') {
                throw new \Exception('The csv file must have a .csv extension.');
            }
        } catch (\Exception $e) {
            Log::error('Validation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 400);
        }

        Log::info('Validation passed');

        // Buat direktori temp jika belum ada
        $tempDir = storage_path('app\temp');
        if (!File::exists($tempDir)) {
            try {
                File::makeDirectory($tempDir, 0755, true);
                Log::info("Created temp directory: $tempDir");
            } catch (\Exception $e) {
                Log::error('Failed to create temp directory: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to create temp directory'], 500);
            }
        }

        $inputListFile = $tempDir . '\\' . uniqid() . '.' . $listExt;
        $inputLstFile = $tempDir . '\\' . uniqid() . '.' . $lstExt;
        $inputCsvFile = $tempDir . '\\' . uniqid() . '.csv';
        $outputFile = $tempDir . '\\converted_' . time() . '.xlsx';

        try {
            $listFile->move($tempDir, basename($inputListFile));
            $lstFile->move($tempDir, basename($inputLstFile));
            $csvFile->move($tempDir, basename($inputCsvFile));
            Log::info('Input files saved: ' . $inputListFile . ', ' . $inputLstFile . ', ' . $inputCsvFile);
        } catch (\Exception $e) {
            Log::error('Failed to save input files: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save input files'], 500);
        }

        if (!file_exists($inputListFile) || !file_exists($inputLstFile) || !file_exists($inputCsvFile)) {
            Log::error('One or more input files not found after saving');
            return response()->json(['error' => 'Failed to save input files'], 500);
        }

        $pythonScript = base_path('scripts\partlist_converter.py');
        $pythonCmd = 'python';
        $command = "$pythonCmd \"$pythonScript\" \"$inputListFile\" \"$inputLstFile\" \"$inputCsvFile\" \"$outputFile\"";
        Log::info('Python command: ' . $command);

        Log::info('Starting Python execution');
        $output = shell_exec($command . " 2>&1");
        Log::info('Python execution completed');
        Log::info('Python script output: ' . ($output ?? 'No output captured'));

        File::delete($inputListFile);
        File::delete($inputLstFile);
        File::delete($inputCsvFile);

        if (!file_exists($outputFile)) {
            Log::error('Output file not created: ' . $outputFile);
            return response()->json(['error' => 'Failed to convert files: ' . ($output ?? 'No error message')], 500);
        }

        $filesize = filesize($outputFile);
        Log::info('Output file exists, size before download: ' . $filesize . ' bytes, path: ' . $outputFile);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        $bytesSent = readfile($outputFile);
        Log::info('Bytes sent to browser: ' . $bytesSent . ' for file: ' . $outputFile);

        File::delete($outputFile);
        exit;
    }
}