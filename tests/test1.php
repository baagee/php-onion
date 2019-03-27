<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/3/27
 * Time: 上午10:58
 */
include __DIR__ . '/../vendor/autoload.php';

// 以下为经过的层

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

/*
 * 运行结果：
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
        "time": 1553656681,
        "input": "input data; CatchError; ALogic; BLogic; "
    },
    "request_id": 1553656681
}
 * */