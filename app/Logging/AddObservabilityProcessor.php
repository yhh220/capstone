<?php

namespace App\Logging;

use Illuminate\Log\Logger;

/**
 * Channel `tap` that pushes the ObservabilityProcessor onto a file channel (the
 * structured JSON log), so file logs also carry trace metadata + breadcrumbs.
 */
class AddObservabilityProcessor
{
    public function __invoke(Logger $logger): void
    {
        $logger->getLogger()->pushProcessor(new ObservabilityProcessor());
    }
}
