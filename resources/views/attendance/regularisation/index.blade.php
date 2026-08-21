@extends('layouts.admin')

@section('page-title')
    {{ __('Attendance Regularisation') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Attendance Regularisation') }}</li>
@endsection

@section('action-button')
    @if (\Auth::user()->type == 'employee' || \Auth::user()->type == 'company')
        <a href="#" data-url="{{ route('attendance-regularisation.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create Attendance Regularisation') }}" data-size="lg" data-bs-toggle="tooltip" title=""
            class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-border-style">
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    @if (\Auth::user()->type != 'employee')
                                        <th class="text-start">{{ __('Employee Name') }}</th>
                                    @endif
                                    <th class="text-start">{{ __('Date') }}</th>
                                    <th class="text-start">{{ __('In Time') }}</th>
                                    <th class="text-start">{{ __('Out Time') }}</th>
                                    <th class="text-start">{{ __('Reason') }}</th>
                                    <th class="text-start">{{ __('Remark') }}</th>
                                    <th class="text-start">{{ __('Status') }}</th>
                                    <th class="text-center" width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($regularisations as $regularisation)
                                    <tr>
                                        @if (\Auth::user()->type != 'employee')
                                            <td class="text-start">{{ !empty($regularisation->employee) ? $regularisation->employee->full_name : __('N/A') }}</td>
                                        @endif
                                        <td class="text-start">{{ \Auth::user()->dateFormat($regularisation->missed_attendance_date) }}</td>
                                        <td class="text-start">{{ \Auth::user()->timeFormat($regularisation->punch_in_time) }}</td>
                                        <td class="text-start">{{ \Auth::user()->timeFormat($regularisation->punch_out_time) }}</td>
                                        <td class="text-start">{{ $regularisation->reason }}</td>
                                        <td class="text-start">{{ \Illuminate\Support\Str::limit($regularisation->remark, 30) }}</td>
                                        <td class="text-start">
                                            @if ($regularisation->status == 'Pending')
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @elseif ($regularisation->status == 'Approved')
                                                <span class="badge bg-success">{{ __('Approved') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                            @endif
                                        </td>
                                        <td class="Action" style="vertical-align: middle;">
                                            <span class="d-flex justify-content-end align-items-center gap-1">
                                                @if (\Auth::user()->type != 'employee')
                                                    @if ($regularisation->status == 'Pending')
                                                        {{-- If pending, show Action button, Edit button, and Delete button --}}
                                                        <div class="action-btn bg-success">
                                                            <a href="#" class="btn btn-sm"
                                                                data-url="{{ route('attendance-regularisation.action', $regularisation->id) }}"
                                                                data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('Attendance Regularisation Action') }}"
                                                                data-bs-original-title="{{ __('Manage Regularisation') }}">
                                                                <i class="ti ti-caret-right text-white"></i>
                                                            </a>
                                                        </div>

                                                        <div class="action-btn bg-info">
                                                            <a href="#" class="btn btn-sm"
                                                                data-url="{{ route('attendance-regularisation.edit', $regularisation->id) }}"
                                                                data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('Edit Regularisation') }}"
                                                                data-bs-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>

                                                        <div class="action-btn bg-danger">
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['attendance-regularisation.destroy', $regularisation->id],
                                                                'id' => 'delete-form-' . $regularisation->id,
                                                            ]) !!}
                                                            <a href="#"
                                                                class="btn btn-sm bs-pass-para"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{ $regularisation->id }}').submit();">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @else
                                                        {{-- If approved or rejected, show View button and Delete button only --}}
                                                        <div class="action-btn bg-info">
                                                            <a href="#" class="btn btn-sm"
                                                                data-url="{{ route('attendance-regularisation.show', $regularisation->id) }}"
                                                                data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('View Details') }}"
                                                                data-bs-original-title="{{ __('View') }}">
                                                                <i class="ti ti-eye text-white"></i>
                                                            </a>
                                                        </div>

                                                        <div class="action-btn bg-danger">
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['attendance-regularisation.destroy', $regularisation->id],
                                                                'id' => 'delete-form-' . $regularisation->id,
                                                            ]) !!}
                                                            <a href="#"
                                                                class="btn btn-sm bs-pass-para"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{ $regularisation->id }}').submit();">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endif
                                                @else
                                                    {{-- Employee can only view and edit if pending --}}
                                                    <div class="action-btn bg-info">
                                                        <a href="#" class="btn btn-sm"
                                                            data-url="{{ route('attendance-regularisation.show', $regularisation->id) }}"
                                                            data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                            title="" data-title="{{ __('View Details') }}"
                                                            data-bs-original-title="{{ __('View') }}">
                                                            <i class="ti ti-eye text-white"></i>
                                                        </a>
                                                    </div>

                                                    @if ($regularisation->status == 'Pending')
                                                        <div class="action-btn bg-info">
                                                            <a href="#" class="btn btn-sm"
                                                                data-url="{{ route('attendance-regularisation.edit', $regularisation->id) }}"
                                                                data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('Edit Regularisation') }}"
                                                                data-bs-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ \Auth::user()->type != 'employee' ? '8' : '7' }}" class="text-center">
                                            {{ __('No attendance regularisation requests found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        .table th {
            white-space: nowrap !important;
            text-align: left !important;
            vertical-align: middle !important;
            padding: 0.75rem !important;
            position: relative;
        }
        
        .table td {
            vertical-align: middle !important;
        }

        /* Ensure proper column width alignment */
        #pc-dt-simple th {
            min-width: 120px;
        }
        
        @media (max-width: 768px) {
            #pc-dt-simple th {
                min-width: 140px !important;
            }
        }
    </style>
    

@endpush


