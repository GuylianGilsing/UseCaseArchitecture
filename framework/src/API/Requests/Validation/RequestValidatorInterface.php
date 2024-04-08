<?php

declare(strict_types=1);

namespace Framework\API\Requests\Validation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The implementation schema for a class that validates a PSR-7 request.
 */
interface RequestValidatorInterface
{
    /**
     * Performs validation on a given PSR-7 request and provides an indication of how that went.
     */
    public function isValid(ServerRequestInterface $request): bool;

    /**
     * Generates an appropiate PSR-7 response.
     *
     * @return ?ResponseInterface returns a response object when the validation fails and `null` when it succeeds.
     */
    public function getResponse(): ?ResponseInterface;
}
