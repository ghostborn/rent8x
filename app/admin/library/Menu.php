<?php

namespace app\admin\library;

use app\common\library\Tree;

class Menu extends Tree
{
    public function getTree($curr = '')
    {
        $data = $this->data;
        return $this->tree($data, 0);
    }

    public function getCurrentRoute($curr = '')
    {
        $result = ['id' => '0', 'pid' => '0'];
        $data = $this->data;
        foreach ($data as $k => $v) {
            if ($v['controller'] === $curr) {
                $result['id'] = $v['id'];
                $result['pid'] = $v['pid'];
                break;
            }
        }
        return $result;
    }
}