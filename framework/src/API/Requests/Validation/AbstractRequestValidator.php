<?php

declare(strict_types=1);

namespace Framework\API\Requests\Validation;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Provides a developer common request validator functionality.
 */
abstract class AbstractRequestValidator implements RequestValidatorInterface
{
    private bool $isValid = false;

    public function isValid(ServerRequestInterface $request): bool
    {
        return $this->validate($request);
    }

    protected function internalValidStateIsMarkedAsValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Marks the internal valid state is valid.
     */
    protected function validState(): true
    {
        $this->isValid = true;

        return $this->isValid;
    }

    /**
     * Marks the internal valid state is invalid.
     */
    protected function invalidState(): false
    {
        $this->isValid = false;

        return $this->isValid;
    }

    abstract protected function validate(ServerRequestInterface $request): bool;
}
