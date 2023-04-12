<?php

namespace DMT\Test\Middlewares;

use DMT\Middlewares\TrimTrailingSlashMiddleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TrimTrailingSlashMiddlewareTest extends TestCase
{
    /**
     * @dataProvider trailingSlashProvider
     */
    public function testProcess(string $method, string $uri, string $expectedPath): void
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMockForAbstractClass();

        $handler->expects($this->any())->method('handle')->willReturnCallback(
            function (ServerRequestInterface $request) use ($expectedPath) {
                return (new Response(200))->withHeader('location', (string)$request->getUri());
            }
        );

        $middleware = new TrimTrailingSlashMiddleware(new HttpFactory());
        $response = $middleware->process(new ServerRequest($method, $uri), $handler);

        $this->assertSame($expectedPath, $response->getHeaderLine('location'));
    }

    public static function trailingSlashProvider(): iterable
    {
        return [
            'redirect with trailing slash' => ['get', '/home/', '/home'],
            'redirect with multiple slashes' => ['get', '/data///', '/data'],
            'no redirect, no trailing slash' => ['get', '/home', '/home'],
            'rewrite with trailing slash' => ['post', '/news/item/', '/news/item'],
            'rewrite with multiple slashes' => ['post', '/news/20///', '/news/20'],
            'no rewrite, no trailing slash' => ['delete', '/news/item', '/news/item'],
        ];
    }
}
