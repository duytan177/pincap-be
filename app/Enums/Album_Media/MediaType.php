<?php

namespace App\Enums\Album_Media;

use BenSampo\Enum\Enum;

/**
 * @method static static IMAGE()
 * @method static static VIDEO()
 */

final class MediaType extends Enum
{
    const IMAGE = "0";
    const VIDEO = "1";
}
