<?php
/**
 * Desc: 抽象层
 * User: baagee
 * Date: 2019/3/27
 * Time: 上午10:56
 */

namespace BaAGee\Onion\Base;

/**
 * Class LayerAbstract
 * @package BaAGee\Onion\Base
 */
abstract class LayerAbstract
{
    /**
     * @param \Closure $next
     * @param          $data
     * @return mixed
     */
    abstract protected function handler(\Closure $next, $data);

    /**
     * @param \Closure $next
     * @param          $data
     * @return mixed
     */
    public function exec(\Closure $next, $data)
    {
        $return = $this->handler($next, $data);
        return $return;
    }
}
