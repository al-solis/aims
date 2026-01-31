@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Asset Labels</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            margin: 0;
        }

        .label {
            width: 70mm;
            /* adjust for your sticker sheet */
            height: 35mm;
            border: 1px solid #000;
            padding: 5px;
            margin: 2mm;
            display: inline-block;
            text-align: center;
            vertical-align: top;
        }

        .qr {
            margin-bottom: 2px;
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

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @forelse ($assets as $asset)
        <div class="label">
            <div class="qr">
                {!! QrCode::size(70)->generate(url('/assets/' . $asset->id)) !!}

                {{-- <img src="data:image/png;base64,{{ $asset->qr_base64 }}" width="70" height="70"> --}}

            </div>
            <div class="asset-code">{{ $asset->asset_code }}</div>
            <div class="description">{{ $asset->description ?? 'N/A' }}</div>
            <div class="assigned">Assigned To:
                {{ $asset->assigned_to ? $asset->assigned_user->last_name . ', ' . $asset->assigned_user->first_name . ' ' . $asset->assigned_user->middle_name : 'N/A' }}
            </div>
            <div class="location">Location: {{ $asset->location->name ?? 'N/A' }}</div>
        </div>

        @if ($loop->iteration % 10 == 0)
            <div class="page-break"></div>
        @endif
    @empty
        <p>No assets to display.</p>
    @endforelse
</body>

</html>
