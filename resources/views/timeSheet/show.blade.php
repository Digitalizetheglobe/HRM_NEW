<div class="modal-body">
    <div class="card border-0 shadow-none">
        <div class="card-body">
            <div class="row mb-3">
                @if (\Auth::user()->type != 'employee')
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('Employee Name') }}</label>
                        <p>{{ $timeSheet->employee->employee->full_name ?? $timeSheet->employee->name ?? 'N/A' }}</p>
                    </div>
                @endif
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Date') }}</label>
                    <p>{{ \Auth::user()->dateFormat($timeSheet->date) }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Hours') }}</label>
                    <p>{{ $timeSheet->hours }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Remark') }}</label>
                    <p>{{ $timeSheet->remark }}</p>
                </div>
            </div>
        </div>
    </div>
</div>