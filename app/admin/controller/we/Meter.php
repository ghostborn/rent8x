<?php

namespace app\admin\controller\we;

use app\admin\controller\Common;
use app\admin\model\HouseNumber as NumberModel;
use app\admin\model\WeMeter as MeterModel;
use app\admin\model\WeDetail as WeDetailModel;
use app\admin\model\WeBill as WeBillModel;
use app\admin\library\Property;

use think\facade\View;
use think\facade\Db;

class Meter extends Common
{
    public function index()
    {
        return View::fetch('/we/meter/index');
    }

    public function queryMeter()
    {
        $house_property_id = Property::getProperty();
        $conditions = array(['a.house_property_id', 'in', $house_property_id]);
        $count = MeterModel::where($conditions)->alias('a')->count();
        $meters = MeterModel::where($conditions)->alias('a')
            ->join('HouseProperty c', 'a.house_property_id = c.id')
            ->field("a.*, c.name as property_name")
            ->order(['house_property_id'])
            ->select();
        foreach ($meters as $value) {
            if ($value['house_number_id']) {
                $value['number_name'] = '';
                $array = explode(',', $value['house_number_id']);
                foreach ($array as $value1) {
                    $value['number_name'] .= NumberModel::find($value1)['name'] . ',';
                }
                if (strlen($value['number_name']) > 0) {
                    $value['number_name'] = substr($value['number_name'], 0, -1);
                }
            }
        }
        return $this->returnResult($meters);
    }

    public function save()
    {
        $id = $this->request->post('id/d', 0);
    }


}