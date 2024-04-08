<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post\Create\Errors;

use App\Domain\Post;
use Framework\API\UseCases\Errors\DuplicateResourceError;
use Framework\API\UseCases\UseCaseErrorInterface;

/**
 * Represents a use case duplicate post error.
 */
final class DuplicatePostError implements UseCaseErrorInterface
{
    private UseCaseErrorInterface $error;

    public function __construct(Post $post)
    {
        $errorMessage = sprintf('Post with title "%s" already exists', $post->getTitle());

        $this->error = new DuplicateResourceError([$errorMessage]);
    }

    /**
     * @return array<string, mixed>|array<mixed> An array that holds all error messages in an applicable format.
     */
    public function getFormatted(): array
    {
        return $this->error->getFormatted();
    }
}
