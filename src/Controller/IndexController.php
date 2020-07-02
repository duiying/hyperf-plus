<?php

declare(strict_types=1);

namespace HyperfPlus\Controller;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * 默认控制器
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Controller
 */
class IndexController extends AbstractController
{
    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        $user = $request->input('user', 'Hyperf');
        $method = $request->getMethod();

        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }
}
