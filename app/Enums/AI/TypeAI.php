<?php declare(strict_types=1);

namespace App\Enums\AI;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TypeAI extends Enum
{
    const GPT = 0;
    const IMAGE = 1;
}
