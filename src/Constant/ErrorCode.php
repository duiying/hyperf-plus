<?php

namespace HyperfPlus\Constant;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * 基础错误码类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Constant
 */

/**
 * @Constants
 */
class ErrorCode extends AbstractConstants
{
    /******************** 基础错误 begin 1001 ~ 1200 ********************************************************************/
    /**
     * @Message("参数错误")
     */
    const PARAMS_INVALID                                = 1001;

    /**
     * @Message("服务异常")
     */
    const TRIGGER_EXCEPTION                             = 1002;

    /**
     * @Message("请勿重复操作")
     */
    const REPEAT_EXCEPTION                              = 1003;
    /******************** 基础错误 end **********************************************************************************/


    /******************** Retry 组件 begin 2001 ~ 2100 *****************************************************************/
    /**
     * @Message("重试回调无效")
     */
    const RETRY_CALLABLE_INVALID                        = 2001;

    /**
     * @Message("服务内部错误，请稍后重试！")
     */
    const RETRY_ERROR                                   = 2002;
    /******************** Retry 组件 end *******************************************************************************/


    /******************** Http RPC 组件 begin 5001 ~ 5100 **************************************************************/
    /**
     * @Message("服务内部错误，请稍后重试！")
     */
    const HTTP_RPC_SERVER_RESPONSE_CODE_ERROR           = 5001;

    /**
     * @Message("服务内部错误，请稍后重试！")
     */
    const HTTP_RPC_RESPONSE_EMPTY_ERROR                 = 5002;

    /**
     * @Message("服务内部错误，请稍后重试！")
     */
    const HTTP_RPC_RESPONSE_EMPTY_ARRAY_ERROR           = 5003;

    /**
     * @Message("服务内部错误，请稍后重试！")
     */
    const HTTP_RPC_RESPONSE_JSON_NOT_COMPLETE_ERROR     = 5004;

    /**
     * @Message("服务内部错误，请稍后重试！")
     */
    const HTTP_RPC_REQUEST_ERROR                        = 5005;
    /******************** Http RPC 组件 end ****************************************************************************/
}