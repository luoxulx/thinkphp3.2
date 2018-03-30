<?php

namespace app\portal\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class AdminCommentController extends AdminBaseController
{
    
    public function saveCommonts()
    {
        if ($this->request->isPost()){
            $data = $_POST['data'];
            
            $resl['user_id'] = $data['comments'][0]['user']['userid'];
            $resl['to_user_id'] = '';
            $resl['object_id'] = $data['sourceid'];
            $resl['create_time'] = $data['comments'][0]['ctime'];
            $resl['delete_time'] = 0;
            $resl['status'] = 0;
            $resl['type'] = 2;#第三方
            $resl['full_name'] = $data['comments'][0]['user']['nickname'];
            $resl['email'] = '';
            $resl['url'] = $data['url'];
            $resl['content'] = $data['comments'][0]['content'];
            $resl['more'] = json_encode($data['comments']);
            
            $res = Db::name('comment')->insertGetId($resl);
            if ($res){
                $this->success('推送内容已保存！');
            }else {
                $this->error('推送内容保存失败！');
            }
        }else {
            
            $this->error('Illegal request code');
        }
        
    }
    
    public function index()
    {
        $post_id = $this->request->param('post_id');
        if (empty($post_id)) $this->error('参数错误！');
        
        $where = [];
        $where['delete_time'] = 0;
        $where['object_id'] = $post_id;
        
        $comments = Db::name('comment')
        ->where($where)
        ->order("id DESC")
        ->paginate(10);
        $page = $comments->render();
        $this->assign("page", $page);
        $this->assign("comments", $comments);
        return $this->fetch();
    }

}