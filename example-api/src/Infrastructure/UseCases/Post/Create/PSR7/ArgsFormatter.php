<?php

declare(strict_types=1);

namespace App\Infrastructure\UseCases\Post\Create\PSR7;

use Framework\API\UseCases\UseCaseArgsFormatterInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ArgsFormatter implements UseCaseArgsFormatterInterface
{
    /**
     * @param ServerRequestInterface $input
     *
     * @return array<string, mixed>
     */
    public function format(mixed $input): array
    {
        return is_array($input->getParsedBody()) ? $input->getParsedBody() : [];
    }
}
