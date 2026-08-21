@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Employee') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Employee') }}</li>
@endsection



@section('action-button')
    <a href="{{ route('employee.export') }}" data-bs-toggle="tooltip" data-bs-placement="top"
        data-bs-original-title="{{ __('Export') }}" class="btn btn-sm btn-primary">
        <i class="ti ti-file-export"></i>
    </a>


    @can('Create Assets')
            <a href="{{ route('employee.create') }}" 
               data-title="{{ __('Create New Employee') }}" 
               class="btn btn-sm btn-primary ">
                <i class="ti ti-plus"></i>
            </a>
    @endcan
@endsection



@section('content')
    @php
        // Check if logged-in user is from Finance & Accounts department
        $isFinanceAccounts = false;
        if (Auth::user()->type == 'employee') {
            try {
                // Method 1: Check by email pattern
                $userEmail = strtolower(Auth::user()->email ?? '');
                if (strpos($userEmail, 'accounts@') !== false || 
                    strpos($userEmail, 'finance@') !== false ||
                    strpos($userEmail, '@accounts') !== false ||
                    strpos($userEmail, '@finance') !== false) {
                    $isFinanceAccounts = true;
                }
                
                // Method 2: Check by department name
                if (!$isFinanceAccounts) {
                    $currentEmployee = \App\Models\Employee::where('user_id', Auth::user()->id)->first();
                    
                    if ($currentEmployee && !empty($currentEmployee->department_id)) {
                        $department = \App\Models\Department::where('id', $currentEmployee->department_id)
                            ->where('created_by', Auth::user()->creatorId())
                            ->first();
                        
                        if ($department) {
                            $deptName = strtolower(trim($department->name));
                            $isFinanceAccounts = (
                                $deptName == 'finance & accounts' || 
                                $deptName == 'finance and accounts' ||
                                $deptName == 'finance & account' ||
                                $deptName == 'finance & accounts team' ||
                                $deptName == 'finance and accounts team' ||
                                (strpos($deptName, 'finance') !== false && (strpos($deptName, 'account') !== false || strpos($deptName, 'accounts') !== false))
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error checking Finance & Accounts department: ' . $e->getMessage());
            }
        }
    @endphp
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-border-style-tab">
                        <ul class="nav nav-tabs" id="employeeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="true">
                                    {{ __('Active Employees') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="left-tab" data-bs-toggle="tab" data-bs-target="#left" type="button" role="tab" aria-controls="left" aria-selected="false">
                                    {{ __('Inactive Employees') }}
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content mt-3" id="employeeTabsContent">
                            <!-- Active Employees Tab -->
                            <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                                
                                    <table class="table" id="pc-dt-simple">
                                        <thead>
                                            <tr>
                                                <th class="text-start">{{ __('Employee ID') }}</th>
                                                <th class="text-start">{{ __('Name') }}</th>
                                                <th class="text-start">{{ __('Email') }}</th>
                                                <th class="text-start">{{ __('Branch') }}</th>
                                                <th class="text-start">{{ __('Department') }}</th>
                                                <th class="text-start">{{ __('Designation') }}</th>
                                                <th class="text-start">{{ __('Date Of Joining') }}</th>
                                                @if (Auth::user()->type != 'hr' && !$isFinanceAccounts && (Gate::check('Edit Employee') || Gate::check('Delete Employee')))
                                                    <th class="text-center" width="100px">{{ __('Action') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($activeEmployees as $employee)
                                                <tr>
                                                    <td class="text-start">
                                                        @can('Show Employee')
                                                            <a class="btn btn-outline-primary btn-sm"
                                                                href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">
                                                                {{ $employee->formatted_id }}
                                                            </a>
                                                        @else
                                                            <span class="badge bg-primary">
                                                                {{ $employee->formatted_id }}
                                                            </span>
                                                        @endcan
                                                    </td>
                                                    <td class="text-start">{{ $employee->full_name }}</td>
                                                    <td class="text-start">{{ $employee->email ?? '-' }}</td>  
                                                    <td class="text-start">
                                                        <span class="">
                                                            {{ $employee->branch?->name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-start">
                                                        <span class="">
                                                            {{ $employee->department?->name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-start">
                                                        <span class="">
                                                            {{ $employee->designation?->name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-start">{{ \Auth::user()->dateFormat($employee->company_doj) }}</td>
                                                    
                                                    @if (Auth::user()->type != 'hr' && !$isFinanceAccounts && (Gate::check('Edit Employee') || Gate::check('Delete Employee')))
                                                        <td class="Action text-center" style="white-space: nowrap;">
                                                            @if (($employee->user?->is_active ?? 0) == 1 && ($employee->user?->is_disable ?? 0) == 1)
                                                                @if(\Auth::user()->type == 'company')
                                                                    <a href="{{ route('employee.welcome.email', $employee->id) }}"
                                                                       class="btn btn-sm btn-icon-only bg-warning ms-2"
                                                                       data-bs-toggle="tooltip" 
                                                                       title="{{ __('Send Welcome Email') }}">
                                                                        <i class="ti ti-mail text-white"></i>
                                                                    </a>
                                                                @endif

                                                                @can('Edit Employee')
                                                                    <a href="{{ route('employee.edit', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}" 
                                                                    class="btn btn-sm btn-icon-only bg-info ms-2" 
                                                                    data-bs-toggle="tooltip" 
                                                                    title="{{ __('Edit') }}">
                                                                        <i class="ti ti-pencil text-white"></i>
                                                                    </a>
                                                                @endcan

                                                                @can('Delete Employee')
                                                                    {!! Form::open([
                                                                        'method' => 'DELETE',
                                                                        'route' => ['employee.destroy', $employee->id],
                                                                        'style' => 'display:inline'
                                                                    ]) !!}
                                                                    <a href="#"
                                                                    class="btn btn-sm btn-icon-only bg-danger ms-2 bs-pass-para"
                                                                    data-bs-toggle="tooltip" 
                                                                    title="{{ __('Delete') }}">
                                                                        <i class="ti ti-trash text-white"></i>
                                                                    </a>
                                                                    {!! Form::close() !!}
                                                                @endcan
                                                            @else
                                                                <i class="ti ti-lock"></i>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                            </div>
                            
                            <!-- Left Employees Tab -->
                            <div class="tab-pane fade" id="left" role="tabpanel" aria-labelledby="left-tab">
                                    <table class="table" id="pc-dt-simple2">
                                        <thead>
                                            <tr>
                                                <th class="text-start">{{ __('Employee ID') }}</th>
                                                <th class="text-start">{{ __('Name') }}</th>
                                                <th class="text-start">{{ __('Email') }}</th>
                                                <th class="text-start">{{ __('Branch') }}</th>
                                                <th class="text-start">{{ __('Department') }}</th>
                                                <th class="text-start">{{ __('Designation') }}</th>
                                                <th class="text-start">{{ __('Date Of Joining') }}</th>
                                                <th class="text-start">{{ __('Termination Date') }}</th>
                                                @if (Auth::user()->type != 'hr' && !$isFinanceAccounts && Gate::check('Show Employee'))
                                                    <th class="text-center" width="80px">{{ __('Action') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($leftEmployees as $employee)
                                                @php
                                                    $termination = \App\Models\Termination::where('employee_id', $employee->id)->first();
                                                @endphp
                                                <tr>
                                                    <td class="text-start">
                                                        <span class="">
                                                            <a class="btn btn-outline-primary btn-sm"
                                                                href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">
                                                                {{ $employee->formatted_id }}
                                                            </a>
                                                        </span>
                                                    </td>
                                                    <td class="text-start">{{ $employee->full_name }}</td>
                                                    <td class="text-start">{{ $employee->email ?? '-' }}</td>  
                                                    <td class="text-start">
                                                        <span class="">
                                                            {{ $employee->branch?->name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-start">
                                                        <span class="">
                                                            {{ $employee->department?->name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-start">
                                                        <span class="">
                                                            {{ $employee->designation?->name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-start">{{ \Auth::user()->dateFormat($employee->company_doj) }}</td>
                                                    <td class="text-start">
                                                        @if($termination)
                                                            {{ \Auth::user()->dateFormat($termination->termination_date) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    @if (Auth::user()->type != 'hr' && !$isFinanceAccounts && Gate::check('Show Employee'))
                                                        <td class="Action text-center">
                                                            @can('Show Employee')
                                                                <a href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}"
                                                                class="btn btn-sm btn-icon-only bg-info"
                                                                data-bs-toggle="tooltip" 
                                                                title="{{ __('View') }}">
                                                                    <i class="ti ti-eye text-white"></i>
                                                                </a>
                                                            @endcan
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
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        /* Mobile responsive tabs */
        @media (max-width: 768px) {
            .nav-tabs {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                border-bottom: 1px solid #dee2e6;
                margin-bottom: 0;
            }
            
            .nav-tabs .nav-item {
                flex: 1;
                min-width: 150px;
            }
            
            .nav-tabs .nav-link {
                display: block;
                width: 100%;
                padding: 0.5rem 1rem;
                text-align: center;
                white-space: nowrap;
                border: 1px solid transparent;
                border-bottom: none;
                border-radius: 0.375rem 0.375rem 0 0;
                font-size: 0.875rem;
                color: #dc3545;
                background-color: #f8f9fa;
            }
            
            .nav-tabs .nav-link:hover {
                border-color: #e9ecef #e9ecef #dee2e6;
                color: #dc3545;
                background-color: #e9ecef;
            }
            
            .nav-tabs .nav-link.active {
                color: #dc3545;
                background-color: #fff;
                border: 1px solid #dee2e6;
                border-bottom-color: transparent;
            }
            
            /* Remove thick border/scroll artifact */
            .nav-tabs::-webkit-scrollbar {
                display: none;
            }
            .nav-tabs {
                -ms-overflow-style: none;
                scrollbar-width: none;
                border-right: none !important;
            }
            
            /* Ensure tab content is responsive */
            .tab-content {
                overflow-x: auto;
            }
            
            .table-responsive {
                margin-bottom: 0;
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
            content: "↑" !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_desc:after {
            content: "↓" !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting:after {
            content: "↕" !important;
            opacity: 0.3;
        }
        
        /* Ensure proper column width alignment */
        #pc-dt-simple th,
        #pc-dt-simple2 th {
            min-width: 120px;
        }
        
        #pc-dt-simple th:nth-child(1),
        #pc-dt-simple2 th:nth-child(1) {
            min-width: 160px; /* Employee ID */
        }
        
        #pc-dt-simple th:nth-child(2),
        #pc-dt-simple2 th:nth-child(2) {
            min-width: 180px; /* Name */
        }
        
        #pc-dt-simple th:nth-child(3),
        #pc-dt-simple2 th:nth-child(3) {
            min-width: 250px; /* Email */
        }
        
        #pc-dt-simple th:nth-child(4),
        #pc-dt-simple2 th:nth-child(4) {
            min-width: 140px; /* Branch */
        }
        
        #pc-dt-simple th:nth-child(5),
        #pc-dt-simple2 th:nth-child(5) {
            min-width: 160px; /* Department */
        }
        
        #pc-dt-simple th:nth-child(6),
        #pc-dt-simple2 th:nth-child(6) {
            min-width: 160px; /* Designation */
        }
        
        #pc-dt-simple th:nth-child(7),
        #pc-dt-simple2 th:nth-child(7) {
            min-width: 180px; /* Date of Joining */
        }
        
        #pc-dt-simple th:nth-child(8) {
            min-width: 120px; /* Action - Active table */
        }
        
        #pc-dt-simple2 th:nth-child(8) {
            min-width: 180px; /* Termination Date */
        }
        
        #pc-dt-simple2 th:nth-child(9) {
            min-width: 100px; /* Action - Inactive table */
        }
    </style>
    <script>
        $(document).ready(function() {
            // Initialize the second table with simple-datatables
            // The first table (#pc-dt-simple) is already initialized in admin.blade.php
            if (document.querySelector("#pc-dt-simple2")) {
                const dataTable2 = new simpleDatatables.DataTable("#pc-dt-simple2");
            }
            
            // Delete functionality with confirmation
            $(document).on('click', '.bs-pass-para', function(e) {
                e.preventDefault();
                const button = $(this);
                const form = button.closest('form');
                
                if (!confirm('Are you sure you want to delete this employee?')) {
                    return;
                }

                // Show loading state
                button.prop('disabled', true);
                button.html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove the row with animation
                            button.closest('tr').fadeOut(400, function() {
                                $(this).remove();
                                
                                // Show success message
                                if (typeof show_toastr === 'function') {
                                    show_toastr('Success', response.message, 'success');
                                } else {
                                    alert(response.message);
                                }
                            });
                        } else {
                            if (typeof show_toastr === 'function') {
                                show_toastr('Error', response.message, 'error');
                            } else {
                                alert(response.message);
                            }
                            button.prop('disabled', false).html('<i class="ti ti-trash"></i>');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Server error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.status === 403) {
                            errorMsg = 'Unauthorized action';
                        }
                        
                        if (typeof show_toastr === 'function') {
                            show_toastr('Error', errorMsg, 'error');
                        } else {
                            alert(errorMsg);
                        }
                        button.prop('disabled', false).html('<i class="ti ti-trash"></i>');
                    }
                });
            });
        });
    </script>
@endpush