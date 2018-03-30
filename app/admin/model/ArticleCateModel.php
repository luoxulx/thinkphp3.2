<?php

/* * 
 * 分类模型
 */
namespace app\admin\model;

use think\Model;
use tree\Tree;
class ArticleCateModel extends Model {
    /* // 设置当前模型对应的完整数据表名称
    protected $table = 'ce_article_cate';
    // 设置当前模型的数据库连接
    protected $connection = [
            // 数据库类型
            'type'        => 'mysql',
            // 服务器地址
            'hostname'    => '127.0.0.1',
            // 数据库名
            'database'    => '_la-extend',
            // 数据库用户名
            'username'    => 'root',
            // 数据库密码
            'password'    => '111111',
            // 数据库编码默认采用utf8
            'charset'     => 'utf8',
            // 数据库表前缀
            'prefix'      => 'ce_',
            // 数据库调试模式
            'debug'       => false,
    ]; */
    protected $type = [
            'more' => 'array',
    ];
    
    //自定义初始化
    /* protected static function init()
    {
        //TODO:自定义的初始化
    } */
    /**
     * 生成分类 select树形结构
     * @param int $selectId 需要选中的分类 id
     * @param int $currentCid 需要隐藏的分类 id
     * @return string
     */
    public function articleCateTree($selectId = 0, $currentCid = 0)
    {
        $where = ['delete_time' => null];
        if (!empty($currentCid)) {
            $where['id'] = ['neq', $currentCid];
        }
        $categories = $this->order("list_order ASC")->where($where)->select()->toArray();
        
        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';
        
        $newCategories = [];
        foreach ($categories as $item) {
            $item['selected'] = $selectId == $item['id'] ? "selected" : "";
            
            array_push($newCategories, $item);
        }
        
        $tree->init($newCategories);
        $str     = '<option value=\"{$id}\" {$selected}>{$spacer}{$name}</option>';
        $treeStr = $tree->getTree(0, $str);
        
        return $treeStr;
    }
    
    public function articleCateTableTree($currentIds = 0, $tpl = ''){
        
        $where = ['delete_time' => null];
        //        if (!empty($currentCid)) {
        //            $where['id'] = ['neq', $currentCid];
        //        }
        $categories = $this->order("list_order ASC")->where($where)->select()->toArray();
        
        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';
        
        if (!is_array($currentIds)) {
            $currentIds = [$currentIds];
        }
        
        $newCategories = [];
        foreach ($categories as $item) {
            $item['checked'] = in_array($item['id'], $currentIds) ? "checked" : "";
            $item['url']     = cmf_url('Admin/ArticleCate/edit', ['id' => $item['id']]);
            $item['status'] = $item['status'] ? 'fa-check' : 'fa-close';
            $item['str_action'] = '<a href="' . url("ArticleCate/add", ["parent" => $item['id']]) . '">添加子分类</a>  <a href="' . url("ArticleCate/edit", ["id" => $item['id']]) . '">' . lang('EDIT') . '</a>  <a class="js-ajax-delete" href="' . url("ArticleCate/delete", ["id" => $item['id']]) . '">' . lang('DELETE') . '</a> ';
            array_push($newCategories, $item);
        }
        
        $tree->init($newCategories);
        
        if (empty($tpl)) {
            $tpl = "<tr>
                        <td><input name='list_orders[\$id]' type='text' size='3' value='\$list_order' class='input-order'></td>
                        <td>\$id</td>
                        <td>\$spacer <a href='\$url'>\$name</a></td>
                        <td>\$abst</td>
                        <td>\$record_nums</td>
                        <td>\$parentspath</td>
                        <td style='text-align:center;'><i class='fa \$status'></i></td>
                        <td>\$str_action</td>
                    </tr>";
        }
        $treeStr = $tree->getTree(0, $tpl);
        
        return $treeStr;
    }
    
    public function addArticleCate($data)
    {
        $result = true;
        self::startTrans();
        try {
            if (!empty($data['more']['img'])) {
                $data['more']['img'] = cmf_asset_relative_url($data['more']['img']);
            }
            $this->allowField(true)->save($data);
            $id = $this->id;
            if (empty($data['parent_id'])) {
                $this->where( ['id' => $id])->update(['parentspath' => '0-' . $id]);
            } else {
                $parentPath = $this->where('id', intval($data['parent_id']))->value('parentspath');
                $this->where( ['id' => $id])->update(['parentspath' => "$parentPath-$id"]);
            }
            self::commit();
        } catch (\Exception $e) {
            self::rollback();
            $result = false;
        }
        
        return $result;
    }
    
    public function editArticleCate($data)
    {
        $result = true;
        $id          = intval($data['id']);
        $parentId    = intval($data['parent_id']);
        $oldCategory = $this->where('id', $id)->find();
        
        if (empty($parentId)) {
            $newPath = '0-' . $id;
        } else {
            $parentPath = $this->where('id', intval($data['parent_id']))->value('parentspath');
            if ($parentPath === false) {
                $newPath = false;
            } else {
                $newPath = "$parentPath-$id";
            }
        }
        
        if (empty($oldCategory) || empty($newPath)) {
            $result = false;
        } else {
            $data['path'] = $newPath;
            if (!empty($data['more']['img'])) {
                $data['more']['img'] = cmf_asset_relative_url($data['more']['img']);
            }
            $this->isUpdate(true)->allowField(true)->save($data, ['id' => $id]);
            
            $children = $this->field('id,parentspath')->where('parentspath', 'like', "%-$id-%")->select();
            
            if (!empty($children)) {
                foreach ($children as $child) {
                    $childPath = str_replace($oldCategory['parentspath'] . '-', $newPath . '-', $child['parentspath']);
                    $this->isUpdate(true)->save(['parentspath' => $childPath], ['id' => $child['id']]);
                }
            }
        }
        return $result;
    }
    
}