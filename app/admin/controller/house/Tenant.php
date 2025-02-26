<?php

namespace app\admin\controller\house;

use app\admin\controller\Common;

use app\admin\model\HouseTenant as TenantModel;
use app\admin\model\TenantPhoto as PhotoModel;
use app\admin\library\Property;


use think\facade\View;

class Tenant extends Common
{
    public function index()
    {
        return View::fetch('/house/tenant/index');
    }

    public function queryTenant()
    {
        $house_property_id = Property::getProperty();
        $conditions = array(['a.house_property_id', 'in', $house_property_id]);
        $parameter = $this->request->param('parameter/s', '');


    }


}