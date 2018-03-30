<?php
namespace app\admin\controller;

use think\Db;
use cmf\controller\AdminBaseController;
use app\admin\model\ArticleModel;
use app\admin\model\ArticleCateModel;

class ArticleController extends AdminBaseController
{
    protected $ArticleMod;
    protected $ArticleCateMod;
    public function _initialize() {
        parent::_initialize();
        $this->ArticleMod = new ArticleModel();
        $this->ArticleCateMod = new ArticleCateModel();
    }
    public function index()
    {
        /* $param = $this->request->param();
        $categoryId = $this->request->param('category', 0, 'intval');
        $data = $this->ArticleMod->getArticleLists($param);
        $data->appends($param);
        $categoryTree = $this->ArticleCateMod->articleCateTree($categoryId);
        
        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('articles', $data->items());
        $this->assign('category_tree', $categoryTree);
        $this->assign('category', $categoryId);
        $this->assign('page', $data->render()); */
        $this->assign('articles',1);
        return $this->fetch();
    }
    
    public function add(){
        
        return $this->fetch();
    }
    
    public function main(){
        $this->treeData="[" . $this->display_tree(0) . "]";
        $this->assign('treeData',$this->treeData);
        return $this->fetch();
    }
    private function display_tree($classid) {
        $request= \think\Request::instance();
        $Mod = db('article_cate');
        $data['parent_id'] = $classid;
        $data['status'] = 1;
        $result = $Mod->where($data)->order('list_order asc,id asc')->select();
        
        $retStr = "";
        foreach($result as $row){
            $retStr .= "{name:\"".$row['name'] . "\"";
            $url = url('index',array('category'=>$row['id'],'admin_temp'=>$row['admin_temp']));
            $retStr .= ",\"url\":\"".$url."\", \"target\":\"".$request->controller()."_iframe_content\",\"click\":\"changeUrl('#')\"";
            
            if($row['parent_id']==0)
            {
                $retStr .=",open:true";
            }
            if($this->display_tree($row['id']) != "")
            {
                $retStr .= ",children:[";
                $retStr .= $this->display_tree($row['id']);
                $retStr .= "]";
            }
            $retStr .= "},";
        }
        return  $retStr;
    }
    
}