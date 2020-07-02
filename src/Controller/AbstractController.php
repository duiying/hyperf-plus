<?php

declare(strict_types=1);

namespace HyperfPlus\Controller;

use HyperfPlus\Http\Response;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

/**
 * 控制器基类（所有的控制器需要继承该类）
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Controller
 */
abstract class AbstractController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var Response
     */
    protected $response;

    /**
     * 统一Action入口方法
     *
     * @param RequestInterface $request
     * @param Response $response
     * @return mixed
     */
    abstract public function handle(RequestInterface $request, Response $response);
}
