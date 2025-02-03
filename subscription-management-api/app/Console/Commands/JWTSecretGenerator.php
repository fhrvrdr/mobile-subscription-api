<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class JWTSecretGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:generate-secret';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will generate a new secret key for JWT.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $secret = base64_encode(random_bytes(32));
        $envPath = base_path('.env');

        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);

            if (str_contains($envContent, 'JWT_SECRET=')) {
                $replace = $this->ask('JWT_SECRET already exists in .env file. Do you want to replace it? (yes/no)', 'no');
                if ($replace === 'yes') {
                    $envContent = preg_replace('/JWT_SECRET=(.*)/', "JWT_SECRET=\"$secret\"", $envContent);
                } else {
                    echo 'JWT_SECRET was not replaced.'.PHP_EOL;

                    return;
                }
            } else {
                $envContent .= "\nJWT_SECRET=\"$secret\"\n";
            }

            echo 'JWT_SECRET generated successfully.'.PHP_EOL;
            file_put_contents($envPath, $envContent);
        }
    }
}
