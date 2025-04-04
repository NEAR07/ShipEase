<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResumeDownloadController extends Controller
{
    public function showDownloadPage()
    {
        return view('download');
    }

    public function downloadResume()
    {
        $filePath = public_path('files/Aplikasi Resume Material List All Block.xlsb');
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan!');
        }

        return response()->download(
            $filePath, 
            'Aplikasi Resume Material List All Block.xlsb',
            ['Content-Type' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12']
        );
    }
}