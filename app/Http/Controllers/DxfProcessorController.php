<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DxfProcessorController extends Controller
{
    public function index()
    {
        return view('process-dxf');
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