<?php

declare(strict_types=1);

namespace Framework\API;

use Framework\API\Application\ApplicationWrapperInterface;
use Framework\API\Endpoints\EndpointInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A REST API application.
 */
final class REST
{
    public function __construct(
        private ApplicationWrapperInterface $wrapper
    ) {
    }

    public function setBaseURL(string $url): void
    {
        $this->wrapper->setBaseURL($url);
    }

    public function registerEndpoint(EndpointInterface $endpoint): void
    {
        $this->wrapper->registerEndpoint($endpoint);
    }

    /**
     * Handle incomming web requests and display the output.
     */
    public function run(): void
    {
        $this->wrapper->run();
    }

    /**
     * Handle an already existing request and return the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->wrapper->handle($request);
    }
}
