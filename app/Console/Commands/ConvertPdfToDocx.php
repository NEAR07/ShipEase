<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ConvertPdfToDocx extends Command
{
    protected $signature = 'convert:pdf-to-docx {inputPdf}';

    protected $description = 'Convert PDF to DOCX';

    public function handle()
    {
        $inputPdf = $this->argument('inputPdf');
        $outputDocx = str_replace('.pdf', '.docx', $inputPdf);

        $process = new Process(['python', 'scripts/convert.py', $inputPdf]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info('Conversion completed.');

        // Move the output DOCX file to the desired location (e.g., public directory)
        // Example: rename($outputDocx, public_path('converted_files/' . basename($outputDocx)));
    }
}
