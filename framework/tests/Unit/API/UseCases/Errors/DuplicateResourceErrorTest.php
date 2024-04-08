<?php

declare(strict_types=1);

namespace Tests\Unit\API\UseCases\Errors;

use ErrorException;
use Framework\API\UseCases\Errors\DuplicateResourceError;

describe('Happy flow', function () {
    it('should accept an indexed error messages array', function () {
        // Arrange
        $errorMessages = ['message1', 'message2', 'message3'];

        // Act
        $error = new DuplicateResourceError($errorMessages);

        // Assert
        expect($error->getFormatted())->toBe([
            'error' => [
                'type' => 'duplicate-resource',
                'messages' => $errorMessages,
            ],
        ]);
    });
});

describe('Throws exceptions', function () {
    it('should throw an exception when a associative array is passed to the error messages argument', function () {
        // Arrange
        $errorMessages = ['associative' => 'array'];
        $expectedExceptionMessage =
            'A "duplicate resource" error may only contain an indexed array of error messages.';

        // Act
        $action = fn () => new DuplicateResourceError($errorMessages);

        // Assert
        expect($action)->toThrow(ErrorException::class, $expectedExceptionMessage);
    });

    it('should throw an exception when a mixed array is passed to the error messages argument', function () {
        // Arrange
        $errorMessages = ['mixed', 'array' => 'test'];
        $expectedExceptionMessage =
            'A "duplicate resource" error may only contain an indexed array of error messages.';

        // Act
        $action = fn () => new DuplicateResourceError($errorMessages);

        // Assert
        expect($action)->toThrow(ErrorException::class, $expectedExceptionMessage);
    });
});
