<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use Smalot\PdfParser\Parser;

class pdfTextController extends Controller
{
    public function index()
    {
        return view('Multimedia.pdfText');
    }

    public function extract(Request $request)
    {
        $pdfFile = $request->file('pdf');

        // Validate file
        $request->validate([
            'pdf' => 'required|mimes:pdf'
        ]);

        // Convert PDF to Word text
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfFile->path());
        $pages = $pdf->getPages();
        $phpWord = new PhpWord();

        foreach ($pages as $pageNumber => $page) {
            $text = $page->getText();

            // Add text to Word document
            $section = $phpWord->addSection();
            $section->addText($text);
        }

        // Save Word document
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save(storage_path('app/public/extracted.docx'));

        return response()->download(storage_path('app/public/extracted.docx'));
    }
}
