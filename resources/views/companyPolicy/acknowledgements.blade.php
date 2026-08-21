@extends('layouts.admin')

@section('page-title')
    {{ __('Policy Acknowledgements') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('company-policy.index') }}">{{ __('Company Policy') }}</a></li>
    <li class="breadcrumb-item">{{ __('Acknowledgements') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <h5>{{ __('Policy Acknowledgements') }}</h5>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="policy_select" class="form-label">{{ __('Select Policy') }}</label>
                        <select class="form-control" id="policy_select" name="policy_id" onchange="filterAcknowledgements()">
                            <option value="">{{ __('Select a policy') }}</option>
                            @foreach ($policies as $policy)
                                <option value="{{ $policy->id }}" {{ $selectedPolicyId == $policy->id ? 'selected' : '' }}>
                                    {{ $policy->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    @if ($selectedPolicyId)
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{ __('Employee Name') }}</th>
                                    <th>{{ __('Employee ID') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Previewed') }}</th>
                                    <th>{{ __('Downloaded') }}</th>
                                    <th>{{ __('Acknowledged At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($acknowledgements as $ack)
                                    <tr>
                                        <td>{{ $ack->employee->full_name ?? '-' }}</td>
                                        <td>{{ $ack->employee->employee_id ?? '-' }}</td>
                                        <td>{{ $ack->employee->branch->name ?? '-' }}</td>
                                        <td>{{ $ack->employee->department->name ?? '-' }}</td>
                                        <td>
                                            @if ($ack->has_previewed)
                                                <span class="badge bg-success">{{ __('Yes') }}</span>
                                                @if ($ack->previewed_at)
                                                    <br><small class="text-muted">{{ $ack->previewed_at->format('Y-m-d H:i') }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-danger">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ack->has_downloaded)
                                                <span class="badge bg-success">{{ __('Yes') }}</span>
                                                @if ($ack->downloaded_at)
                                                    <br><small class="text-muted">{{ $ack->downloaded_at->format('Y-m-d H:i') }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-danger">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ack->acknowledged_at)
                                                <span class="badge bg-success">{{ $ack->acknowledged_at->format('Y-m-d H:i:s') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('Not Acknowledged') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('No acknowledgements found for this policy.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script>
    function filterAcknowledgements() {
        var policyId = $('#policy_select').val();
        if (policyId) {
            window.location.href = '{{ route("company-policy.acknowledgements") }}?policy_id=' + policyId;
        } else {
            window.location.href = '{{ route("company-policy.acknowledgements") }}';
        }
    }
</script>
@endpush

