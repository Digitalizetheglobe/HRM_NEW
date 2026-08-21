@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Timesheet') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Timesheet') }}</li>
@endsection

@section('action-button')
    @if(Auth::user()->type != 'hr')
        <a href="{{ route('timesheet.export', request()->query()) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
            data-bs-original-title="{{ __('Export') }}">
            <i class="ti ti-file-export"></i>
        </a>

        @can('Create TimeSheet')
            <a href="#" data-url="{{ route('timesheet.create') }}" data-ajax-popup="true" data-size="xl"
                data-title="{{ __('Create New Timesheet') }}" data-bs-toggle="tooltip" title=""
                class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}" id="create-btn">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-4" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['timesheet.index'], 'method' => 'get', 'id' => 'timesheet_filter']) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-6">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                            {{ Form::date('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : '', ['class' => 'month-btn form-control', 'autocomplete' => 'off', 'id' => 'start_date']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                            {{ Form::date('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : '', ['class' => 'month-btn form-control', 'autocomplete' => 'off', 'id' => 'end_date']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-auto mt-4">
                                        <a href="#" class="btn btn-sm btn-primary"
                                            onclick="document.getElementById('timesheet_filter').submit(); return false;"
                                            data-bs-toggle="tooltip" title="" data-bs-original-title="apply">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{ route('timesheet.index') }}" class="btn btn-sm btn-danger"
                                            data-bs-toggle="tooltip" title="" data-bs-original-title="Reset">
                                            <span class="btn-inner--icon"><i
                                                    class="ti ti-trash-off text-white-off "></i></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <div class="card-body py-0">
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    @if (\Auth::user()->type != 'employee')
                                        <th>{{ __('Employee Name') }}</th>
                                    @endif
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Hours') }}</th>
                                    <th>{{ __('Remark') }}</th>
                                    @if (Auth::user()->type != 'hr' && (Gate::check('Edit TimeSheet') || Gate::check('Delete TimeSheet')))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                    </tr>
                            </thead>
                                <tbody id="enquiry-table-body">
                                    @foreach ($timeSheets as $timeSheet)
                                        <tr>
                                            @if (\Auth::user()->type != 'employee')
                                                <td>
                                                    {{ $timeSheet->employee->employee->full_name ?? $timeSheet->employee->name ?? 'N/A' }}
                                                </td>
                                            @endif
                                            <td>{{ \Auth::user()->dateFormat($timeSheet->date) }}</td>
                                            <td>{{ $timeSheet->hours }}</td>
                                            <td>
                                                @if(!empty($timeSheet->remark))
                                                    <a href="#" class="action-btn bg-info p-1 rounded view-remark d-inline-flex align-items-center justify-content-center"
                                                       data-remark="{{ $timeSheet->remark }}"
                                                       style="width: 30px; height: 30px; color: white;">
                                                        <i class="ti ti-message-2"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td class="Action">
                                                <span class="d-flex justify-content-end align-items-center gap-1">
                                                    {{-- View button --}}
                                                    <div class="action-btn bg-warning">
                                                        <a href="#" class="btn btn-sm view-btn"
                                                            data-url="{{ route('timesheet.show', $timeSheet->id) }}"
                                                            data-ajax-popup="true"
                                                            data-size="xl"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#commonModal"
                                                            data-title="{{ __('Timesheet Details') }}">
                                                            <i class="ti ti-eye text-white"></i>
                                                        </a>
                                                    </div>

                                                    {{-- Edit/Delete buttons --}}
                                                        @can('Edit TimeSheet')
                                                            <div class="action-btn bg-info">
                                                                <a href="#"
                                                                class="btn btn-sm edit-btn"
                                                                data-url="{{ route('timesheet.edit', $timeSheet->id) }}"
                                                                data-ajax-popup="true"
                                                                data-size="xl"
                                                                data-bs-toggle="tooltip"
                                                                data-title="{{ __('Edit Timesheet') }}"
                                                                data-bs-original-title="{{ __('Edit') }}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @if(Auth::user()->type != 'Director' && strcasecmp(Auth::user()->type, 'Employee') != 0)
                                                            @can('Delete TimeSheet')
                                                                <div class="action-btn bg-danger">
                                                                    <form action="{{ route('timesheet.destroy', $timeSheet->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                                class="btn btn-sm"
                                                                                data-bs-toggle="tooltip"
                                                                                title="{{ __('Delete') }}">
                                                                            <i class="ti ti-trash text-white"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @endcan
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
    <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="remarkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="remarkModalLabel">{{ __('Remark') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="remarkContent" style="white-space: pre-wrap; word-break: break-word;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    $(document).ready(function() {
        var now = new Date();
        var month = (now.getMonth() + 1).toString().padStart(2, '0');
        var day = now.getDate().toString().padStart(2, '0');
        var today = now.getFullYear() + '-' + month + '-' + day;
        $('.current_date').val(today);

        function refreshEnquiryTable() {
            $.ajax({
                url: "{{ route('timesheet.index') }}",
                type: 'GET',
                data: $('#timesheet_filter').serialize(),
                success: function(response) {
                    var newContent = $(response).find('#enquiry-table-body').html();
                    $('#enquiry-table-body').html(newContent);
                    initEventHandlers();
                }
            });
        }

        function initEventHandlers() {
            $('#create-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                var size = $(this).data('size') || 'md';
                $('#commonModal .modal-dialog').removeClass('modal-sm modal-md modal-lg modal-xl').addClass('modal-' + size);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#commonModal .body, #commonModal .modal-body').html(response);
                        $('#commonModal .modal-title').text('Create New Timesheet');
                        $('#commonModal').modal('show');
                        
                        $('#timesheet-form').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            var form = $(this);
                            $.ajax({
                                url: form.attr('action'),
                                type: 'POST',
                                data: form.serialize(),
                                success: function(response) {
                                    $('#commonModal').modal('hide');
                                    show_toastr('Success', 'Timesheet created successfully', 'success');
                                    refreshEnquiryTable();
                                },
                                error: function(xhr) {
                                    show_toastr('Error', xhr.responseJSON.message || 'An error occurred', 'error');
                                }
                            });
                        });
                    }
                });
            });

            $('.edit-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                var size = $(this).data('size') || 'md';
                $('#commonModal .modal-dialog').removeClass('modal-sm modal-md modal-lg modal-xl').addClass('modal-' + size);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#commonModal .body, #commonModal .modal-body').html(response);
                        $('#commonModal .modal-title').text('Edit Timesheet');
                        $('#commonModal').modal('show');
                        
                        $('#timesheet-form').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            var form = $(this);
                            $.ajax({
                                url: form.attr('action'),
                                type: 'POST',
                                data: form.serialize(),
                                success: function(response) {
                                    $('#commonModal').modal('hide');
                                    show_toastr('Success', 'Timesheet updated successfully', 'success');
                                    refreshEnquiryTable();
                                },
                                error: function(xhr) {
                                    show_toastr('Error', xhr.responseJSON.message || 'An error occurred', 'error');
                                }
                            });
                        });
                    }
                });
            });

            $('.view-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                var size = $(this).data('size') || 'md';
                $('#commonModal .modal-dialog').removeClass('modal-sm modal-md modal-lg modal-xl').addClass('modal-' + size);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#commonModal .body, #commonModal .modal-body').html(response);
                        $('#commonModal .modal-title').text('Timesheet Details');
                        $('#commonModal').modal('show');
                    }
                });
            });

            $('.view-remark').off('click').on('click', function(e) {
                e.preventDefault();
                var remark = $(this).data('remark');
                $('#remarkContent').text(remark);
                $('#remarkModal').modal('show');
            });
        }

        initEventHandlers();

        $('#timesheet_filter').on('submit', function(e) {
            e.preventDefault();
            refreshEnquiryTable();
        });

        $('#commonModal').on('hidden.bs.modal', function () {
            refreshEnquiryTable();
        });
    });
</script>
@endpush