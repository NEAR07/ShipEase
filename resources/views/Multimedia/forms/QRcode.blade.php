@if (!isset($qrCode))
    <form id="qrcode-form" action="{{ route('qrcode.generate') }}" method="post">
        @csrf

        <div class="form-group">
            <label for="url" class="form-label" style="font-weight: 800;">Enter your QR Code destination</label>
            <div class="input-group">
                <input type="text" class="form-control" name="url" id="url">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary generate-button" id="generateQRButton"
                        type="submit">Generate as SVG</button>
                </div>
            </div>
        </div>

    </form>
@endif

@if (isset($qrCode))
    <div id="qrcode-container">
        @if (isset($filename))
            <p style="padding-top: 15px;"><button class="btn btn-primary download-link"
                    style="background-color: #5f2dee" data-filename="{{ $filename }}">Click to Download</button></p>
        @endif
    </div>
@endif
