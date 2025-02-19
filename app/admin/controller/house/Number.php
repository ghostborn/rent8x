<?php

namespace app\admin\controller\house;

use app\admin\controller\Common;

use app\admin\model\AdminUser as UserModel;

use app\admin\library\Property;
use think\facade\View;

class Number extends Common
{
    public function index()
    {
        return View::fetch('house/number/index');
    }


    // 房间管理页面-获取房间信息
    public function getNumberMessage()
    {
        $loginUser = $this->auth->getLoginUser();
        $house_property_id = Property::getProperty();
        $user = UserModel::find($loginUser['id']);
        $number_count = $user->houseNumber->where('house_property_id', 'in', $house_property_id)->count();

    }


}