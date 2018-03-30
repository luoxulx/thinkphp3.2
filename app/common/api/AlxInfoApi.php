<?php
namespace app\common\api;

class AlxInfoApi{
    protected static $info = [];
    
    public function scInfo(){
        
        #抑制所有错误
        error_reporting(0);
        @header("content-Type: text/html; charset=utf-8");
        ob_start();
        
        $GLOBALS['_titles'] = 'LKBLX';
        define('HTTP_HOST', preg_replace('~^www\.~i', '', $_SERVER['HTTP_HOST']));
        $time_start = $this->microtime_float();
        
        
        
        
        return self::$info;
    }
    /**
     * 静态及时
     * @return number
     */
    protected  function microtime_float(){
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        return $mtime[1] + $mtime[0];
    }
    protected  function memory_usage(){
        $memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';
        return $memory;
    }
    protected function valid_email($str)
    {
        return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
    }
    protected  function formatsize($size){
        $danwei=array(' B ',' K ',' M ',' G ',' T ');
        $allsize=array();
        $i=0;
        
        for($i = 0; $i <4; $i++)
        {
            if(floor($size/pow(1024,$i))==0){break;}
        }
        
        for($l = $i-1; $l >=0; $l--)
        {
            $allsize1[$l]=floor($size/pow(1024,$l));
            $allsize[$l]=$allsize1[$l]-$allsize1[$l+1]*1024;
        }
        
        $len=count($allsize);
        
        for($j = $len-1; $j >=0; $j--)
        {
            $strlen = 4-strlen($allsize[$j]);
            if($strlen==1)
                $allsize[$j] = "<font color='#FFFFFF'>0</font>".$allsize[$j];
                elseif($strlen==2)
                $allsize[$j] = "<font color='#FFFFFF'>00</font>".$allsize[$j];
                elseif($strlen==3)
                $allsize[$j] = "<font color='#FFFFFF'>000</font>".$allsize[$j];
                
                $fsize=$fsize.$allsize[$j].$danwei[$j];
        }
        return $fsize;
    }
    
    //检测PHP设置参数
    protected function show($varName)
    {
        switch($result = get_cfg_var($varName))
        {
            case 0:
                return '<font color="red">×</font>';
                break;
                
            case 1:
                return '<font color="green">√</font>';
                break;
                
            default:
                return $result;
                break;
        }
    }
    
    
}