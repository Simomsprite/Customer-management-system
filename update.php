<?php
echo 1;die();
// die();
error_reporting(0);
	//获取数据库信息
	$db_name = 'crm_demo_v2';
	$db_user = 'root';
	$db_pwd = 'vjEj3t1aNP8x@2017@mysql';
	//连接数据库，执行升级sql

	header("Content-Type:text/html;charset=utf-8");
	set_time_limit(0);
	$conn_site_status = true;
	try {
		$conn_site = mysql_connect("localhost:3306",$db_user,$db_pwd);
	} catch (Exception $e) {
		$conn_site_status = false;
		$error_log[] = array('0'=>$val,'1'=>'1'); //数据库连接错误
	}
	if($conn_site_status){
		$db_selected = mysql_select_db($db_name, $conn_site);
		if (!$db_selected) {
			$error_log[] = array('0'=>$val,'1'=>'1'); //数据库连接错误
		} else {
			mysql_query("set names utf8", $conn_site);
			mysql_query("set character_set_client=utf8", $conn_site);
			mysql_query("set character_set_results=utf8", $conn_site);
			// $res = mysql_select_db($db_name, $conn_site);

			$empty_customer_arr = array();
			$success_customer_arr = array();
			$error_customer_arr = array();
			$error_address_customer_arr = array();

			//处理数据
			$sql_customer_list = 'SELECT * FROM `pd_customer`';
			$res_customer = mysql_query($sql_customer_list, $conn_site);

			while($row_customer = mysql_fetch_array($res_customer)){
				$address_arr = array();
				$address_arr = explode(chr(10), $row_customer['address']);
				if ($address_arr['3'] !== '') {
					$address_str = implode('', $address_arr);

					$ret = get_lng_lat($address_str);
					if ($ret['lng'] && $ret['lat']) {
						$data['lng'] = $ret['lng'] ?: 0;
						$data['lat'] = $ret['lat'] ?: 0;
						$sql = '';
						$sql = "UPDATE `pd_customer` SET `lng`=".$data['lng'].",`lat`=".$data['lat']." WHERE ( customer_id = ".$row_customer['customer_id']." )";
						if(mysql_query($sql, $conn_site)){
					    	$success_customer_arr[] = $row_customer['customer_id']; //返回客户ID
					    }else{
					    	$error_customer_arrp[] = $row_customer['customer_id'];
					    }
					} else {
						$error_address_customer_arr[] = $row_customer['customer_id'];
					}
				} else {
					$empty_customer_arr[] = $row_customer['customer_id'];
				}
			}
		}
	}

	function get_lng_lat($address){
		$map_ak = 'Z0Fo0ib1GUgWlylCWeLvQh2U';
		$url = "http://api.map.baidu.com/geocoder/v2/?address=$address&output=json&ak=$map_ak&callback=showLocation";

		$ret_script = curl_get($url);
		preg_match_all("/\{.*?\}}/is", $ret_script, $matches);
		$ret_arr = json_decode($matches[0][0],true);
		if ($ret_arr['status'] == 0) { //成功
			$location['lng'] = $ret_arr['result']['location']['lng'];
			$location['lat'] = $ret_arr['result']['location']['lat'];
			return $location;
		} else {
			return false;
		}
	}

	/**
	* curl 模拟GET请求
	* @author lee
	***/
	function curl_get($url){
		//初始化
	    $ch = curl_init();
	    //设置抓取的url
	    curl_setopt($ch, CURLOPT_URL, $url);

	    //设置获取的信息以文件流的形式返回，而不是直接输出。
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // https请求 不验证hosts 

	    //执行命令
	    $output = curl_exec($ch);

		curl_close($ch); //释放curl句柄

		return $output; 
	}
	mysql_close($conn_site); //关闭mysql连接

	$data = array();
	// $data['file_count'] = $file_count;
	// $data['success_count'] = $success_count;
	// $data['new_count'] = $new_count;
	$data['error_log'] = $error_log;
	$data['empty_customer_arr'] = $empty_customer_arr;
	$data['success_customer_arr'] = $success_customer_arr;
	$data['error_customer_arr'] = $error_customer_arr;
	$data['error_address_customer_arr'] = $error_address_customer_arr;
echo '<pre>';
print_r($data);
echo '<pre>';die();

