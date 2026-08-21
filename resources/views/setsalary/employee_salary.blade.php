@extends('layouts.admin')

@section('page-title')
    {{ __('Employee Set Salary') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ url('setsalary') }}">{{ __('Set Salary') }}</a></li>
    <li class="breadcrumb-item">{{ __('Employee Set Salary') }}</li>
@endsection

@section('content')
    <div class="row">

        <div class="col-12">
            <div class="row">
                <div class="col-xl-6">
                    <div class="card set-card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-11">
                                    {{-- <h5>{{ __('Employee Salary') }}</h5> --}}
                                    <h5>{{ __('Employee Salary') }}</h5>

                                </div>
                                @can('Create Set Salary')
                                    <div class="col-1 text-end">

                                        <a data-url="{{ route('employee.basic.salary', $employee->id) }}" data-ajax-popup="true"
                                            data-title="{{ __('Set Basic Salary') }}" data-bs-toggle="tooltip" title=""
                                            class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Set Salary') }}">
                                            <i class="ti ti-plus"></i>
                                        </a>

                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="project-info d-flex text-sm">
                                <div class="project-info-inner mr-3 col-11">
                                    <b class="m-0"> {{ __('Payslip Type') }} </b>
                                    <div class="project-amnt pt-1">{{ $employee->salary_type() }}</div>
                                </div>
                                <div class="project-info-inner mr-3 col-1">
                                    <b class="m-0"> {{ __('Salary') }} </b>
                                    <div class="project-amnt pt-1">{{ $employee->salary }}</div>
                                </div>
                            </div>
                            <!--  -->
                        </div>
                    </div>
                </div>

              
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script type="text/javascript">
        $(document).on('change', '.amount_type', function() {

            var val = $(this).val();
            var label_text = 'Amount';
            if (val == 'percentage') {
                var label_text = 'Percentage';
            }
            $('.amount_label').html(label_text);
        });


        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });



        function getDesignation(did) {
            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#designation_id').empty();
                    $('#designation_id').append(
                        '<option value="">{{ __('Select any Designation') }}</option>');
                    $.each(data, function(key, value) {
                        var select = '';
                        if (key == '{{ $employee->designation_id }}') {
                            select = 'selected';
                        }

                        $('#designation_id').append('<option value="' + key + '"  ' + select + '>' +
                            value + '</option>');
                    });
                }
            });
        }
    </script>
@endpush
