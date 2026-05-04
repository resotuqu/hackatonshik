<?php

declare(strict_types=1);

namespace App\Services\Sms\Contracts;

/**
 * Отправка кода подтверждения на телефон (SMS, Flash Call и т.д.).
 */
interface VerificationSender
{
    /**
     * @param  string  $phone  Номер в формате E.164, например +7xxxxxxxxxx
     * @param  string  $code  Код для доставки; пустая строка — реализация может сгенерировать PIN сама
     */
    public function sendVerificationCode(string $phone, string $code): bool;
}
