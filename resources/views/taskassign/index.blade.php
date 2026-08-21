@extends('layouts.admin')

@section('page-title')
    {{ __('To Do List') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Employee') }}</li>
@endsection

@section('action-button')
    <a href="#" data-url="{{ route('todo.create') }}" data-ajax-popup="true"
        data-title="{{ __('Create New ToDo') }}" data-size="lg" data-bs-toggle="tooltip" title="Create"
        class="btn btn-sm btn-primary">
        <i class="ti ti-plus"></i>
    </a>
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
                                <th class="text-start">{{ __('Task Title') }}</th>
                                <th class="text-start">{{ __('Priority') }}</th>
                                <th class="text-start">{{ __('Due Date') }}</th>
                                <th class="text-start">{{ __('Status') }}</th>
                                <th class="text-center" width="130px">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td class="text-start">{{ $task->task }}</td>
                                    <td class="text-start">
                                        @if ($task->priority == 'high')
                                            <span class="badge bg-danger">{{ __('High') }}</span>
                                        @elseif ($task->priority == 'medium')
                                            <span class="badge bg-warning">{{ __('Medium') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('Low') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-start">{{ $task->expires_at ? \Carbon\Carbon::parse($task->expires_at)->format('Y-m-d') : __('No Deadline') }}</td>
                                    <td class="text-start">
                                        @if ($task->is_completed)
                                            <span class="badge bg-success">{{ __('Completed') }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                    <div class="action-btn bg-info ms-2">
                                        <a href="#" class="mx-3 btn btn-sm align-items-center"
                                            data-url="{{ route('todo.edit', $task->id) }}"
                                            data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                            data-title="{{ __('Edit ToDo') }}" data-bs-original-title="{{ __('Edit') }}">
                                            <i class="ti ti-pencil text-white"></i>
                                        </a>
                                    </div>
                                    <div class="action-btn bg-danger ms-2">
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'route' => ['todo.destroy', $task->id],
                                            
                                        ]) !!}
                                        <a href="#"
                                            class="mx-3 btn btn-sm align-items-center bs-pass-para"
                                            data-bs-toggle="tooltip" title="{{ __('Delete Task') }}"
                                            data-bs-original-title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $task->id }}').submit();">
                                            <i class="ti ti-trash text-white"></i>
                                        </a>
                                        {!! Form::close() !!}
                                    </div>
  
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
@endsection

@push('scripts')
    <style>
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
        
        /* Fix DataTables sorting icons alignment */
        .dataTables_wrapper .dataTables_scrollHead .table th {
            position: relative;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting:after,
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_asc:after,
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_desc:after {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: 8px !important;
            margin-top: 0 !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_asc:after {
            content: "↑" !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting_desc:after {
            content: "↓" !important;
        }
        
        .dataTables_wrapper .dataTables_scrollHead .table th.sorting:after {
            content: "↕" !important;
            opacity: 0.3;
        }
        
        /* Ensure proper column width alignment */
        #pc-dt-simple th {
            min-width: 120px;
        }
        
        #pc-dt-simple th:nth-child(1) {
            min-width: 250px; /* Task Title */
        }
        
        #pc-dt-simple th:nth-child(2) {
            min-width: 120px; /* Priority */
        }
        
        #pc-dt-simple th:nth-child(3) {
            min-width: 130px; /* Due Date */
        }
        
        #pc-dt-simple th:nth-child(4) {
            min-width: 120px; /* Status */
        }
        
        #pc-dt-simple th:nth-child(5) {
            min-width: 130px; /* Actions */
        }
    </style>
    <script>
        $(document).ready(function() {
            $('#pc-dt-simple').DataTable({
                autoWidth: false,
                columnDefs: [
                    { targets: 0, width: "250px" },  // Task Title
                    { targets: 1, width: "120px" },  // Priority
                    { targets: 2, width: "130px" },  // Due Date
                    { targets: 3, width: "120px" },  // Status
                    { targets: 4, width: "130px" }   // Actions
                ],
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    emptyTable: "No entries found"
                }
            });
        });
    </script>
@endpush
