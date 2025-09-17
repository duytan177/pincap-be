<?php declare(strict_types=1);

namespace App\Enums\Album_Media;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class InvitationStatus extends Enum
{
    const INVITED  = 'INVITED';
    const ACCEPTED = 'ACCEPTED';
    const REJECTED = 'REJECTED';
}
