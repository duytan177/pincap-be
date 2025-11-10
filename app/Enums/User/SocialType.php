<?php

namespace App\Enums\User;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SocialType extends Enum
{
    const INSTAGRAM = "INSTAGRAM";
    const FACEBOOK = "FACEBOOK";
    const PINTEREST = "PINTEREST";
}
