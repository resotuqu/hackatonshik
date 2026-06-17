<?php

declare(strict_types=1);

namespace App\Support;

final class FlashToast
{
    public const POSITION = 'toast-top toast-end';

    public const TIMEOUT = 4000;

    /**
     * @return list<array{type: string, title: string, css: string, icon: string, timeout: int, position: string}>
     */
    public static function fromSession(): array
    {
        $toasts = [];

        foreach (self::flashMap() as $sessionKey => $config) {
            $message = session($sessionKey);

            if (! is_string($message) || $message === '') {
                continue;
            }

            $toasts[] = [
                'type' => $config['type'],
                'title' => $message,
                'description' => null,
                'css' => $config['css'],
                'icon' => '',
                'timeout' => self::TIMEOUT,
                'position' => self::POSITION,
                'noProgress' => false,
                'progressClass' => null,
            ];
        }

        return $toasts;
    }

    /**
     * @return array<string, array{type: string, css: string}>
     */
    private static function flashMap(): array
    {
        return [
            'success' => ['type' => 'success', 'css' => 'alert-success'],
            'error' => ['type' => 'error', 'css' => 'alert-error'],
            'warning' => ['type' => 'warning', 'css' => 'alert-warning'],
            'info' => ['type' => 'info', 'css' => 'alert-info'],
        ];
    }
}
