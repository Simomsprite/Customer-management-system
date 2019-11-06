<?php

class FileModel extends Model
{
    // 最大上传文件大小 
    const MAX_UPLOAD_SIZE = 52428800;  // 50M * 1024 * 1024

    // OSS ID
    const OSS_ID = 'LTAI0x8PzoedR0Pv';
    // OSS KET
    const OSS_KEY = 'RKEnQRcLChPYzGOyvIIIzpiHvpFt98';

    // Bucket  云平台录音文件上传地址
    const RECORD_URL = 'https://wukongtest.oss-cn-hangzhou.aliyuncs.com';

    // 云平台附件、头像上传地址
    const FILE_URL = 'https://wk-cloud.oss-cn-beijing.aliyuncs.com';


    // 云平台附件存储的Endpoint（访问域名）
    const OSS_ENDPOINT = 'http://oss-cn-beijing.aliyuncs.com';
    // 云平台附件的存储空间 （Bucket）
    const OSS_BUCKET = 'wk-cloud';

    // 返回信息
    public $msg = '';

    
    /**
     * 获取最大上传文件大小
     */
    public function getMaxSize()
    {
        if (strpos($_SERVER['SERVER_NAME'], 'xxx.com') === false) {
            // 非云平台 默认单位M
            $upload_max_filesize = (int) strtoupper(ini_get('upload_max_filesize'));
            return $upload_max_filesize . 'M';
        } else {
            // 云平台 上传至OSS
            return self::MAX_UPLOAD_SIZE / (1024 * 1024) . 'M';
        }
    }


    /**
     * 当前用户（CRM系统）OSS容量是否超出限制
     * @param   int     新上传文件大小
     * @return  bool    是否超出上限  false 可以继续上传， true 不可以继续上传
     */
    public function checkOssMaxSize($new_file_size = 0)
    {
        return false;
        $c_oss_max_size = C('OSS_MAX_SIZE');
        // $where = array('oss' => 1);
        $where = array();
        $current_size_total = (int) $this->where($where)->sum('size');
        $new_size_total = $current_size_total + $new_file_size;
        $res = $c_oss_max_size - $new_size_total;
        if ($res < 0) {
            $this->msg = '服务器空间不足，剩余:' . ($c_oss_max_size - $current_size_total) / (1024 * 1024) . 'M，超出您上传文件大小，请联系管理员。';
        }
        return $res < 0;
    }


    /**
     * 阿里云oss 删除多个文件
     * @param  bucket 阿里云储存设置的命名空间，如设置为“files”
     * @param  objects 文件对象，从bucket根目录到文件名称的路径，如files/img/a.jgp，取img/a.jgp即可
     * @author lee
     */
    public function ossDelete($objects = array()){

        $bucket = self::OSS_BUCKET;
        try {
            $ossClient = $this->getOssObj();
            $ossClient->deleteObjects($bucket, $objects);
        } catch (\OSS\Core\OssException $e) {
            $this->msg = $e->getMessage();
            return false;
        }
        $this->msg = '删除成功';
        return true;
    }

        
    /**
     * 返回OSS SDK对象
     */
    public function getOssObj()
    {
        //引入自动加载php文件
        import("@.ORG.OssAutoload", "", ".php");
        
        // 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录 https://ram.console.aliyun.com 创建RAM账号。
        $accessKeyId = self::OSS_ID;
        $accessKeySecret = self::OSS_KEY;
        $endpoint = self::OSS_ENDPOINT;
        return new \OSS\OssClient($accessKeyId, $accessKeySecret, $endpoint);
    }


    /**
     * OSS文件下载
     */
    public function ossDownload($url, $file_name = '')
    {
		$data = file_get_contents($url); //获取OSS URL 图片
		if (!$data) {
		    alert('error','sorry，文件不存在！',$_SERVER['HTTP_REFERER']);
		}

        //获取图片文件名
        if ($file_name == '') {
            $pos = strrpos($url, "/");//返回/在图片path出现的最后一个位置
            $file_name = substr($url, $pos+1);  //字符串截取。返回图片名
        }
        $ext = getExtension($url);
        
        //输出页面下载头部
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Content-Length: ".strlen($data));
        header("Content-Disposition: attachment; filename=" . $file_name . '.' . $ext);
        echo $data;
        exit;
    }


    /**
    * 阿里云oss 上传文件
    * @param object 上传到OSS需命名的文件名称，如img/20180825/aa.jpg
    * @param filePath 本地文件路径
    * @author lee
    */
    public function ossUpload($object, $filePath)
    {
        $bucket = self::OSS_BUCKET;
        
        try{
            $ossClient = $this->getOssObj();
            $res = $ossClient->uploadFile($bucket, $object, $filePath);
        } catch(\OSS\Core\OssException $e) {
            $this->msg = $e->getMessage();
            return false;
        }
        return true;
    }


    /**
     * 多文件上传时，格式化$_FILES数组
     * @author lee
     */
    public function getFileList($default_files)
    {
        foreach ($default_files as $k => $v) {
            foreach ($v as $ke => $val) {
                $file_list[$ke][$k] = $val;
            }
        }
        return $file_list;
    }


    /**
     * 判断当前系统上传文件是否可上传阿里云OSS
     * 返回值 如果可上传至OSS，返回路径，否则返回false
     * @author lee
     */
    public function upload_oss()
    {
        $special_host_list = array(
            'nct.pdcrm.net',
            'pss.pdcrm.net',
        );
        $server_name = $_SERVER['SERVER_NAME'];
        if (strpos($server_name, 'xxx.com') !== false) {
            return str_replace('.xxx.com', '', $server_name).'/'.date('Ymd').'/';
        } elseif (in_array($server_name, $special_host_list)) {
            return $server_name.'/'.date('Ymd').'/';
        } else {
            return false;
        }
    }

    
}
