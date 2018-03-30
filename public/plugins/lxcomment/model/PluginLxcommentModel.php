<?php
namespace plugins\lxcomment\model;//Lxomment插件英文名，改成你的插件英文就行了
use think\Model;

//Lxomment插件英文名，改成你的插件英文就行了,插件数据表最好加个plugin前缀再加表名,这个类就是对应“表前缀+plugin_lxomment”表
class PluginLxcommentModel extends Model
{
    protected $type = [
            'more' => 'array',
    ];
    
    /**
     * 一级评论内容提交
     * @param unknown $data
     * @return boolean
     */
    public function firstFloorSave($data)
    {
        static $resl = true;
        self::startTrans();
        
        try {
            if (!empty($data['more']['avatar'])) {
                $data['more']['avatar'] = cmf_asset_relative_url($data['more']['avatar']);
            }
            $this->allowField(true)->save($data);
            
            $id = $this->id;
            
            if (empty($data['parent_id'])) {
                $this->where( ['id' => $id])->update(['path' => '0-' . $id]);
            } else {
                $parentPath = $this->where('id', intval($data['parent_id']))->value('path');
                $this->where( ['id' => $id])->update(['path' => "$parentPath-$id"]);
            }
            self::commit();
            
        }catch (\Exception $e){
            self::rollback();
            $resl = false;
        }
        
        return $resl;
    }
    
}