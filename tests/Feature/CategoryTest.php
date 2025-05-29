<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_category()
    {


   
        $response = $this->postJson('/api/categories', [
            'name' => 'Test category',
            'description' => 'Une super category de test',
            

        ]);

        $response->assertStatus(201);

    }
}
