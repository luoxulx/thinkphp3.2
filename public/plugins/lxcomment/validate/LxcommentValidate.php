<?php 
namespace plugins\lxcomment\validate;

use think\Validate;

class LxcommentValidate extends Validate
{
    protected $rule = [
            'content'  => 'require',
    ];
    protected $message = [
            'content.require' => '评论内容不能为空',
    ];
}


?>