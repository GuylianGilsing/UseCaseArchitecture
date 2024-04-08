<?php

declare(strict_types=1);

namespace App\Domain\UseCases\Post;

use App\Domain\Repositories\PostRepositoryInterface;
use App\Domain\UseCases\Post\GetAll\Result;
use App\Domain\UseCases\Post\GetAll\ResultMessage;
use Framework\API\UseCases\Errors\UseCaseFailedError;
use Framework\API\UseCases\UseCaseInterface;
use Throwable;

final class GetAll implements UseCaseInterface
{
    public function __construct(
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
        try {
            $posts = $this->postRepository->getAll();
        } catch (Throwable $e) {
            return new Result(
                message: ResultMessage::FAILED,
                error: new UseCaseFailedError([$e->getMessage()]),
            );
        }

        return new Result(
            message: ResultMessage::FOUND,
            posts: $posts,
        );
    }
}
