<?php

declare(strict_types=1);

namespace App\Services\Sms;

use App\Services\Sms\Contracts\VerificationSender;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Flash Call через Plusofon REST API v1: входящий звонок с проговариванием PIN.
 *
 * @see https://restapi.plusofon.ru/api/v1/flash-call/send
 */
class PlusofonFlashCallSender implements VerificationSender
{
    private const SEND_RELATIVE_PATH = '/flash-call/send';

    private const CHECK_RELATIVE_PATH = '/flash-call/check';

    private const CALL_ID_CACHE_TTL_MINUTES = 10;

    /**
     * Инициирует Flash Call на номер с указанным PIN (4–6 цифр). При пустом `$code` генерирует PIN локально.
     */
    public function sendVerificationCode(string $phone, string $code): bool
    {
        $pin = $this->normalizeOrGeneratePin($code);

        if (! $this->isLiveModeEnabled()) {
            Log::info('[Flash Call dev] Провайдер выключен или не настроен. Реальный звонок не выполняется.', [
                'phone' => $phone,
                'pin' => $pin,
            ]);

            return true;
        }

        $baseUrl = rtrim((string) config('services.plusofon_flash_call.base_url'), '/');
        $token = (string) config('services.plusofon_flash_call.token');
        $clientId = (string) config('services.plusofon_flash_call.client_id');
        $url = $baseUrl.self::SEND_RELATIVE_PATH;

        try {
            $response = Http::timeout(10)
                ->withToken($token)
                ->withHeaders([
                    'Client' => $clientId,
                    'Accept' => 'application/json',
                ])
                ->acceptJson()
                ->asJson()
                ->post($url, [
                    'phone' => $phone,
                    'pin' => $pin,
                ]);

            /** @var array<string, mixed>|null $body */
            $body = $response->json();

            $httpOk = $response->successful();
            $businessOk = $httpOk && $this->isFlashCallSendBodySuccessful($body);

            if ($httpOk && $businessOk) {
                Log::info('Plusofon Flash Call: отправка успешна.', [
                    'phone' => $phone,
                    'http_status' => $response->status(),
                    'response' => $body,
                ]);

                $sessionId = $this->extractCallSessionIdentifier($body);
                if ($sessionId !== null && $sessionId !== '') {
                    Cache::put(
                        $this->callIdCacheKey($phone),
                        $sessionId,
                        now()->addMinutes(self::CALL_ID_CACHE_TTL_MINUTES)
                    );
                }

                return true;
            }

            if ($httpOk) {
                Log::warning('Plusofon Flash Call: HTTP 200, но в теле ответа ошибка (success=false или code>=400).', [
                    'phone' => $phone,
                    'http_status' => $response->status(),
                    'response' => $body,
                ]);

                return false;
            }

            Log::warning('Plusofon Flash Call: отправка завершилась ошибкой.', [
                'phone' => $phone,
                'http_status' => $response->status(),
                'response' => $body,
            ]);

            return false;
        } catch (Throwable $e) {
            Log::error('Plusofon Flash Call: исключение при отправке.', [
                'phone' => $phone,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Проверка PIN через API провайдера (POST /flash-call/check).
     *
     * В dev-режиме (провайдер выключен) возвращает `false`, т.к. удалённая проверка недоступна.
     */
    public function checkPin(string $phone, string $pin): bool
    {
        if (! $this->isLiveModeEnabled()) {
            Log::info('[Flash Call dev] checkPin: API не вызывается (режим разработки).', [
                'phone' => $phone,
            ]);

            return false;
        }

        $baseUrl = rtrim((string) config('services.plusofon_flash_call.base_url'), '/');
        $token = (string) config('services.plusofon_flash_call.token');
        $clientId = (string) config('services.plusofon_flash_call.client_id');
        $url = $baseUrl.self::CHECK_RELATIVE_PATH;

        try {
            $response = Http::timeout(10)
                ->withToken($token)
                ->withHeaders([
                    'Client' => $clientId,
                    'Accept' => 'application/json',
                ])
                ->acceptJson()
                ->asJson()
                ->post($url, [
                    'phone' => $phone,
                    'pin' => $pin,
                ]);

            /** @var array<string, mixed>|null $body */
            $body = $response->json();

            if (! $response->successful()) {
                Log::warning('Plusofon Flash Call: checkPin HTTP-ошибка.', [
                    'phone' => $phone,
                    'http_status' => $response->status(),
                    'response' => $body,
                ]);

                return false;
            }

            if (! $this->isFlashCallSendBodySuccessful($body)) {
                Log::warning('Plusofon Flash Call: checkPin HTTP 200, но в теле ответа ошибка.', [
                    'phone' => $phone,
                    'response' => $body,
                ]);

                return false;
            }

            $ok = $this->interpretCheckPinResponse($body);

            Log::info('Plusofon Flash Call: checkPin ответ обработан.', [
                'phone' => $phone,
                'interpreted_ok' => $ok,
                'response' => $body,
            ]);

            return $ok;
        } catch (Throwable $e) {
            Log::error('Plusofon Flash Call: исключение при checkPin.', [
                'phone' => $phone,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public static function callIdCacheKeyForPhone(string $phone): string
    {
        return "flash-call:call-id:{$phone}";
    }

    /**
     * Plusofon может вернуть HTTP 200 с JSON вида `success: false`, `code: 500`, `message: "..."`.
     * В таком случае отправку считаем неуспешной.
     *
     * @param  array<string, mixed>|null  $body
     */
    private function isFlashCallSendBodySuccessful(?array $body): bool
    {
        if ($body === null) {
            return true;
        }

        if (array_key_exists('success', $body) && $body['success'] === false) {
            return false;
        }

        if (array_key_exists('success', $body) && $body['success'] === true) {
            return true;
        }

        $code = $body['code'] ?? null;
        if (is_numeric($code) && (int) $code >= 400) {
            return false;
        }

        $data = $body['data'] ?? null;
        if (is_array($data)) {
            if (array_key_exists('success', $data) && $data['success'] === false) {
                return false;
            }

            if (array_key_exists('success', $data) && $data['success'] === true) {
                return true;
            }

            $dataCode = $data['code'] ?? null;
            if (is_numeric($dataCode) && (int) $dataCode >= 400) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function extractCallSessionIdentifier(?array $payload): ?string
    {
        if ($payload === null) {
            return null;
        }

        foreach (['call_id', 'session', 'session_id', 'id', 'callId', 'uuid'] as $key) {
            if (isset($payload[$key]) && is_scalar($payload[$key])) {
                $value = (string) $payload[$key];

                return $value !== '' ? $value : null;
            }
        }

        $data = $payload['data'] ?? null;
        if (is_array($data)) {
            foreach (['call_id', 'session', 'session_id', 'id', 'callId', 'uuid'] as $key) {
                if (isset($data[$key]) && is_scalar($data[$key])) {
                    $value = (string) $data[$key];

                    return $value !== '' ? $value : null;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $body
     */
    private function interpretCheckPinResponse(?array $body): bool
    {
        if ($body === null) {
            return false;
        }

        if (isset($body['error']) || isset($body['errors'])) {
            return false;
        }

        foreach (['success', 'result', 'valid', 'verified', 'match', 'ok'] as $key) {
            if (array_key_exists($key, $body) && is_bool($body[$key])) {
                return $body[$key];
            }
        }

        $data = $body['data'] ?? null;
        if (is_array($data)) {
            foreach (['success', 'result', 'valid', 'verified', 'match', 'ok'] as $key) {
                if (array_key_exists($key, $data) && is_bool($data[$key])) {
                    return $data[$key];
                }
            }
        }

        return false;
    }

    private function callIdCacheKey(string $phone): string
    {
        return self::callIdCacheKeyForPhone($phone);
    }

    private function normalizeOrGeneratePin(string $code): string
    {
        $trimmed = trim($code);
        if ($trimmed === '') {
            return (string) random_int(1000, 9999);
        }

        return $trimmed;
    }

    private function isLiveModeEnabled(): bool
    {
        if (! (bool) config('services.plusofon_flash_call.enabled', false)) {
            return false;
        }

        $token = config('services.plusofon_flash_call.token');
        $clientId = config('services.plusofon_flash_call.client_id');

        return filled($token) && filled($clientId);
    }
}
