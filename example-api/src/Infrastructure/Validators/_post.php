<?php

declare(strict_types=1);

namespace App\Infrastructure\Validators\Post;

use PHPValidation\Fields\FieldValidatorInterface;

use function PHPValidation\Functions\isString;
use function PHPValidation\Functions\maxLength;
use function PHPValidation\Functions\notEmpty;
use function PHPValidation\Functions\required;

/**
 * @return array<string, array<FieldValidatorInterface>>
 */
function title_validation(): array
{
    return ['title' => [required(), isString(), notEmpty(), maxLength(32)]];
}

/**
 * @return array<string, string|array<string, mixed>>
 */
function title_validation_errors(): array
{
    return [];
}

/**
 * @return array<string, array<FieldValidatorInterface>>
 */
function content_validation(): array
{
    return ['content' => [required(), isString(), notEmpty()]];
}

/**
 * @return array<string, string|array<string, mixed>>
 */
function content_validation_errors(): array
{
    return [];
}
