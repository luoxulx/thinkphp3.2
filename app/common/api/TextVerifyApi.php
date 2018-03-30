<?php
namespace app\common\api;

class TextVerifyApi 
{
    private static $SECRETID = '';/** 产品密钥ID，产品标识 */
    private static $SECRETKEY = '';/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
    private static $BUSINESSID = '';/** 业务ID */
    private static $API_URL = '';/** 反垃圾云服务文本結果查詢接口地址 */
    private static $VERSION = '1';
    private static $INTERNAL_STRING_CHARSET = 'auto';/** php内部使用的字符串编码 */
    private static $API_TIMEOUT = '10';
    
    #检查朱程程
    public function main(){
        
    }
    
    public function check($params){
        $params["secretId"] = self::$SECRETID;
        $params["businessId"] = self::$BUSINESSID;
        $params["version"] = self::$VERSION;
        $params["timestamp"] = sprintf("%d", round(microtime(true)*1000));// time in milliseconds
        $params["nonce"] = sprintf("%d", rand()); // random int
        
        $params = toUtf8($params);
        $params["signature"] = gen_signature(self::$SECRETKEY, $params);
        // var_dump($params);
        
        $options = array(
                'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'timeout' => self::$API_TIMEOUT, // read timeout in seconds
                        'content' => http_build_query($params),
                ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents(self::$API_URL, false, $context);
        if($result === FALSE){
            return array("code"=>500, "msg"=>"file_get_contents failed.");
        }else{
            return json_decode($result, true);
        }
    }
    
    protected function gen_signature($secretKey, $params){
        ksort($params);
        $buff="";
        foreach($params as $key=>$value){
            if($value !== null) {
                $buff .=$key;
                $buff .=$value;
            }
        }
        $buff .= $secretKey;
        return md5($buff);
    }
    
    protected function toUtf8($params){
        $utf8s = array();
        foreach ($params as $key => $value) {
            $utf8s[$key] = is_string($value) ? mb_convert_encoding($value, "utf8", self::$INTERNAL_STRING_CHARSET) : $value;
        }
        return $utf8s;
    }
    
}