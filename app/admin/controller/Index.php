<?php

namespace app\admin\controller;

use think\facade\View;


class Index extends Common
{
	protected array $checkLoginExclude = ['login', 'logout'];

	public function index()
	{
		return View::fetch();
	}

	public function login()
	{
		return View::fetch();
	}


}
