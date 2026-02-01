@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Asset Labels</title>
    <style>
        html,
        body {
            font-family: sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .label {
            width: 70mm;
            /* adjust for your sticker sheet */
            height: 25mm;
            /* border: 1px solid #000; */
            padding: 0;
            margin: 0;
            display: inline-block;
            text-align: center;
            vertical-align: top;
        }

        .qr {
            margin-bottom: 2px;
            /* width: 100%;
            height: 12mm;
            margin: 0 auto 1px auto; */
        }

        .asset-code {
            font-weight: bold;
            font-size: 12px;
            margin-top: 2px;
        }

        .description {
            font-size: 10px;
            margin-top: 1px;
        }

        .assigned,
        .location {
            font-size: 9px;
            color: #555;
            margin-top: 1px;
        }

        .barcode {
            display: block;
            max-width: 90%;
            max-height: 10mm;
            margin: 3px auto 0 auto;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @forelse ($assets as $asset)
        <div class="label">
            <div class="qr">
                {!! QrCode::size(60)->generate($asset->asset_code) !!}
            </div>

            <div class="asset-code">{{ $asset->asset_code }}</div>
            <div class="description">{{ $asset->name ?? 'N/A' }}</div>
            <div class="assigned">Assigned To:
                {{ $asset->assigned_to ? $asset->assigned_user->last_name . ', ' . $asset->assigned_user->first_name . ' ' . $asset->assigned_user->middle_name : 'N/A' }}
            </div>
            <div class="location">Location: {{ $asset->location->name ?? 'N/A' }}</div>
            <img class="barcode" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($asset->asset_code, 'C128', 2, 40) }}"
                alt="barcode" />
        </div>

        @if ($loop->iteration % 10 == 0)
            <div class="page-break"></div>
        @endif
    @empty
        <p>No assets to display.</p>
    @endforelse
</body>

</html>
