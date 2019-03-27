<?php
/**
 * Desc: 洋葱模型
 * User: baagee
 * Date: 2019/3/27
 * Time: 上午10:48
 */

namespace BaAGee\Onion;

use BaAGee\Onion\Base\LayerAbstract;
use BaAGee\Onion\Base\OnionInterface;

/**
 * Class Onion
 * @package BaAGee\Onion
 */
final class Onion implements OnionInterface
{
    /**
     * @var string
     */
    protected $inputData = '';
    /**
     * @var array
     */
    protected $layerArray = [];

    /**
     * 发送数据
     * @param $data
     * @return $this
     */
    public function send($data)
    {
        $this->inputData = $data;
        return $this;
    }

    /**
     * 设置经过的层
     * @param array $layer
     * @return $this
     */
    public function through(array $layer)
    {
        $this->layerArray = $layer;
        return $this;
    }

    /**
     * 到达中间所做的操作
     * @param \Closure $core
     * @return mixed
     */
    public function then(\Closure $core)
    {
        $arrive = array_reduce(
            array_reverse($this->layerArray),
            function (\Closure $stack, $layer) {
                return function ($data) use ($stack, $layer) {
                    // 获取此中间件的对象
                    $layerObj = new $layer();
                    if ($layerObj instanceof LayerAbstract) {
                        // 调用中间件
                        return $layerObj->exec($stack, $data);
                    } else {
                        throw new \Exception(sprintf('[%s]没有继承[%s]', $layer, LayerAbstract::class));
                    }
                };
            },
            $core
        );
        return call_user_func($arrive, $this->inputData);
    }
}
