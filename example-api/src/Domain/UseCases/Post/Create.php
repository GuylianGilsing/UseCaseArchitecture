<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post;

use App\Domain\Post;
use App\Domain\Repositories\PostRepositoryInterface;
use App\Domain\UseCases\Post\Create\Result;
use App\Domain\UseCases\Post\Create\ResultMessage;
use Framework\API\UseCases\AcceptanceCriteriaInterface;
use Framework\API\UseCases\Errors\ArgumentValidationError;
use Framework\API\UseCases\Errors\UseCaseFailedError;
use Framework\API\UseCases\UseCaseInterface;
use PHPValidation\ValidatorInterface;

/**
 * Represents business logic that creates a post within the application.
 */
final class Create implements UseCaseInterface
{
    public function __construct(
        private readonly ValidatorInterface $argumentsValidator,
        private readonly AcceptanceCriteriaInterface $acceptanceCriteria,
        private readonly PostRepositoryInterface $postRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return Result
     */
    public function invoke(array $args = []): object
    {
        if (!$this->argumentsValidator->isValid($args)) {
            return new Result(
                message: ResultMessage::ARGUMENT_ERROR,
                error: new ArgumentValidationError($this->argumentsValidator->getErrorMessages()),
            );
        }

        $post = new Post(null, $args['title'], $args['content']);

        if (!$this->acceptanceCriteria->complies($post)) {
            return new Result(
                message: ResultMessage::BUSINESS_LOGIC_ERROR,
                error: $this->acceptanceCriteria->getError(),
            );
        }

        $post = $this->postRepository->create($post);

        if ($post === null) {
            return new Result(
                message: ResultMessage::FAILED,
                error: new UseCaseFailedError(['persistence mechanism failed']),
            );
        }

        return new Result(message: ResultMessage::CREATED, post: $post);
    }
}
