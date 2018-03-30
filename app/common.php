<?php
use OSS\OssClient;
use OSS\Model\PrefixInfo;
use OSS\Model\ObjectInfo;
use OSS\Model\BucketInfo;
use OSS\Model\ObjectListInfo;
use OSS\Core\OssException;
use think\Db;



#2017-11-28 lx 新增common函数文件
#公共部分可提供给多个模块统一调用
/**
 * 实例化阿里云OSS
 * @return object 实例化得到的对象
 * @return 此步作为共用对象，
 */
function newOss()
{
    $config = config('aliyun_oss');
    if (!$config) return false;
    $oss = new OssClient($config['accessKeyId'], $config['accessKeySecret'], $config['endpoint']);
    return $oss;
}

/**
 * 解析 prefixInfo 类 
 * @param PrefixInfo $prefixInfo
 * @return NULL[]
 */
function prefixInfoParse(PrefixInfo $prefixInfo)
{
    return [
            'name' => getPaths($prefixInfo->getPrefix()),
    ];
}

/**
 * 解析 objectInfo 类 
 * @param ObjectInfo $objectInfo
 * @return string[]|number[]
 */
 function objectInfoParse(ObjectInfo $objectInfo)
 {
     $config = config('aliyun_oss');
     return [
            'name'      => $objectInfo->getKey(),
            'size'      => $objectInfo->getSize(),
            'update_at' => getLocalTime($objectInfo->getLastModified()),
            'type'      => $objectInfo->getType(),
            'download'  => getSignedUrlForFile(newOss(), $config['bucketName'], $objectInfo->getKey())
     ];
}

function str_n_pos($str,$find,$n=2){
    for ($i=1;$i<=$n;$i++){
        $pos = strpos($str, $find);
        $str = substr($str, $pos+1);
        $pos_val = $pos+$pos_val+1;
    }
    return $pos_val-1;
}

/**
 * 去除路径多余 delimiter
 * @param unknown $path
 * @return string
 */
function getPaths($path)
{
    if ($path == '/'){
        $path = '';
    }else {
        if (substr($path, 0, 1) == '/'){
            $path = substr($path, 1);
            if (substr($path, -1, 1) != '/'){
                $path += '/';
            }
        }
    }
    return $path;
}

/**
 * Bucket信息，ListBuckets接口返回数据
 * @param BucketInfo $bucketInfo
 * @return string[]
 */
function bucketInfoParse(BucketInfo $bucketInfo)
{
    return [
            'location' => $bucketInfo->getLocation(),#得到bucket所在的region
            'name'     => $bucketInfo->getName(),
            'createtime' => $bucketInfo->getCreateDate(),
    ];
}

/**
 * 解析 ObjectListInfo 类 
 * @param ObjectListInfo $objectListInfo
 * @return \OSS\Model\ObjectInfo[][]|\OSS\Model\PrefixInfo[][]|string[]
 */
function objectListInfoParse(ObjectListInfo $objectListInfo)
{
    return [
            'objectList' => $objectListInfo->getObjectList(),#return数组,返回ListObjects接口返回数据中的ObjectInfo列表
            'prefixList' => $objectListInfo->getPrefixList(),#return数组,返回数据中的PrefixInfo列表
            'prefix'     => $objectListInfo->getPrefix(),
    ];
}

/**
 * 生成GetObject的签名url
 * @param unknown $ossClient
 * @param unknown $bucket
 * @param 文件路径，eg：test/test2/filename.txt $object
 * @param 签名URL过期时间，默认3600秒  number $timeout
 * @return void|unknown
 */
function getSignedUrlForFile($ossClient, $bucket, $object, $timeout = 3600)
{
    try {
        $signedUrl = $ossClient->signUrl($bucket, $object, $timeout);
    } catch (\OSS\Core\OssException $e){
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    return $signedUrl;
}

/**
 * 删除单个object不可恢复
 * @param unknown $bucket
 * @param unknown $object
 * @return boolean
 */
function delOneObject($bucket, $object)
{
    try{
        newOss()->deleteObject($bucket, $object);
    } catch(OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return false;
    }
    return true;
}






/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr")){
        $slice = mb_substr($str, $start, $length, $charset);
        $len=mb_strlen($str,$charset);
    }
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
        $len= count($match[0]);
    }
    
    if($len>$length){
        return $suffix ? $slice.'…' : $slice;
    }
    return $suffix ? $slice.'' : $slice;
}

function getLocalTime($stime)
{
    return date('Y-m-d H:i:s', strtotime($stime));
}

#2018-02-20new
/**
 * ip 地址
 * @param string $ip
 * @return boolean|string|mixed
 */
function lxGetIpLookup($ip = ''){
    if(empty($ip)) return false;
    $ipd = '';
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
    if(empty($res)){ return false; }
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if(!isset($jsonMatches[0])){ return false; }
    $json = json_decode($jsonMatches[0], true);
    if(isset($json['ret']) && $json['ret'] == 1){
        $json['ip'] = $ip;
        unset($json['ret']);
    }else{
        return false;
    }
    $ipd = $json['country'].'-'.$json['province'].'-'.$json['city'];
    if ($json['district']){
        $ipd = $ipd.'-'.$json['district'];
    }
    if ($json['isp']) {
        $ipd = $ipd.'&'.$json['isp'];
    }
    return $ipd;
}

/**
 * 生成颜色码
 * @return string
 */
function lxReturnRandColor(){
    $colors = array();
    for($i = 0;$i<6;$i++){
        $colors[] = dechex(rand(0,15));
    }
    return implode('',$colors);
}

?>