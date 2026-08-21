@extends('layouts.admin')

@section('page-title')
    {{ __('Notice List') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Notice List') }}</li>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@endpush

@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
@endpush
@section('action-button')
    @if(Auth::user()->type != 'hr') {{-- Only show export and create for non-HR users --}}
        <div class="row align-items-center m-1">
            @can('Create Employee')
                <a href="#" data-size="lg" data-url="{{ route('notices.create') }}" data-ajax-popup="true"
                    data-bs-toggle="tooltip" title="{{ __('Create New Notice') }}" data-title="{{ __('Add New Notice') }}"
                    class="btn btn-sm btn-primary">
                    <i class="ti ti-plus"></i>
                </a>
            @endcan
        </div>
    @endif
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th class="text-start ">{{ __('Title') }}</th>
                                <th class="text-start">{{ __('Description') }}</th>
                                @if(Auth::user()->type != 'employee')
                                    <th class="text-start">{{ __('Start Date') }}</th>
                                    <th class="text-start">{{ __('End Date') }}</th>
                                @endif
                                @if (Auth::user()->type != 'hr')
                                    <th class="text-center" width="130px">{{ __('Actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notices as $notice)
                                <tr>
                                    <td class="text-start">{{ $notice->title }}</td>
                                    <td class="text-start">{{ Str::limit(strip_tags($notice->description), 50) }}</td>
                                    @if(Auth::user()->type != 'employee')
                                        <td class="text-start">{{ $notice->notice_startdate ? \Carbon\Carbon::parse($notice->notice_startdate)->format('d M Y') : '-' }}</td>
                                        <td class="text-start">{{ $notice->notice_enddate ? \Carbon\Carbon::parse($notice->notice_enddate)->format('d M Y') : '-' }}</td>
                                    @endif
                                    @if (Auth::user()->type != 'hr')
                                        <td class="text-center d-flex gap-2 justify-content-center">
                                            <!-- Show Button -->
                                            <a href="#" 
                                                class="btn btn-sm btn-warning text-white" 
                                                data-url="{{ route('notices.show', $notice->id) }}" 
                                                data-ajax-popup="true" 
                                                data-size="lg" 
                                                data-bs-toggle="tooltip" 
                                                data-title="{{ __('Notice Details') }}" 
                                                data-bs-original-title="{{ __('Show') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>

                                            @can('Edit Meeting')
                                                <!-- Edit Button -->
                                                <a href="#" 
                                                    class="btn btn-sm btn-info text-white" 
                                                    data-url="{{ route('notices.edit', $notice->id) }}" 
                                                    data-ajax-popup="true" 
                                                    data-size="lg" 
                                                    data-bs-toggle="tooltip" 
                                                    data-title="{{ __('Edit Notice') }}" 
                                                    data-bs-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil"></i>
                                                </a>
                                            @endcan

                                            @can('Delete Meeting')
                                                <!-- Delete Button with Form -->
                                                <form id="delete-form-{{ $notice->id }}" method="POST" action="{{ route('notices.destroy', $notice->id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <a href="#" class="btn btn-sm btn-danger text-white"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ __('Delete Notice') }}"
                                                    onclick="event.preventDefault(); document.getElementById('delete-form-{{ $notice->id }}').submit();">
                                                    <i class="ti ti-trash text-white"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
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
    /* Mobile responsive for all columns */
    @media (max-width: 767px) {
        #pc-dt-simple th:nth-child(1) {
            min-width: 250px !important; /* Title - increased for mobile */
            width: 250px !important;
        }
        
        #pc-dt-simple td:nth-child(1) {
            min-width: 250px !important; /* Title - increased for mobile */
            width: 250px !important;
        }
        
        #pc-dt-simple th:nth-child(2) {
            min-width: 200px !important; /* Description - increased for mobile */
            width: 200px !important;
        }
        
        #pc-dt-simple td:nth-child(2) {
            min-width: 200px !important; /* Description - increased for mobile */
            width: 200px !important;
        }
        
        #pc-dt-simple th:nth-child(3) {
            min-width: 150px !important; /* Start Date - increased for mobile */
            width: 150px !important;
        }
        
        #pc-dt-simple td:nth-child(3) {
            min-width: 150px !important; /* Start Date - increased for mobile */
            width: 150px !important;
        }
        
        #pc-dt-simple th:nth-child(4) {
            min-width: 150px !important; /* End Date - increased for mobile */
            width: 150px !important;
        }
        
        #pc-dt-simple td:nth-child(4) {
            min-width: 150px !important; /* End Date - increased for mobile */
            width: 150px !important;
        }
        
        #pc-dt-simple th:nth-child(5) {
            min-width: 150px !important; /* Actions - increased for mobile */
            width: 150px !important;
        }
        
        #pc-dt-simple td:nth-child(5) {
            min-width: 150px !important; /* Actions - increased for mobile */
            width: 150px !important;
        }
    }
</style>

@endpush