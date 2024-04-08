# Framework documentation
Welcome to the documentation of this simple PHP API framework.

<!-- TOC -->

- [Framework documentation](#framework-documentation)
    - [Concepts](#concepts)
        - [Domain objects](#domain-objects)
        - [Use cases](#use-cases)
        - [Use case argument formatter](#use-case-argument-formatter)
        - [Use case results](#use-case-results)
        - [Use case result handlers](#use-case-result-handlers)
        - [Endpoints](#endpoints)
    - [Applications](#applications)
        - [Application wrappers](#application-wrappers)
    - [Request validation](#request-validation)
        - [Request validator](#request-validator)

<!-- /TOC -->

## Concepts
This framework uses a few key concepts that help with speeding up API development.

### Domain objects
Where MVC uses models, this framework uses domain objects. These objects can be used by use cases, repositories, etc. to facilitate business or persistence logic. An example of a domain object inside a blog application would look like this:

```php
declare(strict_types=1);

namespace App\Domain;

final class Post
{
    private int $id = 0;
    private string $title;
    private string $content;

    public function __construct(int $id, string $title, string $content)
    {
        // These methods should throw exceptions when validation errors are encountered
        $this->id = $id;
        $this->setTitle($title);
        $this->setContent($content);
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        // TODO: Add validation code here...
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        // TODO: Add validation code here...
        $this->content = $content;
    }
}
```
*The example above is taken from an Example API project.*

### Use cases
This framework doesn't follow an MVC architecture. Instead, it uses a use case-centric architecture that automatically separates the concerns of all business logic within an application.

A use case is nothing more than a complete feature (also known as a use case) of the application that is being developed. An example of a use case would be a class that only creates blog posts.

A use case class must be delivery method agnostic. This means that it should not refer to anything that ties it to a specific delivery method (web API, CLI app, etc.). This requirement is achieved by making the use case accept a simple arguments array, and generate a simple result object.

An example of a use case class that creates a blog post would look like this:

```php
declare(strict_types=1);

namespace App\UseCases;

use App\Domain\Post;
use App\Repositories\PostRepositoryInterface;
use App\UseCases\CreatePost\CreatePostResult;
use App\UseCases\CreatePost\CreatePostResultMessage;
use App\Validators\CreatePostValidator;
use Exception;
use Framework\API\UseCases\UseCaseInterface;

final class CreatePost implements UseCaseInterface
{
    public function __construct(
        private readonly CreatePostValidator $validator,
        private readonly PostRepositoryInterface $postRepository,
    ) {
    }

    /**
     * @param array<string, mixed>
     *
     * @return CreatePostResult
     */
    public function invoke(array $args = []): object
    {
        if (!$this->validator->isValid($args)) {
            return new CreatePostResult(
                post: null,
                message: CreatePostResultMessage::ARGS_ERROR,
                argumentErrors: $this->validator->getErrorMessages(),
            );
        }

        try {
            $post = $this->postRepository->create(new Post(0, $args['title'], $args['content']));
        } catch (Exception $e) {
            return new CreatePostResult(
                post: null,
                message: CreatePostResultMessage::PROCESS_ERROR,
                processErrors: [$e->getMessage()],
            );
        }

        if ($post === null) {
            return new CreatePostResult(
                post: null,
                message: CreatePostResultMessage::PROCESS_ERROR,
                processErrors: ['Could not create post'],
            );
        }

        return new CreatePostResult(post: $post, message: CreatePostResultMessage::SUCCEEDED);
    }
}
```
*The example above is taken from an Example API project.*

As the example above illustrates, the use case receives an associative array of arguments that can be used to generate a use case result.

### Use case argument formatter
Use cases receive arguments in the form of an associative array. Depending on the application that uses the use case, these arguments could come from a web request, CLI input, etc. The use case argument formatter class specializes in retrieving these arguments from a source (web request, CLI input, etc) and formats it to the desired use case arguments format.

A use case argument formatter class that formats a PSR 7 request would look like this:

```php
```
*The example above is taken from an Example API project.*

### Use case results
A use case result is a class that contains all possible results that a use case can generate. An example of a use case result class would look like this:

```php
declare(strict_types=1);

namespace App\UseCases\CreatePost;

use App\Domain\Post;

final class CreatePostResult
{
    /**
     * @param array<string, mixed> $argumentErrors An associative array that holds all argument error messages.
     * @param array<string, mixed> $validationErrors An associative array that holds all validation error messages.
     * @param array<string, mixed> $processErrors An associative array that holds all process error messages.
     */
    public function __construct(
        public ?Post $post,
        public CreatePostResultMessage $message,
        public array $argumentErrors = [],
        public array $validationErrors = [],
        public array $processErrors = [],
    ) {
    }
}
```
*The example above is taken from an Example API project.*

### Use case result handlers
A use case result handler takes the use case result and converts it to a desired output. An example of a use case result handler that converts a use case result to a PSR 7 response would look like this:

```php
declare(strict_types=1);

namespace App\UseCases\CreatePost;

use App\Serializers\PostSerializer;
use Fig\Http\Message\StatusCodeInterface;
use Framework\API\UseCases\UseCaseResultHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

final class CreatePostResultPSR7Handler implements UseCaseResultHandlerInterface
{
    public function __construct(
        private readonly PostSerializer $postSerializer,
    ) {
    }

    /**
     * @param CreatePostResult $result
     *
     * @return ResponseInterface
     */
    public function handle(object $result): mixed
    {
        $response = new Response(StatusCodeInterface::STATUS_OK);

        switch ($result->message) {
            case CreatePostResultMessage::ARGS_ERROR:
                $response = $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
                $response->getBody()->write(
                    json_encode([
                        'argumentErrors' => $result->argumentErrors,
                    ])
                );
                break;

            case CreatePostResultMessage::VALIDATION_ERROR:
                $response = $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
                $response->getBody()->write(
                    json_encode([
                        'validationErrors' => $result->validationErrors,
                    ])
                );
                break;

            case CreatePostResultMessage::PROCESS_ERROR:
                $response = $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
                $response->getBody()->write(
                    json_encode([
                        'processErrors' => $result->processErrors,
                    ])
                );
                break;

            case CreatePostResultMessage::SUCCEEDED:
                $response->getBody()->write(
                    json_encode($this->postSerializer->serialize($result->post)),
                );
                break;
        }

        return $response->withAddedHeader('Content-Type', 'application/json');
    }
}
```
*The example above is taken from an Example API project.*

### Endpoints
An endpoint is a web-specific class that ties a use case to a specific URL. An example of an endpoint class that represents the creation of a blog post would look like this:

```php
declare(strict_types=1);

namespace App\Endpoints;

use App\RequestValidators\CreatePostRequestValidator;
use App\UseCases\CreatePost;
use App\UseCases\CreatePost\CreatePostPSR7ArgsFormatter;
use App\UseCases\CreatePost\CreatePostResultPSR7Handler;
use Framework\API\Endpoints\EndpointInterface;
use Framework\API\Endpoints\RoutingInformation;

final class CreatePostEndpoint implements EndpointInterface
{
    public function getRoutingInformation(): RoutingInformation
    {
        return new RoutingInformation(
            methods: ['POST'],
            path: '/post',
        );
    }

    /**
     * @return array<callable|string>
     */
    public function getMiddlewareStack(): array
    {
        return [];
    }

    public function getRequestValidator(): ?string
    {
        return CreatePostRequestValidator::class;
    }

    public function getUseCaseArgsFormatter(): ?string
    {
        return CreatePostPSR7ArgsFormatter::class;
    }

    public function getUseCase(): string
    {
        return CreatePost::class;
    }

    public function getUseCaseResultHandler(): string
    {
        return CreatePostResultPSR7Handler::class;
    }
}
```
*The example above is taken from an Example API project.*

## Applications
The only supported application at this moment is a REST API application. The framework itself provides a [Slim framework](https://www.slimframework.com/) wrapper that handles all web-related functionality (like routing, middleware, etc.). To create a standard application, the following code can be used:

```php
declare(strict_types=1);

use Framework\API\Application\Wrappers\SlimApplicationWrapper;
use Slim\Factory\AppFactory;

require_once __DIR__.'/../vendor/autoload.php';
// Slim application setup
$app = AppFactory::create();

// Your slim app configuration here...
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(displayErrorDetails: true, logErrors: false, logErrorDetails: false);

// REST API application setup
$api = new \Framework\API\REST(
    new SlimApplicationWrapper($app)
);

// Register endpoints
$api->registerEndpoint(new \App\Endpoints\CreatePostEndpoint());
$api->registerEndpoint(new \App\Endpoints\GetPostByIDEndpoint());
$api->registerEndpoint(new \App\Endpoints\GetPostsEndpoint());

// Run the slim application
$api->run();
```

### Application wrappers
It is possible to use a different web backend. The only thing that a developer needs to do is create a class that wraps their favorite framework and implements the `ApplicationWrapperInterface` interface:

```php
declare(strict_types=1);

namespace Framework\API\Application\Wrappers;

use Framework\API\Application\ApplicationWrapperInterface;
use Framework\API\Application\Wrappers\SlimApplicationWrapper\SlimRequestHandler;
use Framework\API\Endpoints\EndpointInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

/**
 * Handles all application functionality with a Slim framework application.
 */
final class SlimApplicationWrapper implements ApplicationWrapperInterface
{
    public function __construct(
        private App $app,
    ) {
    }

    public function setBaseURL(string $url): void
    {
        $this->app->setBasePath($url);
    }

    public function registerEndpoint(EndpointInterface $endpoint): void
    {
        $routing = $endpoint->getRoutingInformation();
        $route = $this->app->map(
            $routing->methods,
            $routing->path,
            static function (ServerRequestInterface $request, ContainerInterface $container) use ($endpoint) {
                $requestHandler = new SlimRequestHandler($endpoint, $container);

                return $requestHandler($request);
            }
        );

        foreach ($endpoint->getMiddlewareStack() as $middleware) {
            $route->add($middleware);
        }
    }

    public function run(): void
    {
        $this->app->run();
    }
}
```
*The example above is taken from framework source code.*

The application wrapper above simply translates the endpoint data to commands that the framework can use to register routes.

## Request validation
Making sure that an incoming HTTP request is valid can be a bit of a hassle. This framework streamlines this process by offering a request validation component that abstracts some of the messy details away.

Using the validator component would roughly look like this:

```php
use App\RequestValidators\CreatePostRequestValidator;
use Framework\API\Requests\Validation\RequestValidation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RequestHandler
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $dependencyContainer = // Create your own dependency container here...
        $requestValidation = new RequestValidation($dependencyContainer);

        // Act
        $validationResult = $requestValidation->validate($request, using: CreatePostRequestValidator::class);

        if (!$validationResult->isValid()) {
            return $validationResult->response();
        }

        $response = // Response create your own response here...

        return $response;
    }
}
```
*The example above is intentionally not using any dependency injection. It is recommended that the `RequestValidation` class is injected through the constructor when actually using this in a real-world application.*

As the example above illustrates, the request validation object will use a PSR 7 request object and the `::class` name of a request validator class to create a validation result. Once the `isValid()` method on that result is called, the request validator is instantiated by the dependency container and will get the request object passed into it. The validator will indicate whether the validation was successful or not and make a PSR 7 response object available when the validation has failed.

### Request validator
A request validator class will take a PSR 7 request object and validate it. The result can then be checked and used to generate a failure response.

The `AbstractRequestValidator` class can be used to quickly write a request validator class that, for example, checks if the request body has the right format:

```php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Validators\CreatePostValidator;
use Fig\Http\Message\StatusCodeInterface;
use Framework\API\Requests\Validation\AbstractRequestValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class CreatePostRequestValidator extends AbstractRequestValidator
{
    /** @var ?array<string, mixed> */
    private ?array $validationErrorMessages = null;

    public function __construct(
        private readonly CreatePostValidator $validator,
    ) {
    }

    public function getResponse(): ?ResponseInterface
    {
        if ($this->internalValidStateIsMarkedAsValid()) {
            return null;
        }

        if ($this->validationErrorMessages !== null) {
            $response = new Response(StatusCodeInterface::STATUS_BAD_REQUEST);

            $response->getBody()->write(
                json_encode([
                    'validationErrors' => $this->validationErrorMessages,
                ])
            );

            return $response->withAddedHeader('Content-Type', 'application/json');
        }

        return null;
    }

    protected function validate(ServerRequestInterface $request): bool
    {
        $body = is_array($request->getParsedBody()) ? $request->getParsedBody() : [];

        if (!$this->validator->isValid($body)) {
            $this->validationErrorMessages = $this->validator->getErrorMessages();

            return $this->invalidState();
        }

        return $this->validState();
    }
}
```
*The example above is taken from an Example API project.*
