<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Employee;
use App\Models\Termination;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TerminationFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_active_employee_can_login()
    {
        $user = User::create([
            'name' => 'Test Employee',
            'email' => 'test_emp_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'type' => 'employee',
            'is_active' => 1,
        ]);
        
        $employee = Employee::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'employee_id' => 'EMP_' . uniqid(),
            'company_doj' => '2025-01-01',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect();
    }

    public function test_terminated_employee_cannot_login()
    {
        $user = User::create([
            'name' => 'Test Employee',
            'email' => 'test_emp_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'type' => 'employee',
            'is_active' => 1,
        ]);
        
        $employee = Employee::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'employee_id' => 'EMP_' . uniqid(),
            'company_doj' => '2025-01-01',
        ]);

        // Create termination
        Termination::create([
            'employee_id' => $employee->id,
            'notice_date' => '2026-06-11',
            'termination_date' => '2026-06-25', // Future date
            'termination_type' => 1,
            'description' => 'Discharged',
            'created_by' => 1,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Your account has been terminated. Please contact administrator.');
    }

    public function test_employee_logged_out_on_next_request_after_termination()
    {
        $user = User::create([
            'name' => 'Test Employee',
            'email' => 'test_emp_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'type' => 'employee',
            'is_active' => 1,
            'email_verified_at' => now(), // Avoid verification check redirection
        ]);
        
        $employee = Employee::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'employee_id' => 'EMP_' . uniqid(),
            'company_doj' => '2025-01-01',
        ]);

        // Log in via post to establish session
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($user);

        // Access dashboard; should redirect but remain authenticated
        $response = $this->get('/dashboard');
        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);

        // Create termination
        Termination::create([
            'employee_id' => $employee->id,
            'notice_date' => '2026-06-11',
            'termination_date' => '2026-06-25',
            'termination_type' => 1,
            'description' => 'Discharged',
            'created_by' => 1,
        ]);

        // Attempt next request; should log out and redirect to login
        $response = $this->get('/dashboard');
        
        // Assert redirected to login, guest session, and error message present
        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Your account has been terminated. Please contact administrator.');
    }
}
