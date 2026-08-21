@php
    $plan = Utility::getChatGPTSettings();
@endphp

{{ Form::model($resignation, ['route' => ['resignation.update', $resignation->id], 'method' => 'PUT']) }}
<div class="modal-body">

    @if ($plan->enable_chatgpt == 'on')
    <div class="card-footer text-end">
        <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true" data-url="{{ route('generate', ['resignation']) }}"
            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}"
            data-title="{{ __('Generate Content With AI') }}">
            <i class="fas fa-robot"></i>{{ __(' Generate With AI') }}
        </a>
    </div>
    @endif

    <div class="row">
        @if (\Auth::user()->type != 'employee')
            <div class="form-group col-lg-12">
                {{ Form::label('employee_id', __('Employee'), ['class' => 'col-form-label']) }}
                {{ Form::select('employee_id', $employees, null, ['class' => 'form-control select2', 'required' => 'required']) }}
            </div>
        @endif
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('notice_date', __('Resignation Date'), ['class' => 'col-form-label']) }}
            {{ Form::text('notice_date', null, ['class' => 'form-control d_week','autocomplete'=>'off' , 'required' => 'required']) }}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('resignation_date', __('Last Working Day'), ['class' => 'col-form-label']) }}
            @if (\Auth::user()->type == 'employee')
                {{ Form::text('resignation_date', null, ['class' => 'form-control','autocomplete'=>'off' , 'required' => 'required', 'readonly' => 'readonly']) }}
            @else
                {{ Form::text('resignation_date', null, ['class' => 'form-control d_week','autocomplete'=>'off' , 'required' => 'required']) }}
            @endif
        </div>
        <div class="form-group col-lg-12">
            {{ Form::label('description', __('Reason'), ['class' => 'col-form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Enter Description'),'rows'=>'3' , 'required' => 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>

{{ Form::close() }}

<script>
    $(document).ready(function() {
        var isEmployee = {{ \Auth::user()->type == 'employee' ? 'true' : 'false' }};
        var employeeDoj = {!! json_encode($employeeDoj ?? null) !!};
        var employeeDojMap = {!! json_encode($employeeDojMap ?? []) !!};

        function parseLocalDate(dateStr) {
            var parts = dateStr.split('-');
            if (parts.length === 3) {
                return new Date(parts[0], parts[1] - 1, parts[2]);
            }
            return new Date(dateStr);
        }

        function calculateLastWorkingDate() {
            var resignDateVal = $('#notice_date').val();
            if (!resignDateVal) return;
            
            var joiningDateVal = null;
            if (isEmployee) {
                joiningDateVal = employeeDoj;
            } else {
                var empId = $('#employee_id').val();
                joiningDateVal = employeeDojMap[empId];
            }
            
            if (!joiningDateVal) {
                // Default fallback to 30 days if no joining date is available
                setLastWorkingDate(resignDateVal, 30);
                return;
            }
            
            var resignDate = parseLocalDate(resignDateVal);
            var joiningDate = parseLocalDate(joiningDateVal);
            
            var oneYearLater = new Date(joiningDate);
            oneYearLater.setFullYear(oneYearLater.getFullYear() + 1);
            
            var daysToAdd = 30;
            if (resignDate >= oneYearLater) {
                daysToAdd = 45;
            }
            
            setLastWorkingDate(resignDateVal, daysToAdd);
        }

        function setLastWorkingDate(resignDateVal, days) {
            var resignDate = parseLocalDate(resignDateVal);
            resignDate.setDate(resignDate.getDate() + days);
            
            var year = resignDate.getFullYear();
            var month = (resignDate.getMonth() + 1).toString().padStart(2, '0');
            var day = resignDate.getDate().toString().padStart(2, '0');
            var formattedDate = year + '-' + month + '-' + day;
            
            $('#resignation_date').val(formattedDate);
        }

        // Bind change and input events to keep the resignation date synced
        $(document).on('change changeDate keyup input', '#notice_date', function() {
            if (isEmployee || !$('#resignation_date').val() || $('#resignation_date').attr('data-user-edited') !== 'true') {
                calculateLastWorkingDate();
            }
        });
        
        $(document).on('change', '#employee_id', function() {
            if (isEmployee || !$('#resignation_date').val() || $('#resignation_date').attr('data-user-edited') !== 'true') {
                calculateLastWorkingDate();
            }
        });

        if (!isEmployee) {
            // Track if HR/Admin manually edits the resignation date
            $(document).on('change changeDate keyup input', '#resignation_date', function() {
                $('#resignation_date').attr('data-user-edited', 'true');
            });
        }

        // Initial run
        setTimeout(calculateLastWorkingDate, 100);
    });
</script>
