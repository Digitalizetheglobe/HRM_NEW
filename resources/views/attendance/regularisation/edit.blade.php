{{ Form::model($regularisation, ['route' => ['attendance-regularisation.update', $regularisation->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('missed_attendance_date', __('Missed Attendance Date'), ['class' => 'col-form-label']) }}
            {{ Form::date('missed_attendance_date', $regularisation->missed_attendance_date, ['class' => 'form-control', 'required' => 'required', 'max' => date('Y-m-d')]) }}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('punch_in_time', __('Punch In Time'), ['class' => 'col-form-label']) }}
            {{ Form::time('punch_in_time', date('H:i', strtotime($regularisation->punch_in_time)), ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('punch_out_time', __('Punch Out Time'), ['class' => 'col-form-label']) }}
            {{ Form::time('punch_out_time', date('H:i', strtotime($regularisation->punch_out_time)), ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('reason', __('Reason'), ['class' => 'col-form-label']) }}
            {{ Form::select('reason', ['Missed Punch' => __('Missed Punch'), 'Technical Error' => __('Technical Error'), 'Others' => __('Others')], $regularisation->reason, ['class' => 'form-control select2', 'required' => 'required']) }}
        </div>
        <div class="form-group col-lg-12 col-md-12">
            {{ Form::label('remark', __('Remark'), ['class' => 'col-form-label']) }}
            {{ Form::textarea('remark', $regularisation->remark, ['class' => 'form-control', 'rows' => 3, 'required' => 'required', 'placeholder' => __('Enter remark')]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn btn-primary']) }}
</div>
{{ Form::close() }}

<script>
    $(document).ready(function() {
        // Validate punch out time is after punch in time
        $('form').on('submit', function(e) {
            var punchIn = $('input[name="punch_in_time"]').val();
            var punchOut = $('input[name="punch_out_time"]').val();
            
            if (punchIn && punchOut) {
                if (punchOut <= punchIn) {
                    e.preventDefault();
                    alert('{{ __('Punch Out Time must be after Punch In Time.') }}');
                    return false;
                }
            }
        });
    });
</script>










