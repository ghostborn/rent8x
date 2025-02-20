<?php

namespace app\admin\controller\house;

use app\admin\controller\Common;

use app\admin\library\Date;
use app\admin\model\HouseNumber as NumberModel;
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
        $empty_count = $user->houseNumber->where('rent-mark', 'N')->where('house_property_id', 'in',
            $house_property_id)->count();
        $occupancy = $number_count == 0 ? '0%' : round((($number_count - $empty_count) / $number_count) * 100) . '%';
        $number_info = [
            'occupancy' => $occupancy,
            'rented' => $number_count - $empty_count,
            'empty' => $empty_count,
        ];
        return $this->returnResult($number_info);
    }

    public function queryNumber()
    {
        $house_property_id = Property::getProperty();
        $conditions = array(['a.house_property_id', 'in', $house_property_id]);
        $parameter = $this->request->param('parameter/s', '');
        if ($parameter) {
            $conditions[] = function ($query) use ($parameter) {
                $query->where('a.name', 'like', "%{$parameter}%")
                    ->whereOr('b.name', 'like', "%{$parameter}%");
            };
        }
        $numbers = NumberModel::where($conditions)
            ->alias('a')
            ->join('HouseProperty b', 'a.house_property_id = b.id')
            ->field('a.*,b.name as property_name')
            ->order('a.house_property_id,a.name')
            ->select();

        $currentDateTime = new \DateTime();
        foreach ($numbers as &$value) {
            if ($value['lease']) {
                $value['rent_date'] = Date::getLease($value['checkin_time'], $value['lease'] - $value['lease_type'])[0];
            }
            if ($value['checkin_time']) {
                $value['checkin_time'] = \substr($value['checkin_time'], 0, 10);
            }
            if ($value['rent_mark'] === 'N' && $value['payment_time']) {
                $value['idle'] = Date::formatDays($currentDateTime->diff(new \DateTime($value['payment_time']))->days);
            }
        }
        return $this->returnResult($numbers);
    }


    public function save()
    {
        $id = $this->request->post('id/d', 0);
        $data = [
            'house_property_id' => $this->request->post('house_property_id/d', 0),
            'name' => $this->request->post('name/s', '', 'trim'),
            'rental' => $this->request->post('rental/d', 0),
            'deposit' => $this->request->post('deposit/d', 0),
            'lease_type' => $this->request->post('lease_type/d', 0),
            'management' => $this->request->post('management/d', 0),
            'network' => $this->request->post('network/d', 0),
            'garbage_fee' => $this->request->post('garbage_fee/d', 0),
            'daily_rent' => $this->request->post('daily_rent/d', 0),
            'water_price' => $this->request->post('water_price/f', 0.0),
            'electricity_price' => $this->request->post('electricity_price/f', 0.0),
            'equipment' => $this->request->post('equipment/s', '', 'trim'),
        ];


    }


}