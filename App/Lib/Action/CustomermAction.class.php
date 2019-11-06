<?php
include_once "jpush.php";
include_once "JMessage.php";
include_once "JMessageupload.php";
class CustomermAction extends Action {

	/**
	*  用于判断权限
	*  @permission 无限制
	*  @allow 登录用户可访问
	*  @other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('validate','check','edit','checkinfo','getcustomeroriginal','close','getcurrentstatus','excelimportact','clistdialog','edit_ajax','settop','yc_share','share_list','ajax_share','sendsms','nearby_map', 'top_10', 'new_add')
		);
		B('Authenticate', $action);
		$this->alert = parseAlert();
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);

        header('Access-Control-Allow-Origin: https://api.im.jpush.cn');
        // 响应类型
        header('Access-Control-Allow-Methods:POST,GET');
        // 带 cookie 的跨域访问
        header('Access-Control-Allow-Credentials: true');
        // 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,Content-Type,X-CSRF-Token');
	}
	/**
	*字段查重
	**/
	public function checkinfo(){
		if($this->isAjax()){
			$field_value = trim($_POST['field_value']);
			$field_name= trim($_POST['field_name']);
			$customer_id = intval($_POST['customer_id']);
			$m_customer = M('Customer');
			$where[$field_name] = $field_value;
			if($customer_id){
				$where['customer_id'] = array('neq',$customer_id);
			}
			$where['is_deleted'] = 0;
			//判断是否存在，如存在获取负责人
			$info = $m_customer ->where($where)->field('name,owner_role_id,creator_role_id,update_time,customer_id')->find();
			if($info){
				$owner_role_name = M('user')->where('role_id =%d',$info['owner_role_id'])->getField('full_name');
				$message = '该数据已存在<a target="_blank" title="点击查看" href="'.$url.'">客户：'.$info['name'].'</a>中！负责人为:'.$owner_role_name;
				$this->ajaxReturn($message,'客户重复！',1);
			}else{
				$this->ajaxReturn(0,'为空！',0);
			}
		}
	}
	/**
	*  Ajax检测客户名称
	*
	**/
	public function check(){
		if($_REQUEST['customer_id']){
			$where['customer_id'] = array('neq',$_REQUEST['customer_id']);
		}
		import("@.ORG.SplitWord");
		$sp = new SplitWord();
		$m_customer = M('customer');
		$useless_words = array(L('COMPANY'),L('LIMITED'),L('DI'),L('LIMITED_COMPANY'));
		if ($this->isAjax()) {
			$split_result = $sp->SplitRMM($_POST['name']);
			if(!is_utf8($split_result)) $split_result = iconv("GB2312//IGNORE", "UTF-8", $split_result) ;
			$result_array = explode(' ',trim($split_result));
            if(count($result_array) < 2){
                $this->ajaxReturn(0,'',0);
                die;
            }
			foreach($result_array as $k=>$v){
				if(in_array($v,$useless_words)) unset($result_array[$k]);
			}
			$where['is_deleted'] = 0;
			$name_list = $m_customer->where($where)->getField('name', true);
			$seach_array = array();
			foreach($name_list as $k=>$v){
				$search = 0;
				foreach($result_array as $k2=>$v2){
					if(strpos($v, $v2) > -1){
						$v = str_replace("$v2","<span style='color:red;'>$v2</span>", $v, $count);
						$search += $count;
					}
				}
				if($search > 0) $seach_array[$k] = array('value'=>$v,'search'=>$search);
			}
			$seach_sort_result = array_sort($seach_array,'search','desc');
			if(empty($seach_sort_result)){
				$this->ajaxReturn(0,L('ABLE_ADD'),0);
			}else{
				$this->ajaxReturn($seach_sort_result,L('CUSTOMER_IS_CREATED'),1);
			}
		}
	}

	/**
	*  Ajax检测客户唯一字段
	*
	**/
	public function validate() {
		if($this->isAjax()){
            if(!$this->_request('clientid','trim') || !$this->_request($this->_request('clientid','trim'),'trim')) $this->ajaxReturn("","",3);
            $field = M('Fields')->where('model = "customer" and field = "%s"', $this->_request('clientid','trim'))->find();
            $m_customer = $field['is_main'] ? D('Customer') : D('CustomerData');
            $where[$this->_request('clientid','trim')] = array('eq',$this->_request($this->_request('clientid','trim'),'trim'));
            if($this->_request('id','intval',0)){
                $where[$m_customer->getpk()] = array('neq',$this->_request('id','intval',0));
            }
			if($this->_request('clientid','trim')) {
				if ($m_customer->where($where)->find()) {
					$this->ajaxReturn("","",1);
				} else {
					$this->ajaxReturn("","",0);
				}
			}else{
				$this->ajaxReturn("","",0);
			}
		}
	}

