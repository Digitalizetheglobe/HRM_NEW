@extends('layouts.admin')

@section('page-title')
    {{ __('Unit List') }} 
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Unit List') }}</li>
@endsection

@section('action-button')
    @if(Auth::user()->type != 'hr')
        @can('Create Employee')
            @unless(auth()->user()->hasRole('HR'))
                <a href="#" data-url="{{ route('units.create') }}" data-ajax-popup="true"
                    data-title="{{ __('Add New Unit') }}" data-size="lg" data-bs-toggle="tooltip" title="Create"
                    class="btn btn-sm btn-primary">
                    <i class="ti ti-plus"></i>
                </a>
            @endunless
        @endcan
    @endif
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">

                

                
                <!-- Success Message -->
                @if(session('success'))
                <div class="alert alert-success mb-3">
                    {{ session('success') }}
                </div>
                @endif
                
                <form method="GET" action="{{ route('units.index') }}" class="mb-4">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="project_id" class="form-label">{{ __('Select Project') }}</label>
                                <select class="form-select" id="project_id" name="project_id" onchange="this.form.submit()">
                                    <option value="">{{ __('All Projects') }}</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->project_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                
                @if(count($units) > 0 && Auth::user()->can('Delete Employee'))
                <div class="bulk-actions mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                        <label class="form-check-label ms-2" for="selectAll">{{ __('Select All') }}</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger ms-3" id="bulkDeleteBtn" disabled>
                        <i class="ti ti-trash"></i> {{ __('Delete Selected') }} (<span id="selectedCount">0</span>)
                    </button>

                </div>
                @endif
                
                <!-- Bulk Delete Confirmation Modal -->
                <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="bulkDeleteModalLabel">{{ __('Confirm Bulk Delete') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>{{ __('Are you sure you want to delete the selected units?') }}</p>
                                <p class="text-danger">{{ __('This action cannot be undone.') }}</p>
                                <div id="selectedUnitsList" class="mt-3"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="button" class="btn btn-danger" id="confirmBulkDelete">{{ __('Delete Selected Units') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                @if(count($units) > 0 && Auth::user()->can('Delete Employee'))
                                <th width="50px">{{ __('Select') }}</th>
                                @endif
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Project Name') }}</th>
                                <th>{{ __('Unit Name') }}</th>
                                <th>{{ __('Unit Size') }}</th>
                                @if (Auth::user()->type != 'hr' && Gate::check('Delete Employee'))
                                <th width="200px">{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($units as $unit)
                                <tr>
                                    @if(Auth::user()->can('Delete Employee'))
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input unit-checkbox" value="{{ $unit->id }}" data-unit-name="{{ $unit->unit_name }}">
                                        </div>
                                    </td>
                                    @endif
                                    <td>{{ $unit->id }}</td>
                                    <td>{{ $unit->project ? $unit->project->project_name : __('N/A') }}</td>
                                    <td>{{ $unit->unit_name }}</td>
                                    <td>{{ $unit->unit_size }}</td>
                                    @if (Auth::user()->type != 'hr' && Gate::check('Delete Employee'))
                                        <td class="Action">
                                            @can('Delete Employee')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['units.destroy', $unit->id], 'id' => 'delete-form-' . $unit->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                        data-original-title="{{ __('Delete') }}"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $unit->id }}').submit();">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('Delete Employee') ? 6 : 5 }}" class="text-center">{{ __('No units found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create a hidden form for bulk delete -->
<form action="{{ route('units.bulk-delete') }}" method="POST" id="bulkDeleteForm" style="display: none;">
    @csrf
    <input type="hidden" name="unit_ids" id="unitIdsInput">
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const unitCheckboxes = document.querySelectorAll('.unit-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const debugBtn = document.getElementById('debugBtn');
    const selectedCount = document.getElementById('selectedCount');
    const unitIdsInput = document.getElementById('unitIdsInput');
    const confirmBulkDelete = document.getElementById('confirmBulkDelete');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const selectedUnitsList = document.getElementById('selectedUnitsList');
    
    // Select all functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            unitCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateSelectedCount();
        });
    }
    
    // Individual checkbox functionality
    unitCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Update selected count and enable/disable delete button
    function updateSelectedCount() {
        const selected = document.querySelectorAll('.unit-checkbox:checked');
        const count = selected.length;
        
        selectedCount.textContent = count;
        bulkDeleteBtn.disabled = count === 0;
        
        // Update select all checkbox state
        if (selectAll) {
            selectAll.checked = count === unitCheckboxes.length;
        }
    }
    
    // Bulk delete button click
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const selected = document.querySelectorAll('.unit-checkbox:checked');
            const ids = Array.from(selected).map(checkbox => checkbox.value);
            
            if (ids.length === 0) {
                alert('Please select at least one unit to delete.');
                return;
            }
            
            // Show selected units in modal
            let unitsHtml = '<strong>Selected Units:</strong><ul class="mt-2">';
            selected.forEach(checkbox => {
                unitsHtml += `<li>${checkbox.dataset.unitName} (ID: ${checkbox.value})</li>`;
            });
            unitsHtml += '</ul>';
            selectedUnitsList.innerHTML = unitsHtml;
            
            unitIdsInput.value = JSON.stringify(ids);
            console.log('Unit IDs to delete:', ids);
            
            $('#bulkDeleteModal').modal('show');
        });
    }
    
    // Debug button click
    if (debugBtn) {
        debugBtn.addEventListener('click', function() {
            const selected = document.querySelectorAll('.unit-checkbox:checked');
            const ids = Array.from(selected).map(checkbox => checkbox.value);
            
            console.log('Debug Info:');
            console.log('Selected Unit IDs:', ids);
            console.log('Selected Units JSON:', JSON.stringify(ids));
            
            alert(`Debug Information:\nSelected Unit IDs: ${ids.join(', ')}\nJSON: ${JSON.stringify(ids)}`);
        });
    }
    
    // Confirm bulk delete
    if (confirmBulkDelete) {
        confirmBulkDelete.addEventListener('click', function() {
            const selected = document.querySelectorAll('.unit-checkbox:checked');
            const ids = Array.from(selected).map(checkbox => checkbox.value);
            
            console.log('Submitting delete request for IDs:', ids);
            
            $('#bulkDeleteModal').modal('hide');
            bulkDeleteForm.submit();
        });
    }
});
</script>
<style>
.bulk-actions {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

.form-check {
    margin-bottom: 0;
}

#bulkDeleteBtn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.unit-checkbox {
    cursor: pointer;
}

