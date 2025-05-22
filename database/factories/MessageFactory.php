<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    
    protected $model = Message::class;
    

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        return [
            'conversation' => Chat::factory(),
            'sender' => $sender->id,
            'receiver' => $receiver->id,
            'content' => $this->faker->sentence,
        ];
    }
}
