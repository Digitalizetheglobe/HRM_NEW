
@php
    if (!function_exists('breakAfterWords')) {
        function breakAfterWords($text, $wordsPerLine = 3) {
            $words = explode(' ', $text);
            $lines = array_chunk($words, $wordsPerLine);
            return implode('<br>', array_map('implode', array_fill(0, count($lines), ' '), $lines));
        }
    }
@endphp

@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Attendance List') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Attendance List') }}</li>
@endsection


@push('script-page')
    <script>
        $('input[name="type"]:radio').on('change', function(e) {
            var type = $(this).val();

            if (type == 'monthly') {
                $('.month').addClass('d-block');
                $('.month').removeClass('d-none');
                $('.date').addClass('d-none');
                $('.date').removeClass('d-block');
            } else {
                $('.date').addClass('d-block');
                $('.date').removeClass('d-none');
                $('.month').addClass('d-none');
                $('.month').removeClass('d-block');
            }
        });

        $('input[name="type"]:radio:checked').trigger('change');
    </script>

    <script>
        $(document).ready(function() {
            var b_id = $('#branch_id').val();
            // getDepartment(b_id);
        });
        $(document).on('change', 'select[name=branch]', function() {
            var branch_id = $(this).val();

            getDepartment(branch_id);
        });

        function getDepartment(bid) {

            $.ajax({
                url: '{{ route('monthly.getdepartment') }}',
                type: 'POST',
                data: {
                    "branch_id": bid,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {

                    $('.department_id').empty();
                    var emp_selct = `<select class="form-control department_id" name="department_id" id="choices-multiple"
                                            placeholder="Select Department" >
                                            </select>`;
                    $('.department_div').html(emp_selct);

                    $('.department_id').append('<option value=""> {{ __('Select Department') }} </option>');
                    $.each(data, function(key, value) {
                        $('.department_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                    new Choices('#choices-multiple', {
                        removeItemButton: true,
                    });
                }
            });
        }
    </script>
@endpush
@section('action-button')
@endsection
@section('content')
    @if (session('status'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('   ') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-sm-12">
            <div class=" mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['attendanceemployee.index'], 'method' => 'get', 'id' => 'attendanceemployee_filter']) }}
                        <div class="row align-items-end justify-content-center">
                            <div class="col-xl">
                                <div class="row align-items-center">
                                    <div class="col-xl-auto col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
                                            <div class="d-flex flex-nowrap gap-3 pt-2">
                                                <div class="form-check">
                                                    <input type="radio" id="daily" value="daily" name="type"
                                                        class="form-check-input"
                                                        {{ isset($_GET['type']) && $_GET['type'] == 'daily' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="daily">{{ __('Daily') }}</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio" id="monthly" value="monthly" name="type"
                                                        class="form-check-input"
                                                        {{ isset($_GET['type']) && $_GET['type'] == 'monthly' ? 'checked' : 'checked' }}>
                                                    <label class="form-check-label" for="monthly">{{ __('Monthly') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl col-lg-6 col-md-12 col-sm-12 col-12 month">
                                        <div class="btn-box">
                                            {{ Form::label('month', __('Month'), ['class' => 'form-label']) }}
                                            {{ Form::month('month', isset($_GET['month']) ? $_GET['month'] : date('Y-m'), ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl col-lg-3 col-md-6 col-sm-12 col-12 date">
                                        <div class="btn-box">
                                            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                            {{ Form::date('date', isset($_GET['date']) ? $_GET['date'] : '', ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                    @if (\Auth::user()->type != 'employee')
                                        <div class="col-xl col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                                                {{ Form::select('branch', $branch, isset($_GET['branch']) ? $_GET['branch'] : '', ['class' => 'form-control select branch_id', 'id' => 'branch_id']) }}
                                            </div>
                                        </div>
                                        <div class="col-xl col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                                                {{ Form::select('department', $department, isset($_GET['department']) ? $_GET['department'] : '', ['class' => 'form-control select department_id', 'id' => 'department_id']) }}
                                            </div>
                                        </div>
                                        <div class="col-xl col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('employee', __('Employee'), ['class' => 'form-label']) }}
                                                {{ Form::select('employee', $employees, isset($_GET['employee']) ? $_GET['employee'] : '', ['class' => 'form-control select', 'id' => 'employee_id']) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-auto">
                                <div class="d-flex flex-wrap gap-2 justify-content-end flex-nowrap">
                                    <a href="#" class="btn btn-sm btn-primary"
                                        onclick="document.getElementById('attendanceemployee_filter').submit(); return false;"
                                        data-bs-toggle="tooltip" title="{{ __('Apply') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>

                                    <a href="{{ route('attendanceemployee.index') }}" class="btn btn-sm btn-danger"
                                        data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-trash-off"></i></span>
                                    </a>

                                    <a href="#" data-url="{{ route('attendance.file.import') }}"
                                        data-ajax-popup="true" data-title="{{ __('Import Attendance CSV File') }}"
                                        data-bs-toggle="tooltip" title="{{ __('Import') }}" class="btn btn-sm btn-primary">
                                        <span class="btn-inner--icon"><i class="ti ti-file"></i></span>
                                    </a>

                                    <a href="{{ route('attendance.export', request()->query()) }}" class="btn btn-sm btn-primary" 
                                        data-bs-toggle="tooltip" title="{{ __('Export') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style" >
                    <div class="table-responsive" style="padding: 15px !important">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    @if (\Auth::user()->type != 'employee')
                                        <th class="text-start">{{ __('Employee') }}</th>
                                    @endif
                                    <th class="text-start">{{ __('Date') }}</th>
                                    <th class="text-start">{{ __('Status') }}</th>
                                    <th class="text-start">{{ __('Reason') }}</th>
                                    <th class="text-start">{{ __('Clock-In Time') }}</th>
                                    <th class="text-start">{{ __('Clock-Out Time') }}</th>
                                    <th class="text-start">{{ __('Total Hours') }}</th>
                                    <th class="text-start">{{ __('Difference') }}</th>
                                    @if (Gate::check('Edit Attendance') || Gate::check('Delete Attendance'))
                                        <th width="200px" class="text-center">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendanceEmployee as $attendance)
                                    @php
                                        // Calculate total hours based on 8/4 rule
                                        $totalHoursFormatted = '-';
                                        $differenceFormatted = '-';
                                        $totalMinutes = null;
                                        $diffMinutes = null;
                                        
                                        if ($attendance->clock_in != '00:00:00' && $attendance->clock_out != '00:00:00' && !empty($attendance->clock_in) && !empty($attendance->clock_out)) {
                                            try {
                                                $date = $attendance->date;
                                                $inTime = \Carbon\Carbon::parse($date . ' ' . $attendance->clock_in);
                                                $outTime = \Carbon\Carbon::parse($date . ' ' . $attendance->clock_out);
                                                
                                                if ($outTime->lt($inTime)) {
                                                    $outTime->addDay();
                                                }
                                                
                                                $totalMinutes = $outTime->diffInMinutes($inTime);
                                                
                                                $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
                                                $standardMinutes = ($dayOfWeek == \Carbon\Carbon::SATURDAY) ? 240 : 480;
                                                
                                                $diffMinutes = $totalMinutes - $standardMinutes;
                                                
                                                $hours = floor($totalMinutes / 60);
                                                $minutes = $totalMinutes % 60;
                                                $totalHoursFormatted = $hours . 'h ' . $minutes . 'm';
                                                
                                                if ($diffMinutes == 0) {
                                                    $differenceFormatted = '0m';
                                                } else {
                                                    $sign = $diffMinutes > 0 ? '+' : '-';
                                                    $absMinutes = abs($diffMinutes);
                                                    
                                                    if ($absMinutes >= 60) {
                                                        $diffHours = floor($absMinutes / 60);
                                                        $diffMins = $absMinutes % 60;
                                                        $differenceFormatted = $diffMins > 0 ? $sign . $diffHours . 'h ' . $diffMins . 'm' : $sign . $diffHours . 'h';
                                                    } else {
                                                        $differenceFormatted = $sign . $absMinutes . 'm';
                                                    }
                                                }
                                            } catch (\Exception $e) {
                                                // ignore
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        @if (\Auth::user()->type != 'employee')
                                            <td class="text-start">{{ !empty($attendance->employee) ? $attendance->employee->full_name : '' }}</td>
                                        @endif
                                        <td class="text-start">{{ \Auth::user()->dateFormat($attendance->date) }}</td>
                                        <td class="text-start">
                                            @php
                                                $isSinglePunch = ($attendance->clock_out == '00:00:00' || empty($attendance->clock_out)) && !empty($attendance->clock_in) && $attendance->clock_in != '00:00:00';
                                                
                                                // Past single punches are natively resolved in the database
                                            @endphp

                                            @if($isSinglePunch)
                                                <span class="badge bg-info">{{ __('Single Punch In') }}</span>
                                            @else
                                                @php
                                                    $displayStatus = $attendance->status;
                                                    if ($displayStatus == 'Early Clock-Out') {
                                                        $displayStatus = 'Present';
                                                    }
                                                @endphp
                                                <span class="badge {{ $displayStatus == 'Present' ? 'bg-success' : ($displayStatus == 'Absent' ? 'bg-danger' : 'bg-primary') }}">{{ __($displayStatus) }}</span>
                                            @endif

                                            @php
                                                $isLate = !empty($attendance->late) && $attendance->late != '00:00:00';
                                            @endphp

                                            <div class="mt-1 d-flex flex-wrap gap-1">
                                                @if($isLate)
                                                    <span class="badge bg-danger" title="{{ __('Late by: ') . $attendance->late }}">{{ __('Late') }}</span>
                                                @endif
                                                @if($attendance->status == 'Early Clock-Out')
                                                    <span class="badge bg-secondary" title="{{ __('Early Clock-Out') }}">{{ __('Early Clock-Out') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            @if(!empty($attendance->status_reason))
                                                <span class="badge bg-warning text-dark">{{ $attendance->status_reason }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-start">{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}
                                        </td>
                                        <td class="text-start">{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}
                                        </td>
                                        
                                        <td class="text-start">{{ $totalHoursFormatted }}</td>
                                        <td class="text-start">
                                            @if($diffMinutes !== null)
                                                <span class="{{ $diffMinutes >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $differenceFormatted }}
                                                </span>
                                            @else
                                                {{ $differenceFormatted }}
                                            @endif
                                        </td>
                                        @if (Gate::check('Edit Attendance') || Gate::check('Delete Attendance'))
                                            <td class="Action" style="vertical-align: middle;">
                                                <span class="d-flex align-items-center justify-content-center">
                                                    @can('Edit Attendance')
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="#" class="btn btn-sm d-flex align-items-center justify-content-center"
                                                                data-size="lg"
                                                                data-url="{{ URL::to('attendanceemployee/' . $attendance->id . '/edit') }}"
                                                                data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('Edit Attendance') }}"
                                                                data-bs-original-title="{{ __('Edit') }}"
                                                                style="width: 100%; height: 100%; padding: 0; margin: 0;">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endcan

                                                    @can('Delete Attendance')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['attendanceemployee.destroy', $attendance->id],
                                                                'id' => 'delete-form-' . $attendance->id,
                                                                'style' => 'display: contents;',
                                                            ]) !!}
                                                            <a href="#"
                                                                class="btn btn-sm d-flex align-items-center justify-content-center bs-pass-para"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{ $attendance->id }}').submit();"
                                                                style="width: 100%; height: 100%; padding: 0; margin: 0;">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                </span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<script>
    $(document).ready(function() {
    $('.export-btn').click(function() {
        // Get all the filter values
        var type = $('input[name="type"]:checked').val();
        var month = $('input[name="month"]').val();
        var date = $('input[name="date"]').val();
        var branch = $('select[name="branch"]').val();
        var department = $('select[name="department"]').val();
        
        // Build the export URL with all filters
        var url = "{{ route('attendance.export') }}";
        url += "?type=" + type;
        
        if (type == 'monthly' && month) {
            url += "&month=" + month;
        } else if (type == 'daily' && date) {
            url += "&date=" + date;
        }
        
        if (branch) {
            url += "&branch=" + branch;
        }
        
        if (department) {
            url += "&department=" + department;
        }
        
        // Redirect to the export URL which will trigger the download
        window.location.href = url;
    });
});
</script>

    @push('scripts')
    <style>
        /* Mobile-specific styles */
        @media (max-width: 768px) {
            /* Force Type radio buttons to be inline on mobile */
            .btn-box .d-flex {
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                gap: 1rem !important;
            }
            
            .btn-box .form-check {
                display: inline-block !important;
                margin-right: 1rem !important;
            }
            
            /* Force action buttons to be properly spaced on mobile */
            .justify-content-end.gap-2 {
                flex-direction: row !important;
                flex-wrap: wrap !important;
                gap: 0.5rem !important;
                justify-content: flex-end !important;
                margin-top: 15px !important;
            }
            
            .btn-sm {
                padding: 0.25rem 0.5rem !important;
            }
        }
        
        .table th {
            white-space: nowrap;
            text-align: left !important;
            vertical-align: middle !important;
            padding-right: 25px !important;
            position: relative;
        }
        
        .table td {
            vertical-align: middle !important;
        }
        
        /* Fix DataTables sorting icons alignment */
        .dataTables_wrapper .dataTables_scrollHead .table th {
            position: relative;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting:after,
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_asc:after,
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_desc:after {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: 8px !important;
            margin-top: 0 !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_asc:after {
            content: "·" !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_desc:after {
            content: "·" !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting:after {
            content: "·" !important;
            opacity: 0.3;
        }
        
        /* Ensure proper column width alignment */
        #pc-dt-simple th {
            min-width: 120px;
        }
        
        @if (\Auth::user()->type != 'employee')
        #pc-dt-simple th:nth-child(1) {
            min-width: 200px; /* Employee */
        }
        
        #pc-dt-simple th:nth-child(2) {
            min-width: 140px; /* Date */
        }
        
        #pc-dt-simple th:nth-child(3) {
            min-width: 120px; /* Status */
        }
        
        #pc-dt-simple th:nth-child(4) {
            min-width: 160px; /* Clock-In Time */
        }
        
        #pc-dt-simple th:nth-child(5) {
            min-width: 250px; /* Clock-In Location */
        }
        
        #pc-dt-simple th:nth-child(6) {
            min-width: 160px; /* Clock-In 2 */
        }
        
        #pc-dt-simple th:nth-child(7) {
            min-width: 250px; /* Clock-In 2 Location */
        }

        #pc-dt-simple th:nth-child(8) {
            min-width: 160px; /* Clock-Out Time */
        }
        
        #pc-dt-simple th:nth-child(9) {
            min-width: 250px; /* Clock-Out Location */
        }

        #pc-dt-simple th:nth-child(10) {
            min-width: 160px; /* Clock-Out 2 */
        }

        #pc-dt-simple th:nth-child(11) {
            min-width: 250px; /* Clock-Out 2 Location */
        }
        
        #pc-dt-simple th:nth-child(12) {
            min-width: 200px; /* Action */
        }
        @else
        #pc-dt-simple th:nth-child(1) {
            min-width: 140px; /* Date */
        }
        
        #pc-dt-simple th:nth-child(2) {
            min-width: 120px; /* Status */
        }
        
        #pc-dt-simple th:nth-child(3) {
            min-width: 160px; /* Clock-In Time */
        }
        
        #pc-dt-simple th:nth-child(4) {
            min-width: 250px; /* Clock-In Location */
        }
        
        #pc-dt-simple th:nth-child(5) {
            min-width: 160px; /* Clock-In 2 */
        }

        #pc-dt-simple th:nth-child(6) {
            min-width: 250px; /* Clock-In 2 Location */
        }

        #pc-dt-simple th:nth-child(7) {
            min-width: 160px; /* Clock-Out Time */
        }
        
        #pc-dt-simple th:nth-child(8) {
            min-width: 250px; /* Clock-Out Location */
        }

        #pc-dt-simple th:nth-child(9) {
            min-width: 160px; /* Clock-Out 2 */
        }

        #pc-dt-simple th:nth-child(10) {
            min-width: 250px; /* Clock-Out 2 Location */
        }
        
        #pc-dt-simple th:nth-child(11) {
            min-width: 200px; /* Action */
        }
        @endif
    </style>

@endpush

