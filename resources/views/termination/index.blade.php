@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Exit Formalities') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Exit Formalities') }}</li>
@endsection


@section('action-button')
    @can('Create Termination')
        <a href="#" data-url="{{ route('termination.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create New Relieve') }}" data-size="lg" data-bs-toggle="tooltip" title=""
            class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection

@section('content')
    <div class="row">

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    {{-- <h5> </h5> --}}
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    @role('company')
                                        <th>{{ __('Employee Name') }}</th>
                                    @endrole
                                    <th>{{ __('Relieve Type') }}</th>
                                    <th>{{ __('Notice Date') }}</th>
                                    <th>{{ __('Relieve Date') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    @if (Gate::check('Edit Relieve') || Gate::check('Delete Relieve'))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($terminations as $termination)
                                    <tr>
                                        @role('company')
                                            <td>{{ !empty($termination->employee_id) ? $termination->employee->full_name : '' }}
                                            </td>
                                        @endrole

                                        <td>{{ !empty($termination->termination_type) ? $termination->terminationType->name : '' }}
                                        </td>
                                        <td>{{ \Auth::user()->dateFormat($termination->notice_date) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($termination->termination_date) }}</td>
                                        <td><a href="#" class="action-item"
                                                data-url="{{ route('termination.description', $termination->id) }}"
                                                data-ajax-popup="true" data-bs-toggle="tooltip"
                                                title="{{ __('Desciption') }}" data-title="{{ __('Desciption') }}"><i
                                                    class="icon_desc fa fa-comment"></i></a>
                                        </td>
                                        <td class="Action">
                                            @if (Gate::check('Edit Termination') || Gate::check('Delete Termination'))
                                                <span>
                                                    @can('Edit Termination')
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                                data-size="lg"
                                                                data-url="{{ URL::to('termination/' . $termination->id . '/edit') }}"
                                                                data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('Edit Termination') }}"
                                                                data-bs-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endcan

                                                    @can('Delete Termination')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['termination.destroy', $termination->id],
                                                                'id' => 'delete-form-' . $termination->id,
                                                            ]) !!}
                                                            <a href="#"
                                                                class="mx-3 btn btn-sm  align-items-center bs-pass-para"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"><i
                                                                    class="ti ti-trash text-white text-white"></i></a>
                                                            </form>
                                                        </div>
                                                    @endcan
                                                </span>
                                            @endif
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
@push('scripts')
    <style>
        /* Increase all termination table column widths */
        #pc-dt-simple th:nth-child(1) {
            min-width: 220px; /* Employee Name - increased */
        }
        
        #pc-dt-simple th:nth-child(2) {
            min-width: 180px; /* Relieve Type - increased */
        }
        
        #pc-dt-simple th:nth-child(3) {
            min-width: 160px; /* Notice Date - increased */
        }
        
        #pc-dt-simple th:nth-child(4) {
            min-width: 160px; /* Relieve Date - increased */
        }
        
        #pc-dt-simple th:nth-child(5) {
            min-width: 200px; /* Description - increased */
        }
        
        #pc-dt-simple th:nth-child(6) {
            min-width: 220px; /* Action - increased */
        }
    </style>
@endpush
@endsection
