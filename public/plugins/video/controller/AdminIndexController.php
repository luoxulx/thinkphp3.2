<?php 
namespace plugins\video\controller;
use cmf\controller\PluginBaseController;
use plugins\video\model\PluginVideoModel;

/**
 * LNMPA-video
 */

class AdminIndexController extends PluginBaseController
{
    public $viMod;
    
    public function _initialize()
    {
        $adminId = cmf_get_current_admin_id();
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        } else {
            //TODO no login
            $this->error('未登录');
        }
        
        #实例数据表video，表前缀+plugin+表名
        $this->viMod = new PluginVideoModel();
    }
    
    public function index()
    {
        $list = $this->viMod->order('add_time desc')->where(['del_time'=>0])->paginate(10);
        
        $this->assign('list',$list);
        $this->assign('page', $list->render());
        return $this->fetch('/admin_index');
    }
    
    public function oneadd()
    {
        if ($this->request->isPost()){
            $data = $this->request->param();
            $data['user_id'] = cmf_get_current_admin_id();
            $data['add_time'] = time();
            $data['status'] = 1;
            
            $resl = $this->viMod->allowField(true)->save($data);
            if ($resl){
                $this->success('添加成功！',cmf_plugin_url('video://AdminIndex/index'));
            }else {
                $this->error('添加失败！');
            }
        }
        
        return $this->fetch('/oneadd');
    }
    
    public function oneedit()
    {
        if ($this->request->isPost()){
            $data = $this->request->param();
            
            $resl = $this->viMod->allowField(true)->save($data,['id'=>$data['id']]);
            if ($resl){
                $this->success('修改保存成功！',cmf_plugin_url('video://AdminIndex/index'));
            }else {
                $this->error('保存失败！');
            }
        }
        
        $id = $this->request->param('id/n');
        empty($id) && $this->error('参数错误！');
        
        $result = $this->viMod->get($id);
        $this->assign('data',$result);
        
        return $this->fetch('/oneedit');
    }
    
    function delete()
    {
        $id = $this->request->param('id/n');
        if (!intval($id)) $this->error('参数错误！');
        $resl = $this->viMod->where('id',$id)->delete();
        if ($resl){
            $this->success('此记录已删除！');
        }else {
            $this->error('删除失败！');
        }
    }
    
    public function toggle()
    {
        $data      = $this->request->param();
        
        if (isset($data['ids']) && !empty($data["display"])) {
            $ids = $this->request->param('ids/a');
            $this->viMod->where(['id' => ['in', $ids]])->update(['status' => 1]);
            $this->success("更新成功！");
        }
        
        if (isset($data['ids']) && !empty($data["hide"])) {
            $ids = $this->request->param('ids/a');
            $this->viMod->where(['id' => ['in', $ids]])->update(['status' => 0]);
            $this->success("更新成功！");
        }
    }
}





?>