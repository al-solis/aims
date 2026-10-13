@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
    use App\Models\mdr_exc_loc;

    $monthLabel = $dateRangeLabels == 'custom' ? Carbon::parse($pFromDate)->format('F Y') : $dateRangeLabels;

    // firearm counting helpers (same logic as the PDF)
    $excl = mdr_exc_loc::pluck('location_id');
    $longKeys = ['45', 'm16', '357', 'ak47', '44', 'ect'];
    $shortKeys = ['9mm', '38', '380', '32', '12ga', '22'];
    $count = fn($set, $keys) => $set
        ->filter(fn($i) => collect($keys)->contains(fn($k) => str_contains(strtolower($i->caliber), $k)))
        ->count();

    $issued = $noOfFirearms->whereNotIn('location_id', $excl)->whereIn('status', [3]);
    $vault = $noOfFirearms->where('location_id', 20198)->whereIn('status', [1, 2, 3]);
    $grand = $noOfFirearms->whereIn('status', [1, 2, 3, 8]);

    $firearmRows = [
        ['ISSUED TO SG/DEPLOYED', $issued],
        ["NO. FA'S IN VAULT FOR SAFEKEEPING", $vault],
        ['TURN OVER TO FED FOR STORAGE', null],
        ['GRAND TOTAL', $grand],
    ];

    $statuses = [0 => 'Inactive', 1 => 'Active', 2 => 'On Leave', 3 => 'Resigned', 4 => 'Retired', 5 => 'Terminated'];
@endphp

