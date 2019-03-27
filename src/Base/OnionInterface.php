<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/3/27
 * Time: 上午10:49
 */

namespace BaAGee\Onion\Base;
interface OnionInterface
{
    public function send($data);

    public function through(array $layer);

    public function then(\Closure $core);
}
