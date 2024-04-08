<?php

declare(strict_types=1);

namespace Framework\API\UseCases\Errors;

use ErrorException;
use Framework\API\UseCases\UseCaseErrorInterface;

/**
 * Represents a resource not found error.
 */
final class ResourceNotFoundError implements UseCaseErrorInterface
{
    /**
     * @param array<string, mixed>|array<mixed> $errorMessages An array that contains all argument error messages.
     *
     * @throws ErrorException when the given array is not an indexed array.
     */
    public function __construct(
        private readonly array $errorMessages,
    ) {
        if (!array_is_list($errorMessages)) {
            throw new ErrorException(
                'A "resource not found" error may only contain an indexed array of error messages.'
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getFormatted(): array
    {
        return [
            'error' => [
                'type' => 'resource-not-found',
                'messages' => $this->errorMessages,
            ],
        ];
    }
}
