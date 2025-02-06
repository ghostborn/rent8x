<?php

namespace app\admin\library;

use think\facade\Session;

class Auth
{
    protected string $sessionName = 'admin';
    protected static $instance;

    public static function getInstance($options = []): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    public function isLogin(): bool
    {
        return false;
    }
}