<?php

declare(strict_types=1);

namespace Framework\API\UseCases;

/**
 * The implementation schema for a class that performs business logic.
 */
interface UseCaseInterface
{
    /**
     * Invoke the business logic from this use case.
     *
     * @param array<string, mixed> $args An associative array that holds all arguments that pertains to this use case.
     *
     * @return object A context object that holds the data that this use case generated.
     */
    public function invoke(array $args = []): object;
}
