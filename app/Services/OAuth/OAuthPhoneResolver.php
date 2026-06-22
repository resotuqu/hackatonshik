<?php

declare(strict_types=1);

namespace App\Services\OAuth;

use App\Models\User;

class OAuthPhoneResolver
{
    /**
     * @param  array<string, mixed>|object  $rawPayload
     */
    public function extractPhone(string $provider, array|object $rawPayload): ?string
    {
        $payload = is_array($rawPayload) ? $rawPayload : (array) $rawPayload;

        return match ($provider) {
            'yandex' => $this->normalize($payload['default_phone']['number'] ?? null),
            'vk' => $this->normalize($payload['phone'] ?? null),
            default => null,
        };
    }

    public function normalize(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '8') && strlen($digits) === 11) {
            $digits = '7'.substr($digits, 1);
        }

        if (str_starts_with($digits, '7') && strlen($digits) === 11) {
            return '+'.$digits;
        }

        if (strlen($digits) === 10) {
            return '+7'.$digits;
        }

        if (str_starts_with($digits, '7') && strlen($digits) > 11) {
            return '+'.substr($digits, 0, 11);
        }

        return null;
    }

    public function applyToUser(User $user, ?string $phone): OAuthPhoneResult
    {
        if ($user->phone_verified_at !== null) {
            return OAuthPhoneResult::AlreadyVerified;
        }

        $this->clearUnverifiedOAuthPhone($user);

        $normalized = $this->normalize($phone);

        if ($normalized === null) {
            return OAuthPhoneResult::NeedsManualEntry;
        }

        if (! $this->isPhoneUnique($normalized, $user->id)) {
            return OAuthPhoneResult::NeedsManualEntry;
        }

        $user->forceFill([
            'phone' => $normalized,
            'phone_verified_at' => now(),
        ])->save();

        return OAuthPhoneResult::Verified;
    }

    public function clearUnverifiedOAuthPhone(User $user): void
    {
        if ($user->phone_verified_at !== null || $user->oauth_provider === null) {
            return;
        }

        if (blank($user->phone)) {
            return;
        }

        $user->forceFill(['phone' => null])->save();
    }

    private function isPhoneUnique(string $phone, int $userId): bool
    {
        if ($phone === '') {
            return false;
        }

        return ! User::query()
            ->where('phone', $phone)
            ->whereKeyNot($userId)
            ->exists();
    }
}
