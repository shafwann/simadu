<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase; // Reset database setiap kali test jalan

    /** @test */
    public function it_can_list_users()
    {
        // 1. Siapkan data (Arrange)
        User::factory()->count(3)->create();

        // 2. Tembak Endpoint (Act)
        $response = $this->getJson('/api/users');

        // 3. Cek Hasil (Assert)
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'email'] // Pastikan struktur JSON benar
                     ],
                     'links', // Cek pagination
                     'meta'
                 ]);
    }

    /** @test */
    public function it_validates_create_user_input()
    {
        // Coba kirim data kosong
        $response = $this->postJson('/api/users', []);

        // Harusnya error 422 (Unprocessable Entity)
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email', 'password']);
    }
}
