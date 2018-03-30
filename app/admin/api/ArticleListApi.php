<?php
namespace app\admin\api;

use app\admin\model\ArticleModel;

class ArticleListApi {
    //资讯列表
    public function getArticleList()
    {
        $where = [
                'a.add_time' => ['neq', null],
                'a.delete_time' => null
        ];
        
        $join = [
                ['__USER__ u', 'a.user_id = u.id']
        ];
        
        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';
        
        $category = empty($filter['category']) ? 0 : intval($filter['category']);
        if (!empty($category)) {
            $where['b.category_id'] = ['eq', $category];
            array_push($join, [
                    '__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id'
            ]);
            $field = 'a.*,b.id AS post_category_id,b.list_order,b.category_id,u.user_login,u.user_nickname,u.user_email';
        }
        
        $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
        $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['a.published_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        } else {
            if (!empty($startTime)) {
                $where['a.published_time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['a.published_time'] = ['<= time', $endTime];
            }
        }
        
        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.post_title'] = ['like', "%$keyword%"];
        }
        
        if ($isPage) {
            $where['a.post_type'] = 2;
        } else {
            $where['a.post_type'] = 1;
        }
        
        $portalPostModel = new PortalPostModel();
        $articles        = $portalPostModel->alias('a')->field($field)
        ->join($join)
        ->where($where)
        ->order('update_time', 'DESC')
        ->paginate(10);
        
        return $articles;
    }
}