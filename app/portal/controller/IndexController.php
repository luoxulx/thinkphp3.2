<?php
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\PortalTagModel;

class IndexController extends HomeBaseController
{
    public function index()
    {
        
        $tags = cache('index_tags');
        if (!$tags){
            $tags = [];
            $tagMod = new PortalTagModel();
            $tags = $tagMod->all(function($query){
                $query->field('name,post_count')->where(['status'=>1]);
            });
            
            cache('index_tags',$tags);
        }
        $this->assign('tags',$tags);
        
        $code = $this->request->param('code/s');
        if ($code){
            $this->redirect(cmf_plugin_url('Qqlogin://Index/index').'?code='.$code);
        }else {
            return $this->fetch(':index');
        }
    }
    
    /**
     * lx-tags return
     */
    public function backTags()
    {
        if ($this->request->isPost()){
            $auth = $this->request->post('auth/s');
            if (!$auth){
                $this->error('Illegal request code');
            }
            $tags = cache('index_tags');
            if (!$tags){
                $tags = [];
                $tagMod = new PortalTagModel();
                $tags = $tagMod->all(function($query){
                    $query->field('name,post_count')->where(['status'=>1]);
                });
                    cache('index_tags',$tags);
            }
            
            $this->success('OK','',$tags);
        }else {
            $this->error('Illegal request code');
        }
    }
}