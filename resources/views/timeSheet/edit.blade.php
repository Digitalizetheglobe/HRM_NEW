{{ Form::model($timeSheet, ['route' => ['timesheet.update', $timeSheet->id], 'method' => 'PUT', 'id' => 'timesheet-form', 'class' => 'needs-validation', 'novalidate' => 'novalidate']) }}
@csrf

<div class="modal-body px-50">
    <div class="row">
        @if (\Auth::user()->type != 'employee')
            <div class="form-group col-md-12">
                {{ Form::label('employee_id', __('Employee'), ['class' => 'col-form-label']) }}
                {!! Form::select('employee_id', $employees, null, [
                    'class' => 'form-control select2',
                    'id' => 'choices-multiple',
                    'required' => 'required',
                ]) !!}
            </div>
        @endif

        <div class="form-group col-md-6">
            {{ Form::label('date', __('Date'), ['class' => 'col-form-label']) }}
            {{ Form::date('date', $timeSheet->date, ['class' => 'form-control', 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('hours', __('Hours'), ['class' => 'col-form-label']) }}
            {{ Form::number('hours', $timeSheet->hours, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'min' => '0', 'max' => '24']) }}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('remark', __('Remark'), ['class' => 'col-form-label']) }}
            {{ Form::textarea('remark', $timeSheet->remark, ['class' => 'form-control', 'required' => 'required', 'rows' => 3]) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}