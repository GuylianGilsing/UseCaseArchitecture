<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post\GetByID;

enum ResultMessage: string
{
    case FOUND = 'post-get-by-id-found';
    case NOT_FOUND = 'post-get-by-id-not-found';
    case FAILED = 'post-get-by-id-failed';
}
