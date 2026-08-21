<div class="modal-body">
    <div class="row p-2">
        <div class="col-12 mb-4">
            <h4 class="text-primary fw-bold mb-3">{{ $notice->title }}</h4>
            <div class="text-muted" style="line-height: 1.8;">{!! $notice->description !!}</div>
        </div>
        
        @if(Auth::user()->type != 'employee')
        <div class="col-12 mt-2">
            <div class="row bg-light p-3 rounded">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <div class="theme-avtar bg-primary text-white rounded p-2 me-3">
                            <i class="ti ti-calendar-event fs-4"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted text-sm">{{ __('Start Date') }}</p>
                            <h6 class="mb-0 fw-bold">{{ $notice->notice_startdate ? \Carbon\Carbon::parse($notice->notice_startdate)->format('d M Y') : '-' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="theme-avtar bg-danger text-white rounded p-2 me-3">
                            <i class="ti ti-calendar-off fs-4"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted text-sm">{{ __('End Date') }}</p>
                            <h6 class="mb-0 fw-bold">{{ $notice->notice_enddate ? \Carbon\Carbon::parse($notice->notice_enddate)->format('d M Y') : '-' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
