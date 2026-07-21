@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a>
            <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right" style="font-size:9px;"></i></span>
            <a href="{{ route('employees.index') }}">Employee</a>
            <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right" style="font-size:9px;"></i></span>
            <span id="breadcrumbCurrent">Employee Profile</span>
        </div>
        <div class="page-title" id="pageTitle">Employee Profile — {{ $employee->name }}</div>
    </div>
    <div class="page-actions" id="pageActions">
        <a href="{{ route('employees.index') }}" class="btn-outline-sm">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
        <a href="{{ route('employees.edit', $employee->id) }}" class="wbtn wbtn-primary" style="padding: 8px 16px;">
            <i class="fa-solid fa-pencil"></i> Edit Profile
        </a>
    </div>
</div>

<!-- Profile Content Wrapper -->
<div class="content content-single" style="padding-top: 20px;">
    @php
        $emp = $employee->employee;
        $branchName = $emp && $emp->branch ? $emp->branch->name : '—';
        $deptName = $emp && $emp->department ? $emp->department->name : '—';
        $desigName = $emp && $emp->designation ? $emp->designation->name : '—';
        $joiningDate = $emp && $emp->joining_date ? $emp->joining_date->format('M d, Y') : '—';
        $dob = $emp && $emp->dob ? \Carbon\Carbon::parse($emp->dob)->format('M d, Y') : '—';
        $gender = $emp && $emp->gender ? ucfirst($emp->gender) : '—';
        $uid = $emp ? $emp->employee_uid : '#DTG' . str_pad($employee->id, 3, '0', STR_PAD_LEFT);
        
        $genderSlug = $emp ? $emp->gender : 'male';
        $avatarBg = $genderSlug === 'female' 
            ? 'linear-gradient(135deg, #ec4899, #f43f5e)' 
            : 'linear-gradient(135deg, #6366f1, #8b5cf6)';
    @endphp

    <div class="profile-layout-wrap">
        <!-- Sidebar Card -->
        <div class="profile-sidebar-card">
            <!-- Profile Avatar and Badge -->
            <div class="profile-avatar-container">
                <div class="profile-avatar-large" style="background: {{ $avatarBg }};">
                    @if($employee->avatar)
                        <img src="{{ asset('storage/' . $employee->avatar) }}" alt="{{ $employee->name }}">
                    @else
                        {{ strtoupper(substr($employee->name, 0, 1)) }}{{ strtoupper(substr(strrchr($employee->name, ' ') ?: $employee->name, 1, 1)) }}
                    @endif
                </div>
                <span class="status-badge {{ $employee->is_active ? 'active' : 'inactive' }}">
                    {{ $employee->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <!-- Basic Info -->
            <h2 class="profile-name">{{ $employee->name }}</h2>
            <div class="profile-uid">{{ $uid }}</div>
            <div class="profile-meta-role">
                <i class="fa-solid fa-user-shield"></i> {{ $desigName }}
            </div>

            <div class="profile-info-divider"></div>

            <!-- Contact Information List -->
            <div class="profile-contact-list">
                <div class="contact-item">
                    <span class="contact-icon"><i class="fa-regular fa-envelope"></i></span>
                    <div>
                        <div class="contact-label">Email Address</div>
                        <div class="contact-value" title="{{ $employee->email }}">{{ $employee->email }}</div>
                    </div>
                </div>
                <div class="contact-item">
                    <span class="contact-icon"><i class="fa-solid fa-phone"></i></span>
                    <div>
                        <div class="contact-label">Phone Number</div>
                        <div class="contact-value">{{ $emp && $emp->phone ? $emp->phone : '—' }}</div>
                    </div>
                </div>
                <div class="contact-item">
                    <span class="contact-icon"><i class="fa-solid fa-map-location-dot"></i></span>
                    <div>
                        <div class="contact-label">Residential Address</div>
                        <div class="contact-value font-small">{{ $emp && $emp->address ? $emp->address : '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Details Sections -->
        <div class="profile-main-content">
            <!-- Company details section -->
            <div class="profile-details-section">
                <div class="profile-section-header">
                    <div class="p-section-icon" style="background: linear-gradient(135deg, #2563eb, #3b82f6);"><i class="fa-solid fa-building"></i></div>
                    <div>
                        <h3 class="p-section-title">Company Assignment</h3>
                        <p class="p-section-sub">Corporate alignment & salary parameters</p>
                    </div>
                </div>
                <div class="p-section-body">
                    <div class="p-details-grid">
                        <div class="p-detail-item">
                            <span class="p-detail-label">Select Branch</span>
                            <span class="p-detail-value">{{ $branchName }}</span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">Department</span>
                            <span class="p-detail-value">{{ $deptName }}</span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">Designation</span>
                            <span class="p-detail-value">{{ $desigName }}</span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">Salary Model</span>
                            <span class="p-detail-value">{{ $emp && $emp->salary_type ? $emp->salary_type : '—' }}</span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">Basic Salary</span>
                            <span class="p-detail-value salary-highlight">
                                {{ $emp && $emp->basic_salary ? '₹' . number_format($emp->basic_salary, 2) : '₹0.00' }}
                            </span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">Date of Joining</span>
                            <span class="p-detail-value">{{ $joiningDate }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal info section -->
            <div class="profile-details-section">
                <div class="profile-section-header">
                    <div class="p-section-icon" style="background: linear-gradient(135deg, #7c3aed, #a855f7);"><i class="fa-solid fa-user"></i></div>
                    <div>
                        <h3 class="p-section-title">Personal Information</h3>
                        <p class="p-section-sub">Birth date and biological details</p>
                    </div>
                </div>
                <div class="p-section-body">
                    <div class="p-details-grid">
                        <div class="p-detail-item">
                            <span class="p-detail-label">Date of Birth</span>
                            <span class="p-detail-value">{{ $dob }}</span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">Gender</span>
                            <span class="p-detail-value">{{ $gender }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank account details section -->
            <div class="profile-details-section">
                <div class="profile-section-header">
                    <div class="p-section-icon" style="background: linear-gradient(135deg, #10b981, #34d399);"><i class="fa-solid fa-credit-card"></i></div>
                    <div>
                        <h3 class="p-section-title">Bank & Financial Account</h3>
                        <p class="p-section-sub">Payout configurations and TAX registers</p>
                    </div>
                </div>
                <div class="p-section-body">
                    <div class="p-details-grid">
                        <div class="p-detail-item">
                            <span class="p-detail-label">Account Holder Name</span>
                            <span class="p-detail-value">{{ $emp && $emp->account_holder_name ? $emp->account_holder_name : '—' }}</span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">Bank Name</span>
                            <span class="p-detail-value">{{ $emp && $emp->bank_name ? $emp->bank_name : '—' }}</span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">Account Number</span>
                            <span class="p-detail-value font-mono">{{ $emp && $emp->account_number ? $emp->account_number : '—' }}</span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">Bank Branch</span>
                            <span class="p-detail-value">{{ $emp && $emp->bank_branch ? $emp->bank_branch : '—' }}</span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">IFSC Code</span>
                            <span class="p-detail-value font-mono">{{ $emp && $emp->ifsc_code ? $emp->ifsc_code : '—' }}</span>
                        </div>
                        <div class="p-detail-item">
                            <span class="p-detail-label">PAN Card Number</span>
                            <span class="p-detail-value font-mono">{{ $emp && $emp->pan_number ? $emp->pan_number : '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uploaded Documents Hub -->
            <div class="profile-details-section">
                <div class="profile-section-header">
                    <div class="p-section-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);"><i class="fa-solid fa-folder-open"></i></div>
                    <div>
                        <h3 class="p-section-title">Official Document Attachments</h3>
                        <p class="p-section-sub">Stored verified documents and identification files</p>
                    </div>
                </div>
                <div class="p-section-body">
                    <div class="p-docs-list">
                        @php
                            $docsMeta = [
                                'doc_aadhar_card' => ['title' => 'Aadhar Card Scan', 'icon' => 'fa-id-card', 'color' => '#3b82f6'],
                                'doc_pan_card' => ['title' => 'PAN Card Scan', 'icon' => 'fa-id-card', 'color' => '#10b981'],
                                'doc_marksheet_10th' => ['title' => '10th SSC Marksheet', 'icon' => 'fa-file-invoice', 'color' => '#6366f1'],
                                'doc_marksheet_12th' => ['title' => '12th HSC Marksheet', 'icon' => 'fa-file-invoice', 'color' => '#a855f7'],
                                'doc_degree_certificate' => ['title' => 'Degree Graduation Certificate', 'icon' => 'fa-graduation-cap', 'color' => '#ec4899'],
                                'doc_experience_letter' => ['title' => 'Relieving / Experience Letter', 'icon' => 'fa-file-signature', 'color' => '#06b6d4'],
                                'doc_offer_letter' => ['title' => 'Signed Offer Letter', 'icon' => 'fa-file-contract', 'color' => '#f59e0b'],
                            ];
                        @endphp

                        @foreach($docsMeta as $field => $meta)
                            @php
                                $filePath = $emp ? $emp->{$field} : null;
                            @endphp
                            <div class="p-doc-row">
                                <div class="p-doc-file-info">
                                    <span class="p-doc-icon" style="background: {{ $meta['color'] }}18; color: {{ $meta['color'] }};">
                                        <i class="fa-solid {{ $meta['icon'] }}"></i>
                                    </span>
                                    <div>
                                        <div class="p-doc-title">{{ $meta['title'] }}</div>
                                        <div class="p-doc-status {{ $filePath ? 'uploaded' : 'empty' }}">
                                            @if($filePath)
                                                <i class="fa-solid fa-circle-check"></i> Verified File Stored
                                            @else
                                                <i class="fa-solid fa-circle-info"></i> Document Not Uploaded
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($filePath)
                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="p-doc-action-btn">
                                        <i class="fa-solid fa-download"></i> View File
                                    </a>
                                @else
                                    <span class="p-doc-action-btn disabled">
                                        <i class="fa-solid fa-minus"></i>
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
