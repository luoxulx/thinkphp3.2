<?php

namespace plugins\Lxcomment;//Demo插件英文名，改成你的插件英文就行了
use cmf\lib\Plugin;
use think\Db;

//Demo插件英文名，改成你的插件英文就行了
class LxcommentPlugin extends Plugin
{

    public $info = [
        'name'        => 'Lxcomment',//Demo插件英文名，改成你的插件英文就行了
        'title'       => 'LNMPA评论插件',
        'description' => 'LNMPA评论插件',
        'status'      => 1,
        'author'      => 'LNMPA',
        'version'     => '1.0',
        'demo_url'    => 'https://lnmpa.top',
        'author_url'  => 'https://lnmpa.top'
    ];

    public $hasAdmin = 1;//插件是否有后台管理界面

    // 插件安装
    public function install()
    {
        if ($this->lxcomment_is_installed()) {
            return true;
        }
        $config = config('database');
        $sql = cmf_split_sql(PLUGINS_PATH . 'lxcomment/data/ce_plugin_lxcomment.sql', $config['prefix'], $config['charset']);
        foreach ($sql as &$value) {
            Db::query($value);
        }
        @touch(PLUGINS_PATH . 'lxcomment/data/install.lock');
        
        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {   
        #清空缓存
        cmf_clear_cache();
        #如不需要评论数据请手动删除数据表    表前缀+plugin+lxcomment
        @unlink(PLUGINS_PATH . 'lxcomment/data/install.lock');
        return true;//卸载成功返回true，失败false
    }

    //实现的footer_start钩子方法
    public function comment($param)
    {
        
        $config = $this->getConfig();
        $this->assign($config);
        cache('lxcomment_param',$param);
        $this->assign('param',json_encode($param));
        
        echo $this->fetch('widget');
    }
    #是否安装
    public function lxcomment_is_installed()
    {
        static $lxcomment = null;
        if (empty($lxcomment)) {
            $lxcomment = file_exists(PLUGINS_PATH . 'lxcomment/data/install.lock');
        }
        return $lxcomment;
    }
    
    
    
    

}