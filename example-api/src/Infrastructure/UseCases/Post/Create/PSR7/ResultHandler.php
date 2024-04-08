<?php

declare(strict_types=1);

namespace App\Infrastructure\UseCases\Post\Create\PSR7;

use App\Domain\UseCases\Post\Create;
use App\Domain\UseCases\Post\Create\Result;
use App\Domain\UseCases\Post\Create\ResultMessage;
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
            case ResultMessage::CREATED:
                $response = $this->handleCreated($result);
                break;

            case ResultMessage::ARGUMENT_ERROR:
                $response = $this->handleArgumentError($result);
                break;

            case ResultMessage::BUSINESS_LOGIC_ERROR:
                $response = $this->handleBusinessLogicError($result);
                break;

            case ResultMessage::FAILED:
                $response = $this->handleFailedError($result);
                break;
        }

        return $response;
    }

    private function handleCreated(Result $result): ResponseInterface
    {
        $logMessage = '('.Create::class.') -> post created';
        $this->logger->debug($logMessage, $result->post->toJSONArray());

        return json_response(
            status: StatusCodeInterface::STATUS_OK,
            body: parse_array_to_json($result->post->toJSONArray()),
        );
    }

    private function handleArgumentError(Result $result): ResponseInterface
    {
        $logMessage = '('.Create::class.') -> argument validation failed';
        $this->logger->debug($logMessage, $result->error->getFormatted());

        return json_response(
            status: StatusCodeInterface::STATUS_BAD_REQUEST,
            body: parse_array_to_json($result->error->getFormatted()),
        );
    }

    private function handleBusinessLogicError(Result $result): ResponseInterface
    {
        $logMessage = '('.Create::class.') -> business logic failed';
        $this->logger->debug($logMessage, $result->error->getFormatted());

        return json_response(
            status: StatusCodeInterface::STATUS_BAD_REQUEST,
            body: parse_array_to_json($result->error->getFormatted()),
        );
    }

    private function handleFailedError(Result $result): ResponseInterface
    {
        $logMessage = '('.Create::class.') -> persistence mechanism failed';
        $this->logger->debug($logMessage, $result->error->getFormatted());

        return json_response(
            status: StatusCodeInterface::STATUS_BAD_REQUEST,
            body: parse_array_to_json($result->error->getFormatted()),
        );
    }
}
