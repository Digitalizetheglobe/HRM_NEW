@extends('layouts.admin')

@section('page-title')
    {{ __('Company Policies') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Company Policies') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <h5>{{ __('Company Policies') }}</h5>
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>{{ __('Branch') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Attachment') }}</th>
                                <th width="200px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($companyPolicy as $policy)
                                @php
                                    $policyPath = \App\Models\Utility::get_file('uploads/companyPolicy');
                                    $acknowledgement = \App\Models\PolicyAcknowledgement::where('company_policy_id', $policy->id)
                                        ->where('employee_id', $employee->id)
                                        ->first();
                                    $canAcknowledge = $acknowledgement && ($acknowledgement->has_previewed || $acknowledgement->has_downloaded);
                                    $isAcknowledged = $acknowledgement && $acknowledgement->isAcknowledged();
                                @endphp
                                <tr>
                                    <td>{{ !empty($policy->branches) ? $policy->branches->name : '-' }}</td>
                                    <td>{{ $policy->title }}</td>
                                    <td>{{ Str::limit($policy->description, 50) }}</td>
                                    <td>
                                        @if (!empty($policy->attachment))
                                            <div class="d-flex gap-2">
                                                <div class="action-btn bg-primary ms-2">
                                                    <a class="mx-3 btn btn-sm align-items-center download-policy" 
                                                       href="{{ route('company-policy.employee.download', $policy->id) }}" 
                                                       data-policy-id="{{ $policy->id }}">
                                                        <i class="ti ti-download text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn bg-secondary ms-2">
                                                    <a class="mx-3 btn btn-sm align-items-center preview-policy" 
                                                       href="{{ $policyPath . '/' . $policy->attachment }}" 
                                                       target="_blank"
                                                       data-policy-id="{{ $policy->id }}"
                                                       onclick="trackPreviewOnClick({{ $policy->id }}, this); return true;">
                                                        <i class="ti ti-crosshair text-white" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Preview') }}"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <p>-</p>
                                        @endif
                                    </td>
                                    <td class="Action">
                                        <span>
                                            @if ($isAcknowledged)
                                                <span class="badge bg-success">{{ __('Acknowledged') }}</span>
                                                <br>
                                                <small class="text-muted">{{ __('Acknowledged on') }}: {{ $acknowledgement->acknowledged_at ? $acknowledgement->acknowledged_at->format('Y-m-d H:i') : '-' }}</small>
                                            @else
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary acknowledge-btn" 
                                                        data-policy-id="{{ $policy->id }}"
                                                        data-can-acknowledge="{{ $canAcknowledge ? '1' : '0' }}">
                                                    {{ __('Acknowledgement') }}
                                                </button>
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

<!-- Acknowledgement Confirmation Modal -->
<div class="modal fade" id="acknowledgementModal" tabindex="-1" role="dialog" aria-labelledby="acknowledgementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="acknowledgementModalLabel">{{ __('Confirm Acknowledgement') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <i class="ti ti-alert-circle text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <p class="mb-0">{{ __('Are you sure you want to acknowledge this policy?') }}</p>
                        <small class="text-muted">{{ __('This action confirms that you have read and understood the policy.') }}</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirmAcknowledgementBtn">{{ __('Confirm Acknowledgement') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script>
    // Track preview when link is clicked
    function trackPreviewOnClick(policyId, element) {
        // Track preview via AJAX
        $.ajax({
            url: '{{ route("company-policy.employee.preview", ":id") }}'.replace(':id', policyId),
            type: 'GET',
            success: function(response) {
                // Update button state
                var btn = $('.acknowledge-btn[data-policy-id="' + policyId + '"]');
                if (btn.length) {
                    btn.data('can-acknowledge', '1');
                }
            },
            error: function(xhr) {
                console.error('Error tracking preview:', xhr);
            }
        });
    }

    $(document).ready(function() {
        // Download tracking is handled server-side in the download route
        // After download, the page will need to be refreshed to update button state
        // Or we can use a small delay and check acknowledgement status

        // Store current policy ID and button for modal confirmation
        var currentPolicyId = null;
        var currentBtn = null;

        // Handle acknowledgement button click
        $('.acknowledge-btn').on('click', function() {
            var policyId = $(this).data('policy-id');
            var canAcknowledge = $(this).data('can-acknowledge') == '1';
            var btn = $(this);

            if (!canAcknowledge) {
                show_toastr('Error', '{{ __('Please preview the policy first.') }}', 'error');
                return;
            }

            // Store for modal confirmation
            currentPolicyId = policyId;
            currentBtn = btn;

            // Show confirmation modal
            $('#acknowledgementModal').modal('show');
        });

        // Handle confirmation button in modal
        $('#confirmAcknowledgementBtn').on('click', function() {
            if (currentPolicyId) {
                // Hide modal
                $('#acknowledgementModal').modal('hide');
                
                // Process acknowledgement
                acknowledgePolicy(currentPolicyId, currentBtn);
                
                // Reset
                currentPolicyId = null;
                currentBtn = null;
            }
        });

        // Reset variables when modal is closed
        $('#acknowledgementModal').on('hidden.bs.modal', function() {
            currentPolicyId = null;
            currentBtn = null;
        });

        function trackPreview(policyId) {
            $.ajax({
                url: '{{ route("company-policy.employee.preview", ":id") }}'.replace(':id', policyId),
                type: 'GET',
                success: function(response) {
                    // Update button state
                    var btn = $('.acknowledge-btn[data-policy-id="' + policyId + '"]');
                    if (btn.length) {
                        btn.data('can-acknowledge', '1');
                    }
                },
                error: function(xhr) {
                    console.error('Error tracking preview:', xhr);
                }
            });
        }


        function acknowledgePolicy(policyId, btn) {
            $.ajax({
                url: '{{ route("company-policy.employee.acknowledge", ":id") }}'.replace(':id', policyId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        show_toastr('Success', response.message, 'success');
                        // Reload page to show acknowledged status
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        show_toastr('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    var message = '{{ __('An error occurred. Please try again.') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    show_toastr('Error', message, 'error');
                }
            });
        }
    });
</script>
@endpush

