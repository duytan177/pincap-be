<?php declare(strict_types=1);

namespace App\Enums\Album_Media;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class AlbumRole extends Enum
{
    const OWNER = 0;
    const EDIT = 1;
}
