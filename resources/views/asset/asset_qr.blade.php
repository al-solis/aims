<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Asset QR Codes</title>

    <style>
        @page {
            margin: 0;
        }

        html,
        body {
            margin: 2px;
            padding: 0;
            /* width: 100%;
            height: 100%; */
            font-family: DejaVu Sans, sans-serif;
        }

        .label {
            /* width: 100%;
            height: 100%;
            padding: 1mm;
            box-sizing: border-box; */
            page-break-after: always;
        }

        .label:last-child {
            page-break-after: auto;
        }

        table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: middle;
            padding: 0;
        }

        .qr-cell {
            width: 30%;
            text-align: center;
        }

        .qr-cell img {
            width: 40px;
            height: 40px;
        }

        .info-cell {
            width: 70%;
            padding-left: 2mm;
        }

        .asset-code {
            font-size: 7pt;
            font-weight: bold;
            line-height: 1.0;
            margin-bottom: 1px;
        }

        .description {
            font-size: 5pt;
            line-height: 1.0;
            margin-bottom: 1px;
        }

        .location {
            font-size: 4.5pt;
            line-height: 1.0;
            color: #333;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    @foreach ($assets as $asset)
        <div class="label">
            <table>
                <tr>

                    <td class="qr-cell">
                        <img src="data:image/svg+xml;base64,{{ $asset->qr }}">
                    </td>

                    <td class="info-cell">

                        <div class="asset-code">
                            {{ $asset->asset_code }}
                        </div>

                        <div class="description">
                            {{ \Illuminate\Support\Str::limit($asset->name, 18) }}
                        </div>

                        <div class="location">
                            {{ \Illuminate\Support\Str::limit($asset->location->name ?? 'N/A', 22) }}
                        </div>

                    </td>

                </tr>
            </table>
        </div>

        {{-- @if (!$loop->last)
            <div class="page-break"></div>
        @endif --}}
    @endforeach

</body>

</html>
