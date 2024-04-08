<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post\GetAll;

enum ResultMessage: string
{
    case FOUND = 'post-get-all-found';
    case FAILED = 'post-get-all-failed';
}
