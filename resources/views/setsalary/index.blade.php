@extends('layouts.admin')

@section('page-title')
   {{ __('Manage Employee Salary') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Employee Salary') }}</li>
@endsection


@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12 col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th class="text-start">{{ __('Employee Id') }}</th>
                                <th class="text-start">{{ __('Name') }}</th>
                                <th class="text-start">{{ __('Payroll Type') }}</th>
                                <th class="text-start">{{ __('Salary') }}</th>
                                <th class="text-start">{{ __('Net Salary') }}</th>
                                <th class="text-center" width="200px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td class="text-start">
                                        <a href="#" data-url="{{ route('setsalary.popup', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}" 
                                            data-ajax-popup="true" data-title="{{ __('Manage Salary') }}: {{ $employee->full_name }}" 
                                            data-size="xl" class="btn btn-outline-primary">
                                            {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                        </a>
                                    </td>
                                    <td class="text-start">{{ $employee->full_name }}</td>
                                    <td class="text-start">{{ !empty($employee->salary_type()) ? $employee->salary_type() : '-' }}</td>
                                    <td class="text-start">{{ \Auth::user()->priceFormat($employee->salary) }}</td>
                                    <td class="text-start">{{ !empty($employee->get_net_salary()) ? \Auth::user()->priceFormat($employee->get_net_salary()) : '-' }}
                                    </td>
                                    <td class="Action">
                                        <span>
                                            <div class="action-btn bg-warning ms-2">
                                                <a href="#" data-url="{{ route('setsalary.popup', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}" 
                                                    data-ajax-popup="true" data-title="{{ __('Manage Salary') }}: {{ $employee->full_name }}" 
                                                    data-size="xl" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip"
                                                    title="" data-bs-original-title="{{ __('View') }}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            

                                        </span>
                                    </td>
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