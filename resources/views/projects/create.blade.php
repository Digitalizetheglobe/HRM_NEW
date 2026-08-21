@php
    $setting = App\Models\Utility::settings();
    $devDept = \App\Models\Department::where('name', 'Development')
        ->where('created_by', \Auth::user()->creatorId())
        ->first();
    $devDeptId = $devDept ? $devDept->id : '';

    $holidays = \App\Models\Holiday::all(['start_date', 'end_date']);
    $holidayDates = [];
    foreach ($holidays as $holiday) {
        if ($holiday->start_date && $holiday->end_date) {
            $period = \Carbon\CarbonPeriod::create($holiday->start_date, $holiday->end_date);
            foreach ($period as $date) {
                $holidayDates[] = $date->format('Y-m-d');
            }
        }
    }
    $holidayDates = array_unique($holidayDates);
@endphp

{{ Form::open(['route' => ['projects.store'], 'id' => 'projectForm', 'class' => 'needs-validation', 'novalidate' => 'novalidate']) }}
@csrf

<div class="modal-body p-4">
    <!-- Hidden Department ID -->
    <input type="hidden" id="department_id" name="department_id" value="{{ $devDeptId }}">

    <!-- Section: Basic Details -->
    <h6 class="mb-3 fw-bold text-primary border-bottom pb-2">{{ __('Basic Details') }}</h6>
    <div class="row mb-4">
        <!-- Project Name Field -->
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="form-group mb-0">
                {{ Form::label('project_name', __('Project Name'), ['class' => 'form-label fw-bold']) }}
                {{ Form::text('project_name', null, ['class' => 'form-control shadow-sm', 'required' => 'required', 'placeholder' => __('Enter Project Name')]) }}
                <div class="invalid-feedback project_name-error" style="display: none;"></div>
            </div>
        </div>

        <!-- Client Name Field -->
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="form-group mb-0">
                {{ Form::label('client_name', __('Client Name'), ['class' => 'form-label fw-bold']) }}
                {{ Form::text('client_name', null, ['class' => 'form-control shadow-sm', 'required' => 'required', 'placeholder' => __('Enter Client Name')]) }}
            </div>
        </div>

        <!-- Project Description Field -->
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group mb-0">
                {{ Form::label('project_description', __('Project Description'), ['class' => 'form-label fw-bold']) }}
                {{ Form::textarea('project_description', null, ['class' => 'form-control shadow-sm', 'rows' => '3', 'placeholder' => __('Enter Project Description')]) }}
            </div>
        </div>
    </div>

    <!-- Section: Categorization -->
    <h6 class="mb-3 fw-bold text-primary border-bottom pb-2">{{ __('Categorization') }}</h6>
    <div class="row mb-4">
        <!-- Project Lead Field -->
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="form-group mb-0">
                {{ Form::label('project_lead', __('Project Lead'), ['class' => 'form-label fw-bold']) }}
                <select class="form-control select2 shadow-sm" name="project_lead" id="project_lead" data-placeholder="{{ __('Select Project Lead') }}">
                    <option value="">{{ __('Select Project Lead') }}</option>
                    @foreach($teamLeaders as $leader)
                        <option value="{{ $leader->id }}">{{ $leader->name }} {{ $leader->last_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Project Type Field -->
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="form-group mb-0">
                <label for="project_type" class="form-label fw-bold">{{ __('Project Type') }}</label>
                <select class="form-control select2 shadow-sm" name="project_type[]" id="project_type" multiple data-placeholder="{{ __('Select Project Type') }}" required>
                    <option value="HR Software">{{ __('HR Software') }}</option>
                    <option value="Application">{{ __('Application') }}</option>
                    <option value="Dashboard">{{ __('Dashboard') }}</option>
                    <option value="CMS">{{ __('CMS') }}</option>
                    <option value="CRM">{{ __('CRM') }}</option>
                    <option value="Software">{{ __('Software') }}</option>
                    <option value="Web App">{{ __('Web App') }}</option>
                    <option value="Website">{{ __('Website') }}</option>
                    <option value="Landing Page">{{ __('Landing Page') }}</option>
                </select>
                <div class="invalid-feedback project_type-error" style="display: none;"></div>
            </div>
        </div>

        <!-- Technology Field -->
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group mb-0">
                <label for="technology" class="form-label fw-bold">{{ __('Technology') }}</label>
                <select class="form-control select2 shadow-sm" name="technology[]" id="technology" multiple data-placeholder="{{ __('Select Technology') }}">
                    <option value="HTML">HTML</option>
                    <option value="Bootstrap">Bootstrap</option>
                    <option value="PHP">PHP</option>
                    <option value="Laravel">Laravel</option>
                    <option value="React Native">React Native</option>
                    <option value="React JS">React JS</option>
                    <option value="Node JS">Node JS</option>
                    <option value="Next JS">Next JS</option>
                    <option value="Wordpress">Wordpress</option>
                    <option value="SQL">SQL</option>
                    <option value="PostgreSQL">PostgreSQL</option>
                    <option value="MongoDB">MongoDB</option>
                    <option value="Python">Python</option>
                    <option value="JAVA">JAVA</option>
                    <option value="Other">Other</option>
                </select>
                <div class="mt-2" id="custom_technology_container" style="display: none;">
                    {{ Form::text('custom_technology', null, ['class' => 'form-control shadow-sm border-info', 'id' => 'custom_technology', 'placeholder' => __('Enter Custom Technology (e.g. AWS)')]) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Timeline -->
    <h6 class="mb-3 fw-bold text-primary border-bottom pb-2">{{ __('Timeline') }}</h6>
    <div class="row mb-4">
        <!-- Project Start Date Field -->
        <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
            <div class="form-group mb-0">
                {{ Form::label('project_startdate', __('Project Start Date'), ['class' => 'form-label fw-bold']) }}
                <input type="date" class="form-control shadow-sm" name="project_startdate" id="project_startdate" autocomplete="off">
            </div>
        </div>

        <!-- Project Days Field -->
        <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
            <div class="form-group mb-0">
                {{ Form::label('project_days', __('Project Days'), ['class' => 'form-label fw-bold']) }}
                <input type="number" class="form-control shadow-sm" name="project_days" id="project_days" min="1" placeholder="{{ __('Enter Project Days') }}">
            </div>
        </div>

        <!-- Project End Date Field -->
        <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
            <div class="form-group mb-0">
                {{ Form::label('project_enddate', __('Project End Date (Optional)'), ['class' => 'form-label fw-bold']) }}
                <input type="date" class="form-control shadow-sm" name="project_enddate" id="project_enddate" autocomplete="off">
            </div>
        </div>
    </div>

    <!-- Section: Tracking & Options -->
    <h6 class="mb-3 fw-bold text-primary border-bottom pb-2">{{ __('Tracking & Options') }}</h6>
    <div class="row mb-4">
        <!-- Status Field -->
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="form-group mb-0">
                <label for="status" class="form-label fw-bold">{{ __('Status') }}</label>
                <select class="form-control shadow-sm" name="status" id="status">
                    <option value="on_boarding">{{ __('On Boarding') }}</option>
                    <option value="on_hold">{{ __('On Hold') }}</option>
                    <option value="assigned">{{ __('Assigned') }}</option>
                    <option value="in_progress">{{ __('In Progress') }}</option>
                    <option value="completed">{{ __('Completed') }}</option>
                </select>
            </div>
        </div>

        <!-- Current Status Field -->
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="form-group mb-0">
                {{ Form::label('current_status', __('Current Status'), ['class' => 'form-label fw-bold']) }}
                {{ Form::text('current_status', null, ['class' => 'form-control shadow-sm', 'placeholder' => __('Enter Current Status')]) }}
            </div>
        </div>

        <!-- Delay Reason Field -->
        <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
            <div class="form-group mb-0">
                {{ Form::label('delay_reason', __('Delay Reason'), ['class' => 'form-label fw-bold']) }}
                {{ Form::textarea('delay_reason', null, ['class' => 'form-control shadow-sm', 'rows' => '2', 'placeholder' => __('Enter Delay Reason')]) }}
            </div>
        </div>
        
        <!-- Toggles Container in Card -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 bg-light p-3 mb-3">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                        <div class="form-group mb-0">
                            <div class="form-check form-switch custom-switch-v1">
                                <input type="checkbox" class="form-check-input input-primary" name="ui_ux_required" id="ui_ux_required" value="1">
                                <label class="form-check-label fw-bold" for="ui_ux_required">{{ __('UI/UX Required for this project?') }}</label>
                            </div>
                            <small class="text-muted d-block mt-1">{{ __('Enable this if the project needs dedicated UI/UX design work before development.') }}</small>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                        <div class="form-group mb-0">
                            <div class="form-check form-switch custom-switch-v1">
                                <input type="checkbox" class="form-check-input input-primary" name="has_urls" id="has_urls" value="1">
                                <label class="form-check-label fw-bold" for="has_urls">{{ __('Add Project URLs?') }}</label>
                            </div>
                            <small class="text-muted d-block mt-1">{{ __('Enable this to add website and dashboard URLs for this project.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project URLs Fields (Hidden by default) -->
        <div class="col-12 mb-3" id="project_urls_div" style="display: none;">
            <div class="card shadow-sm border-info p-3 mb-0">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group mb-0">
                            {{ Form::label('website_url', __('Website URL'), ['class' => 'form-label fw-bold']) }}
                            {{ Form::url('website_url', null, ['class' => 'form-control shadow-sm', 'placeholder' => __('https://example.com')]) }}
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group mb-0">
                            {{ Form::label('dashboard_url', __('Dashboard URL'), ['class' => 'form-label fw-bold']) }}
                            {{ Form::url('dashboard_url', null, ['class' => 'form-control shadow-sm', 'placeholder' => __('https://admin.example.com')]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Team Assignments -->
    <h6 class="mb-3 fw-bold text-primary border-bottom pb-2">{{ __('Team Assignments') }}</h6>
    <div class="row">
        <!-- UI/UX Employee Selection (Hidden by default) -->
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3" id="ui_ux_selection_div" style="display: none;">
            <div class="form-group mb-0">
                {{ Form::label('ui_ux_employee_id', __('Assign UI/UX Designer'), ['class' => 'form-label fw-bold text-black']) }}
                <select class="form-control shadow-sm border-info" id="ui_ux_employee_id">
                    <option value="">{{ __('Select UI/UX Designer') }}</option>
                </select>
            </div>
        </div>

        <!-- Employee Selection -->
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="form-group mb-0">
                {{ Form::label('employee_id', __('Assign Developer'), ['class' => 'form-label fw-bold']) }}
                <select class="form-control shadow-sm" id="employee_id">
                    <option value="">{{ __('Select Developer') }}</option>
                </select>
            </div>
        </div>

        <!-- Hidden field for assigned_data -->
        <input type="hidden" name="assigned_data" id="assignedData">

        <div class="col-md-12 mt-2">
            <div class="form-group mb-0">
                <label class="form-label fw-bold">{{ __('Selected Assignments') }}</label>
                <div id="selectedAssignmentsBox" class="p-3 border rounded shadow-sm bg-white" style="min-height: 100px;">
                    <div class="text-muted text-center pt-2">{{ __('No assignments selected. Please select a designer or developer above.') }}</div>
                </div>
                <div class="invalid-feedback assigned_data-error" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

<script>
$(document).ready(function() {
    const holidayDates = @json($holidayDates);

    function calculateEndDate() {
        const startDateStr = $('#project_startdate').val();
        const daysStr = $('#project_days').val();
        
        if (!startDateStr || !daysStr) {
            $('#project_enddate').val('');
            return;
        }
        
        let days = parseInt(daysStr, 10);
        if (isNaN(days) || days <= 0) return;
        
        let currentDate = new Date(startDateStr);
        let daysToAdd = days;
        
        while (daysToAdd > 0) {
            currentDate.setDate(currentDate.getDate() + 1);
            
            // Check if Sunday (0)
            if (currentDate.getDay() === 0) {
                continue;
            }
            
            // Format to YYYY-MM-DD using local time
            let localDateString = new Date(currentDate.getTime() - (currentDate.getTimezoneOffset() * 60000)).toISOString().split('T')[0];

            // Check if Holiday
            if (holidayDates.includes(localDateString)) {
                continue;
            }
            
            daysToAdd--;
        }
        
        let finalDateString = new Date(currentDate.getTime() - (currentDate.getTimezoneOffset() * 60000)).toISOString().split('T')[0];
        $('#project_enddate').val(finalDateString);
    }

    $('#project_startdate, #project_days').on('change keyup', calculateEndDate);

    // Initialize select2 for technology if available
    if ($.fn.select2) {
        $('#technology').select2({
            dropdownParent: $('#projectForm'), // Sometimes needed if in a modal
            placeholder: "Select Technology",
            allowClear: true,
            closeOnSelect: false
        });
        $('#project_type').select2({
            dropdownParent: $('#projectForm'),
            placeholder: "Select Project Type",
            allowClear: true,
            closeOnSelect: false
        });
        $('#project_lead').select2({
            dropdownParent: $('#projectForm'),
            placeholder: "Select Project Lead",
            allowClear: true
        });
    }

    // Handle 'Other' technology selection
    $('#technology').on('change', function() {
        const selected = $(this).val() || [];
        if (selected.includes('Other')) {
            $('#custom_technology_container').show();
        } else {
            $('#custom_technology_container').hide();
            $('#custom_technology').val('');
        }
    });

    // Initialize data structure
    let assignments = []; // Array of { department_id, department_name, employees: [] }
    let allEmployees = {}; // Cache of employees by department: { deptId: [employee1, employee2] }
    const devDeptId = '{{ $devDeptId }}';
    const uiUxDeptId = '{{ $uiUxDeptId }}';

    // Handle UI/UX Toggle
    $('#ui_ux_required').change(function() {
        if ($(this).is(':checked')) {
            $('#ui_ux_selection_div').fadeIn();
            if (uiUxDeptId && !allEmployees[uiUxDeptId]) {
                loadEmployeesForDept(uiUxDeptId, '#ui_ux_employee_id');
            } else if (uiUxDeptId) {
                updateEmployeeDropdown(uiUxDeptId, '#ui_ux_employee_id');
            }
        } else {
            $('#ui_ux_selection_div').fadeOut();
            // Optionally remove UI/UX assignments when disabled
            assignments = assignments.filter(a => a.department_id != uiUxDeptId);
            updateAssignmentsUI();
        }
    });

    // Handle Project URLs Toggle
    $('#has_urls').change(function() {
        if ($(this).is(':checked')) {
            $('#project_urls_div').fadeIn();
        } else {
            $('#project_urls_div').fadeOut();
            // Clear inputs when hiding
            $('#website_url').val('');
            $('#dashboard_url').val('');
        }
    });

    function loadEmployeesForDept(deptId, selectId) {
        const employeeSelect = $(selectId);
        employeeSelect.prop('disabled', false);
        employeeSelect.html('<option value="">{{ __("Loading employees...") }}</option>');

        $.ajax({
            url: '{{ route("get-employees-by-department", "") }}/' + deptId,
            type: 'GET',
            success: function(data) {
                allEmployees[deptId] = data;
                updateEmployeeDropdown(deptId, selectId);
            },
            error: function(xhr) {
                console.error('Error loading employees:', xhr);
                $(selectId).html('<option value="">{{ __("Error loading employees") }}</option>');
            }
        });
    }

    if (devDeptId) {
        // Automatically load Development department employees
        loadEmployeesForDept(devDeptId, '#employee_id');
    }

    // Update the updateEmployeeDropdown function to accept a target select element
    function updateEmployeeDropdown(departmentId, selectId = '#employee_id') {
        const currentEmployees = allEmployees[departmentId] || [];
        console.log('All employees for department', departmentId, ':', currentEmployees);
        
        const selectedInDepartment = [];
        assignments.filter(a => a.department_id == departmentId).forEach(a => {
            if (a.employees) {
                a.employees.forEach(e => selectedInDepartment.push(String(e.id)));
            }
            if (a.employee_ids) {
                a.employee_ids.forEach(id => selectedInDepartment.push(String(id)));
            }
        });

        let options = '<option value="">{{ __("Select Employee") }}</option>';
        let availableCount = 0;
        
        currentEmployees.forEach(employee => {
            if (!selectedInDepartment.includes(String(employee.id))) {
                options += `<option value="${employee.id}">${employee.name}</option>`;
                availableCount++;
            }
        });
        
        console.log('Available employees after filtering:', availableCount);
        // Only clear available count warning since it depends on the dept.
        
        $(selectId).html(options);
        
        if (availableCount === 0) {
            console.log('No employees available - all might be selected or excluded');
        }
    }

    // Add an event listener for site heads changes to refresh the employee dropdown

    function assignEmployeeFromSelect(selectElement, deptId, deptName, updateSelectId) {
        const employeeId = selectElement.val();
        const employeeName = selectElement.find('option:selected').text();

        if (!employeeId || !deptId) return;

        // Find or create department assignment
        let assignment = assignments.find(a => a.department_id == deptId);
        if (!assignment) {
            assignment = {
                department_id: deptId,
                department_name: deptName,
                employees: []
            };
            assignments.push(assignment);
        }

        // Add employee if not already exists
        if (!assignment.employees.some(e => e.id == employeeId)) {
            assignment.employees.push({
                id: employeeId,
                name: employeeName
            });
            updateAssignmentsUI();
            updateEmployeeDropdown(deptId, updateSelectId); // Refresh dropdown
        }

        selectElement.val('');
    }

    // Add employee to Dev assignment
    $('#employee_id').change(function() {
        assignEmployeeFromSelect($(this), $('#department_id').val(), 'Development', '#employee_id');
    });

    // Add employee to UI/UX assignment
    $('#ui_ux_employee_id').change(function() {
        assignEmployeeFromSelect($(this), uiUxDeptId, 'UI-UX Designer', '#ui_ux_employee_id');
    });

    // Update the assignments UI and hidden field
    function updateAssignmentsUI() {
        const container = $('#selectedAssignmentsBox');
        container.empty();

        if (assignments.length === 0) {
            container.html('<div class="text-muted">{{ __("No assignments selected.") }}</div>');
            $('#assignedData').val(JSON.stringify([]));
            return;
        }

        assignments.forEach(assignment => {
            const deptDiv = $(`
                <div class="mb-3 assignment-group" data-dept-id="${assignment.department_id}">
                    <h6 class="mb-1">${assignment.department_name}</h6>
                    <div class="d-flex flex-wrap employees-container"></div>
                </div>
            `);

            const employeesContainer = deptDiv.find('.employees-container');
            
            if (assignment.employees.length === 0) {
                employeesContainer.append('<span class="text-muted small">{{ __("No employees selected") }}</span>');
            } else {
                assignment.employees.forEach(employee => {
                    employeesContainer.append(`
                        <span class="badge bg-primary me-1 mb-1 employee-badge" 
                              data-dept-id="${assignment.department_id}" 
                              data-emp-id="${employee.id}">
                            ${employee.name}
                            <i class="fas fa-times ms-1 remove-employee" style="cursor: pointer;"></i>
                        </span>
                    `);
                });
            }

            container.append(deptDiv);
        });

        // Update hidden field with the current assignments
        const formattedData = assignments.map(assignment => ({
            department_id: assignment.department_id,
            employee_ids: assignment.employees.map(emp => emp.id)
        }));
        
        $('#assignedData').val(JSON.stringify(formattedData));
    }

    // Remove employee from assignment
    $(document).on('click', '.remove-employee', function() {
        const badge = $(this).closest('.employee-badge');
        const departmentId = badge.data('dept-id');
        const employeeId = badge.data('emp-id');

        // Find the assignment
        const assignment = assignments.find(a => a.department_id == departmentId);
        if (assignment) {
            // Remove the employee
            assignment.employees = assignment.employees.filter(e => e.id != employeeId);
            
            // If no more employees, remove the entire assignment
            if (assignment.employees.length === 0) {
                assignments = assignments.filter(a => a.department_id != departmentId);
            }
            
            updateAssignmentsUI();
            // Refresh the right dropdown
            if (departmentId == uiUxDeptId) {
                updateEmployeeDropdown(departmentId, '#ui_ux_employee_id');
            } else {
                updateEmployeeDropdown(departmentId, '#employee_id');
            }
        }
    });

    // For edit view - pre-populate assignments if editing
    @if(isset($project) && $project->assigned_data)
        // First load all departments with their employees
        const departmentIds = {!! json_encode(collect($project->assigned_data)->pluck('department_id')->unique()) !!};
        
        // Fetch all needed employees in one query
        $.ajax({
            url: '{{ route("get-employees-by-departments") }}',
            type: 'POST',
            data: {
                department_ids: departmentIds,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                // Cache all employees by department
                data.forEach(dept => {
                    allEmployees[dept.department_id] = dept.employees;
                });
                
                // Now populate assignments
                assignments = {!! json_encode(
                    collect($project->assigned_data)->map(function($item) {
                        $department = Department::find($item['department_id']);
                        return [
                            'department_id' => $item['department_id'],
                            'department_name' => $department ? $department->name : 'Unknown',
                            'employees' => Employee::whereIn('id', $item['employee_ids'])
                                ->get()
                                ->map(function($emp) {
                                    return ['id' => $emp->id, 'name' => $emp->full_name];
                                })
                                ->toArray()
                        ];
                    })
                ) !!};
                
                updateAssignmentsUI();
                
                // Set the first department as selected
                if (assignments.length > 0) {
                    $('#department_id').val(assignments[0].department_id).trigger('change');
                }
            },
            error: function(xhr) {
                console.error('Error loading employees for edit:', xhr);
            }
        });
    @endif
});


// Initialize select2 for site heads
$('#site_heads').select2({
    placeholder: "Select Site Heads",
    allowClear: true
});

// When site heads change, refresh the employee dropdown if a department is selected


</script>


<script>
$('#projectForm').on('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();
    
    let isValid = true;
    
    // Check required fields
    if (!$('#project_name').val().trim()) {
        showError('project_name', 'Project name is required');
        isValid = false;
    }

    const projectTypeVal = $('#project_type').val();
    if (!projectTypeVal || projectTypeVal.length === 0) {
        showError('project_type', 'Project type is required');
        isValid = false;
    }
    
    if (!$('#department_id').val()) {
        showError('employee_id', 'Development department not found');
        isValid = false;
    }
    
    // Check date validation if dates are provided
    const startDate = $('#project_startdate').val();
    const endDate = $('#project_enddate').val();
    
    if (startDate && endDate) {
        if (new Date(endDate) < new Date(startDate)) {
            showError('project_enddate', 'End date must be after start date');
            isValid = false;
        }
    }
    
    // Check assignments
    if (!validateAssignments()) {
        isValid = false;
    }
    
    if (isValid) {
        let formData = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            },
            error: function(xhr) {
                // Handle errors
            }
        });
    }
});

function showError(fieldName, message) {
    $(`#${fieldName}`).addClass('is-invalid');
    $(`.${fieldName}-error`).text(message).show();
}

// Real-time validation for fields when they lose focus
$('#project_name, #department_id').on('blur', function() {
    const field = $(this);
    const fieldName = field.attr('id');
    
    if (fieldName === 'project_name' && !field.val().trim()) {
        showError(fieldName, 'Project name is required');
    } else {
        field.removeClass('is-invalid');
        $(`.${fieldName}-error`).hide();
    }
});

$('#project_type').on('blur', function() {
    if (!$(this).val()) {
        showError('project_type', 'Project type is required');
    } else {
        $(this).removeClass('is-invalid');
        $('.project_type-error').hide();
    }
});

// Validate assigned data when adding/removing employees
function validateAssignments() {
    const assignedData = JSON.parse($('#assignedData').val() || '[]');
    const errorElement = $('.assigned_data-error');
    
    if (assignedData.length === 0) {
        errorElement.text('At least one employee assignment is required').show();
        $('#selectedAssignmentsBox').addClass('is-invalid');
        return false;
    }
    
    let hasEmployees = false;
    assignedData.forEach(assignment => {
        if (assignment.employee_ids && assignment.employee_ids.length > 0) {
            hasEmployees = true;
        }
    });
    
    if (!hasEmployees) {
        errorElement.text('At least one employee must be assigned').show();
        $('#selectedAssignmentsBox').addClass('is-invalid');
        return false;
    }
    
    errorElement.hide();
    $('#selectedAssignmentsBox').removeClass('is-invalid');
    return true;
}

// Call validateAssignments when assignments change
function updateAssignmentsUI() {
    // ... your existing code ...
    
    // Add validation check
    validateAssignments();
}
</script>