<?php

declare(strict_types=1);

namespace Framework\API\Application\Wrappers\SlimApplicationWrapper;

use ErrorException;
use Framework\API\ClassNameImplementsTrait;
use Framework\API\Endpoints\EndpointInterface;
use Framework\API\Requests\Validation\RequestValidation;
use Framework\API\UseCases\UseCaseArgsFormatterInterface;
use Framework\API\UseCases\UseCaseInterface;
use Framework\API\UseCases\UseCaseResultHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SlimRequestHandler
{
    use ClassNameImplementsTrait;

    private RequestValidation $requestValidation;

    public function __construct(
        private readonly EndpointInterface $endpoint,
        private readonly ContainerInterface $container,
    ) {
        $this->requestValidation = $container->get(RequestValidation::class);
        $this->validateAllEndpointClassNames($endpoint);
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->endpoint->getRequestValidator() !== null) {
            $result = $this->requestValidation->validate($request, using: $this->endpoint->getRequestValidator());

            if (!$result->isValid()) {
                return $result->response();
            }
        }

        $useCaseArgs = [];

        if ($this->endpoint->getUseCaseArgsFormatter() !== null) {
            /** @var UseCaseArgsFormatterInterface $useCaseArgsFormatter */
            $useCaseArgsFormatter = $this->container->get($this->endpoint->getUseCaseArgsFormatter());
            $useCaseArgs = $useCaseArgsFormatter->format($request);
        }

        /** @var UseCaseInterface $useCase */
        $useCase = $this->container->get($this->endpoint->getUseCase());

        /** @var UseCaseResultHandlerInterface $useCaseResultHandler */
        $useCaseResultHandler = $this->container->get($this->endpoint->getUseCaseResultHandler());

        $result = $useCase->invoke($useCaseArgs);
        $response = $useCaseResultHandler->handle($result);

        if (!($response instanceof ResponseInterface)) {
            throw new ErrorException(
                'Use case result handler must return an object that implements "'.ResponseInterface::class.'".'
            );
        }

        return $response;
    }

    private function validateAllEndpointClassNames(EndpointInterface $endpoint): void
    {
        $this->classNameImplements(UseCaseInterface::class, $endpoint->getUseCase());

        if ($endpoint->getUseCaseResultHandler() !== null) {
            $this->classNameImplements(UseCaseResultHandlerInterface::class, $endpoint->getUseCaseResultHandler());
        }

        if ($endpoint->getUseCaseArgsFormatter() !== null) {
            $this->classNameImplements(UseCaseArgsFormatterInterface::class, $endpoint->getUseCaseArgsFormatter());
        }
    }
}
