<?php

namespace HyperfPlus\Http;

use HyperfPlus\Constant\Constant;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

/**
 * API 响应数据封装类
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

    /**
     * API 成功响应数据
     *
     * @param string $msg
     * @param null $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function success($msg = '', $data = null)
    {
        return $this->response->json([Constant::API_CODE => 0, Constant::API_MESSAGE => $msg, Constant::API_DATA => $data]);
    }

    /**
     * API 失败响应数据
     *
     * @param int $code
     * @param string $msg
     * @param null $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function error($code = 0, $msg = '', $data = null)
    {
        return $this->response->json([Constant::API_CODE => $code, Constant::API_MESSAGE => $msg, Constant::API_DATA => $data]);
    }
}