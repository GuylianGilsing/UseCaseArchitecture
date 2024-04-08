<?php

declare(strict_types=1);

namespace App\Helpers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

function string_is_blank(string $text): bool
{
    return strlen(trim($text)) === 0;
}

/**
 * Creates a new JSON response.
 *
 * @param int $status A HTTP status code.
 * @param string $body The response body.
 * @param array<string, mixed> $headers An associative array of key => value headers.
 */
function json_response(
    int $status = StatusCodeInterface::STATUS_OK,
    string $body = '',
    array $headers = []
): ResponseInterface {
    $response = new Response($status);

    $response->getBody()->write($body);

    foreach ($headers as $key => $value) {
        $response = $response->withHeader($key, $value);
    }

    return $response->withHeader('Content-Type', 'application/json');
}

/**
 * Parses an array to the default CMS JSON format.
 *
 * NOTE: It is discouraged to use double quotes inside your JSON keys. It will lead to undefined behavior when
 * automatically parsing empty objects to `null` values.
 *
 * @param array<mixed> $array
 *
 * @return ?string Returns the parsed JSON string when a valid array is given, `null` otherwise.
 */
function parse_array_to_json(array $array): ?string
{
    $parsed = json_encode($array, JSON_OBJECT_AS_ARRAY | JSON_PRESERVE_ZERO_FRACTION);

    if (!is_string($parsed)) {
        return null;
    }

    // Convert empty objects to null values
    return preg_replace('~("[^":\\\]+"\:)\{\}~', '\1null', $parsed);
}

/**
 * Parses a `parse_array_to_json()` formatted JSON string back to an array.
 *
 * @param string $json The JSON string.
 *
 * @return ?array<mixed> Returns an array when the string is valid, `null` otherwise.
 */
function parse_json_string_to_array(string $json): ?array
{
    $decoded = json_decode($json, true, 512, JSON_OBJECT_AS_ARRAY | JSON_PRESERVE_ZERO_FRACTION);

    if (!is_array($decoded)) {
        return null;
    }

    return $decoded;
}
