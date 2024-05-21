<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class pdfWordController extends Controller
{
    public function showUploadForm()
    {
        return view('pdfWord');
    }

    public function convertPdfToDocx(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240', // Max file size: 10MB
        ]);

        $pdfFile = $request->file('file');
        $outputFile = str_replace('.pdf', '.docx', $pdfFile->getClientOriginalName());

        $process = new Process(['python', 'scripts/convert.py', $pdfFile->getRealPath()]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return response()->json(['message' => 'File converted successfully']);
    }
}
