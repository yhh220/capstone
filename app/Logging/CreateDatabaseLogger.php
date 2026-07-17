<?php

namespace App\Logging;

use Monolog\Logger;

/**
 * Custom channel factory (`'driver' => 'custom', 'via' => ...`) that builds a
 * Monolog logger writing to the `app_logs` table, enriched with trace context +
 * breadcrumbs.
 */
class CreateDatabaseLogger
{
    public function __invoke(array $config): Logger
    {
        $level = Logger::toMonologLevel($config['level'] ?? env('LOG_DB_LEVEL', 'info'));

        $logger = new Logger('database', [new DatabaseLogHandler($level)]);
        $logger->pushProcessor(new ObservabilityProcessor);

        return $logger;
    }
}