	/**
	*  放入客户池
	*
	**/
	public function remove(){
		$m_customer = M('Customer');
		$customer_ids =$_POST['customer_id'];
		if($this->isPost()){
			$customer_ids = is_array($_POST['customer_id']) ? implode(',', $_POST['customer_id']) : '';
			if('' == $customer_ids){
				alert('error', L('NOT_CHOOSE_ANY'), $_SERVER['HTTP_REFERER']);
			}
			$lock_names = $m_customer->where('customer_id in (%s) and is_locked = 1',$customer_ids)->getField('name',true);
			if($lock_names){
				$customers = implode(' , ',$lock_names);
				alert('error','客户('.$customers.')已被锁定，不能放入客户池！',$_SERVER['HTTP_REFERER']);
			}
			if($m_customer->where('customer_id in (%s)', $customer_ids)->setField('owner_role_id',0)){
				$m_action_record = M('action_record');
				$customer_arr = explode(',',$customer_ids);
				foreach($customer_arr as $k=>$v){
					add_record('放入客户池','将此客户放入客户池！','customer',$v);
				}
				alert('success', L('BATCH_INTO_THE_SUCCESSFUL_CUSTOMER_POOL'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('BATCH_INTO_THE_CUSTOMER_POOL_FAILURE'), $_SERVER['HTTP_REFERER']);
			}
		}
	}

	/**
	*  客户池领取/分配操作
	*
	**/
	public function receive(){
		$m_customer = M('Customer');
		$m_config = M('Config');
		$m_customer_record = M('CustomerRecord');
		if(!empty($_POST['owner_role_id'])){
			$owner_role_id = $_POST['owner_role_id'];
		}elseif(!empty($_POST['owner_role'])){
			$owner_role_id = $_POST['owner_role'];
		}else{
			$owner_role_id = session('role_id');
		}
		$data['owner_role_id'] = $owner_role_id;
		$data['update_time'] = time();
		$data['get_time'] = time();
		//是否分配需要提醒
		$need_alert = false;
		//单个领取
		if($this->isPost()){
			$customer_name = array();
			$customer_ids = $_POST['customer_id'];

			/*if(!$customer_ids){
				alert('error', L('NO_CHANCE_CUSTOMER'), $_SERVER['HTTP_REFERER']);
			}*/
			//是否批量操作 否的话是单个分配
			if(is_array($customer_ids) || $customer_ids){
				$opennum = $m_config->where('name="opennum"')->getField('value');
				if($opennum){
					$outdays = $m_config->where('name="customer_outdays"')->getField('value');
					$outdate = empty($outdays) ? time() : time()-86400*$outdays;

					$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
					$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
					$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
					$openrecycle = $m_config -> where('name="openrecycle"')->getField('value');

					if ($openrecycle == 2) {
						$c_where['_string'] = '(update_time > '.$outdate.' AND get_time > '.$contract_outdays.') OR is_locked = 1';
					}

					$c_where['owner_role_id'] = $owner_role_id;
					$c_where['customer_status'] = '意向客户';
					$customer_count = M('customer')->where($c_where)->count();
					$customer_num = M('user')->where('role_id =%d',session('role_id'))->getField('customer_num');
					if($customer_count >= $customer_num){
						alert('error', '此负责人的客户数量已超出限制！操作失败', U('customer/index'));
					}else{
						$sy_count = $customer_num - $customer_count ;
						$cus_counts = count($customer_ids);
						if($cus_counts > $sy_count){
							alert('error', '此负责人的最多可领取或被分配'.$sy_count.'客户！操作失败', U('customer/index'));
						}
					}
				}

				//检查用户是否符合领取客户池资源资格
				//判断领取或分配  operating_type  receive:领取  assign:分配
				$customer_limit_counts = $m_config->where('name = "customer_limit_counts"')->getField('value');
                $customer_record_count = $this->check_customer_limit(session('user_id'), 1);
				if(sizeof($customer_ids) + $customer_record_count <= $customer_limit_counts){
					if($_POST['operating_type'] == 'receive'){
						if($customer_record_count >= $customer_limit_counts){
							alert('error', L('GET_THE_FAILURE_OVER_GET'), $_SERVER['HTTP_REFERER']);
						}
					}
				}else{
					alert('error', L('GET_THE_FAILURE_OVER_GET_LIMIT',array($customer_limit_counts)),$_SERVER['HTTP_REFERER']);
				}
				$where['update_time'] = array('lt',(time()-86400));
                /*$where['customer_id'] = array('in',implode(',',$customer_ids));*/
                $where['customer_id'] = $customer_ids;
				$where['owner_role_id'] = array('gt',0);

				$updated_owner = $m_customer->where($where)->save($data);
				unset($where['update_time']);
				$where['owner_role_id'] = array('eq',0);
				$customer_name = $m_customer->where($data)->getField('name', true);
				$updated_time = $m_customer->where($where)->save($data);

				//是否操作成功
				if($updated_owner || $updated_time){
					//增加customer_record记录
					$m_user = M('user');
					$user_id = $m_user->where('role_id = %d', $owner_role_id)->getField('user_id');
					$user_name = $m_user->where('role_id = %d', $owner_role_id)->getField('full_name');
					$info['start_time'] = time();
					foreach($customer_ids as $v){
						$info['customer_id'] = $v;
						if($_POST['operating_type'] == 'receive'){
							$info['user_id'] = session('user_id');
							$info['type'] = 1;
							add_record('领取','从客户池领取了此客户！','customer',$v);
						}else{
							$info['user_id'] = $user_id;
							$info['type'] = 2;
							add_record('分配','将此客户分配给'.$user_name.'!','customer',$v);
						}
						$m_customer_record->add($info);
					}
					//是分配还是领取
					if($_POST['owner_role']){
						$title=L('you_have_new_customer');
						$content=L('THE_CUSTOMER_RESOURCES',array(session('name'),implode(',', $customer_name)));
						$need_alert = true;
					}else{
						alert('success', L('BATCH_TO_GET_SUCCESS'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('BATCH_ALLOCATION_FAILURE'), $_SERVER['HTTP_REFERER']);
				}
			}

			//分配需要提醒
			if($need_alert){
				if(intval($_POST['message_alert']) == 1) {
					sendMessage($owner_role_id,$content,1);
				}
				if(intval($_POST['email_alert']) == 1){
					$email_result = sysSendEmail($owner_role_id,$title,$content);
					if(!$email_result) alert('error', L('EMAIL_FAILURE_NOT_SET_EFFECTIVE_MAILBOX'),$_SERVER['HTTP_REFERER']);
				}
				if(intval($_POST['sms_alert']) == 1){
					$sms_result = sysSendSms($owner_role_id,$content);
					if(100 == $sms_result){
						alert('error', L('MESSAGE_FAILURE_NOT_SET_EFFECTIVE_MOBILE'),$_SERVER['HTTP_REFERER']);
					}elseif($sms_result < 0){
						alert('error',L('MESSAGE_FAILURE_ERRORCODE',array($sms_result)),$_SERVER['HTTP_REFERER']);
					}
				}
				alert('success', L('DISTRIBUTION_OF_SUCCESS'), $_SERVER['HTTP_REFERER']);
			}
		}
	}

	/**
	*  客户详情页面，放入客户池
	*
	**/
	public function fenpei(){
		$customer_id = intval($_GET['customer_id']);

		 if ($this->isGET()) {
			if($_GET['by'] == 'put'){
				if($customer_id){
					$customer = M('customer')->where('customer_id = %d', $customer_id)->find();
					if($customer['is_locked'] == 0){
						if(M('customer')->where('customer_id = %d', $customer_id)->setField('owner_role_id',0)){
							alert('success', L('IN_THE_SUCCESSFUL_CUSTOMER_POOL'), U('customer/index'));
						}else{
							alert('error', L('IN_THE_CUSTOMER_POOL'), $_SERVER['HTTP_REFERER']);
						}
					}else{
						alert('error', L('ISLOCK_CAN_NOT_PUT_IN_CUSTOMER_POOL'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->customer_id = $customer_id;
				$this->display();
			}
		}
	}

	/**
	*  添加客户
	*
	**/
	public function add(){
        if($this->isPost()){
            $m_customer = D('Customer');
            $m_customer_data = D('CustomerData');
            $field_list = M('Fields')->where(array('model'=>'customer','in_add'=>1))->order('order_id')->select();
            foreach ($field_list as $v){
                switch($v['form_type']) {
                    case 'address':
                        $_POST[$v['field']] = $_POST[$v['field']] ? implode(chr(10),$_POST[$v['field']]) : '';
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
                        $d_contacts = D('Contacts');
                        $m_contacts_data = M('ContactsData');
                        //处理POST数据
                        $contacts_field_list = M('Fields')->where(array('model'=>'contacts','in_add'=>1))->order('order_id')->select();
                        foreach ($contacts_field_list as $v){
                            switch($v['form_type']) {
                                case 'address':
                                    $_POST['con_contacts'][$v['field']] = $_POST['con_contacts'][$v['field']] ? implode(chr(10),$_POST['con_contacts'][$v['field']]) : '';
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
                        if($d_contacts->create($_POST['con_contacts'])){
                            if($m_contacts_data->create($_POST['con_contacts']) !== false){
                                $d_contacts->creator_role_id = session('role_id');
                                $d_contacts->create_time = time();
                                $d_contacts->update_time = time();
                                if($contacts_id = $d_contacts->add()){
                                    $m_contacts_data->contacts_id = $contacts_id;
                                    $m_contacts_data->add();
                                }else{
                                    $this->error(L('ADD_THE_PRIMARY_CONTACT_FAILURE'));
                                }
                            }
                        } else {
                            $this->error($d_contacts->getError());
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

					//地址信息转地理坐标
					$address_arr = explode(chr(10), $_POST['address']);
					if ($address_arr['3']) {
						$address_str = implode('', $address_arr);
						$ret = get_lng_lat($address_str);
						$m_customer->lng = $ret['lng'] ?: 0;
						$m_customer->lat = $ret['lat'] ?: 0;
					}
					if(!$customer_id = $m_customer->add()){
						$this->error(L('ADD_CUSTOMER_FAILS_CONTACT_ADMIN'));
					}
                    $m_customer_data->customer_id = $customer_id;
                    $m_customer_data->add();


                    //线索转换
					if ($_POST['leads_id']) {
						$leads_id = intval($_POST['leads_id']);
						$r_module = array(
							array('key'=>'log_id','r1'=>'RCustomerLog','r2'=>'RLeadsLog'),
							array('key'=>'file_id','r1'=>'RCustomerFile','r2'=>'RFileLeads'),
							array('key'=>'event_id','r1'=>'RCustomerEvent','r2'=>'REventLeads'),
							array('key'=>'task_id','r1'=>'RCustomerTask','r2'=>'RLeadsTask')
						);
						foreach ($r_module as $key=>$value) {
							$key_id_array = M($value['r2'])->where('leads_id = %d', $leads_id)->getField($value['key'],true);
							$r1 = M($value['r1']);
							$data['customer_id'] = $customer_id;
							foreach($key_id_array as $k=>$v){
								$data[$value['key']] = $v;
								$r1->add($data);
							}
						}
						$leads_data['is_transformed'] = 1;
						$leads_data['update_time'] = time();
						$leads_data['customer_id'] = $customer_id;
						$leads_data['contacts_id'] = $contacts_id;
						$leads_data['transform_role_id'] = session('role_id');
						M('Leads')->where('leads_id = %d', $leads_id)->save($leads_data);
					}

					//记录操作记录
					actionLog($customer_id);
					if ($contacts_id && $customer_id) {
						$rcc['contacts_id'] = $contacts_id;
						$rcc['customer_id'] = $customer_id;
						M('RContactsCustomer')->add($rcc);
					}

					if($_POST['submit'] == L('SAVE')) {
						if($_POST['refer_url']){
							//如果是从线索转换，列表跳回列表，详情页跳回列表
							if(strpos($_POST['refer_url'],'m=leads&a=view')){
                                $this->redirect('../Customerm/index');
							}elseif(strpos($_POST['refer_url'],'m=contacts&a=add')){
                                $this->redirect('../Customerm/index');
							}else{
                                $this->redirect('../Customerm/index');
							}
						}else{
                            $this->redirect('../Customerm/index');
						}
					}else{
                        $this->redirect('../Customerm/index');
					}
				}else{
					$this->error($m_customer_data->getError());
				}
			}else{
				$this->error($m_customer->getError());
            }
		}else{
			//判断客户数限制
			$m_config = M('Config');
			$opennum = $m_config->where('name="opennum"')->getField('value');
			if($opennum){
				$outdays = $m_config->where('name="customer_outdays"')->getField('value');
				$outdate = empty($outdays) ? time() : time()-86400*$outdays;

				$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
				$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
				$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
				$openrecycle = $m_config -> where('name="openrecycle"')->getField('value');

				if($openrecycle == 2){
					$c_where['_string'] = '(update_time > '.$outdate.' AND get_time > '.$contract_outdays.') OR is_locked = 1';
				}
				$c_where['owner_role_id'] = session('role_id');
				$c_where['customer_status'] = '意向客户';

				$customer_count = M('customer')->where($c_where)->count();
				$customer_num = M('user')->where('role_id =%d',session('role_id'))->getField('customer_num');
				if($customer_count >= $customer_num){
					alert('error', '你的客户数量已超出限制！请联系管理员', U('customer/index'));
				}
			}

			if(intval($_GET['leads_id'])){
				$leads = D('LeadsView')->where('leads.leads_id = %d', intval($_GET['leads_id']))->find();
				$this->leads = $leads;
				$this->field_list = field_list_html("edit","customer",$leads);
			}else{
				$this->field_list = field_list_html("add","customer");
				$this->contacts_field_list = field_list_html("add","contacts","","contacts");
			}
			$count = ((int) M('Customer')->max('customer_id')) + 1;
			$this->customer_code = date('Ymd_') . $count;
			$this->refer_url = $_SERVER['HTTP_REFERER'];
            $alert = parseAlert();
            $this->alert = $alert;
            $action=get_action_name();
            $this->display($action);

		}
	}

	/**
	*  删除客户
	*
	**/
	public function delete(){
		$m_customer = M('Customer');
        $m_business = M('Business');
        $m_contract = M('Contract');
		$r_module = array('Log'=>'RCustomerLog','File'=>'RCustomerFile', 'Contacts' => 'RContactsCustomer');
        $where = array();
        $where_resource = array();
		if ($this->isPost()) {
			$customer_ids = $_POST['customer_id'];
			if (empty($customer_ids)) {
				$this->ajaxReturn('',L('HAVE_NOT_CHOOSE_ANY_CONTENT'),0);
			}else {
				if (!session('?admin') && !checkPerByAction('customer','del_resource')) {
					$where['owner_role_id'] = array('in',$this->_permissionRes);
					$where_resource['owner_role_id'] = array('in',$this->_permissionRes);
					//判断是否客户池（只有管理员能删除）
					$m_config = M('Config');
					$outdays = $m_config->where('name="customer_outdays"')->getField('value');
					$outdate = empty($outdays) ? 0 : time()-86400*$outdays;
					$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
					$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
					$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
					$openrecycle = $m_config->where('name="openrecycle"')->getField('value');

					if($openrecycle == 2){
						$where_resource['_string'] = '(update_time > '.$outdate.' AND get_time > '.$contract_outdays.') OR is_locked = 1';
					}
					// else{
					// 	$where_resource['_string'] = "owner_role_id=0 or (update_time < ".$outdate." and is_locked = 0) or (get_time < ".$contract_outdays." and is_locked = 0)";
					// }
					$where_resource['customer_id'] = array('in',$customer_ids);
					$resource_customer_ids = M('Customer')->where($where_resource)->getField('customer_id',true);
				}

                //判断客户下是否有非空商机（即有商机编号），如果有则不能删除
                $business_customer_ids = $m_business->where(array('customer_id'=>array('in',$customer_ids),'code'=>array('neq','')))->getField('customer_id',true);
                if ($business_customer_ids) {
                    $customer_ids = array_diff($customer_ids,$business_customer_ids);//数组差集
                }
                if (!session('?admin') && !checkPerByAction('customer','del_resource')) {
                	if ($resource_customer_ids) {
	                	$customer_ids = array_intersect($customer_ids, $resource_customer_ids);//数组交集
	                } else {
	                	$customer_ids = array();
	                }
                }
                $where['customer_id'] = array('in',$customer_ids);
                if ($customer_ids) {
                    $del_customer_ids = $m_customer->where($where)->getField('customer_id',true);
                    if ($m_customer->where(array('customer_id'=>array('in',$del_customer_ids)))->delete()) {
                        //删除附表信息
                        M('CustomerData')->where(array('customer_id'=>array('in',$del_customer_ids)))->delete();
                        foreach($del_customer_ids as $key=>$val){
                            //记录操作记录
                            actionLog($val);
                            //删除相关信息
                            foreach ($r_module as $key2=>$value2) {
                                $module_ids = M($value2)->where('customer_id = %d', $val)->getField($key2 . '_id', true);
                                M($value2)->where('customer_id = %d', $val)->delete();
                                if(!is_int($key2)){
                                    M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
                                }
                            }
                        }
                        //删除客户关联空商机
                        $m_business->where(array('customer_id'=>array('in',$del_customer_ids)))->delete();
                        if ($business_customer_ids) {
                            //如果客户存在未删除的商机，提示先删除商机
                            $this->ajaxReturn('','部分客户删除失败，请先删除客户下相关商机！',0);
                        } else {
                            return;
                        }
                    } else {
                    	$this->ajaxReturn('',L('DELETE_FAILED_CONTACT_ADMIN'),0);
                    }
                } else {
                	if ($business_customer_ids) {
						$this->ajaxReturn('','客户删除失败，请先删除该客户下相关商机后重试！',0);
                	} else {
                		$this->ajaxReturn('','您没有此权利！',0);
                	}
                }
			}
		}
	}

	/**
	*  编辑客户资料
	*
	**/
	public function edit(){
		$customer = D('CustomerView')->where('customer.customer_id = %d',$this->_request('customer_id'))->find();
		if (!$customer) {
            alert('error', L('CUSTOMER_DOES_NOT_EXIST!'),$_SERVER['HTTP_REFERER']);
        }
		/*if(!in_array($customer['owner_role_id'], $this->_permissionRes)) $this->error(L('HAVE NOT PRIVILEGES'));*/
        $customer['owner'] = D('RoleView')->where('role.role_id = %d', $customer['owner_role_id'])->find();
        $customer['customer_code']=M('customer')->where('customer_id=%d',$customer['customer_id'])->getField('customer_code');
        $customer['contacts_name'] = M('contacts')->where('contacts_id = %d', $customer['contacts_id'])->getField('name');
        $field_list = M('Fields')->where('model = "customer"')->order('order_id')->select();
		if($this->isPost()){
			$m_customer = D('Customer');
			$m_customer_data = D('CustomerData');
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$_POST[$v['field']] = $_POST[$v['field']] ? implode(chr(10),$_POST[$v['field']]) : '';
					break;
					case 'datetime':
						$_POST[$v['field']] = strtotime($_POST[$v['field']]);
					break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$_POST[$v['field']] = implode(chr(10),$_POST[$v['field']]);
						}
					break;
				}
			}
			if($m_customer->create()){
				if($m_customer_data->create()!==false){
					$m_customer->update_time = time();
					//修改字段记录
					$old_customer = M('Customer') ->where('customer_id= %d',$customer['customer_id'])->find();  //修改前数据

					//地址信息转地理坐标
					$address_arr = explode(chr(10), $_POST['address']);
					if ($address_arr['3']) {
						$address_str = implode('', $address_arr);
						$ret = get_lng_lat($address_str);
						$m_customer->lng = $ret['lng'] ?: 0;
						$m_customer->lat = $ret['lat'] ?: 0;
					}
					$a = $m_customer->where('customer_id= %s',$customer['customer_id'])->save();

					$new_customer = $m_customer ->where('customer_id= %d',$customer['customer_id'])->find();	//修改后数据
					$update_ago = array_diff_assoc($new_customer,$old_customer); //获取已修改的字段
					$m_fields = M('fields');
					$m_action_record = M('action_record');
					$up_message = '';
					foreach($update_ago as $k => $v){
						if($k != 'update_time'){
							$field_info = $m_fields ->where('model="customer" and field="%s"',$k)->field('form_type,name')->find();
							$field_name = $field_info['name'];
							if($field_info['form_type'] == 'datetime'){
								$old_value = date('Y-m-d',$old_customer[$k]);
								$new_value = date('Y-m-d',$v);
							}else{
								$old_value = $old_customer[$k];
								$new_value = $v;
							}
							$up_message .= '<div>将 '.$field_name.' 由 <span style="color:#77B0E9">"'.$old_value.'"</span> 修改为 <span style="color:#E7AE6F">"'.$new_value.'"</span> </div>';
						}
					}
					//副表数据查询
					$old_customer_data = M('Customer_data') ->where('customer_id= %d',$customer['customer_id'])->find();
					$b = $m_customer_data->where('customer_id= %d', $customer['customer_id'])->save();
					$new_customer_data = M('Customer_data') ->where('customer_id= %d',$customer['customer_id'])->find();
					$update_ago_data = array_diff_assoc($new_customer_data,$old_customer_data);

					foreach($update_ago_data as $kk => $vv){
						if($kk != 'update_time'){
							// dd($update_ago_data);
							$field_infos = $m_fields->where('model="customer" and field="%s"',$kk)->field('form_type,name')->find();
							$field_names = $field_infos['name'];
							if($field_infos['form_type'] == 'datetime'){
								$old_values = date('Y-m-d',$old_customer[$kk]);
								$new_values = date('Y-m-d',$vv);
							}else{
								$old_values = $old_customer[$kk];
								$new_values = $vv;
							}
							$up_message .= '<div>将 '.$field_names.' 由 <span style="color:#77B0E9">"'.$old_values.'"</span> 修改为 <span style="color:#E7AE6F">"'.$new_values.'"</span> </div>';
						}
					}
					$arr['create_time'] = time();
					$arr['create_role_id'] = session('role_id');
					$arr['type'] = '修改';
					$arr['duixiang'] = $up_message;
					$arr['model_name'] = 'customer';
					$arr['action_id'] = $customer['customer_id'];
					$m_action_record ->add($arr);

					if($a !== false && $b !== false){
						if($_POST['contacts_id'] && ($_POST['contacts_id'] != $customer['contacts_id'])){
							$rcc['contacts_id'] = intval($_POST['contacts_id']);
							$rcc['customer_id'] = $customer['customer_id'];
							M('RContactsCustomer')->add($rcc);
						}
						actionLog($customer['customer_id']);
						$this->redirect('../Customerm/index');
					}else{
						alert('error', L('CUSTOMER_EDITING_FAILED!'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', $m_customer_data->getError());
					$this->alert = parseAlert();
					$this->error();
				}
            }else{
               $this->error($m_customer->getError(), $_SERVER['HTTP_REFERER']);
            }
		}else{
            $alert = parseAlert();
            $this->alert = $alert;
			$this->p = $_SERVER['HTTP_REFERER'];
			//等级数据错误处理
			$customer['grade'] = ($customer['grade'] > 5 || $customer['grade'] < 0)  ? 0 : $customer['grade'];
			$this->customer = $customer;
            $this->field_list = field_list_html("edit","customer",$customer);
            $action=get_action_name();
            $this->display($action);
		}
	}

	/**
	*  客户详情（字段编辑）
	*
	**/
	public function edit_ajax(){
		$m_customer = D('Customer');
		$m_customer_data = D('CustomerData');
		$field = trim($_POST['field']);
		$value = trim($_POST['val']);
		$customer_id = intval($_POST['customer_id']);
		$contacts_id = intval($_POST['contacts_id']);
		$contacts_arr = array('contacts_name', 'contacts_phone', 'contacts_saltname');
		//判断权限
		$customer = D('CustomerView')->where('customer.customer_id = %d',$customer_id)->find();
		if (!$customer) {
            $this->ajaxReturn('参数错误！','error',0);
        }
		if(!in_array($customer['owner_role_id'], getPerByAction('customer','edit'))){
			$this->ajaxReturn('您没有此权限！','error',0);
		}

		if(in_array($field, $contacts_arr)){
			switch ($field) {
				case 'contacts_name':
					$field = 'name';
					break;
				case 'contacts_phone':
					$field = 'telephone';
					break;
				case 'contacts_saltname':
					$field = 'saltname';
					break;
			}
			if ($contacts_id) {
				$edit_data = array(
					$field=>$value,
					'contacts_id'=>$contacts_id,
					'update_time'=>time()
				);
				$ret = D('contacts')->save($edit_data);
				$this->ajaxReturn('','success',1);
			}else{
				//新增联系人
				$contacts_data = array();
				$contacts_data[$field] = $value;
				$contacts_data['create_time'] = time();
				$contacts_data['update_time'] = time();
				$contacts_data['creator_role_id'] = session('role_id');
				$contacts_id = D('contacts')->add($contacts_data);
				//联系人、客户关系处理
				$RContactsCustomer = array();
				$RContactsCustomer['contacts_id'] = $contacts_id;
				$RContactsCustomer['customer_id'] = $customer_id;
				M('RContactsCustomer')->add($RContactsCustomer);
				//设置为首要联系人
				M('Customer')->where('customer_id = %d',$customer_id)->setField('contacts_id',$contacts_id);
				$this->ajaxReturn($contacts_id,'success',1);
			}
		}else{
			//$field_conut = count(explode('[', $field));

			if(false !== stristr($field, '[')){
				list($field) = explode('[', $field);
				$value = str_replace(',',chr(10),$value);
				//dump($value);die;
			}
			$edit_data = array(
				$field=>$value,
				'customer_id'=>$customer_id,
				);

			if($m_customer->create($edit_data)){
				if($m_customer_data->create($edit_data)!==false){
					$m_customer->update_time = time();

					//修改字段记录
					$old_customer = M('Customer') ->where('customer_id= %d',$customer_id)->find(); //修改前数据

					//地址信息转地理坐标
					$address_arr = explode(chr(10), $edit_data['address']);
					if ($address_arr['3']) {
						$address_str = implode('', $address_arr);
						$ret = get_lng_lat($address_str);
						$m_customer->lng = $ret['lng'] ?: 0;
						$m_customer->lat = $ret['lat'] ?: 0;
					}
					$a = $m_customer->where('customer_id= %d',$customer_id)->save();
					$new_customer = $m_customer ->where('customer_id= %d',$customer_id)->find();	//修改后数据
					$update_ago = array_diff_assoc($new_customer,$old_customer); //获取已修改的字段
					$m_fields = M('fields');
					$m_action_record = M('action_record');
					$up_message = '';
					foreach($update_ago as $k => $v){
						if($k != 'update_time'){
							$field_info = $m_fields ->where('model="customer" and field="%s"',$k)->field('form_type,name')->find();
							$field_name = $field_info['name'];
							if($field_info['form_type'] == 'datetime'){
								$old_value = date('Y-m-d',$old_customer[$k]);
								$new_value = date('Y-m-d',$v);
							}else{
								$old_value = $old_customer[$k];
								$new_value = $v;
							}
							$up_message .= '<div>将 '.$field_name.' 由 <span style="color:#77B0E9">"'.$old_value.'"</span> 修改为 <span style="color:#E7AE6F">"'.$new_value.'"</span> </div>';
						}
					}
					//副表数据查询
					$old_customer_data = M('Customer_data') ->where('customer_id= %d',$customer_id)->find();
					$b = $m_customer_data->where('customer_id= %d', $customer_id)->save();
					$new_customer_data = M('Customer_data') ->where('customer_id= %d',$customer_id)->find();
					$update_ago_data = array_diff_assoc($new_customer_data,$old_customer_data);
					foreach($update_ago_data as $kk => $vv){
						if($kk != 'update_time'){
							$field_infos = $m_fields ->where('field="%s" and model="customer"',$kk)->field('form_type,name')->find();
							$field_names = $field_infos['name'];
							if($field_infos['form_type'] == 'datetime'){
								$old_values = date('Y-m-d',$old_customer[$k]);
								$new_values = date('Y-m-d',$v);
							}else{
								$old_values = $old_customer[$k];
								$new_values = $v;
							}
							$up_message .= '<div>将 '.$field_names.' 由 <span style="color:#77B0E9">"'.$old_values.'"</span> 修改为 <span style="color:#E7AE6F">"'.$new_values.'"</span> </div>';
						}
					}
					$arr['create_time'] = time();
					$arr['create_role_id'] = session('role_id');
					$arr['type'] = '修改';
					$arr['duixiang'] = $up_message;
					$arr['model_name'] = 'customer';
					$arr['action_id'] = $customer_id;
					$m_action_record ->add($arr);

					if($a !== false && $b !== false){
						$this->ajaxReturn('','success',1);
						// actionLog($customer['customer_id']);
					}else{
						$this->ajaxReturn('修改失败，请重试！','error',0);
					}
				}else{
					$result = $m_customer_data->getError();
				}
            }else{
            	$result = $m_customer->getError();
            }
			// $ret = M('Customer')->save($edit_data);
		}
		if($result){
			$this->ajaxReturn($result,'error',0);
		}
	}
    /**
     * 极光发送消息
     */
    public function chatMessage(){
        $merchant_id=1;
        $user_id = $_POST['user_id'];
        $content = $_POST['content'];


        $user = M('user');
        $mername=$user->where("user_id=".$merchant_id)->getField("chatname");
        if ($merchant_id){
            $username=$user->where("user_id=".$user_id)->getField("chatname");
            if ($mername) {
                $jim = new JMessage();

                $jim->sendMsg($mername,$content,"single",array(),'666666');

            }
        }
    }

    /**
     * 极光发送图片
     */
    public function chatImg(){
        $merchant_id=1;
        $user_id = $_POST['user_id'];
        $image = $_POST['image'];
        $imagename=$_FILES['image']['name'];
        $img='C:\Users\30752\Pictures\2019-09\IMG_0844.JPG';

        print_r($img);
        $user = M('user');
        $mername=$user->where("user_id=".$merchant_id)->getField("chatname");
        if ($merchant_id){
            $username=$user->where("user_id=".$user_id)->getField("chatname");
            if ($mername) {
                $jim = new JMessageupload();
                $imgs=$jim->upload('image','C:\Users\307indexcon52\Pictures\2019-09\IMG_0844.JPG');
                var_dump($imgs);die;
                $jj=new JMessage();
                $jj->sendImg('hahcc','','','','dev1');
            }
        }
    }

    /**
     * 极光加好友
     */
    public function chatAddFriends(){
        $merchant_id=1;
        $user_id = $_POST['user_id'];

        $user = M('user');
        $mername=$user->where("user_id=".$merchant_id)->getField("chatname");
        if ($user_id){
            $username=$user->where("user_id=".$user_id)->getField("chatname");
            if ($username){
                $jim = new JMessage();
                $res=$jim->addfriends($username,array("666666"));
                if (!$res){
                    print_r(1,"添加好友成功");
                }else{
                    print_r(2,"添加好友失败或已添加");
                }
            }else{
                print_r(3,"您尚未注册");
            }
        }else{
            print_r(2,"该用户不存在");
        }

    }



    /**
     * 极光推送接口
     */
    public function chatPush(){
        if (IS_POST) {
            $title=I('post.title');
            $content=I('post.content');
            $username = "";
            $appkeys = 'efbdad17a9be59ad08af28a0'; //应用程序(appKey)
            $masterSecret = 'a66414bc1ab0274b7a4535a7'; //应用密钥
            $obj = new jpush($username, $appkeys, $masterSecret);
            $sendno = intval((double)microtime() * 10000);
            $sendno = date('His') . str_pad($sendno, 4, '0', STR_PAD_LEFT);
            $receiver_value = '';
            $msg_content = json_encode(array('n_builder_id' => 0, 'n_title' => $title, 'n_content' => $content));
            $platform = 'android';
            $obj->send($sendno, 4, $receiver_value, 1, $msg_content, $platform);
            exit();
        }
        $action=get_action_name();
        $this->display($action);
    }

	/**
	*  联系人详情（页面）
	*
	**/
	public function indexcon(){
	    $contact_id=I('get.contacts_id');
	    $content=I('get.content');
	    if ($contact_id){
	    $contacts=M('contacts');
	    $contactsRes=$contacts->where("contacts_id=".$contact_id)->find();
	    }
	    if ($contactsRes) {
	        $contactsRes['content']=$content;
            $this->assign('contact', $contactsRes);
        }
	    $action=get_action_name();
	    $this->display($action);
	}

    /**
     *
     * 客户详情（页面）
     */
    public function indexctm(){
        $customer_id=I('get.customer_id');
        if ($customer_id){
            $customer=M('customer');
            $customerRes=$customer->where("customer_id=".$customer_id)->find();
            $user=M('user');
            $this->username=$user->field('name')->where('user_id='.$customerRes['owner_role_id'])->find();
        }
        if ($customerRes){
            $this->assign('customer',$customerRes);

        }
        $action=get_action_name();
        $this->display($action);
    }

    /**
     *  客户列表（默认页面）
     *
     **/

	public function index(){
		$d_v_customer = D('CustomerView');
		$m_contract = M('Contract');
		$d_customer = D('Customer');

		$below_ids = getPerByAction(MODULE_NAME,ACTION_NAME,true);

		$m_config = M('Config');
		$outdays = $m_config -> where('name="customer_outdays"')->getField('value');
		$outdays = $outdays ?: $outdays;
		$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
		$c_outdays = $c_outdays ?: $c_outdays;

		$where = array();
		$params = array();
		$order = "top.set_top desc,top.top_time desc,customer.update_time desc,customer.customer_id desc";

		if ($_GET['order_field'] && $_GET['order_type']) {
			$order = 'top.set_top desc,top.top_time desc,customer.'.trim($_GET['order_field']).' '.trim($_GET['order_type']).',customer.customer_id desc';
		}

		$m_config = M('Config');
		// 未跟进时间
		$outdays = $m_config->where('name="customer_outdays"')->getField('value') ?: 0;
		// 未签合同时间
		$c_outdays = $m_config->where('name="contract_outdays"')->getField('value') ?: 0;

		//查询分享给我的
		$m_share =  M('CustomerShare');
		$sharing_id = session('role_id');
		$customerid = $m_share->where('by_sharing_id =%d',$sharing_id)->getField('customer_id',true);
		//查询我分享的
		$share_customer_ids = $m_share ->where('share_role_id =%d',session('role_id'))->getField('customer_id',true);

		// 过滤已删除的
		$where['is_deleted'] = 0;

		// 客户池
		if($this->_get('content') == 'resource'){
            $where += $d_customer->where_customer('public');
        }else{
        	// 非客户池
        	if(empty($_GET['scene_id'])){
				$by = isset($_GET['by']) && empty($_GET['scene_id']) ? trim($_GET['by']) : 'me';
			}
			switch ($by) {
				case 'todaycontact' :
					$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-d', time()))+86400), array('gt',strtotime(date('Y-m-d', time()))), 'and');
					$where['owner_role_id'] = session('role_id');
					break;
				case 'sub' : $where['owner_role_id'] = array('in',$below_ids); break;
				case 'me' : $where['owner_role_id'] = session('role_id'); break;
				case 'share' : $where['customer_id'] = array('in',$customerid); break;
				case 'myshare' :
					$where['customer_id'] = array('in',$share_customer_ids);
					$where['owner_role_id'] = array('in', $this->_permissionRes);
					break;
				default :
					// by=all
					$where['owner_role_id'] = array('in', $this->_permissionRes);
				break;
			}
        	$where += $d_customer->where_customer();
        }
//p($where,'');

		//普通查询
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']);
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if ($this->_request('state')){
				$state = $this->_request('state', 'trim');
				$address_where[] = '%'.$state.'%';
				if($this->_request('city')){
					$city = $this->_request('city', 'trim');
					$address_where[] = '%'.$city.'%';
					if($this->_request('area')){
						$area = $this->_request('area', 'trim');
						$address_where[] = '%'.$this->_request('area', 'trim').'%';
					}
				}
				if($search) $address_where[] = '%'.$search.'%';
				$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'state='.$this->_request('state','trim'), 'city='.$this->_request('city','trim'),'area='.$this->_request('area','trim'),'search='.$this->_request('search','trim'));
				if($condition == 'not_contain'){
					$where[$field] = array('notlike', $address_where, 'OR');
				}else{
					$where[$field] = array('like', $address_where, 'AND');
				}
			}else{
				if($field == 'name'){
					$c_where['_string'] = 'name like "%'.$search.'%" or telephone like "%'.$search.'%"';
					$contacts_ids = M('Contacts')->where($c_where)->getField('contacts_id',true);
					$contacts_str = implode(',',$contacts_ids);
					if($contacts_str){
						$contacts_customer_ids = M('rContactsCustomer')->where(array('contacts_id'=>array('in',$contacts_str)))->getField('customer_id',true);
						$field_where = array();
						$field_where['customer.name'] = array('like','%'.$search.'%');
						$field_where['customer.customer_id'] = array('in',$contacts_customer_ids);
						$field_where['_logic'] = 'OR';
						$where['_complex'] = $field_where;
					}else{
						$where['customer.name'] = array('like','%'.$search.'%');
					}
				}else{
					switch ($condition) {
						case "is" : $where[$field] = array('eq',$search);break;
						case "isnot" :  $where[$field] = array('neq',$search);break;
						case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
						case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
						case "start_with" :  $where[$field] = array('like',$search.'%');break;
						case "not_start_with" :  $where[$field] = array('notlike',$search.'%');break;
						case "end_with" :  $where[$field] = array('like','%'.$search);break;
						case "is_empty" :  $where[$field] = array('eq','');break;
						case "is_not_empty" :  $where[$field] = array('neq','');break;
						case "gt" :  $where[$field] = array('gt',$search);break;
						case "egt" :  $where[$field] = array('egt',$search);break;
						case "lt" :  $where[$field] = array('lt',$search);break;
						case "elt" :  $where[$field] = array('elt',$search);break;
						case "eq" : $where[$field] = array('eq',$search);break;
						case "neq" : $where[$field] = array('neq',$search);break;
						case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
						case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
						case "tgt" :  $where[$field] = array('gt',$search+86400);break;
						default : $where[$field] = array('eq',$search);
					}
				}
				$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$search);
			}
			//过滤不在权限范围内的role_id
			if(trim($_REQUEST['field']) == 'owner_role_id'){
				if(!in_array(trim($search),$this->_permissionRes)){
					$where['owner_role_id'] = array('in', $this->_permissionRes);
				}
			}
		}

		//多选类型字段
		$check_field_arr = M('Fields')->where(array('model'=>'customer','form_type'=>'box','setting'=>array('like','%'."'type'=>'checkbox'".'%')))->getField('field',true);
		//高级搜索
		if(!$_GET['field']){
			$no_field_array = array('act','content','p','condition','listrows','daochu','this_page','current_page','export_limit','desc_order','asc_order','selectexcelxport','by','scene_id','order_field','order_type');
			foreach($_GET as $k=>$v){
				if(!in_array($k,$no_field_array)){
					if(is_array($v)){
						if ($v['state']){
							$address_where[] = '%'.$v['state'].'%';

							if($v['city']){
								$address_where[] = '%'.$v['city'].'%';

								if($v['area']){
									$address_where[] = '%'.$v['area'].'%';
								}
							}
							if($v['search']) $address_where[] = '%'.$v['search'].'%';

							if($v['condition'] == 'not_contain'){
								$where[$k] = array('notlike', $address_where, 'OR');
							}else{
								$where[$k] = array('like', $address_where, 'AND');
							}
						} elseif (($v['start'] != '' || $v['end'] != '')) {
							if($k == 'create_time'){
								$k = 'customer.create_time';
							}elseif($k == 'update_time'){
								$k = 'customer.update_time';
							}
							//时间段查询
							if ($v['start'] && $v['end']) {
								$where[$k] = array('between',array(strtotime($v['start']),strtotime($v['end'])+86399));
							} elseif ($v['start']) {
								$where[$k] = array('egt',strtotime($v['start']));
							} else {
								$where[$k] = array('elt',strtotime($v['end'])+86399);
							}
						} elseif(($v['value']) != '') {
							if (in_array($k,$check_field_arr)) {
								$where[$k] = field($v['value'],'contains');
							} else {
								if($k == 'status_id'){
									$business_map['status_id'] = $v['value'];
								}else{
									// $v['condition'] = $v['condition'] ? : 'contains';
									$where[$k] = field($v['value'], $v['condition']);
								}
							}
						} elseif (in_array($v['condition'], array('is_empty', 'is_not_empty'))) {
							$where[$k] = field($v['value'],$v['condition']);
						}
					}else{
						if(!empty($v)){
							$where[$k] = field($v);
						}
				    }
				}
				if($k == 'customer.create_time'){
					$k = 'create_time';
				}elseif($k == 'customer.update_time'){
					$k = 'update_time';
				}
				if(is_array($v)){
					foreach ($v as $key => $value) {
						$params[] = $k.'['.$key.']='.$value;
					}
				}else{
					$params[] = $k.'='.$v;
				}
			}

			// 需要权限范围筛选的role_id，需要重新整理where条件
			$d_search = D('Search');
				// 非客户池才做过滤
			if ($_GET['content'] != 'resource' && $_GET['by'] != 'share') {
				// 客户池、共享给我的不做处理
				$where = $d_search->roleWhere('owner_role_id', $where, $this->_permissionRes);
			}
		}

		//自定义场景
		$m_scene = M('Scene');
		$scene_id = $_REQUEST['scene_id'] ? intval($_REQUEST['scene_id']) : '';
		$scene_where = array();
		$scene_where['role_id']  = session('role_id');
		$scene_where['type']  = 1;
		$scene_where['_logic'] = 'or';
		$map_scene['_complex'] = $scene_where;
		$map_scene['module'] = 'customer';
		$map_scene['is_hide'] = 0;

		$scene_list = $m_scene->where($map_scene)->order('order_id asc,id asc')->select();
		foreach ($scene_list as $k=>$v) {
			if ($v['type'] == 0) {
				eval('$data = '.$v["data"].';');
			} else {
				$data = array();
			}
			if ($scene_id && $scene_id == $v['id']) {
				$fields_search = $data;
			}
			$scene_list[$k]['cut_name'] = cutString($v['name'],8);
		}
		if ($scene_id) {
			$scene_info = $m_scene->where(array('id'=>$scene_id,'role_id'=>session('role_id')))->find();
			if (!$scene_info) {
				alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
			}
			eval('$scene_info_data = '.$scene_info["data"].';');
			if(is_array($scene_info_data)){
				foreach ($scene_info_data as $k=>$v) {
					if ($v['state']){
						$address_where[] = '%'.$v['state'].'%';
						if($v['city']){
							$address_where[] = '%'.$v['city'].'%';
							if($v['area']){
								$address_where[] = '%'.$v['area'].'%';
							}
						}
						if($v['condition'] == 'not_contain'){
							$where[$k] = array('notlike', $address_where, 'OR');
						}else{
							$where[$k] = array('like', $address_where, 'AND');
						}
					} elseif (($v['start'] != '' || $v['end'] != '')) {
						if($k == 'create_time'){
							$k = 'customer.create_time';
						}elseif($k == 'update_time'){
							$k = 'customer.update_time';
						}
						//时间段查询
						if ($v['start'] && $v['end']) {
							$where[$k] = array('between',array(strtotime($v['start']),strtotime($v['end'])+86399));
						} elseif ($v['start']) {
							$where[$k] = array('egt',strtotime($v['start']));
						} else {
							$where[$k] = array('elt',strtotime($v['end'])+86399);
						}
					} elseif ($v['value'] != ''){
						if($k == 'status_id'){
							$business_map['status_id'] = $v['value'];
						}else{
							$where[$k] = field($v['value'],$v['condition']);
						}
					}
					if(is_array($v)){
						foreach ($v as $key => $value) {
							$params[] = $k.'['.$key.']='.$value;
						}
					}else{
						$params[] = $k.'='.$v;
					}
				}
			}
		}
		//场景名称
		switch ($by) {
			case 'me' : $scene_name = '我的客户';break;
			case 'sub' : $scene_name = '下属客户';break;
			case 'all' : $scene_name = '全部客户';break;
			case 'share' : $scene_name = '共享给我的';break;
			case 'myshare' : $scene_name = '我共享的';break;
			default : $scene_name = '我的客户';break;
		}
		$this->by = $by;
		$params[] = 'by=' . $by;
		if ($scene_id) {
			$scene_name = $scene_info['name'] ? $scene_info['name'] : '我的客户';
		}
		$this->scene_name = $scene_name;

		//高级搜索字段
		$fields_list_data = M('Fields')->where(array('model'=>array('in',array('','customer')),'is_main'=>1))->field('field,form_type')->select();
		foreach($fields_list_data as $k=>$v){
			$fields_data_list[$v['field']] = $v['form_type'];
		}
		$fields_search = array();
		foreach($params as $k=>$v){
			if(strpos($v,'[condition]=') || strpos($v,'[value]=') || strpos($v,'[state]=') || strpos($v,'[city]=') || strpos($v,'[area]=') || strpos($v,'[start]=') || strpos($v,'[end]=')){
				$field = explode('[',$v);

				if(strpos($field[0],'.')){
					$ex_field = explode('.',$field[0]);
					$field[0] = $ex_field[1];
				}

				if (strpos($v,'[condition]=')) {
					$condition = explode('=',$v);
					$fields_search[$field[0]]['field'] = $field[0];
					$fields_search[$field[0]]['condition'] = $condition[1];
				} elseif (strpos($v,'[state]=')) {
					$state = explode('=',$field[1]);
					$fields_search[$field[0]]['state'] = $state[1];
				} elseif (strpos($v,'[city]=')) {
					$city = explode('=',$field[1]);
					$fields_search[$field[0]]['city'] = $city[1];
				} elseif (strpos($v,'[area]=')) {
					$area = explode('=',$field[1]);
					$fields_search[$field[0]]['area'] = $area[1];
				} elseif (strpos($v,'[start]=')) {
					$start = explode('=',$field[1]);
					$fields_search[$field[0]]['field'] = $field[0];
					$fields_search[$field[0]]['start'] = $start[1];
				} elseif (strpos($v,'[end]=')) {
					$end = explode('=',$field[1]);
					$fields_search[$field[0]]['end'] = $end[1];
				} else {
					$value = explode('=',$v);
					if($fields_search[$field[0]]['field']){
						$fields_search[$field[0]]['value'] = $value[1];
					}else{
						$fields_search[$field[0]]['field'] = $field[0];
						$fields_search[$field[0]]['condition'] = 'eq';
						$fields_search[$field[0]]['value'] = $value[1];
					}
				}
				$fields_search[$field[0]]['form_type'] = $fields_data_list[$field[0]];
			}
		}
		//高级排序（暂只支持时间、数字类型字段，客户等级）
		$order_field_where = array();
		$order_field_where['field']  = array('in',array('grade'));
		$order_field_where['form_type']  = array('in',array('number','datetime'));
		$order_field_where['_logic'] = 'or';
		$order_map['_complex'] = $order_field_where;
		$order_map['model'] = 'customer';
		$order_fields = M('Fields')->where($order_map)->field('field,name')->select();
		$this->order_fields = $order_fields;

		if(trim($_GET['act']) == 'sms'){
			if(!checkPerByAction('Setting','sendsms')){
				alert('error', L('DO NOT HAVE PRIVILEGES'),$_SERVER['HTTP_REFERER']);
			}
			$customer_ids = $d_v_customer->where($where)->getField('customer_id', true);
			$contacts_ids = M('RContactsCustomer')->where('customer_id in (%s)', implode(',', $customer_ids))->getField('contacts_id', true);
			$contacts_ids = implode(',', $contacts_ids);
			$contacts = M('Contacts')->where('contacts_id in (%s)', $contacts_ids)->select();
			$this->contacts = $contacts;
			$this->display('Setting:sendsms');
		}else{
			// $listrows = $_GET['listrows'] ? intval($_GET['listrows']) : 15;
			if($_GET['listrows']){
				$listrows = intval($_GET['listrows']);
				cookie('listrows', $listrows, 3600 * 24 * 30);
				$params[] = "listrows=" . intval($_GET['listrows']);
			}else{
				$listrows = cookie('listrows') ? cookie('listrows') : 15;
				$params[] = "listrows=".$listrows;
			}

			$m_business = M('Business');
			$d_business = D('BusinessTopView');
			$m_customer = M('Customer');
			$m_remind = M('Remind');
			$m_contacts = M('Contacts');
			$d_contacts = D('ContactsView');
			$m_r_contacts_customer = M('RContactsCustomer');
			$d_role = D('RoleView');
			$m_receivables = M('Receivables');
			$m_receivingorder = M('Receivingorder');
			$m_contract = M('Contract');
			$m_r_business_product = M('RBusinessProduct');
			$d_business_product = D('BusinessProductView');
			$m_user = M('User');

			//客户导出
			if(trim($_GET['act']) == 'excel'){
				if(checkPerByAction('customer','excelexport')){
					$order = $order ? $order : 'customer.update_time desc,customer.customer_id asc';
					$daochu = $_GET['daochu'];
					if($daochu){
						$dc_ids = explode(',',$_GET['daochu']);
					}
					$model_ids = explode(',',$_GET['selectexcelxport']);
					$model_count = $model_ids ? count($model_ids) : 1;

					$current_page = intval($_GET['current_page']);
					$export_limit = intval($_GET['export_limit']);
					$limit = ($export_limit*($current_page-1)).','.$export_limit;
					$m_user = M('user');

					// 123 124 134 234 1234 13 14 34
					if($model_count > 1 && ($model_count != 2 || !in_array(2,$model_ids))){
						if($dc_ids){
							$customer_ids = $dc_ids;
						}else{
							$customer_ids = array();
							$customer_ids = $d_v_customer->where($where)->getField('customer_id',true);
						}
						$m = $d_v_customer->export();
						$all_list = $m->where(array('customer.customer_id' => array('IN', $customer_ids)))
							->order($order)->limit($limit)
							->select();
						foreach($all_list as $k=>$v){
							// 如果选中联系人则导出联系人信息
							if(in_array(2, $model_ids)){
								if ($v['contacts_id']) {
									$contacts = $d_contacts->where('contacts.is_deleted = 0 and contacts.contacts_id = %d', $v['contacts_id'])->find();
								} else {
									$contacts = $d_contacts->where('contacts.is_deleted = 0 and customer.customer_id = %d', $v['customer_id'])->find();
								}
								$all_list[$k]['contacts'] = $contacts;
							}

							//产品名称
							if(in_array(3, $model_ids)){
								$product_name = array();
								$product_name = $d_business_product->where('r_business_product.business_id = (%d)', $v['business_id'])->getField('name',true);
								if($product_name){
									if(count($product_name) > 1){
										$all_list[$k]['product_name'] = implode(',',$product_name);
									}else{
										$all_list[$k]['product_name'] = $product_name[0];
									}
								}
							}

							//收款进度
							if(in_array(4, $model_ids)){   // 如果选中联系人则导出联系人信息
								$contract_info = $m_contract->where('business_id = %d',$v['business_id'])->field('contract_id,price')->find();
								$schedule = 0;
								if($contract_info){
									//应收款
									$receivables_id = $m_receivables->where('contract_id = %d',$contract_info['contract_id'])->getField('receivables_id');
									//回款金额
									$sum_price = 0;
									$sum_price = $m_receivingorder->where(array('receivables_id'=>$receivables_id,'status'=>1))->sum('money');
									//当前收款进度
									if($sum_price){
										if($contract_info['price'] == 0 || $contract_info['price'] == ''){
											$schedule = 100;
										}else{
											$schedule = round(($sum_price/$contract_info['price'])*100,2);
										}
									}
								}
								$all_list[$k]['price'] = $contract_info['price'];
								$all_list[$k]['sum_price'] = $sum_price;
								$all_list[$k]['un_price'] = $contract_info['price'] - $sum_price;
								$all_list[$k]['schedule'] = $schedule;
							}
						}
					}else{
						if($dc_ids){
							$where['customer_id'] = array('in',$dc_ids);
						}
						$all_list = $d_v_customer->where($where)->order($order)->limit($limit)->select();
						foreach($all_list as $k=>$v){
							$all_list[$k]['owner_role_name'] = $m_user ->where('role_id =%d',$v['owner_role_id'])->getField('full_name');
							$days = 0;
							$all_list[$k]["owner_name"] = $m_user->where('role_id = %d', $v['owner_role_id'])->getField('full_name');
							$all_list[$k]["creator_name"] = $m_user->where('role_id = %d', $v['creator_role_id'])->getField('full_name');
							$days =  $v['update_time'];
							$c_days =  $v['get_time'];
							$all_list[$k]["days"] = $outdays-floor((time()-$days)/86400);
							$all_list[$k]["c_days"] = $c_outdays-floor((time()-$c_days)/86400);
							if($model_count == 2 && in_array(2,$model_ids)){
								$contacts = array();
								if(!empty($v['contacts_id'])){
									$contacts = $d_contacts->where('contacts.is_deleted = 0 and contacts.contacts_id = %d',$v['contacts_id'])->find();
									$all_list[$k]['contacts'] = $contacts;
								}else{
									$contacts_customer = $m_r_contacts_customer->where('customer_id = %d',$v['customer_id'])->limit(1)->order('id desc')->select();
									if(!empty($contacts_customer)){
										$contacts = $d_contacts->where('contacts.is_deleted = 0 and contacts.contacts_id = %d',$contacts_customer[0]['contacts_id'])->find();
									}
									$all_list[$k]['contacts'] = $contacts;
								}
							}
						}
					}

					session('export_status', 1);
					$this->excelExport($all_list,$model_ids);
				}else{
					alert('error',  L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
				}
			}

			import("@.ORG.Page");
			$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
			$this->_where = http_build_query($where);

			$list = $d_v_customer->where($where)->order($order)->page($p.','.$listrows)->select();
//echo sqlFormat($d_v_customer->_sql()); die();
			$count = $d_v_customer->where($where)->count();
			$p_num = ceil($count/$listrows);
			if($p_num<$p){
				$p = $p_num;
			}

			$Page = new Page($count,$listrows);
			$Page->parameter = implode('&', $params);
			$this->assign('page',$Page->show());

			$d_user = D('User');
			foreach($list as $k=>$v){
				// $list[$k]['owner_role_name'] = $m_user ->where('role_id =%d',$v['owner_role_id'])->getField('full_name');
				$list[$k]['create_role_name'] = $d_user->get_full_name((int) $v['creator_role_id']);
				$list[$k]['owner_role_name'] = $v['owner_role_full_name'];
				//提醒
				$remind_info = array();
				$remind_info = $m_remind->where(array('module'=>'customer','module_id'=>$v['customer_id'],'create_role_id'=>session('role_id'),'is_remind'=>array('neq',1)))->order('remind_id desc')->find();
				$list[$k]['remind_time'] = !empty($remind_info) ? $remind_info['remind_time'] : '';
				//到期限制数据
				$days = 0;
				$days = $v['update_time'];
				$c_days = $v['get_time'];
				$list[$k]["days"] = $outdays-floor((time()-$days)/86400);
				$list[$k]["c_days"] = $c_outdays-floor((time()-$c_days)/86400);
				$contacts = array();
				$contacts_list = array();
				//首要联系人信息
				if(!empty($v['contacts_id'])){
					$contacts_customer = $m_r_contacts_customer->where('customer_id = %d',$v['customer_id'])->order('id desc')->getField('contacts_id',true);
				}else{
					$contacts_customer = $m_r_contacts_customer->where('customer_id = %d',$v['customer_id'])->order('id desc')->getField('contacts_id',true);
					if(!empty($contacts_customer)){
						$contacts = $m_contacts->where('is_deleted = 0 and contacts_id = %d',$contacts_customer[0])->field('contacts_id,name,telephone')->find();
					}
					$list[$k]['contacts_id'] = $contacts['contacts_id'];
					$list[$k]['contacts_name'] = $contacts['name'];
					$list[$k]['contacts_telephone'] = $contacts['telephone'];
				}
				$list[$k]['name'] = $v['name'] ? cutString($v['name'],15) : '查看详情';
				$list[$k]['custome_title_name'] = $v['name'];
			}
			//客户联系人是否显示
			$m_fields = M('Fields');
			$this->cn_is_show = $cn_is_show = $m_fields->where('model="contacts" and field ="name"')->getField('is_show');

			//联系人电话是否显示
			$this->ct_is_show = $ct_is_show = $m_fields->where('model="contacts" and field ="telephone"')->getField('is_show');

			$this->listrows = $listrows;
			$this->customerlist = $list;
			$this->assign("count",$count);
			$this->this_page = $p;

			$field_array = getIndexFields('customer');
			$this->field_array = $field_array;
			foreach($field_array as $k=>$v){
				if($v['field'] == 'name'){
					$name_field_array[] = $v;
					break;
				}
			}
			$this->name_field_array = $name_field_array;
			$this->field_list = getMainFields('customer');
			//高级搜索
			$this->fields_search = $fields_search;
			$this->scene_list = $scene_list;

			// 是否启用自动回收
			$this->assign('openrecycle', M('Config')->where('name="openrecycle"')->getField('value'));

			//自定义排序问题
			if ($_GET['order_type'] || $scene_id || $listrows) {
				//排序专用params
				$order_params = array();
				foreach ($params as $kp=>$vp) {
					if (strpos($vp, 'order_type') !== false) {
						unset($params[$kp]);
					} elseif (strpos($vp, 'order_field') !== false) {
						unset($params[$kp]);
					} elseif (strpos($vp, 'scene_id') !== false) {
						unset($params[$kp]);
					} elseif (strpos($vp, 'listrows') !== false) {
						unset($params[$kp]);
					} else {
						$order_params[] = $vp;
					}
				}
				if ($_GET['order_type'] != 'cancel_order') {
					$params[] = "order_type=" . trim($_GET['order_type']);
					$params[] = "order_field=" . trim($_GET['order_field']);
				}
				if ($scene_id) {
					$params[] = "scene_id=" . $scene_id;
					$order_params[] = "scene_id=" . $scene_id;
				}
				if ($listrows) {
					$params[] = "listrows=" . $listrows;
					$order_params[] = "listrows=" . $listrows;
				}
			}
			$this->order_parameter = implode('&', $order_params);//排序专用params

			//by_parameter(特殊处理)
			$this->by_parameter = str_replace('by='.$_GET['by'], '', implode('&', $params));

			// 【距到期天数】需拼接的url字符串【排序：追加的url字符串应该去除排序参数本身，因为排序参数是在html中写死的，且和上次排序应相反】
			foreach ($params as $k => $v) {
				$param_arr = explode('=', $v);
				if ($param_arr[0] == 'asc_order' || $param_arr[0] == 'desc_order') {
					unset($params[$k]);
				}
			}
            $content=$_GET['content'];
			$this->parameter = implode('&', $params);
			$this->alert = parseAlert();
			$action=get_action_name();
            $this->assign("content",$content);
			$this->display($action);

		}
	}

	/**
	*  合同页客户弹出框列表
	*
	**/
	public function clistDialog(){
		$m_customer = M('Customer');
		$m_business = M('Business');
		$m_business_status = M('BusinessStatus');
		$m_r_business_product = M('r_business_product');
		$m_product = M('product');
		$outdays = M('config') -> where('name="customer_outdays"')->getField('value');
		$outdate = empty($outdays) ? 0 : time()-86400*$outdays;
		import("@.ORG.DialogListPage");
		$b_where = array();
		$params = array();
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']);
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);

			if ('create_time' == $field || 'update_time' == $field) {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			if('customer_name' == $field){
				$sc_where['name'] = array('like','%'.$search.'%');
				$customer_ids = $m_customer ->where($sc_where)->getField('customer_id',true);
				if($customer_ids){
					$new_customer_ids = $customer_ids;
				}else{
					$new_customer_ids = -1;
				}
			}else{
				switch ($condition) {
					case "is" : $where[$field] = array('eq',$search);break;
					case "isnot" :  $where[$field] = array('neq',$search);break;
					case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
					case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
					case "start_with" :  $where[$field] = array('like',$search.'%');break;
					case "end_with" :  $where[$field] = array('like','%'.$search);break;
					case "is_empty" :  $where[$field] = array('eq','');break;
					case "is_not_empty" :  $where[$field] = array('neq','');break;
					case "gt" :  $where[$field] = array('gt',$search);break;
					case "egt" :  $where[$field] = array('egt',$search);break;
					case "lt" :  $where[$field] = array('lt',$search);break;
					case "elt" :  $where[$field] = array('elt',$search);break;
					case "eq" : $where[$field] = array('eq',$search);break;
					case "neq" : $where[$field] = array('neq',$search);break;
					case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
					case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
					case "tgt" :  $where[$field] = array('gt',$search+86400);break;
					default : $where[$field] = array('eq',$search);
				}
			}
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);

		//权限控制
		$c_where['owner_role_id'] = array('in',implode(',',getPerByAction('customer','index')));
		$c_where['is_deleted'] = array('neq',1);
		$c_where['_string'] = 'update_time > '.$outdate.' OR is_locked = 1';
		$customer_ids = $m_customer->where($c_where)->getField('customer_id',true);

		$where['is_deleted'] = 0;
		if($_GET['contacts_id']){
			//过滤已关联的商机ID
			$link_business_ids = M('RBusinessContacts')->where('contacts_id = %d',intval($_GET['contacts_id']))->getField('business_id',true);
			$where['business_id'] = array('not in',$link_business_ids);
		}
		if($_GET['customer_id']){
			if(in_array(intval($_GET['customer_id']),$customer_ids)){
				$where['customer_id'] = intval($_GET['customer_id']);
			}else{
				$where['customer_id'] = -1;
			}
		}else{
			if($customer_ids){
				if($new_customer_ids == -1){
					$where['customer_id'] = -1;
				}elseif($new_customer_ids){
					$b_customer_ids = array_intersect($customer_ids, $new_customer_ids);
					if($b_customer_ids){
						$where['customer_id'] = array('in',$b_customer_ids);
					}else{
						$where['customer_id'] = -1;
					}
				}else{
					$where['customer_id'] = array('in',$customer_ids);
				}
			}else{
				$where['customer_id'] = -1;
			}
		}
		if(!$_GET['contacts_id']){
			//排除已签订合同的商机
			$business_ids = M('contract')->where('is_deleted = 0')->getField('business_id',true);
			//过滤空商机
			$where['code'] = array('neq','');
			//过滤项目失败的商机
			$where['status_id'] = array('neq',99);
			if($business_ids){
				$where['business_id'] = array('not in',$business_ids);
			}
		}

		$business_list = $m_business->where($where)->page($p.',10')->select();
		$count = $m_business ->where($where)->count();
		$d_product_info = D('ProductInfo');		//
		foreach($business_list as $kk=>$vv){
			$business_list[$kk]['customer_name'] = $m_customer->where('customer_id =%d',$vv['customer_id'])->getField('name');
			$business_list[$kk]['status_name'] = $m_business_status->where('status_id =%d',$vv['status_id'])->getField('name');
			$business_list[$kk]['product_counts'] = $m_r_business_product ->where('business_id =%d',$vv['business_id'])->count();
			$business_product_list = $m_r_business_product ->where('business_id =%d',$vv['business_id'])->select();
			foreach($business_product_list as $k1=>$v1){
				$business_product_list[$k1] += $d_product_info->getNameSpec($v1['product_info_id']);
				// 上为进销存之后改动 	下边为该动之前
				// $business_product_list[$k1]['product_name'] = $m_product->where('product_id = %d',$v1['product_id'])->getField('name');
			}
			$business_list[$kk]['business_product_list'] =$business_product_list;
			$business_list[$kk]['product_info'] = $m_product ->where('product_id =%d',$business_product_list[0]['product_id'])->field('name,suggested_price,standard')->find();
		}
		$this->business_list = $business_list;

		$this->search_field = $_REQUEST;//搜索信息
		$Page = new Page($count,10);
		$Page->parameter = implode('&', $params);
		$this->assign('page',$Page->show());
		$this->display();
	}

	//客户弹出框
	public function listDialog(){
		if ($_GET['input_type'] == 'checkbox') {
			$this->input_type = 'checkbox';
		} else {
			$this->input_type = 'radio';
		}
		$m_customer = M('Customer');
		$m_contacts = M('Contacts');
		$m_user = M('User');
		$m_r_contacts_customer = M('RContactsCustomer');
		$outdays = M('config') -> where('name="customer_outdays"')->getField('value');
		$outdate = empty($outdays) ? 0 : time()-86400*$outdays;
		$where['owner_role_id'] = array('in', $this->_permissionRes);
		$where['is_deleted'] = array('neq',1);
		$where['_string'] = 'update_time > '.$outdate.' OR is_locked = 1';

		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']);
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);

			if ('create_time' == $field || 'update_time' == $field) {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			switch ($condition) {
				case "is" : $where[$field] = array('eq',$search);break;
				case "isnot" :  $where[$field] = array('neq',$search);break;
				case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where[$field] = array('like',$search.'%');break;
				case "end_with" :  $where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $where[$field] = array('eq','');break;
				case "is_not_empty" :  $where[$field] = array('neq','');break;
				case "gt" :  $where[$field] = array('gt',$search);break;
				case "egt" :  $where[$field] = array('egt',$search);break;
				case "lt" :  $where[$field] = array('lt',$search);break;
				case "elt" :  $where[$field] = array('elt',$search);break;
				case "eq" : $where[$field] = array('eq',$search);break;
				case "neq" : $where[$field] = array('neq',$search);break;
				case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where[$field] = array('gt',$search+86400);break;
				default : $where[$field] = array('eq',$search);
			}
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);

		import("@.ORG.DialogListPage");
		//相关合同下客户
		if ($_GET['contract_id']) {
			$customer_id = M('Contract')->where(array('contract_id'=>intval($_GET['contract_id'])))->getField('customer_id');
			if ($customer_id) {
				$where['customer_id'] = $customer_id;
				$params[] = 'contract_id='.intval($_GET['contract_id']);
			}
		}
		$listrows = $_REQUEST['listrows'] ? $_REQUEST['listrows'] : 15;
		$this->listrows = $listrows;
		$customer_list = $m_customer->where($where)->order('create_time desc')->page($p.','.$listrows)->select();
		// dd($m_customer);
		$count = $m_customer->where($where)->count();
		foreach($customer_list as $k=>$v){
			//如果存在首要联系人，则查出首要联系人。否则查出联系人中第一个。
			$contacts = array();
			if(!empty($v['contacts_id'])){
				$contacts = $m_contacts->where('is_deleted = 0 and contacts_id = %d',$v['contacts_id'])->find();
				$customer_list[$k]['contacts_name'] = $contacts['name'];
				$customer_list[$k]['telephone'] = $contacts['telephone'];
			}else{
				$contacts_customer = $m_r_contacts_customer->where('customer_id = %d',$v['customer_id'])->limit(1)->order('id desc')->select();

				if(!empty($contacts_customer)){
					$contacts = $m_contacts->where('is_deleted = 0 and contacts_id = %d',$contacts_customer[0]['contacts_id'])->find();
				}
				$customer_list[$k]['contacts_id'] = $contacts['contacts_id'];
				$customer_list[$k]['contacts_name'] = $contacts['name'];
				$customer_list[$k]['telephone'] = $contacts['telephone'];
			}
			if($v['nextstep_time'] >0){
				$customer_list[$k]['nextstep_time'] = date('Y-m-d',$v['nextstep_time']);
			}else{
				$customer_list[$k]['nextstep_time'] = '';
			}
			//客户负责人
			$customer_list[$k]['customer_owner_id'] = $m_user->where('role_id = %d',$v['owner_role_id'])->getField('full_name');
		}
		$this->customerList = $customer_list;
		$this->search_field = $_REQUEST;//搜索信息

		$Page = new Page($count, $listrows);
		$Page->parameter = implode('&', $params);
		$this->assign('page',$Page->show());
		$data = getIndexFields('customer');
		$this->field_num = sizeof($data)+1;
        $this->field_array = $data;
        if (isMobile()){
            $action=get_action_name();
            $this->display($action);
        }else {
            $this->display();
        }
	}

    /**
	*  查看客户资料
	*
	**/
	public function view(){
		$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$d_role = D('RoleView');
		$m_user = M('User');
        $this->content = $_GET['content'];
		if (0 == $customer_id) {
			alert('error', L('parameter_error'), U('customer/index'));
		} else {
            //查询客户数据
			$customer = D('CustomerView')->where('customer.customer_id = %d', $customer_id)->find();
			$m_config = M('Config');
			$outdays = $m_config->where('name="customer_outdays"')->getField('value');
			$outdate = empty($outdays) ? 0 : time()-86400*$outdays;

			$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
			$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
			$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
			$openrecycle = $m_config->where('name="openrecycle"')->getField('value');
			//查询分享的
			$is_share = false;
			if (M('CustomerShare')->where(array('by_sharing_id'=>session('role_id'),'customer_id'=>$customer_id))->find()) {
				$is_share = true;
			}

			if ($openrecycle == 2) {
				if($customer['owner_role_id'] != 0 && (($customer['update_time'] > $outdate && $customer['get_time'] > $contract_outdays) || $customer['is_locked'] == 1)){
					if(!in_array($customer['owner_role_id'], $this->_permissionRes)){
						if($is_share){
							$this->share_num = 1;
						}else{
							$this->error(L('HAVE NOT PRIVILEGES'));
						}
					}
				}
			}else{
				if($customer['owner_role_id'] != 0){
					if(!in_array($customer['owner_role_id'], $this->_permissionRes)){
						if($is_share){
							$this->share_num = 1;
						}else{
							$this->error(L('HAVE NOT PRIVILEGES'));
						}
					}
				}
			}

            //查询固定信息
			$customer['owner'] = $m_user->where('role_id = %d', $customer['owner_role_id'])->field('role_id,full_name')->find();

            if($customer['is_deleted'] == 1){
                $customer['deleted'] = $d_role->where('role.role_id = %d', $customer['delete_role_id'])->find();
            }

			$customer['business'] = M('Business')->where(array('customer_id'=>$customer['customer_id'],'is_deleted'=>0,'code'=>array('neq','')))->order('business_id desc')->select();
			$customer['business_count'] = sizeof($customer['business']);

			$m_business_status = M('BusinessStatus');
			$d_business_product = D('BusinessProductView');

			foreach($customer['business'] as $k=>$v){
				// $customer['business'][$k]['owner'] = $d_role->where('role.role_id = %d', $v['owner_role_id'])->find();
				$business_status_info = array();
				$status_count = 0;
				$status_count = $m_business_status->where(array('type_id'=>$v['status_type_id']))->count();
				if ($v['status_id'] == 99) {
					$business_status_info['name'] = '项目失败';
					$business_status_info['order_id'] = '99';
				} else {
					$business_status_info = $m_business_status->where(array('status_id'=>$v['status_id'],'type_id'=>$v['status_type_id']))->field('name,order_id')->find();
				}
				$customer['business'][$k]['status'] = $business_status_info['name'];
				$product_name = $d_business_product->where('r_business_product.business_id = (%d)', $v['business_id'])->getField('name',true);
				$status_order = $business_status_info['order_id'];
				$progress = intval($status_order/$status_count > 1 ? 100 : $status_order*100/$status_count);
				$customer['business'][$k]['progress'] = $progress;

				$product_count = count($product_name);
				if($product_count > 1){
					$product_name = $product_name[0].'、...';
				}else{
					$product_name = $product_name[0];
				}
				$customer['business'][$k]['product_name'] = $product_name;
				$customer['business'][$k]['product_count'] = $product_count;
				$business_id[] = $v['business_id'];
			}

			//联系人信息
			// $contacts_ids = M('rContactsCustomer')->where('customer_id = %d', $customer_id)->getField('contacts_id', true);
			// $customer['contacts'] = M('contacts')->where('contacts_id in (%s) and is_deleted=0', implode(',', $contacts_ids))->select();
			$field_list = field_list_html("edit","customer",$customer);
			$array_field = array();
			foreach ($field_list['main'] as $k => $v) {
				if($v['field'] != 'name' && $v['field'] != 'grade' && $v['field'] != 'customer_owner_id' && $v['field'] != 'customer_code'){
					$array_field[] = $v;
				}
			}
			foreach ($field_list['data'] as $k => $v) {
				$array_field[] = $v;
			}
			$field_list = $array_field;
			if(count($field_list)%2 == 1){
				$field_list[] = array('name'=>'','field'=>null);
			}
			//获取分享客户数量
			$this->share_counts = M('customer_share')->where('customer_id =%d',$customer_id)->count();
			//提醒
			$remind_info = M('Remind')->where(array('module'=>'customer','module_id'=>$customer_id,'create_role_id'=>session('role_id'),'is_remind'=>array('neq',1)))->find();
			$customer['remind_info'] = $remind_info;

			// 发票信息
			$this->invoice_info = M('RCustomerInvoice')->where(array('customer_id'=>$customer_id))->find();

			//等级数据错误处理
			$customer['grade'] = ($customer['grade'] > 5 || $customer['grade'] < 0)  ? 0 : $customer['grade'];

			//沟通日志
            $m_business = M('Business');
            $m_r_business_log = M('rBusinessLog');
            $m_log = M('Log');
            $m_log_status = M('LogStatus');
            $m_sign = M('Sign');
            $m_visitor_plan = M('VisitorPlan');
            $m_contacts = M('Contacts');
            $m_r_business_file = M('rBusinessFile');
            $m_r_business_product = M('rBusinessProduct');
            $m_product_category = M('ProductCategory');
            $business = $m_business->where('business_id = %d',$business_id)->select();
            foreach ($business as $k_bus => $vo_bus) {
                //沟通日志
                $log_ids = array();
                $log_ids = $m_r_business_log->where('business_id = %d', $vo_bus['business_id'])->getField('log_id', true);
                if ($log_ids) {
                    $business[$k_bus]['log'] = $m_log->where('log_id in (%s)', implode(',', $log_ids))->order('log_id desc')->select();
                    $log_count = $m_log->where('log_id in (%s)', implode(',', $log_ids))->count();
                } else {
                    $business[$k_bus]['log'] = array();
                    $log_count = 0;
                }

                $business[$k_bus]['log_count'] = empty($log_count)? 0 : $log_count;
                foreach ($business[$k_bus]['log'] as $key=>$value) {
                    $role_info = array();
                    $role_info = $m_user->where('role_id = %d', $value['role_id'])->field('full_name,thumb_path,role_id')->find();
                    if (!$role_info['thumb_path']) {
                        $role_info['thumb_path'] = './Public/img/avatar_default.png';
                    }
                    $business[$k_bus]['log'][$key]['owner'] = $role_info;
                    $business[$k_bus]['log'][$key]['code'] = $vo_bus['code'];
                    $business[$k_bus]['log'][$key]['log_type'] = 'rBusinessLog';
                    $status_name = $m_log_status->where('id = %d',$value['status_id'])->getField('name');
                    $business[$k_bus]['log'][$key]['status_name'] = $status_name ? $status_name : '';

                    // 相关访客计划
                    if ($value['sign']) {
                        $sign_id = $m_sign->where(array('log_id' => $value['log_id']))->getField('sign_id');
                        $vp_where = array('module' => 'sign', 'module_id' => $sign_id);
                    } else {
                        $vp_where = array('module' => 'log', 'module_id' => $value['log_id']);
                    }
                    $business[$k_bus]['log'][$key]['finish_visitor_plan'] = $m_visitor_plan->where($vp_where)->getField('content');

                    $business_logs[] = $business[$k_bus]['log'][$key];

                    // 相关联系人
                    $contacts_id = $m_r_business_log->where('log_id = %d', $value['log_id'])->getField('contacts_id');
                    $contacts = $m_contacts->where('contacts_id = %d', $contacts_id)->field('contacts_id,name as contacts_name,telephone as contacts_phone')->find();
                    $business_logs[$key] += $contacts ?: array();
                }
                //商机附件
                $business_file_ids = array();
                $business_file_ids = $m_r_business_file->where('business_id = %d', $vo_bus['business_id'])->getField('file_id', true);
                $business[$k_bus]['business_file_ids'] = $business_file_ids;

                //产品信息
                $business[$k_bus]['product'] = $m_r_business_product->where('business_id = %d', $vo_bus['business_id'])->select();
                $total_unit_price = 0.00;
                foreach ($business[$k_bus]['product'] as $k => $v) {
                    $info = array();
                    //$info = $m_product->where('product_id = %d', $v['product_id'])->find();
                    $info = D('ProductInfo')->getProductInfo($v['product_info_id']);
                    $business[$k_bus]['product'][$k]['info'] = $info;
                    $business[$k_bus]['product'][$k]['spec_list'] = D('Product')->getProductInfoSpec($v['product_info_id'],'string');
                    $business[$k_bus]['product'][$k]['name'] = $info['name'];
                    $business[$k_bus]['product'][$k]['category_name'] = $m_product_category->where('category_id = %d',$info['category_id'])->getField('name');
                    $business_products[] = $business[$k_bus]['product'][$k];
                    $total_unit_price += $v['unit_price']*$v['amount'];
                }

                if($vo_bus['final_price'] == '' || $vo_bus['final_price'] == '0.00'){
                    //整单折扣
                    $final_price_total = 0.00;
                    $final_price_total = $total_unit_price*(100-$vo_bus['final_discount_rate'])/100;
                    $business[$k_bus]['final_price'] = $final_price_total;
                }
                //判断商机产品总价(如果为空，则重新计算)
                if(($vo_bus['final_price'] == 0 || $vo_bus['final_price'] == 0.00) && $final_price_total != '0' && $final_price_total != '0.00'){
                    $m_business->where('business_id = %d',$vo_bus['business_id'])->setField('final_price',$final_price_total);
                }

                $business_info = $vo_bus;
                $business_info['mark'] = 1;
                $business_products[] = $business_info;
            }
            $file_ids = array();
            if(!empty($customer_id)){
                //通话录音
                $record_list = array();
                $record_list = M('CallRecord')->where(array('model'=>'customer','model_id'=>$customer_id))->select();
                foreach ($record_list as $k=>$v) {
                    $record_list[$k]['owner'] = $m_user->where(array('role_id'=>$v['role_id']))->field('full_name,thumb_path,role_id')->find();
                    $record_list[$k]['create_date'] = $v['start_time'];
                    $record_list[$k]['is_call'] = 1;
                    if ($v['call_type'] == 1) {
                        $record_list[$k]['call_type_name'] = '呼入';
                    } else {
                        $record_list[$k]['call_type_name'] = '呼出';
                    }
                    if ($v['call_upload'] == 1) {
                        $record_list[$k]['file_path'] = 'https://wukongtest.oss-cn-hangzhou.aliyuncs.com/'.$v['file_path'];
                    }
                }

                //沟通日志
                $m_sign = M('Sign');
                $m_sign_img = M('SignImg');
                $m_r_customer_log = M('rCustomerLog');

                $log_cus_id = $m_r_customer_log->where('customer_id = %d', $customer_id)->getField('log_id', true);
                $customer_logs = array();
                if ($log_cus_id) {
                    $customer_logs = M('log')->where('log_id in (%s)', implode(',', $log_cus_id))->order('log_id desc')->select();
                }

                foreach ($customer_logs as $key=>$value){
                    if($value['sign'] == 1){
                        $sign_info = $m_sign->where('log_id = %d',$value['log_id'])->find();
                        $customer_logs[$key]['sign_img'] = $m_sign_img ->where('sign_id = "%d"',$sign_info['sign_id'])->select();
                        $customer_logs[$key]['sign_info'] = $sign_info;
                    }
                    $role_info = array();
                    $role_info = $m_user->where('role_id = %d', $value['role_id'])->field('full_name,thumb_path,role_id')->find();
                    if (!$role_info['thumb_path']) {
                        $role_info['thumb_path'] = './Public/img/avatar_default.png';
                    }
                    $customer_logs[$key]['owner'] = $role_info;
                    $customer_logs[$key]['code'] = null;
                    $customer_logs[$key]['log_type'] = 'rCustomerLog';
                    $customer_logs[$key]['content'] = strip_tags($value['content']);
                    $status_name = $m_log_status->where('id = %d',$value['status_id'])->getField('name');
                    $customer_logs[$key]['status_name'] = $status_name ? $status_name : '';

                    // 相关联系人
                    $contacts_id = $m_r_customer_log->where('customer_id = %d and log_id = %d', $customer_id, $value['log_id'])->getField('contacts_id');
                    $contacts = $m_contacts->where('contacts_id = %d', $contacts_id)->field('contacts_id,name as contacts_name,telephone as contacts_phone')->find();
                    $customer_logs[$key] += $contacts ?: array();

                    // 相关访客计划
                    if ($value['sign']) {
                        $sign_id = $m_sign->where(array('log_id' => $value['log_id']))->getField('sign_id');
                        $vp_where = array('module' => 'sign', 'module_id' => $sign_id);
                    } else {
                        $vp_where = array('module' => 'log', 'module_id' => $value['log_id']);
                    }
                    $customer_logs[$key]['finish_visitor_plan'] = $m_visitor_plan->where($vp_where)->getField('content');
                }

                if($customer_logs == ''){
                    $logs_list = $business_logs;
                }elseif($business_logs == ''){
                    $logs_list = $customer_logs;
                }else{
                    $logs_list = array_merge($business_logs, $customer_logs);
                }

                /*对合并的数组排序*/
                function cmp($a,$b){
                    if($a['log_id'] == $b['log_id']){
                        return  0 ;
                    }
                    return ($a['log_id'] > $b['log_id']) ? -1 : 1;
                }
                usort($logs_list, "cmp");

                /*对合并的数组排序*/
                function cmp_2($a,$b){
                    if($a['create_date'] == $b['create_date']){
                        return  0 ;
                    }
                    return ($a['create_date'] > $b['create_date']) ? -1 : 1;
                }

                if ($record_list) {
                    if ($logs_list) {
                        $logs_list = array_merge($record_list,$logs_list);
                    } else {
                        $logs_list = $record_list;
                    }
                    usort($logs_list, "cmp_2");
                }

                //附件 客户
                $customer_file_id = M('RCustomerFile')->where('customer_id = %d',$customer_id)->getField('file_id',true);
                $business_file_ids = $business[0]['business_file_ids']; //商机附件
                //数组合并
                if($customer_file_id == ''){
                    $file_ids = $business_file_ids;
                }elseif($business_file_ids == ''){
                    $file_ids = $customer_file_id;
                }else{
                    $file_ids = array_merge($customer_file_id, $business_file_ids);
                }
                // 任务
                $m_task = M('Task');
                $m_task_sub = M('TaskSub');
                $role_id = session('role_id');
                $task_where = array();
                $task_where['module'] = 'customer';
                $task_where['module_id'] = $customer_id;
                $task_where['_string'] = 'creator_role_id = ' . $role_id . ' OR owner_role_id LIKE "%,' . $role_id . ',%" OR about_roles LIKE "%,' . $role_id . ',%"';
                $task = $m_task->field('task_id, priority, subject, create_date')->where($task_where)->select();
                $priority = array('高' => '#F96868', '普通' => '#46BE8A', '低' => '#F2A654');
                foreach ($task as $key => $val) {
                    $count = $m_task_sub->where('task_id = %d', $val['task_id'])->count();
                    $done = $m_task_sub->where('task_id = %d && is_done = 1', $val['task_id'])->count();
                    $task[$key]['done'] = floor($done / $count * 100);
                    if ($task[$key]['done'] <= 33) {
                        $task[$key]['done_class'] = 'progress-bar-danger';
                    } else if ($task[$key]['done'] <= 66) {
                        $task[$key]['done_class'] = 'progress-bar-warning';
                    } else if ($task[$key]['done'] <= 99) {
                        $task[$key]['done_class'] = '';
                    } else {
                        $task[$key]['done_class'] = 'progress-bar-success';
                    }
                    $task[$key]['priority_color'] = $priority[$val['priority']];
                    $task[$key]['create_date'] = date('Y-m-d H:i:s', $val['create_date']);
                }
                $this->customer_id = $customer_id;
                $this->task = $task;

                // 访客计划 列表
                $v_p_where = array(
                    'customer_id' => (int) $customer_id,
                    'page' => '1, 10'
                );
                $this->visitor_plan_list = D('VisitorPlan')->getList($v_p_where);

                // 访客计划 今天未完成
                $v_p_where = array(
                    'customer_id' => (int) $customer_id,
                    'status' => array('IN', array(0, 1)),		// 未完成 延期
                    'plan_time' => array('BETWEEN', array(strtotime(date('Y-m-d')), strtotime(date('Y-m-d 23:59:59'))))
                );
                $this->visitor_plan_list_not_done = D('VisitorPlan')->getList($v_p_where);
            }
            $this->log_list = $logs_list;
            $this->customer = $customer;
			$this->business = $business;
            $this->field_list = $field_list;
            $this->assign('business_id',$business_id);
			$this->alert = parseAlert();
			$this->field = D('Field')->getFieldName('Customer');
            $action = get_action_name();
			$this->display($action);
		}
	}
	public function selectexcelexport(){
		$this->display();
	}
	/**
	*  客户导出
	*
	**/
	public function excelExport($list=false,$model_ids){
		C('OUTPUT_ENCODE', false);
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();
		$objProps = $objPHPExcel->getProperties();
		$objProps->setCreator("pdcrm");
		$objProps->setLastModifiedBy("pdcrm");
		$objProps->setTitle("pdcrm Customer");
		$objProps->setSubject("pdcrm Customer Data");
		$objProps->setDescription("pdcrm Customer Data");
		$objProps->setKeywords("pdcrm Customer Data");
		$objProps->setCategory("pdcrm");
		$objPHPExcel->setActiveSheetIndex(0);
		$objActSheet = $objPHPExcel->getActiveSheet();

		$objActSheet->setTitle('Sheet1');
        $field_list = M('Fields')->where(array('model'=>'customer','field'=>array('not in',array('customer_owner_id'))))->order('order_id')->select();
		$j = 0;
        foreach($field_list as $field){
			if($field['form_type'] == 'address'){
				for($a=0;$a<=4;$a++){
					$j++;
					$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
					$address = array('所在省','所在市','所在县/区','街道信息');
					$objActSheet->setCellValue($pCoordinate.'2', $address[$a]);
				}
				$j--;
			}else{
				$j++;
				$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
				$objActSheet->setCellValue($pCoordinate.'2', $field['name']);
			}
        }
		$mark_customer_jj = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
		$mark_customer = PHPExcel_Cell::stringFromColumnIndex($j); //生成Excel

		$mark_customer_ascii = $ascii;
		$mark_customer_cv = $cv;

		$model_count = count($model_ids);

		if(in_array(2,$model_ids)){   // 如果选中联系人则导出联系人信息
			//联系人字段
			$contacts_fields_list = M('Fields')->where('model = \'contacts\'')->where(array('field'=>array('neq','customer_id')))->order('order_id')->select();
			foreach($contacts_fields_list as $field){

				if($field['field'] == 'customer_id') continue;
				if($field['form_type'] == 'address'){
					for($a=0;$a<=4;$a++){
						$j++;
						$pCoordinate_a = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
						$address = array('所在省','所在市','所在县','街道信息');
						$objActSheet->setCellValue($pCoordinate_a.'2', $address[$a]);
					}
					$j--;
				}elseif($field['field'] != 'customer_id'){
					$j++;
					$pCoordinate_a = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
					$objActSheet->setCellValue($pCoordinate_a.'2', $field['name']);
				}
				//$objPHPExcel->getActiveSheet()->getColumnDimension($pCoordinate_a)->setAutoSize(true); //单元格自适应宽度
			}
			$mark_contacts_jj = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
			$mark_contacts = PHPExcel_Cell::stringFromColumnIndex($j); //生成Excel
		}
		if(in_array(3,$model_ids)){   // 如果选中商机则导出商机信息
			$business_field = array('商机编号','营销阶段','营销产品');
			foreach($business_field as $busfield){
				$j++;
				$bCoordinate_a = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
				$objActSheet->setCellValue($bCoordinate_a.'2', $busfield);
			}
			$mark_business_jj = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
			$mark_business = PHPExcel_Cell::stringFromColumnIndex($j); //生成Excel
		}
		if(in_array(4,$model_ids)){   // 如果选中财务则导出财务信息
			$finance_name = array('应收金额','已收金额','未收金额','回款进度');
			foreach($finance_name as $ffield){
				$j++;
				$fCoordinate_a = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
				$objActSheet->setCellValue($fCoordinate_a.'2', $ffield);
			}
			$mark_finance_jj = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
			$mark_finance = PHPExcel_Cell::stringFromColumnIndex($j); //生成Excel
		}

		$i = 2;
		$objActSheet->getRowDimension(2)->setRowHeight(25); //设置行高
		foreach ($list as $k => $v) {
			$m = 0;
			if($model_count > 1){
				$date = M('CustomerData')->where(array('customer_id'=>$v['customer_info']['customer_id']))->find();
	            if(!empty($date)){
	                $v['customer_info'] = $v['customer_info']+$date;
	            }
			}

			$i++;
            foreach($field_list as $field){
            	$temp = str_replace('=', '', $v[$field['field']]);
				if($field['form_type'] == 'datetime'){
					$m++;
					$pCoordinate_c = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel

                    $objActSheet->setCellValue($pCoordinate_c.$i, date('Y-m-d H:i',$temp) ?: '');
                }elseif($field['form_type'] == 'number' || $field['form_type'] == 'floatnumber' || $field['form_type'] == 'phone' || $field['form_type'] == 'mobile' || ($field['form_type'] == 'text' && is_numeric($temp))){
					//防止使用科学计数法，在数据前加空格
					$m++;
					$pCoordinate_c = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel

					$objActSheet->setCellValue($pCoordinate_c.$i, ' '.$temp);
				}elseif($field['form_type'] == 'address'){
					$address = $temp;
					$arr_address = explode(chr(10),$address);
					for($a=0;$a<=4;$a++){
						$m++;
						$pCoordinate_c = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel

						$objActSheet->setCellValue($pCoordinate_c.$i, $arr_address[$a]);
					}
					$m--;
				}elseif($field['field'] == 'customer_owner_id'){
					$m++;
					$pCoordinate_c = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
					if($model_count > 1){
						$objActSheet->setCellValue($pCoordinate_c.$i, $v['customer_info']['owner_role_name']);
					}else{
						$objActSheet->setCellValue($pCoordinate_c.$i, $v['owner_role_name']);
					}
				}else{
					$m++;
					$pCoordinate_c = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
                    $objActSheet->setCellValue($pCoordinate_c.$i, $temp);
                }
            }
			if(in_array(2,$model_ids)){   // 如果选中联系人则导出联系人信息
				//联系人
				$mark_ascii = $ascii;
				$mark_cv = $cv;
				foreach($contacts_fields_list as $valu){
					//$pCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
					$temp = str_replace('=', '', $v['contacts'][$valu['field']]);
					//防止使用科学计数法，在数据前加空格
					if($valu['form_type'] == 'datetime'){
						$m++;
						$pCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
						$objActSheet->setCellValue($pCoordinate_d.$i, date('Y-m-d',$temp));
					}elseif($valu['form_type'] == 'address'){
						$addre = $temp;
						$array_addre = explode(chr(10),$addre);
						for($a=0;$a<=4;$a++){
							$m++;
							$pCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
							$objActSheet->setCellValue($pCoordinate_d.$i, $array_addre[$a]);
						}
						$m--;

					}elseif($valu['field'] == 'telephone' || $valu['field'] =='qq_no'){
						$m++;
						$pCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
						$objActSheet->setCellValue($pCoordinate_d.$i, $temp);
					}else{
						$m++;
						$pCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
						$objActSheet->setCellValue($pCoordinate_d.$i, $temp);
					}
				}
			}

			if(in_array(3,$model_ids)){
				$m++;
				$bCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
				$objActSheet->setCellValue($bCoordinate_d.$i, ' '.$v['code']);
				$m++;
				$bCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
				$objActSheet->setCellValue($bCoordinate_d.$i, $v['status']);
				$m++;
				$bCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
				$objActSheet->setCellValue($bCoordinate_d.$i, $v['product_name']);
			}
			if(in_array(4,$model_ids)){
				$m++;
				$bCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
				$objActSheet->setCellValue($bCoordinate_d.$i, $v['price']);
				$m++;
				$bCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
				$objActSheet->setCellValue($bCoordinate_d.$i, $v['sum_price']);
				$m++;
				$bCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
				$objActSheet->setCellValue($bCoordinate_d.$i, $v['un_price']);
				$m++;
				$bCoordinate_d = PHPExcel_Cell::stringFromColumnIndex($m-1); //生成Excel
				$objActSheet->setCellValue($bCoordinate_d.$i, $v['schedule']);
			}
		}
		//设置边框样式
		$color='00000000';
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框
                    'color' => array('argb' => $color),
                ),
            ),
        );
		// 2为联系人信息3商机信息4财务
		if(in_array(4,$model_ids)){
			$aa = $mark_finance;
			$objActSheet->getStyle('A1:'.$mark_finance.$i)->applyFromArray($styleArray);
			$objActSheet->getStyle('A1:'.$mark_finance.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平居中
			$objActSheet->getStyle('A1:'.$mark_finance.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
		}elseif(in_array(3,$model_ids)){
			$aa = $mark_business;
			$objActSheet->getStyle('A1:'.$mark_business.$i)->applyFromArray($styleArray);
			$objActSheet->getStyle('A1:'.$mark_business.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平居中
			$objActSheet->getStyle('A1:'.$mark_business.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
		}elseif(in_array(2,$model_ids)){
			$aa = $mark_contacts;
			$objActSheet->getStyle('A1:'.$mark_contacts.$i)->applyFromArray($styleArray);
			$objActSheet->getStyle('A1:'.$mark_contacts.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平居中
			$objActSheet->getStyle('A1:'.$mark_contacts.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
		}else{
			$aa = $mark_customer;
			$objActSheet->getStyle('A1:'.$mark_customer.$i)->applyFromArray($styleArray);
			$objActSheet->getStyle('A1:'.$mark_customer.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平居中
			$objActSheet->getStyle('A1:'.$mark_customer.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
		}
		$objActSheet->mergeCells('A1'.':'.$mark_customer_jj.'1');
		if(in_array(2,$model_ids)){   // 2为联系人信息3商机信息4财务
			$objActSheet->mergeCells($mark_customer.'1'.':'.$mark_contacts_jj.'1');
			$objActSheet->setCellValue($mark_customer.'1', L('CONTACTS_INFO'));
			if(in_array(3,$model_ids)){
				$objActSheet->mergeCells($mark_contacts.'1'.':'.$mark_business_jj.'1');
				$objActSheet->setCellValue($mark_contacts.'1', '营销信息');
				if(in_array(4,$model_ids)){
					$objActSheet->mergeCells($mark_business.'1'.':'.$mark_finance_jj.'1');
					$objActSheet->setCellValue($mark_business.'1', '回款信息');
				}
			}else{
				if(in_array(4,$model_ids)){
					$objActSheet->mergeCells($mark_contacts.'1'.':'.$mark_finance_jj.'1');
					$objActSheet->setCellValue($mark_contacts.'1', '回款信息');
				}
			}
		}else{
			if(in_array(3,$model_ids)){
				$objActSheet->mergeCells($mark_customer.'1'.':'.$mark_business_jj.'1');
				$objActSheet->setCellValue($mark_customer.'1', '营销信息');
				if(in_array(4,$model_ids)){
					$objActSheet->mergeCells($mark_business.'1'.':'.$mark_finance_jj.'1');
					$objActSheet->setCellValue($mark_business.'1', '回款信息');
				}
			}else{
				if(in_array(4,$model_ids)){
					$objActSheet->mergeCells($mark_customer.'1'.':'.$mark_finance_jj.'1');
					$objActSheet->setCellValue($mark_customer.'1', '回款信息');
				}
			}
		}
		$objActSheet->getStyle('A1')->getFont()->getColor()->setARGB('00000000');
		$objActSheet->getStyle('A1')->getAlignment()->setWrapText(true);
		$objActSheet->getStyle($mark_customer.'1')->getFont()->getColor()->setARGB('00000000');
		$objActSheet->getStyle($mark_customer.'1')->getAlignment()->setWrapText(true);
        $objActSheet->setCellValue('A1', L('CUSTOMER_INFO'));
		//设置背景色
		$objActSheet->getStyle('A1:'.$aa.'2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objActSheet->getStyle('A1:'.$aa.'2')->getFill()->getStartColor()->setARGB('DCFCCF');
		//$objActSheet->getStyle($mark_customer.'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		//$objActSheet->getStyle($mark_customer.'1')->getFill()->getStartColor()->setARGB('DCFCCF');
		$objActSheet->getRowDimension(1)->setRowHeight(28); //设置行高

		$current_page = intval($_GET['current_page']);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=pdcrm_customer_".date('Y-m-d',mktime())."_".$current_page.".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output');
		session('export_status', 0);
	}

	public function getCurrentStatus(){
		$this->ajaxReturn(intval(session('export_status')), 'success', 1);
	}
	/**
	*  客户导入
	*
	**/
	public function excelImport(){
		if($this->isPost()){
			if (isset($_FILES['excel']['size']) && $_FILES['excel']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('xls','xlsx');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error', L('ATTACHMENTS_TO_UPLOAD_DIRECTORY_CANNOT_WRITE'), $_SERVER['HTTP_REFERER']);
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error', $upload->getErrorMsg(), $_SERVER['HTTP_REFERER']);
				}else{
					$info =  $upload->getUploadFileInfo();
				}
			}
			if(is_array($info[0]) && !empty($info[0])){
				$savepath = $dirname . $info[0]['savename'];
				if($savepath){
					$this->ajaxReturn($savepath,'success',1);
				}else{
					$this->ajaxReturn(0,'error',0);
				}
			}else{
				alert('error', L('UPLOAD_FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->display();
		}
	}
	public function excelImportact(){
		$m_customer = D('Customer');
		$m_customer_data = D('CustomerData');
		$m_config = M('Config');
		import("ORG.PHPExcel.PHPExcel");
		$PHPExcel = new PHPExcel();
		$PHPReader = new PHPExcel_Reader_Excel2007();
		$savePath = $_GET['path'];
		if(!$PHPReader->canRead($savePath)){
			$PHPReader = new PHPExcel_Reader_Excel5();
		}
		$PHPExcel = $PHPReader->load($savePath);
		$currentSheet = $PHPExcel->getSheet(0);
		$allRow = $currentSheet->getHighestRow();
		$field_list = M('Fields')->where('model = \'customer\' && field != "customer_owner_id"')->order('order_id')->select();
		//判断客户数量
		$opennum = $m_config->where('name="opennum"')->getField('value');
		if($opennum){
			$outdays = $m_config->where('name="customer_outdays"')->getField('value');
			$outdate = empty($outdays) ? time() : time()-86400*$outdays;

			$c_outdays = $m_config -> where('name="contract_outdays"')->getField('value');
			$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
			$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
			$openrecycle = $m_config -> where('name="openrecycle"')->getField('value');

			if($openrecycle == 2){
				$c_where['_string'] = '(update_time > '.$outdate.' AND get_time > '.$contract_outdays.') OR is_locked = 1';
			}

			$c_where['owner_role_id'] = intval($_GET['owner_role_id']);
			$c_where['customer_status'] = '意向客户';
			$customer_count = M('customer')->where($c_where)->count();
			$customer_num = M('user')->where('role_id =%d',intval($_GET['owner_role_id']))->getField('customer_num');
			$new_counts = $customer_count + $allRow -2;
			if($customer_count >= $customer_num){
				$this->ajaxReturn('此负责人的客户数量已超出限制！操作失败','error',2);
			}elseif($new_counts > $customer_num){
				$diff_counts = $new_counts - $customer_num;
				$this->ajaxReturn('导入数量已超出此负责人的限制'.$diff_counts.'条！操作失败','error',2);
			}
		}

		$currentRow = intval($_GET['num']);
		if($currentRow+100 <=$allRow){
			$rows_excal = $currentRow+100;
		}else{
			$rows_excal = $allRow;
		}
		$message = array();
		for($currentRow;$currentRow <= $rows_excal;$currentRow++){
			$data = array();
			$data['owner_role_id'] = intval($_GET['owner_role_id']);
			$data['creator_role_id'] = session('role_id');
			$data['create_time'] = time();
			$data['update_time'] = time();
			$ascii = 65;
			$cv = '';
			foreach($field_list as $field){
				if($field['form_type'] == 'address'){
					$address = array();
					for($i=0;$i<4;$i++){
						$info = (String)$currentSheet->getCell($cv.chr($ascii).$currentRow)->getValue();
						$address[] = $info;

						$ascii++;
						if($ascii == 91){
							$ascii = 65;
							$cv .= chr(strlen($cv)+65);
						}
					}
					if ($field['is_main'] == 1){
						$data[$field['field']] =  implode(chr(10), $address);
					}else{
						$data_date[$field['field']] =  implode(chr(10), $address);
					}

					//地址信息转地理坐标（仅处理系统初始的地址字段）
					if ($field['field'] == 'address') {
						$address_arr = $address;
						if ($address_arr['3']) {
							$address_str = implode('', $address_arr);
							$ret = get_lng_lat($address_str);
							$data['lng'] = $ret['lng'] ?: 0;
							$data['lat'] = $ret['lat'] ?: 0;
						}
					}
				}else{
					$cell =$currentSheet->getCell($cv.chr($ascii).$currentRow);
					$info = $cell->getValue();
					if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
						$cellstyleformat=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();

						$formatcode=$cellstyleformat->getFormatCode();
						if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode)) {
							$info=gmdate("Y-m-d H:i", PHPExcel_Shared_Date::ExcelToPHP($info));
						}else{
							$info=PHPExcel_Style_NumberFormat::toFormattedString($info,$formatcode);
						}
					}else{
						$info = (String)$cell->getCalculatedValue();
					}

					if ($field['is_main'] == 1){
						$data[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? intval(strtotime($info)) : trim($info);
					}else{
						$data_date[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? intval(strtotime($info)) : trim($info);
					}

					$ascii++;
					if($ascii == 91){
						$ascii = 65;
						$cv .= chr(strlen($cv)+65);
					}
				}
			}
			//联系人字段
			$contacts_fields_list = M('Fields')->where('model = \'contacts\'')->order('order_id')->select();
			foreach($contacts_fields_list as $field){
				if($field['form_type'] == 'address'){
					$address = array();
					for($i=0;$i<4;$i++){
						$info = (String)$currentSheet->getCell($cv.chr($ascii).$currentRow)->getValue();
						$address[] = $info;
						$ascii++;
						if($ascii == 91){
							$ascii = 65;
							$cv .= chr(strlen($cv)+65);
						}
					}
					if ($field['is_main'] == 1){
						$contacts_data[$field['field']] = implode(chr(10), $address);
					}else{
						$data_fu[$field['field']] = implode(chr(10), $address);
					}
				}elseif($field['form_type'] != 'customer'){
					$cell =$currentSheet->getCell($cv.chr($ascii).$currentRow);
					$info = $cell->getValue();
					if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
						$cellstyleformat=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();

						$formatcode=$cellstyleformat->getFormatCode();
						if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode)) {
							$info=gmdate("Y-m-d H:i", PHPExcel_Shared_Date::ExcelToPHP($info));
						}else{
							$info=PHPExcel_Style_NumberFormat::toFormattedString($info,$formatcode);
						}
					}else{
						$info = (String)$cell->getCalculatedValue();
					}

					if ($field['is_main'] == 1){
						$contacts_data[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? intval(strtotime($info)) : trim($info);
					}else{
						$data_fu[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? intval(strtotime($info)) : trim($info);
					}

					$ascii++;
					if($ascii == 91){
						$ascii = 65;
						$cv .= chr(strlen($cv)+65);
					}
				}
			}

			if ($m_customer->create($data) && $m_customer_data->create($data_date)) {
				$error_message = '';
				$m_customer->get_time = time();
				$customer_id = $m_customer->add();
				$m_customer_data->customer_id = $customer_id;
				$m_customer_data->add();
				//添加联系人
				$m_contacts = M('contacts');
				$m_contacts_data = M('ContactsData');
				if($contacts_data['name'] != ''){
					$contacts_data['creator_role_id'] = intval($_GET['owner_role_id']);
					$contacts_data['create_time'] = time();
					$contacts_data['customer_id'] = $customer_id;
					$contacts_id = $m_contacts->add($contacts_data);
					$data_fu['contacts_id'] = $contacts_id ;
					$m_contacts_data->add($data_fu);
					//添加客户联系人（客户联系人关系表）
					$m_rContactsCustomer = M('rContactsCustomer');
					$m_rContactsCustomer->add(array('contacts_id'=>$contacts_id, 'customer_id'=>$customer_id));
					//设置首要联系人
					$m_customer->where('customer_id = %d', $customer_id)->setField('contacts_id', $contacts_id);
				}
			}else{
				$error_message = L('LINE ERROR',array($currentRow,$m_customer->getError().$m_customer_data->getError()));
				//清空error信息
				$m_customer->clearError();
				$m_customer_data->clearError();

				$error_flag = 1;
			}
			$temp['error_message'] = $error_message;
			$temp['no'] = $currentRow;
			$message[] = $temp;

			//出现错误时候停止
			if (intval($_GET['is_jump']) == 2 && $error_flag == 1) break;
		}
		$return['allrow'] = $allRow;
		$return['message'] = $message;
		if($return){
			$this->ajaxReturn($return,'success',1);
		}else{
			$this->ajaxReturn('','error',0);
		}
	}

	/**
	*  客户导入模板下载
	*
	**/
	public function excelImportDownload(){
		C('OUTPUT_ENCODE', false);
        import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();
		$objProps = $objPHPExcel->getProperties();
		$objProps->setCreator("pdcrm");
		$objProps->setLastModifiedBy("pdcrm");
		$objProps->setTitle("pdcrm Customer");
		$objProps->setSubject("pdcrm Customer Data");
		$objProps->setDescription("pdcrm Customer Data");
		$objProps->setKeywords("pdcrm Customer Data");
		$objProps->setCategory("pdcrm");
		$objPHPExcel->setActiveSheetIndex(0);
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('客户导入模板'.date('Y-m-d',time()));

        $ascii = 65;
        $cv = '';
        $customer_field_list = M('Fields')->where('model = \'customer\' && field != "customer_owner_id"')->order('order_id')->select();
		$contacts_fields_list = M('Fields')->where('model = \'contacts\' ')->order('order_id')->select();
		$j = 0;
        foreach($customer_field_list as $field){
			if($field['form_type'] == 'address'){
				for($i=0;$i<4;$i++){
					$j++;
					$address = array('所在省','所在市','所在县','街道信息');
					$objActSheet->setCellValue($cv.chr($ascii).'2',$address[$i]);
					$ascii++;
					$temp = $cv;
					if($ascii == 91){
						$ascii = 65;
						if($cv){
							$cv = chr(ord($cv)+1);
						}else{
							$cv = 'A';
						}
					}
					$mark_customer_cv = $ascii == 65 ? $temp : $cv;
					$mark_customer_ascii = $ascii;
				}
				$j--;
			}else{
				$j++;
				if($field['form_type'] == 'box'){
					eval('$setting='.$field['setting'].';');
					$select_value = implode(',',$setting['data']);
					//数据有效性   start
					$objValidation = $objActSheet->getCell($cv.chr($ascii).'3')->getDataValidation();
					$objValidation -> setType(PHPExcel_Cell_DataValidation::TYPE_LIST)
			           -> setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
			           -> setAllowBlank(false)
			           -> setShowInputMessage(true)
			           -> setShowErrorMessage(true)
			           -> setShowDropDown(true)
			           -> setErrorTitle('输入的值有误')
			           -> setError('您输入的值不在下拉框列表内.')
			           -> setPromptTitle('--请选择--')
			           -> setFormula1('"'.$select_value.'"');
			        //数据有效性  end
				}

				//检查该字段若必填，加上"*"
				$field['name'] = sign_required($field['is_validate'], $field['is_null'], $field['name']);

				$objActSheet->setCellValue($cv.chr($ascii).'2', $field['name']);
				$ascii++;
				$temp = $cv;
				if($ascii == 91){
					$ascii = 65;
					if($cv){
						$cv = chr(ord($cv)+1);
					}else{
						$cv = 'A';
					}
				}

				$mark_customer_cv = $ascii == 65 ? $temp : $cv;
				$mark_customer_ascii = $ascii;
			}
        }
		$mark_customer_jj = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
		$mark_customer = PHPExcel_Cell::stringFromColumnIndex($j); //生成Excel
		$temp2 = $ascii;
		if($mark_customer_ascii == 91){
			$mark_contacts_cv = 'A';
		}else{
			$mark_contacts_cv = $mark_customer_cv;
		}
		foreach($contacts_fields_list as $field){
			if($field['form_type'] == 'address'){
				for($i=0;$i<4;$i++){
					$j++;
					$address = array('所在省','所在市','所在县','详细地址');
					$objActSheet->setCellValue($cv.chr($ascii).'2',$address[$i]);
					$ascii++;
					$temp = $cv;
					if($ascii == 91){
						$ascii = 65;
						if($cv){
							$cv = chr(ord($cv)+1);
						}else{
							$cv = 'A';
						}
					}

					$mark_customer_cv = $ascii == 65 ? $temp : $cv;
					$mark_customer_ascii = $ascii;
				}
				$j--;
			}elseif($field['form_type'] != 'customer'){
				$j++;

				//检查该字段若必填，加上"*"
				$field['name'] = sign_required($field['is_validate'], $field['is_null'], $field['name']);

				$objActSheet->setCellValue($cv.chr($ascii).'2', $field['name']);
				$ascii++;
				$temp = $cv;
				if($ascii == 91){
					$ascii = 65;
					if($cv){
						$cv = chr(ord($cv)+1);
					}else{
						$cv = 'A';
					}
				}
				$mark_contacts_cv2 = $ascii == 65 ? $temp : $cv;
				$mark_contacts_ascii = $ascii;
			}
        }
		$mark_contacts_jj = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
		$mark_contacts = PHPExcel_Cell::stringFromColumnIndex($j-1); //生成Excel
        if($mark_customer_ascii == 65){
        	$customer_start_mark = 'A1';
        	$customer_end_mark = $mark_customer_cv.'Z1';
        	$contacts_start_mark = $mark_contacts_cv.'A1';

        }else{
        	$customer_start_mark = 'A1';
        	$customer_end_mark = $mark_customer_cv.chr($mark_customer_ascii-1).'1';
        	$contacts_start_mark = $mark_contacts_cv.chr($mark_customer_ascii).'1';
        }

        if($mark_contacts_ascii == 65){
        	$contacts_end_mark = $mark_contacts_cv.'Z1';

        }else{
        	$contacts_end_mark = $mark_contacts_cv2.chr($mark_contacts_ascii-1).'1';
        }

        $objActSheet->mergeCells('A1:'.$mark_customer_jj.'1');
		$objActSheet->mergeCells($mark_customer.'1:'.$mark_contacts.'1');
		$objActSheet->getStyle('A1:'.$mark_customer.'1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平居中
		$objActSheet->getStyle('A1:'.$mark_customer.'1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
		$objActSheet->getRowDimension(1)->setRowHeight(28); //设置行高
		$objActSheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFF0000');
		$objActSheet->getStyle('A1')->getAlignment()->setWrapText(true);
		$objActSheet->getStyle($contacts_start_mark)->getFont()->getColor()->setARGB('FFFF0000');
		$objActSheet->getStyle($contacts_start_mark)->getAlignment()->setWrapText(true);
        $content = '客户信息（*代表必填项）';
        $objActSheet->setCellValue('A1', $content);
        $objActSheet->setCellValue($mark_customer.'1', '联系人信息（*代表必填项）');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=pdcrm_customer.xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output');
    }

	/**
	*  ajax返回客户列表（搜索使用）
	*
	**/
	public function getCustomerList(){
		$idArray = getPerByAction(MODULE_NAME,'index');
		$customerList = M('customer')->where('owner_role_id in (%s) and is_deleted = 0', implode(',', $idArray))->select();
		$this->ajaxReturn($customerList, '', 1);
	}

	/**
	*  客户统计
	*
	**/
	public function analytics(){
		$d_customer = D('CustomerView');
		$m_customer = M('Customer k');
		$m_contract = M('Contract');
		$m_business = M('Business');
		$m_receivables = M('Receivables');
		$m_receivingorder = M('Receivingorder');

		//是否仅查询销售岗
		$user_type = $_REQUEST['user_type'] ? 1 : '';

		//权限判断
		$below_ids = getPerByAction(MODULE_NAME,ACTION_NAME,false);

		if(intval($_GET['role'])){
			$role_ids = array(intval($_GET['role']));
		}else{
			if(intval($_GET['department'])){
				$department_id = intval($_GET['department']);
				foreach(getRoleByDepartmentId($department_id, true) as $k=>$v){
					$role_ids[] = $v['role_id'];
				}
			}else{
				$type_role_array = array();
				if(empty($user_type)){
					//过滤销售岗角色用户
					$m_user = M('User');
					foreach($below_ids as $k=>$v){
						$user_type = '';
						$user_type = $m_user->where('role_id = %d',$v)->getField('type');
						if($user_type == 1){
							$type_role_array[] = $v;
						}
					}
					$role_id_array = $type_role_array;
				}else{
					$role_id_array = $below_ids;
				}
			}
		}

		if($role_ids){
			//数组交集
			$role_id_array = array_intersect($role_ids, $below_ids);
		}
		//时间段搜索
		if($_GET['between_date']){
			$between_date = explode(' - ',trim($_GET['between_date']));
			if($between_date[0]){
				$start_time = strtotime($between_date[0]);
			}
			$end_time = $between_date[1] ?  strtotime(date('Y-m-d 23:59:59',strtotime($between_date[1]))) : strtotime(date('Y-m-d 23:59:59',time()));
		}else{
			$start_time = strtotime(date('Y-m-01 00:00:00'));
			$end_time = strtotime(date('Y-m-d H:i:s'));
		}
		$this->between_date = $_GET['between_date'] ? trim($_GET['between_date']) : date('Y-m-01').' - '.date('Y-m-d');
		$this->start_date = date('Y-m-d',$start_time);
		$this->end_date = date('Y-m-d',$end_time);

		$where_role_id = array('in', implode(',', $role_id_array));
		$this->where_role_id = implode(',', $role_id_array);
		$where_source['creator_role_id'] = $where_role_id;
		$where_industry['owner_role_id'] = $where_role_id;
		$where_renenue['creator_role_id'] = $where_role_id;
		$where_employees['creator_role_id'] = $where_role_id;

		if($start_time){
			$where_create_time = array(array('elt',$end_time),array('egt',$start_time), 'and');
			$where_source['create_time'] = $where_create_time;
			$where_industry['create_time'] = $where_create_time;
			$where_renenue['create_time'] = $where_create_time;
			$where_employees['create_time'] = $where_create_time;

		}else{
			$where_source['create_time'] = array('elt',$end_time);
			$where_industry['create_time'] = array('elt',$end_time);
			$where_renenue['create_time'] = array('elt',$end_time);
			$where_employees['create_time'] = array('elt',$end_time);
		}

		if($start_time){
			$create_time= array(array('elt',$end_time),array('egt',$start_time), 'and');
		}else{
			$create_time = array('elt',$end_time);
		}
		$own_customer_count_total = 0;
		$success_customer_count_total = 0;
		$success_customer_rate_total = 0;
		$own_business_count_total = 0;
		$success_business_count_total = 0;
		$success_business_rate_total = 0;
		$contract_price_total = 0;
		$contract_average_total = 0;
		$receivingorder_price_total = 0;
		$un_receivingorder_price_total = 0;
		$receivingorder_rate_total = 0;
		$contract_count_total = 0;
		$effect_customer_count = $newadd_customer_count = $first_deal_customer_count = $again_deal_customer_count = 0;
		$effect_customer_total = $newadd_customer_total = $first_deal_customer_total = $again_deal_customer_total = 0;

		// $busi_customer_array = M('Business')->getField('customer_id', true);
		// $busi_customer_id = implode(',', $busi_customer_array);
		$d_customer = D('Customer');
		$v_customer = D('CustomerView');
		if ($_GET['content_id'] == 1 || $_GET['content_id'] == '') {
			foreach($role_id_array as $v){
				$user = getUserByRoleId($v);
				//过滤已停用用户
				if($user['status'] == 1){
					$tmp = array('is_deleted'=>0, 'owner_role_id'=>$v, 'create_time'=>$create_time);
					$tmp += $d_customer->where_belong();
					$own_customer_ids = $m_customer->where($tmp)->getField('customer_id',true);
					$own_customer_count = count($own_customer_ids);
					//查询条件
					$tmp = array('is_deleted'=>0, 'owner_role_id'=>$v);
					$tmp += $d_customer->where_belong();
					$where_customer_ids = $m_customer->where()->getField('customer_id',true);

					//已成交（客户签订合同审核通过）
					// $contract_customer_ids = array();
					// if ($own_customer_ids) {
					// 	$contract_customer_ids = $m_contract->where(array('customer_id'=>array('in',$own_customer_ids),'is_checked'=>1))->getField('customer_id',true);
					// }
					// //去除数组重复
					// $contract_customer_ids = $contract_customer_ids ? array_unique($contract_customer_ids) : array();
					// $success_customer_count = $contract_customer_ids ? count($contract_customer_ids) : 0;

					//已成交(客户状态为已成交)
					$success_customer_count = 0;
					if ($own_customer_ids) {
						$tmp = array('is_deleted'=>0, 'owner_role_id'=>$v, 'create_time'=>$create_time,'customer_status'=>'已成交客户');
						$tmp += $d_customer->where_belong();
						$success_customer_count = $m_customer->where($tmp)->count();
					}

					//成交率
					$success_customer_rate = round($success_customer_count/$own_customer_count,2)*100;

					$own_business_count = 0;
					$contract_business_ids = array();
					//商机数
					if ($where_customer_ids) {
						// $own_business_ids = $m_business->where(array('customer_id'=>array('in',$where_customer_ids),'create_time'=>$create_time))->getField('business_id',true);
						$own_business_ids = $m_business->where(array('owner_role_id' => $v,'create_time'=>$create_time))->getField('business_id',true);
						$own_business_count = count($own_business_ids);
						//查询条件
						$where_business_ids = array();
						$where_business_ids = $m_business->where(array('customer_id'=>array('in',$where_customer_ids)))->getField('business_id',true);

						//赢单商机数
						if ($where_business_ids) {
							$contract_business_ids = $m_contract->where(array('business_id'=>array('in',$where_business_ids),'is_checked'=>1,'create_time'=>$create_time))->getField('business_id',true);
						}
					}

					//去除数组重复
					$contract_business_ids = $contract_business_ids ? array_unique($contract_business_ids) : array();
					$success_business_count = $contract_business_ids ? count($contract_business_ids) : 0;

					//商机赢单率
					$success_business_rate = round($success_business_count/$own_business_count,2)*100;

					//合同总金额
					$contract_price = '0';
					$contract_average = '0';
					$contract_count = 0;
					$receivables_ids = 0;
					if ($where_customer_ids) {
						$contract_price = $m_contract->where(array('customer_id'=>array('in',$where_customer_ids),'is_checked'=>1,'create_time'=>$create_time))->sum('price');
						$contract_price = round($contract_price,2);
						//平均合同金额

						$contract_count = $m_contract->where(array('customer_id'=>array('in',$where_customer_ids),'is_checked'=>1,'create_time'=>$create_time))->count();
						$contract_average = $contract_price ? round($contract_price/$contract_count,0) : '0';

						//回款金额
						$receivables_ids = $m_receivables->where(array('customer_id'=>array('in',$where_customer_ids)))->getField('receivables_id',true);
					}

					$receivingorder_price = '0';
					if($receivables_ids){
						$receivingorder_price = $m_receivingorder->where(array('receivables_id'=>array('in',$receivables_ids),'status'=>1,'create_time'=>$create_time))->sum('money');
					}
					$receivingorder_price = $receivingorder_price ? round($receivingorder_price,2) : '0';

					//未回款金额
					$un_receivingorder_price = '0';
					$un_receivingorder_price = $contract_price-$receivingorder_price;
					//回款比例
					$receivingorder_rate = '0';
					$receivingorder_rate = $receivingorder_price ? round($receivingorder_price/$contract_price,2)*100 : '0';

					// 有效客户数
					$effect_customer_total += $effect_customer_count = $m_customer->where(array('owner_role_id' => (int) $v, 'customer_status' => array('NEQ', '失败客户')))->count();
					// 新增客户数
					$newadd_customer_total += $newadd_customer_count = $m_customer->where(array('owner_role_id' => (int) $v, 'create_time' => array('BETWEEN', array($start_time, $end_time))))->count();
					// 首次成交客户数
					$first_deal_customer_total += $first_deal_customer_count = (int) $m_customer
						->join('LEFT JOIN ' . C('DB_PREFIX') .'contract h ON k.customer_id=h.customer_id')
						->where(array('h.renew_contract_id' => 0, 'h.is_checked' => 1, 'h.create_time' => array('BETWEEN', array($start_time, $end_time)), 'k.owner_role_id' => $v))
						->group('k.customer_id')
						->count();
					// 再次成交客户数
					$again_deal_customer_total += $again_deal_customer_count = (int) $m_customer
						->join('LEFT JOIN ' . C('DB_PREFIX') .'contract h ON k.customer_id=h.customer_id')
						->where(array('h.renew_contract_id' => array('neq', 0), 'h.is_checked' => 1, 'h.create_time' => array('BETWEEN', array($start_time, $end_time)), 'k.owner_role_id' => $v))
						->group('k.customer_id')
						->count();

					$reportList[] = array(
						"user"=>$user,
						"own_customer_count"=>$own_customer_count,
						"success_customer_count"=>$success_customer_count,
						"success_customer_rate"=>$success_customer_rate,
						"own_business_count"=>$own_business_count,
						"success_business_count"=>$success_business_count,
						'contract_business_ids' => implode(',', $contract_business_ids),
						"success_business_rate"=>$success_business_rate,
						"contract_price"=>$contract_price,
						"contract_average"=>$contract_average,
						"receivingorder_price"=>$receivingorder_price,
						"un_receivingorder_price"=>$un_receivingorder_price,
						"receivingorder_rate"=>$receivingorder_rate,
						"effect_customer_count"=>$effect_customer_count,
						"newadd_customer_count"=>$newadd_customer_count,
						"first_deal_customer_count"=>$first_deal_customer_count,
						"again_deal_customer_count"=>$again_deal_customer_count,
					);

					$own_customer_count_total += $own_customer_count;
					$success_customer_count_total += $success_customer_count;
					$own_business_count_total += $own_business_count;
					$success_business_count_total += $success_business_count;
					$contract_price_total += $contract_price;
					$contract_count_total += $contract_count;
					$receivingorder_price_total += $receivingorder_price;
					$un_receivingorder_price_total += $un_receivingorder_price;
				}
			}
			//总客户成交率
			$success_customer_rate_total = round($success_customer_count_total/$own_customer_count_total,2)*100;
			//总商机赢单率
			$success_business_rate_total = round($success_business_count_total/$own_business_count_total,2)*100;
			//合同平均金额
			$contract_average_total = $contract_price_total ? round($contract_price_total/$contract_count_total,0) : '0';
			//回款比例
			$receivingorder_rate_total = $receivingorder_price_total ? round($receivingorder_price_total/$contract_price_total,2)*100 : '0';

			$this->total_report = array("own_customer_count_total"=>$own_customer_count_total,
				 "success_customer_count_total"=>$success_customer_count_total,
				"success_customer_rate_total"=>$success_customer_rate_total,
				"own_business_count_total"=>$own_business_count_total,
				"success_business_count_total"=>$success_business_count_total,
				"success_business_rate_total"=>$success_business_rate_total,
				"contract_price_total"=>$contract_price_total,
				"contract_average_total"=>$contract_average_total,
				"receivingorder_price_total"=>$receivingorder_price_total,
				"un_receivingorder_price_total"=>$un_receivingorder_price_total,
				"receivingorder_rate_total"=>$receivingorder_rate_total,
				"effect_customer_total"=>$effect_customer_total,
				"newadd_customer_total"=>$newadd_customer_total,
				"first_deal_customer_total"=>$first_deal_customer_total,
				"again_deal_customer_total"=>$again_deal_customer_total,
			);
			$this->reportList = $reportList;


			$all_fields = D('Customer')->analytics_fields();
			$fields_id = array_filter(explode(',', $_GET['fields']));
			$fields_id = $fields_id ?: y_array_column($all_fields, 'id');;
			$this->fields = array_filter($all_fields, function($val) use($fields_id) {
				return in_array($val['id'], $fields_id);
			});
		}else{
			$owner_role_ids = array();
			foreach($role_id_array as $v){
				$user = getUserByRoleId($v);
				if($user['status'] == 1){
					$owner_role_ids[] = $v;
				}
			}
			$own_customer_count_total = $d_customer->where(array('is_deleted'=>0, 'owner_role_id'=>array('in',$owner_role_ids), 'create_time'=>$create_time))->count();
		}
		$this->own_customer_count_total = $own_customer_count_total;

		//自定义字段统计（下拉、单选）
		$fields_list = M('Fields')->where(array('model'=>'customer','form_type'=>'box','setting'=>array(array('like',"%'type'=>'select'%"),array('like',"%'type'=>'radio'%"),'or')))->select();
		$fields_array = array();
		$fields_list_array = array();
		foreach ($fields_list as $k=>$v) {
			$fields_array[$k]['field'] = $v['field'];
			$fields_array[$k]['field_name'] = $v['name'];
			$fields_list_array[] = $v['field'];
		}

		$field = $_REQUEST['field'] ? trim($_REQUEST['field']) : 'origin';
		$field_name = '';

		if (in_array($field, $fields_list_array)) {
			foreach ($fields_list as $k=>$v) {
				if ($v['field'] == $field) {
					//自定义字段统计图
					$field_count_array = array();
					$setting_str_field = '$fieldList='.$v['setting'].';';
					eval($setting_str_field);
					$field_total_count = 0;
					//自定义字段统计表格
					$field_count_list = array();
					foreach($fieldList['data'] as $k1=>$v1){
						unset($where_source[$v['field']]);
						$where_source[$v['field']] = $v1;
						$target_count = $v_customer->where($where_source)->count();
						$field_count_array[] = '['.'"'.$v1.'",'.$target_count.']';
						$field_total_count += $target_count;

						$field_count_list[$k1]['name'] = $v1;
						$field_count_list[$k1]['num'] = $target_count;
						$field_count_list[$k1]['rate'] = round($target_count/$own_customer_count_total,4)*100;
					}
					$this->other_where = implode(',', $fieldList['data']);
					$field_count_array[] = '["'.L('OTHER').'",'.($own_customer_count_total-$field_total_count).']';
					$this->field_count = implode(',', $field_count_array);
					$field_count_list[] = array('other' => true, 'name'=>'其他','num'=>$own_customer_count_total-$field_total_count,'rate'=>round(($own_customer_count_total-$field_total_count)/$own_customer_count_total,3)*100);
					$this->field_count_list = $field_count_list;
					$field_name = $v['name'];
					break;
				}
			}
		}
		$this->field = $field;
		$this->field_name = $field_name;
		$this->fields_array = $fields_array;
		$this->field_count_list = $field_count_list;

		// $below_ids = getPerByAction(MODULE_NAME,ACTION_NAME,false);
		$this->roleList = getUserByRoleIdArray($below_ids);

		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->alert = parseAlert();
		$this->assign('field',$field);
		$this->assign('departmentList', $departmentList);

		$value = $_GET['c_between_date'] ?: null;
		$this->date_range_picker = dateRangePicker($start_time, $end_time, $value);
		// $this->daterange = daterange();

		$this->type_id = intval($_GET['type_id']);
		$this->content_id = intval($_GET['content_id']);
		$this->display();
	}

	/**
	*  检查用户是否符合领取或被分配到客户池资源资格
	*  @type 1：领取 2：分配
	**/
	private function check_customer_limit($user_id, $type){
		$m_config = M('config');
		$m_customer_record = M('customer_record');
		$customer_limit_condition = $m_config->where('name = "customer_limit_condition"')->getField('value');

		$today_begin = strtotime(date('Y-m-d',time()));
		$today_end = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$this_week_begin = ($today_begin -((date('w'))-1)*86400);
		$this_week_end = ($today_end+(7-(date('w')==0?7:date('w')))*86400);
		$this_month_begain = strtotime(date('Y-m', time()).'-01 00:00:00');
		$this_month_end = mktime(23,59,59,date('m'),date('t'),date('Y'));

		$condition['user_id'] = $user_id;
		$condition['type'] = $type;
		if($customer_limit_condition == 'day'){
			$condition['start_time'] = array('between', array($today_begin, $today_end));
		}elseif($customer_limit_condition == 'week'){
			$condition['start_time'] = array('between', array($this_week_begin, $this_week_end));
		}elseif($customer_limit_condition == 'month'){
			$condition['start_time'] = array('between', array($this_month_begain, $this_month_end));
		}

		$customer_record = $m_customer_record->where($condition)->count();
		return $customer_record;
	}

	/**
	*  锁定/解锁客户
	*
	**/
	public function customerlock(){
		if(intval($_GET['customer_id'])){
			$m_customer = M('Customer');
			$customer = $m_customer->where('customer_id = %d ', intval($_GET['customer_id']))->find();
			if(!empty($customer)){
				if($customer['is_locked']){
					if($m_customer->where('customer_id = %d ', intval($_GET['customer_id']))->setField('is_locked',0)){
						$data = array();
						$data['get_time'] = time();
						$data['update_time'] = time();
						$m_customer->where('customer_id = %d ', intval($_GET['customer_id']))->save($data);
						alert('success', L('UNLOCKING_SUCCESS'), $_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('UNLOCKING_FAILD'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					if($m_customer->where('customer_id = %d ', intval($_GET['customer_id']))->setField('is_locked',1)){
						alert('success', L('LOCKING_SUCCESS'), $_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('UNLOCKING_FAILD'), $_SERVER['HTTP_REFERER']);
					}
				}
			}else{
				alert('error', L('RECORD_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}

	/**
	 * 首页客户来源统计
	 * @ level 0:自己的数据  1:自己和下属的数据
	 **/
	public function getCustomerOriginal	(){
		$dashboard = M('user')->where('user_id = %d', session('user_id'))->getField('dashboard');
		$widget = unserialize($dashboard);
		$id = intval($_GET['id']);
		foreach($widget['dashboard'] as $k=>$v){
			if($v['widget'] == 'Customerorigin' && $v['id'] == $id){
				if($v['level'] == '1'){
					$where['owner_role_id'] = array('in',getSubRoleId());
				}else{
					$where['owner_role_id'] = array('eq', session('role_id'));
				}
			}
		}

		$m_customer = M('customer');
		$original = $m_customer->Distinct(true)->field('origin')->getField('origin',true);
		$originalArr = array_filter($original);
		$customerArr = array();
		$where['is_deleted'] = array('eq',0);
		foreach($originalArr as $v){
			$where['origin'] = array('eq',$v);
			$origin_count = $m_customer ->where($where)->count();
			$customerArr['series'][] = array('value'=>intval($origin_count), 'name'=>$v);
			$customerArr['legend'][] = $v;
		}
		$this->ajaxReturn($customerArr,'success',1);
	}

	/**
	 * 置顶
	 * module 模块
	 * module_id 模块ID
	 **/
	public function settop(){
		$module = $_REQUEST['module'] ? trim($_REQUEST['module']) : '';
		$module_id = $this->_request('module_id','intval');
		if(!$module_id || !$module){
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$m_top = M('Top');
		$module_info = M($module)->where(array($module.'_id'=>$module_id,'is_deleted'=>0))->find();
		if(!$module_info){
			alert('error','数据不存在或已删除！',$_SERVER['HTTP_REFERER']);
		}
		//判断权限
		$below_ids = getPerByAction($module,'index');
		if($module == 'customer' && !in_array($module_info['owner_role_id'],$below_ids)){
			//共享给我的客户
			$share_customer_info = M('customerShare')->where(array('by_sharing_id'=>session('role_id'),'customer_id'=>$module_id))->find();
			if (!$share_customer_info) {
				alert('error','您没有此权利！',$_SERVER['HTTP_REFERER']);
			}
		}
		if($module == 'business'){
			$customer_info = M('Customer')->where(array('customer_id'=>$module_info['customer_id']))->field('owner_role_id')->find();
			if(!in_array($customer_info['owner_role_id'],$below_ids)){
				alert('error','您没有此权利！',$_SERVER['HTTP_REFERER']);
			}
		}
		$top_info = $m_top->where(array('module_id'=>$module_id,'module'=>$module,'create_role_id'=>session('role_id')))->find();
		if($top_info){
			if($m_top->where(array('module_id'=>$module_id,'module'=>$module,'create_role_id'=>session('role_id')))->delete()){
				alert('success','取消置顶成功！',$_SERVER['HTTP_REFERER']);
			}else{
				alert('error','操作失败，请重试！',$_SERVER['HTTP_REFERER']);
			}
		}else{
			$top_data = array();
			$top_data['create_role_id'] = session('role_id');
			$top_data['top_time'] = time();
			$top_data['module_id'] = $module_id;
			$top_data['module'] = $module;
			if($m_top->add($top_data)){
				alert('success','置顶成功！',$_SERVER['HTTP_REFERER']);
			}else{
				alert('error','操作失败，请重试！',$_SERVER['HTTP_REFERER']);
			}
		}
	}

	/**
	*客户地区统计
	*
	**/

	public function city_analytics(){
		//权限判断
		$below_ids = getPerByAction('customer','index',true);
		$m_config = M('Config');
		$outdays = $m_config -> where('name="customer_outdays"')->getField('value');
		$outdate = empty($outdays) ? 0 : time()-86400*$outdays;
		$c_outdays = $m_config -> where('name="contract_outdays"')->getField('value');
		$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
		$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
		$openrecycle = $m_config -> where('name="openrecycle"')->getField('value');
		$params = array();

		$params[] = "department=" . intval($_GET['department']);
		if(intval($_GET['role'])){
			if(in_array($_GET['role'],$below_ids)){
				$role_id_array = array(intval($_GET['role']));
			}
			$params[] = "role=" . intval($_GET['role']);
		}else{
			if(intval($_GET['department'])){
				$department_id = intval($_GET['department']);
				foreach(getRoleByDepartmentId($department_id, true) as $k=>$v){
					if(in_array($v['role_id'],$below_ids)){
						$role_id_array[] = $v['role_id'];
					}
				}
			}else{
				$role_array = getPerByAction('customer','index',false);
				$role_id_array = $role_array;
			}
		}

		//时间段搜索
		// if($_GET['between_date']){
		// 	$between_date = explode(' - ',trim($_GET['between_date']));
		// 	if($between_date[0]){
		// 		$start_time = strtotime($between_date[0]);
		// 	}
		// 	$end_time = $between_date[1] ?  strtotime(date('Y-m-d 23:59:59',strtotime($between_date[1]))) : strtotime(date('Y-m-d 23:59:59',time()));
		// }else{
		// 	$start_time = strtotime(date('Y-m-01 00:00:00'));
		// 	$end_time = strtotime(date('Y-m-d H:i:s'));
		// }
		// $between_date = $_GET['between_date'] ? trim($_GET['between_date']) : date('Y-m-01').' - '.date('Y-m-d');
		// $this->between_date = $between_date;
		// $this->start_date = date('Y-m-d',$start_time);
		// $this->end_date = date('Y-m-d',$end_time);

		//时间段查询
		// if($start_time){
		// 	$where_create_time = array(array('elt',$end_time),array('egt',$start_time), 'and');
		// }else{
		// 	$where_create_time = array('elt',$end_time);
		// }
		// $params[] = "between_date=" . $between_date;

		$m_customer = M('Customer');
		$d_v_customer = D('CustomerView');
		$m_r_contacts_customer = M('RContactsCustomer');

		//查询地区
		$m_area = M('Area');
		$parent_id = $_GET['parent_id'] ? intval($_GET['parent_id']) : '';
		if ($parent_id) {
			$area_list = $m_area->where(array('parent_id'=>$parent_id))->select();
			$parent_name = $m_area->where('id = %d',$parent_id)->getField('name');
		} else {
			$area_list = $m_area->where(array('level'=>1))->select();
		}

		//查询有省份的数组
		foreach($area_list as $key=>$val){
			$where_area = array();

			if ($parent_id) {
				$where_area['address'] = array('like',array('%'.$parent_name.'%','%'.$val['name'].'%'),'AND');
			} else {
				$where_area['address'] = array('like','%'.$val['name'].'%');
			}
			// $where_area['create_time'] = $where_create_time;

			//权限判断（过滤客户池数据）
			if($openrecycle == 2){
				$where_area['_string'] = '(update_time > '.$outdate.' AND get_time > '.$contract_outdays.') OR is_locked = 1';
			}
			$where_area['owner_role_id'] = array('in',$role_id_array);
			$customer_count = $d_v_customer->where($where_area)->find();
			if(empty($customer_count)){
				unset($area_list[$key]);
			}
		}
		$area_list = array_merge($area_list);
		$area_count = count($area_list);
		$area_list_arr = array();
		foreach($area_list as $key=>$val){
			$where_area = array();
			if ($parent_id) {
				$where_area['address'] = array('like',array('%'.$parent_name.'%','%'.$val['name'].'%'),'AND');
			} else {
				$where_area['address'] = array('like','%'.$val['name'].'%');
			}
			// $where_area['create_time'] = $where_create_time;

			//权限判断（过滤客户池数据）
			if($openrecycle == 2){
				$where_area['_string'] = '(update_time > '.$outdate.' AND get_time > '.$contract_outdays.') OR is_locked = 1';
			}
			$where_area['owner_role_id'] = array('in',$role_id_array);

			$customer_count = $d_v_customer->where($where_area)->count();
			$area_list_arr[$key]['customer_count'] = $customer_count;
			$area_list_arr[$key]['area_info'] = $val;
			$state_name = '';
			$city_name = '';
			if ($val['level'] == 1) {
				$state_name = $val['name'];
			} elseif ($val['level'] == 2) {
				$state_name = $m_area->where(array('id'=>$val['parent_id']))->getField('name');
				$city_name = $val['name'];
			}
			$area_list_arr[$key]['area_info']['state'] = $state_name;
			$area_list_arr[$key]['area_info']['city'] = $city_name;
		}

		$this->area_count = $area_count+1;
		$this->area_list_arr = $area_list_arr;

		$idArray = getPerByAction('customer','index',false);
		$roleList = array();
		foreach($idArray as $roleId){
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->roleList = $roleList;
		$url = getCheckUrlByAction('customer','index');
		$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);
		$this->parameter = implode('&', $params);
		$this->alert = parseAlert();
		$this->display();
	}
	//客户批量分享
	public function share(){
		if($this->isPost()){
			$m_share =M('customerShare');
			$m_user = M('user');
			$customer_ids = explode(',',$_POST['customer_id']);
			$to_role = $_POST['to_role_id'];
			$j = 0;
			foreach($customer_ids as $ko=>$vo){
				$share_name_str = '';
				foreach($to_role as $k=>$v){
					$by_sharing_name = $m_user ->where('role_id =%d',$v)->getField('full_name');
					$share_name_str .= $by_sharing_name.',';
					$where['by_sharing_id'] = $v;
					$where['customer_id'] = $vo;
					$result = $m_share ->where($where)->find();
					if($result == false){
						if($v != session('role_id')){
							$data['share_role_id'] = session('role_id');
							$data['by_sharing_id'] = $v;
							$data['customer_id'] = $vo;
							$data['share_time']  = time();
							if($m_share -> add($data)){
								$i++;
							}
						}
					}else{
						$j++;
					}
				}
				add_record('客户分享','将此客户分享给'.$share_name_str.'！','customer',$vo);
			}
			if($i > 0){
				alert('success','分享成功！',$_SERVER["HTTP_REFERER"]);
			}else{
				if($j > 0){
					alert('error','分享失败！不能重复分享！',$_SERVER["HTTP_REFERER"]);
				}else{
					alert('error','分享失败！',$_SERVER["HTTP_REFERER"]);
				}
			}
		}else{
			$d_role = D('RoleView');
			$customer_ids = $_GET['customer_id'];
			$departments_list = M('roleDepartment')->select();
			$is_one = intval($_GET['is_one']);
			if($is_one == 1){
				$by_sharing_ids = M('customerShare')->where('customer_id =%d',$customer_ids)->getField('by_sharing_id',true);
			}
			$status = ' && status != 3';
			if (!M('config')->where('name="search_disable_user"')->getField('value')) {
				$status = ' && status = 1';
			}
			foreach($departments_list as $k=>$v){
				$roleList = $d_role->where('position.department_id = %d'.$status, $v['department_id'])->select();
				foreach($roleList as $ki=>$vi){
					if(in_array($vi['role_id'],$by_sharing_ids)){
						$roleList[$ki]['is_checked'] = 1;
					}
				}
				$departments_list[$k]['user'] = $roleList;
			}
			$this->customer_id = $customer_ids;
			$this->departments_list = $departments_list;
			$this->display();
		}
	}
	public function ajax_share(){
		if($this->isPost()){
			$m_user = M('user');
			$m_share = M('customerShare');
			$customer_id = intval($_POST['customer_id']);
			$share_owner_ids = $_POST['share_owner_ids'];
			$new_arr = array();
			$share_name_str = '';
			if($share_owner_ids){
				foreach($share_owner_ids as $k=>$v){
					$where['by_sharing_id'] = $v;
					$where['customer_id'] = $customer_id;
					$result = $m_share ->where($where)->find();
					if($result == false){
						if($v != session('role_id')){
							$data['share_role_id'] = session('role_id');
							$data['by_sharing_id'] = $v;
							$data['customer_id'] = $customer_id;
							$data['share_time']  = time();
							if($share_id = $m_share -> add($data)){
								$i++;
								$f_data['by_sharing_name'] = $m_user ->where('role_id =%d',$v)->getField('full_name');
								$share_name_str .= $f_data['by_sharing_name'].',';
								$f_data['by_sharing_id'] = $v;
								$f_data['share_id'] = $share_id;
								$f_data['share_name'] = $m_user ->where('role_id =%d',session('role_id'))->getField('full_name');
								$f_data['share_time'] = date('Y-m-d H:i',time());
								$f_data['img'] = $m_user ->where('role_id =%d',$v)->getField('img');
								$new_arr[] = $f_data;
							}
						}
					}
				}
				if($i > 0){
					add_record('客户分享','将此客户分享给'.$share_name_str.'！','customer',$customer_id);
					$this->ajaxReturn($new_arr,'添加成功！',1);
				}else{
					$this->ajaxReturn('','添加失败！',0);
				}
			}else{
				$this->ajaxReturn('','参数错误！',0);
			}
		}
	}
	//取消客户共享
	public function close_share(){
		$m_share = M('customerShare');
		$customer_ids = $_GET['customer_ids'];
		if($customer_ids){
			$customer_arr = explode(',',$customer_ids);
			$where['customer_id'] = array('in',$customer_arr);
			$where['share_role_id'] = session('role_id');
			$result = $m_share ->where($where)->delete();
			if($result){
				foreach($customer_arr as $k=>$v){
					add_record('取消分享','取消了此客户的分享！','customer',$v);
				}
				alert('success','取消成功！',$_SERVER['HTTP_REFERER']);
			}else{
				alert('error','取消失败！',$_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error','请先选择客户信息！',$_SERVER['HTTP_REFERER']);
		}

		$this->display();
	}

	//客户分享列表
	public function share_list(){
		$m_share = M('customerShare');
		$m_user = M('user');
		$customer_id = intval($_GET['customer_id']);
		$share_list = $m_share ->where('customer_id =%d',$customer_id)->select();
		$share_counts = $m_share ->where('customer_id =%d',$customer_id)->count();
		foreach($share_list as $k=>$v){
			$share_list[$k]['by_sharename'] = $m_user ->where('role_id =%d',$v['by_sharing_id'])->getField('full_name');
			$share_list[$k]['sharename'] = $m_user ->where('role_id =%d',$v['share_role_id'])->getField('full_name');
			$share_list[$k]['img'] = $m_user ->where('role_id =%d',$v['by_sharing_id'])->getField('thumb_path');
		}
		$this->share_counts = $share_counts;
		$this->share_list = $share_list;
		$this->customer_id = $customer_id;
		$this->display();
	}

	//移除成员信息
	public function yc_share(){
		$m_share = M('customerShare');
		$share_id = intval($_GET['share_id']);
		if(!checkPerByAction('Customer','close_share')){
			$this->ajaxReturn('','您没有此权限！',0);
		}
		if($share_id){
			$by_find = $m_share ->where('share_id =%d',$share_id)->field('by_sharing_id,customer_id')->find();
			$sharing_name = M('user')->where('role_id =%d',$by_find['by_sharing_id'])->getField('full_name');
			$result = $m_share ->where('share_id =%d',$share_id)->delete();
			if($result){
				add_record('移除','将'.$sharing_name.'从此客户分享中移除','customer',$by_find['customer_id']);
				$this->ajaxReturn('','移除成功！',1);
			}else{
				$this->ajaxReturn('','移除失败！',0);
			}
		}else{
			$this->ajaxReturn('','参数错误！',0);
		}
	}

	//客户转移
	public function transfer_edit(){
		if($this->isPost()){
			$m_customer = M('customer');
			$m_business = M('business');
			$m_contract = M('contract');
			$m_user = M('user');

			$customer_ids = explode(',',$_POST['customer_id']);
			$role_id = intval($_POST['role_id']);

			$role_info = $m_user->where('role_id = %d',$role_id)->field('full_name')->find();
			if (!$role_info || !$role_id) {
				alert('error', '参数错误！', $_SERVER['HTTP_REFERER']);
			}

			//判断负责人客户限制数
			$m_config = M('Config');
			$opennum = $m_config->where('name="opennum"')->getField('value');
			if ($opennum) {
				$outdays = $m_config->where('name="customer_outdays"')->getField('value');
				$outdate = empty($outdays) ? time() : time()-86400*$outdays;

				$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
				$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
				$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
				$openrecycle = $m_config -> where('name="openrecycle"')->getField('value');

				if ($openrecycle == 2) {
					$c_where['_string'] = '(update_time > '.$outdate.' AND get_time > '.$contract_outdays.') OR is_locked = 1';
				}

				$c_where['owner_role_id'] = $role_id;
				$c_where['customer_status'] = '意向客户';
				$customer_count = M('customer')->where($c_where)->count();
				$customer_num = M('user')->where('role_id =%d',$role_id)->getField('customer_num');
				if($customer_count >= $customer_num){
					alert('error', '此负责人的客户数量已超出限制！操作失败', U('customer/index'));
				}else{
					$sy_count = $customer_num - $customer_count ;
					$cus_counts = count($customer_ids);
					if($cus_counts > $sy_count){
						alert('error', '此负责人的最多可再拥有'.$sy_count.'个客户！操作失败', U('customer/index'));
					}
				}
			}

			$about_ids = $_POST['about_ids'];
			$transfer_content = trim($_POST['transfer_content']);
			if (empty($customer_ids)) {
				//判断是否选择客户信息
				alert('error', L('HAVE_NOT_CHOOSE_ANY_CONTENT'), $_SERVER['HTTP_REFERER']);
			} else {
				$where['customer_id'] = array('in',$customer_ids);
				$cus_list = $m_customer ->where($where)->field('customer_id,owner_role_id')->select();
				if($m_customer->where($where)->setField('owner_role_id',$role_id)){
					foreach($cus_list as $k=>$v){
						//查询相关客户的商机和合同
						$old_user_name = $m_user->where('role_id =%d',$v['owner_role_id'])->getField('full_name');
						add_record('客户转移','将此客户由 '.$old_user_name.' 转移给 '.$role_info['full_name'].' ,转移原因：'.$transfer_content,'customer',$v['customer_id']);
						if(in_array(2,$about_ids)){
							//判断是否转移商机
							$b_list = $m_business ->where('customer_id =%d',$v['customer_id'])->field('business_id,owner_role_id')->select();
							foreach($b_list as $ki=>$vi){
								//判断如果商机负责人等于客户负责人转移
								if($vi['owner_role_id'] == $v['owner_role_id']){
									$m_business ->where('business_id =%d',$vi['business_id'])->setField('owner_role_id',$role_id);
								}
							}
						}
						if(in_array(3,$about_ids)){
							//判断是否转移合同
							$c_list = $m_contract ->where('customer_id =%d',$v['customer_id'])->field('contract_id,owner_role_id')->select();
							foreach($c_list as $kk=>$vv){
								//判断如果合同负责人等于客户负责人转移
								if($vv['owner_role_id'] == $v['owner_role_id']){
									$m_contract ->where('contract_id =%d',$vv['contract_id'])->setField('owner_role_id',$role_id);
								}
							}
						}
					}
					alert('success',L('CUSTOMER_TRANSFER_SUCCESS'),$_SERVER['HTTP_REFERER']);
				}else{
					$owner_role_ids = $m_customer->where($where)->getField('owner_role_id',true);
					if(in_array($role_id,$owner_role_ids)){
						alert('error',L('INFORMATION_WITHOUT_CHANGE'),$_SERVER['HTTP_REFERER']);
					}else{
						alert('error',L('CUSTOMER_TRANSFER_FAILURE'),$_SERVER['HTTP_REFERER']);
					}
				}
			}
		}else{
			$customer_ids = $_GET['customer_id'];
			//获取下级和自己的岗位列表,搜索用
			$status = array('neq',3);
			if (!M('config')->where('name="search_disable_user"')->getField('value')) {
				$status = array('eq', 1);
			}
			$this->role_list = M('User')->where(array('status' => $status, 'role_id'=>array('neq',session('role_id'))))->field('role_id,full_name')->select();
			$this->customer_id = $customer_ids;
			$this->display();
		}
	}

	public function sendsms()
	{
		$current_sms_num = check_alert_sms();		// 可能会终止程序
		if ($_GET['ids'] != '') {
			$where['customer_id'] = array('IN', $_GET['ids']);
			$contacts_id_list = M('customer')->where($where)->field('customer_id,contacts_id,name')->select();
		} else {
			$where = $_GET;
			$contacts_id_list = D('CustomerView')->where($where)->field('customer_id,contacts_id,name')->select();
		}
		if (empty($contacts_id_list)) alert('error', '参数错误', $_SERVER['HTTP_REFERER']);
		$phoneNum = '';
		$error_count = 0;
		foreach ($contacts_id_list as $key => $val) {
			if ($val['contacts_id'] == 0) {
				$contacts_id = M('r_contacts_customer')->where('customer_id=%d', $val['customer_id'])->getField('contacts_id');
			} else {
				$contacts_id = $val['contacts_id'];
			}
			$contacts = M('contacts')->where('contacts_id=%d', $contacts_id)->field('telephone, name')->find();
			$phoneNum .= $contacts['telephone'] . ',' . $contacts['name'] . ',' . $val['name'] . "\n";
			if (!$contacts['telephone']) $error_count++;
		}
		$this->phoneNum = trim($phoneNum);
		$this->count = count($contacts_id_list);
		$this->error_count = $error_count;
		$this->surplus = $current_sms_num;
		$this->templateList = M('SmsTemplate')->order('order_id')->select();
		$this->display('Sms:dialog');
	}


	/**
	* 附近的客户
	* @param
	* @author lee
	**/
	public function nearby(){
		$m_customer = M('Customer');
		$m_user = M('User');

		$where['is_deleted'] = 0;
		//客户名称
		$name = trim($_REQUEST['name']);
		if ($name) {
			$where['name'] = array('like','%'.$name.'%');
		}

		//回收周期判断
		$m_config = M('Config');
		$outdays = $m_config->where('name="customer_outdays"')->getField('value');
		$outdate = empty($outdays) ? 0 : time()-86400*$outdays;

		$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
		$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
		$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
		$openrecycle = $m_config->where('name="openrecycle"')->getField('value');

		$act = trim($_GET['act']);
		if ($act == 1 || $act == '') {
			$below_ids = getPerByAction(MODULE_NAME,'nearby');
			$where['owner_role_id'] = array('in', $below_ids);
			if($openrecycle == 2){
				$where['_string'] = '(update_time > '.$outdate.' AND get_time > '.$contract_outdays.') OR is_locked = 1';
			}
		} elseif ($act == 2){
		    $where['_string'] = "owner_role_id=0 or (update_time < $outdate and is_locked = 0) or (get_time < $contract_outdays and is_locked = 0)";
		}

		$lng_lat = explode(',', $_REQUEST['lng_lat']);
		$lng = $map_info['center_lng'] = $lng_lat[0];
		$lat = $map_info['center_lat'] = $lng_lat[1];
		$raidus = $map_info['raidus'] = intval($_REQUEST['raidus']);

		if ($lng && $lat) {
			$ret = get_around($lng,$lat,$raidus);
			if (!$ret) {
				$data['status'] = 0;
				$data['info'] = '地图范围定义失败';
				$this->ajaxReturn($data);
			}

			$where['lng'] = array('between', array($ret['min_lng'],$ret['max_lng']));
			$where['lat'] = array('between', array($ret['min_lat'],$ret['max_lat']));
			$list = $m_customer->where($where)->field('customer_id,owner_role_id,name,address,lng,lat')->select();

			if (!empty($list)) {
				foreach ($list as $k => $v) {
					//计算客户距中心点的距离
					$distance = getDistance($lat, $lng, $v['lat'], $v['lng']);
					if ($distance <= $raidus) {
						$list[$k]['distance'] = round($distance / 1000,2);  //单位转换为公里

						//格式化地址信息
						$address_arr = array();
						$address_arr = explode(chr(10), $v['address']);
						$list[$k]['address'] = implode('', $address_arr);

						//负责人信息
						$list[$k]['owner_name'] = $m_user->where('role_id = %d', $v['owner_role_id'])->getField('full_name');

						//百度地图信息窗体js拼接信息
						$data_info .= '['.$v['lng'].','.$v['lat'].',"客户名称：'.$v['name'].'<br />&#12288;&#12288;地址：'.$list[$k]['address'].'<br />&#12288;负责人：'.$list[$k]['owner_name'].'<br />&#12288;距离约：'.$list[$k]['distance'].'公里"],';

						$list[$k]['content'] = '客户名称：'.$v['name'].'<br />&#12288;&#12288;地址：'.$list[$k]['address'].'<br />&#12288;负责人：'.$list[$k]['owner_name'].'<br />&#12288;距离约：'.$list[$k]['distance'].'公里';
					} else {
						unset($list[$k]);
					}
				}

				//根据相距距离由小到大，重新排序$list
				$temp_array = array();
				foreach ($list as $k => $v) {
					$temp_array[] = $v['distance'];
				}
				array_multisort($temp_array, SORT_ASC, $list);
			}

			$map_info['data_info'] = '['.$data_info.']';
			$map_info['lng_lat'] = $_REQUEST['lng_lat'];
			$map_info['address'] = $_REQUEST['address'];

			$this->assign('map_info', $map_info);
			$this->assign('list', $list);
		}

		$this->alert = parseAlert();
		$this->display();
	}

	//弹出地图，选择地址
	public function nearby_map(){
		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 统计用where条件
	 */
	public function _analytics_where($table)
	{
		//权限判断
		$where = D('Customer')->where_belong('private', $table.'.');
		$below_ids = getPerByAction('customer', 'analytics');
		$this->below_ids = $below_ids;
		$department = (int) $_GET['department'];
		$role = (int) $_GET['role'];
		if ($role) {
			$role_ids = array($role);
		} elseif ($department) {
			$role_ids = getRoleByDepartmentId($department, 1);
			$role_ids = y_array_column($role_ids, 'role_id');
		} else {
			$role_ids = $below_ids;
		}
		$role_ids = array_map('intval', $role_ids);
		if (!is_subset($role_ids, $below_ids)) {
			$role_ids = $below_ids;
		}
		$where[$table . '.owner_role_id'] = array('IN', $role_ids);
		return $where;
	}


	/**
	 * 合同金额Top10
	 */
	public function top_10()
	{
		if (!checkPerByAction('customer', 'analytics')) {
			alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
		}
		$where = $this->_analytics_where('k');
		$year = (int) $_GET['year'] ?: date('Y');
		$month = (int) $_GET['month'];
		if ($year && $month) {
			$start = strtotime(date($year . '-' . $month . '-01 00:00:00'));
			$end = strtotime(date('Y-m-d H:i:s', strtotime('+1 month', $start)));
			$where['h.due_time'] = array('BETWEEN', array($start, $end));
		} elseif ($year) {
			$start = strtotime(date($year . '-01-01 00:00:00'));
			$end = strtotime(date('Y-m-d H:i:s', strtotime('+1 year', $start)));
			$where['h.due_time'] = array('BETWEEN', array($start, $end));
		} elseif ($month) {
			$start = strtotime(date(date('Y-') . $month . '-01 00:00:00'));
			$end = strtotime(date('Y-m-d H:i:s', strtotime('+1 month', $start)));
			$where['h.due_time'] = array('BETWEEN', array($start, $end));
		}

		$m_customer = M('Customer k');
		$where['h.is_checked'] = 1;		// 审核通过
		$list = $m_customer
			->join('LEFT JOIN ' . C('DB_PREFIX') .'contract h ON k.customer_id=h.customer_id')
			->join('LEFT JOIN ' . C('DB_PREFIX') .'r_contract_sales r ON h.contract_id=r.contract_id')
			->join('LEFT JOIN ' . C('DB_PREFIX') .'sales s ON r.sales_id=s.sales_id')
			->where($where)
			->field('k.customer_id, k.name, sum(s.sales_price) as price')
			->group('h.customer_id')
			->order('price desc')
			->limit(0, 10)
			->select();
		$list = array_filter($list, function ($val) {
			return $val['price'] != null;
		});
		$this->roleList = getUserByRoleIdArray($this->below_ids);
		$this->departmentList = getDepartmentByUrl('customer', 'analytics');
		$this->year = $year ?: '';
		$this->month = $month ?: '';
		$this->customer = '"' . implode('","', y_array_column($list, 'name')) . '"';
		$this->price = implode(',', y_array_column($list, 'price'));
		$this->display();
	}


	/**
	 * 客户新增统计
	 */
	public function new_add()
	{
		if (!checkPerByAction('customer', 'analytics')) {
			alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
		}
		$title = array(1 => '新增客户数量', '客户成交率', '客户数量区域');
		$content_id = (int) $_GET['content_id'] ?: 1;
		$this->title = $title[$content_id];
		$m_customer = M('Customer k');
		$m_contract = M('Contract');
		$where = $this->_analytics_where('k');
		$year = (int) $_GET['year'] ?: date('Y');
		if ($content_id != 3) {
			$month_list = $year == date('Y') ? range(1, date('m')) : range(1, 12);
			$new_add = "{name:'当月新增客户数量', color:'#4472C4', data:[";
			$first = "{name:'首次成交率', color:'#4472C4', data:[";
			$renew = "{name:'续约率', color:'#FF964A', data:[";
			$loss = "{name:'流失率', color:'#B58AF7', data:[";
			foreach ($month_list as $month) {
				$start = strtotime($year . '-' . $month . '-01 00:00:00');
				$end = strtotime(date('Y-m-d H:i:s', strtotime('+1 month', $start)));
				$y_where = $where;
				$y_where['k.create_time'] = array('BETWEEN', array($start, $end));
				$count = (int) $m_customer->where($y_where)->count();		// 当月新增客户
				if ($content_id == 1) {
					$new_add .= "{y:" . $count . ",color:'#4472C4'},";
				} elseif ($content_id == 2) {
					$c_where = $where;
					$c_where['h.is_checked'] = 1;		// 审核通过
					$c_where['h.due_time'] = array('BETWEEN', array($start, $end));

					$f_where = $c_where;
					$f_where['h.renew_contract_id'] = 0;	// 非续约
					$f_where['COUNT(h.customer_id)'] = 1;
					$first_c = (int) $m_customer
						->join('LEFT JOIN ' . C('DB_PREFIX') .'contract h ON k.customer_id=h.customer_id')
						->where($f_where)
						->group('k.customer_id')
						->count();
					$first .= "{y:" . number_format(($first_c / $count * 100), 2, '.', '') . ",color:'#4472C4'},";

					$r_where = $c_where;
					$r_where['h.renew_contract_id'] = array('neq', 0);		// 续约
					$renew_c = (int) $m_customer
						->join('LEFT JOIN ' . C('DB_PREFIX') .'contract h ON k.customer_id=h.customer_id')
						->where($r_where)
						->group('k.customer_id')
						->count();
					$renew .= "{y:" . number_format(($renew_c / $count * 100), 2, '.', '') . ",color:'#FF964A'},";

					unset($c_where['h.due_time']);
					$l_where = $c_where;
					$l_where['h.end_date'] = array('BETWEEN', array($start, $end));		// 到期未续约
					$end_contract_id = $m_customer
						->join('LEFT JOIN ' . C('DB_PREFIX') .'contract h ON k.customer_id=h.customer_id')
						->where($l_where)
						->getField('contract_id', true);
					$renew_contract_id = (array) $m_contract
						->where(array('renew_contract_id' => array('in', $end_contract_id)))
						->getField('contract_id', true);
					$contract_id = array_diff($end_contract_id, $renew_contract_id);
					$loss_c = (int) $m_customer
						->join('LEFT JOIN ' . C('DB_PREFIX') .'contract h ON k.customer_id=h.customer_id')
						->where(array('h.contract_id' => array('IN', $contract_id)))
						->group('k.customer_id')
						->count();
					$loss .= "{y:" . number_format(($loss_c / $count * 100), 2, '.', '') . ",color:'#B58AF7'},";
				}
			}
			$new_add = trim($new_add, ',') . ']}';
			$first = trim($first, ',') . ']}';
			$renew = trim($renew, ',') . ']}';
			$loss = trim($loss, ',') . ']}';

			$this->data = $content_id == 1 ? $new_add : $first . ',' . $renew . ',' . $loss;
			$month_list = array_map(function ($val) {
				return '"' . str_pad($val, 2, 0, STR_PAD_LEFT) . '月"';
			}, $month_list);
			$this->unit = $content_id == 1 ? '位' : '%';
			$this->month_list = implode(',', $month_list);
		} elseif ($content_id == 3) {
			$final = (int) $_GET['final'];
			$month = (int) $_GET['month'] ?: '';
			if ($year && $month) {
				$start = strtotime(date($year . '-' . $month . '-01 00:00:00'));
				$end = strtotime(date('Y-m-d H:i:s', strtotime('+1 month', $start)));
			} elseif ($year) {
				$start = strtotime(date($year . '-01-01 00:00:00'));
				$end = strtotime(date('Y-m-d H:i:s', strtotime('+1 year', $start)));
			} elseif ($month) {
				$start = strtotime(date(date('Y-') . $month . '-01 00:00:00'));
				$end = strtotime(date('Y-m-d H:i:s', strtotime('+1 month', $start)));
			}
			if ($start && $end) {
				$where['k.create_time'] = array('BETWEEN', array($start, $end));
			}

			if ($final) {
				$where['_string'] = 'h.contract_id IS NOT NULL';
				$data = $m_customer
					->join('LEFT JOIN ' . C('DB_PREFIX') .'contract h ON k.customer_id=h.customer_id')
					->where($where)
					->group('k.customer_id')
					->getField('SUBSTR(k.address FROM 1 FOR 2)', true);
				$list = array();
				foreach ($data as $val) {
					$count = $list[$val]['count'] ?: 0;
					$count++;
					$list[$val] = array(
						'count' => $count,
						'addr' => $val
					);
				}
				$this->title = '成交' . $this->title;
			} else {
				$list = $m_customer
					->field('count(k.customer_id) as count, SUBSTR(k.address FROM 1 FOR 2) as addr')
					->where($where)
					->group('addr')
					->select();
			}
			$list = y_array_column($list, 'count', 'addr');
			$js_data = array();
			foreach (mapProvince() as $key => $val) {
				$count = $list[$val] !== null ? $list[$val] : 0;
				$js_data[] = array(
					'hc-key' => $key,
					'value' => $count
				);
			}
			$this->final = $final;
			$this->month = $month;
		}
		$js_data = array_map(function ($val) {
			$val['value'] = (int) $val['value'];
			return $val;
		}, $js_data);
		$this->js_data = json_encode($js_data) ?: '';
		$this->content_id = $content_id;
		$this->year = $year;
		$this->roleList = getUserByRoleIdArray($this->below_ids);
		$this->departmentList = getDepartmentByUrl('customer', 'analytics');
        $action = get_action_name();
        $this->display($action);
	}

    public function test_request(){
        header("Content-type: text/html; charset=utf-8");

        header('Access-Control-Allow-Origin: https://api.im.jpush.cn');
        // 响应类型
        header('Access-Control-Allow-Methods:POST,GET');
        // 带 cookie 的跨域访问
        header('Access-Control-Allow-Credentials: true');
        // 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,Content-Type,X-CSRF-Token');
        date_default_timezone_set('PRC'); //设置默认时区为北京时间
        //短信接口用户名 $uid
        $uid = 'dev1';
        //短信接口密码 $passwd
        $passwd = 'password';

        // $num ='15993783128';

         $message='接口测试';

        $msg = rawurlencode(mb_convert_encoding($message, "gb2312", "utf-8"));

        $gateway = "https://api.im.jpush.cn/v1/users/";

        $result = file_get_contents($gateway);

        if(  $result > 0 )
        {
             echo $gateway;
              echo $result;
            echo "发送成功! 发送时间".date("Y-m-d H:i:s");
        }
        else
        {
             echo $gateway;
             echo '<br>';
            echo "发送失败, 错误提示代码: ".$result;
        }
        //exit;
    }
    //客户沟通日志
    public function log_add(){
	    $id = I('request.id');
	    $customer_id = I('request.customer_id');
	    $this->assign('id',$id);
	    $this->assign('customer_id',$customer_id);
	    //跟进类型
        $status_list = M('log_status')->select();
        $this->assign('status_list',$status_list);

        //联系人列表
        $contacts_ids = M('RContactsCustomer')->where('customer_id = %d',$customer_id)->getField('contacts_id',true);
        $contacts_ids = $contacts_ids ? : -1;
        $contacts_list = M('contacts')->where(array('contacts_id'=>array('in',$contacts_ids),'is_deleted'=>'0'))->select();
        $this->contacts_list = $contacts_list;
	    $action = get_action_name();
	    $this->display($action);
    }

    //客户沟通日志保存



}
