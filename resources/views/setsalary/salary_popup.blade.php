<div class="modal-body">
    <style>
        .modal-dialog {
            max-width: 400px;
            margin: 30px auto;
        }
        .modal-body {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .col-form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }
        .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 12px 15px;
            transition: all 0.3s ease;
            width: 100%;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
            outline: none;
        }
        .btn-create {
            background: #ea3538;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 500;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-create:hover {
            background: #c72d2f;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(234,53,56,0.3);
        }
        .employee-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #ea3538;
        }
        .employee-name {
            font-weight: 600;
            color: #495057;
            font-size: 16px;
            margin: 0;
        }
    </style>
    
    <div class="employee-info">
        <p class="employee-name">{{ __('Employee Name') }}: {{ $employee->full_name }}</p>
    </div>
    
    {{ Form::model($employee, ['route' => ['employee.salary.update', $employee->id], 'method' => 'POST', 'id' => 'salary-form']) }}

        <div class="form-group">
            {{ Form::label('salary', __('Salary'), ['class' => 'col-form-label']) }}
            {{ Form::number('salary', null, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
        </div>
        
        @can('Create Set Salary')
            <div class="form-group">
                <input type="submit" value="{{ __('Save Change') }}" class="btn-create">
            </div>
        @endcan
    {{ Form::close() }}

<script>
$(document).ready(function() {
    // Add loading state to forms
    function showLoading(form) {
        form.find('input[type="submit"]').prop('disabled', true).html('<i class="ti ti-loader fa-spin"></i> Saving...');
    }
    
    function hideLoading(form) {
        form.find('input[type="submit"]').prop('disabled', false).html('Save Change');
    }
    
    // Handle form submissions within popup
    $('#salary-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val() || form.attr('method');
        
        // Show loading state
        showLoading(form);
        
        $.ajax({
            url: url,
            method: method,
            data: form.serialize(),
            success: function(response) {
                // Hide loading state
                hideLoading(form);
                
                // Show success message
                if (typeof show_toastr === 'function') {
                    show_toastr('Success', response.message || 'Salary updated successfully!', 'success');
                } else {
                    alert(response.message || 'Salary updated successfully!');
                }
                
                // Close modal after a short delay and update table
                setTimeout(function() {
                    $('#commonModal').modal('hide');
                    
                    // Find the employee row in the table and update it
                    var employeeId = '{{ $employee->id }}';
                    var table = $('#pc-dt-simple').DataTable();
                    
                    // Find the row containing this employee
                    table.rows().every(function() {
                        var row = this.node();
                        var employeeLink = $(row).find('td:first-child a').text();
                        
                        // Match by employee ID format (assuming the employee ID is displayed in the first column)
                        if (employeeLink.includes('{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}')) {
                            // Update the salary columns
                            $(row).find('td:nth-child(3)').text(response.salary_type || '-');
                            $(row).find('td:nth-child(4)').text(response.salary || '0');
                            $(row).find('td:nth-child(5)').text(response.net_salary || '-');
                        }
                    });
                }, 1500);
            },
            error: function(xhr) {
                // Hide loading state
                hideLoading(form);
                
                // Show error message
                var errorMessage = 'An error occurred';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        var errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join(', ');
                    }
                }
                
                if (typeof show_toastr === 'function') {
                    show_toastr('Error', errorMessage, 'error');
                } else {
                    alert('Error: ' + errorMessage);
                }
            }
        });
    });
});
</script>
