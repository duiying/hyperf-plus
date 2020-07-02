<?php

namespace HyperfPlus\RPC;

use HyperfPlus\Constant\Constant;
use HyperfPlus\Constant\ErrorCode;
use HyperfPlus\Exception\HttpRPCException;
use HyperfPlus\Http\Client;
use HyperfPlus\Log\Log;
use HyperfPlus\Retry\Retry;
use HyperfPlus\Util\Util;
use Hyperf\Di\Annotation\Inject;

/**
 * 基于GuzzleHTTP客户端的RPC组件
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\RPC
 */
class HttpRPC
{
    /**
     * 服务地址
     *
     * @var string
     */
    public $service = '';

    /**
     * @Inject()
     * @var Client
     */
    public $client;

    /**
     * 远程调用，返回data字段（Array格式）
     *
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    public function call($param)
    {
        $responseArr = $this->getResponseArr($param);
        return $responseArr[Constant::API_DATA];
    }

    /**
     * 远程调用，返回json（Array格式）
     *
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    public function json($param)
    {
       return $this->getResponseArr($param);
    }

    /**
     * 获取响应数据（Array格式）
     *
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    public function getResponseArr($param)
    {
        // HTTP请求方式
        $method             = isset($param['method']) ? $param['method'] : Constant::METHOD_GET;
        // 接口地址
        $uri                = $param['uri'];
        // 服务响应超时时间（毫秒）
        $timeout            = isset($param['timeout']) ? $param['timeout'] : Constant::DEFAULT_HTTP_TIMEOUT;
        // 连接超时时间（毫秒）
        $connectTimeout     = isset($param['connect_timeout']) ? $param['connect_timeout'] : Constant::DEFAULT_HTTP_CONNECT_TIMEOUT;
        // 错误重试次数（0表示不执行重试机制）
        $retry              = isset($param['retry']) ? $param['retry'] : 0;

        // 发起HTTP请求
        return Retry::run(function () use ($method, $uri, $timeout, $connectTimeout) {
            return $this->request($method, $uri, $timeout, $connectTimeout);
        }, $retry);
    }

    /**
     * 发起HTTP请求
     *
     * @param $method
     * @param $uri
     * @param $timeout
     * @param $connectTimeout
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, $timeout, $connectTimeout)
    {
        $options = [
            'connect_timeout'   => bcdiv($connectTimeout, 1000, 2),
            'timeout'           => bcdiv($timeout, 1000, 2),
        ];
        if (!empty($this->service)) $options['base_uri'] = $this->service;

        $client = $this->client->getClient($options);

        $beginTime = Util::getTimestampWithMilliSecond();
        Log::info('HTTP RPC begin', ['args' => func_get_args(), 'time' => Util::now(), 'beginTime' => $beginTime]);

        try {
            $response = $client->request($method, $uri);
        } catch (\Exception $exception) {
            // 远程HTTP服务响应超时、远程HTTP服务挂掉（连接失败）等情况
            Log::error('HTTP RPC Error', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'args' => func_get_args()]);
            throw new HttpRPCException(ErrorCode::HTTP_RPC_REQUEST_ERROR);
        }

        // HTTP状态码非200
        if ($response->getStatusCode() != 200) {
            Log::error('HTTP RPC Response 状态码错误！', ['status_code' => $response->getStatusCode(), 'args' => func_get_args()]);
            throw new HttpRPCException(ErrorCode::HTTP_RPC_SERVER_RESPONSE_CODE_ERROR);
        }

        $jsonStr = $response->getBody()->getContents();

        if (empty($jsonStr)) throw new HttpRPCException(ErrorCode::HTTP_RPC_RESPONSE_EMPTY_ERROR);

        // json转array
        $responseArr = json_decode($jsonStr, true);

        if (empty($responseArr)) throw new HttpRPCException(ErrorCode::HTTP_RPC_RESPONSE_EMPTY_ARRAY_ERROR);

        // 检查code、msg、data是否存在
        if (!isset($responseArr[Constant::API_CODE]) || !isset($responseArr[Constant::API_MESSAGE]) || !isset($responseArr[Constant::API_DATA])) {
            Log::error('code、msg、data信息不完整', ['responseArr' => $responseArr, 'args' => func_get_args()]);
            throw new HttpRPCException(ErrorCode::HTTP_RPC_RESPONSE_JSON_NOT_COMPLETE_ERROR);
        }

        $finishTime = Util::getTimestampWithMilliSecond();
        Log::info('HTTP RPC finish', ['args' => func_get_args(), 'time' => Util::now(), 'finishTime' => $finishTime, 'used' => sprintf('%dms', $finishTime * 1000 - $beginTime * 1000)]);

        return (array)$responseArr;
    }
}