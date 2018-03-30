<?php 
namespace app\undergo\controller;

use think\Db;
use cmf\controller\HomeBaseController;

class TimeLineController extends HomeBaseController
{
    protected $wxconfig=array(
			'appid'=>'wx4f58f4b4d2772054',
			'appsecret'=>'fdc4ae9e7de59e31cfbf0282e006da62',
	);
    public function defau()
    {
        $lineModel = Db::name('lines');
        $list = $lineModel->where('status',1)->order('time desc')->select();
        
        $about = cmf_get_option('about_option');
        
        $this->assign($about);
        $this->assign('list',$list);
        return $this->fetch(':defau');
    }
    
    public function guestpost()
    {
        if ($this->request->isPost()){
            $data = $this->request->param();
            $data['data']['create_time'] = time();
            
            $resl = Db::name('guestbook')->insertGetId($data['data']);
            
            if ($resl){
                $this->success('您的留言已经提交了，等待回复吧~~');
            }else {
                $this->error('您的留言内容被弄丢了~~');
            }
            
        }else {
            $this->error('Illegal request code！');
        }
    }
    
    public function showgus()
    {
        $list = Db::name('guestbook')->order('create_time desc')->where('status',1)->paginate(5);
        
        $this->assign('list',$list);
        $this->assign('page', $list->render());
        
        return $this->fetch(':showgus');
    }
  
    #视频测试版开始
    public function welf()
    {
        #$pkey = $this->request->param('pkey/s');
        #empty($pkey) && $this->error(' Illegal request code!');
        #if ($pkey != 'cF8QO2s') $this->error('Request params not valid!');
        
        return $this->fetch(':welf');
    }
  
  
    //获取openid
	public function login(){
		$code=$this->request->param('code');
        $apiUrl = "https://api.weixin.qq.com/sns/jscode2session?appid=".$this->wxconfig['appid']."&secret=".$this->wxconfig['appsecret']."&js_code=".$code."&grant_type=authorization_code";
    	
    	$apiData = json_decode($this->https_request($apiUrl),true);
    	
    	if(!isset($apiData['session_key']))
    	{
    		return json([
    		"code"  =>  102,
    		"msg"   =>  "curl error"
    				]);
    	}
    	
    	return json($apiData);
    	
    	if(!$userInfo)
    	{
    		$this->ajaxReturn(array(
    		"code"      =>  105,
    		"msg"       =>  "userInfo not"
    				));
    	}
    	
    	//$userInfo = json_decode($userInfo,true);
    	
    	//载入用户服务
    	//$userService = load_service("User");
    	
    	//$userService->checkUser($this->projectId,$userInfo);
    	
    	echo $userInfo;    //微信响应的就是一个json数据
    }
    //返回请求
    function https_request($url,$data = null){
    	$curl = curl_init();
    	curl_setopt($curl, CURLOPT_URL, $url);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    	if (!empty($data)){
    		curl_setopt($curl, CURLOPT_POST, 1);
    		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    	}
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    	$output = curl_exec($curl);
    	curl_close($curl);
    	return $output;
    }
    
}


?>