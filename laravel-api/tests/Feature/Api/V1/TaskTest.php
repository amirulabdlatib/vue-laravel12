<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_can_get_list_tasks()
    {
        // Arrange
        Task::factory()->count(5)->create();

        // Act
        $response = $this->getJson('/api/v1/tasks');

        // Assert
        $response->assertOk();

        $response->assertJsonCount(5,'data');

        $response->assertJsonStructure([
            'data'=>[
                        ['id','name','is_completed']
                ]
            ]);
    }

    public function test_user_can_get_single_task()
    {
        // Arrange
        $task = Task::factory()->create();

        // Act
        $response = $this->getJson('/api/v1/tasks/'. $task->id);

        // Assert
        $response->assertOk();

        $response->assertJsonStructure([
            'data'=>['id','name','is_completed']
        ]);

        $response->assertJson([
            'data'=>[
                    'id'=>$task->id,
                    'name'=>$task->name,
                    'is_completed'=>$task->is_completed
                ]
            ]);
    }

    public function test_user_can_create_a_task()
    {
        $response = $this->postJson('/api/v1/tasks',[
            'name' => 'New task'
        ]);
        
        $response->assertCreated();
        $response->assertJsonStructure([
            'data'=> ['id','name','is_completed']
        ]);

        $this->assertDatabaseHas('tasks',[
            'name' => 'New task',
        ]);
    }

    public function test_user_cannot_create_invalid_task()
    {
        $response = $this->postJson('/api/v1/tasks',[
            'name' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }


}
