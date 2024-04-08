<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post;

use App\Domain\Repositories\PostRepositoryInterface;
use App\Domain\UseCases\Post\GetByID\Result;
use App\Domain\UseCases\Post\GetByID\ResultMessage;
use Framework\API\UseCases\Errors\ResourceNotFoundError;
use Framework\API\UseCases\UseCaseInterface;
use PHPValidation\ValidatorInterface;

final class GetByID implements UseCaseInterface
{
    public function __construct(
        private readonly ValidatorInterface $argumentsValidator,
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
                message: ResultMessage::NOT_FOUND,
                error: new ResourceNotFoundError(['Post does not exist']),
            );
        }

        $post = $this->postRepository->getByID($args['postID']);

        if ($post === null) {
            return new Result(
                message: ResultMessage::NOT_FOUND,
                error: new ResourceNotFoundError(['Post does not exist']),
            );
        }

        return new Result(
            message: ResultMessage::FOUND,
            post: $post,
        );
    }
}
