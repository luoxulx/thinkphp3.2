<?php 
namespace plugins\lxcomment\controller;

use cmf\controller\PluginBaseController;
use plugins\lxcomment\model\PluginLxcommentModel;

class IndexController extends PluginBaseController
{
    
    public function idnex()
    {
        $lxComMod = New PluginLxcommentModel();
    }
    
    public function firstFloor()
    {
        if ($this->request->isPost()){
            $data = $this->request->param('data/a');
            #$data['content'] = htmlpurifier_filter_extractstyleblocks_muteerrorhandler();
            $ipaddress = get_client_ip(0, true);
            if (!$ipaddress){
                $this->error('Illegal area operation!');
            }
            
            $data['parent_id'] = $data['sb_wang_jun_kai'];
            $data['to_who'] = $data['sb_wang_jun_kai_name'];
            
            unset($data['sb_wang_jun_kai']);
            unset($data['sb_wang_jun_kai_name']);
            
            $data['status'] = 0;
            $data['path'] = 0;
            $data['create_time'] = time();
            $data['ip_add'] = $ipaddress . '|' . lxGetIpLookup($ipaddress);
            
            $nowUserId = cmf_get_current_user_id();
            if (!$nowUserId){
                if (empty($data['full_name'])){
                    $data['full_name'] = '匿名用户';
                }
                $data['type'] = 0;#匿名用户
                $data['user_id'] = 0;
            }else {
                if (empty($data['full_name'])){
                    $data['full_name'] = cmf_get_current_user()['user_nickname'];
                }
                $data['type'] = 1;
                $data['user_id'] = $nowUserId;
            }
            
            $lxComMod = New PluginLxcommentModel();
            $resl = $this->validate($data, 'Lxcomment');
            
            if ($resl !== true) {
                $this->error($resl);
            }
            
            $resl = $lxComMod->firstFloorSave($data);
            
            if ($resl === false) {
                $this->error('系统异常！请稍后再评论！');
            }
            
            $this->success('评论已提交，等待管理员审核中...');
            
        }else {
            $this->error('Unlawful Request!');
        }
    }
    
    public function returnpLs()
    {
        if ($this->request->isPost()){
            $auth = $this->request->param('authcode/s');
            if (!$auth || $auth != 'ZXyCEDpedQb43Zli'){
                $this->error('Illegal request code!');
            }
            
            $param = cache('lxcomment_param');
            cache('lxcomment_param', NULL);
            $id = $param['object_id'];
            empty($id) && $this->error('The content of the cache has expired, please reload this page!');
            
            $newList = $this->backAllMorepls($id);
            
            if (count($newList) > 0){
                $this->success('评论列表渲染中...','',$newList);
            }else {
                $this->error('此文章暂无网友评论~');
            }
        }else {
            $this->error('Unlawful Request!');
        }
    }
    
    /**
     * 返回所有已通过的评论列表
     * @param int $id
     * @param number $parent_id
     * @return unknown array
     */
    private function backAllMorepls($id, $parent_id = 0)
    {
        $lxcomMod = New PluginLxcommentModel();
        $field = 'id,parent_id,create_time,full_name,to_who,email,content,more';
        
        $allList = $lxcomMod->where(['delete_time'=>0,'status'=>1,'object_id'=>$id,'parent_id'=>$parent_id])->order('create_time desc')->field($field)->select()->toArray();
        
        $newArr = [];
        if ($allList){
            foreach ($allList as $val){
                if ($val['parent_id'] == $parent_id){
                    $val['_child'] = $this->backAllMorepls($id, $val['id']);
                }
                $newArr[] = $val;
            }
        }
        return $newArr;
    }
}

?>