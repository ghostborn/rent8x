<?php

namespace app\admin\controller;

use app\admin\library\Auth;
use app\BaseController;
use think\facade\Session;
use think\facade\View;
use Firebase\JWT\JWT;


class Common extends BaseController
{
    protected $auth;
    protected $checkLoginExclude = [];

    public function initialize()
    {
        if ($this->request->isPost()) {
            $token = $this->getToken();
            header('X-CSRF-TOKEN: ' . $token);
            if ($token !== $this->request->header('X-CSRF-TOKEN')) {
                return $this->error('令牌已过期，请重新提交。', '/admin/index/login');
            }
        }
        $this->auth = Auth::getInstance();
        $controller = $this->request->controller();


        $action = $this->request->action();
        if (in_array($action, $this->checkLoginExclude)) {
            return;
        }
        if (!$this->auth->isLogin()) {
            return $this->error('请重新登录', '/admin/index/login');
        }
//        if (!$this->auth->checkAuth($controller, $action)) {
//            return $this->error('您没有操作权限', '/admin/index/login');
//        }
        $loginUser = $this->auth->getLoginUser();
//        View::assign('layout_login_user', ['id' => $loginUser['id'], 'username' => $loginUser['username'], 'expiration_date' => $loginUser['expiration_date']]);

        if (!$this->request->isAjax()) {
            View::assign('layout_menu', $this->auth->menu($controller));
            View::assign('layout_token', $this->getToken());

        }
    }


    public function getToken()
    {
        $token = Session::get('X-CSRF-TOKEN');
        if (!$token) {
            $token = md5(uniqid(microtime(), true));
            Session::set('X-CSRF-TOKEN', $token);
        }
        return $token;
    }

    protected function returnResult($data = [], $count = 0, $msg = '', $code = 1)
    {
        if (!$count) {
            $count = \count($data);
        }
        $data = [
            "code" => $code,
            "msg" => $msg,
            "count" => $count,
            "data" => $data
        ];
        return \json($data);
    }

    protected function returnError($msg = '系统出错')
    {
        $data = [
            "code" => 0,
            "msg" => $msg
        ];
        return \json($data);
    }

    protected function returnSuccess($msg = '操作成功')
    {
        $data = [
            "code" => 1,
            "msg" => $msg
        ];
        return \json($data);
    }
}
