<?php

namespace app\common\house;

use app\admin\model\HouseNumber as NumberModel;

class Number
{
    public static function delete($id)
    {
        if (!$number = NumberModel::find($id)) {
            return ['flag' => false, 'msg' => '删除失败,房间不存在'];
        }
    }
}