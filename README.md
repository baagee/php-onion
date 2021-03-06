# php-onion 洋葱结构
php onion layer

可以利用它来做中间件，action之前做请求拦截，验证登陆，权限，或者action之后的一些逻辑
## 图示例：

图片源至koa的官方图

![示例](http://www.engkan.com/usr/uploads/2018/09/1470252263.png)

## 使用示例：

### 首先定义要经过层
```php
/**
 * Class ReturnJson
 */
class ReturnJson extends \BaAGee\Onion\Base\LayerAbstract
{
    /**
     * @param Closure $next
     * @param         $data
     * @return mixed|void
     */
    protected function handler(Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $ret = $next($data);
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        echo json_encode($ret, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
    }
}

/**
 * Class CatchError
 */
class CatchError extends \BaAGee\Onion\Base\LayerAbstract
{
    /**
     * @param Closure $next
     * @param         $data
     * @return array|mixed
     */
    protected function handler(Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $ret = [
            'code'    => 0,
            'message' => ''
        ];
        try {
            $data        .= __CLASS__ . '; ';
            $ret['data'] = $next($data);
        } catch (Throwable $e) {
            $ret = [
                'code'    => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $ret['request_id'] = time();
        return $ret;
    }
}

/**
 * Class BLogic
 */
class BLogic extends \BaAGee\Onion\Base\LayerAbstract
{
    /**
     * @param Closure $next
     * @param         $data
     * @return mixed
     */
    protected function handler(\Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $data .= __CLASS__ . '; ';
        $ret  = $next($data);
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        return $ret;
    }
}

/**
 * Class ALogic
 */
class ALogic extends \BaAGee\Onion\Base\LayerAbstract
{
    /**
     * @param Closure $next
     * @param         $data
     * @return mixed
     */
    protected function handler(Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $data .= __CLASS__ . "; ";
        // throw new Exception(__CLASS__ . ' Exception', 100);
        $ret = $next($data);
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        return $ret;
    }
}
```

### 使用
```php
// 输入的数据
$input = 'input data; ';

// 经过的层  注意顺序，从前往后执行，然后从后往前一层一层返回
$through = [
    ReturnJson::class,
    CatchError::class,
    ALogic::class,
    BLogic::class
];
// 开始
(new \BaAGee\Onion\Onion())->send($input)->through($through)->then(function ($input) {
    return [
        'time'  => time(),
        'input' => $input
    ];
});
```

### 运行结果
```
开始 ReturnJson 逻辑
开始 CatchError 逻辑
开始 ALogic 逻辑
开始 BLogic 逻辑
结束 BLogic 逻辑
结束 ALogic 逻辑
结束 CatchError 逻辑
结束 ReturnJson 逻辑
{
    "code": 0,
    "message": "",
    "data": {
        "time": 1553657337,
        "input": "input data; CatchError; ALogic; BLogic; "
    },
    "request_id": 1553657337
}
```