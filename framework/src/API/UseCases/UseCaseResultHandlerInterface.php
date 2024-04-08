<?php

declare(strict_types=1);

namespace Framework\API\UseCases;

/**
 * The implementation schema for a class that handles the result that a class that performs business logic generates.
 */
interface UseCaseResultHandlerInterface
{
    /**
     * @param object The result object that the use case generated.
     *
     * @return mixed A response that fits with the type of handler.
     * This could be a PSR-7 request or a rendered HTML template.
     */
    public function handle(object $result): mixed;
}
