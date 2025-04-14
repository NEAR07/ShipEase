<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class PartnameConverterController extends Controller
{
    public function index()
    {
        return view('Multimedia.partnameConverter');
    }

    public function downloadApp()
    {
        $zipFileName = 'PartName Merger.zip';
        $zipPath = public_path('files/' . $zipFileName);

        if (!File::exists($zipPath)) {
            Log::error("File aplikasi tidak ditemukan di: $zipPath");
            return response()->json(['message' => 'File aplikasi tidak ditemukan'], 404);
        }

        Log::info("Ukuran file ZIP sebelum dikirim: " . File::size($zipPath) . " bytes");
        Log::info("Mengirimkan file ZIP untuk diunduh: $zipPath");

        return response()->download($zipPath, $zipFileName, [
            'Content-Type' => 'application/zip',
            'Content-Length' => File::size($zipPath),
        ]);
    }
}

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\File;

// class PartnameConverterController extends Controller
// {
//     public function process(Request $request)
//     {
//         set_time_limit(300); // 5 menit

//         $request->validate([
//             'dxf_files.*' => 'required|file|extensions:dxf'
//         ]);

//         $log = "Memulai pemrosesan DXF...\n";
//         $outputFiles = [];

//         $tempDir = storage_path('app\temp');
//         $outputDir = storage_path('app\processed_dxf');
//         if (!File::exists($tempDir)) {
//             File::makeDirectory($tempDir, 0755, true);
//             Log::info("Membuat direktori temp: $tempDir");
//         }
//         if (!File::exists($outputDir)) {
//             File::makeDirectory($outputDir, 0755, true);
//             Log::info("Membuat direktori output: $outputDir");
//         }

//         $inputFolder = $tempDir . '\\input_' . uniqid();
//         $outputFolder = $outputDir . '\\output_' . uniqid();
//         File::makeDirectory($inputFolder);
//         File::makeDirectory($outputFolder);

//         $uploadedFiles = $request->file('dxf_files');
//         foreach ($uploadedFiles as $file) {
//             $filename = $file->getClientOriginalName();
//             Log::info("File: $filename, MIME: " . $file->getClientMimeType());
//             $file->move($inputFolder, $filename);
//             $log .= "Mengunggah $filename ke folder sementara...\n";
//         }

//         Log::info('Isi folder input: ' . json_encode(scandir($inputFolder)));

//         try {
//             $pythonScript = base_path('scripts\partname_converter.py');
//             $pythonCmd = 'python'; // Ganti dengan jalur Python Anda
//             $command = "$pythonCmd \"$pythonScript\" \"$inputFolder\" \"$outputFolder\"";
//             Log::info("Perintah Python: " . $command);

//             Log::info('Memulai eksekusi Python');
//             $output = shell_exec($command . " 2>&1");
//             Log::info('Eksekusi Python selesai');
//             Log::info('Output skrip Python: ' . ($output ?? 'Tidak ada output yang ditangkap'));

//             $log .= $output ?? "Tidak ada output dari skrip Python.\n";

//             $outputFilesList = File::files($outputFolder);
//             Log::info("Isi folder output setelah pemrosesan: " . json_encode(array_map(function ($file) {
//                 return $file->getFilename();
//             }, $outputFilesList)));

//             foreach ($outputFilesList as $outputFile) {
//                 $filename = $outputFile->getFilename();
//                 $url = Storage::url('processed_dxf/' . basename($outputFolder) . '/' . $filename);
//                 $outputFiles[] = [
//                     'name' => $filename,
//                     'url' => $url,
//                     'path' => $outputFile->getPathname()
//                 ];
//                 $log .= "File yang diproses tersedia: $filename\n";
//             }

//             if (empty($outputFiles)) {
//                 $log .= "Tidak ada file yang diproses.\n";
//             }

//             session(['output_folder' => $outputFolder]);
//             Log::info("Folder output disimpan di sesi: $outputFolder");
//         } catch (\Exception $e) {
//             $log .= "Kesalahan selama pemrosesan: " . $e->getMessage() . "\n";
//             Log::error("Kesalahan selama pemrosesan: " . $e->getMessage());
//         } finally {
//             File::deleteDirectory($inputFolder);
//         }

//         return response()->json([
//             'log' => $log,
//             'output_files' => $outputFiles
//         ]);
//     }

//     public function downloadZip(Request $request)
//     {
//         $outputFolder = session('output_folder');
//         if (!$outputFolder || !File::exists($outputFolder)) {
//             Log::error("Folder output tidak ditemukan di sesi atau tidak ada: " . ($outputFolder ?? 'null'));
//             return response()->json(['message' => 'Folder output tidak ditemukan'], 404);
//         }

//         $filesInOutput = File::files($outputFolder);
//         Log::info("Isi folder output sebelum membuat ZIP: " . json_encode(array_map(function ($file) {
//             return $file->getFilename();
//         }, $filesInOutput)));

//         $zipFileName = 'processed_dxf_files.zip';
//         $zipPath = storage_path('app\temp\\' . $zipFileName);
//         File::makeDirectory(storage_path('app\temp'), 0755, true, true);

//         $zip = new \ZipArchive;
//         if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
//             $folderName = basename($outputFolder);
//             $files = File::allFiles($outputFolder);
//             foreach ($files as $file) {
//                 $relativePath = $folderName . '/' . $file->getRelativePathname();
//                 $filePath = $file->getPathname();
//                 if (File::exists($filePath)) {
//                     $zip->addFile($filePath, $relativePath);
//                     Log::info("Menambahkan file ke ZIP: $relativePath dari $filePath");
//                 } else {
//                     Log::warning("File tidak ditemukan saat menambahkan ke ZIP: $filePath");
//                 }
//             }
//             $zip->close();
//             Log::info("ZIP berhasil dibuat di: $zipPath");

//             // Verifikasi isi ZIP dan ukuran file
//             if (File::exists($zipPath)) {
//                 Log::info("Ukuran file ZIP sebelum dikirim: " . File::size($zipPath) . " bytes");
//                 $zipTest = new \ZipArchive;
//                 if ($zipTest->open($zipPath) === TRUE) {
//                     Log::info("Jumlah file di ZIP: " . $zipTest->numFiles);
//                     for ($i = 0; $i < $zipTest->numFiles; $i++) {
//                         Log::info("File di ZIP: " . $zipTest->getNameIndex($i));
//                     }
//                     $zipTest->close();
//                 } else {
//                     Log::error("Gagal membuka ZIP untuk verifikasi: $zipPath");
//                 }
//             } else {
//                 Log::error("File ZIP tidak ditemukan setelah pembuatan: $zipPath");
//                 return response()->json(['message' => 'File ZIP tidak ditemukan'], 500);
//             }
//         } else {
//             Log::error("Gagal membuka ZIP untuk penulisan: $zipPath");
//             return response()->json(['message' => 'Gagal membuat ZIP'], 500);
//         }

//         // Kirim file ZIP tanpa menghapus dulu
//         $response = response()->download($zipPath, $zipFileName, [
//             'Content-Type' => 'application/zip',
//             'Content-Length' => File::size($zipPath), // Tambahkan ukuran file untuk kejelasan
//         ])->deleteFileAfterSend(true);

//         Log::info("Mengirimkan file ZIP untuk diunduh: $zipPath");

//         // Pembersihan ditunda hingga setelah respons dikirim
//         File::deleteDirectory($outputFolder);
//         session()->forget('output_folder');
//         Log::info("Folder output dihapus dan sesi dibersihkan: $outputFolder");

//         return $response;
//     }
// }