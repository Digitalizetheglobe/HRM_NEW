@extends('layouts.admin')

@section('page-title')
    {{ __('Loan Details') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('loan.index') }}">{{ __('Loan Management') }}</a></li>
    <li class="breadcrumb-item">{{ __('Loan Details') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Loan Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('Employee') }}:</strong> {{ $loan->employee->full_name }}</p>
                            <p><strong>{{ __('Loan Amount') }}:</strong> {{ \Auth::user()->priceFormat($loan->total_amount) }}</p>
                            <p><strong>{{ __('Monthly EMI') }}:</strong> {{ \Auth::user()->priceFormat($loan->monthly_emi) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('Number of Months') }}:</strong> {{ $loan->number_of_months }}</p>
                            <p><strong>{{ __('Remaining Amount') }}:</strong> 
                                {{ \Auth::user()->priceFormat($loan->remaining_amount) }}
                                <small class="text-muted">
                                    ({{ $loan->deducted_month_count }}/{{ $loan->original_month_count }} months deducted)
                                    @if($loan->extended_months > 0)
                                        <span class="text-info">+{{ $loan->extended_months }} extended</span>
                                    @endif
                                </small>
                            </p>
                            <p><strong>{{ __('Start Month') }}:</strong> {{ \Auth::user()->dateFormat($loan->start_month) }}</p>
                        </div>
                        @if($loan->reason)
                        <div class="col-md-12">
                            <p><strong>{{ __('Reason') }}:</strong> {{ $loan->reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('EMI Schedule') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Month') }}</th>
                                    <th>{{ __('EMI Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Remark') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loan->deductions as $deduction)
                                    <tr>
                                        <td>
                                            {{ \Auth::user()->dateFormat($deduction->month) }}
                                            @php
                                                $startMonth = \Carbon\Carbon::parse($loan->start_month);
                                                $deductionMonth = \Carbon\Carbon::parse($deduction->month);
                                                $originalEndMonth = $startMonth->copy()->addMonths($loan->number_of_months - 1);
                                            @endphp
                                            
                                            @if($deductionMonth > $originalEndMonth)
                                                <span class="badge bg-info ms-2">Extended</span>
                                            @endif
                                        </td>
                                        <td>{{ \Auth::user()->priceFormat($deduction->emi_amount) }}</td>
                                        <td>
                                            @if($deduction->remark === 'No Deduction')
                                                <span class="badge bg-danger">{{ __('No Deduction') }}</span>
                                            @elseif($deduction->is_deducted)
                                                <span class="badge bg-success">{{ __('Deducted') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $deduction->remark ?? '-' }}</td>
                                        <td>
                                            @can('Edit Loan')
                                                <a href="{{ route('loan.deduction.edit', $deduction->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                            @endcan
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