<?php

namespace app\admin\controller\bill;

use app\admin\controller\Common;
use app\admin\model\BillSum as SumModel;
use app\admin\model\AdminUser as UserModel;
use app\admin\library\Property;
use app\admin\model\HouseOther as OtherModel;
use app\admin\model\WeBill as WeBillModel;
use app\admin\model\HouseContract as ContractModel;
use think\facade\View;

class Report extends Common
{
    public function index()
    {
        return View::fetch('/bill/report/index');
    }

    public function queryReport()
    {
        $house_property_id = Property::getProperty();
        $loginUser = $this->auth->getLoginUser();
        $accounting_month = date('Y-m');

        $income = SumModel::where('house_property_id', 'in', $house_property_id)
            ->where('type', TYPE_INCOME)
            ->where('accounting_date', $accounting_month)
            ->sum('amount');
        $spending = SumModel::where('house_property_id', 'in', $house_property_id)
            ->where('type', TYPE_EXPENDITURE)
            ->where('accounting_date', $accounting_month)
            ->sum('amount');

        $user = UserModel::find($loginUser['id']);
        $number_count = $user->houseNumber->where('house_property_id', 'in', $house_property_id)->count();
        $empty_count = $user->houseNumber->where('rent_mark', 'N')
            ->where('house_property_id', 'in', $house_property_id)->count();
        $occupancy = $number_count == 0 ? '0%' : round((($number_count - $empty_count) / $number_count) * 100) . '%';
        $contract = ContractModel::where('house_property_id', 'in', $house_property_id)
            ->whereNotNull('end_date')->count();
        $house_info = [
            'income' => $income,
            'spending' => round($spending, 2),
            'profit' => round($income - $spending, 2),
            'occupancy' => $occupancy,
            'number_count' => $number_count,
            'empty_count' => $empty_count,
            'contract_count' => $contract,
        ];
        return $this->returnResult([$house_info]);
    }

    public function echar()
    {

    }


}