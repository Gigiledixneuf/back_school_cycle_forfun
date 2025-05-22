<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Chat;
use App\Models\Announcement;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_chat_send_and_receive_messages_and_close_chat()
    {
        // Création des utilisateurs
        $owner = User::factory()->create(); // Celui qui poste l'annonce
        $other = User::factory()->create(); // Celui qui va discuter

        // Création d'une annonce par le propriétaire
        $announcement = Announcement::factory()->create([
            'created_by' => $owner->id,
        ]);

        // L'utilisateur autre ne peut pas créer un chat avec sa propre annonce
        $this->actingAs($owner);
        $response = $this->postJson("/api/announcements/{$announcement->id}/chats", [
            'created_by' => $owner->id,
            'posted_by' => $announcement->id,
        ]);
        $response->assertStatus(403);

        // L'autre utilisateur peut créer un chat
        $this->actingAs($other);
        $response = $this->postJson("/api/announcements/{$announcement->id}/chats", [
            'created_by' => $other->id,
            'posted_by' => $announcement->id,
        ]);
        $response->assertStatus(201);
        $chatId = $response->json('id');
        $this->assertDatabaseHas('chats', [
            'id' => $chatId,
            'created_by' => $other->id,
            'posted_by' => $announcement->id,
            'is_closed' => false,
        ]);

        // L'autre utilisateur envoie un message
        $sendMessage = $this->postJson("/api/chats/{$chatId}/messages", [
            'content' => 'Bonjour !'
        ]);
        $sendMessage->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'conversation' => $chatId,
            'content' => 'Bonjour !',
            'sender' => $other->id,
            'receiver' => $owner->id,
        ]);

        // Le propriétaire peut voir le message
        $this->actingAs($owner);
        $response = $this->getJson("/api/chats/{$chatId}/messages");
        $response->assertStatus(200)
                 ->assertJsonFragment(['content' => 'Bonjour !']);

        // Un utilisateur non concerné ne peut pas voir les messages
        $stranger = User::factory()->create();
        $this->actingAs($stranger);
        $response = $this->getJson("/api/chats/{$chatId}/messages");
        $response->assertStatus(403);

        // Seul le créateur du chat peut fermer le chat
        $this->actingAs($other);
        $closeResponse = $this->postJson("/api/chats/{$chatId}/close");
        $closeResponse->assertStatus(200);
        $this->assertDatabaseHas('chats', [
            'id' => $chatId,
            'is_closed' => false, // reste ouvert 30 jours
        ]);
        $this->assertNotNull(Chat::find($chatId)->close_to);

        // Un autre utilisateur ne peut pas fermer le chat
        $this->actingAs($owner);
        $closeResponse = $this->postJson("/api/chats/{$chatId}/close");
        $closeResponse->assertStatus(403);
        
    }
}