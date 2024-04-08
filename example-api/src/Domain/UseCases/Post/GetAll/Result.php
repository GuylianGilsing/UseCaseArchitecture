<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post\GetAll;

use App\Domain\Post;
use Framework\API\UseCases\UseCaseErrorInterface;

final class Result
{
    /**
     * @param array<Post> $posts An indexed array of Post objects.
     */
    public function __construct(
        public readonly ResultMessage $message,
        public readonly array $posts = [],
        public readonly ?UseCaseErrorInterface $error = null,
    ) {
    }
}
