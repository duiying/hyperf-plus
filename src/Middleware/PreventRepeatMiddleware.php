<?php

namespace HyperfPlus\Middleware;

use Hyperf\HttpServer\Contract\RequestInterface;
use HyperfPlus\Constant\ErrorCode;
use HyperfPlus\Exception\RepeatException;
use HyperfPlus\Http\Response;
use HyperfPlus\Util\Util;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 防重放中间件
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Middleware
 */
class PreventRepeatMiddleware implements MiddlewareInterface
{
    /**
     * 2 秒内只能请求一次
     * @var int
     */
    private $limit = 2;

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
        $this->container    = $container;
        $this->response     = $response;
        $this->request      = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestData    = $this->request->all();
        $requestPath    = $this->request->getUri()->getPath();

        if (empty($requestData)) {
            return $handler->handle($request);
        }

        // 根据请求 path 和请求数据生成 redis key
        $key = $requestPath . ':' . md5(serialize($requestData));

        if (!Util::getLock($key, $this->limit)) {
            throw new RepeatException(ErrorCode::REPEAT_EXCEPTION);
        }

        return $handler->handle($request);
    }
}