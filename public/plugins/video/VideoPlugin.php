<?php
/**
 * LNMPA-video
 */
namespace plugins\video;
use cmf\lib\Plugin;
use think\Db;
use plugins\video\model\PluginVideoModel;

class VideoPlugin extends Plugin
{

    public $info = [
        'name'        => 'Video',
        'title'       => 'LNMPA视频插件',
        'description' => 'LNMPA视频插件',
        'status'      => 1,
        'author'      => 'LNMPA',
        'version'     => '1111.0',
        'demo_url'    => 'https://LNMPA.TOP',
        'author_url'  => 'https://lnmpa.top'
    ];

    public $hasAdmin = 1;

    // 插件安装
    public function install()
    {
        if ($this->video_is_installed()) {
            return true;
        }
        $config = config('database');
        $sql = cmf_split_sql(PLUGINS_PATH . 'video/data/ce_plugin_video.sql', $config['prefix'], $config['charset']);
        foreach ($sql as &$value) {
            Db::query($value);
        }
        @touch(PLUGINS_PATH . 'video/data/install.lock');
        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        #清空缓存
        cmf_clear_cache();
        @unlink(PLUGINS_PATH . 'video/data/install.lock');
        return true;//卸载成功返回true，失败false
    }

    //实现的video钩子方法
    public function video($param)
    {
        $config = $this->getConfig();
        $this->assign($config);
        
        if (file_exists(PLUGINS_PATH . 'video/data/install.lock')){
            $vimod = new PluginVideoModel();
            
            $list = $vimod->relists();
            $this->assign('lists',$list);
            $this->assign('pages',$list->render());
        }
        
        echo $this->fetch('video');
    }
    
    public function video_is_installed()
    {
        static $cmfIsInstalled = null;
        if (empty($cmfIsInstalled)) {
            $cmfIsInstalled = file_exists(PLUGINS_PATH . 'video/data/install.lock');
        }
        return $cmfIsInstalled;
    }
}