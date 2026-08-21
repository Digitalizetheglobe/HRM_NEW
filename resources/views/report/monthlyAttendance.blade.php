@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Monthly Attendance') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Manage Monthly Attendance Report') }}</li>
@endsection
@section('action-button')

    @php
        $emp = isset($_GET['employee_id']) && !empty($_GET['employee_id']) ? $_GET['employee_id'] : [];
        $employees = implode(', ', $emp);
    @endphp

    <a href="#" class="btn btn-sm btn-primary" onclick="exportToExcel()" data-bs-toggle="tooltip" title="{{ __('Export') }}"
        data-original-title="{{ __('Export') }}">
        <span class="btn-inner--icon"><i class="ti ti-file-export"></i></span>
    </a>
@endsection

@section('action-button')
    <!-- ... other buttons ... -->

    {{-- Updated Export Button --}}
    <a href="#" id="exportBtn" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Export to Excel') }}">
        <span class="btn-inner--icon"><i class="ti ti-file-export"></i></span> {{ __('Export to Excel') }}
    </a>
@endsection

@push('script-page')
    <!-- Include required libraries -->
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.full.min.js"></script>

    <script>
        var filename = $('#filename').val();

        // PDF Export function (existing)
        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 4,
                    dpi: 72,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A2'
                }
            };
            html2pdf().set(opt).from(element).save();
        }

        // New Excel Export function with error handling
        function exportToExcel() {
            // Use the displayed month, not just the filter input
            var displayedMonth = $('#displayed_month').val();
            if (!displayedMonth) {
                alert('No month selected');
                return;
            }
            try {
                // Get other filter values
                var branch = $('#branch_id').val() || 0;
                var department = $('#department_id').val() || 0;
                var employee = $('#employee_id').val() ? $('#employee_id').val().join(',') : 0;
                // Build the export URL
                var url = "{{ route('report.attendance', ['month' => 'MONTH', 'branch' => 'BRANCH', 'department' => 'DEPARTMENT', 'employee' => 'EMPLOYEE']) }}";
                url = url.replace('MONTH', encodeURIComponent(displayedMonth));
                url = url.replace('BRANCH', branch);
                url = url.replace('DEPARTMENT', department);
                url = url.replace('EMPLOYEE', employee);
                window.location.href = url;
            } catch (error) {
                console.error('Export error:', error);
                alert('Error during export: ' + error.message);
            }
        }

        // Initialize button click event
        $(document).ready(function() {
            $('#exportBtn').on('click', function(e) {
                e.preventDefault();
                exportToExcel();
            });
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class=" mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['report.monthly.attendance'], 'method' => 'get', 'id' => 'report_monthly_attendance']) }}
                        <!-- Add hidden input for displayed month -->
                        <input type="hidden" id="displayed_month" value="{{ isset($_GET['month']) ? $_GET['month'] : \Carbon\Carbon::now()->format('Y-m') }}">
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('month', __(' Month'), ['class' => 'form-label']) }}
                                            {{ Form::month('month', isset($_GET['month']) ? $_GET['month'] : '', ['class' => 'month-btn form-control current_date', 'autocomplete' => 'off', 'placeholder' => 'Select month']) }}                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                                            {{ Form::select('branch_id', $branch, isset($_GET['branch']) ? $_GET['branch'] : '', ['class' => 'form-control select branch_id', 'id' => 'branch-select branch_id']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box" id="department_div">
                                            {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                                            <select class="form-control select department_id" name="department"
                                                id="department_id" placeholder="Select Department">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box" id="employee_div">
                                            {{ Form::label('employee', __('Employee'), ['class' => 'form-label']) }}
                                            <select class="form-control select" name="employee_id[]" id="employee_id"
                                                placeholder="Select Employee">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-auto mt-4">
                                        <a href="#" class="btn btn-sm btn-primary"
                                            onclick="document.getElementById('report_monthly_attendance').submit(); return false;"
                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{ route('report.monthly.attendance') }}" class="btn btn-sm btn-danger "
                                            data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                            data-original-title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon"><i
                                                    class="ti ti-trash-off text-white-off "></i></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

   

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <!-- Left Side: Status Indicators -->
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="me-3 d-flex align-items-center">
                                <div class="color-indicator present me-2"></div>
                                <span>Present</span>
                            </div>
                            <div class="me-3 d-flex align-items-center">
                                <div class="color-indicator absent me-2"></div>
                                <span>Absent</span>
                            </div>
                            <div class="me-3 d-flex align-items-center">
                                <div class="color-indicator week-off me-2"></div>
                                <span>Week Off</span>
                            </div>
                            <div class="me-3 d-flex align-items-center">
                                <div class="color-indicator leave me-2"></div>
                                <span>Leave</span>
                            </div>
                        </div>

                        <!-- Right Side: Selected Month Info -->
                        <div class="text-end fw-bold">
                            Selected Attendance Month :
                            <span class="">
                                {{ isset($_GET['month']) ? \Carbon\Carbon::parse($_GET['month'])->format('M Y') : \Carbon\Carbon::now()->format('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive py-4 attendance-table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="active">{{ __('Name') }}</th>
                                        @foreach ($dates as $key => $dateInfo)
                                            @php
                                                $date = $key;
                                                $day = $dateInfo['day'];
                                            @endphp
                                                <th class="day-header">
                                                    <div class="date-number">{{ $date }}</div>
                                                    <div class="day-abbr">{{ $day }}</div>
                                                </th>
                                        @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employeesAttendance as $employee)
                                    <tr>
                                            <td>{{ $employee['name'] }}</td>
                                            @foreach ($dates as $key => $dateInfo)
                                                @php
                                                    $date = $key; // '01', '02', etc.
                                                    $dateFormat = $dateInfo['full_date']; // e.g., "2025-07-01"
                                                    $dayOfWeek = \Carbon\Carbon::parse($dateFormat)->format('l'); // "Monday", etc.

                                                    $statusEntry = $employee['status'][$date] ?? null;  // controller passed 'P', 'A', etc.
                                                    $weekOffDay = $employee['week_off_day'] ?? null;

                                                    $isWeekOff = $weekOffDay && strtolower($dayOfWeek) == strtolower($weekOffDay);
                                                @endphp

                                                <td>
                                                    @if ($dateFormat > date('Y-m-d'))
                                                        {{-- Future date: show empty cell --}}
                                                    @elseif ($isWeekOff && $statusEntry && $statusEntry['status'] == 'P')
                                                        {{-- Week off but present --}}
                                                        <span class="badge bg-success p-2 triangle" title="Week Off ({{ $weekOffDay }}) + Present"> </span>
                                                    @elseif ($isWeekOff)
                                                        {{-- Week off and not present --}}
                                                        <span class="badge bg-info p-2 square" title="Week Off ({{ $weekOffDay }})"> </span>
                                                    @elseif ($statusEntry && $statusEntry['status'] == 'L')
                                                        <span class="badge bg-warning p-2 hexagon" title="Leave: {{ $statusEntry['type'] ?? '' }}"> </span>
                                                    @elseif ($statusEntry && $statusEntry['status'] == 'P')
                                                        <span class="badge bg-success p-2 triangle"> </span>
                                                    @elseif ($statusEntry && $statusEntry['status'] == 'A')
                                                        <span class="badge bg-danger p-2 square"> </span>
                                                    @else ($statusEntry && $statusEntry['status'] == 'Ab')
                                                        <span class="badge bg-danger p-2 square"> </span>
                                                    @endif
                                                </td>
                                        @endforeach
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

@push('script-page')
    <script>
        $(document).ready(function() {
            var b_id = $('#branch_id').val();
            // getDepartment(b_id);
        });
        $(document).on('change', 'select[name=branch_id]', function() {
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
                    var emp_selct = `<select class="department_id form-control multi-select" id="choices-multiple" multiple="" required="required" name="department_id[]">
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

        $(document).on('change', '.department_id', function() {
            var department_id = $(this).val();
            getEmployee(department_id);
        });

        function getEmployee(did) {

            $.ajax({
                url: '{{ route('monthly.getemployee') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {

                    $('#employee_id').empty();

                    $("#employee_div").html('');
                    // $('#employee_div').append('<select class="form-control" id="employee_id" name="employee_id[]"  multiple></select>');
                    $('#employee_div').append(
                        '<label for="employee" class="form-label">{{ __('Employee') }}</label><select class="form-control" id="employee_id" name="employee_id[]"  multiple></select>'
                    );

                    $('#employee_id').append('<option value="">{{ __('Select Employee') }}</option>');

                    $.each(data, function(key, value) {
                        $('#employee_id').append('<option value="' + key + '">' + value + '</option>');
                    });

                    var multipleCancelButton = new Choices('#employee_id', {
                        removeItemButton: true,
                    });
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            var now = new Date();
            var month = (now.getMonth() + 1);
            if (month < 10) month = "0" + month;
            var today = now.getFullYear() + '-' + month;
            $('.current_date').val(today);
        });
    </script>

    <style>
        .color-indicator {
            width: 15px;
            height: 15px;
            border-radius: 3px;
        }

        .color-indicator.present {
            background-color: #65d943;  /* green */
        }

        .color-indicator.absent {
            background-color: #ff3a63;  /* red */
        }

        .color-indicator.week-off {
            background-color: #3ec9d6;  /* gray */
        }

        .color-indicator.leave {
            background-color: #ffa21d;  /* yellow/orange */
        }

    </style>
@endpush
