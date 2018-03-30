<?php 
namespace plugins\video\model;

use think\Model;

class PluginVideoModel extends Model
{
    protected $type = [
            'more' => 'array',
    ];
    
    #TODO
    public function relists()
    {
        
        $list = [];
        $list = $this->where(['status'=>1])->order('add_time desc')->field('id,vi_name,vi_img,vi_desc')->paginate(12);
        
        return $list;
    }
    
}









?>