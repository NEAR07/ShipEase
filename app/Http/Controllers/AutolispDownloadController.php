<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AutolispDownloadController extends Controller
{
    public function show()
    {
        // Daftar file AutoLISP yang tersedia
        $files = [
            'drawline.lsp' => 'Draw Line Tool',
            'areacalc.lsp' => 'Area Calculator',
            'blockcount.lsp' => 'Block Counter',
            // Tambahkan file AutoLISP lainnya sesuai kebutuhan
        ];
        
        return view('autolisp-download', compact('files'));
    }

    public function downloadAutolisp(Request $request)
    {
        $filename = $request->input('filename');
        $filePath = public_path('files/autolisp/' . $filename);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan!');
        }

        return response()->download(
            $filePath,
            $filename,
            ['Content-Type' => 'application/x-lisp']
        );
    }
}