<?php
/**
 * ArticleCate文章分类管理
 */
namespace app\admin\controller;
use cmf\controller\AdminBaseController;
use app\admin\model\ArticleCateModel;
use think\Db;
use think\Model;

class ArticleCateController extends AdminBaseController
{

    protected $ArticleCateMod;

    /*
     * protected static function init()
     * {
     * //TODO:自定义的初始化
     * $this->ArticleCateMod = new ArticleCateModel();
     * }
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->ArticleCateMod = new ArticleCateModel();
    }

    public function index()
    {
        $categoryTree = $this->ArticleCateMod->articleCateTableTree();
        $this->assign('category_tree', $categoryTree);
        return $this->fetch();
    }

    public function add()
    {
        $parentId = $this->request->param('parent', 0, 'intval');
        $categoriesTree = $this->ArticleCateMod->articleCateTree($parentId);
        $this->assign('categories_tree', $categoriesTree);
        return $this->fetch();
    }

    public function addPost()
    {
        $data = $this->request->param();
        $result = $this->validate($data, 'ArticleCate');
        if ($result !== true)
            $this->error($result);
        $result = $this->ArticleCateMod->addArticleCate($data);
        if ($result === false)
            $this->error('添加失败！');
        else
            $this->success('添加成功！', url('ArticleCate/index'));
    }
    public function addAll(){
        if ($this->request->post()){
            $parentid = $this->request->param('parent_id');
            $name = $this->request->param('name');
            $temp = explode("\n",$name);
            foreach($temp as $k=>$v){
                $sort_order = $this->ArticleCateMod->max('list_order');
                preg_match('/^-+/', $v,$input);
                $coun = substr_count($input[0],'-');
                $vt = preg_replace('/^-+/', '$1', $v);
                $vv = str_replace("\r","",$vt);
                
                if(0==$coun){
                    if($parentid){
                        $fath = $this->ArticleCateMod->find($parentid);
                        $data['parent_id'] = $parentid;
                        $data['parentspath'] = $fath['parentspath'].$parentid."-";
                    }
                    else{
                        $data['parent_id'] = 0;
                        $data['parentspath'] = "0-";
                    }
                    $data['name'] = $vv;
                    $data['list_order'] = $sort_order+1;
                    $data['status'] = 1;
                    $re = $this->ArticleCateMod->add($data);
                    $id_arrs[$coun][] = $re;
                }else{
                    $pid = $id_arrs[$coun-1][count($id_arrs[$coun-1])-1];
                    $fath = $this->ArticleCateMod->find($pid);
                    $data['name'] = $vv;
                    $data['parent_id'] = $pid;
                    $data['parentspath'] = $fath['parentspath'].$pid."-";
                    $data['list_order'] = $sort_order+1;
                    $data['status'] = 1;
                    $re = $this->ArticleCateMod->add($data);
                    $id_arrs[$coun][] = $re;
                }
            }
            $this->success('添加成功！',url('ArticleCate/index'));
        }
        $parentId = $this->request->param('parent_id', 0, 'intval');
        $categoriesTree = $this->ArticleCateMod->articleCateTree($parentId);
        $this->assign('categories_tree', $categoriesTree);
        return $this->fetch();
    }
    public function update_num(){
        $ids = $this->request->param('ids');
        $id_strs = implode(',', $ids);
        $Model = new Model();
        
        $sql = "update ".db('ArticleCate')." a set a.record_nums = (select count(*) from ".db('Article')." b where a.id = b.cate_id) where id in ($id_strs)";
        $Model->execute($sql);
        $this->success("操作成功!");
    }

    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            $category = ArticleCateModel::get($id)->toArray();
            $categoriesTree      = $this->ArticleCateMod->articleCateTree($category['parent_id'], $id);
            $this->assign($category);
            $this->assign('categories_tree', $categoriesTree);
            
            return $this->fetch();
        } else {
            $this->error('参数错误！');
        }
    }

    public function editPost()
    {
        $data = $this->request->param();
        $result = $this->validate($data, 'ArticleCate');
        $data['update_time'] = date('Y-m-d H:i:s',time());
        if ($result !== true)
            $this->error($result);
        
        $result = $this->ArticleCateMod->editArticleCate($data);
        if ($result === false)
            $this->error('修改保存失败！');
        
        $this->success('修改保存成功！', url('ArticleCate/index'));
        
    }
    public function select()
    {
        $ids                 = $this->request->param('ids');
        $selectedIds         = explode(',', $ids);
        
        $tpl = <<<tpl
<tr class='data-item-tr'>
    <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
               value='\$id' data-name='\$name' \$checked>
    </td>
    <td>\$id</td>
    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
</tr>
tpl;
        
        $categoryTree = $this->ArticleCateMod->articleCateTableTree($selectedIds, $tpl);
        
        $where      = ['status' => 1];
        $categories = $this->ArticleCateMod->where($where)->select();
        
        $this->assign('categories', $categories);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('categories_tree', $categoryTree);
        return $this->fetch();
    }
    
    public function listOrder()
    {
        parent::listOrders(Db::name('article_cate'));
        $this->success("排序更新成功！", '');
    }

    public function delete()
    {
        $id  = $this->request->param('id');
        //获取删除的内容
        $findCategory = $this->ArticleCateMod->where('id', $id)->find();
        
        if (empty($findCategory)) {
            $this->error('分类不存在!');
        }
        //判断此分类有无子分类（不算被删除的子分类）
        $categoryChildrenCount = $this->ArticleCateMod->where(['parent_id' => $id,'name' => ['neq','']])->count();
        
        if ($categoryChildrenCount > 0) {
            $this->error('此分类下有子类，无法删除!');
        }
        
        $categoryPostCount = Db::name('article')->where('cate_id', $id)->count();
        
        if ($categoryPostCount > 0) {
            $this->error('此分类有内容，无法删除!');
        }
        
        $data   = [
                'object_id'   => $findCategory['id'],
                'create_time' => time(),
                'table_name'  => 'article_cate',
                'name'        => $findCategory['name']
        ];
        $result = $this->ArticleCateMod->where('id', $id)->update(['status'=>0, 'delete_time' => date('Y-m-d H:i:s',time())]);
        if ($result) {
            Db::name('recycleBin')->insert($data);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败！');
        }
    }

    public function status(){
        
    }
    
    
}
