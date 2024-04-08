<?php

declare(strict_types=1);

use App\Domain\Post;
use App\Domain\UseCases\Post\Create\AcceptanceCriteria;
use Framework\API\UseCases\AcceptanceCriteriaInterface;
use Framework\API\UseCases\UseCaseErrorInterface;
use Mockery\MockInterface;

describe('Happy flow', function () {
    it('should comply', function () {
        // Arrange
        $postCannotBeDuplicateAcceptanceCriteria = Mockery::mock(AcceptanceCriteriaInterface::class);

        if ($postCannotBeDuplicateAcceptanceCriteria instanceof MockInterface) {
            $postCannotBeDuplicateAcceptanceCriteria->expects('complies')->andReturn(true);
        }

        $post = new Post(null, 'My title', 'My content');

        $acceptanceCriteria = new AcceptanceCriteria(
            postCannotBeDuplicate: $postCannotBeDuplicateAcceptanceCriteria,
        );

        // Act
        $complies = $acceptanceCriteria->complies($post);

        // Assert
        expect($complies)->toBeTrue();
        expect($acceptanceCriteria->getError())->toBeNull();
    });
});

describe('Unhappy flow', function () {
    it('should not comply when a duplicate post has been found', function () {
        // Arrange
        $postCannotBeDuplicateAcceptanceCriteria = Mockery::mock(AcceptanceCriteriaInterface::class);

        if ($postCannotBeDuplicateAcceptanceCriteria instanceof MockInterface) {
            $postCannotBeDuplicateAcceptanceCriteria->expects('complies')->andReturn(false);
            $postCannotBeDuplicateAcceptanceCriteria->expects('getError')->andReturn(
                Mockery::mock(UseCaseErrorInterface::class)
            );
        }

        $post = new Post(null, 'My title', 'My content');
        $acceptanceCriteria = new AcceptanceCriteria(
            postCannotBeDuplicate: $postCannotBeDuplicateAcceptanceCriteria,
        );

        // Act
        $complies = $acceptanceCriteria->complies($post);

        // Assert
        expect($complies)->toBeFalse();
        expect($acceptanceCriteria->getError())->not()->toBeNull();
    });
});
