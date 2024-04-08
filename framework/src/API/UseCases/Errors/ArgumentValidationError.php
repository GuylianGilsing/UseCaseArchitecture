<?php

declare(strict_types=1);

namespace Framework\API\UseCases\Errors;

use Framework\API\UseCases\UseCaseErrorInterface;

/**
 * Represents a use case argument validation error.
 */
final class ArgumentValidationError implements UseCaseErrorInterface
{
    /**
     * @param array<string, mixed>|array<mixed> $errorMessages An array that contains all error messages.
     */
    public function __construct(
        private readonly array $errorMessages,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getFormatted(): array
    {
        return [
            'error' => [
                'type' => 'argument-validation',
                'messages' => $this->errorMessages,
            ],
        ];
    }
}
