<?php declare(strict_types=1);

namespace App\Enums\User;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class UserStatus extends Enum
{
    const BLOCK = 0;
    const FOLLOWING = 1;

}
