<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\GeneralDailyTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GeneralDailyTaskTest extends TestCase
{
    use DatabaseTransactions;

    public function test_employee_can_manage_general_daily_tasks()
    {
        $employeeUser = User::where('email', 'alwyna@digitalizetheglobe.com')->first();
        if ($employeeUser->employee) {
            $employeeUser->employee->update(['approval_status' => 'approved']);
        }
        $this->actingAs($employeeUser);

        // 1. Create task
        $taskData = [
            'project_name' => 'Internal Testing Project',
            'work_date' => '2026-06-24',
            'duration' => 3.5,
            'task_title' => 'Refactoring project updates module',
            'task_description' => 'Splitted updates into project and general tasks, ran migrations, and updated templates.',
        ];

        $response = $this->post('/general-daily-tasks', $taskData);
        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);

        $task = GeneralDailyTask::where('employee_id', $employeeUser->employee->id)->first();
        $this->assertNotNull($task);
        $this->assertEquals('Internal Testing Project', $task->project_name);

        // 2. Update task
        $updateData = [
            'project_name' => 'Internal Testing Project Edited',
            'work_date' => '2026-06-24',
            'duration' => 4.0,
            'task_title' => 'Refactoring project updates module v2',
            'task_description' => 'Updated descriptions.',
        ];

        $response = $this->put("/general-daily-tasks/{$task->id}", $updateData);
        $response->assertStatus(302);

        $task->refresh();
        $this->assertEquals('Internal Testing Project Edited', $task->project_name);
        $this->assertEquals(4.0, $task->duration);

        // 3. View employee updates page
        $response = $this->get('/my-daily-updates');
        $response->assertStatus(200);

        // 4. View HR report page
        $hrUser = User::where('email', 'hr@digitalizetheglobe.com')->first();
        $this->actingAs($hrUser);

        $response = $this->get('/report/employee-daily');
        $response->assertStatus(200);

        // 5. Delete task
        $this->actingAs($employeeUser);
        $response = $this->delete("/general-daily-tasks/{$task->id}");
        $response->assertStatus(302);

        $this->assertNull(GeneralDailyTask::find($task->id));
    }
}
