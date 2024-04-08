<?php

declare(strict_types=1);

use App\Domain\Post;
use App\Domain\Repositories\PostRepositoryInterface;
use App\Domain\UseCases\Post\Create;
use App\Domain\UseCases\Post\Create\ResultMessage;
use Framework\API\UseCases\AcceptanceCriteriaInterface;
use Framework\API\UseCases\UseCaseErrorInterface;
use Mockery\MockInterface;
use PHPValidation\ValidatorInterface;

describe('Happy flow', function () {
    it('should be able to create a post', function () {
        // Arrange
        $title = 'My title';
        $content = 'My content';
        $arguments = [
            'title' => $title,
            'content' => $content,
        ];

        $argumentsValidator = Mockery::mock(ValidatorInterface::class);

        if ($argumentsValidator instanceof MockInterface) {
            $argumentsValidator->expects('isValid')->andReturn(true);
        }

        $acceptanceCriteria = Mockery::mock(AcceptanceCriteriaInterface::class);

        if ($acceptanceCriteria instanceof MockInterface) {
            $acceptanceCriteria->expects('complies')->andReturn(true);
        }

        $postRepository = Mockery::mock(PostRepositoryInterface::class);

        if ($postRepository instanceof MockInterface) {
            $postRepository->expects('create')->andReturn(new Post(1, $title, $content));
        }

        $useCase = new Create($argumentsValidator, $acceptanceCriteria, $postRepository);

        // Act
        $response = $useCase->invoke($arguments);

        // Assert
        expect($response->message)->toBe(ResultMessage::CREATED);
        expect($response->error)->toBeNull();

        expect($response->post)->not()->toBeNull();
        expect($response->post->getID())->not()->toBeNull();
        expect($response->post->getTitle())->toBe($title);
        expect($response->post->getContent())->toBe($content);
    });
});

describe('Unhappy flow', function () {
    it('should produce an error when the argument validation fails', function () {
        // Arrange
        $argumentsValidator = Mockery::mock(ValidatorInterface::class);

        if ($argumentsValidator instanceof MockInterface) {
            $argumentsValidator->expects('isValid')->andReturn(false);
            $argumentsValidator->expects('getErrorMessages')->andReturn([]);
        }

        $acceptanceCriteria = Mockery::mock(AcceptanceCriteriaInterface::class);
        $postRepository = Mockery::mock(PostRepositoryInterface::class);

        $useCase = new Create($argumentsValidator, $acceptanceCriteria, $postRepository);

        // Act
        $response = $useCase->invoke([]);

        // Assert
        expect($response->message)->toBe(ResultMessage::ARGUMENT_ERROR);
        expect($response->error)->not()->toBeNull();
        expect($response->post)->toBeNull();
    });

    it('should produce an error when the given input does not comply with the acceptance criteria', function () {
        // Arrange
        $title = 'My title';
        $content = 'My content';
        $arguments = [
            'title' => $title,
            'content' => $content,
        ];

        $argumentsValidator = Mockery::mock(ValidatorInterface::class);

        if ($argumentsValidator instanceof MockInterface) {
            $argumentsValidator->expects('isValid')->andReturn(true);
        }

        $acceptanceCriteria = Mockery::mock(AcceptanceCriteriaInterface::class);

        if ($acceptanceCriteria instanceof MockInterface) {
            $acceptanceCriteria->expects('complies')->andReturn(false);
            $acceptanceCriteria->expects('getError')->andReturn(Mockery::mock(UseCaseErrorInterface::class));
        }

        $postRepository = Mockery::mock(PostRepositoryInterface::class);

        $useCase = new Create($argumentsValidator, $acceptanceCriteria, $postRepository);

        // Act
        $response = $useCase->invoke($arguments);

        // Assert
        expect($response->message)->toBe(ResultMessage::BUSINESS_LOGIC_ERROR);
        expect($response->error)->not()->toBeNull();
        expect($response->post)->toBeNull();
    });

    it('should produce an error when the persistence mechanism fails', function () {
        // Arrange
        $title = 'My title';
        $content = 'My content';
        $arguments = [
            'title' => $title,
            'content' => $content,
        ];

        $argumentsValidator = Mockery::mock(ValidatorInterface::class);

        if ($argumentsValidator instanceof MockInterface) {
            $argumentsValidator->expects('isValid')->andReturn(true);
        }

        $acceptanceCriteria = Mockery::mock(AcceptanceCriteriaInterface::class);

        if ($acceptanceCriteria instanceof MockInterface) {
            $acceptanceCriteria->expects('complies')->andReturn(true);
        }

        $postRepository = Mockery::mock(PostRepositoryInterface::class);

        if ($postRepository instanceof MockInterface) {
            $postRepository->expects('create')->andReturn(null);
        }

        $useCase = new Create($argumentsValidator, $acceptanceCriteria, $postRepository);

        // Act
        $response = $useCase->invoke($arguments);

        // Assert
        expect($response->message)->toBe(ResultMessage::FAILED);
        expect($response->error)->not()->toBeNull();
        expect($response->post)->toBeNull();
    });
});
