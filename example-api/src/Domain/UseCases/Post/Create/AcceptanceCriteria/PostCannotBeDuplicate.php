<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post\Create\AcceptanceCriteria;

use App\Domain\Post;
use App\Domain\Repositories\PostRepositoryInterface;
use App\Domain\UseCases\Post\Create\Errors\DuplicatePostError;
use Framework\API\UseCases\AcceptanceCriteriaInterface;
use Framework\API\UseCases\UseCaseErrorInterface;

/**
 * Represents a use case check that makes sure that a potential post does not become a duplicate within the application.
 */
final class PostCannotBeDuplicate implements AcceptanceCriteriaInterface
{
    private bool $failed = false;
    private ?Post $post = null;

    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
    ) {
    }

    /**
     * @param Post $input
     */
    public function complies(object $input): bool
    {
        $this->failed = false;
        $this->post = null;

        $duplicatePost = $this->postRepository->getDuplicate($input);

        if ($duplicatePost !== null) {
            $this->failed = true;
            $this->post = $input;

            return false;
        }

        return true;
    }

    /**
     * Retrieves any error that the acceptance criterion generates.
     */
    public function getError(): ?UseCaseErrorInterface
    {
        return $this->failed ? new DuplicatePostError($this->post) : null;
    }
}
