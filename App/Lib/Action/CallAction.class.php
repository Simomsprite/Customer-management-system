<?php
class CallAction extends Action{
	/**
	*  用于判断权限
	*  @permission 无限制
	*  @allow 登录用户可访问
	*  @other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array('upload','aliosskey','aliosskeycallback'),
			'allow'=>array('teldata','call_content','data','recordadd','user','setting','add','record','getcurrentstatus','analyexcelexport','ossrecord')
		);
		B('Authenticate', $action);
	}



	/**
	 * 呼叫中心(右下角提示信息)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function call_content(){
		$tel = $_REQUEST['tel'] ? trim($_REQUEST['tel']) : '';
		//先查客户联系人电话，再查线索
		$contacts_info = M('Contacts')->where(array('telephone'=>$tel))->field('contacts_id,telephone,name')->find();
		if ($contacts_info) {
			$model = 'customer';
			$model_id = M('RContactsCustomer')->where(array('contacts_id'=>$contacts_info['contacts_id']))->getField('customer_id');
		} else {
			$model = 'leads';
			$model_id = M('Leads')->where(array('mobile'=>$tel))->getField('leads_id');
		}
		if ($model) {
			switch ($model) {
				case 'customer' : 
					$info['name'] = M('Customer')->where(array('customer_id'=>$model_id))->getField('name');
					$info['telephone'] = $contacts_info['telephone'];
					$info['contacts_name'] = $contacts_info['name'];
					break;
				case 'leads' :
					$info = M('Leads')->where(array('mobile'=>$tel))->field('name,contacts_name,telephone')->find();
					break;
			}
			if ($info) {
				$info['model'] = $model;
				$info['model_id'] = $model_id;
				$info['type'] = 1; //有信息返回
				$info['name'] = $info['name'] ? cutString($info['name'],10) : '查看详情';
			} else {
				$info['telephone'] = $tel;
				$info['type'] = 2; //新建客户
			}
		} 
		$this->info = $info;
		$this->display();
	}

	/**
	 * 弹出页面(详情)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function data() {
		$model = trim($_REQUEST['model']) ? : '';
		$model_id = intval($_REQUEST['model_id']) ? : '';
		$tel = trim($_REQUEST['tel']) ? : '';
		$tel_type = trim($_REQUEST['tel_type']) ? : '';
		$m_user = M('User');
		// 	echo '<div class="alert alert-error">参数错误！</div>';die();

		if ($tel_type == 2) {
			$this->model = 'customer';
			$this->field_list = field_list_html("add","customer");
			$contacts_field_list = field_list_html("add","contacts","","contacts");
			foreach ($contacts_field_list['main'] as $k=>$v) {
				if ($v['field'] == 'telephone') {
					$contacts_field_list['main'][$k]['html'] = '<input class="form-control " type="mobile" id="con_contacts[telephone]" name="con_contacts[telephone]" value="'.$tel.'"><span id="con_contacts[telephone]Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>';
				}
			}
			$this->contacts_field_list = $contacts_field_list;
			$this->display('add');
		} else {
			if ($tel && !$model_id && !$model) {
				//先查客户联系人电话，再查线索
				$contacts_info = M('Contacts')->where(array('telephone'=>$tel))->field('contacts_id,telephone,name')->find();
				if ($contacts_info) {
					$model = 'customer';
					$model_id = M('RContactsCustomer')->where(array('contacts_id'=>$contacts_info['contacts_id']))->getField('customer_id');
				} else {
					$model = 'leads';
					$model_id = M('Leads')->where(array('mobile'=>$tel))->getField('leads_id');
				}
			}

			if ($model_id && $model) {
				switch ($model) {
					case 'customer' : 
						if ($tel) {
							$contacts_info = M('Contacts')->where(array('telephone'=>$tel))->field('contacts_id,telephone,name')->find();
						}
						$info = D('CustomerView')->where(array('customer.customer_id'=>$model_id))->find();
						//联系人信息
						$info['contacts_name'] = $contacts_info['name'];
						$info['contacts_telephone'] = $contacts_info['telephone'];
						$info['owner'] = $m_user->where('role_id = %d', $info['owner_role_id'])->field('role_id,full_name')->find();
						//自定义字段
						$field_list = M('Fields')->where(array('model'=>'customer','field'=>array('not in',array('name','grade','customer_owner_id','customer_code'))))->order('is_main desc ,order_id asc')->select();	
						//自定义快捷回复
						$this->status_list = M('LogStatus')->select();
						$m_business = M('Business');
						$r_business_log = M('rBusinessLog');
						//沟通日志
						$business_ids = $m_business->where(array('customer_id'=>$model_id))->getField('business_id',true);
						$business_log_ids = $r_business_log->where(array('business_id'=>array('in',$business_ids)))->getField('log_id', true);
						$customer_log_ids = M('rCustomerLog')->where(array('customer_id'=>$model_id))->getField('log_id',true);
						if ($customer_log_ids == '') {
							$log_ids = $business_log_ids;
						} elseif ($business_log_ids == '') {
							$log_ids = $customer_log_ids;
						} else {
							$log_ids = array_merge($business_log_ids, $customer_log_ids);
						}
						$m_sign = M('Sign');
						$m_sign_img = M('SignImg');
						$m_user = M('User');
						$m_log_status = M('LogStatus');
						$log_list = M('Log')->where(array('log_id'=>array('in',$log_ids)))->order('log_id desc')->select();
						foreach ($log_list as $k=>$v) {
							$business_info = array();
							$business_id = '';
							if ($business_log_ids && in_array($v['log_id'],$business_log_ids)) {
								$business_id = $r_business_log->where(array('log_id'=>$v['log_id']))->getField('business_id');
								$business_info = $m_business->where(array('business_id'=>$business_id))->field('code,business_id')->find();
							}
							$log_list[$k]['business_info'] = $business_info;
							//签到
							if ($v['sign'] == 1) {
								$sign_info = $m_sign->where('log_id = %d',$v['log_id'])->find();
								$log_list[$k]['sign_img'] = $m_sign_img ->where('sign_id = "%d"',$sign_info['sign_id'])->select();
								$log_list[$k]['sign_info'] = $sign_info;
							}
							$role_info = array();
							$role_info = $m_user->where('role_id = %d', $v['role_id'])->field('full_name,thumb_path,role_id')->find();
							if (!$role_info['thumb_path']) {
								$role_info['thumb_path'] = './Public/img/avatar_default.png';
							}
							$log_list[$k]['owner'] = $role_info;
							$log_list[$k]['log_type'] = 'rCustomerLog';
							$log_list[$k]['content'] = strip_tags($v['content']);
							$status_name = $m_log_status->where('id = %d',$v['status_id'])->getField('name');
							$log_list[$k]['status_name'] = $status_name ? $status_name : '';
						}

						//通话录音
				        $record_list = array();
				        $record_list = M('CallRecord')->where(array('model'=>'customer','model_id'=>$model_id))->select();
				        foreach ($record_list as $k=>$v) {
				        	$record_list[$k]['owner'] = $m_user->where(array('role_id'=>$v['role_id']))->field('full_name,thumb_path,role_id')->find();
				        	$record_list[$k]['create_date'] = $v['start_time'];
				        	$record_list[$k]['is_call'] = 1;
				        	if ($v['call_type'] == 1) {
				        		$record_list[$k]['call_type_name'] = '呼入';
				        	} else {
				        		$record_list[$k]['call_type_name'] = '呼出';
				        	}
				        	//阿里云服务器
			                if ($v['call_upload'] == 1 && $v['file_path']) {
			                    $record_list[$k]['file_path'] = 'https://wukongtest.oss-cn-hangzhou.aliyuncs.com/'.$v['file_path'];
			                }
				        }

				        /*对合并的数组排序*/
						function cmp_2($a,$b){
						    if($a['create_date'] == $b['create_date']){
						        return  0 ;
						    }
						    return ($a['create_date'] > $b['create_date']) ? -1 : 1;
						}
						if ($record_list) {
							if ($log_list) {
								$log_list = array_merge($record_list,$log_list);
							} else {
								$log_list = $record_list;
							}
							usort($log_list, "cmp_2");
						}
						break;
					case 'leads' :
						$info = D('LeadsView')->where(array('leads.leads_id'=>$model_id))->find();
						$info['owner'] = $m_user->where('role_id = %d', $info['owner_role_id'])->field('role_id,full_name')->find();
						//自定义字段
						$field_list = M('Fields')->where(array('model'=>'leads'))->order('is_main desc ,order_id asc')->select();	
						//自定义快捷回复
						$this->status_list = M('LogStatus')->select();
						$log_ids = M('rLeadsLog')->where(array('leads_id'=>$model_id))->getField('log_id',true);
						$m_sign = M('Sign');
						$m_sign_img = M('SignImg');
						$m_user = M('User');
						$m_log_status = M('LogStatus');
						$log_list = M('Log')->where(array('log_id'=>array('in',$log_ids)))->order('log_id desc')->select();
						foreach ($log_list as $k=>$v) {
							//签到
							if ($v['sign'] == 1) {
								$sign_info = $m_sign->where('log_id = %d',$v['log_id'])->find();
								$log_list[$k]['sign_img'] = $m_sign_img ->where('sign_id = "%d"',$sign_info['sign_id'])->select();
								$log_list[$k]['sign_info'] = $sign_info;
							}
							$role_info = array();
							$role_info = $m_user->where('role_id = %d', $v['role_id'])->field('full_name,thumb_path,role_id')->find();
							if (!$role_info['thumb_path']) {
								$role_info['thumb_path'] = './Public/img/avatar_default.png';
							}
							$log_list[$k]['owner'] = $role_info;
							$log_list[$k]['log_type'] = 'rCustomerLog';
							$log_list[$k]['content'] = strip_tags($v['content']);
							$status_name = $m_log_status->where('id = %d',$v['status_id'])->getField('name');
							$log_list[$k]['status_name'] = $status_name ? $status_name : '';
						}

