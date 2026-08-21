@extends('layouts.admin')

@section('page-title')
    {{ __('Employee') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Manage Employee') }}</li>
@endsection

@section('action-button')
    <div class="d-flex flex-wrap justify-content-md-end align-items-center gap-2">

        {{-- Show Edit button: Company users can always edit, employees can only edit themselves if not approved --}}
        @if(\Auth::user()->type !== 'employee')
            {{-- Company users can edit any employee --}}
            <a href="{{ route('employee.edit', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}"
                data-bs-toggle="tooltip" title="{{ __('Edit') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-pencil"></i> {{ __('Edit') }}
            </a>
        @elseif(\Auth::user()->type === 'employee' && \Auth::user()->id === $employee->user_id && $employee->approval_status !== 'approved')
            {{-- Employees can only edit themselves if not approved --}}
            <a href="{{ route('employee.edit', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}"
                data-bs-toggle="tooltip" title="{{ __('Edit') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-pencil"></i> {{ __('Edit') }}
            </a>
        @endif
        
        {{-- Show approval buttons for company users --}}
        @if(\Auth::user()->type !== 'employee' && ($employee->approval_status === 'pending' || empty($employee->approval_status)))
            <button type="button" class="btn btn-sm btn-success" 
                data-bs-toggle="modal" data-bs-target="#approveModal">
                <i class="ti ti-check"></i> {{ __('Approve') }}
            </button>
            
            <button type="button" class="btn btn-sm btn-danger" 
                data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="ti ti-x"></i> {{ __('Reject') }}
            </button>
        @endif
        
        {{-- Show request approval button for employees when rejected --}}
        @if(\Auth::user()->type === 'employee' && $employee->approval_status === 'rejected')
            <form action="{{ route('employee.request-approval', $employee->id) }}" method="POST" class="d-inline-block">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning">
                    <i class="ti ti-refresh"></i> {{ __('Request Approval Again') }}
                </button>
            </form>
        @endif
        
        {{-- Offer Letter direct download buttons --}}
        <a href="#" 
            data-url="{{ route('joiningletter.download.pdf', $employee->id) }}"
            data-size="xl"
            class="btn btn-sm btn-info" 
            data-ajax-popup="true"
            data-title="{{ __('Offer Letter') }}"
            data-bs-toggle="tooltip" 
            data-bs-placement="top" 
            title="{{ __('View Offer Letter') }}">
            <i class="ti ti-eye"></i> {{ __('Offer Letter') }}
        </a>

    </div>
@endsection

@section('content')
    {{-- Approval Status Alert --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="alert alert-@if($employee->approval_status === 'approved')success
                                @elseif($employee->approval_status === 'rejected')danger
                                @elsewarning @endif">
                <strong>{{ __('Approval Status') }}:</strong> 
                {{ ucfirst($employee->approval_status ?? 'pending') }}
                
                @if($employee->approval_status === 'approved' && $employee->approved_at)
                    <br><small>{{ __('Approved on') }}: {{ \Auth::user()->dateFormat($employee->approved_at) }} 
                    @if($employee->approvedBy) by {{ $employee->approvedBy->name }} @endif</small>
                @endif
                
                @if($employee->approval_status === 'rejected' && $employee->rejection_reason)
                    <br><small>{{ __('Reason') }}: {{ $employee->rejection_reason }}</small>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Personal & Company Details -->
        <div class="col-sm-12 col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-0 pb-0">
                    <h5 class="text-primary"><i class="ti ti-user me-2"></i>{{ __('Personal Details') }}</h5>
                    <hr>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Employee ID') }}</small>
                            <span class="fw-bold text-dark">{{ $employeesId }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Name') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->full_name }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Email') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->email }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Phone') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->phone }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Emergency Number') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->emergency_number ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Date of Birth') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->dob ? \Auth::user()->dateFormat($employee->dob) : __('Not Set') }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Blood Group') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->blood_group ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Gender') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->gender }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Address') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->address ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-0 pb-0">
                    <h5 class="text-primary"><i class="ti ti-building me-2"></i>{{ __('Company Details') }}</h5>
                    <hr>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Branch') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->branch->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Department') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->department->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Designation') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->designation->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Office Shift') }}</small>
                            <span class="fw-bold text-dark">
                                @if($employee->office_shift == 'First Shift')
                                    First Shift – 10:30 AM to 6:30 PM
                                @elseif($employee->office_shift == 'Second Shift')
                                    Second Shift – 12:00 PM to 8:00 PM
                                @else
                                    {{ $employee->office_shift ?? 'N/A' }}
                                @endif
                            </span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Date of Joining') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->company_doj ? \Auth::user()->dateFormat($employee->company_doj) : __('Not Set') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Account & Documents -->
        <div class="col-sm-12 col-md-6 mt-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-0 pb-0">
                    <h5 class="text-primary"><i class="ti ti-building-bank me-2"></i>{{ __('Bank Account Details') }}</h5>
                    <hr>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Account Holder Name') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->account_holder_name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Account Number') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->account_number ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Bank Name') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->bank_name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('IFSC Code') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->bank_identifier_code ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Branch Location') }}</small>
                            <span class="fw-bold text-dark">{{ $employee->branch_location ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Account Type') }}</small>
                            <span class="fw-bold text-dark">
                                @if(!empty($employee->account_type))
                                    @if($employee->account_type == 'salary_account')
                                        {{ __('Salary Account') }}
                                    @elseif($employee->account_type == 'savings_account')
                                        {{ __('Savings Account') }}
                                    @elseif($employee->account_type == 'Salary account' || $employee->account_type == 'Saving account')
                                        {{ $employee->account_type }}
                                    @elseif(is_numeric($employee->account_type))
                                        @php
                                            if($employee->account_type == 0 || $employee->account_type == '0') {
                                                echo __('Salary Account');
                                            } elseif($employee->account_type == 1 || $employee->account_type == '1') {
                                                echo __('Savings Account');
                                            } else {
                                                try {
                                                    $accountTypeName = $employee->account_type();
                                                    echo $accountTypeName ?: 'N/A';
                                                } catch (\Exception $e) {
                                                    echo 'N/A';
                                                }
                                            }
                                        @endphp
                                    @else
                                        {{ ucfirst(str_replace(['_', '-'], ' ', $employee->account_type)) }}
                                    @endif
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6 mt-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-0 pb-0">
                    <h5 class="text-primary"><i class="ti ti-file-text me-2"></i>{{ __('Document Detail') }}</h5>
                    <hr>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        @php
                            $employeedoc = $employee->documents()->pluck('document_value', 'document_id');
                        @endphp
                        @if (!$documents->isEmpty())
                            @foreach ($documents as $key => $document)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-between border p-2 rounded">
                                        <span class="fw-bold text-sm text-dark">{{ $document->name }}</span>
                                        @if(!empty($employeedoc[$document->id]))
                                            <a href="#" data-doc-url="{{ asset($employeedoc[$document->id]) }}" class="btn btn-sm btn-outline-primary shadow-none view-document-btn">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                        @else
                                            <span class="badge bg-secondary text-white">Missing</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center text-muted">
                                No Document Type Added!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Experience Detail -->
        <div class="col-sm-12 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0 pb-0">
                    <h5 class="text-primary"><i class="ti ti-briefcase me-2"></i>{{ __('Experience Detail') }}</h5>
                    <hr>
                </div>
                <div class="card-body pt-0">
                    @if(!empty($experienceDetails))
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Designation</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Previous Salary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($experienceDetails as $exp)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $exp['previous_company_name'] ?? '-' }}</td>
                                            <td class="text-dark">{{ $exp['previous_designation'] ?? '-' }}</td>
                                            <td class="text-dark">{{ $exp['start_date'] ?? '-' }}</td>
                                            <td class="text-dark">{{ $exp['end_date'] ?? '-' }}</td>
                                            <td class="text-dark">{{ $exp['previous_salary'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <p>No experience detail available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Education Details -->
        <div class="col-sm-12 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0 pb-0">
                    <h5 class="text-primary"><i class="ti ti-school me-2"></i>{{ __('Education Details') }}</h5>
                    <hr>
                </div>
                <div class="card-body pt-0">
                    @if(!empty($educationDetails))
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Degree</th>
                                        <th>College Name</th>
                                        <th>Passing Year</th>
                                        <th>Grade</th>
                                        <th>Document</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($educationDetails as $edu)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $edu['degree'] ?? '-' }}</td>
                                            <td class="text-dark">{{ $edu['college_name'] ?? '-' }}</td>
                                            <td class="text-dark">{{ $edu['passing_year'] ?? '-' }}</td>
                                            <td class="text-dark">{{ $edu['grade'] ?? '-' }}</td>
                                            <td>
                                                @if(isset($edu['document_path']))
                                                    <a href="#" data-doc-url="{{ asset($edu['document_path']) }}" class="btn btn-sm btn-outline-primary shadow-none view-document-btn">
                                                        <i class="ti ti-eye me-1"></i>View
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <p>No education details available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Approval Modals --}}
    @if(\Auth::user()->type !== 'employee')
        <!-- Approve Modal -->
        <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveModalLabel">{{ __('Approve Employee Details') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Are you sure you want to approve this employee\'s details?') }}</p>
                        <p>{{ __('Once approved, the employee will not be able to edit their information.') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <form action="{{ route('employee.approve', $employee->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">{{ __('Approve') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">{{ __('Reject Employee Details') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('employee.reject', $employee->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p>{{ __('Please provide a reason for rejecting this employee\'s details:') }}</p>
                            <div class="form-group">
                                <textarea name="rejection_reason" class="form-control" rows="3" required 
                                          placeholder="{{ __('Enter rejection reason...') }}"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('Reject') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Document Viewer Modal -->
    <div class="modal fade" id="documentViewerModal" tabindex="-1" aria-labelledby="documentViewerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-md-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentViewerModalLabel">{{ __('Document Preview') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 d-flex align-items-center justify-content-center" style="background-color: #e9ecef; min-height: 50vh;">
                    <div class="w-100 h-100 p-2 p-md-4 d-flex align-items-center justify-content-center">
                        <img id="documentPreviewImage" src="" alt="Document" class="img-fluid rounded shadow-sm" style="max-height: 80vh; object-fit: contain; display: none;">
                        <iframe id="documentPreviewPdf" src="" style="width: 100%; height: 80vh; border: none; display: none; background: #fff;"></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="documentDownloadBtn" href="" download class="btn btn-primary"><i class="ti ti-download me-1"></i> <span class="d-none d-sm-inline">{{ __('Download') }}</span></a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    @if(\Auth::user()->type === 'employee' && $employee->approval_status !== 'approved')
        <script>
            // Poll for approval status every 5 seconds
            setInterval(function() {
                $.ajax({
                    url: "{{ route('employee.check_approval') }}",
                    type: "GET",
                    success: function(response) {
                        if (response.status === 'approved') {
                            window.location.href = "{{ route('dashboard') }}";
                        }
                    }
                });
            }, 5000);
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $('.view-document-btn').on('click', function(e) {
                e.preventDefault();
                var docUrl = $(this).data('doc-url');
                
                
                var docUrlLower = docUrl.toLowerCase();
                // Determine if it is a PDF
                var isPdf = docUrlLower.endsWith('.pdf') || docUrlLower.includes('/pdf/') || $(this).data('is-pdf') === true;
                
                // Determine if it is an Office document that browsers auto-download
                var isOfficeDoc = docUrlLower.endsWith('.doc') || docUrlLower.endsWith('.docx') || 
                                  docUrlLower.endsWith('.xls') || docUrlLower.endsWith('.xlsx') || 
                                  docUrlLower.endsWith('.ppt') || docUrlLower.endsWith('.pptx') || 
                                  docUrlLower.endsWith('.csv');
                
                $('#documentDownloadBtn').attr('href', docUrl);
                
                if (isPdf || isOfficeDoc) {
                    $('#documentPreviewImage').hide();
                    // Use Google Docs Viewer to render PDFs and office files inside the iframe
                    // This prevents Android WebViews and Mobile browsers from auto-downloading the file
                    var googleViewerUrl = 'https://docs.google.com/gview?url=' + encodeURIComponent(docUrl) + '&embedded=true';
                    $('#documentPreviewPdf').attr('src', googleViewerUrl).show();
                } else {
                    $('#documentPreviewPdf').hide();
                    $('#documentPreviewImage').attr('src', docUrl).show();
                }
                
                $('#documentViewerModal').modal('show');
            });
            
            // Clear iframe src on close to stop loading/audio if any
            $('#documentViewerModal').on('hidden.bs.modal', function () {
                $('#documentPreviewPdf').attr('src', '');
                $('#documentPreviewImage').attr('src', '');
            });
        });
    </script>
@endpush