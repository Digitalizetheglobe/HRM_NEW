@extends('layouts.admin')

@section('page-title')
    {{ __('Review Resignation') }}
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5>{{ __('Resignation Details') }}</h5>
            </div>
            <div class="card-body">
                {{ Form::open(['route' => ['resignation.approve', $resignation->id], 'method' => 'post']) }}
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>{{ __('Employee') }}</label>
                        <input type="text" class="form-control" value="{{ $resignation->employee->full_name }}" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('notice_date', __('Resignation Date'), ['class' => 'form-label']) }}
                        {{ Form::date('notice_date', $resignation->notice_date, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('resignation_date', __('Last Working Day'), ['class' => 'form-label']) }}
                        {{ Form::date('resignation_date', $resignation->resignation_date, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-12">
                        <label>{{ __('Reason') }}</label>
                        <textarea class="form-control" readonly>{{ $resignation->description }}</textarea>
                    </div>
                    <div class="col-md-12 text-end">
                        <a href="{{ route('resignation.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Approve') }}</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection