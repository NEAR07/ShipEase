<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class QRCodeController extends Controller
{
    public function index()
    {
        return view('Multimedia.forms.QRcode');
    }

    public function generate(Request $request)
    {
        $url = $request->input('url');
        $qrCode = QrCode::format('svg')->size(300)->generate($url);

        $filename = $this->storeQRCode($qrCode, 'svg');

        return view('Multimedia.forms.QRcode', compact('qrCode', 'url', 'filename'));
    }

    private function storeQRCode($qrCode)
    {
        $filename = 'qrcodes/' . uniqid() . '.svg';
        Storage::put($filename, $qrCode);

        return $filename;
    }


    public function downloadFile($filename)
    {
        $path = 'qrcodes/' . $filename;

        if (Storage::exists($path)) {
            $fileContent = Storage::get($path);

            return Response::make($fileContent, 200, [
                'Content-Type' => 'image/svg',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } else {
            abort(404, 'File not found');
        }
    }
}
