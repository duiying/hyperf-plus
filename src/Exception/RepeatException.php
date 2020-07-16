<?php

namespace HyperfPlus\Exception;

use HyperfPlus\Constant\ErrorCode;
use Hyperf\Server\Exception\ServerException;
use Throwable;

/**
 * 重复操作异常类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Exception
 */
class RepeatException extends ServerException
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = ErrorCode::getMessage($code);
        }

        parent::__construct($message, $code, $previous);
    }
}