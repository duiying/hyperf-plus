<?php

namespace HyperfPlus\Constant;

/**
 * 基础常量类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Constant
 */
class Constant
{
    const API_CODE                          = 'code';               // API接口code字段
    const API_MESSAGE                       = 'msg';                // API接口msg字段
    const API_DATA                          = 'data';               // API接口data字段

    const DEFAULT_PAGE                      = 1;                    // 默认页码
    const DEFAULT_SIZE                      = 20;                   // 默认每页大小

    const METHOD_GET                        = 'GET';                // GET请求
    const METHOD_POST                       = 'POST';               // POST请求

    const DEFAULT_HTTP_TIMEOUT              = 1000;                 // 连接超时时间
    const DEFAULT_HTTP_CONNECT_TIMEOUT      = 1000;                 // 服务响应超时时间
}