<?php

declare(strict_types=1);

namespace Tests\Unit\API\Requests\Validation;

use ErrorException;
use Framework\API\Requests\Validation\RequestValidation;
use Framework\API\Requests\Validation\RequestValidatorInterface;
use Mockery;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

final class RequestValidatorMock implements RequestValidatorInterface
{
    public function isValid(ServerRequestInterface $request): bool
    {
        return false;
    }

    public function getResponse(): ?ResponseInterface
    {
        return null;
    }
}

describe('Throws exceptions', function () {
    it('should throw an exception when an invalid request validator class is passed', function () {
        // Arrange
        $request = Mockery::mock(ServerRequestInterface::class);
        $dependencyContainer = Mockery::mock(ContainerInterface::class);

        $requestValidation = new RequestValidation($dependencyContainer);

        $expectedExceptionMessage =
            'Given request validator class does not implement "'.RequestValidatorInterface::class.'".';

        // Act
        $action = fn () => $requestValidation->validate($request, stdClass::class);

        // Assert
        expect($action)->toThrow(ErrorException::class, $expectedExceptionMessage);
    });

    it('should throw an exception when the request validator class can\'t be resolved by a container.', function () {
        // Arrange
        $request = Mockery::mock(ServerRequestInterface::class);
        $dependencyContainer = Mockery::mock(ContainerInterface::class);

        if ($dependencyContainer instanceof MockInterface) {
            $dependencyContainer->expects('has')->with(RequestValidatorMock::class)->times(1)->andReturn(false);
        }

        $requestValidation = new RequestValidation($dependencyContainer);

        $expectedExceptionMessage = 'Can\'t resolve the dependencies of "'.RequestValidatorMock::class.'".';

        // Act
        $action = fn () => $requestValidation->validate($request, RequestValidatorMock::class);

        // Assert
        expect($action)->toThrow(ErrorException::class, $expectedExceptionMessage);
    });
});
