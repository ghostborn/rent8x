<?php

namespace app\admin\model;

use think\Model;

class AdminUser extends Model
{
    public function adminPermission()
    {
        return $this->hasMany('AdminPermission', 'admin_role_id', 'admin_role_id');
    }
}