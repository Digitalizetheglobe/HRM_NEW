@php
    if (!function_exists('breakAfterWords')) {
        function breakAfterWords($text, $wordsPerLine = 3) {
            $words = explode(' ', $text);
            $lines = array_chunk($words, $wordsPerLine);
            return implode('<br>', array_map('implode', array_fill(0, count($lines), ' '), $lines));
        }
    }
@endphp
@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Leave') }}
@endsection


@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Leave ') }}</li>
@endsection

@section('action-button')
    <a href="{{ route('leave.export') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
        data-bs-original-title="{{ __('Export') }}">
        <i class="ti ti-file-export"></i>
    </a>


    @can('Create Leave')
        <a href="#" data-url="{{ route('leave.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create New Leave') }}" data-size="lg" data-bs-toggle="tooltip" title=""
            class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection

@section('content')

    @if(\Auth::user()->type == 'employee' && isset($leaveBalances) && count($leaveBalances) > 0)
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Leave Balance Summary') }}</h5>
                    <div class="row">
                        @php
                            $cardColors = [
                                'total' => 'bg-primary',
                                'used' => 'bg-danger',
                                'remaining' => 'bg-success',
                                'casual' => 'bg-warning'
                            ];
                        @endphp
                        @foreach($leaveBalances as $index => $balance)
                            @php
                                $bg = $cardColors[$balance['type']] ?? 'bg-secondary';
                            @endphp
                            <div class="col-md-3 mb-3">
                                <div class="card {{ $bg }} text-white">
                                    <div class="card-body text-end">
                                        <h6 class="text-white mb-2 text-xl">{{ $balance['title'] }}</h6>
                                        <h3 class="mb-0">
                                            {{ number_format($balance['value'], 2) }}
                                        </h3>
                                        <small class="text-white-50">
                                            {{ $balance['subtext'] }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters Card - Only show to non-employees -->
    @if(\Auth::user()->type != 'employee')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['leave.index'], 'method' => 'get', 'id' => 'leave_filter_form']) }}
                    <div class="row align-items-end justify-content-center">
                        <div class="col-xl-9">
                            <div class="row align-items-end">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('month', __('Month'), ['class' => 'form-label']) }}
                                        <select class="form-select" id="month_filter" name="month">
                                            <option value="">{{ __('All Months') }}</option>
                                            @for($i = 1; $i <= 12; $i++)
                                                @php $m = sprintf("%02d", $i); @endphp
                                                <option value="{{ $m }}" {{ request()->month == $m ? 'selected' : '' }}>
                                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('year', __('Year'), ['class' => 'form-label']) }}
                                        <select class="form-select" id="year_filter" name="year">
                                            <option value="">{{ __('All Years') }}</option>
                                            @php
                                                $currentYear = date('Y');
                                            @endphp
                                            @for($year = $currentYear; $year >= $currentYear - 5; $year--)
                                                <option value="{{ $year }}" {{ request()->year == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12 mt-3 mt-lg-0">
                            <div class="d-flex flex-wrap justify-content-end">
                                <a href="{{ route('leave.index') }}" class="btn btn-danger d-flex align-items-center" id="reset_filters">
                                    <i class="ti ti-refresh me-2"></i> {{ __('Reset Filters') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-border-style-tab">
                        <ul class="nav nav-tabs" id="leaveTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="true">
                                    {{ __('Approved Leave') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
                                    {{ __('Pending Leave') }}
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content mt-3" id="leaveTabsContent">
                            <!-- Approved Leave Tab -->
                            <div class="tab-pane fade show active" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                                    <table class="table" id="pc-dt-simple">
                                        <thead>
                                            <tr>
                                                @if (\Auth::user()->type != 'employee')
                                                    <th class="text-start">{{ __('Employee') }}</th>
                                                @endif
                                                <th class="text-start">{{ __('Leave Type') }}</th>
                                                <th class="text-start">{{ __('Applied On') }}</th>
                                                <th class="text-start">{{ __('Leave Duration') }}</th>
                                                <th class="text-start">{{ __('Date(s)') }}</th>
                                                <th class="text-start">{{ __('status') }}</th>
                                                @if (\Auth::user()->type != 'employee' && strtolower(\Auth::user()->type) != 'director')
                                                    <th class="text-center" width="200px">{{ __('Action') }}</th>
                                                @endif    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($leaves as $leave)
                                                @if($leave->status == 'Approved')
                                                <tr data-leave-type="@if(!empty($leave->leave_type_id) && $leave->leaveType) {{ $leave->leaveType->title }} @else {{ __('N/A') }} @endif" data-month="{{ date('m', strtotime($leave->start_date)) }}" data-year="{{ date('Y', strtotime($leave->start_date)) }}" data-end-month="{{ date('m', strtotime($leave->end_date)) }}" data-end-year="{{ date('Y', strtotime($leave->end_date)) }}">
                                                    @if (\Auth::user()->type != 'employee')
                                                        <td class="text-start">{{ !empty($leave->employee_id) && $leave->employees ? trim($leave->employees->name . ' ' . $leave->employees->middle_name . ' ' . $leave->employees->last_name) : '' }}
                                                        </td>
                                                    @endif
                                                    <td class="text-start">
                                                        @if(!empty($leave->leave_type_id) && $leave->leaveType)
                                                            {{ $leave->leaveType->title }}
                                                        @else
                                                            {{ __('N/A') }}
                                                        @endif
                                                    </td>
                                                    <td class="text-start" data-order="{{ $leave->id }}">{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                                                    <td class="text-start">
                                                        @php
                                                            $durType = $leave->leave_duration_type ?? '';
                                                            $days = $leave->total_leave_days ?? 0;
                                                        @endphp
                                                        @if($durType == 'half_day')
                                                            <span class="badge bg-info">{{ __('Half Day') }}</span>
                                                            @if($leave->half_day_session)
                                                                <br><small class="text-muted">
                                                                    {{ $leave->half_day_session == 'first_half' ? __('First Half') : __('Second Half') }}
                                                                </small>
                                                            @endif
                                                        @elseif($durType == 'full_day')
                                                            @if($days != floor($days))
                                                                {{-- Mixed: e.g. 1.5 days --}}
                                                                <span class="badge" style="background:#7c3aed;">{{ number_format($days,1) }} {{ __('Days') }}</span>
                                                            @else
                                                                <span class="badge bg-primary">{{ __('Full Day') }}</span>
                                                                @if($days > 1)
                                                                    <br><small class="text-muted">{{ number_format($days,0) }} {{ __('days') }}</small>
                                                                @endif
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary">{{ __('N/A') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-start">
                                                        @if(isset($leave->leave_duration_type) && $leave->leave_duration_type == 'half_day')
                                                            {{-- For half day, show date and session --}}
                                                            <div>
                                                                <strong>{{ \Auth::user()->dateFormat($leave->start_date) }}</strong>
                                                                @if($leave->half_day_session)
                                                                    <br><small class="badge bg-info">
                                                                        @if($leave->half_day_session == 'first_half')
                                                                            {{ __('First Half') }}
                                                                        @elseif($leave->half_day_session == 'second_half')
                                                                            {{ __('Second Half') }}
                                                                        @endif
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        @else
                                                            {{-- For full day, show date range --}}
                                                            @if($leave->start_date == $leave->end_date)
                                                                {{ \Auth::user()->dateFormat($leave->start_date) }}
                                                            @else
                                                                {{ \Auth::user()->dateFormat($leave->start_date) }} <br>
                                                                <small class="text-muted">to</small><br>
                                                                {{ \Auth::user()->dateFormat($leave->end_date) }}
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="text-start">
                                                        @if ($leave->status == 'Pending')
                                                            <div class="badge bg-warning p-2 px-3 rounded status-badge5">
                                                                {{ $leave->status }}</div>
                                                        @elseif($leave->status == 'Approved')
                                                            <div class="badge bg-success p-2 px-3 rounded status-badge5">
                                                                {{ $leave->status }}</div>
                                                        @elseif($leave->status == 'Reject')
                                                            <div class="badge bg-danger p-2 px-3 rounded status-badge5">
                                                                {{ $leave->status }}</div>
                                                        @endif
                                                    </td>

                                                    @if (\Auth::user()->type != 'employee' && strtolower(\Auth::user()->type) != 'director')
                                                        <td class="text-center Action">

                                                            <span>
                                                                @if (\Auth::user()->type != 'employee')
                                                                    @if ($leave->status == 'Approved')
                                                                        {{-- If approved, show View button and Delete button --}}
                                                                        <div class="action-btn bg-info ms-2">
                                                                            <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                                                data-size="lg"
                                                                                data-url="{{ URL::to('leave/' . $leave->id) }}"
                                                                                data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                                title="" data-title="{{ __('View Leave Details') }}"
                                                                                data-bs-original-title="{{ __('View') }}">
                                                                                <i class="ti ti-eye text-white"></i>
                                                                            </a>
                                                                        </div>
                                                                        @can('Delete Leave')
                                                                            <div class="action-btn bg-danger ms-2">
                                                                                {!! Form::open([
                                                                                    'method' => 'DELETE',
                                                                                    'route' => ['leave.destroy', $leave->id],
                                                                                    'id' => 'delete-form-' . $leave->id,
                                                                                ]) !!}
                                                                                <a href="#"
                                                                                    class="mx-3 btn btn-sm  align-items-center bs-pass-para"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-bs-original-title="Delete" aria-label="Delete"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                </form>
                                                                            </div>
                                                                        @endcan
                                                                    @else
                                                                        {{-- If not approved, show Action button, Edit button, and Delete button --}}
                                                                        <div class="action-btn bg-success ms-2">
                                                                            <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                                                data-size="lg"
                                                                                data-url="{{ URL::to('leave/' . $leave->id . '/action') }}"
                                                                                data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                                title="" data-title="{{ __('Leave Action') }}"
                                                                                data-bs-original-title="{{ __('Manage Leave') }}">
                                                                                <i class="ti ti-caret-right text-white"></i>
                                                                            </a>
                                                                        </div>
                                                                        @can('Edit Leave')
                                                                            <div class="action-btn bg-info ms-2">
                                                                                <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                                                    data-size="lg"
                                                                                    data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}"
                                                                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                                    title="" data-title="{{ __('Edit Leave') }}"
                                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endcan
                                                                        @can('Delete Leave')
                                                                            <div class="action-btn bg-danger ms-2">
                                                                                {!! Form::open([
                                                                                    'method' => 'DELETE',
                                                                                    'route' => ['leave.destroy', $leave->id],
                                                                                    'id' => 'delete-form-' . $leave->id,
                                                                                ]) !!}
                                                                                <a href="#"
                                                                                    class="mx-3 btn btn-sm  align-items-center bs-pass-para"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-bs-original-title="Delete" aria-label="Delete"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                </form>
                                                                            </div>
                                                                        @endcan
                                                                    @endif
                                                                @else
                                                                    <div class="action-btn bg-success ms-2">
                                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                                            data-size="lg"
                                                                            data-url="{{ URL::to('leave/' . $leave->id . '/action') }}"
                                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                            title="" data-title="{{ __('Leave Action') }}"
                                                                            data-bs-original-title="{{ __('Manage Leave') }}">
                                                                            <i class="ti ti-caret-right text-white"></i>
                                                                        </a>
                                                                    </div>
                                                                @endif

                                                            </span>
                                                        </td>
                                                    @endif
                                                @endif
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                            </div>
                            
                            <!-- Pending Leave Tab -->
                            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                    <table class="table" id="pc-dt-simple2">
                                        <thead>
                                            <tr>
                                                @if (\Auth::user()->type != 'employee')
                                                    <th class="text-start">{{ __('Employee') }}</th>
                                                @endif
                                                <th class="text-start">{{ __('Leave Type') }}</th>
                                                <th class="text-start">{{ __('Applied On') }}</th>
                                                <th class="text-start">{{ __('Leave Duration') }}</th>
                                                <th class="text-start">{{ __('Date(s)') }}</th>
                                                <th class="text-start">{{ __('status') }}</th>
                                                @if (\Auth::user()->type != 'employee' && strtolower(\Auth::user()->type) != 'director')
                                                    <th class="text-center" width="200px">{{ __('Action') }}</th>
                                                @endif    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($leaves as $leave)
                                                @if($leave->status == 'Pending')
                                                <tr data-leave-type="@if(!empty($leave->leave_type_id) && $leave->leaveType) {{ $leave->leaveType->title }} @else {{ __('N/A') }} @endif" data-month="{{ date('m', strtotime($leave->start_date)) }}" data-year="{{ date('Y', strtotime($leave->start_date)) }}" data-end-month="{{ date('m', strtotime($leave->end_date)) }}" data-end-year="{{ date('Y', strtotime($leave->end_date)) }}">
                                                    @if (\Auth::user()->type != 'employee')
                                                        <td class="text-start">{{ !empty($leave->employee_id) && $leave->employees ? trim($leave->employees->name . ' ' . $leave->employees->middle_name . ' ' . $leave->employees->last_name) : '' }}
                                                        </td>
                                                    @endif
                                                    <td class="text-start">
                                                        @if(!empty($leave->leave_type_id) && $leave->leaveType)
                                                            {{ $leave->leaveType->title }}
                                                        @else
                                                            {{ __('N/A') }}
                                                        @endif
                                                    </td>
                                                    <td class="text-start" data-order="{{ $leave->id }}">{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                                                    <td class="text-start">
                                                        @php
                                                            $durType = $leave->leave_duration_type ?? '';
                                                            $days = $leave->total_leave_days ?? 0;
                                                        @endphp
                                                        @if($durType == 'half_day')
                                                            <span class="badge bg-info">{{ __('Half Day') }}</span>
                                                            @if($leave->half_day_session)
                                                                <br><small class="text-muted">
                                                                    {{ $leave->half_day_session == 'first_half' ? __('First Half') : __('Second Half') }}
                                                                </small>
                                                            @endif
                                                        @elseif($durType == 'full_day')
                                                            @if($days != floor($days))
                                                                <span class="badge" style="background:#7c3aed;">{{ number_format($days,1) }} {{ __('Days') }}</span>
                                                            @else
                                                                <span class="badge bg-primary">{{ __('Full Day') }}</span>
                                                                @if($days > 1)
                                                                    <br><small class="text-muted">{{ number_format($days,0) }} {{ __('days') }}</small>
                                                                @endif
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary">{{ __('N/A') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-start">
                                                        @if(isset($leave->leave_duration_type) && $leave->leave_duration_type == 'half_day')
                                                            {{-- For half day, show date and session --}}
                                                            <div>
                                                                <strong>{{ \Auth::user()->dateFormat($leave->start_date) }}</strong>
                                                                @if($leave->half_day_session)
                                                                    <br><small class="badge bg-info">
                                                                        @if($leave->half_day_session == 'first_half')
                                                                            {{ __('First Half') }}
                                                                        @elseif($leave->half_day_session == 'second_half')
                                                                            {{ __('Second Half') }}
                                                                        @endif
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        @else
                                                            {{-- For full day, show date range --}}
                                                            @if($leave->start_date == $leave->end_date)
                                                                {{ \Auth::user()->dateFormat($leave->start_date) }}
                                                            @else
                                                                {{ \Auth::user()->dateFormat($leave->start_date) }} <br>
                                                                <small class="text-muted">to</small><br>
                                                                {{ \Auth::user()->dateFormat($leave->end_date) }}
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="text-start">
                                                        <div class="badge bg-warning p-2 px-3 rounded status-badge5">
                                                            {{ $leave->status }}</div>
                                                    </td>
                                                    @if (\Auth::user()->type != 'employee' && strtolower(\Auth::user()->type) != 'director')
                                                        <td class="text-center Action">
                                                            <span>
                                                                @if (\Auth::user()->type != 'employee')
                                                                    <div class="action-btn bg-success ms-2">
                                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                                            data-size="lg"
                                                                            data-url="{{ URL::to('leave/' . $leave->id . '/action') }}"
                                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                            title="" data-title="{{ __('Leave Action') }}"
                                                                            data-bs-original-title="{{ __('Manage Leave') }}">
                                                                            <i class="ti ti-caret-right text-white"></i>
                                                                        </a>
                                                                    </div>
                                                                    @can('Edit Leave')
                                                                        <div class="action-btn bg-info ms-2">
                                                                            <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                                                data-size="lg"
                                                                                data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}"
                                                                                data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                                title="" data-title="{{ __('Edit Leave') }}"
                                                                                data-bs-original-title="{{ __('Edit') }}">
                                                                                <i class="ti ti-pencil text-white"></i>
                                                                            </a>
                                                                        </div>
                                                                    @endcan
                                                                    @can('Delete Leave')
                                                                        <div class="action-btn bg-danger ms-2">
                                                                            {!! Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['leave.destroy', $leave->id],
                                                                                'id' => 'delete-form-' . $leave->id,
                                                                            ]) !!}
                                                                            <a href="#"
                                                                                class="mx-3 btn btn-sm  align-items-center bs-pass-para"
                                                                                data-bs-toggle="tooltip" title=""
                                                                                data-bs-original-title="Delete" aria-label="Delete"><i
                                                                                    class="ti ti-trash text-white text-white"></i></a>
                                                                            </form>
                                                                        </div>
                                                                    @endcan
                                                                @else
                                                                    <div class="action-btn bg-success ms-2">
                                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                                            data-size="lg"
                                                                            data-url="{{ URL::to('leave/' . $leave->id . '/action') }}"
                                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                            title="" data-title="{{ __('Leave Action') }}"
                                                                            data-bs-original-title="{{ __('Manage Leave') }}">
                                                                            <i class="ti ti-caret-right text-white"></i>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </span>
                                                        </td>
                                                    @endif
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).on('change', '#employee_id', function() {
            var employee_id = $(this).val();
            if (!employee_id) return;

            $.ajax({
                url: '{{ route('leave.jsoncount') }}',
                type: 'POST',
                data: {
                    "employee_id": employee_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    var oldval = $('#leave_type_id').val();
                    $('#leave_type_id').empty();
                    $('#leave_type_id').append(
                        '<option value="">{{ __('Select Leave Type') }}</option>');

                    $.each(data, function(key, value) {
                        var optionText = value.title;
                        var isUnlimited = value.unlimited == 1;
                        
                        if (isUnlimited) {
                            optionText += ' ({{ __("Unlimited") }})';
                        } else {
                            optionText += ' ({{ __("Available: ") }}' + parseFloat(value.available).toFixed(2) + ')';
                        }
                        
                        var option = $('<option></option>')
                            .attr('value', value.id)
                            .attr('data-unlimited', value.unlimited)
                            .attr('data-available', value.available)
                            .text(optionText);

                        if (oldval && oldval == value.id) {
                            option.attr('selected', 'selected');
                        }

                        $('#leave_type_id').append(option);
                    });
                    
                    $('#leave_type_id').trigger('change');
                }
            });
        });

        // Filter functionality for leave requests
        $(document).ready(function() {
            // Auto-apply filters when dropdown changes
            $('#month_filter, #year_filter').on('change', function() {
                $('#leave_filter_form').submit();
            });
            
            // Reset filters button click
            $('#reset_filters').on('click', function(e) {
                e.preventDefault();
                window.location.href = '{{ route('leave.index') }}';
            });
        });
    </script>
    
    <style>
        /* Mobile responsive tabs */
        @media (max-width: 768px) {
            .nav-tabs {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                border-bottom: 1px solid #dee2e6;
                margin-bottom: 0;
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .nav-tabs::-webkit-scrollbar {
                display: none;
            }
            
            .nav-tabs .nav-item {
                flex: 1;
                min-width: 120px;
            }
            
            .nav-tabs .nav-link {
                display: block;
                width: 100%;
                padding: 0.5rem 1rem;
                text-align: center;
                white-space: nowrap;
                border: 1px solid transparent;
                border-bottom: none;
                border-radius: 0.375rem 0.375rem 0 0;
                font-size: 0.875rem;
                color: #dc3545;
                background-color: #f8f9fa;
            }
            
            .nav-tabs .nav-link:hover {
                border-color: #e9ecef #e9ecef #dee2e6;
                color: #dc3545;
                background-color: #e9ecef;
            }
            
            .nav-tabs .nav-link.active {
                color: #dc3545;
                background-color: #fff;
                border-color: #dee2e6 #dee2e6 #fff;
            }
            
            /* Ensure tab content is responsive */
            .tab-content {
                overflow-x: auto;
            }
            
            .table-responsive {
                margin-bottom: 0;
            }
        }
        
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
        
        /* DataTables styles */
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }
        
        /* Ensure proper column width alignment */
        #pc-dt-simple th {
            min-width: 120px;
        }
        
        @if (\Auth::user()->type != 'employee' && strtolower(\Auth::user()->type) != 'director')
        #pc-dt-simple th:nth-child(1), #pc-dt-simple2 th:nth-child(1) { min-width: 200px; } /* Employee */
        #pc-dt-simple th:nth-child(2), #pc-dt-simple2 th:nth-child(2) { min-width: 160px; } /* Leave Type */
        #pc-dt-simple th:nth-child(3), #pc-dt-simple2 th:nth-child(3) { min-width: 140px; } /* Applied On */
        #pc-dt-simple th:nth-child(4), #pc-dt-simple2 th:nth-child(4) { min-width: 180px; } /* Leave Duration */
        #pc-dt-simple th:nth-child(5), #pc-dt-simple2 th:nth-child(5) { min-width: 200px; } /* Date(s) */
        #pc-dt-simple th:nth-child(6), #pc-dt-simple2 th:nth-child(6) { min-width: 80px; } /* Status */
        #pc-dt-simple th:nth-child(1), #pc-dt-simple2 th:nth-child(1) { min-width: 160px; } /* Leave Type */
        #pc-dt-simple th:nth-child(2), #pc-dt-simple2 th:nth-child(2) { min-width: 140px; } /* Applied On */
        #pc-dt-simple th:nth-child(3), #pc-dt-simple2 th:nth-child(3) { min-width: 180px; } /* Leave Duration */
        #pc-dt-simple th:nth-child(4), #pc-dt-simple2 th:nth-child(4) { min-width: 200px; } /* Date(s) */
        #pc-dt-simple th:nth-child(5), #pc-dt-simple2 th:nth-child(5) { min-width: 100px; } /* Status */
        #pc-dt-simple th:nth-child(6), #pc-dt-simple2 th:nth-child(6) { min-width: 160px; } /* Action */
        @endif


        /* Mobile margin for filter buttons */
        @media (max-width: 767px) {
            .filter-buttons-mobile {
                margin-top: 5px !important;
            }
        }
    </style>
@endpush