						//通话录音
				        $record_list = array();
				        $record_list = M('CallRecord')->where(array('model'=>'leads','model_id'=>$model_id))->select();
				        foreach ($record_list as $k=>$v) {
				        	$record_list[$k]['owner'] = $m_user->where(array('role_id'=>$v['role_id']))->field('full_name,thumb_path,role_id')->find();
				        	$record_list[$k]['create_date'] = $v['start_time'];
				        	$record_list[$k]['is_call'] = 1;
				        	if ($v['call_type'] == 1) {
				        		$record_list[$k]['call_type_name'] = '呼入';
				        	} else {
				        		$record_list[$k]['call_type_name'] = '呼出';
				        	}
				        	//阿里云服务器
			                if ($v['call_upload'] == 1 && $v['file_path']) {
			                    $record_list[$k]['file_path'] = 'https://wukongtest.oss-cn-hangzhou.aliyuncs.com/'.$v['file_path'];
			                }
				        }

				        /*对合并的数组排序*/
						function cmp_2($a,$b){
						    if($a['create_date'] == $b['create_date']){
						        return  0 ;
						    }
						    return ($a['create_date'] > $b['create_date']) ? -1 : 1;
						}
						if ($record_list) {
							if ($log_list) {
								$log_list = array_merge($record_list,$log_list);
							} else {
								$log_list = $record_list;
							}
							usort($log_list, "cmp_2");
						}
						break;
				}
				$this->info = $info;
				$this->log_list = $log_list;
				$this->field_list = $field_list;
				$this->model = $model;
				$this->model_id = $model_id;
				$this->display('view');
			} else {
				$this->call_tel = $tel;
				$this->model = 'customer';
				$this->display('add');
			}
		}
	}

	/**
	 * 客户联系人信息
	 * @param 
	 * @author 
	 * @return 
	 */
	public function telData() {
		$model = trim($_REQUEST['model']) ? : 'customer';
		$model_id = intval($_REQUEST['model_id']) ? : '';
		if (!$model || !$model_id) {
			echo '<div class="alert alert-error">参数错误！</div>';die();
		}
		switch ($model) {
			case 'customer' : 
				$contacts_ids = M('RContactsCustomer')->where(array('customer_id'=>$model_id))->getField('contacts_id',true);
				$list = M('Contacts')->where(array('contacts_id'=>array('in',$contacts_ids)))->field('name,telephone,contacts_id')->select();
		}
		$this->list = $list;
		$this->display();
	}

	/**
	 * 呼叫中心(设置开关)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function setting(){
		$call_setting = C('CALL_SETTING');
		$this->call_setting = $call_setting;
		//阿里云空间使用
		$m_call_record = M('CallRecord');
		$sum_size = $m_call_record->where(array('call_upload'=>1,'size'=>array('gt',0)))->sum('size'); //字节
// $sum_size = '12773741824';
		$this->call_size = formatBytes($sum_size);
		$sum_size = $sum_size/1073741824;
		//比例
		$total_size = $call_setting['OSS_FILE_SIZE'];
		$this->total_size = $total_size;
		$this->progress = intval($sum_size/$total_size > 1 ? 100 : $sum_size*100/$total_size);
		$this->display();
	}

	/**
	 * 呼叫中心(创建客户)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function add(){
		if($this->isPost()){
			$m_customer = D('Customer');
			$m_customer_data = D('CustomerData');
			$field_list = M('Fields')->where(array('model'=>'customer','in_add'=>1))->order('order_id')->select();
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$_POST[$v['field']] = implode(chr(10),$_POST[$v['field']]);
					break;
					case 'datetime':
						$_POST[$v['field']] = strtotime($_POST[$v['field']]);
					break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$b = array_filter($_POST[$v['field']]);
							$_POST[$v['field']] = !empty($b) ? implode(chr(10),$b) : '';
						}
					break;
				}
			}
			if($m_customer->create()){
				if($m_customer_data->create()!==false){
					//保存联系人信息
					if($_POST['con_contacts'] && $_POST['con_contacts']['name']){
						$m_contacts = M('Contacts');
						$m_contacts_data = M('ContactsData');
						//处理POST数据
						$contacts_field_list = M('Fields')->where(array('model'=>'contacts','in_add'=>1))->order('order_id')->select();
						foreach ($contacts_field_list as $v){
							switch($v['form_type']) {
								case 'address':
									$_POST['con_contacts'][$v['field']] = implode(chr(10),$_POST['con_contacts'][$v['field']]);
								break;
								case 'datetime':
									$_POST['con_contacts'][$v['field']] = strtotime($_POST['con_contacts'][$v['field']]);
								break;
								case 'box':
									eval('$field_type = '.$v['setting'].';');
									if($field_type['type'] == 'checkbox'){
										$b = array_filter($_POST['con_contacts'][$v['field']]);
										$_POST['con_contacts'][$v['field']] = !empty($b) ? implode(chr(10),$b) : '';
									}
								break;
							}
						}

						if($m_contacts->create($_POST['con_contacts'])){
							if($m_contacts_data->create($_POST['con_contacts']) !== false){
								$m_contacts->creator_role_id = session('role_id');
								$m_contacts->create_time = time();
								$m_contacts->update_time = time();
								if($contacts_id = $m_contacts->add()){
									$m_contacts_data->contacts_id = $contacts_id;
									$m_contacts_data->add();
								}else{
									$this->ajaxReturn('','添加首要联系人失败！',0);
								}
							}
						}
					}
					$m_customer->owner_role_id = session('role_id');
					$m_customer->create_time = time();
					$m_customer->update_time = time();
					$m_customer->get_time = time();
					if($contacts_id){
						$m_customer->contacts_id = $contacts_id;
					} 
					$m_customer->creator_role_id = session('role_id');
					if(!$customer_id = $m_customer->add()){
						$this->ajaxReturn('','添加客户失败，请联系管理员！',0);
					}
					$m_customer_data->customer_id = $customer_id;
					$m_customer_data->add();

					//记录操作记录
					actionLog($customer_id);
					if ($contacts_id && $customer_id) {
						$rcc['contacts_id'] = $contacts_id;
						$rcc['customer_id'] = $customer_id;
						M('RContactsCustomer')->add($rcc);
					}

					$this->ajaxReturn('','添加成功！',1);
				}else{
					$this->ajaxReturn('',$m_customer_data->getError(),0);
				}
			}else{
				$this->ajaxReturn('',$m_customer->getError(),0);
            }
        }
		$this->display();
	}

	/**
	 * 呼叫中心(通话记录添加)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function recordAdd(){
		if ($this->isGet()) {
			$m_call_record = M('CallRecord');
			//通话时长（秒）
			$telephone = $_REQUEST['tel'] ? trim($_REQUEST['tel']) : '';
			$data = array();
			//通话状态（1来电、2呼出、3挂断、4接听）
			$call_status = $_REQUEST['call_status'] ? intval($_REQUEST['call_status']) : '3';
			//通话session_id
			$call_session_id = $_REQUEST['session_id'] ? trim($_REQUEST['session_id']) : '';
			if ($call_status == 1 || $call_status == 2) {
				//呼叫状态(1呼入，2呼出)
				$data['call_type'] = $call_status;
				$data['role_id'] = session('role_id');
				$data['telephone'] = $telephone;
			}
			$data['call_status'] = $call_status;
			if ($telephone && $telephone !== 'undefined') {
				if ($call_session_id) {
					$record_info = array();

					// $record_info = $m_call_record->where(array('role_id'=>session('role_id'),'telephone'=>$telephone,'session_id'=>$call_session_id))->order('id desc')->find();

					$record_info = $m_call_record->where(array('role_id'=>session('role_id'),'telephone'=>$telephone,'call_status'=>array('neq',3),'session_id'=>$call_session_id))->order('id desc')->find();
					//呼叫未接听（挂断）
					//呼入未接听（挂断）
					
					//正常接听挂断
					if ($record_info) {
						if ($call_status == 3) {
							//将状态置3,防止未知原因影响状态标记
							$m_call_record->where(array('role_id'=>session('role_id'),'telephone'=>$telephone,'call_status'=>array('neq',3)))->setField('call_status',3);

							if (!$record_info['model'] || !$record_info['model_id']) {
								//先查客户联系人电话，再查线索
								$contacts_id = M('Contacts')->where(array('telephone'=>$telephone))->getField('contacts_id');
								if ($contacts_id) {
									$model = 'customer';
									$model_id = M('RContactsCustomer')->where(array('contacts_id'=>$contacts_id))->getField('customer_id');
								} else {
									$model = 'leads';
									$model_id = M('Leads')->where(array('mobile'=>$telephone))->getField('leads_id');
								}
								$data['model'] = $model_id ? $model : '';
								$data['model_id'] = $model_id ? intval($model_id) : '';
							}
							$end_time = time();
							if ($record_info['call_status'] == 4) {
								//通话时长
								$talk_time = $end_time-$record_info['answer_time'];
								$data['end_time'] = time();
								$data['talk_time'] = $talk_time;
							} else {
								$data['end_time'] = time();
								$data['talk_time'] = 0;
							}
							if (!$m_call_record->where(array('id'=>$record_info['id']))->save($data)) {
								$this->ajaxReturn('沟通记录创建失败！','',0);
							}
						} elseif ($call_status == 4 && $record_info['call_status'] !== 4) {
							//接听（更新开始时间）
							$data['answer_time'] = time();
							$dial_time = '';
							$dial_time = time()-$record_info['start_time'];
							$data['dial_time'] = $dial_time;
							//计算摘机时长
							if (!$m_call_record->where(array('id'=>$record_info['id']))->save($data)) {
								$this->ajaxReturn('沟通记录创建失败！','',0);
							}
						}
					} else {
						if ($call_status !== 3 && $data['telephone']) {
							$data['start_time'] = time();
							$data['session_id'] = $call_session_id;

							//先查客户联系人电话，再查线索
							$contacts_id = M('Contacts')->where(array('telephone'=>$telephone))->getField('contacts_id');
							if ($contacts_id) {
								$model = 'customer';
								$model_id = M('RContactsCustomer')->where(array('contacts_id'=>$contacts_id))->getField('customer_id');
							} else {
								$model = 'leads';
								$model_id = M('Leads')->where(array('mobile'=>$telephone))->getField('leads_id');
							}
							$data['model'] = $model_id ? $model : '';
							$data['model_id'] = $model_id ? intval($model_id) : '';

							if (!$m_call_record->add($data)) {
								$this->ajaxReturn('沟通记录创建失败！','',0);
							}
						}
					}
				} else {
					if ($call_status == 2) {
						//呼出
						$data['start_time'] = time();
						$data['session_id'] = $call_session_id;
						if (!$m_call_record->add($data)) {
							$this->ajaxReturn('沟通记录创建失败！','',0);
						}
					}
				}
			} else {
				$this->ajaxReturn('error！','',0);
			}
		}
	}

	/**
	 * 呼叫中心(通话记录)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function record () {
		//权限判断
		if(!getPerByAction('call','analytics')){
			alert('error','您没有此权利！',0);
		}
		$m_call_record = M('CallRecord');
		//权限范围
		$below_ids = getPerByAction('call','analytics');
		$role_id_array = array();
		if(intval($_GET['role'])){
			$role_id_array = array(intval($_GET['role']));
		}else{
			if(intval($_GET['department'])){
				$department_id = intval($_GET['department']);
				foreach(getRoleByDepartmentId($department_id, true) as $k=>$v){
					$role_id_array[] = $v['role_id'];
				}
			}
		}
		//过滤权限范围内的role_id
		if($role_id_array){
			//数组交集
			$idArray = array_intersect($role_id_array,$below_ids);
		}else{
			$idArray = $below_ids;
		}
		//时间段搜索
		if($_GET['start_time']){
			$start_time = explode(' - ',trim($_GET['start_time']));
			$end_time = $start_time[1] ?  strtotime(date('Y-m-d 23:59:59',strtotime($start_time[1]))) : strtotime(date('Y-m-d 23:59:59',time()));
			if($start_time[0]){
				$start_time = strtotime($start_time[0]);
			}
			$params[] = "start_time=" . trim($_GET['start_time']);
		}else{
			$start_time = strtotime(date('Y-m-01 00:00:00'));
			$end_time = strtotime(date('Y-m-d H:i:s'));
		}

		if($_GET['role_id']){
			$where['role_id'] = intval($_GET['role_id']);
			$params[] = "role_id=" . trim($_GET['role_id']);
		}else{
			$where['role_id'] = array('in',$idArray);
		}
		if($_GET['month']){
			//本月时间戳范围
			$month_start_time = strtotime(date($_GET['year'].'-'.$_GET['month'].'-01')); 
			$month_end_time = strtotime($_GET['year']."-".$_GET['month']."-".date("t",$month_start_time))+86400;
			$month_time = array('between',array($month_start_time,$month_end_time));
			$where['start_time'] = $month_time;
			$params[] = "year=" . trim($_GET['year']);
			$params[] = "month=" . trim($_GET['month']);
		}else{
			$where['start_time'] = array('between',array($start_time,$end_time));
		}
		//是否接通
		if($_GET['is_talk']){
			$where['talk_time'] = array('gt',0);
			$params[] = "is_talk=" . trim($_GET['is_talk']);
		}
		//呼叫类型（1呼入2呼出）
		if($_GET['call_type']){
			$where['call_type'] = intval($_GET['call_type']);
			$params[] = "call_type=" . intval($_GET['call_type']);
		}

		if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = 15;
			$params[] = "listrows=".$listrows;
		}
		$p = intval($_GET['p'])?intval($_GET['p']):1;
		$where['call_status'] = 3;

		$count = $m_call_record->where($where)->count();
		$p_num = ceil($count/$listrows);
		if($p_num<$p){
			$p = $p_num;
		}
		$list = $m_call_record->where($where)->order('start_time desc')->page($p.','.$listrows)->select();
		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		
		$this->parameter = implode('&', $params);
		$weekarray=array("日","一","二","三","四","五","六");

		//获取域名信息，确定上传路径
		$server_name = $_SERVER['SERVER_NAME'];
		$dir = '';
		if (strpos($server_name,'xxx.com')) {
			//云平台路径
			$dir = str_replace('.xxx.com', '', $server_name);
		} elseif ($server_name == 'localhost') {
			// $dir = 'localhost'; //测试时用
			$dir = '';
		} else {
			$dir = $server_name;
		}

		$m_customer = M('Customer');
		$m_contacts = M('Contacts');
		$m_r_contacts_customer = M('RContactsCustomer');
		$m_leads = M('Leads');
		foreach ($list as $k => $v) {
			$user_info = array();
			$user_info = getUserByRoleId($v['role_id']);
			$list[$k]['user'] = $user_info;
			$list[$k]['date'] = date('Y-m-d',$v['start_time']);
			$list[$k]['time'] = date('H:i:s',$v['start_time']);
			$list[$k]['week'] = "星期".$weekarray[date("w",$v['start_time'])];
			$list[$k]['talk_time'] = $v['talk_time'] ? getTimeBySec($v['talk_time']) : '未接通';
			$list[$k]['dial_time'] = $v['dial_time'] ? getTimeBySec($v['dial_time']) : '';
			switch ($v['call_type']) {
				case 1: $call_type = '呼入'; break;
				case 2: $call_type = '呼出'; break;
			}
			$list[$k]['call_type'] = $call_type;
			$dir_date = '';
			if ($v['talk_time'] && $v['call_upload'] == 1 && $dir) {
				if ($v['file_path']) {
					//阿里云服务器
					$list[$k]['file_path'] = 'https://wukongtest.oss-cn-hangzhou.aliyuncs.com/'.$v['file_path'];
				} else {
					$file_date = '';
					$file_date = date('Ymd',substr($v['session_id'], 10));
					$list[$k]['file_path'] = 'https://wukongtest.oss-cn-hangzhou.aliyuncs.com/'.$server_name.'/'.$file_date.'/'.md5(md5($v['session_id'],'_KAKAROTEpdcrm')).'.wav';
				}
			} else {
				// $list[$k]['file_path'] = '';
			}
			$model_name = '';
			$contacts_name = '';
			$contacts_arr = array();
			$url = '';
			if ($v['model'] && $v['model_id']) {
				switch ($v['model']) {
					case 'customer' : 
						$model_name = $m_customer->where(array('customer_id'=>$v['model_id']))->getField('name');
						// $contacts_arr = $m_r_contacts_customer->where('customer_id = %d',$v['model_id'])->getField('contacts_id',true);
						// if ($contacts_arr) {
						// 	$contacts_name = $m_contacts->where(array('telephone'=>$v['telephone'],'contacts_id'=>array('in',$contacts_arr)))->getField('name');
						// }
						break;
					case 'leads' :
						$model_name = $m_leads->where(array('leads_id'=>$v['model_id']))->getField('name');
						break;
				}
				$url = U($v['model']."/view","id=".$v['model_id']);
			}
			$list[$k]['model_name'] = $model_name;
			$list[$k]['contacts_name'] = $contacts_name;
			$list[$k]['url'] = $url;
		}
		
		//时间插件处理（计算开始、结束时间距今天的天数）
		$daterange = array();
		//上个月
		$daterange[0]['start_day'] = (strtotime(date('Y-m-d',time()))-strtotime(date('Y-m-d', mktime(0,0,0,date('m')-1,1,date('Y')))))/86400;
		$daterange[0]['end_day'] = (strtotime(date('Y-m-d',time()))-strtotime(date('Y-m-01 00:00:00')))/86400;
		//本月
		$daterange[1]['start_day'] = (strtotime(date('Y-m-d',time()))-strtotime(date('Y-m-01 00:00:00')))/86400;
		$daterange[1]['end_day'] = 0;
		//上季度
		$month = date('m');
		if($month==1 || $month==2 ||$month==3){
			$year = date('Y')-1;
			$daterange_start_time = strtotime(date($year.'-10-01 00:00:00'));
			$daterange_end_time = strtotime(date($year.'-12-31 23:59:59'));
		}elseif($month==4 || $month==5 ||$month==6){
			$daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
		}elseif($month==7 || $month==8 ||$month==9){
			$daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
		}else{
			$daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
		}
		$daterange[2]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
		$daterange[2]['end_day'] = (strtotime(date('Y-m-d',time()))-$daterange_end_time-1)/86400;
		//本季度
		$month=date('m');
		if($month==1 || $month==2 ||$month==3){
			$daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
		}elseif($month==4 || $month==5 ||$month==6){
			$daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
		}elseif($month==7 || $month==8 ||$month==9){
			$daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
		}else{
			$daterange_start_time = strtotime(date('Y-10-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-12-31 23:59:59"));
		}
		$daterange[3]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
		$daterange[3]['end_day'] = 0;
		//上一年
		$year = date('Y')-1;
		$daterange_start_time = strtotime(date($year.'-01-01 00:00:00'));
		$daterange_end_time = strtotime(date('Y-01-01 00:00:00'));
		$daterange[4]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
		$daterange[4]['end_day'] = (strtotime(date('Y-m-d',time()))-$daterange_end_time)/86400;
		//本年度
		$daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
		$daterange[5]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
		$daterange[5]['end_day'] = 0;
		$this->daterange = $daterange;	
		//部门岗位
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission')->where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('RoleDepartment')->select();
		}else{
			$departmentList = M('RoleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);
		$roleList = array();
		foreach($below_ids as $roleId){
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->roleList = $roleList;
		$this->list = $list;
		$this->role_list = $role_list;
		$this->start_date = date('Y-m-d',$start_time);
		$this->end_date = date('Y-m-d',$end_time);
		$this->listrows = $listrows;
		$this->assign('count',$count);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		$this->alert = parseAlert();
		$this->display();
	}

	/**
	 * 呼叫中心(用户开启呼叫功能)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function user () {
		$m_user = M('User');
		if ($this->isAjax()) {
			if (!session('?admin') && !checkPerByAction('user','edit')) {
				$this->ajaxReturn('','您没有此权限！',0);
			}
			$user_ids = $_POST['user_id'];
			if (!$user_ids) {
				$this->ajaxReturn('','请先选择需操作的用户！',0);
			}
			$del_ids = array();
			$add_ids = array();
			//呼叫中心授权人数限制
			$call_setting = C('CALL_SETTING');
			$call_num = $call_setting['NUM'];
			$call_error = false;
			$new_call_num = 0;
			$new_call_num = $m_user->where(array('call_status'=>1))->count();
			foreach ($user_ids as $k=>$v) {
				$call_status = 0;
				$call_status = $m_user->where(array('user_id'=>$v))->getField('call_status');
				if(empty($call_status) && $new_call_num < $call_num){
					$new_call_num++;
					$add_ids[] = $v;
				}else{
					// if ($new_call_num >= $call_num) {
					// 	$call_error = true;
					// }
					$del_ids[] = $v;
				}
			}
			if($add_ids){
				$res_add = $m_user->where(array('user_id'=>array('in',$add_ids)))->setField('call_status',1);
			}
			if($del_ids){
				$res_del = $m_user->where(array('user_id'=>array('in',$del_ids)))->setField('call_status',0);
			}
			if($res_add || $res_del){
				if ($call_error !== false) {
					$this->ajaxReturn('','已超出授权人数，操作失败！',0);
				} else {
					$this->ajaxReturn('','操作成功！',1);
				}
			}else{
				if ($call_error !== false) {
					$this->ajaxReturn('','已超出授权人数，操作失败！',0);
				} else {
					$this->ajaxReturn('','操作失败，请重试！',0);
				}
			}
		}else{
			$this->ajaxReturn('','您没有此权限！',0);
		}
	}

	/**
	 * 呼叫中心(通话记录统计分析)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function analytics () {
		$m_call_record = M('CallRecord');
		$m_user = M('User');
		//权限范围
		$below_ids = getPerByAction(MODULE_NAME,ACTION_NAME);
		if (intval($_GET['role'])) {
			$params[] = "role=" . intval($_GET['role']);
			$role_id_array = array(intval($_GET['role']));
		} else {
			if (intval($_GET['department'])) {
				$params[] = "department=" . intval($_GET['department']);
				$department_id = intval($_GET['department']);
				foreach(getRoleByDepartmentId($department_id, true) as $k=>$v){
					$role_id_array[] = $v['role_id'];
				}
			} else {
				$role_id_array = getSubRoleId(true, 1);
			}
		}
		//过滤权限范围内的role_id
		if ($role_id_array) {
			//数组交集
			$idArray = array_intersect($role_id_array,$below_ids);
		} else {
			$idArray = getPerByAction(MODULE_NAME,ACTION_NAME,false);
		}

		//时间段搜索
		if ($_GET['between_date']) {
			$between_date = explode(' - ',trim($_GET['between_date']));
			if($between_date[0]){
				$start_time = strtotime($between_date[0]);
			}
			$end_time = $between_date[1] ?  strtotime(date('Y-m-d 23:59:59',strtotime($between_date[1]))) : strtotime(date('Y-m-d 23:59:59',time()));
		} else {
			$start_time = strtotime(date('Y-m-01 00:00:00'));
			$end_time = strtotime(date('Y-m-d H:i:s'));
		}
		$this->between_date = $_GET['between_date'] ? trim($_GET['between_date']) : date('Y-m-01').' - '.date('Y-m-d');
		$this->start_date = date('Y-m-d',$start_time);
		$this->end_date = date('Y-m-d',$end_time);

		//有呼叫授权的或有呼叫记录的role_id
		$call_role_ids = M('User')->where(array('status'=>1,'call_status'=>1))->getField('role_id',true);

		$call_group_role_ids = $m_call_record->where(array('start_time'=>array('between',array($start_time,$end_time))))->group('role_id')->getField('role_id',true);
		//数组并集，去重
		if ($call_group_role_ids) {
			$new_call_role_ids = array_merge($call_role_ids,$call_group_role_ids);
		} else {
			$new_call_role_ids = $call_role_ids;
		}
		$new_call_role_ids = array_unique($new_call_role_ids);
		$search_call_role_ids = array();
		foreach ($new_call_role_ids as $k=>$v) {
			if (in_array($v,$idArray)) {
				$search_call_role_ids[] = $v;
			}
		}

		$count = $m_user->where(array('role_id'=>array('in', $search_call_role_ids), 'status'=>1))->count();
		$p = $_GET['p'] ? intval($_GET['p']) : 1;
		$listrows = $_GET['listrows'] ? intval($_GET['listrows']) : 15;
		$params[] = "listrows=" . $listrows;
		$p_num = ceil($count/$listrows); 
		if ($p_num < $p) {
			$p = $p_num;
		}
		import("@.ORG.Page");
		//分页功能
		if (trim($_GET['act']) == 'excel') {
			$role_list = $m_user->where(array('role_id'=>array('in', $search_call_role_ids), 'status'=>1))->field('role_id,full_name,thumb_path')->select();
		} else {
			$role_list = $m_user->where(array('role_id'=>array('in', $search_call_role_ids), 'status'=>1))->page($p.','.$listrows)->field('role_id,full_name,thumb_path')->select();
		}

		$call_total = array();
		$status_a_total = '0'; //总通话数
		$status_b_total = '0'; //接通数
		$status_c_total = '0'; //接通率
		$status_d_total = '0'; //总通话时长
		$status_e_total = '0'; //最大通话时长
		$status_f_total = '0'; //外呼总数
		$status_g_total = '0'; //外呼接通数
		$status_h_total = '0'; //外呼接通率
		$status_i_total = '0'; //外呼通话总时长
		$status_j_total = '0'; //外呼通话平均时长
		foreach ($role_list as $k=>$v) {
			$status_a = '0'; //
			$status_b = '0'; //
			$status_c = '0'; //
			$status_d = '0'; //
			$status_e = '0'; //
			$status_f = '0'; //
			$status_g = '0'; //
			$status_h = '0'; //
			$status_i = '0'; //
			$status_j = '0'; //
			$call_list = array();

			$call_list = $m_call_record->where(array('role_id'=>$v['role_id'],'start_time'=>array('between',array($start_time,$end_time)),'call_status'=>array('eq',3)))->select();

			foreach ($call_list as $key=>$val) {
				$status_a++;
				if ($val['talk_time'] > 0) {
					$status_b++;
					$status_d += $val['talk_time'];
					if ($val['call_type'] == 2) {
						$status_g++;
						$status_i += $val['talk_time'];
					}
				}
				if ($val['call_type'] == 2) {
					$status_f++;
				}
			}
			$status_c = $status_b ? round(($status_b/$status_a),2)*100 : '0';
			$status_e = $m_call_record->where(array('role_id'=>$v['role_id'],'start_time'=>array('between',array($start_time,$end_time)),'call_status'=>array('eq',3)))->max('talk_time');
			$status_h = $status_g ? round(($status_g/$status_f),2)*100 : '0';
			if ($status_i > $status_g) {
				$status_j = round($status_i/$status_g);
			} else {
				$status_j = '0';
			}

			$role_list[$k]['status_a'] = $status_a;
			$role_list[$k]['status_b'] = $status_b;
			$role_list[$k]['status_c'] = $status_c;
			$role_list[$k]['status_d'] = $status_d ? getTimeBySec($status_d) : '0';
			$role_list[$k]['status_e'] = $status_e ? getTimeBySec($status_e) : '0';
			$role_list[$k]['status_f'] = $status_f;
			$role_list[$k]['status_g'] = $status_g;
			$role_list[$k]['status_h'] = $status_h;
			$role_list[$k]['status_i'] = $status_i ? getTimeBySec($status_i) : '0';
			$role_list[$k]['status_j'] = $status_j ? getTimeBySec($status_j) : '0';
		}

		//由于分页原因，合计需单独查
		$status_list = $m_call_record->where(array('role_id'=>array('in', $search_call_role_ids),'start_time'=>array('between',array($start_time,$end_time)),'call_status'=>array('eq',3)))->select();

		foreach ($status_list as $key=>$val) {
			$status_a_total++;
			if ($val['talk_time'] > 0) {
				$status_b_total++;
				$status_d_total += $val['talk_time'];
				if ($val['call_type'] == 2) {
					$status_g_total++;
					$status_i_total += $val['talk_time'];
				}
			}
			if ($val['call_type'] == 2) {
				$status_f_total++;
			}
		}
		$status_c_total = $status_b_total ? round(($status_b_total/$status_a_total),2)*100 : '0';
		$status_e_total = $m_call_record->where(array('role_id'=>array('in', $search_call_role_ids),'start_time'=>array('between',array($start_time,$end_time)),'call_status'=>array('eq',3)))->max('talk_time');
		$status_h_total = $status_g_total ? round(($status_g_total/$status_f_total),2)*100 : '0';
		if ($status_i_total > $status_g_total) {
			$status_j_total = round($status_i_total/$status_g_total);
		} else {
			$status_j_total = '0';
		}

		$call_total = array(
							'status_a_total'=>$status_a_total ? : '0',
							'status_b_total'=>$status_b_total ? : '0',
							'status_c_total'=>$status_c_total ? : '0',
							'status_d_total'=>$status_d_total ? getTimeBySec($status_d_total) : '0',
							'status_e_total'=>$status_e_total ? getTimeBySec($status_e_total) : '0',
							'status_f_total'=>$status_f_total ? : '0',
							'status_g_total'=>$status_g_total ? : '0',
							'status_h_total'=>$status_h_total ? : '0',
							'status_i_total'=>$status_i_total ? getTimeBySec($status_i_total) : '0',
							'status_j_total'=>$status_j_total ? getTimeBySec($status_j_total) : '0'
							);

		if (trim($_GET['act']) == 'excel') {
			session('analy_export_status', 1);
			$this->analyExcelExport($role_list,$call_total);
		}

		$this->role_list = $role_list;
		$this->call_total = $call_total;

		$Page = new Page($count,$listrows);
		$this->count = $count;
		$this->assign('count',$count);
		$this->parameter = implode('&', $params);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		$this->listrows = $listrows;

		//时间插件处理（计算开始、结束时间距今天的天数）
		$daterange = array();
		//上个月
		$daterange[0]['start_day'] = (strtotime(date('Y-m-d',time()))-strtotime(date('Y-m-d', mktime(0,0,0,date('m')-1,1,date('Y')))))/86400;
		$daterange[0]['end_day'] = (strtotime(date('Y-m-d',time()))-strtotime(date('Y-m-01 00:00:00')))/86400;
		//本月
		$daterange[1]['start_day'] = (strtotime(date('Y-m-d',time()))-strtotime(date('Y-m-01 00:00:00')))/86400;
		$daterange[1]['end_day'] = 0;
		//上季度
		$month = date('m');
		if($month==1 || $month==2 ||$month==3){
			$year = date('Y')-1;
			$daterange_start_time = strtotime(date($year.'-10-01 00:00:00'));
			$daterange_end_time = strtotime(date($year.'-12-31 23:59:59'));
		}elseif($month==4 || $month==5 ||$month==6){
			$daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
		}elseif($month==7 || $month==8 ||$month==9){
			$daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
		}else{
			$daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
		}
		$daterange[2]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
		$daterange[2]['end_day'] = (strtotime(date('Y-m-d',time()))-$daterange_end_time-1)/86400;
		//本季度
		$month=date('m');
		if($month==1 || $month==2 ||$month==3){
			$daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
		}elseif($month==4 || $month==5 ||$month==6){
			$daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
		}elseif($month==7 || $month==8 ||$month==9){
			$daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
		}else{
			$daterange_start_time = strtotime(date('Y-10-01 00:00:00'));
			$daterange_end_time = strtotime(date("Y-12-31 23:59:59"));
		}
		$daterange[3]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
		$daterange[3]['end_day'] = 0;
		//上一年
		$year = date('Y')-1;
		$daterange_start_time = strtotime(date($year.'-01-01 00:00:00'));
		$daterange_end_time = strtotime(date('Y-01-01 00:00:00'));
		$daterange[4]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
		$daterange[4]['end_day'] = (strtotime(date('Y-m-d',time()))-$daterange_end_time)/86400;
		//本年度
		$daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
		$daterange[5]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
		$daterange[5]['end_day'] = 0;
		$this->daterange = $daterange;

		$roleList = array();
		foreach($search_call_role_ids as $roleId){				
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->roleList = $roleList;
		$departmentArray = D('RoleView')->where(array('role.role_id'=>array('in',$search_call_role_ids)))->group('department_id')->getField('department_id',true);
		$departmentList = M('roleDepartment')->where(array('department_id'=>array('in',$departmentArray)))->select();
		$this->assign('departmentList', $departmentList);
		$this->alert = parseAlert();
		$this->display();
	}

	/**
	 * 电话统计分析导出
	 * @param 
	 * @author 
	 * @return 
	 */
	public function analyExcelExport($callList=false,$call_total){
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("pdcrm");    
		$objProps->setLastModifiedBy("pdcrm");    
		$objProps->setTitle("pdcrm callAnalytics");    
		$objProps->setSubject("pdcrm callAnalytics");    
		$objProps->setDescription("pdcrm callAnalytics");    
		$objProps->setKeywords("pdcrm callAnalytics");    
		$objProps->setCategory("pdcrm");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 

		$excel_title = array();
		$field_arr = array();
		$excel_title = array('总通话数(个)','接通数(个)','接通率(%)','总通话时长','最大通话时长','外呼总数(个)','外呼接通数(个)','外呼接通率(%)','外呼通话总时长','外呼通话平均时长');

		$j = 0;
		foreach($excel_title as $title){
			$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($j); //生成Excel
			$objActSheet->setCellValue($pCoordinate.'1', $title);
			$j++;
        }
		$list = $callList;

		$objActSheet->setCellValue('A2', '合计');
		$objActSheet->setCellValue('B2', $call_total['status_a_total']);
		$objActSheet->setCellValue('C2', $call_total['status_b_total']);
		$objActSheet->setCellValue('D2', $call_total['status_c_total']);
		$objActSheet->setCellValue('E2', $call_total['status_d_total']);
		$objActSheet->setCellValue('F2', $call_total['status_e_total']);
		$objActSheet->setCellValue('G2', $call_total['status_f_total']);
		$objActSheet->setCellValue('H2', $call_total['status_g_total']);
		$objActSheet->setCellValue('I2', $call_total['status_h_total']);
		$objActSheet->setCellValue('J2', $call_total['status_i_total']);
		$objActSheet->setCellValue('K2', $call_total['status_j_total']);
		
		$i = 2;
		foreach ($list as $k => $v) {
			$i++;
			$objActSheet->setCellValue('A'.$i, $v['full_name']);
			$objActSheet->setCellValue('B'.$i, $v['status_a']);
			$objActSheet->setCellValue('C'.$i, $v['status_b']);
			$objActSheet->setCellValue('D'.$i, $v['status_c']);
			$objActSheet->setCellValue('E'.$i, $v['status_d']);
			$objActSheet->setCellValue('F'.$i, $v['status_e']);
			$objActSheet->setCellValue('G'.$i, $v['status_f']);
			$objActSheet->setCellValue('H'.$i, $v['status_g']);
			$objActSheet->setCellValue('I'.$i, $v['status_h']);
			$objActSheet->setCellValue('J'.$i, $v['status_i']);
			$objActSheet->setCellValue('K'.$i, $v['status_j']);
		}		
		$current_page = intval($_GET['current_page']) ? : '';
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=pdcrm_callanalytics_".date('Y-m-d',mktime())."_".$current_page.".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
		session('analy_export_status', 0);
	}

	public function getCurrentStatus(){
		$this->ajaxReturn(intval(session('analy_export_status')), 'success', 1);
	}

	/**
	 * 电话录音上传
	 * @param 
	 * @author 
	 * @return 
	 */
	public function upload(){
		//验证
		if(!$this->checkVerify()){
			$this->ajaxReturn('1','未通过合法性验证！',0);
		}
		if (!empty($_FILES)) {
			if (isset($_FILES['file']['size']) && $_FILES['file']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 500000000;
				$dirname = UPLOAD_PATH . 'call/'. date('Ym', time()).'/'.date('d', time()).'/';

				$allow_file_type = 'wav,mp3'; //上传文件类型
				$upload->allowExts  = explode(',', $allow_file_type);
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					$this->ajaxReturn('','附件上传目录不可写！',0);
				}
				$upload->savePath = $dirname;
				if (!$upload->upload()) {
					$this->ajaxReturn('',$upload->getErrorMsg().$upload->getErrorMsg(),0);
				} else {
					$info = $upload->getUploadFileInfo();
				}
			} else {
				$this->ajaxReturn('2','上传失败！',0);
			}
			$m_file = M('File');
			if (is_array($info[0]) && !empty($info[0])) {
				//根据上传文件名，查找相关通话记录，更新
				$m_call_record = M('CallRecord');
				// $call_record_info = $m_call_record->where(array('session_id'=>$info[0]['name']))->find();
				$call_record_info = $m_call_record->where(array('session_id'=>trim($_POST['session_id'])))->find();
				if (!$call_record_info['file_path']) {
					$data = array();
					$data['file_path'] = $info[0]['savepath'] . $info[0]['savename'];
					$data['size'] = $info[0]['size'];
					$data['file_name'] = $info[0]['name'];
					// $res_call = $m_call_record->where(array('session_id'=>substr($info[0]['name'],0,-4)))->save($data);
					$res_call = $m_call_record->where(array('session_id'=>trim($_POST['session_id'])))->save($data);
					if ($res_call) {
						$this->ajaxReturn('','上传成功！',1);
					} else {
						$this->ajaxReturn('','上传失败！',0);
					}
				}
			} else {
				$this->ajaxReturn('3','上传失败！',0);
			}
		} else {
			$this->ajaxReturn('4','上传失败！',0);
		}
	}
	
	/**
	 * 合法性验证
	 * @param 
	 * @author 
	 * @return 
	 */
	private function checkVerify(){
		// $parmList = $_REQUEST;
		$parmList['client_sign'] = $clientSign = trim($_POST['client_sign']);
		$parmList['send_time'] = $timestamp = trim($_POST['send_time']);
		$parmList['session_id'] = trim($_POST['session_id']);
		//验证session_id 是否存在
		$res_call = M('CallRecord')->where(array('session_id'=>trim($_POST['session_id'])))->find();
		if (!$res_call) {
			$this->ajaxReturn(0,'参数错误!',0);
		}
		//是否重复上传

		if($clientSign && time() - $timestamp < 6000*10){
			unset($parmList['client_sign']);
			if(count($parmList) > 0){

				// 获取签名参数值
				//$client_sign = $_REQUEST[$clientSign];
				// 验证请求合法性
				// 对要签名参数按照签名格式组合

				foreach ($parmList as $key=>$value){
					if (isset($_REQUEST[$key])) {
						$parame[$key] = $key.'='.$_REQUEST[$key];
					}else{
						return false;
					}
				}
				ksort ($parame);

				$returnValue = implode ( "&", $parame);

// echo $returnValue; echo '  ';
				if ($returnValue) {
// echo hash_hmac("sha1", $returnValue, 'pdcrm@2018#sdSDF54fjH==', $raw_output=false); echo '  ';
					$signCalc = base64_encode(hash_hmac("sha1", $returnValue, 'pdcrm@2018#sdSDF54fjH==', $raw_output=false));
//  var_dump($signCalc) ;echo '  ';
// var_dump($clientSign) ;die(); 
					// 检查参数签名是否一致
					if (trim($clientSign) != trim($signCalc)) {
						return false;
					}else{
						return true;
					}
				}else{
					return false;
				}

			}else{
				return false;
			}
		}else{
			if($clientSign){
				$this->ajaxReturn(0,'参数错误!',0);
			}else{
				$this->ajaxReturn(0,'请求超时!',0);
			}

		}
	}

	/**
	 * 阿里云服务器上传返回值
	 * @param 
	 * @author 
	 * @return 
	 */
	public function aliOssKey(){
		// 验证
		if (!$this->checkVerify()) {
			$this->ajaxReturn(0, '未通过合法性验证！', 0);
		}

		//获取域名信息，确定上传路径
		$server_name = $_SERVER['SERVER_NAME'];
		
		if (strpos($server_name,'xxx.com')) {
			//云平台路径
			$dir = str_replace('.xxx.com', '', $server_name).'/'.date('Ymd').'/';
		// } elseif ($server_name == 'localhost') {
		// 	$dir = 'nct.pdcrm.net/'.date('Ymd').'/'; // 测试时用
		} else {
			$dir = $server_name.'/'.date('Ymd').'/';
		}

		if (!$dir) {
			$this->ajaxReturn('参数错误！','error',0);
		}

		// 回调地址
		$callbackUrl = 'http://'.$server_name.$_SERVER["SCRIPT_NAME"]."?m=call&a=aliOssKeyCallBack";
		
		$d_file = D('file');
		$response = aliOssToken($d_file::RECORD_URL, $callbackUrl, $dir);
		
	    $this->ajaxReturn($response,'',1);
	}



	/**
	 * 阿里云服务器上传返回值
	 * @param 
	 * @author 
	 * @return 
	 */
	public function aliOssKeyCallBack(){
		//验证
		// if(!$this->checkVerify()){
		// 	$this->ajaxReturn(0,'未通过合法性验证！',0);
		// }

		header('Content-type: text/json');
		$data['post'] = $_POST;
		$data['status'] = 1;
		// $data['get'] = $_GET;
		// $data['server'] = $_SERVER;
		echo json_encode($data);
	}

	/**
	 * 呼叫中心(阿里云OSS录音文件路径)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function ossRecord () {
		//验证
		// if(!$this->checkVerify()){
		// 	$this->ajaxReturn(0,'未通过合法性验证！',0);
		// }
		if ($this->isPost()) {
			$session_id = trim($_POST['session_id']);
			$file_path = trim($_POST['file_name']);
			$file_size = trim($_POST['size']);
			$file_name = trim($_POST['file_name']);

			$call_upload = intval($_POST['call_upload']) ? : 1;

			if ($session_id && $file_path) {
				//限制上传总大小
				// if ($call_upload) {

				// }

				$m_call_record = M('CallRecord');
				$record_info = $m_call_record->where(array('session_id'=>$session_id))->find();
				if ($record_info) {
					$data = array();
					$data['session_id'] = $session_id;
					$data['file_path'] = $file_path;
					$data['size'] = $file_size;
					$data['file_name'] = $file_name;
					$data['call_upload'] = $call_upload;
					$m_call_record->where(array('id'=>$record_info['id']))->save($data);
					$this->ajaxReturn('','success',1);
				} else {
					$this->ajaxReturn('','参数错误！',0);
				}
			} else {
				$this->ajaxReturn('','参数错误！',0);
			}
		}
	}
	
}