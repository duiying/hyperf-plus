<?php

namespace HyperfPlus\Middleware;

use Hyperf\HttpServer\Contract\RequestInterface;
use HyperfPlus\Http\Response;
use HyperfPlus\Log\StdoutLog;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PreventRepeatMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(ContainerInterface $container, Response $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestData    = $this->request->all();
        $requestUrl     = $this->request->url();

        StdoutLog::print($requestData);
        StdoutLog::print($requestUrl);


        if (empty($requestData)) {
            return $handler->handle($request);
        }

        return $handler->handle($request);
    }
}