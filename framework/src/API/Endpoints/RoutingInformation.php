<?php

declare(strict_types=1);

namespace Framework\API\Endpoints;

/**
 * Holds all the routing information for an API endpoint.
 */
final class RoutingInformation
{
    /**
     * @param array<string> $methods The HTTP methods that this endpoint can be called with.
     * @param string $path A Slim 4 compatible path.
     *
     * @link https://www.slimframework.com/docs/v4/objects/routing.html
     */
    public function __construct(
        public array $methods,
        public string $path,
    ) {
    }
}