<table>
    {{-- HEADER (logos are added by the export class in rows 1-4) --}}
    <tr>
        <td colspan="15" style="text-align:center;font-weight:bold;font-size:14px;">{{ env('APP_COMPANY_NAME') }}</td>
    </tr>
    <tr>
        <td colspan="15" style="text-align:center;">{{ env('APP_COMPANY_ADDRESS') }}</td>
    </tr>
    <tr>
        <td colspan="15" style="text-align:center;">{{ env('APP_COMPANY_EMAIL') }}</td>
    </tr>
    <tr>
        <td colspan="15" style="text-align:center;">{{ env('APP_COMPANY_CONTACT') }}</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>

    <tr>
        <td colspan="15" style="font-weight:bold;">TO : C, SOSIA</td>
    </tr>
    <tr>
        <td colspan="15" style="font-weight:bold;">SUBJECT : MONTHLY DISPOSITION REPORT</td>
    </tr>
    <tr>
        <td colspan="15" style="font-weight:bold;">DATE : {{ now()->format('F d, Y') }}</td>
    </tr>
    <tr>
        <td colspan="15">Submitted herewith is the Disposition of Clients, Guards and Firearms for the month of
            {{ $monthLabel }}.</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>

    {{-- 1. RECAPITULATION --}}
    <tr>
        <th colspan="15" style="font-weight:bold;border:1px solid #000;">1. RECAPITULATION</th>
    </tr>
    <tr>
        <td colspan="2" style="border:1px solid #000;">NO. OF CLIENTS</td>
        <td colspan="13" style="border:1px solid #000;">{{ $noOfClients }}</td>
    </tr>
    <tr>
        <td colspan="2" style="border:1px solid #000;">NO. OF SECURITY GUARDS</td>
        <td style="border:1px solid #000;"></td>
        <td style="border:1px solid #000;">MALE</td>
        <td style="border:1px solid #000;">{{ $noOfGuards->where('gender', '1')->count() }}</td>
        <td style="border:1px solid #000;">LADY GUARD</td>
        <td colspan="9" style="border:1px solid #000;">{{ $noOfGuards->where('gender', '2')->count() }}</td>
    </tr>
    <tr>
        <td colspan="2" style="border:1px solid #000;">NO. OF SECURITY OFFICER</td>
        <td style="border:1px solid #000;"></td>
        <td style="border:1px solid #000;">MALE</td>
        <td style="border:1px solid #000;">{{ $noOfSecurityOfficers->where('gender', '1')->count() }}</td>
        <td style="border:1px solid #000;">LADY</td>
        <td colspan="9" style="border:1px solid #000;">{{ $noOfSecurityOfficers->where('gender', '2')->count() }}
        </td>
    </tr>
    <tr>
        <td colspan="2" style="border:1px solid #000;">NO. OF PRIVATE DETECTIVE</td>
        <td style="border:1px solid #000;"></td>
        <td style="border:1px solid #000;">MALE</td>
        <td style="border:1px solid #000;">{{ $noOfPrivateDetectives->where('gender', '1')->count() }}</td>
        <td style="border:1px solid #000;">LADY</td>
        <td colspan="9" style="border:1px solid #000;">{{ $noOfPrivateDetectives->where('gender', '2')->count() }}
        </td>
    </tr>
    <tr>
        <td colspan="2" style="border:1px solid #000;">NO. OF SECURITY CONSULTANT</td>
        <td colspan="13" style="border:1px solid #000;">{{ $noOfSecurityConsultants }}</td>
    </tr>
    <tr>
        <td colspan="2" style="border:1px solid #000;">NO. OF SPECIAL PROTECTION AGENT</td>
        <td colspan="13" style="border:1px solid #000;">{{ $noOfSPAs }}</td>
    </tr>
    <tr>
        <td colspan="2" style="border:1px solid #000;">NO. OF TRAINING DIRECTOR</td>
        <td style="border:1px solid #000;">{{ $noOfTrainingDirectors }}</td>
        <td colspan="2" style="border:1px solid #000;">NO. OF TRAINING OFFICER</td>
        <td colspan="10" style="border:1px solid #000;">{{ $noOfTrainingOfficers }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight:bold;border:1px solid #000;">TOTAL</td>
        <td colspan="13" style="font-weight:bold;border:1px solid #000;">{{ $totalSecEmployees }}</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>

    {{-- 2. NUMBER OF FIREARMS --}}
    <tr>
        <th colspan="15" style="font-weight:bold;border:1px solid #000;">2. NUMBER OF FIREARMS</th>
    </tr>
    <tr>
        <th style="border:1px solid #000;"></th>
        <th colspan="7" style="text-align:center;border:1px solid #000;">LIGHT ARMS</th>
        <th colspan="7" style="text-align:center;border:1px solid #000;">LOW ARMS</th>
    </tr>
    <tr>
        <th style="border:1px solid #000;"></th>
        @foreach (['0.45', 'M16', '0.357', 'AK47', '0.44', 'ECT.', 'TOTAL', '.9MM', '.38', '.380', '.32', '12 GA', '.22', 'TOTAL'] as $h)
            <th style="text-align:center;border:1px solid #000;">{{ $h }}</th>
        @endforeach
    </tr>
    @foreach ($firearmRows as [$label, $set])
        <tr>
            <td style="{{ $label == 'GRAND TOTAL' ? 'font-weight:bold;' : '' }}border:1px solid #000;">
                {{ $label }}</td>
            @foreach ([$longKeys, $shortKeys] as $keys)
                @foreach ($keys as $k)
                    <td style="border:1px solid #000;">{{ $set ? $count($set, [$k]) : '' }}</td>
                @endforeach
                <td style="border:1px solid #000;">{{ $set ? $count($set, $keys) : '' }}</td>
            @endforeach
        </tr>
    @endforeach
    <tr>
        <td colspan="15"></td>
    </tr>

    {{-- DETAILS --}}
    <tr>
        <th rowspan="2" style="border:1px solid #000;">CLIENTS ADDRESSES AND CONTACT NOS.</th>
        <th rowspan="2" style="border:1px solid #000;">SEQ</th>
        <th rowspan="2" style="border:1px solid #000;">NAME OF GUARDS (FAMILY NAME, FIRST NAME, MIDDLE NAME)</th>
        <th rowspan="2" style="border:1px solid #000;">GENDER</th>
        <th rowspan="2" style="border:1px solid #000;">EDUCATIONAL ATTAINMENT</th>
        <th colspan="2" style="text-align:center;border:1px solid #000;">LICENSE</th>
        <th rowspan="2" style="border:1px solid #000;">SSS NO</th>
        <th colspan="3" style="text-align:center;border:1px solid #000;">FIREARMS ISSUED (BASED ON DDO)</th>
        <th colspan="2" style="text-align:center;border:1px solid #000;">INSURANCE POLICY</th>
        <th rowspan="2" style="border:1px solid #000;">SSS MONTHLY CONTRIBUTION</th>
    </tr>
    <tr>
        <th style="border:1px solid #000;">LICENSE NO.</th>
        <th style="border:1px solid #000;">EXPIRY DATE</th>
        <th style="border:1px solid #000;">KIND</th>
        <th style="border:1px solid #000;">MAKE</th>
        <th style="border:1px solid #000;">SN</th>
        <th style="border:1px solid #000;">POLICY NO.</th>
        <th style="border:1px solid #000;">AMOUNT</th>
    </tr>

    @php $seq = 1; @endphp
    @forelse ($employees as $locId => $empGroup)
        @php
            $locAssets = $locationAssets->get($locId, collect());
            $firstEmp = $empGroup->first()->first();
            $locationRowspan = $locAssets->count() + $empGroup->flatten()->count();
            $locationFirst = true;
        @endphp

        {{-- unassigned firearms first --}}
        @foreach ($locAssets as $asset)
            <tr>
                @if ($locationFirst)
                    <td rowspan="{{ $locationRowspan }}" style="vertical-align:top;border:1px solid #000;">
                        {{ $firstEmp->location_name ?? 'N/A' }} {{ $firstEmp->location_address ?? '' }}
                    </td>
                    @php $locationFirst = false; @endphp
                @endif
                <td colspan="7" style="border:1px solid #000;"></td>
                <td style="border:1px solid #000;">{{ $asset->manufacturer ?? '' }}</td>
                <td style="border:1px solid #000;">{{ $asset->model ?? '' }}</td>
                <td style="border:1px solid #000;">{{ $asset->serial ?? '' }}</td>
                <td colspan="3" style="border:1px solid #000;"></td>
            </tr>
        @endforeach

        @foreach ($empGroup as $empId => $empRows)
            @php $empRowspan = $empRows->count(); @endphp
            @foreach ($empRows as $index => $employee)
                <tr>
                    @if ($locationFirst)
                        <td rowspan="{{ $locationRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ $employee->location_name ?? 'N/A' }} {{ $employee->location_address ?? '' }}
                        </td>
                        @php $locationFirst = false; @endphp
                    @endif

                    @if ($index == 0)
                        <td rowspan="{{ $empRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ $seq++ }}</td>
                        <td rowspan="{{ $empRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ Str::upper($employee->last_name) }}, {{ Str::upper($employee->first_name) }}
                            {{ Str::upper($employee->middle_name) }}
                        </td>
                        <td rowspan="{{ $empRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ $employee->gender == 1 ? 'M' : 'F' }}</td>
                        <td rowspan="{{ $empRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ Str::upper($employee->highest_education) }}</td>
                        <td rowspan="{{ $empRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ $employee->license_no ?? '' }}</td>
                        <td rowspan="{{ $empRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ $employee->license_expiry ? Carbon::parse($employee->license_expiry)->format('m/d/Y') : '' }}
                        </td>
                        <td rowspan="{{ $empRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ $employee->sss_no ?? '' }}</td>
                    @endif

                    <td style="border:1px solid #000;">{{ $employee->asset_kind ?? '' }}</td>
                    <td style="border:1px solid #000;">{{ $employee->asset_make ?? '' }}</td>
                    <td style="border:1px solid #000;">{{ $employee->asset_serial ?? '' }}</td>

                    @if ($index == 0)
                        <td rowspan="{{ $empRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ $employee->policy_no ?? '' }}</td>
                        <td rowspan="{{ $empRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ $employee->policy_amount ?? '' }}</td>
                        <td rowspan="{{ $empRowspan }}" style="vertical-align:top;border:1px solid #000;">
                            {{ number_format($employee->sss_rate, 2) }}</td>
                    @endif
                </tr>
            @endforeach
        @endforeach
    @empty
        <tr>
            <td colspan="14" style="text-align:center;">No employees found.</td>
        </tr>
    @endforelse
    <tr>
        <td colspan="15"></td>
    </tr>

    {{-- 5. GAINS AND LOSSES --}}
    <tr>
        <td colspan="15" style="font-weight:bold;">5. GAINS AND LOSSES:</td>
    </tr>
    <tr>
        <td colspan="15" style="font-weight:bold;">A. GAINS</td>
    </tr>
    <tr>
        <th style="border:1px solid #000;">NO</th>
        <th colspan="2" style="border:1px solid #000;">NAME OF GUARDS (FAMILY NAME, FIRST NAME) MIDDLE NAME &amp;
            QUALIFIER (if any)</th>
        <th colspan="2" style="border:1px solid #000;">NEWLY ASSIGNED</th>
        <th style="border:1px solid #000;">DATE POSTED</th>
        <th colspan="3" style="border:1px solid #000;">PREVIOUS EMPLOYER/ AGENCY</th>
        <th colspan="5" style="border:1px solid #000;">ADDRESS &amp; CONTACT NO. OF PREVIOUS EMPLOYER/ AGENCY</th>
    </tr>
    @forelse ($gains as $index => $gain)
        <tr>
            <td style="border:1px solid #000;">{{ $index + 1 }}</td>
            <td colspan="2" style="border:1px solid #000;">{{ Str::upper($gain->last_name) }},
                {{ Str::upper($gain->first_name) }} {{ Str::upper($gain->middle_name) }}</td>
            <td colspan="2" style="border:1px solid #000;">{{ $gain->location_name ?? '' }}</td>
            <td style="border:1px solid #000;">{{ Carbon::parse($gain->hire_date)->format('m/d/Y') }}</td>
            <td colspan="3" style="border:1px solid #000;">{{ $gain->previous_employer ?? '' }}</td>
            <td colspan="5" style="border:1px solid #000;"></td>
        </tr>
    @empty
        <tr>
            <td colspan="15" style="text-align:center;border:1px solid #000;">No new employees for this period.</td>
        </tr>
    @endforelse
    <tr>
        <td colspan="15"></td>
    </tr>

    <tr>
        <td colspan="15" style="font-weight:bold;">B. LOSSES</td>
    </tr>
    <tr>
        <th style="border:1px solid #000;">NO</th>
        <th colspan="2" style="border:1px solid #000;">NAME OF GUARDS (FAMILY NAME, FIRST NAME) MIDDLE NAME &amp;
            QUALIFIER (if any)</th>
        <th colspan="2" style="border:1px solid #000;">LAST POSTING PLACE</th>
        <th style="border:1px solid #000;">DATE TERMINATED</th>
        <th colspan="8" style="border:1px solid #000;">CAUSE(S) OF TERMINATION</th>
    </tr>
    @forelse ($losses as $index => $loss)
        <tr>
            <td style="border:1px solid #000;">{{ $index + 1 }}</td>
            <td colspan="2" style="border:1px solid #000;">{{ Str::upper($loss->last_name) }},
                {{ Str::upper($loss->first_name) }} {{ Str::upper($loss->middle_name) }}</td>
            <td colspan="2" style="border:1px solid #000;">{{ $loss->location_name ?? '' }}</td>
            <td style="border:1px solid #000;">{{ Carbon::parse($loss->termination_date)->format('m/d/Y') }}</td>
            <td colspan="8" style="border:1px solid #000;">{{ $statuses[$loss->status] ?? '' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="15" style="text-align:center;border:1px solid #000;">No resigned/ terminated employees for
                this period.</td>
        </tr>
    @endforelse
    <tr>
        <td colspan="15"></td>
    </tr>

    {{-- CERTIFICATION --}}
    <tr>
        <td colspan="15">I HEREBY CERTIFY the correctness of the disposition report for the month of
            {{ $monthLabel }}.</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>

    {{-- SIGNATURES --}}
    <tr>
        <td colspan="7">PREPARED BY:</td>
        <td colspan="8">APPROVED BY:</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>
    <tr>
        <td colspan="7">_________________________________</td>
        <td colspan="8">_________________________________</td>
    </tr>
    <tr>
        <td colspan="7" style="font-weight:bold;">ARTHUR G. AQUINO, LLB, CSP, CMPP</td>
        <td colspan="8" style="font-weight:bold;">SHIELLA MARIE P. LIPANA - BALTAZAR</td>
    </tr>
    <tr>
        <td colspan="7">OPERATIONS MANAGER</td>
        <td colspan="8">VICE PRESIDENT FOR ADMIN AND FINANCE</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>

    {{-- NOTARY --}}
    <tr>
        <td colspan="15">SUBSCRIBED AND SWORN to before me this ____ day of ____________, 20___, affiant exhibiting
            to me his/her ___________ with No. __________________ issued on ______________ at __________________.</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>
    <tr>
        <td colspan="15">(NOTARY PUBLIC)</td>
    </tr>
    <tr>
        <td colspan="15">Doc. No. ______;</td>
    </tr>
    <tr>
        <td colspan="15">Page No. ______;</td>
    </tr>
    <tr>
        <td colspan="15">Book No. ______;</td>
    </tr>
    <tr>
        <td colspan="15">Series of 20___.</td>
    </tr>
</table>
