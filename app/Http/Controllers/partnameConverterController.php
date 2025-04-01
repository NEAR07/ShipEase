<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PartnameConverterController extends Controller
{
    public function process(Request $request)
    {
        set_time_limit(300); // 5 menit

        $request->validate([
            'dxf_files.*' => 'required|file|mimes:dxf'
        ]);

        $log = "Starting DXF processing...\n";
        $outputFiles = [];

        // Buat folder sementara untuk input dan output
        $inputFolder = 'temp/input_' . uniqid();
        $outputFolder = 'processed_dxf/output_' . uniqid();
        Storage::makeDirectory($inputFolder);
        Storage::makeDirectory($outputFolder);

        // Simpan file yang diunggah ke folder input
        $uploadedFiles = $request->file('dxf_files');
        foreach ($uploadedFiles as $file) {
            $filename = $file->getClientOriginalName();
            $file->storeAs($inputFolder, $filename);
            $log .= "Uploaded $filename to temporary folder...\n";
        }

        try {
            $pythonScript = base_path('scripts/partname_converter.py');
            $fullInputPath = storage_path('app/' . $inputFolder);
            $fullOutputPath = storage_path('app/' . $outputFolder);

            if (!file_exists($pythonScript)) {
                throw new \Exception("Python script not found at $pythonScript");
            }

            $process = new Process(['python', $pythonScript, $fullInputPath, $fullOutputPath]);
            $process->setTimeout(300); // 5 menit
            Log::info("Running command: " . $process->getCommandLine());

            $process->run();

            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();
                Log::error("Python process failed: " . $errorOutput);
                throw new ProcessFailedException($process);
            }

            $log .= $process->getOutput();

            // Ambil daftar file hasil dari folder output
            $outputFilesList = Storage::files($outputFolder);
            foreach ($outputFilesList as $outputFilePath) {
                $filename = basename($outputFilePath);
                $url = Storage::url($outputFilePath);
                $outputFiles[] = [
                    'name' => $filename,
                    'url' => $url,
                    'path' => $outputFilePath // Simpan path untuk ZIP
                ];
                $log .= "Processed file available: $filename\n";
            }

            if (empty($outputFiles)) {
                $log .= "No files were processed.\n";
            }

            // Simpan path folder output di sesi untuk digunakan saat membuat ZIP
            session(['output_folder' => $outputFolder]);
        } catch (\Exception $e) {
            $log .= "Error during processing: " . $e->getMessage() . "\n";
            Log::error("Error during processing: " . $e->getMessage());
        } finally {
            Storage::deleteDirectory($inputFolder);
        }

        return response()->json([
            'log' => $log,
            'output_files' => $outputFiles
        ]);
    }

    public function downloadZip(Request $request)
    {
        $outputFolder = session('output_folder');
        if (!$outputFolder || !Storage::exists($outputFolder)) {
            return response()->json(['message' => 'Output folder not found'], 404);
        }

        $zipFileName = 'processed_dxf_files.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        Storage::makeDirectory('temp');

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = Storage::files($outputFolder);
            foreach ($files as $file) {
                $filePath = storage_path('app/' . $file);
                $relativeName = basename($file);
                $zip->addFile($filePath, $relativeName);
            }
            $zip->close();
        } else {
            return response()->json(['message' => 'Failed to create ZIP'], 500);
        }

        $response = response()->download($zipPath, $zipFileName, [
            'Content-Type' => 'application/zip'
        ])->deleteFileAfterSend(true);

        // Hapus folder output setelah ZIP diunduh
        Storage::deleteDirectory($outputFolder);
        session()->forget('output_folder');

        return $response;
    }
}