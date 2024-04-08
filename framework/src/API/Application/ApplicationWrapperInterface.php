<?php

declare(strict_types=1);

namespace Framework\API\Application;

use Framework\API\Endpoints\EndpointInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The implementation schema for a class that performs application functionality.
 */
interface ApplicationWrapperInterface
{
    public function setBaseURL(string $url): void;
    public function registerEndpoint(EndpointInterface $endpoint): void;

    /**
     * Handle incomming web requests and display the output.
     */
    public function run(): void;

    /**
     * Handle an already existing request and return the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface;
}
