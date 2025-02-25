<?php

namespace app\admin\controller\house;

use app\admin\model\HouseBilling as BillingModel;
use app\common\house\Uncollected as UncollectedAction;

use app\admin\controller\Common;
use app\admin\library\Property;


use think\facade\View;

class Uncollected extends Common
{
    public function index()
    {
        return View::fetch('/house/uncollected/index');
    }

    // 抄表日期选项
    public function queryReadingTime()
    {
        $house_property_id = Property::getProperty();
        $conditions = array(
            ['a.house_property_id', 'in', $house_property_id],
            ['a.start_time', '< time', 'today+5 days'],
            ['a.accounting_date', 'null', ''],
        );
        $datas = BillingModel::where($conditions)
            ->alias('a')
            ->join('HouseNumber b', 'b.house_property_id = a.house_property_id and b.id = a.house_number_id')
            ->join('HouseProperty c', 'c.id = a.house_property_id')
            ->distinct(true)
            ->field('a.meter_reading_time')
            ->order('a.meter_reading_time', 'asc')
            ->select();
        $data = [];
        foreach ($datas as $value) {
            if ($value['meter_reading_time']) {
                $value['meter_reading_time'] = \substr($value['meter_reading_time'], 0, 10);
                array_push($data, $value);
            }
        }
        return $this->returnResult($data);
    }

    //主页面 table查询
    public function queryUncollected()
    {
        $order = $this->request->param('order/s', '', 'trim');
        $field = $this->request->param('field/s', '', 'trim');
        if (!$order) {
            $field = 'a.start_time, a.house_property_id, b.name';
            $order = 'asc';
        }
        $house_property_id = Property::getProperty();
        $meter_reading_time = $this->request->param('meter_reading_time/s', '', 'trim');
        $conditions = array(
            ['a.house_property_id', 'in', $house_property_id],
            ['a.start_time', '< time', 'today+10 days'],
            ['a.accounting_date', 'null', ''],
        );
        if ($meter_reading_time) {
            \array_push($conditions, ['a.meter_reading_time', '=', $meter_reading_time]);
        }
        $count = BillingModel::where($conditions)->alias('a')->count();
        $datas = BillingModel::where($conditions)
            ->alias('a')
            ->join('HouseNumber b', 'b.house_property_id = a.house_property_id and b.id = a.house_number_id')
            ->join('HouseProperty c', 'c.id = a.house_property_id')
            ->field('a.*, b.name, b.water_price, b.electricity_price, c.name as property_name')
            ->order($field, $order)
            ->select();
        foreach ($datas as $value) {
            if ($value['meter_reading_time']) {
                $value['meter_reading_time'] = \substr($value['meter_reading_time'], 0, 10);
            }
            if ($value['start_time']) {
                $value['start_time'] = \substr($value['start_time'], 0, 10);
            }
            if ($value['end_time']) {
                $value['end_time'] = \substr($value['end_time'], 0, 10);
            }
            $value['total_money2'] = Property::convert_case_number($value['total_money']);
        }
        return $this->returnResult($datas, $count);
    }

    //抄表页面 保存-common
    public function save()
    {
        $id = $this->request->post('id/d', 0);
        $data = [
            'house_property_id' => $this->request->post('house_property_id/d', 0),
            'house_number_id' => $this->request->post('house_number_id/d', 0),
            'meter_reading_time' => $this->request->post('meter_reading_time/s', '', 'trim'),
            'electricity_meter_this_month' => $this->request->post('electricity_meter_this_month/d', 0),
            'water_meter_this_month' => $this->request->post('water_meter_this_month/d', 0),
            'electricity_meter_last_month' => $this->request->post('electricity_meter_last_month/d', 0),
            'water_meter_last_month' => $this->request->post('water_meter_last_month/d', 0),
            'rental' => $this->request->post('rental/d', 0),
            'deposit' => $this->request->post('deposit/d', 0),
            'management' => $this->request->post('management/d', 0),
            'network' => $this->request->post('network/d', 0),
            'garbage_fee' => $this->request->post('garbage_fee/d', 0),
            'other_charges' => $this->request->post('other_charges/f', 0),
            'note' => $this->request->post('note/s', '', 'trim'),
        ];
        $result = UncollectedAction::save($id, $data);
        if ($result['flag']) {
            return $this->returnSuccess($result['msg']);
        } else {
            return $this->returnError($result['msg']);
        }
    }

    //收据页面-查询历史水电
    public function queryHistory()
    {
        $limit = 5;
        $number_id = $this->request->param('number_id/d', 0);
        $tenant_id = $this->request->param('tenant_id/d', 0);
        $conditions = array(
            ['a.house_number_id', '=', $number_id],
            ['a.tenant_id', '=', $tenant_id],
            ['a.end_time', 'not null', '']
        );
        $billing_data = BillingModel::where($conditions)
            ->alias('a')
            ->join('HouseNumber b', 'b.id = a.house_number_id')
            ->join('HouseProperty c', 'c.id = a.house_property_id')
            ->field('a.*, b.name as number_name, c.name as property_name')
            ->order(['a.start_time' => 'desc'])
            ->limit($limit)
            ->select();
        foreach ($billing_data as $value) {
            $value['start_time'] = \substr($value['start_time'], 0, 10);
        }
        return $this->returnResult($billing_data, $limit);


    }


}