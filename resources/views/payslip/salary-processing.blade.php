@extends('layouts.admin')

@section('page-title')
    {{ __('Salary Processing') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Salary Processing') }}</li>
@endsection

@section('content')
@php
    // Check if user is from Finance & Accounts department
    $isFinanceAccounts = false;
    if (\Auth::user()->type == 'employee') {
        // Load employee with department relationship
        $employee = \App\Models\Employee::where('user_id', \Auth::user()->id)
            ->with('department')
            ->first();
        if ($employee && $employee->department) {
            $deptName = strtolower(trim($employee->department->name));
            // Check for various possible department name formats
            $isFinanceAccounts = (
                $deptName == 'finance & accounts' || 
                $deptName == 'finance and accounts' ||
                $deptName == 'finance & account' ||
                strpos($deptName, 'finance') !== false && strpos($deptName, 'account') !== false
            );
        }
    } elseif (\Auth::user()->type == 'company') {
        $isFinanceAccounts = true;
    }
@endphp
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h5>{{ __('Salary Processing') }}</h5>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="btn-box" style="min-width: 120px;">
                            {{ Form::label('month', __('Month'), ['class' => 'form-label']) }}
                            {{ Form::select('month', $month, date('m'), ['class' => 'form-control month_date', 'placeholder' => __('Select Month')]) }}
                        </div>
                        <div class="btn-box" style="min-width: 100px;">
                            {{ Form::label('year', __('Year'), ['class' => 'form-label']) }}
                            {{ Form::select('year', $year, date('Y'), ['class' => 'form-control year_date']) }}
                        </div>
                        <div class="btn-box" style="min-width: 150px;">
                            {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}
                            {{ Form::select('department_id', $departments, '', ['class' => 'form-control department_filter', 'placeholder' => __('All Departments')]) }}
                        </div>
                        <div class="btn-box align-self-end">
                            {{ Form::open(['route' => ['salary-processing.export'], 'method' => 'POST', 'id' => 'salary_processing_export_form']) }}
                            <input type="hidden" name="datePicker" class="export_date_picker" value="">
                            <input type="hidden" name="department_id" class="export_department_id" value="">
                            <button type="submit" class="btn btn-primary" id="export_btn">
                                <i class="ti ti-file-export"></i> {{ __('Export') }}
                            </button>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-border-style">
                <table class="table" id="pc-dt-render-column-cells">
                        <thead>
                            <tr>
                                <th rowspan="2">{{ __('Employee Code') }}</th>
                                <th rowspan="2">{{ __('Employee Name') }}</th>
                                <th rowspan="2">{{ __('Monthly Days') }}</th>
                                <th rowspan="2">{{ __('Payable Days') }}</th>
                                <th rowspan="2">{{ __('Total Leave') }}</th>
                                <th rowspan="2">{{ __('Actual Salary') }}</th>
                                <th rowspan="2">{{ __('Monthly Salary') }}</th>
                                <th colspan="5" style="text-align: center; background-color: #e3f2fd;">{{ __('Monthly Salary Breakdown') }}</th>
                                <th rowspan="2">{{ __('Salary Arrears') }}</th>
                                <th rowspan="2">{{ __('Petrol Allowance') }}</th>
                                <th rowspan="2">{{ __('Gross Salary') }}</th>
                                <th colspan="5" style="text-align: center; background-color: #fff3e0;">{{ __('Deductions') }}</th>
                                <th rowspan="2">{{ __('Net Amount Payable') }}</th>
                                <th rowspan="2">{{ __('Final Salary') }}</th>
                                <th rowspan="2">{{ __('Status') }}</th>
                            </tr>
                            <tr>
                                <th style="background-color: #e3f2fd;">{{ __('Basic Pay') }}<br><small>(41%)</small></th>
                                <th style="background-color: #e3f2fd;">{{ __('HRA') }}<br><small>(25%)</small></th>
                                <th style="background-color: #e3f2fd;">{{ __('Conveyance') }}<br><small>(21%)</small></th>
                                <th style="background-color: #e3f2fd;">{{ __('Special Allowance') }}<br><small>(10%)</small></th>
                                <th style="background-color: #e3f2fd;">{{ __('Medical') }}<br><small>(3%)</small></th>
                                <th style="background-color: #fff3e0;">{{ __('LOP Days') }}</th>
                                <th style="background-color: #fff3e0;">{{ __('LOP Amount') }}</th>
                                <th style="background-color: #fff3e0;">{{ __('PT') }}<br><small>(₹200)</small></th>
                                <th style="background-color: #fff3e0;">{{ __('Salary Advance') }}</th>
                                <th style="background-color: #fff3e0;">{{ __('Other Deductions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {
            callback();

            function callback() {
                var month = $(".month_date").val();
                var year = $(".year_date").val();

                if (month == '' || month == '--') {
                    month = '{{ date('m') }}';
                    year = '{{ date('Y') }}';
                }

                var datePicker = year + '-' + month;
                var departmentId = $(".department_filter").val();
                
                // Build data object
                var ajaxData = {
                    "datePicker": datePicker,
                    "_token": "{{ csrf_token() }}",
                };
                
                // Only add department_id if a department is selected
                if (departmentId && departmentId !== '' && departmentId !== '0') {
                    ajaxData.department_id = departmentId;
                }

                $.ajax({
                    url: '{{ route('salary-processing.search_json') }}',
                    type: 'POST',
                    data: ajaxData,
                    success: function(data) {
                        console.clear();
                        var tr = '';
                        if (data.length > 0) {
                            $.each(data, function(indexInArray, valueOfElement) {
                                var url_employee = valueOfElement['url'];
                                
                                // Array structure: [id, employee_code, employee_name, monthly_days, payable_days, total_leave, actual_salary, monthly_salary, basic_pay, hra, conveyance_allowance, special_allowance, medical_allowance, salary_arrears, petrol_allowance, gross_salary, lop_days, lop_deduction_amount, professional_tax, salary_advance, other_deductions, net_amount_payable, final_payable_salary, status]
                                var employeeId = valueOfElement[0];
                                var employeeCode = valueOfElement[1];
                                var employeeName = valueOfElement[2];
                                var monthlyDays = parseFloat(valueOfElement[3]) || 0;
                                var payableDays = parseFloat(valueOfElement[4]) || 0;
                                var totalLeave = parseFloat(valueOfElement[5]) || 0;
                                var actualSalary = parseFloat(valueOfElement[6]) || 0;
                                var monthlySalary = parseFloat(valueOfElement[7]) || 0;
                                var basicPay = parseFloat(valueOfElement[8]) || 0;
                                var hra = parseFloat(valueOfElement[9]) || 0;
                                var conveyanceAllowance = parseFloat(valueOfElement[10]) || 0;
                                var specialAllowance = parseFloat(valueOfElement[11]) || 0;
                                var medicalAllowance = parseFloat(valueOfElement[12]) || 0;
                                var salaryArrears = parseFloat(valueOfElement[13]) || 0;
                                var petrolAllowance = parseFloat(valueOfElement[14]) || 0;
                                var grossSalary = parseFloat(valueOfElement[15]) || 0;
                                var lopDays = parseFloat(valueOfElement[16]) || 0;
                                var lopDeductionAmount = parseFloat(valueOfElement[17]) || 0;
                                var professionalTax = parseFloat(valueOfElement[18]) || 0;
                                var salaryAdvance = parseFloat(valueOfElement[19]) || 0;
                                var otherDeductions = parseFloat(valueOfElement[20]) || 0;
                                var netAmountPayable = parseFloat(valueOfElement[21]) || 0;
                                var finalPayableSalary = parseFloat(valueOfElement[22]) || 0;
                                var status = valueOfElement[23] || 'Pending';

                                // Format numbers with Indian locale (comma separators)
                                function formatCurrency(num) {
                                    return parseFloat(num).toLocaleString('en-IN', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }

                                // Status cell - show button with confirmation for Finance & Accounts, badge for others
                                var statusCell = '';
                                @if ($isFinanceAccounts)
                                    var statusBadgeClass = status === 'Done' ? 'bg-success' : 'bg-info';
                                    
                                    if (status === 'Done') {
                                        // When status is Done, only show badge (no button - payment is final)
                                        statusCell = '<td><span class="badge ' + statusBadgeClass + '">' + status + '</span></td>';
                                    } else {
                                        // When status is Pending, show badge + Mark as Paid button
                                        statusCell = '<td>' +
                                            '<span class="badge ' + statusBadgeClass + '" style="margin-right: 10px;">' + status + '</span>' +
                                            '<button type="button" class="btn btn-sm btn-success mark-payment-btn" ' +
                                            'data-employee-id="' + employeeId + '" ' +
                                            'data-employee-name="' + employeeName + '" ' +
                                            'data-current-status="' + status + '" ' +
                                            'data-new-status="Done">' +
                                            '<i class="ti ti-check"></i> Mark as Paid' +
                                            '</button>' +
                                            '</td>';
                                    }
                                @else
                                    var statusBadgeClass = status === 'Done' ? 'bg-success' : 'bg-info';
                                    statusCell = '<td><span class="badge ' + statusBadgeClass + '">' + status + '</span></td>';
                                @endif

                                tr +=
                                    '<tr>' +
                                    '<td><a class="btn btn-outline-primary" href="' + url_employee + '">' + employeeCode + '</a></td>' +
                                    '<td>' + employeeName + '</td>' +
                                    '<td>' + formatCurrency(monthlyDays) + '</td>' +
                                    '<td>' + formatCurrency(payableDays) + '</td>' +
                                    '<td>' + formatCurrency(totalLeave) + '</td>' +
                                    '<td>' + formatCurrency(actualSalary) + '</td>' +
                                    '<td><strong>' + formatCurrency(monthlySalary) + '</strong></td>' +
                                    '<td style="background-color: #e3f2fd;">' + formatCurrency(basicPay) + '</td>' +
                                    '<td style="background-color: #e3f2fd;">' + formatCurrency(hra) + '</td>' +
                                    '<td style="background-color: #e3f2fd;">' + formatCurrency(conveyanceAllowance) + '</td>' +
                                    '<td style="background-color: #e3f2fd;">' + formatCurrency(specialAllowance) + '</td>' +
                                    '<td style="background-color: #e3f2fd;">' + formatCurrency(medicalAllowance) + '</td>' +
                                    '<td>' + formatCurrency(salaryArrears) + '</td>' +
                                    '<td>' + formatCurrency(petrolAllowance) + '</td>' +
                                    '<td><strong>' + formatCurrency(grossSalary) + '</strong></td>' +
                                    '<td style="background-color: #fff3e0;">' + formatCurrency(lopDays) + '</td>' +
                                    '<td style="background-color: #fff3e0;">' + formatCurrency(lopDeductionAmount) + '</td>' +
                                    '<td style="background-color: #fff3e0;">' + formatCurrency(professionalTax) + '</td>' +
                                    '<td style="background-color: #fff3e0;">' + formatCurrency(salaryAdvance) + '</td>' +
                                    '<td style="background-color: #fff3e0;">' + formatCurrency(otherDeductions) + '</td>' +
                                    '<td style="background-color: #fff3e0;"><strong>' + formatCurrency(netAmountPayable) + '</strong></td>' +
                                    '<td><strong style="color: #28a745; font-size: 1.1em;">' + formatCurrency(finalPayableSalary) + '</strong></td>' +
                                    statusCell +
                                    '</tr>';
                            });
                        } else {
                            // Count total columns including rowspan headers
                            var colspan = 23; // Total columns: 7 single + 5 allowance breakdown + 5 deduction + 6 others
                            tr = '<tr><td class="dataTables-empty" colspan="' + colspan +
                                '">{{ __('No entries found') }}</td></tr>';
                        }

                        $('#pc-dt-render-column-cells tbody').html(tr);
                        var table = document.querySelector("#pc-dt-render-column-cells");
                        if (table && typeof simpleDatatables !== 'undefined') {
                            var datatable = new simpleDatatables.DataTable(table);
                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            }

            $(document).on("change", ".month_date,.year_date,.department_filter", function() {
                callback();
            });

            // Update export date picker and department when month/year/department changes
            function updateExportForm() {
                var month = $(".month_date").val();
                var year = $(".year_date").val();
                var departmentId = $(".department_filter").val();

                if (month == '' || month == '--') {
                    month = '{{ date('m') }}';
                    year = '{{ date('Y') }}';
                }

                var datePicker = year + '-' + month;
                $('.export_date_picker').val(datePicker);
                $('.export_department_id').val(departmentId || '');
            }

            // Initialize export form
            updateExportForm();

            // Update export form on change
            $(document).on("change", ".month_date,.year_date,.department_filter", function() {
                updateExportForm();
            });

            // Handle payment status change with confirmation for Finance & Accounts users
            @if ($isFinanceAccounts)
            $(document).on("click", ".mark-payment-btn", function(e) {
                e.preventDefault();
                var $btn = $(this);
                var employeeId = $btn.data('employee-id');
                var employeeName = $btn.data('employee-name');
                var currentStatus = $btn.data('current-status');
                var newStatus = $btn.data('new-status');
                var month = $(".month_date").val();
                var year = $(".year_date").val();

                if (month == '' || month == '--') {
                    month = '{{ date('m') }}';
                    year = '{{ date('Y') }}';
                }

                var monthNames = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
                var monthName = monthNames[parseInt(month) - 1];
                var statusText = newStatus === 'Done' ? 'Paid' : 'Pending';

                // Show confirmation modal using SweetAlert
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: 'Confirm Payment Status Change',
                    html: '<div class="text-start">' +
                          '<p><strong>Employee:</strong> ' + employeeName + '</p>' +
                          '<p><strong>Period:</strong> ' + monthName + ' ' + year + '</p>' +
                          '<p><strong>Current Status:</strong> <span class="badge bg-info">' + currentStatus + '</span></p>' +
                          '<p><strong>New Status:</strong> <span class="badge bg-success">' + statusText + '</span></p>' +
                          '<hr>' +
                          '<p class="text-danger"><strong>Are you sure the payment has been completed?</strong></p>' +
                          '<p class="text-muted small">This action will mark the salary payment as ' + statusText + ' for this employee.</p>' +
                          '</div>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-check"></i> Yes, Confirm Payment',
                    cancelButtonText: '<i class="ti ti-x"></i> Cancel',
                    reverseButtons: true,
                    focusConfirm: false,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable button during processing
                        $btn.prop('disabled', true).html('<i class="ti ti-loader"></i> Processing...');

                        $.ajax({
                            url: '{{ route('salary-processing.update-status') }}',
                            type: 'POST',
                            data: {
                                employee_id: employeeId,
                                year: year,
                                month: month,
                                status: newStatus,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    swalWithBootstrapButtons.fire({
                                        title: 'Success!',
                                        text: 'Payment status has been updated successfully.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        // Reload the table to show updated status
                                        callback();
                                    });
                                } else {
                                    swalWithBootstrapButtons.fire({
                                        title: 'Error!',
                                        text: response.message || 'Failed to update status',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                    $btn.prop('disabled', false);
                                }
                            },
                            error: function(xhr) {
                                var errorMsg = 'Failed to update payment status';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMsg = xhr.responseJSON.error;
                                }
                                
                                swalWithBootstrapButtons.fire({
                                    title: 'Error!',
                                    text: errorMsg,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                
                                // Re-enable button and reload table
                                $btn.prop('disabled', false);
                                var originalText = newStatus === 'Done' ? '<i class="ti ti-check"></i> Mark as Paid' : '<i class="ti ti-x"></i> Mark as Pending';
                                $btn.html(originalText);
                                callback();
                            }
                        });
                    } else {
                        // User cancelled - do nothing
                    }
                });
            });
            @endif
        });
    </script>
    
    <style>
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
        #pc-dt-render-column-cells th {
            min-width: 120px;
        }
        
        #pc-dt-render-column-cells th:nth-child(1),
        #pc-dt-render-column-cells th:nth-child(2) {
            min-width: 180px; /* Employee Code, Employee Name */
        }
        
        #pc-dt-render-column-cells th:nth-child(3),
        #pc-dt-render-column-cells th:nth-child(4) {
            min-width: 160px; /* Monthly Days, Payable Days */
        }
        
        #pc-dt-render-column-cells th:nth-child(5),
        #pc-dt-render-column-cells th:nth-child(6) {
            min-width: 180px; /* Total Leave, Actual Salary */
        }
        
        #pc-dt-render-column-cells th:nth-child(7),
        #pc-dt-render-column-cells th:nth-child(8) {
            min-width: 240px; /* Monthly Salary, Salary Arrears */
        }
        
        #pc-dt-render-column-cells th:nth-child(9),
        #pc-dt-render-column-cells th:nth-child(10) {
            min-width: 180px; /* Petrol Allowance, Gross Salary */
        }
        
        #pc-dt-render-column-cells th:nth-child(11) {
            min-width: 160px; /* Net Amount Payable */
        }
        
        #pc-dt-render-column-cells th:nth-child(12) {
            min-width: 350px; /* Final Salary - increased width */
        }
        
        #pc-dt-render-column-cells th:nth-child(13) {
            min-width: 200px; /* Status */
        }
        
        /* Special styling for header rows */
        #pc-dt-render-column-cells tr:nth-child(2) th {
            background-color: #e3f2fd !important;
        }
        
        #pc-dt-render-column-cells tr:nth-child(3) th {
            background-color: #fff3e0 !important;
        }
    </style>
    
    <!-- <script>
        $(document).ready(function() {
            // Initialize DataTables with proper configuration
            $('#pc-dt-render-column-cells').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    emptyTable: "No salary processing records found"
                },
                autoWidth: false,
                scrollX: true
            });
        });
    </script> -->
@endpush


