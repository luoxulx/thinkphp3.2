<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:kane < chengjin005@163.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use app\portal\model\PortalTagModel;
use cmf\controller\AdminBaseController;
use think\Db;

/**
 * Class AdminTagController 标签管理控制器
 * @package app\portal\controller
 */
class AdminTagController extends AdminBaseController
{
    /**
     * 文章标签管理
     * @adminMenu(
     *     'name'   => '文章标签',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章标签',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $portalTagModel = new PortalTagModel();
        
        $where = true;
        $key = $this->request->param('key/s');
        if ($key){
            $where = [];
            $where['name'] = ['like',"%$key%"];
            $this->assign('keyword',$key);
        }
        
        $tags           = $portalTagModel->where($where)->order('post_count desc')->paginate(15);

        $this->assign("arrStatus", $portalTagModel::$STATUS);
        $this->assign("tags", $tags);
        $this->assign('page', $tags->render());
        return $this->fetch();
    }

    /**
     * 添加文章标签
     * @adminMenu(
     *     'name'   => '添加文章标签',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加文章标签',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $portalTagModel = new PortalTagModel();
        $this->assign("arrStatus", $portalTagModel::$STATUS);
        return $this->fetch();
    }

    /**
     * 添加文章标签提交
     * @adminMenu(
     *     'name'   => '添加文章标签提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加文章标签提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {

        $arrData = $this->request->param();

        $portalTagModel = new PortalTagModel();
        $portalTagModel->isUpdate(false)->allowField(true)->save($arrData);

        $this->success(lang("SAVE_SUCCESS"));

    }

    /**
     * 更新文章标签状态
     * @adminMenu(
     *     'name'   => '更新标签状态',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '更新标签状态',
     *     'param'  => ''
     * )
     */
    public function upStatus()
    {
        $intId     = $this->request->param("id");
        $intStatus = $this->request->param("status");
        $intStatus = $intStatus ? 1 : 0;
        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }

        $portalTagModel = new PortalTagModel();
        $portalTagModel->isUpdate(true)->save(["status" => $intStatus], ["id" => $intId]);

        $this->success(lang("SAVE_SUCCESS"));

    }
    
    #lx_new
    public function upCounts()
    {
        $portalTagModel = new PortalTagModel();
        
        $allTags = $portalTagModel->all(function($query){
            $query->where('status',1)->field('id')->order('id asc');
        });
        $carr = [];
        foreach ($allTags as $k=>$v){
            $carr[$k]['id'] = $allTags[$k]['id'];
            $carr[$k]['post_count'] = Db::name('portal_tag_post')->where(['tag_id'=>$allTags[$k]['id'],'status'=>1])->count();
        }
        
        $resl = $portalTagModel->isUpdate()->saveAll($carr);
        
        if ($resl){
            $this->success('统计更新成功！');
            cache('index_tags',NULL);
        }else {
            $this->error('error!');
        }
    }
    
    public function upRecom()
    {
        $portalTagModel = new PortalTagModel();
        $data      = $this->request->param();
        
        if (isset($data['ids']) && !empty($data["yes"])) {
            $ids = $this->request->param('ids/a');
            $portalTagModel->where(['id' => ['in', $ids]])->update(['recommended' => 1]);
            $this->success("推荐成功！");
        }
        if (isset($data['ids']) && !empty($data["no"])) {
            $ids = $this->request->param('ids/a');
            $portalTagModel->where(['id' => ['in', $ids]])->update(['recommended' => 0]);
            $this->success("已取消推荐！");
        }
        
    }
    #lx-new-tags-select
    public function select()
    {
        $ids                 = $this->request->param('ids');
        
        
        
        $selectedIds         = explode(',', $ids);
        $portalTagModel = new PortalTagModel();
        
        $tags = $portalTagModel->all(function($query){
            $key = $this->request->param('key/s','');
            if ($key){
                $this->assign('keyword',$key);
                $where['name'] = ['like',"%$key%"];
            }
            $where['status'] = 1;
            $query->where($where)->field('id,name,post_count')->order('post_count desc');
        });
        
        
        $this->assign('selectedIds', $selectedIds);
        $this->assign('tags',$tags);
        return $this->fetch();
    }
    

    /**
     * 删除文章标签
     * @adminMenu(
     *     'name'   => '删除文章标签',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除文章标签',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $intId = $this->request->param("id", 0, 'intval');

        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }
        $portalTagModel = new PortalTagModel();

        $portalTagModel->where(['id' => $intId])->delete();
        Db::name('portal_tag_post')->where('tag_id', $intId)->delete();
        $this->success(lang("DELETE_SUCCESS"));
    }
}
