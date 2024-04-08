<?php

declare(strict_types=1);

namespace Tests\Unit\API\Requests\Validation;

use ErrorException;
use Framework\API\Requests\Validation\RequestValidatorInterface;
use Framework\API\Requests\Validation\ValidationResult;
use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ServerRequestInterface;

describe('Throws exceptions', function () {
    it('should throw an exception when the response() method is called, and the request has passed the validation checks.', function () {
        // Arrange
        $request = Mockery::mock(ServerRequestInterface::class);
        $requestValidator = Mockery::mock(RequestValidatorInterface::class);

        if ($requestValidator instanceof MockInterface) {
            $requestValidator->expects('isValid')->with($request)->times(1)->andReturn(true);
        }

        $validationResult = new ValidationResult(
            $requestValidator,
            $request,
        );

        $expectedExceptionMessage =
            'Can\'t convert request validation result to a PSR-7 response, the validation passed.';

        // Act
        $validationResult->isValid();
        $action = fn () => $validationResult->response();

        // Assert
        expect($action)->toThrow(ErrorException::class, $expectedExceptionMessage);
    });
});
