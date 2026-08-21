@extends('layouts.admin')

@section('page-title')
    {{ __('Other Deduction') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Other Deduction') }}</li>
@endsection

@section('action-button')
    @if (\Auth::user()->type == 'company')
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createDeductionModal" data-bs-toggle="tooltip" title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable mt-10">
                            <thead>
                                <tr>
                                    <th class="text-start">{{ __('Employee') }}</th>
                                    <th class="text-start">{{ __('Month') }}</th>
                                    <th class="text-start">{{ __('Amount') }}</th>
                                    <th class="text-start">{{ __('Remark') }}</th>
                                    <th class="text-start">{{ __('Created At') }}</th>
                                    <th class="text-center" width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deductions as $deduction)
                                    <tr>
                                        <td class="text-start">{{ $deduction->employee->full_name ?? __('N/A') }}</td>
                                        <td class="text-start">{{ \Auth::user()->dateFormat($deduction->month) }}</td>
                                        <td class="text-start">{{ \Auth::user()->priceFormat($deduction->amount) }}</td>
                                        <td class="text-start">{{ $deduction->remark ?? '-' }}</td>
                                        <td class="text-start">{{ \Auth::user()->dateFormat($deduction->created_at) }}</td>
                                        <td class="text-center Action">
                                            <span class="d-flex">
                                                @if (\Auth::user()->type == 'company')
                                                    <a href="#" class="btn btn-sm btn-warning me-2" data-bs-toggle="tooltip" title="Edit" onclick="editDeduction({{ $deduction->id }})">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['other-deduction.destroy', $deduction->id],
                                                        'id' => 'delete-form-' . $deduction->id,
                                                        'style' => 'display:inline;',
                                                    ]) !!}
                                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('{{ __('Are you sure you want to delete this other deduction entry?') }}')">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </button>
                                                    {!! Form::close() !!}
                                                @endif
                                            </span>
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

    <!-- Create Deduction Modal -->
    <div class="modal fade" id="createDeductionModal" tabindex="-1" role="dialog" aria-labelledby="createDeductionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createDeductionModalLabel">{{ __('Create Other Deduction') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createDeductionForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label">{{ __('Employee') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="employee_id" name="employee_id" required>
                                    <option value="">{{ __('Select Employee') }}</option>
                                    @foreach($employees as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ __('Select an employee') }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Month') }} <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" id="month" name="month" required>
                                <small class="text-muted">{{ __('Select the month for deduction') }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ \Auth::user()->currencySymbol() }}</span>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required placeholder="{{ __('Enter amount') }}">
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">{{ __('Remark') }}</label>
                                <textarea class="form-control" id="remark" name="remark" rows="3" placeholder="{{ __('Enter remark (optional)') }}"></textarea>
                                <small class="text-muted">{{ __('Optional remark for this deduction') }}</small>
                            </div>
                        </div>
                        <div id="formErrors" class="alert alert-danger" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Deduction Modal -->
    <div class="modal fade" id="editDeductionModal" tabindex="-1" role="dialog" aria-labelledby="editDeductionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDeductionModalLabel">{{ __('Edit Other Deduction') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editDeductionForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_deduction_id" name="deduction_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label">{{ __('Employee') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="edit_employee_id" name="employee_id" required>
                                    <option value="">{{ __('Select Employee') }}</option>
                                    @foreach($employees as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ __('Select an employee') }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Month') }} <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" id="edit_month" name="month" required>
                                <small class="text-muted">{{ __('Select the month for deduction') }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ \Auth::user()->currencySymbol() }}</span>
                                    <input type="number" class="form-control" id="edit_amount" name="amount" step="0.01" min="0.01" required placeholder="{{ __('Enter amount') }}">
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">{{ __('Remark') }}</label>
                                <textarea class="form-control" id="edit_remark" name="remark" rows="3" placeholder="{{ __('Enter remark (optional)') }}"></textarea>
                                <small class="text-muted">{{ __('Optional remark for this deduction') }}</small>
                            </div>
                        </div>
                        <div id="editFormErrors" class="alert alert-danger" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
$(document).ready(function() {
    // Initialize Select2 for employee dropdown when create modal is shown
    $('#createDeductionModal').on('shown.bs.modal', function () {
        // Initialize Select2 if not already initialized
        if (!$('#employee_id').hasClass('select2-hidden-accessible')) {
            $('#employee_id').select2({
                dropdownParent: $('#createDeductionModal'),
                theme: 'bootstrap-5',
                placeholder: '{{ __('Select Employee') }}',
                allowClear: true
            });
        }
    });

    // Initialize Select2 for employee dropdown when edit modal is shown
    $('#editDeductionModal').on('shown.bs.modal', function () {
        // Initialize Select2 if not already initialized
        if (!$('#edit_employee_id').hasClass('select2-hidden-accessible')) {
            $('#edit_employee_id').select2({
                dropdownParent: $('#editDeductionModal'),
                theme: 'bootstrap-5',
                placeholder: '{{ __('Select Employee') }}',
                allowClear: true
            });
        }
    });

    // Function to edit deduction
    window.editDeduction = function(id) {
        // Show loading state
        $('#editFormErrors').hide().html('');
        
        // Fetch deduction data
        $.ajax({
            url: '{{ route("other-deduction.edit", "") }}/' + id,
            method: 'GET',
            success: function(response) {
                if (response.deduction) {
                    // Populate form fields
                    $('#edit_deduction_id').val(response.deduction.id);
                    $('#edit_employee_id').val(response.deduction.employee_id).trigger('change');
                    $('#edit_month').val(response.deduction.month);
                    $('#edit_amount').val(response.deduction.amount);
                    $('#edit_remark').val(response.deduction.remark || '');
                    
                    // Show modal
                    $('#editDeductionModal').modal('show');
                }
            },
            error: function(xhr) {
                var errorMsg = '{{ __("Failed to load other deduction data.") }}';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                show_toastr('error', errorMsg);
            }
        });
    };

    // Handle form submission
    $('#createDeductionForm').on('submit', function(e) {
        e.preventDefault();
        
        // Hide previous errors
        $('#formErrors').hide().html('');
        
        // Get form data
        var formData = {
            employee_id: $('#employee_id').val(),
            month: $('#month').val(),
            amount: $('#amount').val(),
            remark: $('#remark').val(),
            _token: '{{ csrf_token() }}'
        };

        // Validate form
        if (!formData.employee_id || !formData.month || !formData.amount) {
            $('#formErrors').html('{{ __("Please fill in all required fields.") }}').show();
            return;
        }

        // Submit via AJAX
        $.ajax({
            url: '{{ route("other-deduction.store") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    show_toastr('success', response.success);
                    
                    // Close modal
                    $('#createDeductionModal').modal('hide');
                    
                    // Reset form
                    $('#createDeductionForm')[0].reset();
                    $('#employee_id').val(null).trigger('change');
                    
                    // Reload page after a short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                var errors = '';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errors = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errors += value[0] + '<br>';
                    });
                } else {
                    errors = '{{ __("Something went wrong. Please try again.") }}';
                }
                $('#formErrors').html(errors).show();
            }
        });
    });

    // Handle edit form submission
    $('#editDeductionForm').on('submit', function(e) {
        e.preventDefault();
        
        // Hide previous errors
        $('#editFormErrors').hide().html('');
        
        var deductionId = $('#edit_deduction_id').val();
        
        // Get form data
        var formData = {
            employee_id: $('#edit_employee_id').val(),
            month: $('#edit_month').val(),
            amount: $('#edit_amount').val(),
            remark: $('#edit_remark').val(),
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        // Validate form
        if (!formData.employee_id || !formData.month || !formData.amount) {
            $('#editFormErrors').html('{{ __("Please fill in all required fields.") }}').show();
            return;
        }

        // Submit via AJAX
        $.ajax({
            url: '{{ route("other-deduction.update", "") }}/' + deductionId,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    show_toastr('success', response.success);
                    
                    // Close modal
                    $('#editDeductionModal').modal('hide');
                    
                    // Reset form
                    $('#editDeductionForm')[0].reset();
                    $('#edit_employee_id').val(null).trigger('change');
                    
                    // Reload page after a short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                var errors = '';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errors = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errors += value[0] + '<br>';
                    });
                } else {
                    errors = '{{ __("Something went wrong. Please try again.") }}';
                }
                $('#editFormErrors').html(errors).show();
            }
        });
    });

    // Reset form when create modal is closed
    $('#createDeductionModal').on('hidden.bs.modal', function () {
        $('#createDeductionForm')[0].reset();
        if ($('#employee_id').hasClass('select2-hidden-accessible')) {
            $('#employee_id').val(null).trigger('change');
        }
        $('#formErrors').hide().html('');
    });

    // Reset form when edit modal is closed
    $('#editDeductionModal').on('hidden.bs.modal', function () {
        $('#editDeductionForm')[0].reset();
        if ($('#edit_employee_id').hasClass('select2-hidden-accessible')) {
            $('#edit_employee_id').val(null).trigger('change');
        }
        $('#editFormErrors').hide().html('');
    });
});
</script>
@endpush

@push('scripts')
    <style>
        .table th {
            white-space: nowrap;
            text-align: left !important;
            vertical-align: middle !important;
            padding-right: 25px !important;
            position: relative;
        }
        
        .table td {
            vertical-align: middle !important;
        }
        
        /* Ensure proper column width alignment */
        .datatable th:nth-child(1) {
            min-width: 180px; /* Employee */
        }
        
        .datatable th:nth-child(2) {
            min-width: 120px; /* Month */
        }
        
        .datatable th:nth-child(3) {
            min-width: 120px; /* Amount */
        }
        
        .datatable th:nth-child(4) {
            min-width: 200px; /* Remark */
        }
        
        .datatable th:nth-child(5) {
            min-width: 140px; /* Created At */
        }
        
        .datatable th:nth-child(6) {
            min-width: 200px; /* Action */
        }
    </style>
@endpush






