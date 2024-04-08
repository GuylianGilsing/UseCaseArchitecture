<?php

declare(strict_types=1);

namespace Framework\API\UseCases;

/**
 * The implementation schema for a class that formats arguments that can be passed into a use case class.
 */
interface UseCaseArgsFormatterInterface
{
    /**
     * @param mixed $input The input that will be transformed to an use case args array.
     *
     * @return array<string, mixed> An associative array that can be passed into a use case's `invoke` method.
     */
    public function format(mixed $input): array;
}
