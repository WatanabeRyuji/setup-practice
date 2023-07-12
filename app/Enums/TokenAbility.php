<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<string>
 */
class TokenAbility extends Enum
{
    public const AccessApi = 'access-api';

    public const RefreshToken = 'refresh-token';
}
