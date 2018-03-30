<?php 
namespace app\undergo\controller;

use think\Db;
use cmf\controller\AdminBaseController;

class AdminLineController extends AdminBaseController
{
    
    
    public function adminIndex()
    {
        $list = Db::name('lines')->order('time desc')->paginate(10);
        $page = $list->render();
        
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    
    public function add()
    {
        if ($this->request->isPost()){
            $data = $this->request->param();
            $data['time'] = strtotime($data['time']);
            if (empty($data['info'])) $this->error('请填写事件内容！');
            
            $resl = Db::name('lines')->insertGetId($data);
            if ($resl){
                $this->success('事件已添加！',url('adminIndex'));
            }else {
                $this->error('事件添加失败！');
            }
        }
        
        $this->assign('meatname','添加');
        return $this->fetch('edit');
    }
    
    public function edit()
    {
        $lineModel = Db::name('lines');
        if ($this->request->isPost()){
            $data = $this->request->param();
            $data['time'] = strtotime($data['time']);
            if (empty($data['info'])) $this->error('请填写事件内容！');
            $resl = $lineModel->update($data);
            if ($resl){
                $this->success('事件更改已保存！',url('adminIndex'));
            }else {
                $this->error('事件更改保存失败！');
            }
        }
        
        $id = $this->request->param('id');
        if (!$id) $this->error('参数错误！');
        
        $data = $lineModel->find($id);
        $this->assign('data',$data);
        
        $this->assign('meatname','编辑');
        return $this->fetch('edit');
    }
    
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        $resl = Db::name('lines')->delete($id);
        
        if ($resl){
            $this->success("删除成功！", url("adminIndex"));
        }else {
            $this->error('删除失败了！');
        }
        
    }
    
    public function listOrder()
    {
        $linkModel = Db::name('lines');
        parent::listOrders($linkModel);
        $this->success("排序更新成功！");
    }
    
    public function toggle()
    {
        $data      = $this->request->param();
        $linkModel = Db::name('lines');
        
        if (isset($data['ids']) && !empty($data["display"])) {
            $ids = $this->request->param('ids/a');
            $linkModel->where(['id' => ['in', $ids]])->update(['status' => 1]);
            $this->success("更新成功！");
        }
        
        if (isset($data['ids']) && !empty($data["hide"])) {
            $ids = $this->request->param('ids/a');
            $linkModel->where(['id' => ['in', $ids]])->update(['status' => 0]);
            $this->success("更新成功！");
        }
    }
    
    public function addabout()
    {
        if ($this->request->isPost()){
            $post = array_map('trim', $this->request->param());
            
            if (in_array('', $post) && !empty($post['smtpsecure'])) {
                $this->error("不能留空！");
            }
            $resl = cmf_set_option('about_option', $post);
            
            if ($resl){
                $this->success('保存成功！');
            }else {
                $this->error('保存失败！');
            }
        }
        
        $about = cmf_get_option('about_option');
        $this->assign($about);
        
        return $this->fetch();
    }

}

?>