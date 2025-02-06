<?php

namespace app\admin\controller;

use app\admin\library\Auth;

use app\BaseController;

class Common extends BaseController
{
	protected $auth;
	protected array $checkLoginExclude = [];


	public function initialize()
	{

		$this->auth = Auth::getInstance();

		$action = $this->request->action();
		if (in_array($action, $this->checkLoginExclude)) {
			return;
		}

		if (!$this->auth->isLogin()) {
			return $this->error('请重新登录', '/admin/index/login');
		}
	}
}