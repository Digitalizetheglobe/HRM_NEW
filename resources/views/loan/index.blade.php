@extends('layouts.admin')

@section('page-title')
    {{ __('Loan Management') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Loan Management') }}</li>
@endsection

@section('action-button')
    @can('Create Employee')
        <a href="{{ route('loan.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable mt-10">
                            <thead>
                                <tr>
                                    <th class="text-start">{{ __('Employee') }}</th>
                                    <th class="text-start">{{ __('Loan Amount') }}</th>
                                    <th class="text-start">{{ __('Monthly EMI') }}</th>
                                    <th class="text-start">{{ __('Months') }}</th>
                                    <th class="text-start">{{ __('Remaining') }}</th>
                                    <th class="text-start">{{ __('Start Month') }}</th>
                                    <th class="text-center" width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loans as $loan)
                                    <tr>
                                        <td class="text-start">{{ $loan->employee->full_name }}</td>
                                        <td class="text-start">{{ \Auth::user()->priceFormat($loan->total_amount) }}</td>
                                        <td class="text-start">{{ \Auth::user()->priceFormat($loan->monthly_emi) }}</td>
                                        <td class="text-start">{{ $loan->number_of_months }}</td>
                                        <td class="text-start {{ $loan->remaining_amount > 0 ? 'text-warning' : 'text-success' }}">
                                            {{ \Auth::user()->priceFormat($loan->remaining_amount) }}
                                        </td>
                                        <td class="text-start">{{ \Auth::user()->dateFormat($loan->start_month) }}</td>
                                        <td class="text-center Action">
                                            <span class="d-flex">
                                                @can('Show Employee')
                                                    <a href="{{ route('loan.show', $loan->id) }}" class="btn btn-sm btn-warning me-2" data-bs-toggle="tooltip" title="View">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                @endcan

                                                @can('Delete Employee')
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['loan.destroy', $loan->id],
                                                        'id' => 'delete-form-' . $loan->id,
                                                        'style' => 'display:inline;',
                                                    ]) !!}
                                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </button>
                                                    {!! Form::close() !!}
                                                @endcan
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

@push('scripts')
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
        
        /* Ensure proper column width alignment */
        .datatable th:nth-child(1) {
            min-width: 220px; /* Employee - increased */
        }
        
        .datatable th:nth-child(2) {
            min-width: 180px; /* Loan Amount - increased */
        }
        
        .datatable th:nth-child(3) {
            min-width: 160px; /* Monthly EMI - increased */
        }
        
        .datatable th:nth-child(4) {
            min-width: 120px; /* Months - increased */
        }
        
        .datatable th:nth-child(5) {
            min-width: 160px; /* Remaining - increased */
        }
        
        .datatable th:nth-child(6) {
            min-width: 150px; /* Start Month - increased */
        }
        
        .datatable th:nth-child(7) {
            min-width: 220px; /* Action - increased */
        }
    </style>
@endpush