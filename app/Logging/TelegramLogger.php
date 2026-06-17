<?php

namespace App\Logging;

use Illuminate\Support\Facades\Http;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class TelegramLogger
{
    /**
     * @var list<string>
     */
    private const SENSITIVE_CONTEXT_KEYS = [
        'password',
        'token',
        'secret',
        'authorization',
        'cookie',
        'api_key',
    ];

    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        return new Logger('telegram', [
            new class($config['level'], $config['token'], $config['chat_id']) extends AbstractProcessingHandler
            {
                public function __construct(
                    $level,
                    protected string $token,
                    protected string $chatId
                ) {
                    parent::__construct($level);
                }

                protected function write(LogRecord $record): void
                {
                    if (! $this->token || ! $this->chatId) {
                        return;
                    }

                    $context = self::redactContext($record->context);

                    $message = sprintf(
                        "<b>[%s] %s</b>\n\n%s\n\n<pre>%s</pre>",
                        $record->level->getName(),
                        config('app.name'),
                        $record->message,
                        json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    );

                    Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                        'chat_id' => $this->chatId,
                        'text' => substr($message, 0, 4096),
                        'parse_mode' => 'HTML',
                    ]);
                }

                /**
                 * @param  array<string, mixed>  $context
                 * @return array<string, mixed>
                 */
                private static function redactContext(array $context): array
                {
                    $redacted = [];

                    foreach ($context as $key => $value) {
                        if (is_string($key) && self::isSensitiveKey($key)) {
                            $redacted[$key] = '[REDACTED]';

                            continue;
                        }

                        if (is_array($value)) {
                            $redacted[$key] = self::redactContext($value);

                            continue;
                        }

                        $redacted[$key] = $value;
                    }

                    return $redacted;
                }

                private static function isSensitiveKey(string $key): bool
                {
                    $normalized = strtolower($key);

                    foreach (TelegramLogger::SENSITIVE_CONTEXT_KEYS as $sensitiveKey) {
                        if (str_contains($normalized, $sensitiveKey)) {
                            return true;
                        }
                    }

                    return false;
                }
            },
        ]);
    }
}
