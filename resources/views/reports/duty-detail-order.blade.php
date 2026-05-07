<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Duty Detail</title>

    <style>
        body {
            /* font-family: DejaVu Sans, sans-serif;  */
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
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

        .no-space p {
            margin: 0;
            padding: 0;
            text-align: justify;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <table width="100%" style="border:0;">
            <tr>
                <td width="7%" style="border:0;">
                    <img src="{{ public_path('images/logo.PNG') }}" style="width:80px;">
                </td>
                <td width="93%" style="border:0; text-align:center;">
                    <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
                    <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
                    <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <strong>
                DUTY DETAIL ORDER NO.
                <u>{{ $ddoFormatted }}</u>
            </strong>
        </div>

        <div style="text-align: right">
            Date:
            <strong>
                <u>{{ Carbon::now()->format('j F Y') }}</u>
            </strong>
        </div>
    </div>

    <div class ="no-space">
        <p>1. Reference</p>
        <ol type="a" style="margin: 0 0 0 25px; padding: 0;">
            <li>DOLE Department Order No. 150-16</li>
            <li>Republic Act 10591, as amended, "Comprehensive Firearms and Ammunitions
                Regulations
                Act"; and</li>
            <li>Rule 39 Section 154-156 of Republic Act No. 11917, "Strengthening the Private
                Security Industry Services
                Act".</li>
        </ol>
        <p>2. Purpose of Detail: <strong><u>Post Security Service Duties.</u></strong></p>
        <p>3. Duration/ Inclusive Dates of Detail: From
            <strong><u>{{ Carbon::parse($pFromDate)->format('F j, Y') }} </u></strong> to
            <strong><u>{{ Carbon::parse($pToDate)->format('F j, Y') }}</u></strong>
        </p>
        <p>4. The following security personnel is/are hereby assigned to render post security service duties in place/s
            indicated and are hereby issued agency/company/government owned firearms (FAs):</p>
    </div>

    {{-- DETAILS --}}
    <div>
        <table border="1" cellspacing="0" cellpadding="5"
            style="width:100%; border-collapse:collapse; text-align:center;">
            <thead>
                <tr>
                    <th rowspan="2">NAME OF GUARDS</th>
                    <th rowspan="2">DESIGNATION</th>
                    <th rowspan="2">SPECIFIC POST/ STATION</th>
                    <th rowspan="2" style="text-align: center">TIME OF SHIFT</th>
                    <th colspan="4" style="text-align: center">FIREARMS INFORMATION</th>
                <tr>
                    <th style="text-align: center">Kind</th>
                    <th style="text-align: center">Make/Cal</th>
                    <th style="text-align: center">Serial No</th>
                    <th style="text-align: center">License Validity</th>
                </tr>
                </tr>
            </thead>
            {{-- <tbody>
                @forelse ($employees as $empId => $empRows)

                    @php
                        $rowspan = $empRows->count();
                        $first = $empRows->first();
                    @endphp

                    @foreach ($empRows as $index => $row)
                        <tr>

                            @if ($index == 0)
                                <td rowspan="{{ $rowspan }}">
                                    {{ $row->last_name }}, {{ $row->first_name }} {{ $row->middle_name }}
                                </td>

                                <td rowspan="{{ $rowspan }}">
                                    {{ $row->position ?? '' }}
                                </td>

                                <td rowspan="{{ $rowspan }}">
                                    {{ $row->location_name ?? '' }}<br>
                                    {{ $row->location_address ?? '' }}
                                </td>

                                <td rowspan="{{ $rowspan }}"></td>
                            @endif

                            <td>{{ $row->model ?? '' }}</td>
                            <td>{{ $row->asset_name ?? '' }}</td>
                            <td>{{ $row->serial ?? '' }}</td>
                            <td>{{ $row->expiration_date ?? '' }}</td>

                        </tr>
                    @endforeach

                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;">No data found</td>
                    </tr>
                @endforelse
            </tbody> --}}
            <tbody>
                @forelse ($employees as $location => $empGroup)

                    @php
                        $locationRowspan = $empGroup->flatten()->count();
                        $locationFirst = true;
                    @endphp

                    @foreach ($empGroup as $empId => $empRows)
                        @php
                            $empRowspan = $empRows->count();
                        @endphp

                        @foreach ($empRows as $index => $row)
                            <tr>

                                {{-- EMPLOYEE (only once per employee) --}}
                                @if ($index == 0)
                                    <td rowspan="{{ $empRowspan }}">
                                        @if ($row->last_name != null)
                                            {{ $row->last_name }},
                                            {{ $row->first_name }}
                                            {{ $row->middle_name }}
                                        @endif

                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $row->position ?? '' }}
                                    </td>
                                @endif

                                {{-- LOCATION (only once) --}}
                                @if ($locationFirst)
                                    <td rowspan="{{ $locationRowspan }}">
                                        {{ $row->location_name }}<br>
                                        {{ $row->location_address }}
                                    </td>
                                    @php $locationFirst = false; @endphp
                                @endif

                                {{-- TIME OF SHIFT (empty for now) --}}
                                @if ($index == 0)
                                    <td rowspan="{{ $empRowspan }}"></td>
                                @endif

                                {{-- FIREARM --}}
                                <td>{{ $row->model ?? '' }}</td>
                                <td>{{ $row->asset_name ?? '' }}</td>
                                <td>{{ $row->serial ?? '' }}</td>
                                <td>{{ $row->expiration_date ?? '' }}</td>

                            </tr>
                        @endforeach
                    @endforeach

                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;">No data found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p>5. Specific Instructions:</p>
        <ol type="a">
            <li>Shall be issued by the Private Security Agency (PSA), Company Guard Force (CGF), and
                Government Security Force (GSF) Managers and/or the Security Officers to their posted
                security personnel while carrying/bearing of firearms.</li>
            <li>Shall serve as authority to bear the issued firearms while in the actual performance of
                guard duties in respective specific guard posts/establishment/compound of the
                principal/client, and prescribed uniform.</li>
            <li>Shall serves as authority to bear and transport firearms outside of the respective guard posts
                and official registered residence of the firearms for routine rotation, repair, new posting,
                recall of firearms, and escorting large amount of cash or valuables outside its specified
                post within 24 hours only. If is beyond 24 hours, a permit to transport is required to be
                issued by FEO.</li>
            <li>Shall be valid for (30) days, renewable until termination of the security contract with the
                principal/client;</li>
            <li>The issued firearms shall be licensed and a copy of this DDO shall be in the actual possession
                of the posted security personnel. Electronic copy of this DDO may be presented in lieu of the
                original during any inspections. Provided that the original copy is presented when required by
                an authorized PNP personnel; and
            </li>
            <li>Remarks:</li>
        </ol>
        <p style="margin-left: 25px;"><strong>"THIS IS NOT AN AUTHORITY TO BEAR FIREARM OUTSIDE THE PREMISES OF THE
                SPECIFIED POST/ESTABLISHMENT OF THE PRINCIPAL NOR SHALL THE FIREARM DESCRIBED HEREIN LEAVE
                THE POSTS/STATIONS OF THE PRINCIPAL."</strong></p>

        <p>6. For strict compliance.</p>
    </div>

    {{-- SIGNATURES --}}
    <div class="footer">
        <div class="signature">

            _________________________________________<br>
            <strong>{{ env('DDO_SIG1') }}</strong> <br>
            {{ env('DDO_SIG1_POSITION') }}
        </div>
        <div class="signature" style="float:right;">

            _________________________________________<br>
            <strong>{{ env('DDO_SIG2') }}</strong><br>
            {{ env('DDO_SIG2_POSITION') }}
        </div>
    </div>

</body>

</html>
