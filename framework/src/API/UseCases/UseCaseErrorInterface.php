<?php

declare(strict_types=1);

namespace Framework\API\UseCases;

/**
 * The implementation schema for a class that contains use case error messages.
 */
interface UseCaseErrorInterface
{
    /**
     * @return array<string, mixed>|array<mixed> An array that holds all error messages in an applicable format.
     */
    public function getFormatted(): array;
}
