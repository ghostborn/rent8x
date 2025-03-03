<?php

namespace app\admin\controller\admin;

use app\admin\controller\Common;

use think\facade\View;

class Permission extends Common
{
    public function index()
    {
        return View::fetch('/admin/permission/index');
    }
}