<?php

namespace Tests\Unit;


use App\Models\User;
use App\Models\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_announcement()
    {

        $user = User::factory()->create(['role' => 'tutor']);

        $category = Category::factory()->create();

        Sanctum::actingAs($user);
   
        $response = $this->postJson('/api/announcements', [
            'title' => 'Test annonce',
            'description' => 'Une super annonce de test',
            'operation_type' => 'sale',
            'price' => 1500,
            'is_completed' => false,
            'is_cancelled' => false,
            'exchange_location_address' => '123 avenue des tests',
            'exchange_location_lng' => 15.2663,
            'exchange_location_lat' => -4.4419,
            'category_id'=>$category->id
            
        ]);

        $response->assertStatus(201);

    }
}
