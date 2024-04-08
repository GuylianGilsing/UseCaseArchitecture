<?php

declare(strict_types=1);

namespace Tests\Feature\API\Requests\Validation;

use Framework\API\Requests\Validation\AbstractRequestValidator;
use Framework\API\Requests\Validation\RequestValidation;
use Mockery;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RequestValidatorMock extends AbstractRequestValidator
{
    protected function validate(ServerRequestInterface $request): bool
    {
        if (!$request->hasHeader('valid')) {
            return $this->invalidState();
        }

        return $this->validState();
    }

    public function getResponse(): ?ResponseInterface
    {
        if ($this->internalValidStateIsMarkedAsValid()) {
            return null;
        }

        return Mockery::mock(ResponseInterface::class);
    }
}

describe('Happy flow', function () {
    it('should be able to mark a request that passes the validation as valid', function () {
        // Arrange
        $requestValidator = new RequestValidatorMock();
        $request = Mockery::mock(ServerRequestInterface::class);

        if ($request instanceof MockInterface) {
            $request->expects('hasHeader')->with('valid')->times(1)->andReturn(true);
        }

        $dependencyContainer = Mockery::mock(ContainerInterface::class);

        if ($dependencyContainer instanceof MockInterface) {
            $dependencyContainer->expects('has')->with($requestValidator::class)->times(1)->andReturn(true);
            $dependencyContainer->expects('get')->with($requestValidator::class)->times(1)->andReturn($requestValidator);
        }

        $requestValidation = new RequestValidation($dependencyContainer);

        // Act
        $validationResult = $requestValidation->validate($request, using: $requestValidator::class);

        // Assert
        expect($validationResult->isValid())->toBeTrue();
    });

    it('should be able to mark a request that doesn\'t pass the validation as invalid', function () {
        // Arrange
        $requestValidator = new RequestValidatorMock();
        $request = Mockery::mock(ServerRequestInterface::class);

        if ($request instanceof MockInterface) {
            $request->expects('hasHeader')->with('valid')->times(1)->andReturn(false);
        }

        $dependencyContainer = Mockery::mock(ContainerInterface::class);

        if ($dependencyContainer instanceof MockInterface) {
            $dependencyContainer->expects('has')->with($requestValidator::class)->times(1)->andReturn(true);
            $dependencyContainer->expects('get')->with($requestValidator::class)->times(1)->andReturn($requestValidator);
        }

        $requestValidation = new RequestValidation($dependencyContainer);

        // Act
        $validationResult = $requestValidation->validate($request, using: $requestValidator::class);

        // Assert
        expect($validationResult->isValid())->toBeFalse();
    });
});
