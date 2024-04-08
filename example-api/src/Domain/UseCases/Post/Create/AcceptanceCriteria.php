<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post\Create;

use App\Domain\Post;
use Framework\API\UseCases\AcceptanceCriteriaInterface;
use Framework\API\UseCases\UseCaseErrorInterface;

/**
 * Represents a set of checks that the create post use case uses to validate a potential post.
 */
final class AcceptanceCriteria implements AcceptanceCriteriaInterface
{
    private ?UseCaseErrorInterface $error = null;

    public function __construct(
        private readonly AcceptanceCriteriaInterface $postCannotBeDuplicate,
    ) {
    }

    /**
     * @param Post $input
     */
    public function complies(object $input): bool
    {
        $this->error = null;

        if (!$this->postCannotBeDuplicate->complies($input)) {
            $this->error = $this->postCannotBeDuplicate->getError();

            return false;
        }

        return true;
    }

    /**
     * Retrieves any error that the acceptance criterion generates.
     */
    public function getError(): ?UseCaseErrorInterface
    {
        return $this->error;
    }
}
