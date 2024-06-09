<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

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

        if (!$process->isSuccessful()) {
            \Log::error('Process failed', ['output' => $process->getErrorOutput()]);
            throw new ProcessFailedException($process);
        }

        \Log::info('Process successful', ['output' => $process->getOutput()]);

        return response()->download($outputPath)->deleteFileAfterSend(true);
    }
}
