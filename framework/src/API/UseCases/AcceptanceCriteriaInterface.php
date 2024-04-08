<?php

declare(strict_types=1);

namespace Framework\API\UseCases;

/**
 * The implementation schema for a class that performs a single, or multiple, check(s) within business logic.
 */
interface AcceptanceCriteriaInterface
{
    /**
     * Validates that the input complies with the acceptance criterion.
     */
    public function complies(object $input): bool;

    /**
     * Retrieves any error that the acceptance criterion generates.
     */
    public function getError(): ?UseCaseErrorInterface;
}
