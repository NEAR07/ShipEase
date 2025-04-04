<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompareDownloadController extends Controller
{
    public function show()
    {
        return view('compare-download');
    }

    public function downloadCompare()
    {
        $filePath = public_path('files/CEK  MATLIST.xls');
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan!');
        }

        return response()->download(
            $filePath, 
            'CEK  MATLIST.xls',
            ['Content-Type' => 'application/vnd.ms-excel']
        );
    }
}