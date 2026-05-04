<?php

declare(strict_types=1);

namespace App\Services\Sms;

use App\Services\Sms\Contracts\VerificationSender;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlusofonSmsSender implements VerificationSender
{
    public function sendVerificationCode(string $phone, string $code): bool
    {
        $apiUrl = config('services.plusofon.api_url');
        $apiToken = config('services.plusofon.token');
        $sender = config('services.plusofon.sender');

        if (! $apiUrl || ! $apiToken || ! $sender) {
            Log::info('SMS provider is not configured. Verification code logged locally.', [
                'phone' => $phone,
                'code' => $code,
            ]);

            return true;
        }

        $response = Http::timeout(10)
            ->acceptJson()
            ->asJson()
            ->post($apiUrl, [
                'token' => $apiToken,
                'sender' => $sender,
                'phone' => $phone,
                'message' => "Код подтверждения: {$code}",
            ]);

        return $response->successful();
    }
}
