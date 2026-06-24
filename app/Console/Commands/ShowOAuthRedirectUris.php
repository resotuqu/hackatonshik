<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\OAuthRedirectUris;
use Illuminate\Console\Command;

class ShowOAuthRedirectUris extends Command
{
    protected $signature = 'oauth:redirect-uris';

    protected $description = 'Show OAuth redirect URIs to register in Yandex and VK consoles';

    public function handle(): int
    {
        $this->line('APP_URL: '.config('app.url'));
        $this->newLine();

        $this->components->info('Yandex (oauth.yandex.ru) — register all URLs:');
        foreach (OAuthRedirectUris::yandexConsoleUris() as $uri) {
            $this->line("  {$uri}");
        }

        $this->newLine();
        $this->components->info('VK ID — register callback URL:');
        $this->line('  '.OAuthRedirectUris::vkCallback());

        return self::SUCCESS;
    }
}
