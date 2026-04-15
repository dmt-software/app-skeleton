<?php

namespace DMT\Controllers;

use DMT\Routing\Attributes\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Route(methods: ['GET'], pattern: '[/]', name: 'home')]
class HomepageController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('Hello World!');

        return $response;
    }
}
