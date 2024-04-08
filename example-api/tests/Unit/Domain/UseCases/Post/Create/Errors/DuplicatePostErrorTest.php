<?php

declare(strict_types=1);

use App\Domain\Post;
use App\Domain\UseCases\Post\Create\Errors\DuplicatePostError;

test('The error message formats properly', function () {
    // Arrange
    $post = new Post(null, 'My title', 'My content');
    $error = new DuplicatePostError($post);

    // Act
    $formattedError = $error->getFormatted();

    // Assert
    expect($formattedError)->toBe([
        'error' => [
            'type' => 'duplicate-resource',
            'messages' => [sprintf('Post with title "%s" already exists', $post->getTitle())],
        ],
    ]);
});
