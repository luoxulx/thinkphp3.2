<?php
namespace app\admin\validate;

use think\Validate;
use think\Db;

class ArticleValidate extends Validate{
    protected $rule = [
            'title'       => 'require|max:128',
            'cate_id'     => 'require',
    ];
    protected $message = [
            'title.require'       => '请填写内容标题！',
            'title.max'     => '标题最多不能超过128个字符',
            'cate_id.require'       => '请选择分类！',
    ];
    protected $scene = [
            #'add'  => ['name', 'url', 'parent_id'],
            #'edit' => ['name', 'url', 'id', 'parent_id'],
            
    ];
    // 自定义验证规则
    protected function checkParentId($value)
    {
        
        return true;
    }
    
}