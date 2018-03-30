<?php 
namespace plugins\lxcomment\controller;

use cmf\controller\PluginBaseController;
use plugins\lxcomment\model\PluginLxcommentModel;
use think\Db;

class AdminIndexController extends PluginBaseController
{
    function _initialize()
    {
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        } else {
            //TODO no login
            $this->error('未登录');
        }
    }
    
    function index()
    {
        $plMod = new PluginLxcommentModel();
        
        $where['delete_time'] = 0;
        #$where['parent_id'] = 0;#一楼评论
        
        $list = $plMod->where($where)->order('create_time desc')->paginate(15);
        
        foreach ($list as $k=>$v){
            if ($list[$k]['url']){
                $list[$k]['url'] = json_decode(base64_decode($list[$k]['url']),true);
                $list[$k]['url'] = url($list[$k]['url']['action'], $list[$k]['url']['param']);
                $list[$k]['object_title'] = Db::name('portal_post')->where(['id'=>$list[$k]['object_id']])->value('post_title');
            }
        }
        
        $this->assign('list',$list);
        $this->assign('page',$list->render());
        
        return $this->fetch('/admin_index');
    }
    
    public function delete()
    {
        $plMod = new PluginLxcommentModel();
        $id = $this->request->param('id/d');
        $ids = $this->request->param('ids/a');
        
        if ($id){
            $plMod->where(['id'=>$id])->update(['delete_time'=>time(),'status'=>0]);
        }
        if ($ids){
            $plMod->where(['id' => ['in', $ids]])->update(['delete_time'=>time(),'status' => 0]);
        }
        
        $this->success('评论已删除！');
    }
    
    public function toggle()
    {
        $plMod = new PluginLxcommentModel();
        $data      = $this->request->param();
        
        if (isset($data['ids']) && !empty($data["display"])) {
            $ids = $this->request->param('ids/a');
            $plMod->where(['id' => ['in', $ids]])->update(['status' => 1]);
            $this->success("更新成功！");
        }
        
        if (isset($data['ids']) && !empty($data["hide"])) {
            $ids = $this->request->param('ids/a');
            $plMod->where(['id' => ['in', $ids]])->update(['status' => 0]);
            $this->success("更新成功！");
        }
    }
    
    /**
     * 评论回收站
     */
    public function recycle(){
        $plMod = new PluginLxcommentModel();
        
        $list = $plMod->where(['delete_time'=>['neq',0],'status'=>0])->order('create_time desc')->paginate(15);
        foreach ($list as $k=>$v){
            if ($list[$k]['url']){
                $list[$k]['url'] = json_decode(base64_decode($list[$k]['url']),true);
                $list[$k]['url'] = url($list[$k]['url']['action'], $list[$k]['url']['param']);
                $list[$k]['object_title'] = Db::name('portal_post')->where(['id'=>$list[$k]['object_id']])->value('post_title');
            }
        }
        
        $this->assign('list',$list);
        $this->assign('page',$list->render());
        return $this->fetch('/admin_recycle');
    }
    
    public function rollback()
    {
        $id = $this->request->param('id/d');
        $ids = $this->request->param('ids/a');
        $plMod = new PluginLxcommentModel();
        
        if ($id){
            $resl = $plMod->where(['id'=>$id])->update(['delete_time'=>0,'status'=>1]);
        }
        
        if ($ids){
            $resl = $plMod->where(['id'=>['in',$ids]])->update(['delete_time'=>0,'status'=>1]);
        }
        
        if ($resl){
            $this->success('评论还原成功！');
        }else {
            $this->error('还原失败！');
        }
    }
    
    public function dieDelete(){
        $id = $this->request->param('id/d');
        $ids = $this->request->param('ids/a');
        $plMod = new PluginLxcommentModel();
        
        if ($id){
            $resl = $plMod->destroy($id);
        }
        if ($ids){
            $resl = $plMod->destroy($ids);
        }
        
        if ($resl){
            $this->success('评论已彻底删除！');
        }else {
            $this->error('删除失败！');
        }
    }
    
}



?>