<?php

namespace DMT\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TrimTrailingSlashMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        if ($path != '/' && str_ends_with($path, '/')) {
            $uri = $request->getUri()->withPath(rtrim($path, '/'));

            if (strcasecmp($request->getMethod(), 'get') == 0) {
                return $this->responseFactory->createResponse()
                    ->withStatus(301)
                    ->withHeader('location', (string)$uri);
            }

            $request = $request->withUri($uri);
        }

        return $handler->handle($request);
    }
}
