<?php

namespace App\Enums;

enum AccreditationType : string
{
    use EnumToSelectArray;

    case NOT_APPLICABLE = 'not applicable';
    case APPLIED = 'applied';
    case PENDING = 'pending';
    case PROCESSED = 'processed';
    case IN_REVIEW = 'in review';
    case CONDITIONALLY_ACCREDITED = 'conditionally accredited';
    case ACCREDITED = 'accredited';
    case REJECTED = 'rejected';
    case SUSPENDED = 'suspended';
    case REVOKED = 'revoked';
    case EXPIRED = 'expired';
    case WITHDRAWN = 'withdrawn';
    case DEFERRED = 'deferred';
    case APPEALED = 'appealed';
    case PROBATIONARY = 'probationary';

    public function label(): string
    {
        return ucwords(strtolower(str_replace('_', ' ', $this->name)));
    }
}
