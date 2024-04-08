<?php

declare(strict_types=1);

namespace Framework\API;

/**
 * Provides functionality for checking if a `CLASS::name` string implements a specific interface.
 */
trait ClassNameImplementsTrait
{
    /**
     * Checks if a `CLASS::name` string implements a specific interface.
     *
     * @param string $interface The interface that the class must implement.
     * @param string $className The `CLASS::name` string of the class that needs to be checked.
     */
    protected function classNameImplements(string $interface, string $className): bool
    {
        $interfaces = class_implements($className);

        if (!is_array($interfaces)) {
            $interfaces = [];
        }

        return !in_array($interface, array_keys($interfaces));
    }
}
