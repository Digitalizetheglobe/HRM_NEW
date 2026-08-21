@php
    $setting = App\Models\Utility::settings();
@endphp

{!! Form::open(['route' => ['salary-increment.store', $employee->id], 'method' => 'POST']) !!}
<div class="modal-body">
    <div class="row">

        <!-- Old Salary (Display only) -->
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('old_salary', __('Old Salary'), ['class' => 'form-label']) }}
                <input type="text" class="form-control" value="{{ $employee->salary }}" readonly>
            </div>
        </div>

        <!-- New Salary -->
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('new_salary', __('New Salary'), ['class' => 'form-label']) }}
                {{ Form::number('new_salary', null, ['class' => 'form-control', 'required' => true, 'placeholder' => __('Enter New Salary')]) }}
            </div>
        </div>

        <!-- Month of Effective Date -->
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('month_of_effective_date', __('Month of Effective Date'), ['class' => 'form-label']) }}
                {{ Form::month('month_of_effective_date', null, ['class' => 'form-control', 'required' => true]) }}
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <a href="{{ route('setsalary.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
    {{ Form::submit(__('Save Increment'), ['class' => 'btn btn-primary']) }}
</div>
{!! Form::close() !!}
