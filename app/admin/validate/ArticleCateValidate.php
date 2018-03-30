<?php
namespace app\admin\validate;

use think\Validate;
use think\Db;

class ArticleCateValidate extends Validate{
    protected $rule = [
            'name'       => 'require|max:5',
            'url'        => 'require|max:50',
            'parent_id'  => 'checkParentId',
            
    ];
    protected $message = [
            'name.require'       => '请填写分类名！',
            'name.max'     => '名称最多不能超过5个字符',
            'url.require'        => '请填写分类链接！',
            'url.max'     => '分类链接最多50个字符！',
            'parent_id'          => '分类已超过了4级！',
            'name.unique'      => '同样的分类名已经存在!',
    ];
    protected $scene = [
            #'add'  => ['name', 'url', 'parent_id'],
            #'edit' => ['name', 'url', 'id', 'parent_id'],
            
    ];
    // 自定义验证规则
    protected function checkParentId($value)
    {
        $find = Db::name('article_cate')->where(["id" => $value])->value('parent_id');
        
        if ($find) {
            $find2 = Db::name('article_cate')->where(["id" => $find])->value('parent_id');
            if ($find2) {
                $find3 = Db::name('article_cate')->where(["id" => $find2])->value('parent_id');
                if ($find3) {
                    return false;
                }
            }
        }
        return true;
    }
    
}