<?php declare(strict_types=1);

namespace App\Enums\Album_Media;

use BenSampo\Enum\Enum;

/**

 */
final class StateReport extends Enum
{
    const UNPROCESSED = "0";
    const PROCESSING = "1";
    const PROCESSED = "2";
}
