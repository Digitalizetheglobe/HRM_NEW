<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label">{{ __('Task Title') }}</label>
                <p class="text-muted">{{ $todo->task }}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">{{ __('Priority') }}</label>
                <p class="text-muted">
                    @if(strtolower($todo->priority) == 'high')
                        <span class="badge bg-danger">{{ ucfirst($todo->priority) }}</span>
                    @elseif(strtolower($todo->priority) == 'medium')
                        <span class="badge bg-warning">{{ ucfirst($todo->priority) }}</span>
                    @else
                        <span class="badge bg-success">{{ ucfirst($todo->priority) }}</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">{{ __('Status') }}</label>
                <p class="text-muted">
                    @if($todo->is_completed)
                        <span class="badge bg-success">{{ __('Completed') }}</span>
                    @else
                        <span class="badge bg-warning">{{ __('Pending') }}</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">{{ __('Start Date') }}</label>
                <p class="text-muted">{{ $todo->start_date ? \Carbon\Carbon::parse($todo->start_date)->format('d M Y, h:i A') : '-' }}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">{{ __('End Date') }}</label>
                <p class="text-muted">{{ $todo->end_date ? \Carbon\Carbon::parse($todo->end_date)->format('d M Y, h:i A') : '-' }}</p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label">{{ __('Description') }}</label>
                <p class="text-muted">{{ $todo->description ?: '-' }}</p>
            </div>
        </div>
    </div>
</div>
