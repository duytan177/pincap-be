<?php declare(strict_types=1);

namespace App\Enums\Album_Media;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Privacy extends Enum
{
    const PRIVATE = "0";
    const PUBLIC = "1";
}
