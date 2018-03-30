<?php
namespace plugins\guest\controller; //Demo插件英文名，改成你的插件英文就行了

use cmf\controller\PluginBaseController;
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
        $list = Db::name('guestbook')->order('list_order asc')->where(array('create_time'=>['neq',0]))->paginate(15);
        
        $this->assign('list',$list);
        $this->assign('page', $list->render());
        
        return $this->fetch('/admin_index');
    }
    
    function detail()
    {
        if ($this->request->isPost()){
            $re_info = $this->request->param();
            $data['re_info'] = $re_info['data']['re_info'];
            $data['id'] = $re_info['data']['id'];
            $is_re = $re_info['data']['is_re'];#是否邮件通知  已回复
            $email = $re_info['data']['email'];
            
            $config = $this->getPlugin('guest')->getConfig();
            
            if ($is_re){
                if ($config['subject'] && $config['infos']){
                    $resl = Db::name('guestbook')->update($data);
                    cmf_send_email($email,$config['subject'],$config['infos']);
                    
                    if ($resl){
                        $this->success('留言回复成功！且邮件通知已发送！');
                    }else {
                        $this->error('回复访客留言失败了~~');
                    }
                    
                }else {
                    $this->error('没有配置邮件发送主题及内容！请先配置！');
                }
            }else {
                $resl = Db::name('guestbook')->update($data);
                if ($resl){
                    $this->success('留言回复成功！访客没有勾选接收邮件通知！未发送邮件');
                }else {
                    $this->error('回复访客留言失败了~~');
                }
            }
            unset($data);
        }
        
        $id = $this->request->param('id/n');
        if (!$id) $this->error('参数错误！');
        
        $data = Db::name('guestbook')->where('id',$id)->find();
        $this->assign('data',$data);
        
        return $this->fetch('/admin_detail');
    }
    
    function delete()
    {
        $id = $this->request->param('id/n');
        if (!$id) $this->error('参数错误！');
        $resl = Db::name('guestbook')->delete($id);
        if ($resl){
            $this->success('此留言信息已删除！');
        }else {
            $this->error('删除失败！');
        }
    }
    
    public function listOrder()
    {
        $linkModel = Db::name('guestbook');
        parent::listOrders($linkModel);
        $this->success("排序更新成功！");
    }
    
    public function toggle()
    {
        $data      = $this->request->param();
        $linkModel = Db::name('guestbook');
        
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

}
