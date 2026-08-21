{{ Form::open(['route' => 'attendance-regularisation.store', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        @if (\Auth::user()->type == 'company')
            <div class="form-group col-lg-12 col-md-12">
                {{ Form::label('employee_id', __('Employee'), ['class' => 'col-form-label']) }}
                <select name="employee_id" id="employee_id" class="form-control employee-select" required>
                    <option value="">{{ __('Select Employee') }}</option>
                </select>
            </div>
        @endif
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('missed_attendance_date', __('Missed Attendance Date'), ['class' => 'col-form-label']) }}
            {{ Form::date('missed_attendance_date', null, ['class' => 'form-control', 'required' => 'required', 'max' => date('Y-m-d')]) }}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('punch_in_time', __('Punch In Time'), ['class' => 'col-form-label']) }}
            {{ Form::time('punch_in_time', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('punch_out_time', __('Punch Out Time'), ['class' => 'col-form-label']) }}
            {{ Form::time('punch_out_time', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('reason', __('Reason'), ['class' => 'col-form-label']) }}
            {{ Form::select('reason', ['Missed Punch' => __('Missed Punch'), 'Technical Error' => __('Technical Error'), 'Others' => __('Others')], null, ['class' => 'form-control select2', 'required' => 'required', 'placeholder' => __('Select Reason')]) }}
        </div>
        <div class="form-group col-lg-12 col-md-12">
            {{ Form::label('remark', __('Remark'), ['class' => 'col-form-label']) }}
            {{ Form::textarea('remark', null, ['class' => 'form-control', 'rows' => 3, 'required' => 'required', 'placeholder' => __('Enter remark')]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
</div>
{{ Form::close() }}

<script>
    @if (\Auth::user()->type == 'company')
    // Initialize Select2 when commonModal is shown (for AJAX-loaded modals)
    $(document).on('shown.bs.modal', '#commonModal', function() {
        // Small delay to ensure DOM is ready
        setTimeout(function() {
            // Check if employee_id field exists in this modal
            var $employeeSelect = $('#employee_id');
            if ($employeeSelect.length > 0) {
                // Check if Select2 is available
                if (typeof $.fn.select2 === 'undefined') {
                    console.error('Select2 library is not loaded');
                    // Fallback: Load employees via regular AJAX and populate dropdown
                    $.ajax({
                        url: "{{ route('attendance-regularisation.getEmployees') }}",
                        type: 'GET',
                        dataType: 'json',
                        data: { search: '' },
                        success: function(data) {
                            if (Array.isArray(data)) {
                                $employeeSelect.empty().append('<option value="">{{ __('Select Employee') }}</option>');
                                data.forEach(function(item) {
                                    $employeeSelect.append('<option value="' + item.id + '">' + item.text + '</option>');
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading employees:', xhr);
                        }
                    });
                    return;
                }
                
                // Destroy any existing Select2 or Choices initialization
                if ($employeeSelect.hasClass('select2-hidden-accessible')) {
                    $employeeSelect.select2('destroy');
                }
                if ($employeeSelect.data('choices')) {
                    $employeeSelect.data('choices').destroy();
                }
                
                // Initialize Select2 for employee dropdown with AJAX search
                $employeeSelect.select2({
                    dropdownParent: $('#commonModal'),
                    placeholder: "{{ __('Search Employee...') }}",
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: "{{ route('attendance-regularisation.getEmployees') }}",
                        type: 'GET',
                        dataType: 'json',
                        delay: 250,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        data: function (params) {
                            return {
                                search: params.term || '',
                                page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            // Check if data is an array
                            if (Array.isArray(data) && data.length > 0) {
                                return {
                                    results: data.map(function(item) {
                                        return {
                                            id: item.id,
                                            text: item.text
                                        };
                                    })
                                };
                            } else if (data && data.error) {
                                console.error('Server error:', data.error);
                                return {
                                    results: []
                                };
                            } else {
                                return {
                                    results: []
                                };
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error loading employees:', {
                                status: status,
                                error: error,
                                response: xhr.responseText,
                                url: "{{ route('attendance-regularisation.getEmployees') }}"
                            });
                            return {
                                results: []
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0
                });
            }
        }, 200);
    });
    @endif

    // Validate punch out time is after punch in time
    $(document).on('submit', 'form', function(e) {
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
</script>


