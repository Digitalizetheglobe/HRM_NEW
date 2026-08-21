{{-- Add this at the top of your edit.blade.php file --}}
@if($employee->approval_status === 'approved' && \Auth::user()->type === 'employee')
    <div class="alert alert-warning">
        <strong>{{ __('Notice') }}:</strong> 
        {{ __('Your details have been approved and can no longer be edited.') }}
        {{ __('If you need to make changes, please contact your administrator.') }}
    </div>
    
    <div class="float-end">
        <a href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}"
           class="btn btn-primary">{{ __('Back to View') }}</a>
    </div>
    
    @php
        // Prevent form submission by disabling all inputs
        $readonly = true;
    @endphp
@else
    @php
        $readonly = false;
    @endphp
@endif

@extends('layouts.admin')

@section('page-title')
    {{ __('Edit Employee') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ url('employee') }}">{{ __('Employee') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit Employee') }}</li>
@endsection

@push('css')
    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="">
            <div class="">
                {{ Form::model($employee, ['route' => ['employee.update', $employee->id], 'method' => 'put', 'enctype' => 'multipart/form-data']) }}

                <!-- Add this error display section at the top of your form -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row">
                    <!-- Personal Details Section -->
                    <div class="col-md-6">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Personal Detail') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        {!! Form::label('name', __('First Name'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::text('name', old('name', $employee->name ?? ''), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => 'Enter first name',
                                            'readonly' => $readonly,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-4">
                                        {!! Form::label('middle_name', __('Middle Name'), ['class' => 'form-label']) !!}
                                        {!! Form::text('middle_name', old('middle_name', $employee->middle_name ?? ''), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter middle name',
                                            'readonly' => $readonly,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-4">
                                        {!! Form::label('last_name', __('Last Name'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::text('last_name', old('last_name', $employee->last_name ?? ''), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => 'Enter last name',
                                            'readonly' => $readonly,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('phone', __('Phone'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::text('phone', old('phone', $employee->phone), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter employee phone',
                                            'oninput' => 'validateNumbers()',
                                            'minlength' => '10',
                                            'maxlength' => '10',
                                            'pattern' => '\d{10}',
                                            'title' => 'Please enter exactly 10 digits',
                                        ]) !!}
                                        <span id="phone-error" class="text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-6">
                                        {!! Form::label('emergency_number', __('Emergency Number'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::text('emergency_number', old('emergency_number', $employee->emergency_number), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter emergency number',
                                            'oninput' => 'validateNumbers()',
                                            'minlength' => '10',
                                            'maxlength' => '10',
                                            'pattern' => '\d{10}',
                                            'title' => 'Please enter exactly 10 digits',
                                        ]) !!}
                                        <span id="emergency_number-error" class="text-danger"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}
                                            {!! Form::date('dob', !empty($employee->dob) ? date('Y-m-d', strtotime($employee->dob)) : null, [
                                                'class' => 'form-control',
                                                'autocomplete' => 'off',
                                                'placeholder' => 'yyyy-mm-dd'
                                            ]) !!}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('blood_group', __('Blood-Group'), ['class' => 'form-label']) !!}<span class="text-danger pl-1"></span>
                                            {!! Form::text('blood_group', old('Blood-Group'), [
                                                'class' => 'form-control',
                                                'placeholder' => 'Enter employee Blood-Group',
                                            ]) !!}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                            <div class="d-flex radio-check">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="g_male" value="Male" name="gender"
                                                        class="form-check-input" {{ $employee->gender == 'Male' ? 'checked' : '' }}>
                                                    <label class="form-check-label "
                                                        for="g_male">{{ __('Male') }}</label>
                                                </div>
                                                <div class="custom-control custom-radio ms-1 custom-control-inline">
                                                    <input type="radio" id="g_female" value="Female" name="gender"
                                                        class="form-check-input" {{ $employee->gender == 'Female' ? 'checked' : '' }}>
                                                    <label class="form-check-label "
                                                        for="g_female">{{ __('Female') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('email', __('Email'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        @if(\Auth::user()->type === 'employee' && \Auth::user()->id === $employee->user_id)
                                            <small class="text-muted d-block mb-1">
                                                <i class="ti ti-info-circle"></i> {{ __('Email cannot be changed. Please contact administrator.') }}
                                            </small>
                                        @endif
                                        {!! Form::email('email', old('email', $employee->email), [
                                            'class' => 'form-control' . ((\Auth::user()->type === 'employee' && \Auth::user()->id === $employee->user_id) ? ' bg-light' : ''),
                                            'required' => 'required',
                                            'placeholder' => 'Enter employee email',
                                            'readonly' => (\Auth::user()->type === 'employee' && \Auth::user()->id === $employee->user_id),
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('password', __('Password'), ['class' => 'form-label']) !!}
                                        {!! Form::password('password', [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter new password (leave blank to keep current)',
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('address', __('Address'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                    {!! Form::textarea('address', old('address', $employee->address), [
                                        'class' => 'form-control',
                                        'rows' => 3,
                                        'placeholder' => 'Enter employee address',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Details Section -->
                    <div class="col-md-6">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Company Detail') }}</h5>
                            </div>
                            <div class="card-body employee-detail-create-body">
                                <div class="row">
                                    @csrf
                                    <div class="form-group">
                                        {!! Form::label('employee_id', __('Employee ID'), ['class' => 'form-label']) !!}
                                        {!! Form::text('employee_id', \Auth::user()->employeeIdFormat($employee->employee_id), ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{ Form::label('branch_id', __('Select Branch*'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::select('branch_id', $branches, $employee->branch_id, [
                                                'class' => 'form-control branch_id',
                                                'id' => 'branch_id',
                                                'required' => 'required',
                                                'disabled' => (\Auth::user()->type === 'employee') ? true : false,
                                            ]) }}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <div class="form-icon-user" id="department_id">
                                            {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}
                                            <select class="form-control select department_id" name="department_id"
                                                id="department_id" placeholder="Select Department" required {{ (\Auth::user()->type === 'employee') ? 'disabled' : '' }}>
                                                @foreach($departments as $id => $department)
                                                    <option value="{{ $id }}" {{ $employee->department_id == $id ? 'selected' : '' }}>{{ $department }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{ Form::label('designation_id', __('Select Designation'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user designation_div">
                                            <select class="form-control designation_id" name="designation_id" id="designation_id" required {{ (\Auth::user()->type === 'employee') ? 'disabled' : '' }}>
                                                <option value="" disabled>{{ __('Select Designation') }}</option>
                                                @foreach($designations as $id => $designation)
                                                    <option value="{{ $id }}" {{ $employee->designation_id == $id ? 'selected' : '' }}>{{ $designation }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-md-6">
                                        {{ Form::label('office_shift', __('Office Shift'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::select('office_shift', [
                                                'First Shift' => 'First Shift – 10:30 AM to 6:30 PM',
                                                'Second Shift' => 'Second Shift – 12:00 PM to 8:00 PM'
                                            ], $employee->office_shift, ['class' => 'form-control', 'id' => 'office_shift', 'placeholder' => 'Select Office Shift', 'disabled' => (\Auth::user()->type === 'employee') ? true : false]) }}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        {!! Form::label('company_doj', __('Date Of Joining'), ['class' => 'form-label']) !!}
                                        {!! Form::date('company_doj', !empty($employee->company_doj) ? date('Y-m-d', strtotime($employee->company_doj)) : null, [
                                            'class' => 'form-control',
                                            'autocomplete' => 'off',
                                            'placeholder' => 'yyyy-mm-dd',
                                            'readonly' => (\Auth::user()->type === 'employee') ? true : false,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('is_team_leader', __('Team Leader'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::select('is_team_leader', [
                                                '0' => 'No',
                                                '1' => 'Yes',
                                            ], old('is_team_leader', $employee->is_team_leader ? '1' : '0'), [
                                                'class' => 'form-control',
                                                'id' => 'is_team_leader',
                                                'disabled' => (\Auth::user()->type === 'employee') ? true : false,
                                            ]) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                   
                        <!-- Experience Section -->
                        <div class="col-md-12">
                            <div class="card md-12">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ __('Total Experience') }}</h5>
                                    <button type="button" class="btn btn-primary btn-sm add-experience-row">
                                        <i class="fa fa-plus"></i> Add Experience
                                    </button>
                                </div>
                                <div class="card-body employee-detail-create-body">
                                    <div id="experience-details-container">
                                        @if(!empty($experiences))
                                            @foreach($experiences as $key => $experience)
                                                <div class="row experience-detail-row mb-3">
                                                    <div class="form-group col-md-6">
                                                        {!! Form::label("experience[$key][previous_company_name]", __('Previous Company Name'), ['class' => 'form-label']) !!}
                                                        {!! Form::text("experience[$key][previous_company_name]", $experience['previous_company_name'] ?? null, [
                                                            'class' => 'form-control',
                                                            'placeholder' => 'Enter previous company name',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        {!! Form::label("experience[$key][previous_designation]", __('Designation'), ['class' => 'form-label']) !!}
                                                        {!! Form::text("experience[$key][previous_designation]", $experience['previous_designation'] ?? null, [
                                                            'class' => 'form-control',
                                                            'placeholder' => 'Enter designation',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        {!! Form::label("experience[$key][start_date]", __('Start Date'), ['class' => 'form-label']) !!}
                                                        {!! Form::date("experience[$key][start_date]", null, [
                                                            'class' => 'form-control',
                                                            'placeholder' => 'dd-mm-yyyy'
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        {!! Form::label("experience[$key][end_date]", __('End Date'), ['class' => 'form-label']) !!}
                                                        {!! Form::date("experience[$key][end_date]", null, [
                                                            'class' => 'form-control',
                                                            'placeholder' => 'dd-mm-yyyy'
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        {!! Form::label("experience[$key][previous_salary]", __('Previous Salary'), ['class' => 'form-label']) !!}
                                                        {!! Form::number("experience[$key][previous_salary]", $experience['previous_salary'] ?? null, [
                                                            'class' => 'form-control',
                                                            'placeholder' => 'Enter previous salary',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group col-md-12 text-end">
                                                        <button type="button" class="btn btn-danger remove-experience-row">
                                                            <i class="fa fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="row experience-detail-row mb-3">
                                                <div class="form-group col-md-6">
                                                    {!! Form::label('experience[0][previous_company_name]', __('Previous Company Name'), ['class' => 'form-label']) !!}
                                                    {!! Form::text('experience[0][previous_company_name]', null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Enter previous company name',
                                                    ]) !!}
                                                </div>
                                                <div class="form-group col-md-6">
                                                    {!! Form::label('experience[0][previous_designation]', __('Designation'), ['class' => 'form-label']) !!}
                                                    {!! Form::text('experience[0][previous_designation]', null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Enter designation',
                                                    ]) !!}
                                                </div>
                                                <div class="form-group col-md-6">
                                                    {!! Form::label('experience[0][start_date]', __('Start Date'), ['class' => 'form-label']) !!}
                                                    {!! Form::date('experience[0][start_date]', null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Select start date',
                                                    ]) !!}
                                                </div>
                                                <div class="form-group col-md-6">
                                                    {!! Form::label('experience[0][end_date]', __('End Date'), ['class' => 'form-label']) !!}
                                                    {!! Form::date('experience[0][end_date]', null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Select end date',
                                                    ]) !!}
                                                </div>
                                                <div class="form-group col-md-12">
                                                    {!! Form::label('experience[0][previous_salary]', __('Previous Salary'), ['class' => 'form-label']) !!}
                                                    {!! Form::number('experience[0][previous_salary]', null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Enter previous salary',
                                                    ]) !!}
                                                </div>
                                                <div class="form-group col-md-12 text-end">
                                                    <button type="button" class="btn btn-danger remove-experience-row">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents and Education Section -->
                <div class="row">
                    <!-- Documents Section -->
                    <div class="col-md-6">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Document') }}</h5>
                            </div>
                            <div class="card-body employee-detail-create-body">
                                @foreach ($documents as $key => $document)
                                    <div class="row">
                                        <div class="form-group col-12 d-flex">
                                            <div class="float-left col-4">
                                                <label for="document" class="float-left pt-1 form-label">
                                                    {{ $document->name }} 
                                                    @if ($document->is_required == 1)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="float-right col-8">
                                                <input type="hidden" name="emp_doc_id[{{ $document->id }}]" value="{{ $document->id }}">
                                                @php
                                                    $employeeDoc = $employee->documents()->where('document_id', $document->id)->first();
                                                @endphp
                                                <div class="d-flex flex-column gap-2 mt-2">
                                                    <div class="d-flex align-items-center flex-wrap gap-2 m-0 p-0">
                                                        <div class="choose-files m-0 p-0" style="margin: 0 !important;">
                                                            <label for="document[{{ $document->id }}]" style="margin: 0 !important; display: flex;">
                                                                <div class="btn btn-sm btn-primary document cursor-pointer m-0" style="margin: 0 !important; white-space: nowrap;">
                                                                    <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                <input type="file" 
                                                                    class="form-control file @error('document') is-invalid @enderror"
                                                                    @if ($document->is_required == 1 && !($employeeDoc && $employeeDoc->document_value)) required @endif
                                                                    name="document[{{ $document->id }}]"
                                                                    id="document[{{ $document->id }}]"
                                                                    data-filename="{{ $document->id . '_filename' }}"
                                                                    onchange="handleCompanyDocumentPreview(this, '{{ $key }}')">
                                                            </label>
                                                        </div>
                                                        <div class="m-0 p-0" id="preview_link_{{ $key }}" style="display: none;">
                                                            <a href="#" target="_blank" class="btn btn-sm btn-info m-0" style="margin: 0 !important; white-space: nowrap;">
                                                                <i class="ti ti-eye"></i> Preview Added File
                                                            </a>
                                                        </div>
                                                        @if($employeeDoc && $employeeDoc->document_value)
                                                            <div class="m-0 p-0" style="margin: 0 !important;">
                                                                <a href="{{ asset($employeeDoc->document_value) }}"
                                                                target="_blank" 
                                                                class="btn btn-sm btn-primary m-0" style="margin: 0 !important; white-space: nowrap;">
                                                                    <i class="ti ti-download"></i> View Document
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        @if($employeeDoc && $employeeDoc->document_value)
                                                            @php
                                                                $ext = pathinfo($employeeDoc->document_value, PATHINFO_EXTENSION);
                                                                $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                            @endphp
                                                            <img id="{{ 'blah' . $key }}" src="{{ asset(str_replace('public/', '', $employeeDoc->document_value)) }}" width="50%" class="mt-2" style="{{ $isImage ? '' : 'display: none;' }}" onerror="this.style.display='none';" />
                                                        @else
                                                            <img id="{{ 'blah' . $key }}" src="" width="50%" class="mt-2" style="display: none;" onerror="this.style.display='none';" />
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
              
                    <!-- Education Section -->
                    <!-- Education Section -->
                    <div class="col-md-6">
                        <div class="card em-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ __('Education Details') }}</h5>
                                <button type="button" class="btn btn-primary btn-sm add-education-row">
                                    <i class="fa fa-plus"></i> Add Education
                                </button>
                            </div>
                            <div class="card-body employee-detail-create-body">
                                <div id="education-details-container">
                                    @if(!empty($educations))
                                        @foreach($educations as $key => $education)
                                            <div class="row education-detail-row mb-3">
                                                <div class="form-group col-md-6">
                                                    {!! Form::label("education[$key][degree]", __('Degree'), ['class' => 'form-label']) !!}
                                                    <select name="education[{{ $key }}][degree]" class="form-control degree">
                                                        <option value="10th" {{ (isset($education['degree']) && $education['degree'] == '10th') ? 'selected' : '' }}>{{ __('10th') }}</option>
                                                        <option value="12th" {{ (isset($education['degree']) && $education['degree'] == '12th') ? 'selected' : '' }}>{{ __('12th') }}</option>
                                                        <option value="Bachelor" {{ (isset($education['degree']) && $education['degree'] == 'Bachelor') ? 'selected' : '' }}>{{ __('Bachelor') }}</option>
                                                        <option value="Master" {{ (isset($education['degree']) && $education['degree'] == 'Master') ? 'selected' : '' }}>{{ __('Master') }}</option>
                                                        <option value="PhD" {{ (isset($education['degree']) && $education['degree'] == 'PhD') ? 'selected' : '' }}>{{ __('PhD') }}</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    {!! Form::label("education[$key][college_name]", __('College Name'), ['class' => 'form-label']) !!}
                                                    {!! Form::text("education[$key][college_name]", $education['college_name'] ?? null, [
                                                        'class' => 'form-control college-name',
                                                        'placeholder' => 'Enter college name',
                                                    ]) !!}
                                                </div>
                                                <div class="form-group col-md-6">
                                                    {!! Form::label("education[$key][passing_year]", __('Passing Year'), ['class' => 'form-label']) !!}
                                                    <select name="education[{{ $key }}][passing_year]" class="form-control passing-year">
                                                        <option value="" disabled selected>{{ __('Select Year') }}</option>
                                                        @for ($year = 1997; $year <= 2040; $year++)
                                                            <option value="{{ $year }}" {{ (isset($education['passing_year']) && $education['passing_year'] == $year) ? 'selected' : '' }}>{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    {!! Form::label("education[$key][grade]", __('Grade'), ['class' => 'form-label']) !!}
                                                    {!! Form::number("education[$key][grade]", $education['grade'] ?? null, [
                                                        'class' => 'form-control grade',
                                                        'placeholder' => 'Enter grade (e.g., 4.0)',
                                                        'step' => '0.1',
                                                        'min' => '0',
                                                        'max' => '10',
                                                    ]) !!}
                                                </div>
                                                <div class="form-group col-md-12">
                                                    {!! Form::label("education[$key][document]", __('Education Document'), ['class' => 'form-label']) !!}
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                        <div class="d-flex align-items-center flex-wrap gap-2 m-0 p-0">
                                                            <div class="choose-files m-0 p-0" style="margin: 0 !important;">
                                                                <label for="education[{{ $key }}][document]" style="margin: 0 !important; display: flex;">
                                                                    <div class="btn btn-sm btn-primary document cursor-pointer m-0" style="margin: 0 !important; white-space: nowrap;">
                                                                        <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                                                                    </div>
                                                                    <input type="file" 
                                                                        name="education[{{ $key }}][document]" 
                                                                        id="education[{{ $key }}][document]" 
                                                                        class="form-control file education-document"
                                                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                </label>
                                                            </div>
                                                            <div class="m-0 p-0 education-preview-link" style="display: none;">
                                                                <a href="#" target="_blank" class="btn btn-sm btn-info m-0" style="margin: 0 !important; white-space: nowrap;">
                                                                    <i class="ti ti-eye"></i> Preview Added File
                                                                </a>
                                                            </div>
                                                            @if(isset($education['document_path']))
                                                                <div class="m-0 p-0" style="margin: 0 !important;">
                                                                    <a href="{{ asset($education['document_path']) }}" 
                                                                    target="_blank" 
                                                                    class="btn btn-sm btn-primary m-0" style="margin: 0 !important; white-space: nowrap;">
                                                                        <i class="ti ti-download"></i> View Document
                                                                    </a>
                                                                    <input type="hidden" name="education[{{ $key }}][existing_document]" value="{{ $education['document_path'] }}">
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="m-0 p-0" style="margin: 0 !important;">
                                                            <button type="button" class="btn btn-sm btn-danger remove-education-row m-0" style="margin: 0 !important; white-space: nowrap;">
                                                                <i class="fa fa-trash"></i> Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row education-detail-row">
                                            <div class="form-group col-md-6">
                                                {!! Form::label('education[0][degree]', __('Degree'), ['class' => 'form-label']) !!}
                                                <select name="education[0][degree]" class="form-control degree">
                                                    <option value="10th">{{ __('10th') }}</option>
                                                    <option value="12th">{{ __('12th') }}</option>
                                                    <option value="Bachelor">{{ __('Bachelor') }}</option>
                                                    <option value="Master">{{ __('Master') }}</option>
                                                    <option value="PhD">{{ __('PhD') }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                {!! Form::label('education[0][college_name]', __('College Name'), ['class' => 'form-label']) !!}
                                                {!! Form::text('education[0][college_name]', null, [
                                                    'class' => 'form-control college-name',
                                                    'placeholder' => 'Enter college name',
                                                ]) !!}
                                            </div>
                                            <div class="form-group col-md-6">
                                                {!! Form::label('education[0][passing_year]', __('Passing Year'), ['class' => 'form-label']) !!}
                                                <select name="education[0][passing_year]" class="form-control passing-year">
                                                    <option value="" disabled selected>{{ __('Select Year') }}</option>
                                                    @for ($year = 1997; $year <= 2040; $year++)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                {!! Form::label('education[0][grade]', __('Grade'), ['class' => 'form-label']) !!}
                                                {!! Form::number('education[0][grade]', null, [
                                                    'class' => 'form-control grade',
                                                    'placeholder' => 'Enter grade (e.g., 4.0)',
                                                    'step' => '0.1',
                                                    'min' => '0',
                                                    'max' => '10',
                                                ]) !!}
                                            </div>
                                            <div class="form-group col-md-12">
                                                {!! Form::label("education[0][document]", __('Education Document'), ['class' => 'form-label']) !!}
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <div class="choose-files m-0 p-0" style="margin: 0 !important;">
                                                        <label for="education[0][document]" style="margin: 0 !important; display: flex;">
                                                            <div class="btn btn-sm btn-primary document cursor-pointer m-0" style="margin: 0 !important; white-space: nowrap;">
                                                                <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            <input type="file" 
                                                                name="education[0][document]" 
                                                                id="education[0][document]" 
                                                                class="form-control file education-document"
                                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                        </label>
                                                    </div>
                                                    <div class="m-0 p-0" style="margin: 0 !important;">
                                                        <button type="button" class="btn btn-sm btn-danger remove-education-row m-0" style="margin: 0 !important; white-space: nowrap;">
                                                            <i class="fa fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Details Section -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Bank Details') }}</h5>
                            </div>
                            <div class="card-body employee-detail-create-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {!! Form::label('account_holder_name', __('Account Holder Name'), ['class' => 'form-label']) !!}
                                        {!! Form::text('account_holder_name', old('account_holder_name', $employee->account_holder_name ?? ''), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter account holder name',
                                            'readonly' => $readonly,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) !!}
                                        {!! Form::text('bank_name', old('bank_name', $employee->bank_name ?? ''), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter bank name',
                                            'readonly' => $readonly,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('bank_identifier_code', __('IFSC Code'), ['class' => 'form-label']) !!}
                                        {!! Form::text('bank_identifier_code', old('bank_identifier_code', $employee->bank_identifier_code ?? ''), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter IFSC code',
                                            'maxlength' => '11',
                                            'readonly' => $readonly,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('branch_location', __('Branch Location'), ['class' => 'form-label']) !!}
                                        {!! Form::text('branch_location', old('branch_location', $employee->branch_location ?? ''), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter branch location',
                                            'readonly' => $readonly,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('account_number', __('Account Number'), ['class' => 'form-label']) !!}
                                        {!! Form::text('account_number', old('account_number', $employee->account_number ?? ''), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter account number',
                                            'readonly' => $readonly,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('account_type', __('Account Type'), ['class' => 'form-label']) !!}
                                        @php
                                            // Handle old values and convert to new format
                                            $accountTypeValue = old('account_type', $employee->account_type ?? '');
                                            // Convert old string values
                                            if($accountTypeValue == 'Salary account') {
                                                $accountTypeValue = 'salary_account';
                                            } elseif($accountTypeValue == 'Saving account') {
                                                $accountTypeValue = 'savings_account';
                                            }
                                            // Convert old numeric values (0, 1) - legacy support
                                            elseif($accountTypeValue === 0 || $accountTypeValue === '0') {
                                                $accountTypeValue = 'salary_account'; // Default mapping
                                            } elseif($accountTypeValue === 1 || $accountTypeValue === '1') {
                                                $accountTypeValue = 'savings_account'; // Default mapping
                                            }
                                        @endphp
                                        @if($readonly)
                                            <input type="hidden" name="account_type" value="{{ $accountTypeValue }}">
                                        @endif
                                        {!! Form::select('account_type', [
                                            '' => __('Select Account Type'),
                                            'salary_account' => __('Salary Account'),
                                            'savings_account' => __('Savings Account'),
                                        ], $accountTypeValue, [
                                            'class' => 'form-control',
                                            'placeholder' => 'Select account type',
                                            'disabled' => $readonly,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="float-end">
                    <button type="submit" class="btn btn-primary">{{ 'Update' }}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $('input[type="file"]').change(function(e) {
            var file = e.target.files[0].name;
            var file_name = $(this).attr('data-filename');
            $('.' + file_name).append(file);
        });
    </script>
    <script>
        $(document).ready(function() {
            var b_id = $('#branch_id').val();
            // getDepartment(b_id);
        });
        $(document).on('change', 'select[name=branch_id]', function() {
            var branch_id = $(this).val();

            getDepartment(branch_id);
        });

        function getDepartment(bid) {
            $.ajax({
                url: '{{ route('monthly.getdepartment') }}',
                type: 'POST',
                data: {
                    "branch_id": bid,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('.department_id').empty();
                    var emp_selct = `<select class="form-control department_id" name="department_id" id="choices-multiple"
                                            placeholder="Select Department" required>
                                            </select>`;
                    $('.department_div').html(emp_selct);

                    $('.department_id').append('<option value=""> {{ __('Select Department') }} </option>');
                    $.each(data, function(key, value) {
                        $('.department_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                    new Choices('#choices-multiple', {
                        removeItemButton: true,
                    });
                }
            });
        }

        $(document).ready(function() {
            // Document ready
        });

        // Make getDesignation return a Promise
        function getDesignation(did) {
            return new Promise((resolve) => {
                $.ajax({
                    url: '{{ route("employee.json") }}',
                    type: 'POST',
                    data: {
                        "department_id": did,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        $('.designation_id').empty();
                        $('.designation_id').append('<option value="">{{ __("Select Designation") }}</option>');
                        
                        $.each(data, function(key, value) {
                            $('.designation_id').append('<option value="' + key + '">' + value + '</option>');
                        });
                        
                        resolve(); // Resolve the promise when done
                    }
                });
            });
        }
    </script>



    <script>
        // Education Details Dynamic Rows
        $(document).ready(function() {
            let educationRowCount = {{ !empty($educations) ? count($educations) : 1 }};
            
            // Add new education row
            $('.add-education-row').click(function() {
                const newRow = `
                    <div class="row education-detail-row mb-3">
                        <div class="form-group col-md-6">
                            <label class="form-label">Degree</label>
                            <select name="education[${educationRowCount}][degree]" class="form-control degree">
                                <option value="10th">10th</option>
                                <option value="12th">12th</option>
                                <option value="Bachelor">Bachelor</option>
                                <option value="Master">Master</option>
                                <option value="PhD">PhD</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">College Name</label>
                            <input type="text" name="education[${educationRowCount}][college_name]" 
                                   class="form-control college-name" placeholder="Enter college name">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Passing Year</label>
                            <select name="education[${educationRowCount}][passing_year]" class="form-control passing-year">
                                <option value="" disabled selected>Select Year</option>
                                @for ($year = 1997; $year <= 2040; $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Grade</label>
                            <input type="number" name="education[${educationRowCount}][grade]" 
                                   class="form-control grade" placeholder="Enter grade" step="0.1" min="0" max="10">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="form-label">Education Document</label>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="choose-files m-0 p-0" style="margin: 0 !important;">
                                    <label for="education[${educationRowCount}][document]" style="margin: 0 !important; display: flex;">
                                        <div class="btn btn-sm btn-primary document cursor-pointer m-0" style="margin: 0 !important; white-space: nowrap;">
                                            <i class="ti ti-upload"></i> Choose file here
                                        </div>
                                        <input type="file" name="education[${educationRowCount}][document]"
                                               id="education[${educationRowCount}][document]"
                                               class="form-control file education-document"
                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    </label>
                                </div>
                                <div class="m-0 p-0 education-preview-link" style="display: none;">
                                    <a href="#" target="_blank" class="btn btn-sm btn-info m-0" style="margin: 0 !important; white-space: nowrap;">
                                        <i class="ti ti-eye"></i> Preview Added File
                                    </a>
                                </div>
                                <div class="m-0 p-0" style="margin: 0 !important;">
                                    <button type="button" class="btn btn-sm btn-danger remove-education-row m-0" style="margin: 0 !important; white-space: nowrap;">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#education-details-container').append(newRow);
                educationRowCount++;
            });
            
            // Remove education row
            $(document).on('click', '.remove-education-row', function() {
                $(this).closest('.education-detail-row').remove();
            });

            // Experience Details Dynamic Rows
            let experienceRowCount = {{ !empty($experiences) ? count($experiences) : 1 }};

            // Add new experience row
            $(document).on('click', '.add-experience-row', function() {
                const newRow = `
                    <div class="row experience-detail-row mb-3">
                        <div class="form-group col-md-6">
                            <label class="form-label">Previous Company Name</label>
                            <input type="text" name="experience[${experienceRowCount}][previous_company_name]" 
                                class="form-control" placeholder="Enter previous company name">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" name="experience[${experienceRowCount}][previous_designation]" 
                                class="form-control" placeholder="Enter designation">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="experience[${experienceRowCount}][start_date]" 
                                class="form-control" placeholder="Select start date">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="experience[${experienceRowCount}][end_date]" 
                                class="form-control" placeholder="Select end date">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="form-label">Previous Salary</label>
                            <input type="number" name="experience[${experienceRowCount}][previous_salary]" 
                                class="form-control" placeholder="Enter previous salary">
                        </div>
                        <div class="form-group col-md-12 text-end">
                            <button type="button" class="btn btn-danger remove-experience-row">
                                <i class="fa fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                `;
                
                $('#experience-details-container').append(newRow);
                experienceRowCount++;
            });

            // Remove experience row
            $(document).on('click', '.remove-experience-row', function() {
                $(this).closest('.experience-detail-row').remove();
            });
        });

        // Phone number validation
        function validateNumbers() {
            const phoneInput = document.getElementsByName('phone')[0];
            const emergencyInput = document.getElementsByName('emergency_number')[0];

            // Remove non-numeric characters immediately
            if (phoneInput) phoneInput.value = phoneInput.value.replace(/\D/g, '');
            if (emergencyInput) emergencyInput.value = emergencyInput.value.replace(/\D/g, '');

            const phone = phoneInput ? phoneInput.value : '';
            const emergencyNumber = emergencyInput ? emergencyInput.value : '';

            const numbers = [phone, emergencyNumber];
            const errorIds = ['phone-error', 'emergency_number-error'];
            
            // Clear previous errors
            errorIds.forEach(id => {
                const el = document.getElementById(id);
                if(el) el.innerText = '';
            });
            
            // Check for duplicates
            for (let i = 0; i < numbers.length; i++) {
                if (numbers[i]) {
                    for (let j = 0; j < numbers.length; j++) {
                        if (i !== j && numbers[i] && numbers[i] === numbers[j]) {
                            const elI = document.getElementById(errorIds[i]);
                            const elJ = document.getElementById(errorIds[j]);
                            if(elI) elI.innerText = 'Do not use the same number in multiple fields.';
                            if(elJ) elJ.innerText = 'Do not use the same number in multiple fields.';
                        }
                    }
                }
            }
        }

        // Project dropdown change event
        document.addEventListener('DOMContentLoaded', function () {
            const projectDropdown = document.getElementById('project_id');
            const siteDropdown = document.getElementById('site_id');

            if (projectDropdown && siteDropdown) {
                projectDropdown.addEventListener('change', function () {
                    const projectId = this.value;

                    // Clear the Site dropdown and show a loading message
                    siteDropdown.innerHTML = '<option value="">Loading...</option>';

                    if (projectId) {
                        // Fetch sites for the selected project
                        fetch(`/get-sites-by-project/${projectId}`)
                            .then(response => response.json())
                            .then(data => {
                                siteDropdown.innerHTML = '<option value="">Select Site</option>';
                                data.sites.forEach(site => {
                                    const option = document.createElement('option');
                                    option.value = site.id;
                                    option.textContent = site.name;
                                    siteDropdown.appendChild(option);
                                });
                            })
                            .catch(error => {
                                console.error('Error fetching sites:', error);
                                siteDropdown.innerHTML = '<option value="">Error loading sites</option>';
                            });
                    } else {
                        siteDropdown.innerHTML = '<option value="">Select Project First</option>';
                    }
                });
            }
        });
    </script>

    <script>
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
                    $('.designation_id').empty();
                    var emp_selct = `<select class="form-control designation_id" name="designation_id" id="choices-multiple"
                                                 placeholder="Select Designation" required>
                                            </select>`;
                    $('.designation_div').html(emp_selct);

                    $('.designation_id').append('<option value=""> {{ __('Select Designation') }} </option>');
                    $.each(data, function(key, value) {
                        $('.designation_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                    new Choices('#choices-multiple', {
                        removeItemButton: true,
                    });
                }
            });
        }
    </script>


    <script>
        $(document).ready(function() {
            // Handle education document preview
            $(document).on('change', '.education-document', function() {
                const input = this;
                const row = $(this).closest('.education-detail-row');
                
                if (input.files && input.files[0]) {
                    const url = window.URL.createObjectURL(input.files[0]);
                    const previewLinkDiv = row.find('.education-preview-link');
                    
                    if(previewLinkDiv.length) {
                        previewLinkDiv.find('a').attr('href', url);
                        previewLinkDiv.show();
                    }

                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        // Remove any existing preview
                        row.find('.document-preview').remove();
                        
                        // Add preview for image files
                        if (input.files[0].type.match('image.*')) {
                            const preview = $('<img class="document-preview mt-2" style="max-width: 200px; max-height: 200px;">');
                            preview.attr('src', e.target.result);
                            row.find('.choose-files').append(preview);
                        }
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                }
            });
        });

        function handleCompanyDocumentPreview(input, key) {
            if (input.files && input.files[0]) {
                var url = window.URL.createObjectURL(input.files[0]);
                var previewLink = document.getElementById('preview_link_' + key);
                if (previewLink) {
                    var aTag = previewLink.querySelector('a');
                    aTag.href = url;
                    previewLink.style.display = 'block';
                }
                
                var img = document.getElementById('blah' + key);
                if (img) {
                    if (input.files[0].type.match('image.*')) {
                        img.src = url;
                        img.style.display = 'block';
                    } else {
                        img.style.display = 'none';
                    }
                }
            }
        }
    </script>
@endpush