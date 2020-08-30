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
 * 基于 GuzzleHTTP 客户端的 RPC 组件
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
     * 远程调用，返回 data 字段（Array 格式）
     *
     * @param array $requestData
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function call($requestData = [], $options = [])
    {
        // 增加链路追踪参数
        $requestData['traceId'] = Util::getTraceId();
        $responseArr = $this->getResponseArr($requestData, $options);
        return $responseArr[Constant::API_DATA];
    }

    /**
     * 远程调用，返回 json（Array 格式）
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
     * 获取响应数据（Array 格式）
     *
     * @param array $requestData
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function getResponseArr($requestData = [], $options = [])
    {
        // HTTP 请求方式（默认为 GET 请求）
        $method                 = isset($options['method']) ? $options['method'] : Constant::METHOD_GET;
        // 接口地址
        $uri                    = $options['uri'];
        // 服务响应超时时间（毫秒）
        $timeout                = isset($options['timeout']) ? $options['timeout'] : Constant::DEFAULT_HTTP_TIMEOUT;
        // 连接超时时间（毫秒）
        $connectTimeout         = isset($options['connect_timeout']) ? $options['connect_timeout'] : Constant::DEFAULT_HTTP_CONNECT_TIMEOUT;
        // 错误重试次数（0 表示不执行重试机制）
        $retry                  = isset($options['retry']) ? $options['retry'] : 0;
        // headers
        $headers                = isset($options['headers']) ? $options['headers'] : [];
        if (!isset($headers['User-Agent'])) {
            $headers['User-Agent'] = env('User-Agent', Constant::DEFAULT_USER_AGENT);
        }

        // 发起 HTTP 请求
        return Retry::run(function () use ($method, $uri, $timeout, $connectTimeout, $headers, $requestData) {
            return $this->request($method, $uri, $timeout, $connectTimeout, $headers, $requestData);
        }, $retry);
    }

    /**
     * 发起 HTTP 请求
     *
     * @param $method
     * @param $uri
     * @param $timeout
     * @param $connectTimeout
     * @param $headers
     * @param array $requestData
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, $timeout, $connectTimeout, $headers, $requestData = [])
    {
        $options = [
            'connect_timeout'   => bcdiv($connectTimeout, 1000, 2),
            'timeout'           => bcdiv($timeout, 1000, 2),
            'headers'           => $headers,
        ];
        if (!empty($this->service)) $options['base_uri'] = $this->service;

        $client = $this->client->getClient($options);

        // 日志记录请求开始时间
        $beginTime = microtime(true);
        Log::info('HTTP RPC begin', ['args' => func_get_args(), 'time' => Util::now(), 'beginTime' => $beginTime]);

        try {
            if ($method === Constant::METHOD_GET) {
                $response = $client->request($method, $uri, ['query' => $requestData]);
            } else {
                $response = $client->request($method, $uri, ['form_params' => $requestData]);
            }
        } catch (\Exception $exception) {
            // 远程 HTTP 服务响应超时、远程 HTTP 服务挂掉（连接失败）等情况
            Log::error('HTTP RPC Error！', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'args' => func_get_args()]);
            throw new HttpRPCException(ErrorCode::HTTP_RPC_REQUEST_ERROR);
        }

        // 日志记录请求结束时间
        $finishTime = microtime(true);
        Log::info('HTTP RPC finish', ['args' => func_get_args(), 'time' => Util::now(), 'finishTime' => $finishTime, 'used' => bcsub($finishTime, $beginTime, 3)]);

        // HTTP 状态码非 200
        if ($response->getStatusCode() != 200) {
            Log::error('HTTP RPC Response 状态码错误！', ['statusCode' => $response->getStatusCode(), 'args' => func_get_args()]);
            throw new HttpRPCException(ErrorCode::HTTP_RPC_SERVER_RESPONSE_CODE_ERROR);
        }

        $jsonStr = $response->getBody()->getContents();

        // 返回内容为空
        if (empty($jsonStr)) throw new HttpRPCException(ErrorCode::HTTP_RPC_RESPONSE_EMPTY_ERROR);

        // json 转 array
        $responseArr = json_decode($jsonStr, true);

        // 返回内容转成数组为空
        if (empty($responseArr)) throw new HttpRPCException(ErrorCode::HTTP_RPC_RESPONSE_EMPTY_ARRAY_ERROR);

        // 检查 code、msg、data 是否完整
        if (!array_key_exists(Constant::API_CODE, $responseArr) || !array_key_exists(Constant::API_MESSAGE, $responseArr) || !array_key_exists(Constant::API_DATA, $responseArr)) {
            Log::error('code、msg、data 信息不完整！', ['responseArr' => $responseArr, 'args' => func_get_args()]);
            throw new HttpRPCException(ErrorCode::HTTP_RPC_RESPONSE_JSON_NOT_COMPLETE_ERROR);
        }

        if ($responseArr[Constant::API_CODE] !== 0) {
            Log::error('远程服务抛出异常！', ['responseArr' => $responseArr, 'args' => func_get_args()]);
            throw new \Exception($responseArr[Constant::API_CODE], $responseArr[Constant::API_MESSAGE]);
        }

        return (array)$responseArr;
    }
}