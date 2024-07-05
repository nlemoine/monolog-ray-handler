<?php

namespace n5s\MonologRayHandler;

use DateTimeInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Monolog\Level;
use Monolog\Logger;

if (Logger::API >= 3) {
    trait CompatibilityProcessingHandlerTrait
    {
        public function __construct(int|string|Level $level = Level::Debug, bool $bubble = true)
        {
            parent::__construct($level, $bubble);
        }

        private function getRecord(LogRecord $record): array
        {
            return $record->toArray();
        }

        private static function getColorFromLevel(int $level): string
        {
            $level = Level::from($level);

            switch ($level) {
                case Level::Debug:
                    return 'gray';
                case Level::Warning:
                    return 'orange';
                case Level::Error:
                    return 'purple';
                case Level::Critical:
                case Level::Alert:
                case Level::Emergency:
                    return 'red';
                case Level::Info:
                case Level::Notice:
                default:
                    return 'blue';
            }
        }
    }
} else {
    trait CompatibilityProcessingHandlerTrait
    {
        public function __construct($level = Level::Debug, bool $bubble = true)
        {
            parent::__construct($level, $bubble);
        }

        private function getRecord(array $record): array
        {
            return $record;
        }

        private static function getColorFromLevel(int $level): string
        {
            switch ($level) {
                case Logger::DEBUG:
                    return 'gray';
                case Logger::WARNING:
                    return 'orange';
                case Logger::ERROR:
                    return 'purple';
                case Logger::CRITICAL:
                case Logger::ALERT:
                case Logger::EMERGENCY:
                    return 'red';
                case Logger::INFO:
                case Logger::NOTICE:
                default:
                    return 'blue';
            }
        }
    }
}

class RayHandler extends AbstractProcessingHandler
{
    use CompatibilityProcessingHandlerTrait;

    /**
     * @param array<string, mixed>|LogRecord $record
     *
     * @return void
     */
    protected function write($record): void
    {
        if (!function_exists('ray')) {
            return;
        }

        $record = array_map(static function ($item): mixed {
            return $item instanceof DateTimeInterface ? $item->format('Y-m-d H:i:s') : $item;
        }, $this->getRecord($record));

        unset($record['formatted']);

        ray()
            ->table($record, $record['channel'] ?? 'monolog')
            ->color(self::getColorFromLevel($record['level']));
    }
}