#selectedUnitsList {
    max-height: 200px;
    overflow-y: auto;
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

#selectedUnitsList ul {
    margin-bottom: 0;
}

/* Table alignment improvements */
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
#pc-dt-simple th {
    min-width: 120px;
}

#pc-dt-simple th:nth-child(1) {
    min-width: 80px !important; /* Select (conditional) */
    width: 80px !important;
    max-width: 80px !important;
}

#pc-dt-simple th:nth-child(2) {
    min-width: 80px; /* ID */
}

#pc-dt-simple th:nth-child(3) {
    min-width: 250px; /* Project Name */
}

#pc-dt-simple th:nth-child(4) {
    min-width: 200px; /* Unit Name */
}

#pc-dt-simple th:nth-child(5) {
    min-width: 150px; /* Unit Size */
}

#pc-dt-simple th:nth-child(6) {
    min-width: 220px; /* Action (conditional) */
}

/* Force column widths for table cells as well */
#pc-dt-simple td:nth-child(1) {
    min-width: 80px !important; /* Select (conditional) */
    width: 80px !important;
    max-width: 80px !important;
}

#pc-dt-simple td:nth-child(3) {
    min-width: 250px !important; /* Project Name */
    width: 250px !important;
    max-width: 250px !important;
}

#pc-dt-simple td:nth-child(4) {
    min-width: 200px !important; /* Unit Name */
    width: 200px !important;
    max-width: 200px !important;
}
</style>
@endpush