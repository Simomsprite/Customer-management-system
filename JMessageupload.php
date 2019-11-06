<?php
/**
 * Created by PhpStorm.
 * User: Super
 * Date: 2019-09-18
 * Time: 10:41
 */


class JMessageupload
{
    private $appkey;
    private $masterkey;
    private $url;

    public function __construct()
    {
        $this->appkey = 'efbdad17a9be59ad08af28a0';
        $this->masterkey = 'a66414bc1ab0274b7a4535a7';
        $this->url = 'https://api.im.jpush.cn';
        if (empty ($this->appkey) || empty ($this->masterkey)) {
            return false;
        }
    }


    /**
     * @param $type
     * @param $path
     * @return mixed
     * 文件上传
     */
    public function upload($type, $path) {
        $uri = $this->url.'/v1/resource'. '?' . http_build_query(array('type' => $type));
        $header=array(
            "Connection"=>"Keep-Alive",
            "Content-Type"=>"multipart/form-data"
        );
        $body = array('filename' => $path);
        $response = $this->postCurl($uri,$body,$header,"POST");
        return $response;
    }




    /**
 * 获取HTTP HEADER
 */
private function getHttpAuthHeader($header = array())
{
    $basic = base64_encode($this->appkey . ":" . $this->masterkey);
    $header_array = array();
    $header_array[] = "Authorization: Basic " . $basic;
    //$header_array[] = "Content-Type: application/json;charset=utf-8";
    $header_array[] = "Content-Type: multipart/form-data";     //上传文件的时候使用
    $header_array[] = "Connection: Keep-Alive";
    if (!empty($header)) {
        return array_merge($header_array, $header);
    }
    return $header_array;
}





/**
 * CURL Post
 */
private function postCurl($url, $option, $header = array(), $type = 'POST')
{
    $header = $this->getHttpAuthHeader($header);
    $ssl = stripos($url, 'https://') === 0 ? true : false;
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    if ($ssl) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    }
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //在HTTP请求中包含一个"User-Agent: "头的字符串。
    curl_setopt($curl, CURLOPT_HEADER, 0); //启用时会将头文件的信息作为数据流输出。
    if (!empty ($option)) {
        $options = json_encode($option);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $options); // Post提交的数据包
    }
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    if (!empty($header)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header); // 设置HTTP头
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
    $result = curl_exec($curl); // 执行操作
    $res['status'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $res = json_decode($result, true);
    /*   if(empty($res)){
           $res['result'] = $result;
       }*/
    curl_close($curl); // 关闭CURL会话
    return $res;
}
}


?>