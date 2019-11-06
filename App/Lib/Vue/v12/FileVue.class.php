<?php
/**
 *附件相关
 **/
class FileVue extends Action {
	/**
	 *用于判断权限
	 *@permission 无限制
	 *@allow 登录用户可访问
	 *@other 其他根据系统设置
	 **/
	public function _initialize(){
		$action = array(
			'permission'=>array('ali_oss_key_callback'),
			'allow'=>array('add','delete', 'ali_oss_key', 'check_max_size')
		);
		B('VueAuthenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
		
		Global $role;
		$this->role = $role;
		Global $roles;
		$this->roles = $roles;

		if($roles == 2){
			$this->ajaxReturn('','您没有此权限！',-2);
		}

		if($roles == 3){
			$this->ajaxReturn('','请先登录！',-1);
		}
	}
	
	/**
	 * 添加附件
	 * @param 
	 * @author 
	 * @return 
	 */
	public function add() {
		$m_config = M('Config');
		if (!empty($_FILES)) {
			if (isset($_FILES['file']['size']) && $_FILES['file']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				$defaultinfo = $m_config->where('name = "defaultinfo"')->find();
				$value = unserialize($defaultinfo['value']);
				$allow_file_type = str_replace(",php","",$value['allow_file_type']);
				$upload->allowExts  = explode(',', $allow_file_type);
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					$this->ajaxReturn('',L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'),0);
				}
				$upload->savePath = $dirname;
				if (!$upload->upload()) {
					$this->ajaxReturn('',$upload->getErrorMsg().$upload->getErrorMsg(),0);
				} else {
					$info = $upload->getUploadFileInfo();
				}
			}
			$m_file = M('File');
			if (is_array($info[0]) && !empty($info[0])) {
				if (substr($info[0]['savename'], -3)=='jpg' || substr($info[0]['savename'], -3)=='png' || substr($info[0]['savename'], -4)=='jpeg') {
					$data['file_path_thumb'] = $info[0]['savepath'] .'thumb_'. $info[0]['savename'];
				}
				$data['file_path'] = $info[0]['savepath'] . $info[0]['savename'];
				$data['name'] = $info[0]['name'];
				$data['role_id'] = session('role_id');
				$data['size'] = $info[0]['size'];
				$data['create_date'] = time(); 
				if ($file_id = $m_file->add($data)) {
					//返回数据
					$res_data = array();
					$res_data['img_data'] = $data;
					$res_data['img_data']['file_type'] = end(explode('.',$data['name']));
					$res_data['img_data']['size'] = round($data['size']/1024,2).'Kb';
					if (intval($data['size']) > 1024*1024) {
						$res_data['size'] = round($data['size']/(1024*1024),2).'Mb';
					}

					if ($_POST['r'] && $_POST['module'] && $_POST['id']) {
						$r_file_module = M(trim($_POST['r']));
						$module = trim($_POST['module']);
						$m_id = intval($_POST['id']);

						$temp = array();
						$temp['file_id'] = $file_id;
						$temp[$module . '_id'] = $m_id;
						if ($r_file_module->add($temp)) {
							$res_data['data'] = $file_id;
							$res_data['info'] = '附件上传成功！';
							$res_data['status'] = 1;
							$this->ajaxReturn($res_data,'JSON');
						} else {
							$this->ajaxReturn('',$data['name'].'上传失败！',0);
						}
					} else {
						$res_data['data'] = $file_id;
						$res_data['info'] = '附件上传成功！';
						$res_data['status'] = 1;
						$this->ajaxReturn($res_data,'JSON');
					}
				} else {
					$this->ajaxReturn('',$data['name'].'上传失败！',0);
				}
			} else {
				$this->ajaxReturn('',$data['name'].'上传失败！',0);
			}
		}
	}

	/**
	 * 删除附件
	 * @param 
	 * @author 
	 * @return 
	 */
	public function delete() {
		if ($this->isPost()) {
			$file_id = $_POST['id'] ? intval($_POST['id']) : 0;
			if ($file_id) {
				$m_file = M('File');
				$r_module = $_POST['module'] ? trim($_POST['module']) : '';
				if (!$r_module) {
					$this->ajaxReturn('','参数错误！',0);
				}
				switch ($r_module) {
					case 'examine' : $r = M('ExamineFile'); break;
					case 'business' : $r = M('RBusinessFile'); break;
					case 'contract' : $r = M('RContractFile'); break;
					case 'customer' : $r = M('RCustomerFile'); break;
					case 'finance' : $r = M('RFileFinance'); break;
					case 'leads' : $r = M('RFileLeads'); break;
					case 'log' : $r = M('RFileLog'); break;
					case 'product' : $r = M('RFileProduct'); break;
					case 'member' : $r = M('RMemberFile'); break;
					case 'task' : $r = M('RTaskFile'); break;
					case 'invoice' : $r = M('RFileInvoice'); break;
				}
				if ($r) {
					$file_info = $m_file->where('file_id = %d',$file_id)->find();
					$msg = $m_file->where('file_id = %d',$file_id)->delete();
					if ($msg) {
						if ($file_info['oss'] == 1) {
							$d_file = D('File');
							$file_path_list = array($file_info['file_path']);
							$res = $d_file->ossDelete($file_path_list);
							if ($res['status'] === false) {
								$this->ajaxReturn('', $d_file->msg, 0);
							}
						} else {
							@unlink($file_info['file_path']);
						}
						if ($r->where('file_id = %d',$file_id)->find()) {
							$r->where('file_id = %d',$file_id)->delete();
						}
						$this->ajaxReturn('','删除成功！',1);
					} else {
						$this->ajaxReturn('','删除失败！',0);
					}
				} else {
					$this->ajaxReturn('','参数错误！',0);
				}
			}
		} else {
			$this->ajaxReturn('','跑神儿了！:-D',0);
		}
	}

	public function ali_oss_key()
	{
		if (IS_POST) {
			// 获取域名信息，确定上传路径
			$server_name = $_POST['SERVER_NAME'];
			// 云平台路径
			// $this->ajaxReturn(array('status' => 2, 'info' => '上传至CRM服务器。'));
			if (strpos($server_name,'xxx.com')) {
				$dir = str_replace('.xxx.com', '', $server_name).'/'.date('Ymd').'/';
			} elseif ($server_name == 'nct.pdcrm.net/') {
				$dir = 'nct.pdcrm.net/' . date('Ymd') . '/';
			} else {
				$this->ajaxReturn(array('status' => 2, 'info' => '上传至CRM服务器。', $server_name));
			}

			// 回调地址
			$callbackUrl = 'http://' . $server_name . $_SERVER["SCRIPT_NAME"] . '?m=file&a=ali_oss_key_callback';
			
			$d_file = D('File');
			$response = aliOssToken($d_file::FILE_URL, $callbackUrl, $dir);
			$response['max_upload_size'] = $d_file::MAX_UPLOAD_SIZE;
			$defaultinfo = M('Config')->where('name = "defaultinfo"')->find();
			$config = unserialize($defaultinfo['value']);
			$response['allow_file_type'] = explode(',', $config['allow_file_type']);	// 设置附件上传类型
			
			$this->ajaxReturn($response, '', 1);
		}
	}


	public function ali_oss_key_callback()
	{
		if (IS_POST) {
			$m_file = M('File');
			$r_file_module = M($_POST['r']);
			$module = $_POST['module'];
			$m_id = $_POST['id'];
			if (substr($_POST['mimeType'], 0, 5) == 'image') {
				$data['file_path_thumb'] = $_POST['filename'] . '?x-oss-process=image/resize,m_fixed,h_100,w_100';
			}
			$data['file_path'] = $_POST['filename'];
			$data['name'] = $_POST['oldname'];
			$data['size'] = $_POST['size'];
			$data['role_id'] = $_POST['role_id'];
			$data['create_date'] = time();
			$data['oss'] = 1;
			if ($file_id = $m_file->add($data)) {
				if ($module == '' || $m_id == '' || $r_file_module == '') {
					$this->ajaxReturn(array('file_id' => $file_id, 'info' => '上传成功！', 'status' => 1));
				}
				$temp['file_id'] = $file_id;
				$temp[$module . '_id'] = $m_id;
				if ($r_file_module->add($temp)) {
					$this->ajaxReturn(array('file_id' => $file_id, 'info' => '上传成功！', 'status' => 1));
				} else {
					$m_file->delete($file_id);
					// 删除oss文件
					$this->ajaxReturn(array('info' => '上传失败！', 'status' => 2));
				}
			} else {
				$this->ajaxReturn(array('info' => '上传失败！', 'status' => 2));
			}
		}
	}


	/**
	 * 判断新上传文件大小是否超出限制
	 */
	public function check_max_size()
	{
		if (IS_POST) {
			$new_file_size = $_POST['new_file_size'];
			$d_file = D('File');
			$res = $d_file->checkOssMaxSize($new_file_size);
			if ($res) {
				$this->ajaxReturn(array('info' => $d_file->msg, 'status' => 2));
			} else {
				$this->ajaxReturn(array('info' => '', 'status' => 1));
			}
		}
	}
}
