<?php

declare(strict_types=1);

namespace App\Infrastructure\DependencyInjection;

use DateTimeImmutable;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @return array<string, mixed> An associative array of PHP-DI dependency container definitions.
 */
function get_dependency_definitions(): array
{
    return [
        // Domain
        \App\Domain\UseCases\Post\Create::class => \DI\factory(static function (ContainerInterface $c) {
            return new \App\Domain\UseCases\Post\Create(
                argumentsValidator: $c->get(\App\Infrastructure\UseCases\Post\Create\ArgumentsValidator::class),
                acceptanceCriteria: $c->get(\App\Domain\UseCases\Post\Create\AcceptanceCriteria::class),
                postRepository: $c->get(\App\Domain\Repositories\PostRepositoryInterface::class),
            );
        }),

        \App\Domain\UseCases\Post\Create\AcceptanceCriteria::class => \DI\factory(
            static function (ContainerInterface $c) {
                return new \App\Domain\UseCases\Post\Create\AcceptanceCriteria(
                    $c->get(\App\Domain\UseCases\Post\Create\AcceptanceCriteria\PostCannotBeDuplicate::class),
                );
            }
        ),

        // Infrastructure
        \App\Domain\Repositories\PostRepositoryInterface::class => \DI\autowire(
            \App\Infrastructure\Repositories\PostRepository\InMemory::class
        ),

        // Monolog
        LoggerInterface::class => \DI\factory(static function () {
            $loggingDir = __DIR__.'/../../logs';

            $logger = new Logger('App');

            // Formulate the log file name
            $logFileName = (new DateTimeImmutable())->format('Y-m-d').'.log';
            $logFilePath = $loggingDir.'/'.$logFileName;

            $logger->pushHandler(
                new StreamHandler($logFilePath, LogLevel::DEBUG)
            );

            return $logger;
        }),
    ];
}
