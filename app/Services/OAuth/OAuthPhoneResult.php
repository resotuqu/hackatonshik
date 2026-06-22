<?php

declare(strict_types=1);

namespace App\Services\OAuth;

enum OAuthPhoneResult
{
    case Verified;
    case NeedsManualEntry;
    case AlreadyVerified;
}
