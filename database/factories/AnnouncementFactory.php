<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'title'=>$this->faker->word(),
            'description'=>$this->faker->paragraph(),
            'category_id'=> Category::inRandomOrder()->first()->id ?? Category::factory(),
            'operation_type'=>$this->faker->randomElement(['don', 'sale', 'exchange']),
            'price'=>$this->faker->numberBetween(100, 10000),
            'is_completed'=>false,
            'is_cancelled'=>false,
            'exchange_location_address'=>$this->faker->address(),
            'exchange_location_lng'=>$this->faker->longitude(),
            'exchange_location_lat'=>$this->faker->latitude(),
            'created_by'=>User::inRandomOrder()->first()->id ?? User::factory()
        ];
    }
}
