@extends('layouts.admin')

@section('page-title')
    {{ __('Salary Arrears') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Salary Arrears') }}</li>
@endsection

@section('action-button')
    @if (\Auth::user()->type == 'company')
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createArrearsModal" data-bs-toggle="tooltip" title="{{ __('Create') }}">
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
                                    <th class="text-start">{{ __('Arrears Month') }}</th>
                                    <th class="text-start">{{ __('Payment Month') }}</th>
                                    <th class="text-start">{{ __('Amount') }}</th>
                                    <th class="text-start">{{ __('Created At') }}</th>
                                    <th class="text-center" width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($arrears as $arrear)
                                    <tr>
                                        <td class="text-start">{{ $arrear->employee->full_name ?? __('N/A') }}</td>
                                        <td class="text-start">{{ \Auth::user()->dateFormat($arrear->arrears_month) }}</td>
                                        <td class="text-start">{{ \Auth::user()->dateFormat($arrear->payment_month) }}</td>
                                        <td class="text-start">{{ \Auth::user()->priceFormat($arrear->amount) }}</td>
                                        <td class="text-start">{{ \Auth::user()->dateFormat($arrear->created_at) }}</td>
                                        <td class="text-center Action">
                                            <span class="d-flex">
                                                @if (\Auth::user()->type == 'company')
                                                    <a href="#" class="btn btn-sm btn-warning me-2" data-bs-toggle="tooltip" title="Edit" onclick="editArrears({{ $arrear->id }})">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['salary-arrears.destroy', $arrear->id],
                                                        'id' => 'delete-form-' . $arrear->id,
                                                        'style' => 'display:inline;',
                                                    ]) !!}
                                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('{{ __('Are you sure you want to delete this salary arrears entry?') }}')">
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

    <!-- Create Arrears Modal -->
    <div class="modal fade" id="createArrearsModal" tabindex="-1" role="dialog" aria-labelledby="createArrearsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createArrearsModalLabel">{{ __('Create Salary Arrears') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createArrearsForm">
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
                                <label class="form-label">{{ __('Arrears Month') }} <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" id="arrears_month" name="arrears_month" required>
                                <small class="text-muted">{{ __('The month for which salary was pending') }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Payment Month') }} <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" id="payment_month" name="payment_month" required>
                                <small class="text-muted">{{ __('The month in which arrears will be paid') }}</small>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ \Auth::user()->currencySymbol() }}</span>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required placeholder="{{ __('Enter amount') }}">
                                </div>
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

    <!-- Edit Arrears Modal -->
    <div class="modal fade" id="editArrearsModal" tabindex="-1" role="dialog" aria-labelledby="editArrearsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editArrearsModalLabel">{{ __('Edit Salary Arrears') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editArrearsForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_arrears_id" name="arrears_id">
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
                                <label class="form-label">{{ __('Arrears Month') }} <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" id="edit_arrears_month" name="arrears_month" required>
                                <small class="text-muted">{{ __('The month for which salary was pending') }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Payment Month') }} <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" id="edit_payment_month" name="payment_month" required>
                                <small class="text-muted">{{ __('The month in which arrears will be paid') }}</small>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ \Auth::user()->currencySymbol() }}</span>
                                    <input type="number" class="form-control" id="edit_amount" name="amount" step="0.01" min="0.01" required placeholder="{{ __('Enter amount') }}">
                                </div>
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
    $('#createArrearsModal').on('shown.bs.modal', function () {
        // Initialize Select2 if not already initialized
        if (!$('#employee_id').hasClass('select2-hidden-accessible')) {
            $('#employee_id').select2({
                dropdownParent: $('#createArrearsModal'),
                theme: 'bootstrap-5',
                placeholder: '{{ __('Select Employee') }}',
                allowClear: true
            });
        }
    });

    // Initialize Select2 for employee dropdown when edit modal is shown
    $('#editArrearsModal').on('shown.bs.modal', function () {
        // Initialize Select2 if not already initialized
        if (!$('#edit_employee_id').hasClass('select2-hidden-accessible')) {
            $('#edit_employee_id').select2({
                dropdownParent: $('#editArrearsModal'),
                theme: 'bootstrap-5',
                placeholder: '{{ __('Select Employee') }}',
                allowClear: true
            });
        }
    });

    // Function to edit arrears
    window.editArrears = function(id) {
        // Show loading state
        $('#editFormErrors').hide().html('');
        
        // Fetch arrears data
        $.ajax({
            url: '{{ route("salary-arrears.edit", "") }}/' + id,
            method: 'GET',
            success: function(response) {
                if (response.arrears) {
                    // Populate form fields
                    $('#edit_arrears_id').val(response.arrears.id);
                    $('#edit_employee_id').val(response.arrears.employee_id).trigger('change');
                    $('#edit_arrears_month').val(response.arrears.arrears_month);
                    $('#edit_payment_month').val(response.arrears.payment_month);
                    $('#edit_amount').val(response.arrears.amount);
                    
                    // Show modal
                    $('#editArrearsModal').modal('show');
                }
            },
            error: function(xhr) {
                var errorMsg = '{{ __("Failed to load salary arrears data.") }}';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                show_toastr('error', errorMsg);
            }
        });
    };

    // Handle form submission
    $('#createArrearsForm').on('submit', function(e) {
        e.preventDefault();
        
        // Hide previous errors
        $('#formErrors').hide().html('');
        
        // Get form data
        var formData = {
            employee_id: $('#employee_id').val(),
            arrears_month: $('#arrears_month').val(),
            payment_month: $('#payment_month').val(),
            amount: $('#amount').val(),
            _token: '{{ csrf_token() }}'
        };

        // Validate form
        if (!formData.employee_id || !formData.arrears_month || !formData.payment_month || !formData.amount) {
            $('#formErrors').html('{{ __("Please fill in all required fields.") }}').show();
            return;
        }

        // Submit via AJAX
        $.ajax({
            url: '{{ route("salary-arrears.store") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    show_toastr('success', response.success);
                    
                    // Close modal
                    $('#createArrearsModal').modal('hide');
                    
                    // Reset form
                    $('#createArrearsForm')[0].reset();
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
    $('#editArrearsForm').on('submit', function(e) {
        e.preventDefault();
        
        // Hide previous errors
        $('#editFormErrors').hide().html('');
        
        var arrearsId = $('#edit_arrears_id').val();
        
        // Get form data
        var formData = {
            employee_id: $('#edit_employee_id').val(),
            arrears_month: $('#edit_arrears_month').val(),
            payment_month: $('#edit_payment_month').val(),
            amount: $('#edit_amount').val(),
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        // Validate form
        if (!formData.employee_id || !formData.arrears_month || !formData.payment_month || !formData.amount) {
            $('#editFormErrors').html('{{ __("Please fill in all required fields.") }}').show();
            return;
        }

        // Submit via AJAX
        $.ajax({
            url: '{{ route("salary-arrears.update", "") }}/' + arrearsId,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    show_toastr('success', response.success);
                    
                    // Close modal
                    $('#editArrearsModal').modal('hide');
                    
                    // Reset form
                    $('#editArrearsForm')[0].reset();
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
    $('#createArrearsModal').on('hidden.bs.modal', function () {
        $('#createArrearsForm')[0].reset();
        if ($('#employee_id').hasClass('select2-hidden-accessible')) {
            $('#employee_id').val(null).trigger('change');
        }
        $('#formErrors').hide().html('');
    });

    // Reset form when edit modal is closed
    $('#editArrearsModal').on('hidden.bs.modal', function () {
        $('#editArrearsForm')[0].reset();
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
        
        /* Enhanced table styling */
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
        
        /* Fix DataTables sorting icons alignment */
        .dataTables_wrapper .dataTables_scrollHead .table th {
            position: relative;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting:after,
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_asc:after,
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_desc:after {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: 8px !important;
            margin-top: 0 !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_asc:after {
            content: "·" !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_desc:after {
            content: "·" !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting:after {
            content: "·" !important;
            opacity: 0.3;
        }
        
        /* Ensure proper column width alignment */
        .datatable th {
            min-width: 120px;
        }
        
        .datatable th:nth-child(1) {
            min-width: 120px; /* Employee */
        }
        
        .datatable th:nth-child(2) {
            min-width: 160px; /* Arrears Month */
        }
        
        .datatable th:nth-child(3) {
            min-width: 180px; /* Payment Month */
        }
        
        .datatable th:nth-child(4) {
            min-width: 140px; /* Amount */
        }
        
        .datatable th:nth-child(5) {
            min-width: 160px; /* Created At */
        }
        
        .datatable th:nth-child(6) {
            min-width: 220px; /* Action */
        }
    </style>
    
    <!-- <script>
        $(document).ready(function() {
            // Initialize DataTables with proper configuration
            $('.datatable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    emptyTable: "No salary arrears records found"
                },
                autoWidth: false
            });
        });
    </script> -->
@endpush

