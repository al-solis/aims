<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
    use App\Models\mdr_exc_loc;
@endphp

<head>
    <meta charset="utf-8">
    <title>Monthly Disposition Report</title>

    <style>
        @page {
            margin-top: 0.1in;
            margin-right: 0.5in;
            margin-bottom: 0.2in;
            margin-left: 0.5in;
        }

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
            text-align: left;
            font-size: 8px;
            color: #666;
            padding: 5px 0;
            /* border-top: 1px solid #ddd; */
        }

        .signature {
            margin-top: 50px;
            width: 45%;
            display: inline-block;
            text-align: center;
            font-size: 10px;
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

        .no-space p {
            margin: 0;
            padding: 0;
            text-align: left;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    @php
        $sortLabels = [
            'last_name' => 'Last Name',
            'hire_date' => 'Date Hired',
        ];
    @endphp

    <div class="header">
        <table width="100%" style="border:0;">
            <tr>
                <td width="7%" style="border:0; padding-left:80px;">
                    <img src="{{ public_path('images/sosia.png') }}" style="width:80px;">
                </td>
                <td width="93%" style="border:0; text-align:center;">
                    <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
                    <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
                    <div class="sub-title">{{ env('APP_COMPANY_EMAIL') }}</div>
                    <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
                </td>
                <td width="7%" style="border:0; padding-right:80px; text-align:right;">
                    <img src="{{ public_path('images/logo.PNG') }}" style="width:80px;">
                </td>
            </tr>
        </table>
        <br>
        {{-- <div class="sub-title" style="font-weight: bolder; font-size: 15px">EMPLOYEE LISTING REPORT</div>
        <div class="sub-title">Generated on: {{ now()->format('F d, Y') }}</div>
        <div class="sub-title">Location: {{ $pLocationName }}</div>
        <div class="sub-title">Date Range:
            {{ $pDateRange == 'custom' ? $pFromDate . ' to ' . $pToDate : $pDateRange }}</div>
        <div class="sub-title">Status: {{ $statusLabel }}</div>
        <div class="sub-title">Sort By: {{ $sortLabels[$sortField] ?? $sortField }} ({{ $sortDirection }})</div> --}}
    </div>

    {{-- HEADER --}}
    <div class="no-space">
        <p><strong>TO : C, SOSIA</strong></p>
        <p><strong>SUBJECT : MONTHLY DISPOSITION REPORT</strong></p>
        <p><strong>DATE : {{ now()->format('F d, Y') }}</strong></p>
        <br>
        <p style="font-size: 10px;">Submitted herewith is the Disposition of Clients, Guards and Firearms for the month
            of
            {{ $dateRangeLabels == 'custom' ? Carbon::parse($pFromDate)->format('F Y') : $dateRangeLabels }}.</p>
    </div>

    {{-- <div class="section">
        <p style="font-bold">1. RECAPITULATION</p>
        <table>
            <tr>
                <td width="25%">NO. OF CLIENTS</td>
                <td width="75%"></td>
            </tr>
            <tr>
                <td width="25%">NO. OF SECURITY GUARDS</td>
                <td width="5%"></td>
                <td width="20%">MALE</td>
                <td width="10%">50</td>
                <td width="20%">LADY GUARD</td>
                <td width="10%">100</td>
                <td width="10%"></td>
            </tr>
            <tr>
                <td width="25%">NO. OF SECURITY OFFICER</td>
                <td width="5%"></td>
                <td width="20%"></td>
                <td width="10%">43</td>
                <td width="20%"></td>
                <td width="10%">1</td>
                <td width="10%"></td>
            </tr>
            <tr>
                <td width="25%">NO. OF PRIVATE DETECTIVE</td>
                <td width="5%"></td>
                <td width="20%"></td>
                <td width="10%">43</td>
                <td width="20%"></td>
                <td width="10%">1</td>
                <td width="10%"></td>
            </tr>
            <tr>
                <td width="25%">NO. OF SECURITY CONSULTANT</td>
                <td width="75%">1</td>
            </tr>
            <tr>
                <td width="25%">NO. OF SPECIAL PROTECTION AGENT</td>
                <td width="75%"></td>
            </tr>
            <tr>
                <td width="25%">NO. OF TRAINING DIRECTOR</td>
                <td width="5%">1</td>
                <td width="50%">NO. OF TRAINING OFFICER</td>
                <td width="10%">1</td>
                <td width="10%"></td>
            </tr>
            <tr>
                <td width="25%"><strong>TOTAL</strong></td>
                <td width="75%">630</td>
            </tr>
        </table>
    </div> --}}

    {{-- <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        td {
            border: 1px solid #000;
            padding: 4px;
        }

        .no-border {
            border: none;
        } --}}
    </style>

    {{-- RECAPITULATION TABLE --}}
    <table>
        <thead>
            <tr>
                <th colspan="7">1. RECAPITULATION</th>
            </tr>
        </thead>
        <!-- ROW 1 -->
        <tr>
            <td colspan="2">NO. OF CLIENTS</td>
            <td colspan="5">{{ $noOfClients }}</td>
        </tr>

        <!-- ROW 2 -->
        <tr>
            <td colspan="2">NO. OF SECURITY GUARDS</td>
            <td></td>
            <td>MALE</td>
            <td>{{ $noOfGuards->where('gender', '1')->count() }}</td>
            <td>LADY GUARD</td>
            <td>{{ $noOfGuards->where('gender', '2')->count() }}</td>
        </tr>

        <!-- ROW 3 -->
        <tr>
            <td colspan="2">NO. OF SECURITY OFFICER</td>
            <td></td>
            <td></td>
            <td>{{ $noOfSecurityOfficers->where('gender', '1')->count() }}</td>
            <td></td>
            <td>{{ $noOfSecurityOfficers->where('gender', '2')->count() }}</td>
        </tr>

        <!-- ROW 4 -->
        <tr>
            <td colspan="2">NO. OF PRIVATE DETECTIVE</td>
            <td></td>
            <td></td>
            <td>{{ $noOfPrivateDetectives->where('gender', '1')->count() }}</td>
            <td></td>
            <td>{{ $noOfPrivateDetectives->where('gender', '2')->count() }}</td>
        </tr>

        <!-- ROW 5 -->
        <tr>
            <td colspan="2">NO. OF SECURITY CONSULTANT</td>
            <td colspan="5">{{ $noOfSecurityConsultants }}</td>
        </tr>

        <!-- ROW 6 -->
        <tr>
            <td colspan="2">NO. OF SPECIAL PROTECTION AGENT</td>
            <td colspan="5">{{ $noOfSPAs }}</td>
        </tr>

        <!-- ROW 7 -->
        <tr>
            <td colspan="2">NO. OF TRAINING DIRECTOR</td>
            <td>{{ $noOfTrainingDirectors }}</td>
            <td colspan="2">NO. OF TRAINING OFFICER</td>
            <td colspan="2">{{ $noOfTrainingOfficers }}</td>
        </tr>

        <!-- TOTAL -->
        <tr>
            <td colspan="2"><strong>TOTAL</strong></td>
            <td colspan="5">{{ $totalSecEmployees }}</td>
        </tr>
    </table>

    {{-- NO OF FIREARMS --}}
    <br>
    <table>
        <thead>
            <tr>
                <th colspan="15">2. NUMBER OF FIREARMS</th>
            </tr>
            <tr>
                <th></th>
                <th colspan="7" style="text-align: center">LIGHT ARMS</th>
                <th colspan="7" style="text-align: center">LOW ARMS</th>
            </tr>
            <tr>
                <th></th>
                <th style="text-align: center">0.45</th>
                <th style="text-align: center">M16</th>
                <th style="text-align: center">0.357</th>
                <th style="text-align: center">AK47</th>
                <th style="text-align: center">0.44</th>
                <th style="text-align: center">ECT.</th>
                <th style="text-align: center">TOTAL</th>
                <th style="text-align: center">.9MM</th>
                <th style="text-align: center">.38</th>
                <th style="text-align: center">.380</th>
                <th style="text-align: center">.32</th>
                <th style="text-align: center">12 GA</th>
                <th style="text-align: center">.22</th>
                <th style="text-align: center">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ISSUED TO SG/DEPLOYED</td>

                {{-- LONG FIREARMS --}}
                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), '45'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), 'm16'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), '357'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), 'ak47'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), '44'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), 'ect'))->count() }}
                </td>

                {{-- TOTAL LONG --}}
                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(function ($item) {
                            $caliber = strtolower($item->caliber);
                    
                            return in_array($item->status, [3]) &&
                                (str_contains($caliber, '45') ||
                                    str_contains($caliber, 'm16') ||
                                    str_contains($caliber, '357') ||
                                    str_contains($caliber, 'ak47') ||
                                    str_contains($caliber, '44') ||
                                    str_contains($caliber, 'ect'));
                        })->count() }}
                </td>

                {{-- SHORT FIREARMS --}}
                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), '9mm'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), '38'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), '380'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), '32'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), '12ga'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(fn($item) => in_array($item->status, [3]) && str_contains(strtolower($item->caliber), '22'))->count() }}
                </td>

                {{-- TOTAL SHORT --}}
                <td>
                    {{ $noOfFirearms->whereNotIn('location_id', mdr_exc_loc::pluck('location_id'))->filter(function ($item) {
                            $caliber = strtolower($item->caliber);
                    
                            return in_array($item->status, [3]) &&
                                (str_contains($caliber, '9mm') ||
                                    str_contains($caliber, '38') ||
                                    str_contains($caliber, '380') ||
                                    str_contains($caliber, '32') ||
                                    str_contains($caliber, '12ga') ||
                                    str_contains($caliber, '22'));
                        })->count() }}
                </td>
            </tr>
            <tr>
                {{-- <td>NO. FA'S IN VAULT FOR SAFEKEEPING</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td> --}}
                <td>NO. FA'S IN VAULT FOR SAFEKEEPING</td>
                {{-- LONG FIREARMS --}}
                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), '45'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), 'm16'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), '357'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), 'ak47'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), '44'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), 'ect'))->count() }}
                </td>

                {{-- TOTAL LONG --}}
                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(function ($item) {
                            $caliber = strtolower($item->caliber);
                    
                            return in_array($item->status, [1, 2, 3]) &&
                                (str_contains($caliber, '45') ||
                                    str_contains($caliber, 'm16') ||
                                    str_contains($caliber, '357') ||
                                    str_contains($caliber, 'ak47') ||
                                    str_contains($caliber, '44') ||
                                    str_contains($caliber, 'ect'));
                        })->count() }}
                </td>

                {{-- SHORT FIREARMS --}}
                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), '9mm'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), '38'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), '380'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), '32'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), '12ga'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(fn($item) => in_array($item->status, [1, 2, 3]) && str_contains(strtolower($item->caliber), '22'))->count() }}
                </td>

                {{-- TOTAL SHORT --}}
                <td>
                    {{ $noOfFirearms->where('location_id', 20198)->filter(function ($item) {
                            $caliber = strtolower($item->caliber);
                    
                            return in_array($item->status, [1, 2, 3]) &&
                                (str_contains($caliber, '9mm') ||
                                    str_contains($caliber, '38') ||
                                    str_contains($caliber, '380') ||
                                    str_contains($caliber, '32') ||
                                    str_contains($caliber, '12ga') ||
                                    str_contains($caliber, '22'));
                        })->count() }}
                </td>
            </tr>
            <tr>
                <td>TURN OVER TO FED FOR STORAGE</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>GRAND TOTAL</strong></td>
                {{-- LONG FIREARMS --}}
                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), '45'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), 'm16'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), '357'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), 'ak47'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), '44'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), 'ect'))->count() }}
                </td>

                {{-- TOTAL LONG --}}
                <td>
                    {{ $noOfFirearms->filter(function ($item) {
                            $caliber = strtolower($item->caliber);
                    
                            return in_array($item->status, [1, 2, 3, 8]) &&
                                (str_contains($caliber, '45') ||
                                    str_contains($caliber, 'm16') ||
                                    str_contains($caliber, '357') ||
                                    str_contains($caliber, 'ak47') ||
                                    str_contains($caliber, '44') ||
                                    str_contains($caliber, 'ect'));
                        })->count() }}
                </td>

                {{-- SHORT FIREARMS --}}
                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), '9mm'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), '38'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), '380'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), '32'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), '12ga'))->count() }}
                </td>

                <td>
                    {{ $noOfFirearms->filter(fn($item) => in_array($item->status, [1, 2, 3, 8]) && str_contains(strtolower($item->caliber), '22'))->count() }}
                </td>

                {{-- TOTAL SHORT --}}
                <td>
                    {{ $noOfFirearms->filter(function ($item) {
                            $caliber = strtolower($item->caliber);
                    
                            return in_array($item->status, [1, 2, 3, 8]) &&
                                (str_contains($caliber, '9mm') ||
                                    str_contains($caliber, '38') ||
                                    str_contains($caliber, '380') ||
                                    str_contains($caliber, '32') ||
                                    str_contains($caliber, '12ga') ||
                                    str_contains($caliber, '22'));
                        })->count() }}
                </td>
        </tbody>
    </table>

    {{-- DETAILS --}}
    <br>
    <br>
    <div class="section">
        {{-- <div class="section-title">Report Details</div> --}}
        <table>
            <thead>
                <!-- TOP HEADER -->
                <tr>
                    <th rowspan="2">CLIENTS ADDRESSES AND CONTACT NOS.</th>
                    <th rowspan="2">SEQ</th>
                    <th rowspan="2">NAME OF GUARDS <br>FAMILY NAME, <br> FIRST NAME, <br>MIDDLE NAME</th>
                    <th rowspan="2">GENDER</th>
                    <th rowspan="2">EDUCATIONAL ATTAINMENT</th>

                    <th colspan="2" style="text-align: center">LICENSE</th>

                    <th rowspan="2">SSS NO</th>

                    <th colspan="3" style="text-align: center">FIREARMS ISSUED <br>(BASED ON DDO)</th>

                    <th colspan="2" style="text-align: center">INSURANCE POLICY</th>

                    <th rowspan="2" style="text-align: center">SSS MONTHLY CONTRIBUTION</th>
                </tr>

                <!-- SUB HEADER -->
                <tr>
                    <!-- LICENSE -->
                    <th>LICENSE NO.</th>
                    <th>EXPIRY DATE</th>

                    <!-- FIREARMS -->
                    <th style="text-align: center">KIND</th>
                    <th style="text-align: center">MAKE</th>
                    <th style="text-align: center">SN</th>

                    <!-- INSURANCE -->
                    <th>POLICY NO.</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @php $seq = 1; @endphp
                @forelse ($employees as $locId => $empGroup)

                    @php
                        // total rows for this location
                        $locationRowspan = $empGroup->flatten()->count();
                        $locationFirst = true;
                    @endphp

                    @foreach ($empGroup as $empId => $empRows)
                        @php
                            $empRowspan = $empRows->count();
                        @endphp

                        @foreach ($empRows as $index => $employee)
                            <tr>

                                {{-- LOCATION (only once) --}}
                                {{-- @if ($locationFirst)
                                    <td rowspan="{{ $locationRowspan }}">
                                        {{ $employee->location_name ?? 'N/A' }}<br>
                                        {{ $employee->location_address ?? '' }}
                                    </td>
                                    @php $locationFirst = false; @endphp
                                @endif --}}
                                {{-- CLIENT (repeat per EMPLOYEE, not per LOCATION) --}}
                                @if ($index == 0)
                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->location_name ?? 'N/A' }}<br>
                                        {{ $employee->location_address ?? '' }}
                                    </td>
                                @endif

                                {{-- EMPLOYEE DETAILS (only once per employee) --}}
                                @if ($index == 0)
                                    <td rowspan="{{ $empRowspan }}">{{ $seq++ }}</td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ Str::upper($employee->last_name) }},
                                        {{ Str::upper($employee->first_name) }}
                                        {{ Str::upper($employee->middle_name) }}
                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->gender == 1 ? 'M' : 'F' }}
                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ Str::upper($employee->highest_education) }}
                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->license_no ?? '' }}
                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->license_expiry ? Carbon::parse($employee->license_expiry)->format('m/d/Y') : '' }}
                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->sss_no ?? '' }}
                                    </td>
                                @endif

                                {{-- ASSET / FIREARM (ALWAYS REPEATED) --}}
                                <td>{{ $employee->asset_kind ?? '' }}</td>
                                <td>{{ $employee->asset_make ?? '' }}</td>
                                <td>{{ $employee->asset_serial ?? '' }}</td>

                                {{-- INSURANCE --}}
                                @if ($index == 0)
                                    <td rowspan="{{ $empRowspan }}">{{ $employee->policy_no ?? '' }}</td>
                                    <td rowspan="{{ $empRowspan }}" style="text-align:right">
                                        {{ $employee->policy_amount ?? '' }}</td>
                                @endif

                                {{-- SSS --}}
                                @if ($index == 0)
                                    <td rowspan="{{ $empRowspan }}" style="text-align:right">
                                        {{ number_format($employee->sss_rate, 2) }}
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                    @endforeach

                @empty
                    <tr>
                        <td colspan="14" style="text-align:center;">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p> --}}

        {{-- <div class="page-number">
            Page {PAGE_NUM} of {PAGE_COUNT} | Generated on {{ now()->format('F d, Y') }}
        </div> --}}
    </div>

    <div class="section">
        <p style="font-size: 10px; font-weight: bold;">5. GAINS AND LOSSES:</p>
        <p style="font-size: 10px; font-weight: bold;">A. GAINS</p>
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAME OF GUARDS (FAMILY NAME, FIRST NAME) <br> MIDDLE NAME & QUALIFIER (if any)</th>
                    <th>NEWLY ASSIGNED</th>
                    <th>DATE <br> POSTED</th>
                    <th>PREVIOUS EMPLOYER/ AGENCY</th>
                    <th>ADDRESS & CONTACT NO. OF <br> PREVIOUS EMPLOYER/ AGENCY</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($gains as $index => $gain)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ Str::upper($gain->last_name) }}, {{ Str::upper($gain->first_name) }}
                            {{ Str::upper($gain->middle_name) }}</td>
                        <td>{{ $gain->location_name ?? '' }}</td>
                        <td>{{ Carbon::parse($gain->hire_date)->format('m/d/Y') }}</td>
                        <td>{{ $gain->previous_employer ?? 'N/A' }}</td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">No new employees for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p style="font-size: 10px; font-weight: bold;">B. LOSSES</p>
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAME OF GUARDS (FAMILY NAME, FIRST NAME) <br> MIDDLE NAME & QUALIFIER (if any)</th>
                    <th>LAST POSTING <br> PLACE</th>
                    <th>DATE <br> TERMINATED</th>
                    <th>CAUSE(S) OF TERMINATION</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($losses as $index => $loss)
                    @php
                        $statuses = [
                            0 => 'Inactive',
                            1 => 'Active',
                            2 => 'On Leave',
                            3 => 'Resigned',
                            4 => 'Retired',
                            5 => 'Terminated',
                        ];
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ Str::upper($loss->last_name) }}, {{ Str::upper($loss->first_name) }}
                            {{ Str::upper($loss->middle_name) }}</td>
                        <td>{{ $loss->location_name ?? '' }}</td>
                        <td>{{ Carbon::parse($loss->termination_date)->format('m/d/Y') }}</td>
                        <td>{{ $statuses[$loss->status] ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">No resigned/ terminated employees for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <br>
    <br>
    <p style ="font-size: 10px;">
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; I HEREBY CERTIFY the correctness of
        the disposition report for the month of
        {{ $dateRangeLabels == 'custom' ? Carbon::parse($pFromDate)->format('F Y') : $dateRangeLabels }}.
    </p>

    <div class="footer">
        <div class="signature">
            PREPARED BY: <br><br>

            _________________________________________<br>
            <strong>ARTHUR G. AQUINO, LLB, CSP, CMPP</strong> <br>
            OPERATIONS MANAGER
        </div>
        <div class="signature" style="float:right;">
            APPROVED BY: <br><br>

            _________________________________________<br>
            <strong>SHIELLA MARIE P. LIPANA - BALTAZAR</strong><br>
            VICE PRESIDENT FOR ADMIN AND FINANCE
        </div>
    </div>

    <br>
    <br>
    <p style ="font-size: 10px;">
        SUBSCRIBED AND SWORN to before me this ____ day of ____________, 20___, affiant exhibiting to me his/her
        ___________ with No. __________________ issued on ______________ at __________________.
    </p>
    <br>
    <p style ="font-size: 10px;">
        (NOTARY PUBLIC) <br><br>
        Doc. No. ______; <br>
        Page No. ______; <br>
        Book No. ______; <br>
        Series of 20___.
    </p>

    <script type="text/php">
        if (isset($pdf)) {
            // Use the page_script method to run this on every page
            $pdf->page_script(function ($pageNumber, $pageCount, $pdf, $fontMetrics) {
                $font = $fontMetrics->get_font('Arial, Helvetica, sans-serif', 'normal');
                $size = 8;                
                
                $pageText = "Page " . $pageNumber . " of " . $pageCount;
                
                // Position for the page number (bottom left)
                $x = 15;
                $y = $pdf->get_height() - 20;
                
                // Add the page number text to the current page
                $pdf->text($x, $y, $pageText, $font, $size);
            });
        }
    </script>
</body>

</html>
