<?php

declare(strict_types=1);

namespace App\Application\Endpoints\Post;

use Framework\API\Endpoints\EndpointInterface;
use Framework\API\Endpoints\RoutingInformation;

final class CreateEndpoint implements EndpointInterface
{
    public function getRoutingInformation(): RoutingInformation
    {
        return new RoutingInformation(
            methods: ['POST'],
            path: '/post',
        );
    }

    /**
     * @return array<callable|string>
     */
    public function getMiddlewareStack(): array
    {
        return [];
    }

    public function getRequestValidator(): ?string
    {
        return null;
    }

    public function getUseCaseArgsFormatter(): ?string
    {
        return \App\Infrastructure\UseCases\Post\Create\PSR7\ArgsFormatter::class;
    }

    public function getUseCase(): string
    {
        return \App\Domain\UseCases\Post\Create::class;
    }

    public function getUseCaseResultHandler(): string
    {
        return \App\Infrastructure\UseCases\Post\Create\PSR7\ResultHandler::class;
    }
}
