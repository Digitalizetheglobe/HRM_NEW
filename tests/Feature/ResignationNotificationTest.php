<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Resignation;
use App\Models\Utility;
use App\Models\UserEmailTemplate;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonEmailTemplate;
use Illuminate\Support\Facades\Hash;

class ResignationNotificationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function employee_submitting_resignation_sends_email_to_company_and_approval_sends_email_to_employee()
    {
        Mail::fake();

        // 1. Find the company user
        $company = User::where('type', 'company')->first();
        $this->assertNotNull($company, "Company user not found in database.");
        
        if (empty($company->email_verified_at)) {
            $company->email_verified_at = date("Y-m-d H:i:s");
            $company->save();
        }

        // 2. Find or create an employee belonging to the company
        $employee = Employee::where('created_by', $company->id)->first();
        if (!$employee) {
            $employeeUser = User::create([
                'name' => 'Test Employee',
                'email' => 'employee_test_notification@example.com',
                'password' => Hash::make('password'),
                'type' => 'employee',
                'created_by' => $company->id,
                'lang' => 'en',
                'email_verified_at' => date("Y-m-d H:i:s"),
            ]);
            $employee = Employee::create([
                'user_id' => $employeeUser->id,
                'name' => 'Test Employee',
                'email' => 'employee_test_notification@example.com',
                'password' => Hash::make('password'),
                'employee_id' => '#EMP001',
                'created_by' => $company->id,
                'approval_status' => 'approved',
            ]);
        } else {
            $employeeUser = User::find($employee->user_id);
            if (!$employeeUser) {
                $employeeUser = User::create([
                    'name' => $employee->name,
                    'email' => $employee->email ?? 'employee_test_notification@example.com',
                    'password' => Hash::make('password'),
                    'type' => 'employee',
                    'created_by' => $company->id,
                    'lang' => 'en',
                    'email_verified_at' => date("Y-m-d H:i:s"),
                ]);
                $employee->user_id = $employeeUser->id;
            } else {
                $employeeUser->email_verified_at = date("Y-m-d H:i:s");
                $employeeUser->save();
            }
            $employee->approval_status = 'approved';
            $employee->save();
        }

        // Ensure the employee_resignation template exists and is active for the company
        $template = EmailTemplate::where('slug', 'employee_resignation')->first();
        $this->assertNotNull($template, "employee_resignation email template not found.");
        
        $userTemplate = UserEmailTemplate::where('user_id', $company->id)
            ->where('template_id', $template->id)
            ->first();
        if (!$userTemplate) {
            UserEmailTemplate::create([
                'user_id' => $company->id,
                'template_id' => $template->id,
                'is_active' => 1
            ]);
        } else {
            $userTemplate->is_active = 1;
            $userTemplate->save();
        }

        // Clean up any existing resignation for this employee so we can submit one
        Resignation::where('employee_id', $employee->id)->delete();

        // Ensure employee has permission
        $employeeUser->givePermissionTo('Create Resignation');
        $employeeUser->givePermissionTo('Manage Resignation');

        // 3. Act as the employee and submit a resignation request
        $noticeDate = date('Y-m-d');
        $resignationDate = date('Y-m-d', strtotime('+30 days'));

        $response = $this->actingAs($employeeUser)->post(route('resignation.store'), [
            'notice_date' => $noticeDate,
            'resignation_date' => $resignationDate,
            'description' => 'Submitting my resignation.',
        ]);

        $response->assertRedirect(route('resignation.index'));

        // Assert that the resignation request was saved in DB
        $resignation = Resignation::where('employee_id', $employee->id)->first();
        $this->assertNotNull($resignation);

        // Assert that email was sent to company
        $companyEmail = !empty($company->email) ? $company->email : 'hr@digitalizetheglobe.com';
        Mail::assertSent(CommonEmailTemplate::class, function ($mail) use ($companyEmail) {
            return $mail->hasTo($companyEmail) &&
                   $mail->template->parent_id == 7; // employee_resignation template ID is 7
        });

        // Clear mock to test approval email separately
        Mail::fake();

        // Ensure company user has permission to approve
        $company->givePermissionTo('Manage Resignation');

        // 4. Act as the company user and approve the resignation
        $approveResponse = $this->actingAs($company)->post(route('resignation.approve', $resignation->id), [
            'notice_date' => $noticeDate,
            'resignation_date' => $resignationDate,
        ]);

        $approveResponse->assertRedirect(route('resignation.index'));

        // Assert that email was sent to the employee
        $employeeEmail = $employeeUser->email;
        Mail::assertSent(CommonEmailTemplate::class, function ($mail) use ($employeeEmail) {
            return $mail->hasTo($employeeEmail) &&
                   $mail->template->parent_id == 7; // employee_resignation template ID is 7
        });
    }
}
