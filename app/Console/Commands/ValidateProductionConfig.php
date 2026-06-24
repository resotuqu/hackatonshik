<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValidateProductionConfig extends Command
{
    protected $signature = 'app:validate-production-config';

    protected $description = 'Validate required production environment configuration';

    public function handle(): int
    {
        if (! app()->isProduction()) {
            $this->warn('Skipped: APP_ENV is not production.');

            return self::SUCCESS;
        }

        $errors = [];

        $mailMailer = (string) config('mail.default');
        if (in_array($mailMailer, ['log', 'array'], true)) {
            $errors[] = 'MAIL_MAILER must not be log/array in production.';
        }

        if (config('database.default') === 'sqlite') {
            $errors[] = 'DB_CONNECTION must not be sqlite in production.';
        }

        if (blank(config('app.trusted_proxies'))) {
            $errors[] = 'TRUSTED_PROXIES must be set behind a reverse proxy.';
        }

        if (config('broadcasting.default') === 'reverb') {
            $reverb = config('reverb.apps.apps.0', []);

            foreach (['id', 'key', 'secret'] as $key) {
                if (blank($reverb[$key] ?? null)) {
                    $errors[] = "Reverb app {$key} is required when BROADCAST_CONNECTION=reverb.";
                }
            }

            if (blank(config('reverb.servers.reverb.host'))) {
                $errors[] = 'REVERB_HOST must be set when BROADCAST_CONNECTION=reverb.';
            }

            $viteReverb = config('app.vite_reverb', []);
            foreach (['app_key', 'host', 'port', 'scheme'] as $key) {
                if (blank($viteReverb[$key] ?? null)) {
                    $errors[] = 'VITE_REVERB_'.strtoupper($key).' must be set before frontend build.';
                }
            }
        }

        if ($errors !== []) {
            foreach ($errors as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $this->info('Production configuration looks valid.');

        return self::SUCCESS;
    }
}
