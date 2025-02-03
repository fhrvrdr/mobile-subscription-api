<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_register_successful_response()
    {
        $request = [
            'uid' => '123456',
            'app_id' => 'asdasdasd123',
            'operating_system' => 'ios',
            'language' => 'en',
        ];

        $response = $this->post('/api/register', $request);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
                'expires_at',
            ],
        ]);
        $response->assertJson([
            'success' => true,
            'message' => 'Device registered successfully',
        ]);
    }

    public function test_the_register_failed_response()
    {
        $request = [
            'uid' => '123456',
            'app_id' => 'asdasdasd123',
            'operating_system' => 'windows',
            'language' => 'en',
        ];

        $response = $this->post('/api/register', $request);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [],
        ]);
        $response->assertJson([
            'success' => false,
            'message' => 'Validation Error',
        ]);
    }
}
