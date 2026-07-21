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
            <span id="breadcrumbCurrent">Add Employee</span>
        </div>
        <div class="page-title" id="pageTitle">Create Employee</div>
    </div>
    <div class="page-actions" id="pageActions">
        <div class="emp-view-toggle">
            <a href="{{ route('employees.index') }}" class="emp-view-tab">
                <i class="fa-solid fa-list" style="font-size:12px;"></i> List View
            </a>
            <a href="{{ route('employees.create') }}" class="emp-view-tab active">
                <i class="fa-solid fa-plus" style="font-size:12px;"></i> Add Employee
            </a>
        </div>
    </div>
</div>

<div class="content content-single" style="padding-top: 20px;">
    <!-- Laravel Validation Errors -->
    @if($errors->any())
        <div style="background: #fef2f2; border: 1.5px solid #fca5a5; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; color: #ef4444; font-size: 13.5px; font-weight: 600;">
            <div style="margin-bottom: 5px;"><i class="fa-solid fa-triangle-exclamation"></i> Please fix the following errors:</div>
            <ul style="list-style-type: disc; padding-left: 20px; font-weight: 500; font-size: 13px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Steps Progress Bar -->
    <div class="steps-bar" style="margin-bottom: 24px;">
        <div class="steps-inner">
            <div class="step-item">
                <div class="step-circle active" id="step1circle" onclick="goStep(1)">1</div>
                <div class="step-connector" id="conn1"></div>
            </div>
            <div class="step-item">
                <div class="step-circle" id="step2circle" onclick="goStep(2)">2</div>
                <div class="step-connector" id="conn2"></div>
            </div>
            <div class="step-item">
                <div class="step-circle" id="step3circle" onclick="goStep(3)">
                    <i class="fa-solid fa-check" style="font-size:12px;"></i>
                </div>
            </div>
        </div>
        <div class="steps-labels">
            <span class="step-lbl active" id="lbl1">Personal & Company Info</span>
            <span class="step-lbl" id="lbl2">Documents & Bank Details</span>
            <span class="step-lbl" id="lbl3">Review & Save</span>
        </div>
    </div>

    <!-- Main Wizard Form -->
    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" id="employeeForm">
        @csrf

        <!-- ==================== STEP 1: Personal & Company ==================== -->
        <div id="stepPanel1" class="wizard-panel">
            <div class="emp-form-grid">
                <!-- Personal Card -->
                <div class="form-section">
                    <div class="section-hdr">
                        <div class="section-icon-box" style="background: linear-gradient(135deg, #7c3aed, #a855f7);"><i class="fa-solid fa-user"></i></div>
                        <div>
                            <div class="section-hdr-title">Personal Details</div>
                            <div class="section-hdr-sub">Basic employee information</div>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="field-row cols2">
                            <div class="field">
                                <label class="field-lbl">Name <span class="req">*</span></label>
                                <input class="field-inp" type="text" name="name" id="fName" value="{{ old('name') }}" placeholder="Enter employee name" required/>
                            </div>
                            <div class="field">
                                <label class="field-lbl">Phone <span class="req">*</span></label>
                                <input class="field-inp" type="tel" name="phone" id="fPhone" value="{{ old('phone') }}" placeholder="Enter phone number" required/>
                            </div>
                        </div>
                        <div class="field-row cols2">
                            <div class="field">
                                <label class="field-lbl">Date of Birth <span class="req">*</span></label>
                                <div class="field-icon-wrap">
                                    <input class="field-inp" type="date" name="dob" id="fDob" value="{{ old('dob', '2000-01-01') }}" required/>
                                    <span class="fi-icon"><i class="fa-regular fa-calendar"></i></span>
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-lbl">Gender <span class="req">*</span></label>
                                <div class="radio-group">
                                    <label class="radio-item"><input type="radio" name="gender" value="male" {{ old('gender', 'male') === 'male' ? 'checked' : '' }}/><span>Male</span></label>
                                    <label class="radio-item"><input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }}/><span>Female</span></label>
                                    <label class="radio-item"><input type="radio" name="gender" value="other" {{ old('gender') === 'other' ? 'checked' : '' }}/><span>Other</span></label>
                                </div>
                            </div>
                        </div>
                        <div class="field-row cols2">
                            <div class="field">
                                <label class="field-lbl">Email Address <span class="req">*</span></label>
                                <input class="field-inp" type="email" name="email" id="fEmail" value="{{ old('email') }}" placeholder="employee@company.com" required/>
                            </div>
                            <div class="field">
                                <label class="field-lbl">Login Password <span class="req">*</span></label>
                                <div class="field-icon-wrap">
                                    <input class="field-inp" type="password" name="password" id="fPassword" placeholder="Set password" required/>
                                    <span class="fi-icon" onclick="togglePasswordVisibility()"><i class="fa-regular fa-eye" id="eyeIcon"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label class="field-lbl">Full Address <span class="req">*</span></label>
                            <textarea class="field-inp" name="address" id="fAddress" placeholder="Enter full address" required>{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Company Details Card -->
                <div class="form-section">
                    <div class="section-hdr">
                        <div class="section-icon-box" style="background: linear-gradient(135deg, #2563eb, #3b82f6);"><i class="fa-solid fa-building"></i></div>
                        <div>
                            <div class="section-hdr-title">Company Details</div>
                            <div class="section-hdr-sub">Role and assignment info</div>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="field">
                            <label class="field-lbl">Employee ID</label>
                            <div class="emp-id-display">
                                <i class="fa-solid fa-id-badge" style="color: var(--primary); font-size: 15px;"></i>
                                <span class="uid-text" id="empIdDisplay">{{ $nextUid }}</span>
                                <span class="uid-auto-badge">Auto</span>
                            </div>
                        </div>
                        <div class="field-row cols2">
                            <div class="field">
                                <label class="field-lbl">Select Branch <span class="req">*</span></label>
                                <div class="field-select-wrap">
                                    <select name="branch_id" id="fBranch" onchange="filterBranchDepartments()" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $b)
                                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-lbl">Select Department <span class="req">*</span></label>
                                <div class="field-select-wrap">
                                    <select name="department_id" id="fDept" onchange="filterDeptDesignations()" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->id }}" data-branch="{{ $d->branch_id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label class="field-lbl">Select Designation <span class="req">*</span></label>
                            <div class="field-select-wrap">
                                <select name="designation_id" id="fDesig" required>
                                    <option value="">Select Designation</option>
                                    @foreach($designations as $ds)
                                        <option value="{{ $ds->id }}" data-dept="{{ $ds->department_id }}" {{ old('designation_id') == $ds->id ? 'selected' : '' }}>{{ $ds->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="field-row cols2">
                            <div class="field">
                                <label class="field-lbl">Salary Type</label>
                                <div class="field-select-wrap">
                                    <select name="salary_type" id="fSalaryType">
                                        <option value="Monthly" {{ old('salary_type', 'Monthly') === 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="Weekly" {{ old('salary_type') === 'Weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="Hourly" {{ old('salary_type') === 'Hourly' ? 'selected' : '' }}>Hourly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-lbl">Basic Salary (₹)</label>
                                <div class="field-icon-wrap">
                                    <input class="field-inp" type="number" name="basic_salary" id="fSalary" value="{{ old('basic_salary', '0.00') }}" placeholder="0.00" style="padding-left: 28px;"/>
                                    <span class="fi-icon" style="left: 10px; right: auto; color: var(--text-secondary); cursor: default;">₹</span>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label class="field-lbl">Company Date of Joining <span class="req">*</span></label>
                            <div class="field-icon-wrap">
                                <input class="field-inp" type="date" name="joining_date" id="fJoining" value="{{ old('joining_date', date('Y-m-d')) }}" required/>
                                <span class="fi-icon"><i class="fa-regular fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== STEP 2: Documents & Bank ==================== -->
        <div id="stepPanel2" class="wizard-panel" style="display: none;">
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <!-- Bank Info Card -->
                <div class="form-section">
                    <div class="section-hdr">
                        <div class="section-icon-box" style="background: linear-gradient(135deg, #ea580c, #fb923c);"><i class="fa-solid fa-building-columns"></i></div>
                        <div>
                            <div class="section-hdr-title">Bank Account Details</div>
                            <div class="section-hdr-sub">For salary disbursement</div>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="field-row cols2">
                            <div class="field">
                                <label class="field-lbl">Account Holder Name <span class="req">*</span></label>
                                <input class="field-inp" type="text" name="account_holder_name" id="fAccName" value="{{ old('account_holder_name') }}" placeholder="Full name as on account"/>
                            </div>
                            <div class="field">
                                <label class="field-lbl">Account Number <span class="req">*</span></label>
                                <input class="field-inp" type="text" name="account_number" id="fAccNum" value="{{ old('account_number') }}" placeholder="Enter account number"/>
                            </div>
                        </div>
                        <div class="field-row cols2">
                            <div class="field">
                                <label class="field-lbl">Bank Name <span class="req">*</span></label>
                                <input class="field-inp" type="text" name="bank_name" id="fBankName" value="{{ old('bank_name') }}" placeholder="e.g. State Bank of India"/>
                            </div>
                            <div class="field">
                                <label class="field-lbl">Branch Location</label>
                                <input class="field-inp" type="text" name="bank_branch" id="fBankBranch" value="{{ old('bank_branch') }}" placeholder="Bank branch location"/>
                            </div>
                        </div>
                        <div class="field-row cols2">
                            <div class="field">
                                <label class="field-lbl">IFSC Code</label>
                                <input class="field-inp" type="text" name="ifsc_code" id="fIfsc" value="{{ old('ifsc_code') }}" placeholder="e.g. SBIN0001234" style="font-family: 'DM Mono', monospace; text-transform: uppercase;"/>
                            </div>
                            <div class="field">
                                <label class="field-lbl">PAN Card Number</label>
                                <input class="field-inp" type="text" name="pan_number" id="fPan" value="{{ old('pan_number') }}" placeholder="e.g. ABCDE1234F" style="font-family: 'DM Mono', monospace; text-transform: uppercase;"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Upload Card -->
                <div class="form-section">
                    <div class="section-hdr">
                        <div class="section-icon-box" style="background: linear-gradient(135deg, #0d9488, #2dd4bf);"><i class="fa-solid fa-folder-open"></i></div>
                        <div>
                            <div class="section-hdr-title">Document Details</div>
                            <div class="section-hdr-sub">Upload required employee documents (PDF, JPG, PNG up to 5MB)</div>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="doc-grid">
                            <!-- Aadhar Card -->
                            <div class="doc-item" id="docItem_aadhar" onclick="triggerFileUpload('doc_aadhar_card')">
                                <div class="doc-icon-box" id="docIcon_aadhar"><i class="fa-solid fa-file-arrow-up"></i></div>
                                <div class="doc-info">
                                    <div class="doc-name">Aadhar Card <span class="doc-req-star">*</span></div>
                                    <div class="doc-status" id="docStatus_aadhar">Click to upload</div>
                                </div>
                                <input type="file" name="doc_aadhar_card" id="doc_aadhar_card" style="display: none;" onchange="handleFileChange('aadhar', this)"/>
                            </div>

                            <!-- PAN Card -->
                            <div class="doc-item" id="docItem_pan" onclick="triggerFileUpload('doc_pan_card')">
                                <div class="doc-icon-box" id="docIcon_pan"><i class="fa-solid fa-file-arrow-up"></i></div>
                                <div class="doc-info">
                                    <div class="doc-name">PAN Card <span class="doc-req-star">*</span></div>
                                    <div class="doc-status" id="docStatus_pan">Click to upload</div>
                                </div>
                                <input type="file" name="doc_pan_card" id="doc_pan_card" style="display: none;" onchange="handleFileChange('pan', this)"/>
                            </div>

                            <!-- 10th Marksheet -->
                            <div class="doc-item" id="docItem_marksheet10" onclick="triggerFileUpload('doc_marksheet_10th')">
                                <div class="doc-icon-box" id="docIcon_marksheet10"><i class="fa-solid fa-file-arrow-up"></i></div>
                                <div class="doc-info">
                                    <div class="doc-name">10th Marksheet</div>
                                    <div class="doc-status" id="docStatus_marksheet10">Click to upload</div>
                                </div>
                                <input type="file" name="doc_marksheet_10th" id="doc_marksheet_10th" style="display: none;" onchange="handleFileChange('marksheet10', this)"/>
                            </div>

                            <!-- 12th Marksheet -->
                            <div class="doc-item" id="docItem_marksheet12" onclick="triggerFileUpload('doc_marksheet_12th')">
                                <div class="doc-icon-box" id="docIcon_marksheet12"><i class="fa-solid fa-file-arrow-up"></i></div>
                                <div class="doc-info">
                                    <div class="doc-name">12th Marksheet</div>
                                    <div class="doc-status" id="docStatus_marksheet12">Click to upload</div>
                                </div>
                                <input type="file" name="doc_marksheet_12th" id="doc_marksheet_12th" style="display: none;" onchange="handleFileChange('marksheet12', this)"/>
                            </div>

                            <!-- Degree Certificate -->
                            <div class="doc-item" id="docItem_degree" onclick="triggerFileUpload('doc_degree_certificate')">
                                <div class="doc-icon-box" id="docIcon_degree"><i class="fa-solid fa-file-arrow-up"></i></div>
                                <div class="doc-info">
                                    <div class="doc-name">Degree Certificate <span class="doc-req-star">*</span></div>
                                    <div class="doc-status" id="docStatus_degree">Click to upload</div>
                                </div>
                                <input type="file" name="doc_degree_certificate" id="doc_degree_certificate" style="display: none;" onchange="handleFileChange('degree', this)"/>
                            </div>

                            <!-- Experience Letter -->
                            <div class="doc-item" id="docItem_experience" onclick="triggerFileUpload('doc_experience_letter')">
                                <div class="doc-icon-box" id="docIcon_experience"><i class="fa-solid fa-file-arrow-up"></i></div>
                                <div class="doc-info">
                                    <div class="doc-name">Experience Letter</div>
                                    <div class="doc-status" id="docStatus_experience">Click to upload</div>
                                </div>
                                <input type="file" name="doc_experience_letter" id="doc_experience_letter" style="display: none;" onchange="handleFileChange('experience', this)"/>
                            </div>

                            <!-- Offer Letter -->
                            <div class="doc-item" id="docItem_offer" onclick="triggerFileUpload('doc_offer_letter')">
                                <div class="doc-icon-box" id="docIcon_offer"><i class="fa-solid fa-file-arrow-up"></i></div>
                                <div class="doc-info">
                                    <div class="doc-name">Offer Letter <span class="doc-req-star">*</span></div>
                                    <div class="doc-status" id="docStatus_offer">Click to upload</div>
                                </div>
                                <input type="file" name="doc_offer_letter" id="doc_offer_letter" style="display: none;" onchange="handleFileChange('offer', this)"/>
                            </div>

                            <!-- Passport Photo -->
                            <div class="doc-item" id="docItem_passport" onclick="triggerFileUpload('doc_passport_photo')">
                                <div class="doc-icon-box" id="docIcon_passport"><i class="fa-solid fa-file-arrow-up"></i></div>
                                <div class="doc-info">
                                    <div class="doc-name">Passport Photo <span class="doc-req-star">*</span></div>
                                    <div class="doc-status" id="docStatus_passport">Click to upload</div>
                                </div>
                                <input type="file" name="doc_passport_photo" id="doc_passport_photo" style="display: none;" onchange="handleFileChange('passport', this)"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== STEP 3: Review & Confirm ==================== -->
        <div id="stepPanel3" class="wizard-panel" style="display: none;">
            <div class="form-section">
                <div class="section-hdr">
                    <div class="section-icon-box" style="background: linear-gradient(135deg, #16a34a, #4ade80);"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <div class="section-hdr-title">Review & Confirm</div>
                        <div class="section-hdr-sub">Please verify all details before saving</div>
                    </div>
                </div>
                <div class="section-body">
                    <div id="reviewContent" class="review-grid">
                        <!-- Javascript generated list -->
                    </div>
                    <div class="review-ready-banner">
                        <i class="fa-solid fa-circle-check" style="color: var(--accent-green); font-size: 18px;"></i>
                        <span>Everything looks good! Click "Save Employee" to complete.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Wizard Footer -->
        <div class="wizard-footer" style="margin-top: 24px;">
            <div class="wizard-footer-info">
                <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                <span id="footerInfo">Step 1 of 3 — Fill in personal & company details</span>
            </div>
            <div class="wizard-btn-group">
                <button type="button" class="wbtn wbtn-outline" id="btnBack" onclick="prevStep()" style="display: none;">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </button>
                <a href="{{ route('employees.index') }}" class="wbtn wbtn-outline">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </a>
                <button type="button" class="wbtn wbtn-primary" id="btnNext" onclick="nextStep()">
                    Next Step <i class="fa-solid fa-arrow-right"></i>
                </button>
                <button type="submit" class="wbtn wbtn-success" id="btnSave" style="display: none;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Employee
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 3;

    document.addEventListener('DOMContentLoaded', () => {
        // Run selectors on load to match old state or presets
        filterBranchDepartments(true);
        renderStepIndicator();
    });

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('fPassword');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.className = 'fa-regular fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            eyeIcon.className = 'fa-regular fa-eye';
        }
    }

    /* ── Cascading Dropdowns ── */
    function filterBranchDepartments(isInit = false) {
        const branchSelect = document.getElementById('fBranch');
        const deptSelect = document.getElementById('fDept');
        const selectedBranch = branchSelect.value;

        // Reset department & designation selections
        if (!isInit) {
            deptSelect.value = '';
            document.getElementById('fDesig').value = '';
        }

        Array.from(deptSelect.options).forEach(opt => {
            if (opt.value === '') {
                opt.style.display = '';
                return;
            }
            const belongsToBranch = opt.getAttribute('data-branch') === selectedBranch;
            opt.style.display = belongsToBranch ? '' : 'none';
        });

        filterDeptDesignations(isInit);
    }

    function filterDeptDesignations(isInit = false) {
        const deptSelect = document.getElementById('fDept');
        const desigSelect = document.getElementById('fDesig');
        const selectedDept = deptSelect.value;

        if (!isInit) {
            desigSelect.value = '';
        }

        Array.from(desigSelect.options).forEach(opt => {
            if (opt.value === '') {
                opt.style.display = '';
                return;
            }
            const belongsToDept = opt.getAttribute('data-dept') === selectedDept;
            opt.style.display = belongsToDept ? '' : 'none';
        });
    }

    /* ── Step Navigation ── */
    function nextStep() {
        if (currentStep === 1) {
            // Basic step validation
            if (!validateStep1()) return;
        }

        if (currentStep < totalSteps) {
            currentStep++;
            renderStepIndicator();
            if (currentStep === 3) {
                buildReviewScreen();
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            renderStepIndicator();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function goStep(step) {
        if (step < currentStep) {
            currentStep = step;
            renderStepIndicator();
        } else if (step > currentStep) {
            if (currentStep === 1 && validateStep1()) {
                currentStep = step;
                renderStepIndicator();
                if (currentStep === 3) buildReviewScreen();
            }
        }
    }

    function validateStep1() {
        const fields = [
            { id: 'fName', name: 'Name' },
            { id: 'fPhone', name: 'Phone' },
            { id: 'fDob', name: 'Date of Birth' },
            { id: 'fEmail', name: 'Email Address' },
            { id: 'fPassword', name: 'Password' },
            { id: 'fAddress', name: 'Address' },
            { id: 'fBranch', name: 'Branch' },
            { id: 'fDept', name: 'Department' },
            { id: 'fDesig', name: 'Designation' },
            { id: 'fJoining', name: 'Joining Date' }
        ];

        for (let f of fields) {
            const el = document.getElementById(f.id);
            if (!el || !el.value.trim()) {
                alert(`Please enter a valid value for: ${f.name}`);
                el?.focus();
                return false;
            }
        }
        return true;
    }

    function renderStepIndicator() {
        for (let s = 1; s <= 3; s++) {
            const circle = document.getElementById(`step${s}circle`);
            const lbl = document.getElementById(`lbl${s}`);
            
            circle.classList.remove('active', 'done');
            if (lbl) lbl.classList.remove('active', 'done');

            if (s < currentStep) {
                circle.classList.add('done');
                circle.innerHTML = '<i class="fa-solid fa-check" style="font-size:11px;"></i>';
                if (lbl) lbl.classList.add('done');
            } else if (s === currentStep) {
                circle.classList.add('active');
                circle.innerHTML = s;
                if (lbl) lbl.classList.add('active');
            } else {
                circle.innerHTML = s === 3 ? '<i class="fa-solid fa-check" style="font-size:12px;"></i>' : s;
            }
        }

        // Connectors
        for (let c = 1; c <= 2; c++) {
            const conn = document.getElementById(`conn${c}`);
            if (conn) conn.classList.toggle('done', c < currentStep);
        }

        // Show/hide panels
        document.getElementById('stepPanel1').style.display = currentStep === 1 ? 'block' : 'none';
        document.getElementById('stepPanel2').style.display = currentStep === 2 ? 'block' : 'none';
        document.getElementById('stepPanel3').style.display = currentStep === 3 ? 'block' : 'none';

        // Update footer buttons
        document.getElementById('btnBack').style.display = currentStep > 1 ? 'inline-flex' : 'none';
        document.getElementById('btnNext').style.display = currentStep < totalSteps ? 'inline-flex' : 'none';
        document.getElementById('btnSave').style.display = currentStep === totalSteps ? 'inline-flex' : 'none';

        const infos = [
            'Step 1 of 3 — Fill in personal & company details',
            'Step 2 of 3 — Upload documents & bank details',
            'Step 3 of 3 — Review all details and save'
        ];
        document.getElementById('footerInfo').textContent = infos[currentStep - 1];
    }

    /* ── File Upload Widget Handlers ── */
    function triggerFileUpload(id) {
        document.getElementById(id).click();
    }

    function handleFileChange(slug, input) {
        const item = document.getElementById(`docItem_${slug}`);
        const iconBox = document.getElementById(`docIcon_${slug}`);
        const status = document.getElementById(`docStatus_${slug}`);

        if (input.files && input.files.length > 0) {
            const fileName = input.files[0].name;
            item.classList.add('uploaded');
            status.textContent = '✓ ' + (fileName.length > 20 ? fileName.slice(0, 18) + '...' : fileName);
            iconBox.innerHTML = '<i class="fa-solid fa-file-circle-check"></i>';
        } else {
            item.classList.remove('uploaded');
            status.textContent = 'Click to upload';
            iconBox.innerHTML = '<i class="fa-solid fa-file-arrow-up"></i>';
        }
    }

    /* ── Review Screen Builder ── */
    function buildReviewScreen() {
        const container = document.getElementById('reviewContent');
        const val = id => {
            const el = document.getElementById(id);
            if (!el) return '—';
            if (el.tagName === 'SELECT') {
                return el.options[el.selectedIndex]?.text || '—';
            }
            return el.value.trim() || '—';
        };

        const genderVal = document.querySelector('input[name="gender"]:checked')?.value || '—';

        container.innerHTML = `
            <div>
                <div class="review-section-label">Personal Details</div>
                ${reviewRow('Full Name', val('fName'))}
                ${reviewRow('Phone Number', val('fPhone'))}
                ${reviewRow('Email Address', val('fEmail'))}
                ${reviewRow('Date of Birth', val('fDob'))}
                ${reviewRow('Gender', genderVal.toUpperCase())}
                ${reviewRow('Full Address', val('fAddress'))}
            </div>
            <div>
                <div class="review-section-label">Company Assignments</div>
                ${reviewRow('Employee ID', document.getElementById('empIdDisplay').textContent)}
                ${reviewRow('Branch', val('fBranch'))}
                ${reviewRow('Department', val('fDept'))}
                ${reviewRow('Designation', val('fDesig'))}
                ${reviewRow('Salary Option', val('fSalaryType') + ' - ₹' + val('fSalary'))}
                ${reviewRow('Joining Date', val('fJoining'))}
            </div>
            <div style="grid-column: 1 / -1; border-top: 1px solid var(--border); padding-top: 20px;">
                <div class="review-section-label">Bank Account Info</div>
                <div class="review-grid">
                    <div>
                        ${reviewRow('Account Holder Name', val('fAccName'))}
                        ${reviewRow('Account Number', val('fAccNum'))}
                        ${reviewRow('Bank Name', val('fBankName'))}
                    </div>
                    <div>
                        ${reviewRow('Branch Location', val('fBankBranch'))}
                        ${reviewRow('IFSC Code', val('fIfsc').toUpperCase())}
                        ${reviewRow('PAN Card', val('fPan').toUpperCase())}
                    </div>
                </div>
            </div>
        `;
    }

    function reviewRow(label, value) {
        return `
            <div class="review-row">
                <span class="r-label">${label}</span>
                <span class="r-val">${value}</span>
            </div>
        `;
    }
</script>
@endpush
@endsection
