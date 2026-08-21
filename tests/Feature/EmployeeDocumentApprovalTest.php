<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Document;
use App\Models\EmployeeDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeDocumentApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function employee_without_approved_documents_cannot_access_attendance()
    {
        // Create a company user
        $company = User::factory()->create(['type' => 'company']);
        
        // Create an employee user
        $employeeUser = User::factory()->create(['type' => 'employee', 'created_by' => $company->id]);
        
        // Create employee record without approval
        $employee = Employee::factory()->create([
            'user_id' => $employeeUser->id,
            'created_by' => $company->id,
            'approval_status' => null // Not approved
        ]);
        
        // Create required documents
        $requiredDoc = Document::factory()->create([
            'created_by' => $company->id,
            'is_required' => 1
        ]);
        
        // Act as employee and try to access attendance
        $response = $this->actingAs($employeeUser)
                        ->get(route('attendance.calendar'));
        
        // Should be redirected to employee profile
        $response->assertRedirect(route('employee.show', encrypt($employee->id)));
    }

    /** @test */
    public function employee_with_approved_documents_can_access_attendance()
    {
        // Create a company user
        $company = User::factory()->create(['type' => 'company']);
        
        // Create an employee user
        $employeeUser = User::factory()->create(['type' => 'employee', 'created_by' => $company->id]);
        
        // Create employee record with approval
        $employee = Employee::factory()->create([
            'user_id' => $employeeUser->id,
            'created_by' => $company->id,
            'approval_status' => 'approved' // Already approved
        ]);
        
        // Act as employee and try to access attendance
        $response = $this->actingAs($employeeUser)
                        ->get(route('attendance.calendar'));
        
        // Should be successful
        $response->assertSuccessful();
    }

    /** @test */
    public function employee_with_uploaded_required_documents_can_access_system()
    {
        // Create a company user
        $company = User::factory()->create(['type' => 'company']);
        
        // Create an employee user
        $employeeUser = User::factory()->create(['type' => 'employee', 'created_by' => $company->id]);
        
        // Create employee record
        $employee = Employee::factory()->create([
            'user_id' => $employeeUser->id,
            'created_by' => $company->id,
            'approval_status' => null
        ]);
        
        // Create required documents
        $requiredDoc = Document::factory()->create([
            'created_by' => $company->id,
            'is_required' => 1
        ]);
        
        // Upload required document
        EmployeeDocument::factory()->create([
            'employee_id' => $employee->id,
            'document_id' => $requiredDoc->id,
            'document_value' => 'path/to/document.pdf'
        ]);
        
        // Test the hasApprovedDocuments method
        $this->assertTrue($employee->hasApprovedDocuments());
        $this->assertTrue($employee->canAccessSystem());
        
        // Act as employee and try to access attendance
        $response = $this->actingAs($employeeUser)
                        ->get(route('attendance.calendar'));
        
        // Should be successful
        $response->assertSuccessful();
    }

    /** @test */
    public function employee_can_always_access_their_profile()
    {
        // Create a company user
        $company = User::factory()->create(['type' => 'company']);
        
        // Create an employee user
        $employeeUser = User::factory()->create(['type' => 'employee', 'created_by' => $company->id]);
        
        // Create employee record without approval
        $employee = Employee::factory()->create([
            'user_id' => $employeeUser->id,
            'created_by' => $company->id,
            'approval_status' => null
        ]);
        
        // Create required documents
        $requiredDoc = Document::factory()->create([
            'created_by' => $company->id,
            'is_required' => 1
        ]);
        
        // Act as employee and try to access their profile
        $response = $this->actingAs($employeeUser)
                        ->get(route('employee.show', encrypt($employee->id)));
        
        // Should be successful
        $response->assertSuccessful();
    }
}
