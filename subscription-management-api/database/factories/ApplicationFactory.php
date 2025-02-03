<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'app_id' => 'app_345',
            'provider_credentials' => json_encode($this->providerCredentials()),
            'callback_url' => 'mock-api/callback',
        ];
    }

    public function providerCredentials(): array
    {
        return [
            'google' => [
                'username' => 'admin',
                'password' => 'password',
            ],
            'ios' => [
                'username' => 'admin',
                'password' => 'password',
            ],
        ];
    }
}
