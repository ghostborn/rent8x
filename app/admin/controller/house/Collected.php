<?php

namespace app\admin\controller\house;

use app\admin\controller\Common;
use app\admin\library\Property;
use app\admin\model\HouseBilling as BillingModel;
use think\facade\View;
use const app\admin\controller\LAYUI_LIMIT;
use const app\admin\controller\LAYUI_PAGE;

class Collected extends Common
{
    public function index()
    {
        return View::fetch('/house/collected/index');
    }


    public function queryCollected()
    {
        $page = $this->request->param('page/d', LAYUI_PAGE);
        $limit = $this->request->param('limit/d', LAYUI_LIMIT);
        $house_property_id = Property::getProperty();
        $conditions = array(
            ['a.house_property_id', 'in', $house_property_id],
            ['a.accounting_date', 'not null', '']
        );
        $parameter = $this->request->param('parameter/s', '');
        if ($parameter) {
            $conditions[] = function ($query) use ($parameter) {
                $query->where('b.name', 'like', "%{$parameter}%")
                    ->whereOr('c.name', 'like', "%{$parameter}%");
            };
        };
        $count = BillingModel::alias('a')
            ->join('HouseNumber b', 'a.house_property_id = b.house_property_id and a.house_number_id = b.id')
            ->join('HouseNumber c', 'c.id = a.house_property_id')
            ->where($conditions)
            ->count();
        $billing = BillingModel::where($conditions)
            ->alias('a')
            ->join('HouseNumber b', 'a.house_property_id = b.house_property_id and a.house_number_id = b.id')
            ->join('HouseProperty c', 'c.id = a.house_property_id')
            ->field('a.*,b.name as number_name, c.name as property_name')
            ->order(['a.accounting_date' => 'desc', 'number_name'])
            ->page($page, $limit)
            ->select();
        foreach ($billing as $value) {
            $value['accounting_date'] = \substr($value['accounting_date'], 0, 10);
            if ($value['end_time']) {
                $value['lease'] = \substr($value['start_time'], 0, 10) . '至' . \substr($value['end_time'], 0, 10);
            } elseif ($value['meter_reading_time']) {
                $value['lease'] = \substr($value['start_time'], 0, 10) . '退房';
            } else {
                $value['lease'] = \substr($value['start_time'], 0, 10);
            }
        }
        return $this->returnResult($billing, $count);
    }


    public function sum()
    {
        $house_property_id = Property::getProperty();
        $sum = BillingModel::where('house_property_id', 'in', $house_property_id)
            ->whereTime('accounting_date', 'today')
            ->sum('total_money');
        return $this->returnResult([], 1, round($sum, 2));
    }
}