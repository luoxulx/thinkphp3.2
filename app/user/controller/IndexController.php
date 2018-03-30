<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class IndexController extends HomeBaseController
{

    /**
     * 前台用户首页(公开)
     */
    public function index()
    {
        $id   = $this->request->param("id", 0, "intval");
        $userQuery = Db::name("User");
        $user = $userQuery->where('id',$id)->find();
        if (empty($user)) {
            session('user',null);
            $this->error("查无此人！");
        }
        $this->assign($user);
        return $this->fetch(":index");

    }

    /**
     * 前台ajax 判断用户登录状态接口
     */
    function isLogin()
    {
        if (cmf_is_user_login()) {
            $a = cmf_get_current_user();
            $this->success("已登录",null,['user'=>['id'=>$a['id'],'user_nickname'=>$a['user_nickname'],'avatar'=>$a['avatar'],'user_login'=>$a['user_login']]]);
        } else {
            $this->error("未登录!");
        }
    }

    /**
     * 退出登录
    */
    public function logout()
    {
        session("user", null);//只有前台用户退出
        return redirect($this->request->root() . "/");
    }

}
