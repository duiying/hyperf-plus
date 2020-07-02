<?php

namespace HyperfPlus\Http;

use Hyperf\Guzzle\ClientFactory;

/**
 * GuzzleHTTP客户端封装类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Http
 */
class Client
{
    /**
     * @var \Hyperf\Guzzle\ClientFactory
     */
    private $clientFactory;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * 获取guzzle客户端
     *
     * @param array $options
     * @return \GuzzleHttp\Client
     */
    public function getClient($options = [])
    {
        // $options 等同于 GuzzleHttp\Client 构造函数的 $config 参数
        // $client 为协程化的 GuzzleHttp\Client 对象
        return $this->clientFactory->create($options);
    }
}