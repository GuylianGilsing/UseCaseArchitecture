<?php

declare(strict_types=1);

namespace Framework\API\Endpoints;

/**
 * The implementation schema for a class that holds all information for an API endpoint.
 */
interface EndpointInterface
{
    public function getRoutingInformation(): RoutingInformation;

    /**
     * @return array<callable|string>
     */
    public function getMiddlewareStack(): array;

    /**
     * @return ?string The `CLASS::name` of a custom validator class that implements the `RequestValidatorInterface`
     * interface. A `null` value can be used if no request validator is needed.
     */
    public function getRequestValidator(): ?string;

    /**
     * @return ?string The `CLASS::name` of a custom args formatter class that implements the
     * `UseCaseArgsFormatterInterface` interface.  A `null` value can be used if no argument formatter is needed.
     */
    public function getUseCaseArgsFormatter(): ?string;

    /**
     * @return string The `CLASS::name` of a use case class that implements the `UseCaseInterface` interface.
     */
    public function getUseCase(): string;

    /**
     * @return string The `CLASS::name` of a use case result handler class that implements the
     * `UseCaseResultHandlerInterface` interface and returns a `Psr\Http\Message\ResponseInterface` object.
     */
    public function getUseCaseResultHandler(): string;
}
