<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function generateQRCode(Request $request)
    {
        $url = $request->input('url');
        $svg = QrCode::format('svg')->size(300)->generate($url);

        $headers = [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="qrcode.svg"'
        ];

        return Response::make($svg, 200, $headers);
    }
}
