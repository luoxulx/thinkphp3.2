<?php

namespace plugins\userlogin;
use cmf\lib\Plugin;
use think\Db;
use plugins\userlogin\model\UserLoginLogModel;

class UserloginPlugin extends Plugin
{

    public $info = [
        'name'        => 'Userlogin',
        'title'       => '前台登录记录',
        'description' => '前台登录记录',
        'status'      => 1,
        'author'      => 'LNMPA',
        'version'     => '111',
        'demo_url'    => 'https://lnmpa.top',
        'author_url'  => 'https://lnmpa.top'
    ];

    public $hasAdmin = 1;

    public function install()
    {
        if (userlogin_is_installed()) {
            return true;
        }
        $config=config('database');
        $sql = cmf_split_sql(PLUGINS_PATH . 'userlogin/data/userlogin.sql', $config['prefix'], $config['charset']);
        foreach ($sql as &$value) {Db::query($value);}
        @touch(PLUGINS_PATH . 'userlogin/data/install.lock');
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function UserLoginStart($hookParam)
    {
        $loginlog=new UserLoginLogModel();
        if($hookParam['compare_password_result']){
            $loginlog->succeed=1;
        }else{
            $loginlog->succeed=0;
        }
        if(!empty($hookParam['user']['mobile'])) $username=$hookParam['user']['mobile'];
        if(!empty($hookParam['user']['user_login'])) $username=$hookParam['user']['user_login'];
        if(!empty($hookParam['user']['user_email'])) $username=$hookParam['user']['user_email'];
        $loginlog->username=$username;
        $loginlog->pwd=substr($hookParam['user']['user_pass'], 0,4)."****";
        $loginlog->time=time();
        $loginlog->ip=get_client_ip(0, true);
        $loginlog->save();
    }
}

function userlogin_is_installed()
{
    static $cmfIsInstalled;
    if (empty($cmfIsInstalled)) {
        $cmfIsInstalled = file_exists(PLUGINS_PATH . 'userlogin/data/install.lock');
    }
    return $cmfIsInstalled;
}