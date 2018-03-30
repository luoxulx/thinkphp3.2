<?php
namespace plugins\guest;//Demo插件英文名，改成你的插件英文就行了
use cmf\lib\Plugin;

//Demo插件英文名，改成你的插件英文就行了
class GuestPlugin extends Plugin
{

    public $info = [
        'name'        => 'Guest',//Demo插件英文名，改成你的插件英文就行了
        'title'       => '留言插件',
        'description' => '留言插件',
        'status'      => 1,
        'author'      => 'LNMPA',
        'version'     => '111',
        'demo_url'    => 'https://lnmpa.top',
        'author_url'  => 'https://lnmpa.top'
    ];

    public $hasAdmin = 1;//插件是否有后台管理界面

    // 插件安装
    public function install()
    {
        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        return true;//卸载成功返回true，失败false
    }
    
    public function guestbook($param)
    {
        $config = $this->getConfig();
        $this->assign($param);
        $this->assign($config);
        return $this->fetch('widget');
    }

}