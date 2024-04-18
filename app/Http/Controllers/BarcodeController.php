<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeController extends Controller
{
    public function generateBarcode(Request $request)
    {
        $barcodeText = $request->input('barcode_text');

        // Validate the barcode text
        $request->validate([
            'barcode_text' => 'required|string'
        ]);

        $generator = new BarcodeGeneratorPNG();
        $barcodePNG = $generator->getBarcode($barcodeText, $generator::TYPE_CODE_128);

        // Set the response headers
        $headers = [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="barcode.png"'
        ];

        // Return response with PNG content
        return Response::make($barcodePNG, 200, $headers);
    }
}
