<?php

declare(strict_types=1);

namespace Framework\API\Application\Wrappers;

use Framework\API\Application\ApplicationWrapperInterface;
use Framework\API\Application\Wrappers\SlimApplicationWrapper\SlimRequestHandler;
use Framework\API\Endpoints\EndpointInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

/**
 * Handles all application functionality with a Slim framework application.
 */
final class SlimApplicationWrapper implements ApplicationWrapperInterface
{
    public function __construct(
        private App $app,
    ) {
    }

    public function setBaseURL(string $url): void
    {
        $this->app->setBasePath($url);
    }

    public function registerEndpoint(EndpointInterface $endpoint): void
    {
        $routing = $endpoint->getRoutingInformation();
        $route = $this->app->map(
            $routing->methods,
            $routing->path,
            static function (ServerRequestInterface $request, ContainerInterface $container) use ($endpoint) {
                $requestHandler = new SlimRequestHandler($endpoint, $container);

                return $requestHandler($request);
            }
        );

        foreach ($endpoint->getMiddlewareStack() as $middleware) {
            $route->add($middleware);
        }
    }

    public function run(): void
    {
        $this->app->run();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->app->handle($request);
    }
}
