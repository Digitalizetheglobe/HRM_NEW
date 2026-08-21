@extends('layouts.admin')

@section('page-title')
    {{ __('Petrol Allowance') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Petrol Allowance') }}</li>
@endsection

@section('action-button')
    @if (\Auth::user()->type == 'company')
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createPetrolAllowanceModal" data-bs-toggle="tooltip" title="{{ __('Create') }}">
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
                                    <th class="text-start">{{ __('Vehicle Type') }}</th>
                                    <th class="text-start">{{ __('Amount') }}</th>
                                    <th class="text-start">{{ __('Created At') }}</th>
                                    <th class="text-center" width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($petrolAllowances as $petrolAllowance)
                                    <tr>
                                        <td class="text-start">{{ $petrolAllowance->employee->full_name ?? __('N/A') }}</td>
                                        <td class="text-start">{{ \Auth::user()->dateFormat($petrolAllowance->month) }}</td>
                                        <td class="text-start">{{ ucfirst(str_replace('-', ' ', $petrolAllowance->vehicle_type)) }}</td>
                                        <td class="text-start">{{ \Auth::user()->priceFormat($petrolAllowance->amount) }}</td>
                                        <td class="text-start">{{ \Auth::user()->dateFormat($petrolAllowance->created_at) }}</td>
                                        <td class="text-center Action">
                                            <span class="d-flex">
                                                @if (\Auth::user()->type == 'company')
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['petrol-allowance.destroy', $petrolAllowance->id],
                                                        'id' => 'delete-form-' . $petrolAllowance->id,
                                                        'style' => 'display:inline;',
                                                    ]) !!}
                                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('{{ __('Are you sure you want to delete this petrol allowance entry?') }}')">
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

    <!-- Create Petrol Allowance Modal -->
    <div class="modal fade" id="createPetrolAllowanceModal" tabindex="-1" role="dialog" aria-labelledby="createPetrolAllowanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPetrolAllowanceModalLabel">{{ __('Create Petrol Allowance') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createPetrolAllowanceForm">
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
                            <div class="form-group col-md-12">
                                <label class="form-label">{{ __('Months') }} <span class="text-danger">*</span></label>
                                <div class="months-selection-container" style="border: 1px solid #ddd; border-radius: 4px; padding: 15px; max-height: 300px; overflow-y: auto; background-color: #f9f9f9;">
                                    <div class="row g-2">
                                        @php
                                            $currentDate = \Carbon\Carbon::now();
                                            // Generate months: past 12 months, current month, and next 12 months (25 months total)
                                            for ($i = -12; $i <= 12; $i++) {
                                                $date = $currentDate->copy()->addMonths($i);
                                                $monthValue = $date->format('Y-m');
                                                $monthLabel = $date->format('M Y');
                                                $isCurrentMonth = $i == 0;
                                        @endphp
                                        <div class="col-md-3 col-sm-4 col-6">
                                            <div class="form-check">
                                                <input class="form-check-input month-checkbox" type="checkbox" name="months[]" value="{{ $monthValue }}" id="month_{{ $monthValue }}">
                                                <label class="form-check-label" for="month_{{ $monthValue }}" style="cursor: pointer; {{ $isCurrentMonth ? 'font-weight: bold; color: #007bff;' : '' }}">
                                                    {{ $monthLabel }}
                                                    @if($isCurrentMonth)
                                                        <span class="badge bg-primary">Current</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                        @php
                                            }
                                        @endphp
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllMonths">{{ __('Select All') }}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllMonths">{{ __('Clear All') }}</button>
                                    <small class="text-muted d-block mt-2">{{ __('Select one or multiple months (consecutive or non-consecutive)') }}</small>
                                </div>
                                <div id="selectedMonthsDisplay" class="mt-2" style="display: none;">
                                    <strong>{{ __('Selected:') }}</strong> <span id="selectedMonthsText"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Vehicle Type') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="vehicle_type" name="vehicle_type" required>
                                    <option value="">{{ __('Select Vehicle Type') }}</option>
                                    <option value="two-wheeler">{{ __('Two-wheeler') }}</option>
                                    <option value="four-wheeler">{{ __('Four-wheeler') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
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
@endsection

@push('script-page')
<script>
$(document).ready(function() {
    // Initialize Select2 for employee dropdown when modal is shown
    $('#createPetrolAllowanceModal').on('shown.bs.modal', function () {
        // Initialize Select2 for employee if not already initialized
        if (!$('#employee_id').hasClass('select2-hidden-accessible')) {
            $('#employee_id').select2({
                dropdownParent: $('#createPetrolAllowanceModal'),
                theme: 'bootstrap-5',
                placeholder: '{{ __('Select Employee') }}',
                allowClear: true
            });
        }
        
    });
    
    // Handle month checkbox selection
    function updateSelectedMonths() {
        var selected = [];
        $('.month-checkbox:checked').each(function() {
            var label = $(this).next('label').text().trim();
            // Remove "Current" badge text if present
            label = label.replace(/\s*Current\s*/g, '').trim();
            selected.push(label);
        });
        
        if (selected.length > 0) {
            $('#selectedMonthsDisplay').show();
            $('#selectedMonthsText').text(selected.join(', '));
        } else {
            $('#selectedMonthsDisplay').hide();
        }
    }
    
    // Select all months
    $('#selectAllMonths').on('click', function() {
        $('.month-checkbox').prop('checked', true);
        updateSelectedMonths();
    });
    
    // Clear all months
    $('#clearAllMonths').on('click', function() {
        $('.month-checkbox').prop('checked', false);
        updateSelectedMonths();
    });
    
    // Update display when checkbox changes
    $(document).on('change', '.month-checkbox', function() {
        updateSelectedMonths();
    });

    // Handle form submission
    $('#createPetrolAllowanceForm').on('submit', function(e) {
        e.preventDefault();
        
        // Hide previous errors
        $('#formErrors').hide().html('');
        
        // Get selected months from checkboxes
        var selectedMonths = [];
        $('.month-checkbox:checked').each(function() {
            selectedMonths.push($(this).val());
        });
        
        // Get form data
        var formData = {
            employee_id: $('#employee_id').val(),
            months: selectedMonths,
            vehicle_type: $('#vehicle_type').val(),
            amount: $('#amount').val(),
            _token: '{{ csrf_token() }}'
        };

        // Validate form
        if (!formData.employee_id || !formData.months || formData.months.length === 0 || !formData.vehicle_type || !formData.amount) {
            $('#formErrors').html('{{ __("Please fill in all required fields.") }}').show();
            return;
        }

        // Submit via AJAX
        $.ajax({
            url: '{{ route("petrol-allowance.store") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    show_toastr('success', response.success);
                    
                    // Close modal
                    $('#createPetrolAllowanceModal').modal('hide');
                    
                    // Reset form
                    $('#createPetrolAllowanceForm')[0].reset();
                    $('#employee_id').val(null).trigger('change');
                    $('#months').val(null).trigger('change');
                    
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

    // Reset form when modal is closed
    $('#createPetrolAllowanceModal').on('hidden.bs.modal', function () {
        $('#createPetrolAllowanceForm')[0].reset();
        $('#employee_id').val(null).trigger('change');
        $('.month-checkbox').prop('checked', false);
        $('#selectedMonthsDisplay').hide();
        $('#formErrors').hide().html('');
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
            min-width: 140px; /* Vehicle Type */
        }
        
        .datatable th:nth-child(4) {
            min-width: 120px; /* Amount */
        }
        
        .datatable th:nth-child(5) {
            min-width: 140px; /* Created At */
        }
        
        .datatable th:nth-child(6) {
            min-width: 200px; /* Action */
        }
    </style>
@endpush

