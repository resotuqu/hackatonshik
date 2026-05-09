<?php

namespace App\Logging;

use Illuminate\Support\Facades\Http;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class TelegramLogger
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        return new Logger('telegram', [
            new class($config['level'], $config['token'], $config['chat_id']) extends AbstractProcessingHandler {
                public function __construct(
                    $level,
                    protected string $token,
                    protected string $chatId
                ) {
                    parent::__construct($level);
                }

                protected function write(LogRecord $record): void
                {
                    if (!$this->token || !$this->chatId) {
                        return;
                    }

                    $message = sprintf(
                        "<b>[%s] %s</b>\n\n%s\n\n<pre>%s</pre>",
                        $record->level->getName(),
                        config('app.name'),
                        $record->message,
                        json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    );

                    Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                        'chat_id' => $this->chatId,
                        'text' => substr($message, 0, 4096),
                        'parse_mode' => 'HTML',
                    ]);
                }
            },
        ]);
    }
}
