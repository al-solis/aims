<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Supplies Summary Report</title>

    <style>
        body {
            /* font-family: DejaVu Sans, sans-serif;  */
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 5px;
        }

        .sub-title {
            font-size: 12px;
            color: #555;
        }

        .section {
            margin-top: 20px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }

        table th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 40px;
        }

        .page-number {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            padding: 5px 0;
            border-top: 1px solid #ddd;
        }

        .signature {
            margin-top: 50px;
            width: 45%;
            display: inline-block;
            text-align: center;
        }

        .status-returned {
            color: green;
            font-weight: bold;
        }

        .status-damaged {
            color: orange;
            font-weight: bold;
        }

        .status-lost {
            color: red;
            font-weight: bold;
        }

        .status-pending {
            color: gray;
            font-weight: bold;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
        <br>
        <br>
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">SUPPLIES SUMMARY REPORT</div>
        <div class="sub-title">Generated on: {{ now()->format('F d, Y') }}</div>
        <div class="sub-title">Category: {{ $pCategory }}</div>
        <div class="sub-title">Supplier: {{ $pSupplier }}</div>
        <div class="sub-title">Balance: {{ $pBalance }}</div>
        <div class="sub-title">Sort: {{ ucfirst($sortField) }}</div>
    </div>

    {{-- DETAILS --}}
    <div class="section">
        {{-- <div class="section-title">Report Details</div> --}}
        <table>
            <thead>
                <tr>
                    <th>Item Code</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Supplier</th>
                    <th>Category</th>
                    <th>UOM</th>
                    <th>Qty</th>
                    <th>Reorder</th>
                    <th>Price</th>
                    <th>Total Value</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                $grandTotalQty = 0; @endphp

                @foreach ($supplies as $supply)
                    @php
                        $grandTotal += $supply->unit_price * $supply->available_stock;
                        $grandTotalQty += $supply->available_stock;

                        $statuses = [
                            1 => 'Active',
                            2 => 'Inactive',
                        ];
                    @endphp
                    <tr>
                        <td>{{ $supply->code }}</td>
                        <td>{{ $supply->name }}</td>
                        <td>{{ $supply->description }}</td>
                        <td>{{ $supply->supplier->name ?? 'N/A' }}</td>
                        <td>{{ $supply->category->name ?? 'N/A' }}</td>
                        <td>{{ $supply->uom->name ?? 'N/A' }}</td>
                        <td class="text-right">{{ number_format($supply->available_stock, 0) }}</td>
                        <td class="text-right">{{ number_format($supply->reorder_quantity, 0) }}</td>
                        <td class="text-right">{{ number_format($supply->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($supply->unit_price * $supply->available_stock, 2) }}
                        </td>

                    </tr>
                @endforeach

                @if ($supplies->isEmpty())
                    <tr>
                        <td colspan="10" style="text-align: center; font-size: 11px; color: #555">No supplies found.
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="6" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($grandTotalQty, 0) }}</strong></td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p>

        {{-- <div class="page-number">
            Page {PAGE_NUM} of {PAGE_COUNT} | Generated on {{ now()->format('F d, Y') }}
        </div> --}}
</body>

</html>
