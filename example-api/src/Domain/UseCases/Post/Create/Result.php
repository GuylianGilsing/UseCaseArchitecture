<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post\Create;

use App\Domain\Post;
use Framework\API\UseCases\UseCaseErrorInterface;

/**
 * Represents the result that the create post use case produces.
 */
final class Result
{
    public function __construct(
        public readonly ResultMessage $message,
        public readonly ?Post $post = null,
        public readonly ?UseCaseErrorInterface $error = null,
    ) {
    }
}
