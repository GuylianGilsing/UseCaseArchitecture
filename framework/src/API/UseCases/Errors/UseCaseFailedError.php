<?php

declare(strict_types=1);

namespace Framework\API\UseCases\Errors;

use ErrorException;
use Framework\API\UseCases\UseCaseErrorInterface;

/**
 * Represents any error that made the use case fail in its execution.
 */
final class UseCaseFailedError implements UseCaseErrorInterface
{
    /**
     * @param array<string, mixed>|array<mixed> $errorMessages An array that contains all error messages.
     *
     * @throws ErrorException when the given array is not an indexed array.
     */
    public function __construct(
        private readonly array $errorMessages,
    ) {
        if (!array_is_list($errorMessages)) {
            throw new ErrorException(
                'A "use case failed" error may only contain an indexed array of error messages.'
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
                'type' => 'failed',
                'messages' => $this->errorMessages,
            ],
        ];
    }
}
