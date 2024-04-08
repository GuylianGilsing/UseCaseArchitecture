<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post\Create;

/**
 * Represents all possible states that a generated create post result object can be in.
 */
enum ResultMessage: string
{
    case CREATED = 'post-created';
    case ARGUMENT_ERROR = 'post-created-argument-error';
    case BUSINESS_LOGIC_ERROR = 'post-created-business-logic-error';
    case FAILED = 'post-created-failed';
}
