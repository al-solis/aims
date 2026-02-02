<!DOCTYPE html>
<html>
@php
    use Illuminate\Support\Str;
@endphp

<head>
    <meta charset="utf-8">
    <title>Asset Labels</title>
    <style>
        @page {
            size: 70mm 25mm;
            /* Exact label size */
            margin: 0;
            padding: 0;
        }

        body {
            width: 70mm;
            height: 25mm;
            margin: 0;
            padding: 1mm;
            font-family: 'Arial', sans-serif;
            font-size: 8px;
            position: relative;
        }

        .label-container {
            width: 100%;
            height: 100%;
            position: relative;
        }

        /* QR Code - Top left */
        .qr {
            position: absolute;
            top: 1mm;
            left: 1mm;
            width: 20mm;
            height: 20mm;
        }

        /* Asset Information - Right of QR */
        .asset-info {
            flex: 2;
            margin-left: 1mm;
            padding-right: 1mm;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* top: 1mm;
            left: 22mm; */
            /* 20mm QR + 2mm margin */
            /* width: 46mm; */
            /* Total width 70mm - 22mm - 2mm padding */
        }

        .asset-code {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 0.5mm;
            line-height: 1.1;
        }

        .description {
            font-size: 9px;
            margin-bottom: 0.5mm;
            line-height: 1.1;
            word-wrap: break-word;
        }

        .assigned,
        .location {
            font-size: 8px;
            color: #333;
            margin-bottom: 0.5mm;
            line-height: 1.1;
        }

        /* Barcode - Bottom section */
        .barcode {
            /* position: absolute; */
            bottom: 0.5mm;

            width: 80%;
            /* Almost full width */
            height: 8mm;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
    @foreach ($assets as $asset)
        <!-- Each label is a separate page -->
        <div class="label-container">
            <!-- QR Code -->
            <div class="qr">
                {!! QrCode::size(60)->generate($asset->asset_code) !!}
            </div>

            <!-- Asset Information -->
            <div class="asset-info">
                <div class="asset-code">{{ $asset->asset_code }}</div>
                <div class="description">{{ Str::limit($asset->name ?? 'N/A', 3) }}</div>
                <div class="assigned">
                    {{ $asset->assigned_to
                        ? Str::limit($asset->assigned_user->last_name . ', ' . $asset->assigned_user->first_name, 20)
                        : 'N/A' }}
                </div>
                <div class="location">
                    {{ Str::limit($asset->location->name ?? 'N/A', 30) }}
                </div>
            </div>

            <!-- Barcode -->
            <img class="barcode"
                src="data:image/png;base64,{{ DNS1D::getBarcodePNG($asset->asset_code, 'C128', 1.5, 30) }}"
                alt="barcode" />
        </div>

        <!-- Page break for next label -->
        @if (!$loop->last)
            <div style="page-break-before: always;"></div>
        @endif
    @endforeach
</body>

</html>
