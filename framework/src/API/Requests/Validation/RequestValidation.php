<?php

declare(strict_types=1);

namespace Framework\API\Requests\Validation;

use ErrorException;
use Framework\API\ClassNameImplementsTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Validates PSR-7 compliant request objects.
 */
final class RequestValidation
{
    use ClassNameImplementsTrait;

    /**
     * @param ContainerInterface $container A valid PSR-11 dependency container instance.
     */
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    /**
     * Validates a request based on a custom validator class.
     *
     * @param string $using The `CLASS::name` of a custom validator class that implements the
     * `RequestValidatorInterface` interface.
     *
     * @throws ErrorException when an invalid request validator class is passed.
     * @throws ErrorException when the request validator class can't be resolved by a PSR-11 dependency container.
     */
    public function validate(ServerRequestInterface $request, string $using): ValidationResult
    {
        if ($this->classNameImplements(RequestValidatorInterface::class, $using)) {
            throw new ErrorException(
                'Given request validator class does not implement "'.RequestValidatorInterface::class.'".'
            );
        }

        if (!$this->container->has($using)) {
            throw new ErrorException('Can\'t resolve the dependencies of "'.$using.'".');
        }

        /** @var RequestValidatorInterface $requestValidator */
        $requestValidator = $this->container->get($using);

        return new ValidationResult($requestValidator, $request);
    }
}
