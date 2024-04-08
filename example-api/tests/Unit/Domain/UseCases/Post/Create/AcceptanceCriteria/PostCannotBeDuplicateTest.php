<?php

declare(strict_types=1);

use App\Domain\Post;
use App\Domain\Repositories\PostRepositoryInterface;
use App\Domain\UseCases\Post\Create\AcceptanceCriteria\PostCannotBeDuplicate;
use Mockery\MockInterface;

describe('Happy flow', function () {
    it('should comply when no duplicate post could be found', function () {
        // Arrange
        $postRepository = Mockery::mock(PostRepositoryInterface::class);

        if ($postRepository instanceof MockInterface) {
            $postRepository->expects('getDuplicate')->andReturn(null);
        }

        $post = new Post(null, 'My title', 'My content');
        $acceptanceCriteria = new PostCannotBeDuplicate(postRepository: $postRepository);

        // Act
        $complies = $acceptanceCriteria->complies($post);

        // Assert
        expect($complies)->toBeTrue();
        expect($acceptanceCriteria->getError())->toBeNull();
    });

    it('should not expose an old error when a post complies', function () {
        // Arrange
        $post1 = new Post(null, 'My title', 'My content');
        $post2 = new Post(null, 'Another title', 'Some content');

        $postRepository = Mockery::mock(PostRepositoryInterface::class);

        if ($postRepository instanceof MockInterface) {
            $postRepository->expects('getDuplicate')->with($post1)->andReturn($post1);
            $postRepository->expects('getDuplicate')->with($post2)->andReturn(null);
        }

        $acceptanceCriteria = new PostCannotBeDuplicate(postRepository: $postRepository);

        // Act
        $complies = $acceptanceCriteria->complies($post1);

        // Assert
        expect($complies)->toBeFalse();
        expect($acceptanceCriteria->getError())->not()->toBeNull();

        // Act
        $complies = $acceptanceCriteria->complies($post2);

        // Assert
        expect($complies)->toBeTrue();
        expect($acceptanceCriteria->getError())->toBeNull();
    });
});

describe('Unhappy flow', function () {
    it('should not comply when a duplicate post has been found', function () {
        // Arrange
        $post = new Post(null, 'My title', 'My content');
        $postRepository = Mockery::mock(PostRepositoryInterface::class);

        if ($postRepository instanceof MockInterface) {
            $postRepository->expects('getDuplicate')->andReturn($post);
        }

        $acceptanceCriteria = new PostCannotBeDuplicate(postRepository: $postRepository);

        // Act
        $complies = $acceptanceCriteria->complies($post);

        // Assert
        expect($complies)->toBeFalse();
        expect($acceptanceCriteria->getError())->not()->toBeNull();
    });
});
