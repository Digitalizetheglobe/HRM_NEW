{{ Form::open(['url' => 'attendanceemployee', 'method' => 'post']) }}
<div class="card-body p-0">
    <div class="row">
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('employee_id', __('Employee'), ['class' => 'col-form-label']) }}
            {{ Form::select('employee_id', $employees, null, ['class' => 'form-control select2', 'id' => 'employee_id']) }}
            <div id="single-punch-alert" class="alert alert-warning mt-2" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i> <strong>{{ __('Single Punch In') }}</strong> - {{ __('This employee has only clocked in without clocking out.') }}
            </div>
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('date', __('Date'), ['class' => 'col-form-label']) }}
            {{ Form::text('date', null, ['class' => 'form-control d_week','autocomplete'=>'off']) }}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('clock_in', __('Clock In'), ['class' => 'col-form-label']) }}
            {{ Form::text('clock_in', null, ['class' => 'form-control timepicker']) }}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('clock_out', __('Clock Out'), ['class' => 'col-form-label']) }}
            {{ Form::text('clock_out', null, ['class' => 'form-control timepicker']) }}
        </div>
    </div>
</div>
<div class="modal-footer pr-0">
    <button type="button" class="btn dark btn-outline" data-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
</div>
{{ Form::close() }}

<script>
    // Single punch employees list
    var singlePunchEmployees = @json($singlePunchEmployees ?? []);
    
    // Check for single punch when employee is selected
    $(document).ready(function() {
        $('#employee_id').on('change', function() {
            var employeeId = $(this).val();
            var alertDiv = $('#single-punch-alert');
            
            if (employeeId && singlePunchEmployees.includes(parseInt(employeeId))) {
                alertDiv.show();
            } else {
                alertDiv.hide();
            }
        });
        
        // Trigger on page load if employee is pre-selected
        if ($('#employee_id').val()) {
            $('#employee_id').trigger('change');
        }
    });
</script>
