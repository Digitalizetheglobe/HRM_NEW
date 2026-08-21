@extends('layouts.admin')

@section('page-title')
    {{ __('Create Loan') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('loan.index') }}">{{ __('Loan Management') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create Loan') }}</li>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('loan.store') }}">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Employee') }}</label>
                            <select class="form-control select" required="required" name="employee_id">
                                <option value="">{{ __('Select Employee') }}</option>
                                @foreach ($employees as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Loan Amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ \Auth::user()->currencySymbol() }}</span>
                                <input type="number" class="form-control" name="total_amount" required placeholder="{{ __('Loan Amount') }}">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Number of Months') }}</label>
                            <input type="number" class="form-control" name="number_of_months" required placeholder="{{ __('Number of Months') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Start Month') }}</label>
                            <input type="month" class="form-control" name="start_month" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="form-label">{{ __('Reason (Optional)') }}</label>
                            <textarea class="form-control" name="reason" rows="3" placeholder="{{ __('Reason for loan') }}"></textarea>
                        </div>
                        <div class="col-12">
                            <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection