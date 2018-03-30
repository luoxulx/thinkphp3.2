<?php
namespace plugins\video\controller;
use cmf\controller\PluginBaseController;
use plugins\video\model\PluginVideoModel;
use think\Db;

class IndexController extends PluginBaseController
{
    public $viMod;
    public function _initialize()
    {
        
        #实例数据表video，表前缀+plugin+表名
        $this->viMod = new PluginVideoModel();
    }

    public function index()
    {
        die('401');
    }
    
    public function showvi()
    {
        $id = $this->request->param('id/n');
        empty($id) && $this->error('参数错误！');
        
        $data = $this->viMod->get($id);
        $data['user_id'] = Db::name('user')->where('id',$data['user_id'])->value('user_nickname');
        
        $this->assign('data',$data);
        
        $prev = $this->viMod->get(['status'=>1,'add_time'=>['lt',$data['add_time']]]);
        $next = $this->viMod->get(['status'=>1,'add_time'=>['gt',$data['add_time']]]);
        
        $this->assign('prev',$prev);
        $this->assign('next',$next);
        
        return $this->fetch('/showvi');
    }

}
