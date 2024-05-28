<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class ConversionController extends Controller
{
    public function showUploadForm()
    {
        return view('upload');
    }

    public function convertPdfToDocx(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240', // Max file size: 10MB
        ]);

        $pdfFile = $request->file('file');
        $outputFile = str_replace('.pdf', '.docx', $pdfFile->getClientOriginalName());
        $outputPath = storage_path('app/public/' . $outputFile);

        $process = new Process(['python', base_path('scripts/convert.py'), $pdfFile->getRealPath(), $outputPath]);
        $process->run();

        return response()->json(['message' => 'File converted successfully', 'outputFile' => $outputFile]);
    }
}
