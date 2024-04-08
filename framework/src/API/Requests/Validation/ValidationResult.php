<?php

declare(strict_types=1);

namespace Framework\API\Requests\Validation;

use ErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Provides the result of a PSR-7 request validation operation.
 */
final class ValidationResult
{
    private bool $isValid = false;

    public function __construct(
        private readonly RequestValidatorInterface $requestValidator,
        private readonly ServerRequestInterface $request,
    ) {
    }

    public function isValid(): bool
    {
        $this->isValid = $this->requestValidator->isValid($this->request);

        return $this->isValid;
    }

    /**
     * Converts the validation result to a PSR-7 response.
     *
     * @throws ErrorException when the request has passed the validation checks.
     */
    public function response(): ResponseInterface
    {
        if ($this->isValid) {
            throw new ErrorException(
                'Can\'t convert request validation result to a PSR-7 response, the validation passed.'
            );
        }

        return $this->requestValidator->getResponse();
    }
}
