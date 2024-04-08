<?php

declare(strict_types=1);

namespace App\Infrastructure\UseCases\Post\GetByID\PSR7;

use App\Domain\UseCases\Post\GetByID;
use App\Domain\UseCases\Post\GetByID\Result;
use App\Domain\UseCases\Post\GetByID\ResultMessage;
use Fig\Http\Message\StatusCodeInterface;
use Framework\API\UseCases\UseCaseResultHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

use function App\Helpers\json_response;
use function App\Helpers\parse_array_to_json;

final class ResultHandler implements UseCaseResultHandlerInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param Result $result
     *
     * @return ResponseInterface
     */
    public function handle(object $result): mixed
    {
        $response = new Response(StatusCodeInterface::STATUS_NO_CONTENT);

        switch ($result->message) {
            case ResultMessage::FOUND:
                $response = $this->handleFound($result);
                break;

            case ResultMessage::NOT_FOUND:
                $response = $this->handleNotFound($result);
                break;

            case ResultMessage::FAILED:
                $response = $this->handleFailedError($result);
                break;
        }

        return $response;
    }

    private function handleFound(Result $result): ResponseInterface
    {
        $logMessage = '('.GetByID::class.') -> post found';
        $this->logger->debug($logMessage, $result->post->toJSONArray());

        return json_response(
            status: StatusCodeInterface::STATUS_OK,
            body: parse_array_to_json($result->post->toJSONArray()),
        );
    }

    private function handleNotFound(Result $result): ResponseInterface
    {
        $logMessage = '('.GetByID::class.') -> post found';
        $this->logger->debug($logMessage, $result->post->toJSONArray());

        return json_response(
            status: StatusCodeInterface::STATUS_NOT_FOUND,
            body: parse_array_to_json($result->error->getFormatted()),
        );
    }

    private function handleFailedError(Result $result): ResponseInterface
    {
        $logMessage = '('.GetByID::class.') -> persistence mechanism failed';
        $this->logger->warning($logMessage, $result->error->getFormatted());

        return json_response(
            status: StatusCodeInterface::STATUS_BAD_REQUEST,
            body: parse_array_to_json($result->error->getFormatted()),
        );
    }
}
