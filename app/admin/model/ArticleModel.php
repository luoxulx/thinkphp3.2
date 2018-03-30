<?php
namespace app\admin\model;


use think\Model;

class ArticleModel extends Model{
    protected $type = [
            'more' => 'array',
    ];
    
    
    
    public function getArticleLists($filter, $isPage = false)
    {
        return $this->has('ArticleCate');
        $where = ['delete_time' => null];
        $join = [
                ['__USER__ u', 'a.user_id = u.id']
        ];
        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';
        $category = empty($filter['category']) ? 0 : intval($filter['category']);
        #TODO
        $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
        $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['a.add_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        } else {
            if (!empty($startTime)) {
                $where['a.add_time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['a.add_time'] = ['<= time', $endTime];
            }
        }
        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.title'] = ['like', "%$keyword%"];
        }
        if ($isPage) {
            $where['a.post_type'] = 2;
        } else {
            $where['a.post_type'] = 1;
        }
       
        $articles = $this->alias('a')->field($field)->join($join)->where($where)->order('add_time DESC')->paginate(10);
        
        return $articles;var_dump($articles);die;
    }
}