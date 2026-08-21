@extends('layouts.admin')

@section('page-title')
    {{ __('Edit Deduction Schedule') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('loan.index') }}">{{ __('Loan Management') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit Deduction') }}</li>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('loan.deduction.update', ['deduction' => $deduction->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Month') }}</label>
                            <input type="text" class="form-control" value="{{ \Auth::user()->dateFormat($deduction->month) }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('EMI Amount') }}</label>
                            <input type="text" class="form-control" value="{{ \Auth::user()->priceFormat($deduction->emi_amount) }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Deduct this month?') }}</label>
                            <select class="form-control" name="is_deducted" required>
                                <option value="1" {{ $deduction->is_deducted ? 'selected' : '' }}>
                                    {{ __('Yes, deduct as scheduled') }}
                                </option>
                                <option value="0" 
                                    {{ $deduction->remark === 'No Deduction' ? 'disabled selected' : '' }}
                                    {{ !$deduction->is_deducted && $deduction->remark !== 'No Deduction' ? 'selected' : '' }}>
                                    {{ $deduction->remark === 'No Deduction' ? 'Already marked as No Deduction' : __('No, defer this deduction') }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Remark') }}</label>
                            <input type="text" class="form-control" name="remark" value="{{ $deduction->remark }}" placeholder="{{ __('Reason for deferral') }}">
                        </div>
                        <div class="col-12">
                            <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
                            <a href="{{ route('loan.show', $deduction->loan_id) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
