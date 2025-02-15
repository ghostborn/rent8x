<?php

namespace app\admin\controller\house;

use app\admin\controller\Common;
use app\admin\model\HouseProperty as PropertyModel;

use think\facade\View;

class Property extends Common
{
    public function index()
    {
        return View::fetch('house/property/index');
    }

    public function queryProperty()
    {
        $loginUser = $this->auth->getLoginUser();
        $property = PropertyModel::where('admin_user_id', $loginUser['id'])
            ->order('id')
            ->select();
        return $this->returnResult($property);

    }


}