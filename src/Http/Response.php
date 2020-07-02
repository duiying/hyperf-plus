<?php

namespace HyperfPlus\Http;

use HyperfPlus\Constant\Constant;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

/**
 * API响应数据封装类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Http
 */
class Response
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container    = $container;
        $this->response     = $container->get(ResponseInterface::class);
    }

    public function success($data = null, $msg = '')
    {
        return $this->response->json([Constant::API_CODE => 0, Constant::API_MESSAGE => $msg, Constant::API_DATA => $data]);
    }
}