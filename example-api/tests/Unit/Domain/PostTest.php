<?php

declare(strict_types=1);

use App\Domain\Post;

describe('Happy flow', static function () {
    it('should construct without an ID and not throw any exceptions', function () {
        // Arrange
        $id = null;
        $title = 'My post';
        $content = 'My content';

        // Act
        $post = new Post($id, $title, $content);

        // Assert
        expect($post->getID())->toBe($id);
        expect($post->getTitle())->toBe($title);
        expect($post->getContent())->toBe($content);
    });

    it('should construct with an ID and not throw any exceptions', function () {
        // Arrange
        $id = 1;
        $title = 'My post';
        $content = 'My content';

        // Act
        $post = new Post($id, $title, $content);

        // Assert
        expect($post->getID())->toBe($id);
        expect($post->getTitle())->toBe($title);
        expect($post->getContent())->toBe($content);
    });

    it('should construct with a title that is exactly 32 characters long', function () {
        // Arrange
        $id = 1;
        $title = 'A truly 32 characters long title';
        $content = 'My content';

        // Act
        $post = new Post($id, $title, $content);

        // Assert
        expect($post->getID())->toBe($id);
        expect($post->getTitle())->toHaveLength(32);
        expect($post->getContent())->toBe($content);
    });

    it('should be able to set a title that is exactly 32 characters long', function () {
        // Arrange
        $id = 1;
        $title = 'My title';
        $content = 'My content';

        $post = new Post($id, $title, $content);

        // Act
        $post->setTitle('A truly 32 characters long title');

        // Assert
        expect($post->getID())->toBe($id);
        expect($post->getTitle())->toHaveLength(32);
        expect($post->getContent())->toBe($content);
    });

    it('should properly format to JSON with ID field when it\'s included', function () {
        // Arrange
        $id = 1;
        $title = 'My title';
        $content = 'My content';

        $post = new Post($id, $title, $content);

        // Act
        $json = $post->toJSONArray();

        // Assert
        expect($json)->toBe([
            'id' => $id,
            'title' => $title,
            'content' => $content,
        ]);
    });

    it('should properly format to JSON without ID field when it\'s omitted', function () {
        // Arrange
        $title = 'My title';
        $content = 'My content';

        $post = new Post(null, $title, $content);

        // Act
        $json = $post->toJSONArray();

        // Assert
        expect($json)->toBe([
            'title' => $title,
            'content' => $content,
        ]);
    });
});

describe('Unhappy flow', function () {
    it('should throw an exception when a post is constructed with an empty title', function () {
        // Arrange
        $id = 1;
        $title = '';
        $content = 'My content';

        $expectedException = ErrorException::class;
        $expectedExceptionMessage = 'Title can\'t be blank';

        // Act
        $action = fn () => new Post($id, $title, $content);

        // Assert
        expect($action)->toThrow($expectedException, $expectedExceptionMessage);
    });

    it('should throw an exception when a post title is set to an empty piece of text', function () {
        // Arrange
        $id = 1;
        $title = 'My post';
        $content = 'My content';

        $expectedException = ErrorException::class;
        $expectedExceptionMessage = 'Title can\'t be blank';

        $post = new Post($id, $title, $content);

        // Act
        $action = fn () => $post->setTitle('');

        // Assert
        expect($action)->toThrow($expectedException, $expectedExceptionMessage);
    });

    it('should throw an exception when a post is constructed with a title that is longer than 32 characters', function () {
        // Arrange
        $id = 1;
        $title = 'A title that is longer than 32 characters';
        $content = 'My content';

        $expectedException = ErrorException::class;
        $expectedExceptionMessage = 'Title can\'t have more than 32 characters';

        // Act
        $action = fn () => new Post($id, $title, $content);

        // Assert
        expect($action)->toThrow($expectedException, $expectedExceptionMessage);
    });

    it('should throw an exception when a post title is set to a title that is longer than 32 characters', function () {
        // Arrange
        $id = 1;
        $title = 'My post';
        $content = 'My content';

        $expectedException = ErrorException::class;
        $expectedExceptionMessage = 'Title can\'t have more than 32 characters';

        $post = new Post($id, $title, $content);

        // Act
        $action = fn () => $post->setTitle('A title that is longer than 32 characters');

        // Assert
        expect($action)->toThrow($expectedException, $expectedExceptionMessage);
    });

    it('should throw an exception when a post is constructed with no content', function () {
        // Arrange
        $id = 1;
        $title = 'My title';
        $content = '';

        $expectedException = ErrorException::class;
        $expectedExceptionMessage = 'No content has been given';

        // Act
        $action = fn () => new Post($id, $title, $content);

        // Assert
        expect($action)->toThrow($expectedException, $expectedExceptionMessage);
    });

    it('should throw an exception when a post\'s content is set to an empty piece of text', function () {
        // Arrange
        $id = 1;
        $title = 'My post';
        $content = 'My content';

        $expectedException = ErrorException::class;
        $expectedExceptionMessage = 'No content has been given';

        $post = new Post($id, $title, $content);

        // Act
        $action = fn () => $post->setContent('');

        // Assert
        expect($action)->toThrow($expectedException, $expectedExceptionMessage);
    });
});
