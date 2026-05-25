<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Contoh pengujian dasar fitur.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Rute root harus mengalihkan pengguna ke halaman login (302)
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // Rute login harus mengembalikan status sukses (200)
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
    }
}
