<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class AutolispDownloadController extends Controller
{
    public function show()
    {
        // Daftar file AutoLISP yang tersedia
        $files = [
            'CMB1.lsp' => 'This LISP script is designed to collect multiple DXF files in one folder and arrange them neatly in a vertical layout in ZWCAD. Its main function is to import and organize drawings automatically, thus saving time in CAD document management.',
            'LAYER49.lsp' => 'This script changes the line color from red to yellow specifically for objects on Layer 49. Useful for standardizing or highlighting certain elements in a CAD project.',
            'MLY.lsp' => 'This script modifies material lines by turning them bold and changing the color from green to yellow. Ideal for marking or clarifying material elements in engineering drawings.',
            'RT0.lsp' => 'This script rotates an object that was initially at a 180 degree rotation back to the 0 degree position. Useful for correction or adjustment of object orientation in CAD drawings.',
            'AFC.lsp' => 'This LISP script changes the font of text automatically in ZWCAD. Functions include changing the font for all text in a drawing or text on a specific layer, ensuring consistency of appearance.',
            // 'AFC.lsp' => '',
            // Tambahkan file AutoLISP lainnya sesuai kebutuhan
        ];
        
        return view('autolisp-download', compact('files'));
    }

    public function downloadAutolisp(Request $request)
    {
        $filename = $request->input('filename');
        $filePath = public_path('files/autolisp/' . $filename);

        // Periksa apakah file utama ada
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan!');
        }

        // Jika file adalah CMB1.lsp, tambahkan database-profile.csv ke dalam ZIP
        if ($filename === 'CMB1.lsp') {
            $csvFilePath = public_path('files/autolisp/database-profile.csv');

            // Periksa apakah database-profile.csv ada
            if (!file_exists($csvFilePath)) {
                return redirect()->back()->with('error', 'File database-profile.csv tidak ditemukan!');
            }

            // Buat file ZIP sementara
            $zipFileName = 'CMB1_bundle_' . time() . '.zip';
            $zipFilePath = public_path('files/temp/' . $zipFileName);

            // Pastikan folder temp ada
            if (!file_exists(public_path('files/temp'))) {
                mkdir(public_path('files/temp'), 0755, true);
            }

            // Inisialisasi ZipArchive
            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                // Tambahkan CMB1.lsp
                $zip->addFile($filePath, 'CMB1.lsp');
                // Tambahkan database-profile.csv
                $zip->addFile($csvFilePath, 'database-profile.csv');
                $zip->close();

                // Kirim file ZIP sebagai respons unduhan
                $response = response()->download($zipFilePath, 'CMB1_bundle.zip', [
                    'Content-Type' => 'application/zip'
                ]);

                // Hapus file ZIP sementara setelah dikirim
                $response->deleteFileAfterSend(true);

                return $response;
            } else {
                return redirect()->back()->with('error', 'Gagal membuat file ZIP!');
            }
        }

        // Jika bukan CMB1.lsp, langsung unduh file asli
        return response()->download(
            $filePath,
            $filename,
            ['Content-Type' => 'application/x-lisp']
        );
    }
}