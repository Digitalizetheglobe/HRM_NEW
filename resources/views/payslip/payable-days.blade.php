@extends('layouts.admin')

@section('page-title')
    {{ __('Payable Days') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Payable Days') }}</li>
@endsection

@section('content')
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <div class="d-flex align-items-center justify-content-start">
                            <h5>{{ __('Payable Days') }}</h5>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex justify-content-end align-items-end gap-2">
                            <div class="payable-control-wrapper">
                                <label class="form-label">{{ __('Month') }}</label>
                                <select class="form-control month_date" name="month" tabindex="-1"
                                    aria-hidden="true" style="min-width: 120px;">
                                    <option value="--">--</option>
                                    @foreach ($month as $k => $mon)
                                        @php
                                            $selected = date('m') == $k ? 'selected' : '';
                                        @endphp
                                        <option value="{{ $k }}" {{ $selected }}>{{ $mon }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="payable-control-wrapper">
                                <label class="form-label">{{ __('Year') }}</label>
                                {{ Form::select('year', $year, date('Y'), ['class' => 'form-control year_date', 'style' => 'min-width: 100px;']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-header card-body table-border-style">
                <div class="table-responsive">
                    <table class="table" id="pc-dt-render-column-cells">
                        <thead>
                            <tr>
                                <th>{{ __('Employee Id') }}</th>
                                @if (\Auth::user()->type != 'employee')
                                    <th>{{ __('Name') }}</th>
                                @endif
                                <th>{{ __('Payable Days') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <style>
        /* Column width adjustments */
        #pc-dt-render-column-cells th:nth-child(1) {
            min-width: 200px; /* Employee Id - increased width */
        }
        
        @if (\Auth::user()->type != 'employee')
            #pc-dt-render-column-cells th:nth-child(3) {
                min-width: 150px; /* Payable Days - increased width */
            }
        @else
            #pc-dt-render-column-cells th:nth-child(2) {
                min-width: 150px; /* Payable Days - increased width */
            }
        @endif
        
        /* Mobile responsiveness for payable control wrapper */
        @media (max-width: 767px) {
            .payable-control-wrapper {
                flex: 1 1 100%;
                margin-bottom: 10px;
            }
            
            .d-flex.justify-content-end.align-items-end {
                flex-direction: column;
                align-items: stretch !important;
            }
        }
    </style>
    <script>
        $(document).ready(function() {
            callback();

            function callback() {
                var month = $(".month_date").val();
                var year = $(".year_date").val();

                if (month == '' || month == '--') {
                    month = '{{ date('m') }}';
                    year = '{{ date('Y') }}';
                }

                var datePicker = year + '-' + month;

                $.ajax({
                    url: '{{ route('payable-days.search_json') }}',
                    type: 'POST',
                    data: {
                        "datePicker": datePicker,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        console.clear();
                        var tr = '';
                        if (data.length > 0) {
                            $.each(data, function(indexInArray, valueOfElement) {
                                var url_employee = valueOfElement['url'];

                                @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'hr')
                                    // Array structure: [id, employee_id_formatted, name, payable_days]
                                    var employeeId = valueOfElement[1];
                                    var name = valueOfElement[2];
                                    var payableDays = valueOfElement[3] || 0;
                                    tr +=
                                        '<tr>' +
                                        '<td> <a class="btn btn-outline-primary" href="' +
                                        url_employee + '">' + employeeId + '</a></td> ' +
                                        '<td>' + name + '</td> ' +
                                        '<td>' + payableDays + '</td>' +
                                        '<td><a href="' + url_employee + '" class="btn btn-primary btn-sm">{{ __('View') }}</a></td>' +
                                        '</tr>';
                                @else
                                    // Array structure: [id, employee_id_formatted, payable_days]
                                    var employeeId = valueOfElement[1];
                                    var payableDays = valueOfElement[2] || 0;
                                    tr +=
                                        '<tr>' +
                                        '<td> <a class="btn btn-outline-primary" href="' +
                                        url_employee + '">' + employeeId + '</a></td> ' +
                                        '<td>' + payableDays + '</td>' +
                                        '<td><a href="' + url_employee + '" class="btn btn-primary btn-sm">{{ __('View') }}</a></td>' +
                                        '</tr>';
                                @endif
                            });
                        } else {
                            var colspan = $('#pc-dt-render-column-cells thead tr th').length;
                            var tr = '<tr><td class="dataTables-empty" colspan="' + colspan +
                                '">{{ __('No entries found') }}</td></tr>';
                        }

                        $('#pc-dt-render-column-cells tbody').html(tr);
                        var table = document.querySelector("#pc-dt-render-column-cells");
                        var datatable = new simpleDatatables.DataTable(table);
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            }

            $(document).on("change", ".month_date,.year_date", function() {
                callback();
            });
        });
    </script>
@endpush

