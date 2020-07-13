<h1 align="center">
    hyperf-plus
</h1>

<p align="center">
    基于 hyperf 框架定制的扩展包
</p>

**env 配置**  

.env 增加一项  

```
DEBUG=true
```

**常量配置**  

app/Constant 目录下新建 AppConstant.php、AppErrorCode.php、RedisKeyConst.php。  

app/Constant/AppConstant.php  

```php
<?php

namespace App\Constant;

/**
 * 应用常量类
 *
 * @author Yaxian <wangyaxiandev@gmail.com>
 * @package App\Constant
 */
class AppConstant
{

}
```

app/Constant/AppErrorCode.php 

```php
<?php

namespace App\Constant;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * 应用错误码类
 *
 * @author Yaxian <wangyaxiandev@gmail.com>
 * @package App\Constant
 */

/**
 * @Constants
 */
class AppErrorCode extends AbstractConstants
{
    
}
```

app/Constant/RedisKeyConst.php

```php
<?php

namespace App\Constant;

/**
 * redis key 常量类
 *
 * 所有的redis key统一使用常量定义在该类中，key必须要有注释
 *
 * key命名格式（）
 *      数据类型:服务简称:业务名称
 *
 * 数据类型
 *      string -> s
 *      hash -> h
 *      set -> s
 *      zset -> z
 *      list -> l
 *      geo -> g
 *
 * 服务简称
 *      ContentService -> cs
 *      AccountService -> as
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Constant
 */
class RedisKeyConst
{
    // 示例key
    const EXAMPLE_KEY = 's:服务简称:业务名称';
}
```

**路由配置**  

config/routes.php  

```php
<?php

declare(strict_types=1);

use HyperfPlus\Route\Route;

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'HyperfPlus\Controller\IndexController@handle');
```

**命令配置**  

config/autoload/commands.php  

```php
<?php

declare(strict_types=1);

return [
    \HyperfPlus\Command\ModuleGenerator::class,
];
```

**异常处理器配置**  

config/autoload/exceptions.php  

```php
<?php

declare(strict_types=1);

return [
    'handler' => [
        'http' => [
            \HyperfPlus\Exception\Handler\AppExceptionHandler::class,
        ],
    ],
];
```

**中间件配置**  

config/autoload/middlewares.php  

```php
<?php

declare(strict_types=1);

return [
    'http' => [
        \Hyperf\Validation\Middleware\ValidationMiddleware::class,
    ],
];
```

