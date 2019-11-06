<?php 

class AppBehavior extends Behavior {
	protected $options = array();
	
	public function run(&$params) {
		$m = MODULE_NAME;
		$a = ACTION_NAME;

		// 重新定义多层控制器[TP框架下的Base/Common/common.php中的A方法有对应改动]
		if (defined('APP_TYPE')) {
			if (APP_TYPE == 'Vue') {
				// 手机端多版本情况
				if ($_GET['v']) {
					$layer = APP_TYPE.'/v'.trim($_GET['v']);
				} else {
					$layer = APP_TYPE.'/v11'; // 手机端起始最小版本
				}
			} else {
				$layer = APP_TYPE;
			}
			C('DEFAULT_C_LAYER', $layer);
		}

		$key_file = file_get_contents(CONF_PATH.'license.dat');	
		if ($key_file) {
			//本地部署授权验证
			import('@.ORG.Scan');
			import("@.ORG.Unwrap");
			$padl = new padl();
			$padl->init(false);				
			$key_info = $padl->_unwrap_license($key_file);		
			$mac_address = $padl->_get_mac_address();
			// 调试用
			if ($_GET['kakarote'] == 1) {
				print_r($key_info['server']);echo '</br>';
				print_r($_SERVER['SERVER_NAME']);echo '</br>';
				print_r($key_info['mac_address']);echo '</br>';
				print_r($mac_address);
				print_r($key_info);die();
			}
			//对授权信息加密处理
			import("@.ORG.Kakarote");
			$str = serialize($key_info);
			$key = 'KAKAROTE@pdcrm！@#'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
			$des = new Kakarote();
			$re = $des->encrypt($str, $key); //加密
			$re_fey_info = bin2hex($re); //加密后结果 给二进制转为16进制，所谓的解决乱码
			//解密
			// $dre = $des->decrypt(hex2bin($re), $key);

			F('key_info',$re_fey_info);
			// C('license', $key_info);
			$padl->make_secure();
			
			$check_per = false;
			if ($key_info['end_time'] && $key_info['end_time'] < time()) {
				if (browserType() != 'ie') header('HTTP/1.1 404 Not Found');
				header('Content-Type: text/html; charset=utf-8');
				echo "Error: 您的授权服务已到期<!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding -->";
				exit;
			} else {
				if($key_info['server'] && $key_info['server'] ==  $_SERVER['SERVER_NAME']){
					$check_per = true;
				}elseif($key_info['mac_address'] && $key_info['mac_address'] ==  $mac_address){
					$check_per = true;
				}
			}		
			if(!$check_per){		
				if (browserType() != 'ie') header('HTTP/1.1 404 Not Found');
				header('Content-Type: text/html; charset=utf-8');
				echo "Error: 授权验证信息出错，请在授权的域名上面使用本程序！<!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding -->";
				exit;
			}elseif (!file_exists( CONF_PATH.'install.lock') && MODULE_NAME != 'Install') {			
				redirect(U('install/index'));
				exit;
			}elseif( MODULE_NAME != 'Install'){
				if (MODULE_NAME == 'User' && ACTION_NAME == 'user_add') {
					if(M('user')->count() >= $key_info['number']){
						echo '<div class="alert alert-error">软件官方提示：</br>&nbsp;&nbsp;&nbsp;&nbsp;您的CRM授权使用人数为'.$key_info['number'].'人，现已无法继续添加用户</div>';die();
					}
				} elseif (M('user')->count() > $key_info['number']) {
					if (browserType() != 'ie') header('HTTP/1.1 404 Not Found');
					header('Content-Type: text/html; charset=utf-8');
					echo "Error: 您的授权信息出错，系统用户人数已经超出授权限制<!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding -->";
					exit;
				}	
				if (!F('smtp')) {
					$value = M('Config')->where('name = "smtp"')->getField('value');
					F('smtp',unserialize($value));			
				}
				C('smtp', F('smtp'));
				if (!F('defaultinfo')) {
					$value = M('Config')->where('name = "defaultinfo"')->getField('value');
					F('defaultinfo',unserialize($value));			
				}
				C('defaultinfo', F('defaultinfo'));
				if (!F('sms') && $value = M('Config')->where('name = "sms"')->getField('value')) {
					F('sms',unserialize($value));
					C('sms', F('sms'));
				}			
			}
		} else {


			
		
			import('@.ORG.Scan');
			if (!file_exists( CONF_PATH.'install.lock') && MODULE_NAME != 'Install') {
				redirect(U('install/index'));
			} elseif (MODULE_NAME != 'Install') {
				if (!F('smtp')) {
					$value = M('Config')->where('name = "smtp"')->getField('value');
					F('smtp',unserialize($value));			
				}
				C('smtp', F('smtp'));
				if (!F('defaultinfo')) {
					$value = M('Config')->where('name = "defaultinfo"')->getField('value');
					F('defaultinfo',unserialize($value));			
				} else {
					// 如果有"悟空"字眼，修改为"简信"
					$defaultinfo = F('defaultinfo');
					if (strpos($defaultinfo['name'], '悟空') !== false) {
						$defaultinfo['name'] = 'PDCRM';
						$defaultinfo_ser = serialize($defaultinfo);
						M('Config')->where('name = "defaultinfo"')->setField('value', $defaultinfo_ser);
						F('defaultinfo', $defaultinfo);
					}
				}
				C('defaultinfo', F('defaultinfo'));
				if (!F('sms') && $value = M('Config')->where('name = "sms"')->getField('value')) {
					F('sms',unserialize($value));
					C('sms', F('sms'));
				}
			}

		}
	}
}