<?php

namespace App\Enums\Album_Media;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class MediaType extends Enum
{
    const IMAGE = "0";
    const VIDEO = "1";
}
