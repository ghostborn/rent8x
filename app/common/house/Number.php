<?php

namespace app\common\house;

use app\admin\model\HouseProperty as PropertyModel;

use app\admin\model\HouseNumber as NumberModel;

class Number
{

    public static function save($id, $data)
    {
        if (!PropertyModel::find($data['house_property_id'])) {
            return ['flag' => false, 'msg' => '房产不存在'];
        }
        if ($id) {
            if (!$number = NumberModel::find($id)) {
                return ['flag' => false, 'msg' => '房间不存在'];
            }
            if (NumberModel::where('name', $data['name'])
                ->where('id', '<>', $id)
                ->where('house_property_id', $data['house_property_id'])
                ->find()) {
                return ['flag' => false, 'msg' => '房间名已存在'];
            }
        }
    }


    public static function delete($id)
    {
        if (!$number = NumberModel::find($id)) {
            return ['flag' => false, 'msg' => '删除失败,房间不存在'];
        }
    }
}