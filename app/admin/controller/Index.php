<?php

namespace app\admin\controller;

use app\admin\model\AdminUser as UserModel;
use think\facade\View;


class Index extends Common
{
	protected  $checkLoginExclude = ['login', 'logout'];

	public function index()
	{
		return View::fetch();
	}

	public function login()
	{
		if ($this->request->isPost()) {
			$data = [
				'username' => $this->request->post('username/s', '', 'trim'),
				'password' => $this->request->post('password/s', ''),
			];
			if (!$this->auth->login($data['username'], $data['password'])) {
				return $this->returnError('登陆失败：' . $this->auth->getError());
			}
			$loginUser = $this->auth->getLoginUser();
			$user = UserModel::find($loginUser['id']);
			$user->save(['login_date' => date("Y-m-d H:i:s")]);
			return $this->returnSuccess('登陆成功');


		}
		View::assign('thisYear', date("Y"));
		View::assign('token', $this->getToken());
		return View::fetch();
	}


}
