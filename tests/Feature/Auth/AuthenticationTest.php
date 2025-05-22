<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful login returns AuthLoginResource with token.
     */
    public function test_store_returns_token_on_successful_login()
    {
        // Création d'un utilisateur avec mot de passe hashé
        $password = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        // Préparation des données valides
        $data = [
            'email' => $user->email,
            'password' => $password,
        ];

        // Appel de la route POST login (adapter selon votre route)
        $response = $this->postJson(route('login'), $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                    'id',
                    'email',
                    'token',
            ]);

        // Vérifier que le token est bien présent et non vide
        $this->assertNotEmpty($response->json('token'));
    }

    /**
     * Test login fails with wrong password.
     */
    public function test_store_returns_unauthorized_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct_password'),
        ]);

        $data = [
            'email' => $user->email,
            'password' => 'wrong_password',
        ];

        $response = $this->postJson(route('login'), $data);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized',
            ]);
    }

    /**
     * Test login fails when user does not exist.
     */
    public function test_store_returns_unauthorized_when_user_not_found()
    {
        $data = [
            'email' => 'nonexistent@example.com',
            'password' => 'any_password',
        ];

        $response = $this->postJson(route('login'), $data);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized',
            ]);
    }

    /**
     * Test successful logout deletes current access token.
     */
    public function test_destroy_deletes_token_and_returns_logout_true()
    {
        $user = User::factory()->create();

        // Authentifier avec Sanctum
        Sanctum::actingAs($user, ['*']);

        // Appeler la route logout (adapter selon votre route)
        $response = $this->postJson(route('logout'));

        $response->assertStatus(200)
            ->assertJson([
                'logout' => true,
            ]);

        // Vérifier que le token a été supprimé
        $this->assertCount(0, $user->tokens);
    }

    /**
     * Test logout returns unauthorized if no user.
     */
    public function test_destroy_returns_unauthorized_if_no_authenticated_user()
    {
        $response = $this->postJson(route('logout'));

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
