<?php
/**
 *合同相关
 **/
class ContractVue extends Action {
	/**
	 *用于判断权限
	 *@permission 无限制
	 *@allow 登录用户可访问
	 *@other 其他根据系统设置
	 **/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('product','finance','dynamic','checklist','check','revokecheck','examinestatus','checkper')
		);
		B('VueAuthenticate',$action);
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
	 * 合同列表
	 * @param 
	 * @author 
	 * @return 
	 */
	public function index() {
		$contract_custom = M('Config') -> where('name="contract_custom"')->getField('value');
		if (!$contract_custom) {
			$contract_custom = 'pd_crm';
		} 
		if ($this->isPost()) {
			//获取权限
			$permission_list = apppermission('contract','index');
			if ($permission_list) {
				$data['permission_list'] = $permission_list;
			} else {
				$data['permission_list'] = array();
			}
			$m_user = M('User');
			$d_contract = D('ContractView');
			$by = $_POST['by'] ? trim($_POST['by']) : 'me';
			$where = array();
			//按合同编号查询
			if (trim($_POST['search'])) {
				$search = trim($_POST['search']);
				//获取客户ID
				$cus_where['name'] = array('like','%'.$search.'%');
				$customer_ids = M('Customer')->where($cus_where)->getField('customer_id',true);
				$customer_str = implode(',',$customer_ids);
				//获取商机ID
				$b_where['name'] = array('like','%'.$search.'%');
				$business_ids = M('business')->where($b_where)->getField('business_id',true);
				$business_str = implode(',',$business_ids);
				if($customer_str && $business_str){
					$where['_string'] = 'contract.contract_name like "%'.$search.'%" or contract.number like "%'.$search.'%" or contract.customer_id in ('.$customer_str.') or contract.business_id in ('.$business_str.')';
				}elseif($customer_str){
					$where['_string'] = 'contract.contract_name like "%'.$search.'%" or contract.number like "%'.$search.'%" or contract.customer_id in ('.$customer_str.')';
				}elseif($business_str){
					$where['_string'] = 'contract.contract_name like "%'.$search.'%" or contract.number like "%'.$search.'%" or contract.business_id in ('.$business_str.')';
				}else{
					$where['_string'] = 'contract.contract_name like "%'.$search.'%" or contract.number like "%'.$search.'%"';
				}
			}
			$below_ids = getPerByAction('contract','index',true);
			$sub_ids = getSubRoleId(false);
			$where['contract.owner_role_id'] = array('in', $this->_permissionRes);
			$order = 'contract.update_time desc,contract.contract_id asc';

			//排序
			if ($_POST['order_field'] && $_POST['order_type']) {
				$order = 'contract.'.trim($_POST['order_field']).' '.trim($_POST['order_type']).',contract.contract_id asc';
			}

			//查询条件
			switch ($by){
				case 'create':
					$where['creator_role_id'] = session('role_id');
					break;
				case 'sub' :
					$where['contract.owner_role_id'] = array('in',implode(',', $sub_ids));
					break;
				case 'subcreate' :
					$where['creator_role_id'] = array('in',implode(',', $below_ids));
					break;
				case 'today' :
					$where['due_time'] =  array('between',array(strtotime(date('Y-m-d')) -1 ,strtotime(date('Y-m-d')) + 86400));
					break;
				case 'week' :
					$week = (date('w') == 0)?7:date('w');
					$where['due_time'] =  array('between',array(strtotime(date('Y-m-d')) - ($week-1) * 86400 -1 ,strtotime(date('Y-m-d')) + (8-$week) * 86400));
					break;
				case 'month' :
					$next_year = date('Y')+1;
					$next_month = date('m')+1;
					$month_time = date('m') ==12 ? strtotime($next_year.'-01-01') : strtotime(date('Y').'-'.$next_month.'-01');
					$where['due_time'] = array('between',array(strtotime(date('Y-m-01')) -1 ,$month_time));
					break;
				case 'add' :
					$order = 'contract.create_time desc,contract.contract_id asc';
					break;
				case 'deleted' :
					$where['is_deleted'] = 1;
					break;
				case 'update' :
					$order = 'contract.update_time desc,contract.contract_id asc';
					break;
				case 'me' :
					$where['contract.owner_role_id'] = session('role_id');
					break;
				case 'dqcontact' :
					$days = C('defaultinfo.contract_alert_time') ? intval(C('defaultinfo.contract_alert_time')) : 30;
					$temp_time = time()+$days*86400;
					$where['contract.is_checked'] = 1;
					$where['contract.contract_status'] = 0;
					$where['contract.owner_role_id'] = session('role_id');
					$where['end_date'] = array('elt',$temp_time);
					break;
				default: $where['contract.owner_role_id'] = array('in',getPerByAction(MODULE_NAME,ACTION_NAME));break;
			}
			//多选类型字段
			$check_field_arr = M('Fields')->where(array('model'=>'contract','form_type'=>'box','setting'=>array('like','%'."'type'=>'checkbox'".'%')))->getField('field',true);
			//高级搜索
			if(!$_POST['field']){
				$no_field_array = array('act','content','p','search','listrows','by','contract_checked','order_field','order_type','token');
				foreach($_POST as $k => $v){
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
							}elseif(in_array($k,array('is_checked'))){
								if(!empty($v)){
									$where[$k] = $v['value'];
								}
							}elseif (($v['start'] != '' || $v['end'] != '')){
								if($k == 'create_time'){
									$k = 'contract.create_time';
								}elseif($k == 'update_time'){
									$k = 'contract.update_time';
								}
								//时间段查询
								if ($v['start'] && $v['end']) {
									$where[$k] = array('between',array(strtotime($v['start']),strtotime($v['end'])+86399));
								} elseif ($v['start']) {
									$where[$k] = array('egt',strtotime($v['start']));
								} else {
									$where[$k] = array('elt',strtotime($v['end'])+86399);
								}
							}elseif($k =='customer_name'){
								if(!empty($v['value'])){
									$c_where['name'] = array('like','%'.$v['value'].'%');
									$customer_ids = M('Customer')->where($c_where)->getField('customer_id',true); 
									if($customer_ids){
										$where['customer_id'] = array('in',$customer_ids);
									}else{
										$where['customer_id'] = -1;
									}
								}
							}elseif($k =='code'){
								if(!empty($v['value'])){
									$b_where['code'] = array('like','%'.$v['value'].'%');
									$business_ids = M('Business')->where($b_where)->getField('business_id',true); 
									if($business_ids){
										$where['business_id'] = array('in',$business_ids);
									}else{
										$where['business_id'] = -1;
									}
								}
							}elseif($k =='owner_role_id' || $k =='creator_role_id'){
								if(!empty($v)){
									$where['contract.'.$k] = $v['value'];
								}
							} elseif(($v['value']) != '') {
								if (in_array($k,$check_field_arr)) {
									$where[$k] = field($v['value'],'contains');
								} else {
									if($k == 'status_id'){
										$business_map['status_id'] = $v['value'];
									}else{
										$where[$k] = field($v['value'],$v['condition']);
									}
								}
							}
						} else {
							if(!empty($v)){
								$where[$k] = field($v);
							}
						}
	                }
	            }
	            //过滤不在权限范围内的role_id
				if(isset($where['contract.owner_role_id'])){
					if(!empty($where['contract.owner_role_id']) && $where['owner_role_id']['1'] && !in_array(intval($where['contract.owner_role_id']),$this->_permissionRes)){
						$where['contract.owner_role_id'] = array('in',implode(',', $this->_permissionRes));
					}
				}
			}
			//按分类 1销售 2采购
			$where['contract.type'] = 1;

			//待审核的合同(未审核、审核中)
			if ($_POST['contract_checked'] == 1) {
				$where['is_checked'] = array('in',array('0','3'));
			}

			if (!isset($where['is_deleted'])) {
				$where['is_deleted'] = 0;
			}
			//商机下的合同
			if ($_POST['business_id']) {
				$contract_ids = M('rBusinessContract')->where('business_id = %d', $_POST['business_id'])->getField('contract_id', true);
				$where['contract.contract_id'] = array('in', $contract_ids);
			}
			//客户下的合同
			if ($_POST['customer_id']) {
				$where['contract.customer_id'] = intval($_POST['customer_id']);
			}
			$p = isset($_POST['p']) ? intval($_POST['p']) : 1 ;
			$list = $d_contract->where($where)->page($p.',10')->order($order)->field('number,price,customer_id,contract_id,type,owner_role_id,create_time,is_checked')->select();

			$m_customer = M('Customer');
			$m_receivables = M('Receivables');
			$d_receivingorder = D('ReceivingorderView');
			$m_invoice = M('Invoice');
			foreach ($list as $k=>$v) {
				if ($v['type'] == 1) {
					$customer_name = $m_customer->where('customer_id = %d',$v['customer_id'])->getField('name');
					if ($customer_name) {
						$list[$k]['customer_name'] = $customer_name;
					} else {
						$list[$k]['customer_name'] = '';
					}
				}
				$owner_role_id = $v['owner_role_id'];
				
				//合同到期时间
				$end_date = 0;
				$end_date = $d_contract->where('contract_id = %d', $v['contract_id'])->getField('end_date');
				if ($end_date) {
					$list[$k]['days'] = round(($end_date-time())/86400);
				} else {
					$list[$k]['days'] = '';
				}

				//应收款
				$receivables_info = $m_receivables->where('is_deleted <> 1 and contract_id = %d',$v['contract_id'])->find();
				$sum_money = $d_receivingorder->where('receivingorder.is_deleted <> 1 and receivingorder.receivables_id = %d and receivingorder.status = 1', $receivables_info['receivables_id'])->sum('money');
				//收款进度
				$schedule = 0;
				if ($sum_money) {
					if ($receivables_info['price'] == 0 || $receivables_info['price'] == '') {
						$schedule = 100;
					} else {
						$schedule = round(($sum_money/$receivables_info['price'])*100,0);
					}
				}
				$list[$k]['schedule'] = $schedule ? $schedule : 0;

				//获取操作权限
				$list[$k]['permission'] = permissionlist(MODULE_NAME,$owner_role_id);
				//发票（剩余未开票金额）
				$invoice_price = '0.00';
				$sub_invoice_price = '0.00';
				$invoice_price = $m_invoice->where(array('contract_id'=>$v['contract_id'],'is_checked'=>array('neq',2)))->sum('price');
				$sub_invoice_price = round(($v['price']-$invoice_price),2);
				$list[$k]['sub_invoice_price'] = ($sub_invoice_price > 0) ? $sub_invoice_price : '0.00';
			}
			$count = $d_contract->where($where)->count();

			//自定义场景
			if($p == 1 && $_POST['search'] == ''){
				$m_scene = M('Scene');
				$scene_where = array();
				$scene_where['role_id']  = session('role_id');
				$scene_where['type']  = 1;
				$scene_where['_logic'] = 'or';
				$map_scene['_complex'] = $scene_where;
				$map_scene['module'] = 'contract';
				$map_scene['is_hide'] = 0;
				$scene_list = $m_scene->where($map_scene)->order('order_id asc,id asc')->field('id,name,data,type,by')->select();
				foreach ($scene_list as $k=>$v) {
					if ($v['type'] == 0) {
						eval('$scene_data = '.$v["data"].';');
					} else {
						$scene_data = array();
					}
					if ($scene_id && $scene_id == $v['id']) {
						$fields_search = $scene_data;
					}
					$scene_list[$k]['cut_name'] = cutString($v['name'],8);
				}
			}

			//获取查询条件信息
			if($p == 1 && $_POST['search'] == ''){
				$where_field = array();
				$where_field['model'] = array('in',array('','contract'));
				$where_field['is_main'] = '1';
				$where_field['field'] = array('not in',array('customer_id','business_id','delete_role_id','is_deleted','delete_time'));
				$fields_list = M('Fields')->where($where_field)->field('name,field,setting,form_type,input_tips')->order('order_id')->select();
				foreach($fields_list as $k=>$v){
					if ($v['setting']) {
						//将内容为数组的字符串格式转换为数组格式
						eval("\$setting = ".$v['setting'].'; ');
						$setting_arr = array();
						$data_arr = array();
						foreach ($setting['data'] as $key=>$val) {
							$key = $key-1;
							$data_arr[$key]['key'] = $val;
							$data_arr[$key]['value'] = $val;
						}
						$fields_list[$k]['setting'] = $data_arr;
						$fields_list[$k]['form_type'] = $setting['type'] == 'checkbox' ? 'checkbox' : 'select';
					} elseif ($v['field'] == 'owner_role_id' || $v['field'] == 'create_role_id') {
						$fields_list[$k]['form_type'] = 'user';
					} else {
						$fields_list[$k]['setting'] = '';
					}
				}
				//追加其他字段信息
				$contract_field_list = array(
					'0'=>array('field'=>'customer_name','form_type'=>'text','input_tips'=>'','name'=>'客户名称','setting'=>''),
					'1'=>array('field'=>'code','form_type'=>'text','input_tips'=>'','name'=>'商机编号','setting'=>'')
				);
				$fields_list = array_merge($fields_list,$contract_field_list);
			}

			$page = ceil($count/10);
			if ($p == 1 && $_POST['search'] == '') {
				$data['contract_custom'] = $contract_custom;
				$data['fields_list'] = $fields_list ? $fields_list : array();
				//场景信息
				$data['scene_list'] = $scene_list ? $scene_list : array();
			} else {
				$data['fields_list'] = array();
				$data['scene_list'] = array();
			}
			$data['list'] = empty($list) ? array() : $list;
			$data['page'] = $page;
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		} else {
			$this->ajaxReturn('','非法请求！',0);
		}
	}

	/**
	 * 合同动态
	 * @param 
	 * @author 
	 * @return 
	 */
	public function dynamic(){
		if ($this->isPost()) {
			$m_contract = M('Contract');
			$m_user = M('User');
			$contract_id = $_POST['id'] ? intval($_POST['id']) : 0;
			$contract_info = $m_contract->where(array('contract_id'=>$contract_id))->field('contract_id,contract_name,create_time,customer_id,business_id,is_checked,owner_role_id,order_id,examine_role_id')->find();
			if (!$contract_info) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			}
			//判断权限
			if (!in_array($contract_info['owner_role_id'], getPerByAction('contract','view'))) {
				$this->ajaxReturn('','您没有此权限！',-2);
			}
			$customer_name = M('Customer')->where(array('customer_id'=>$contract_info['customer_id']))->getField('name');
			$business_name = M('Business')->where(array('business_id'=>$contract_info['business_id']))->getField('name');
			$contract_info['customer_name'] = $customer_name ? $customer_name : '';
			$contract_info['business_name'] = $business_name ? $business_name : '';
			switch ($contract_info['is_checked']) {
				case '0' : $check_name = '待审'; break;
				case '1' : $check_name = '通过'; break;
				case '2' : $check_name = '驳回'; break;
				case '3' : $check_name = '审批中'; break;
			}
			$contract_info['check_name'] = $check_name;

			// 审批权限
			if ($this->checkPer($contract_id)) {
				$add_examine = 1;
			}
			$contract_info['add_examine'] = $add_examine ? : 0;

			//撤销审核权限（管理员）
			if (session('?admin')) {
				$revokecheck = 1;
			}
			$contract_info['revokecheck'] = $revokecheck ? : 0;

			$data['data'] = $contract_info ? $contract_info : array();
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		}
	}

	/**
	 * 合同详情
	 * @param 
	 * @author 
	 * @return 
	 */
	public function view(){
		if ($this->isPost()) {
			$contract_id = intval($_POST['id']);
			$d_contract = D('ContractView');
			$m_user = M('User');
			$m_contacts = M('Contacts');
			$m_customer = M('Customer');
			$m_business = M('Business');
			$m_product = M('Product');
			$contract_info = $d_contract->where('contract.contract_id = %d',$contract_id)->find();
			//权限判断
			if (empty($contract_info) || empty($contract_id)) {
				$this->ajaxReturn('','合同不存在或已被删除！',0);
			}
			if (!in_array($contract_info['owner_role_id'], $this->_permissionRes)) {
				$this->ajaxReturn('','您没有此权利！',-2);
			}
			//自定义字段
			$where_field = array();
			$where_field['field'] = array('not in',array('business_id','customer_id','number','contract_name','owner_role_id','price','due_time','start_date','end_date','description'));
			$where_field['model'] = 'contract';
			$fields_list = M('Fields')->where($where_field)->order('is_main desc,order_id asc')->field('is_main,field,name,form_type,default_value,max_length,is_unique,is_null,is_validate,in_add,input_tips,setting')->select();
			foreach ($fields_list as $k=>$v) {
				if ($v['setting']) {
					//将内容为数组的字符串格式转换为数组格式
					eval("\$setting = ".$v['setting'].'; ');
					$setting_arr = array();
					$data_arr = array();
					foreach ($setting['data'] as $key=>$val) {
						$key = $key-1;
						$data_arr[$key]['key'] = $val;
						$data_arr[$key]['value'] = $val;
					}
					$fields_list[$k]['setting'] = $data_arr;
					$fields_list[$k]['form_type'] = $setting['type'] == 'checkbox' ? 'checkbox' : 'select';
				} else {
					$fields_list[$k]['form_type'] = $v['form_type'];
				}
				$data_a = trim($contract_info[$v['field']]);
				if($v['form_type'] == 'address') {
					$address_array = str_replace(chr(10),' ',$data_a);
					$fields_list[$k]['val'] = $address_array;
					$fields_list[$k]['type'] = 0;
				} else {
					$fields_list[$k]['val'] = $data_a;
					$fields_list[$k]['type'] = 0;
				}
				$fields_list[$k]['id'] = '';
			}

			//审批人信息
			//合同审批人类型
			if (!$contract_info['examine_type_id']) {
				$contract_examine = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
			}
			$contract_examine = $contract_examine ? $contract_examine : '0';

			// if ($contract_examine) {
			// 	$examine_setting = array();
			// 	//查询审批人信息
			// 	$examine_step_info = M('ContractExamine')->order('order_id asc')->find();
			// 	$examine_role_ids = $examine_step_info['role_id'];
			// 	$examine_role_list = array();
			// 	if ($examine_role_ids) {
			// 		$examine_role_list = M('User')->where(array('role_id'=>array('in',explode(',',$examine_role_ids))))->field('full_name,role_id,thumb_path')->select();
			// 	}
			// 	$examine_form_type = 'user_list';
			// 	if ($examine_step_info['relation'] == 1) {
			// 		$examine_relation = '并';
			// 	} elseif ($examine_step_info['relation'] == 2) {
			// 		$examine_relation = '或';
			// 	}
			// 	$examine_setting = array('examine_role_list'=>$examine_role_list,'examine_relation'=>$examine_relation);
			// } else {
			// 	//自选
			// 	$examine_role_ids = array();
			// 	$examine_role_ids = $contract_info['examine_role_id'];
			// 	$examine_role_list = array();
			// 	if (count($examine_role_ids) == 1) {
			// 		$examine_role_list = M('User')->where(array('role_id'=>array('in',explode(',',$examine_role_ids))))->field('full_name,role_id,thumb_path')->select();
			// 	}

			// 	$examine_relation = '';
			// 	$examine_form_type = 'user_select';
			// 	$examine_setting = array('examine_role_list'=>$examine_role_list,'examine_relation'=>$examine_relation);
			// }

			$examine_role_id_count = count(array_filter(explode(',', $contract_info['examine_role_id'])));
			if (!$contract_examine && $examine_role_id_count <= 1) {
				$examine_form_type = 'user_select';
				$examine_setting = array();
				$examine_role_id = array_filter(explode(',', $contract_info['examine_role_id']));
			}

			//自选流程（固定流程暂时隐藏不展示）
			//模拟自定义字段返回数据
			$field_array = array(
				'0'=>array('field'=>'number','name'=>'合同编号','form_type'=>'text'),
				'1'=>array('field'=>'start_date','name'=>'签约时间','form_type'=>'datetime'),
				'2'=>array('field'=>'customer_id','name'=>'签约客户','form_type'=>'text'),
				'3'=>array('field'=>'business_id','name'=>'相关商机','form_type'=>'text'),
				'4'=>array('field'=>'contract_name','name'=>'合同名称','form_type'=>'text'),
				'5'=>array('field'=>'owner_role_id','name'=>'合同签约人','form_type'=>'text'),
				'6'=>array('field'=>'examine_role_id','name'=>'合同审批人','form_type'=>$examine_form_type,'setting'=>$examine_setting),
				'7'=>array('field'=>'price','name'=>'合同金额','form_type'=>'text'),
				'8'=>array('field'=>'due_time','name'=>'签约时间','form_type'=>'datetime'),
				'9'=>array('field'=>'start_date','name'=>'生效时间','form_type'=>'datetime'),
				'10'=>array('field'=>'end_date','name'=>'到期时间','form_type'=>'datetime'),
				'11'=>array('field'=>'creator_role_id','name'=>'合同创建人','form_type'=>'text'),
				'12'=>array('field'=>'create_time','name'=>'创建时间','form_type'=>'datetime'),
				'13'=>array('field'=>'description','name'=>'合同备注','form_type'=>'textarea')
			);
			$contract_list = array();
			$i = 0;
			foreach ($field_array as $k=>$v) {
				if ($contract_examine && $v['field'] == 'examine_role_id' && $examine_role_id_count <= 1) {
					continue;
				}
				$contract_list[$i]['field'] = $v['field'];
				$contract_list[$i]['name'] = $v['name'];
				$contract_list[$i]['form_type'] = $v['form_type'];
				switch ($v['field']) {
					case 'customer_id' :
						$customer_name = $m_customer->where(array('customer_id'=>$contract_info[$v['field']]))->getField('name');
						$id = $contract_info[$v['field']];
						$val = $customer_name ? $customer_name : '';
						$type = 3;
						break;
					case 'business_id' :
						$business_name = $m_business->where(array('business_id'=>$contract_info[$v['field']]))->getField('name');
						$id = $contract_info[$v['field']];
						$val = $business_name ? $business_name : '';
						$type = 4;
						break;
					case 'examine_role_id' : 
						$examine_role_info = $m_user->where('role_id = %d',$examine_role_id)->field('full_name,role_id')->find();
						$id = $examine_role_info['role_id'];
						$val = $examine_role_info['full_name'];
						$type = 1;
						break;
					case 'owner_role_id' : 
					case 'creator_role_id' : 
						$user_info = $m_user->where('role_id = %d',$contract_info[$v['field']])->field('full_name,role_id')->find();
						$id = $user_info['role_id'];
						$val = $user_info['full_name'];
						$type = 1;
						break;
					default :
						$id = '';
						$val = $contract_info[$v['field']];
						$type = 0;
						break;
				}
				$contract_list[$i]['id'] = $id;
				$contract_list[$i]['val'] = $val;
				$contract_list[$i]['type'] = $type;
				$i++;
			}
			//合并自定义字段
			if ($fields_list) {
				$fields_list = array_merge($contract_list,$fields_list);
			} else {
				$fields_list = $contract_list;
			}

			//相关产品
			$sales_id = M('rContractSales')->where(array('contract_id'=>$contract_id,'sales_type'=>0))->getField('sales_id');
			$sales_info = M('Sales')->where(array('sales_id'=>$sales_id))->field('prime_price,final_discount_rate,sales_price,total_amount')->find();
			$product_list = M('SalesProduct')->where(array('sales_id'=>$sales_id))->field('sales_product_id,product_id,amount,ori_price,unit_price,unit,cost_price,discount_rate,subtotal')->select();
			foreach ($product_list as $k=>$v) {
				$product_name = $m_product->where(array('product_id'=>$v['product_id']))->getField('name');
				$product_list[$k]['product_name'] = $product_name ? : '';
			}
			$product_info = array();
			$product_info['total_subtotal_val'] = $sales_info['prime_price'];
			$product_info['final_discount_rate'] = $sales_info['final_discount_rate'];
			$product_info['final_price'] = $sales_info['sales_price'];
			$product_info['total_amount'] = $sales_info['total_amount'];
			$product_info['product_list'] = $product_list ? $product_list : array();

			$m_contract_examine = M('ContractExamine');
			$m_contract_check = M('ContractCheck');

			//申请人
			$d_user = D('User');
			$temp_val = $d_user->get_full_name(array($contract_info['owner_role_id']));
			$contract_info['owner_role_info'] = $temp_val[$contract_info['owner_role_id']];

			if ($contract_examine == 1) {
				//审批人
				$examine_arr = array();
				$examine_role_id = $m_contract_examine->where(array('order_id'=>$contract_info['order_id']))->getField('role_id');
			} else {
				//审批人
				$examine_arr = array();
				$examine_role_id = $contract_info['examine_role_id'];
			}
			$examine_arr = $d_user->get_full_name(explode(',',$examine_role_id));

			//获取权限
			$data['permission'] = permissionlist(MODULE_NAME,$contract_info['owner_role_id']);
			$data['data'] = $fields_list ? : array();
			$data['product_info'] = $product_info ? : array();
			$data['contract_info'] = $contract_info ? : array();
			
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		}
	}

	/**
	 * 合同删除
	 * @param 
	 * @author 
	 * @return 
	 */
	public function delete(){
		if ($this->isPost()) {
			$contract_id = $_POST['id'] ? intval($_POST['id']) : 0;
			if (!$contract_id) {
				$this->ajaxReturn('','参数错误！',0);
			} else {
				$m_contract = M('Contract');
				$m_receivables = M('Receivables');
				$m_payables = M('Payables');
				$m_r_contract_product = M('rContractProduct');
				$m_r_contract_file = M('rContractFile');
				//权限判断
				$contracts = $m_contract->where('contract_id = %d', $contract_id)->find();
				if (!in_array($contracts['owner_role_id'], $this->_permissionRes)){
					$this->ajaxReturn('您没有此权利!','您没有此权利!',-2);
				}
				//如果合同下有产品，财务和文件信息，提示先删除产品，财务和文件数据。
				// $data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
				$contract = $m_contract->where('contract_id = %d',$contract_id)->find();
				$contract_product = $m_r_contract_product->where('contract_id = %d',$contract_id)->select();//合同关联的产品记录
				$contract_file = $m_r_contract_file->where('contract_id = %d',$contract_id)->select();//合同关联的文件
				$contract_receivables = $m_receivables->where('is_deleted <> 1 and contract_id = %d',$contract_id)->select();//合同关联的应收款
				$contract_payables = $m_payables->where('is_deleted <> 1 and contract_id = %d',$contract_id)->select();//合同关联的应付款

				if(empty($contract_product) && empty($contract_file) && empty($contract_receivables) && empty($contract_payables)){
					if(!$m_contract->where('contract_id = %d', $contract_id)->delete()){
						$this->ajaxReturn('删除失败，请联系管理员！','删除失败，请联系管理员！',2);
					} else {
						//附表删除
						M('ContractData')->where(array('contract_id'=>$contract_id))->delete();
						//关联日程
						$event_res = M('Event')->where(array('module'=>'contract','module_id'=>$contract_id))->delete();
					}
				}else{
					if(!empty($contract_product)){
						$this->ajaxReturn('删除失败！请先删除'.$contract['number'].'合同下的产品信息!','删除失败！请先删除'.$contract['number'].'合同下的产品信息!',2);
					}elseif(!empty($contract_file)){
						$this->ajaxReturn('删除失败！请先删除'.$contract['number'].'合同下的文件信息!','删除失败！请先删除'.$contract['number'].'合同下的文件信息!',2);
					}elseif(!empty($contract_receivables)){
						$this->ajaxReturn('删除失败！请先删除'.$contract['number'].'合同中财务下的应收款信息!','删除失败！请先删除'.$contract['number'].'合同中财务下的应收款信息!',2);
					}else{
						$this->ajaxReturn('删除失败！请先删除'.$contract['number'].'合同中财务下的应收款信息!','删除失败！请先删除'.$contract['number'].'合同中财务下的应收款信息!',2);
					}
				}
				$this->ajaxReturn('删除成功','删除成功',1);
			}
		}
	}

	/**
	 * 合同审核历史
	 * @param 
	 * @author 
	 * @return 
	 */
	public function checkList() {
		if ($this->isPost()) {
			$m_contract_check = M('contract_check');
			$m_user = M('user');
			$d_user = D('User');

			$contract_id = $_POST['id'] ? intval($_POST['id']) : 0;
			if ($contract_id) {
				$check_list = $m_contract_check ->where('contract_id =%d',$contract_id)->order('check_id asc')->select();
				foreach($check_list as $kk=>$vv){
					$temp_val = $d_user->get_full_name(array($vv['role_id']));
					$check_list[$kk]['user'] = $temp_val[$vv['role_id']];
					if (!$vv['is_checked']) {
						$check_list[$kk]['is_checked'] = '4';
					}
					$check_list[$kk]['content'] = $vv['content'] ? : '';
				}

				$m_contract_examine = M('ContractExamine');
				$m_contract_check = M('ContractCheck');

				$contract_info = M('Contract')->where(array('contract_id'=>$contract_id))->field('contract_id,examine_type_id,order_id')->find();
				if ($contract_info['is_checked']) {
					$contract_examine = $contract_info['examine_type_id'];
				} else {
					$contract_examine = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
				}
				//审批流
				$step_list = array();
				if ($contract_examine == 1) {
					//审批流
					$step_list = $m_contract_examine->order('order_id')->select();
					$d_user = D('User');
					foreach ($step_list as $k=>$v) {
						$role_ids = explode(',',$v['role_id']);
						$role_list = $d_user->get_full_name($role_ids);
						if ($contract_info['order_id'] >= $v['order_id']) {
							$check_role_arr = array();
							//审批意见
							$check_role_arr = $m_contract_check->where(array('contract_id'=>$contract_info['contract_id'],'order_id'=>$v['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);
							$is_checked = '';
							$is_checked_name = '';
							if ($contract_info['order_id'] == $v['order_id']) {
								$is_checked = 1;
								$is_checked_name = '待审';
							}
							foreach ($role_list as $key=>$val) {
								if ($check_role_arr && in_array($val['role_id'],$check_role_arr)) {
									$role_list[$key]['is_checked'] = 2;
									$role_list[$key]['is_checked_name'] = '同意';
								} else {
									$role_list[$key]['is_checked_name'] = $is_checked_name;
									$role_list[$key]['is_checked'] = $is_checked;
								}
							}
						}
						$step_list[$k]['role_list'] = $role_list;
						if (count($role_ids) > 1) {
							if ($v['relation'] == 1) {
								$step_list[$k]['relation_name'] = '并';
							} elseif ($v['relation'] == 2) {
								$step_list[$k]['relation_name'] = '或';
							}
						}
					}
				}
				$data['step_list'] = $step_list ? : array();

				$data['list'] = $check_list ? $check_list : array();
				$data['info'] = 'success'; 
				$data['status'] = 1; 			
				$this->ajaxReturn($data,'JSON');
			}
		}
	}

	/**
	 * 合同审核
	 * @param 
	 * @author 
	 * @return 
	 */
	public function check(){
		if ($this->isPost()) {
			$m_contract = M('Contract');
			$contract_id = $_POST['id'] ? intval($_POST['id']) : '';
			if (!$contract_id) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$contract = $m_contract->where('contract_id = %d', $contract_id)->find();
			if (!$contract) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			}
			if ($contract['examine_type_id']) {
				$option = $contract['examine_type_id'];
			} else {
				//contract_examine 1为自定义审批流
				$option = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
			}
			if (in_array($contract['is_checked'],array('1','2'))) {
				$this->ajaxReturn('','该合同已审核结束，请勿重复操作！',0);
			}
			$m_contract_examine = M('ContractExamine');
			$m_contract_check = M('ContractCheck');

			// 审批权限
			if (!$this->checkPer($contract_id)) {
				$this->ajaxReturn('','您没有审核权限或审批已通过！',0);
			}
			$m_user = M('User');

			$is_agree = intval($_POST['is_agree']);
			$is_receivables = intval($_POST['is_receivables']);
			$description = trim($_POST['description']);
			$m_r_contract_sales = M('rContractSales');
			$m_sales = M('Sales');
			//默认（是否生成应收款）
			M('User')->where('role_id =%d',session('role_id'))->setField('is_receivables',$is_receivables);
			$r_contract_sales_info = $m_r_contract_sales->where(array('contract_id'=>$contract_id,'sales_type'=>array('neq',1)))->find();
			$sales_id = $m_r_contract_sales->where('contract_id = %d && sales_type = 0', $contract_id)->getField('sales_id');
			$sales_status = $m_sales->where('sales_id = %d',$sales_id)->getField('status');
			if ($contract['is_checked'] != 1) {
				if ($sales_status == 97 || $sales_status == 99 || $r_contract_sales_info['sales_type'] == 2 || empty($r_contract_sales_info)) {
					$data = array();
					$data['check_des'] = $description;
					$data['update_time'] = time();
					$data['check_time'] = time();

					//审批流程
					$is_end = 0; //是否结束审批（发送站内信判断）

					if ($option == 1) {
						//当前单个审批流程是否结束
						$examine_step_info = $m_contract_examine->where(array('order_id'=>$contract['order_id']))->find();
						//已审批role_id
						$is_check_role = $m_contract_check->where(array('contract_id'=>$contract['contract_id'],'order_id'=>$contract['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);
						$is_check_role[] = session('role_id');
						$examine_is_end = 0;
						if ($examine_step_info['relation'] == 1 ) {
							$relation_name = '并';
							//并
							if ($is_check_role && !array_diff(array_filter(explode(',',$examine_step_info['role_id'])),$is_check_role)) {
								$examine_is_end = 1;
							}
						} elseif ($examine_step_info['relation'] == 2) {
							$relation_name = '或';
							//或
							// if (array_intersect(session('role_id'),$examine_step_info)) {
								$examine_is_end = 1;
							// }
						} else {
							$examine_is_end = 1;
						}
						if ($examine_is_end == 1) {
							//当前流程结束，转下一审批流程
							$next_order_id = $contract['order_id']+1; //下一审批流程排序ID
							//获取下一审批人
		                    $next_examine_step_info = $m_contract_examine->where(array('order_id'=>$next_order_id))->find();
		                    if ($next_examine_step_info['relation'] == 1) {
		                        $relation_name = '并';
		                    } elseif ($next_examine_step_info['relation'] == 2) {
		                        $relation_name = '或';
		                    }
							$next_role_id = $next_examine_step_info['role_id'];
						} else {
							//当前流程，剩余审批人
							$next_order_id = $contract['order_id'];
							$next_examine_step_info = $m_contract_examine->where(array('order_id'=>$next_order_id))->find();
		                    if ($next_examine_step_info['relation'] == 1) {
		                        $relation_name = '并';
		                    } elseif ($next_examine_step_info['relation'] == 2) {
		                        $relation_name = '或';
		                    }
							$next_role_id = $is_check_role ? array_merge(array_diff(array_filter(explode(',',$examine_step_info['role_id'])),$is_check_role)) : array_filter(explode(',',$examine_step_info['role_id']));
						}
						$data['examine_type_id'] = 1;
					} else {
						$next_role_id = trim($_POST['examine_role_id']);
						$next_order_id = 0;
					}
					if ($is_agree == 1) {
						if ($_POST['examine_status'] != 2 && $next_role_id == null && $option !== 1) {
							$this->ajaxReturn('','请选择下一审批人！',0);
						}
						if (is_array($next_role_id)) {
							$data['examine_role_id'] = ','.implode(',', $next_role_id).',';
						} else {
							$data['examine_role_id'] = $next_role_id;
						}
						
						if ($_POST['examine_status'] == 2) {
							$data['order_id'] = intval($next_order_id);
							$data['is_checked'] = 1;
							$is_end = 1;
						} elseif ($option == 1) {
							//自定义流程
							//查询审批流程排序最大值，如果order_id和最大值相等，则审批结束
							$max_order_id = $m_contract_examine->max('order_id');
							$order_id = intval($next_order_id);
							$new_order_id = $order_id-1;
							if ($new_order_id == $max_order_id) {
								$data['is_checked'] = 2;   //审批结束
								$is_end = 1;
							} else {
								$data['order_id'] = $order_id;
								$data['is_checked'] = 3;	//审批中
							}
						} else {
							$data['is_checked'] = 3;	//审批中
						}

					} elseif ($is_agree == 2) {
						//结束审批
						$is_end = 1;
						//如果是自定义流程,驳回至最初状态
						$step_role_id = '';					
						if($option == 1){
							$step_role_id = $m_contract_examine->order('order_id asc')->getField('role_id');
							//将自定义审批意见标记为无效
							$m_contract_check->where(array('contract_id'=>$contract['contract_id']))->setField('is_end',1);
						}
						$data['examine_role_id'] = $step_role_id ? : 0;
						$data['order_id'] = 0;
						$data['is_checked'] = 2;
						$data['examine_type_id'] = 0;
					} else {
						alert('error', '请求错误!', $_SERVER['HTTP_REFERER']);
					}
					$result = $m_contract->where('contract_id = %d', $contract_id)->save($data);

					//审批时给创建人发送站内信
					if (empty($is_end)) {
						if ($is_agree == 1) {
							$check_result = session('full_name').'<font style="color:green;">同意了</font>';
						} elseif ($is_agree == 1) {
							$check_result = session('full_name').'<font style="color:red;">驳回了</font>';
						}
						//发送站内信
						$url = U('contract/view','id='.$contract_id);
						sendMessage($contract['creator_role_id'],$check_result.'您创建的合同《<a href="'.$url.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>》',1);
					}

					//审核意见
					$c_data = array();
					$c_data['role_id'] = session('role_id');
					$c_data['is_checked'] = $is_agree;
					$c_data['contract_id'] = $contract_id;
					$c_data['content'] = $description;
					$c_data['check_time'] = time();
					$c_data['order_id'] = $contract['order_id'];
					M('ContractCheck')->add($c_data);
					if ($is_agree == 1 && $is_end == 1) {
						//客户状态改为已成交客户
						M('Customer')->where('customer_id =%d',$contract['customer_id'])->setField('customer_status','已成交客户');
						//商机状态改变为项目成功
						if($contract['business_id']){
							$m_business = M('Business');
							$status_type_id = $m_business->where(array('business_id'=>$contract['business_id']))->getField('status_type_id');
							$status_id = M('BusinessStatus')->where(array('type_id'=>$status_type_id,'is_end'=>3))->getField('status_id');
							$m_business->where(array('business_id'=>$contract['business_id']))->setField('status_id',$status_id);
						}
						if($contract['renew_contract_id'] > 0){
							$m_contract->where('contract_id =%d',$contract['renew_contract_id'])->setField('contract_status',1);
						}
					}
					$m_sales->where('sales_id =%d',$sales_id)->setField('is_checked',$data['is_checked']);
					if ($result) {
						actionLog($contract_id);
						//同时创建应收款
						if ($is_agree == 1 && $is_end == 1) {
							//判断是否生成应收款
							if ($is_receivables == 1) {
								$m_receivables = M('Receivables');
								$r_data['type'] = 1;
								//应收款编号
								$receivables_custom = M('Config')->where('name = "receivables_custom"')->getField('value');
								$receivables_max_id = $m_receivables->max('receivables_id');
								$receivables_max_id = $receivables_max_id+1;
								$receivables_max_code = str_pad($receivables_max_id,4,0,STR_PAD_LEFT);//填充字符串的左侧（将字符串填充为新的长度）
								$code = $receivables_custom.date('Ymd').'-'.$receivables_max_code;
								$r_data['name'] = $code;
								$r_data['prefixion'] = $receivables_custom;
								$r_data['price'] = $contract['price'] ? $contract['price'] : '0.00';
								$r_data['customer_id'] = $contract['customer_id'];
								$r_data['contract_id'] = $contract_id;
								$r_data['sales_id'] = $sales_id;
								$r_data['pay_time'] = $_POST['pay_time'] ? $_POST['pay_time'] : time();
								$r_data['creator_role_id'] = $contract['creator_role_id'];
								$r_data['owner_role_id'] = $contract['owner_role_id'];
								$r_data['create_time'] = time();
								$r_data['update_time'] = time();
								$r_data['status'] = 0;
								$m_receivables->add($r_data);
							} else {
								//发站内信给财务
								$receivables_userId = getRoleByPer(array('finance/add_receivables'));
								foreach($receivables_userId as $v){
									$c = U('contract/view','id='.$contract_id);
									sendMessage($v,'《<a href="'.$c.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>》<font style="color:green;">已通过审核，财务人员可添加应收款单据</font>！',1);
								}
							}
							//发送站内信
							$url = U('contract/view','id='.$contract_id);
							sendMessage($contract['creator_role_id'],'您创建的合同《<a href="'.$url.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>》<font style="color:green;">已通过审核</font>！',1);
						} elseif ($is_agree == 2 && $is_end == 1) {
							sendMessage($contract['creator_role_id'],'您创建的合同《<a href="'.$url.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>》<font style="color:red;">经审核已被拒绝！请及时更正！</font>！',1);
						}

						//发送站内信给下一审批人
						if ($is_agree == 1 && $is_end != 1) {
							//下一审批人
							if (is_array($next_role_id)) {
								$examine_role_ids = $next_role_id ? array_filter($next_role_id) : array();
							} elseif ($next_role_id) {
								$examine_role_ids = $next_role_id ? array_filter(explode(',',$next_role_id)) : array();
							} else {
								$examine_role_ids = array();
							}
							$url = U('contract/view','id='.$contract_id);
							$creator = getUserByRoleId($contract['owner_role_id']);
							$message_content = '您有一个合同<a href="'.$url.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>待审批！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.$contract['owner_role_id'].'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp';
							foreach ($examine_role_ids as $k=>$v) {
								sendMessage($v,$message_content,1);
							}
						}
						$this->ajaxReturn('','审核成功！',1);
					} else {
						$this->ajaxReturn('','审核失败！',0);
					}
				} else {
					$this->ajaxReturn('','审核失败！',0);
				}
			} else {
				$this->ajaxReturn('',L('THE_ORDER_HAS_BEEN_CHECKED_DO_NO_REPEAT_THE_OPERATION'),0);
			}
		}
	}

	/**
	 * 合同下财务信息
	 * @param 
	 * @author 
	 * @return 
	 */
	public function finance() {
		if ($this->isPost()) {
			$contract_id = $_POST['id'] ? intval($_POST['id']) : '';
			if (!$contract_id) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$d_contract = D('ContractView');
			$d_role = D('RoleView');
			$m_quote_product = M('QuoteProduct');
			$contract_info = $d_contract->where(array('contract.contract_id'=>$contract_id))->find();
			//权限判断
			if (empty($contract_info)) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			}
			if (!in_array($contract_info['owner_role_id'], getPerByAction('contract','view'))) {
				$this->ajaxReturn('','您没有此权利！',-2);
			}
			//应收款
			$m_receivables = M('Receivables');
			$m_receivingorder = M('Receivingorder');
			$m_user = M('User');
			//查询应收款列表
			$receivables_list = $m_receivables->where('is_deleted <> 1 and contract_id = %d',$contract_id)->field('receivables_id,price,create_time')->select();
			$price_all = array();
			//应收总金额
			$receivables_price_all = '0.00';
			//已收金额 
			$ys_receivables_price_all = '0.00';
			foreach ($receivables_list as $k=>$v) {
				$receivables_price_all += $v['price'];
				//单个应收款的已收金额
				$ys_receivables_price = '0.00';
				//获取该应收款对应的收款单
				$receivingorder_list = $m_receivingorder->where('is_deleted <> 1 and receivables_id = %d',$v['receivables_id'])->field('receivingorder_id,status,money,pay_time,owner_role_id,receipt_account')->select();
				foreach ($receivingorder_list as $ki=>$vi) {
					if ($vi['status'] == 1) {
						$ys_receivables_price += $vi['money'];
						$ys_receivables_price_all += $vi['money'];
					}
					switch ($vi['status']) {
						case 0 : $status_name = '待审'; break;
						case 1 : $status_name = '通过'; break;
						case 2 : $status_name = '驳回'; break;
						default : $status_name = '待审'; break;
					}
					$receivingorder_list[$ki]['status_name'] = $status_name;
					$receivingorder_list[$ki]['owner_role'] = $m_user->where(array('role_id'=>$vi['owner_role_id']))->getField('full_name');
				}
				$receivables_list[$k]['ys_receivables_price'] = trim($ys_receivables_price);
				$receivables_list[$k]['receivingorder'] = $receivingorder_list ? $receivingorder_list : array();
			}
			$price_all['receivables_price_all'] = trim($receivables_price_all);
			$price_all['sub_receivables_price_all'] = trim($receivables_price_all-$ys_receivables_price_all);
			$data['price'] = $price_all;
			$data['list'] = $receivables_list ? $receivables_list : array();
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		}
	}

	/**
	 * 合同(商机)下产品
	 * @param 
	 * @author 
	 * @return 
	 */
	public function product(){
		if ($this->isPost()) {
			$contract_id = intval($_POST['contract_id']);
			$business_id = intval($_POST['business_id']);
			if (!$contract_id && !$business_id) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$m_product = M('Product');
			$d_product_info = D('ProductInfo');
			if ($contract_id) {
				$m_contract = M('Contract');
				$contract_info = $m_contract->where('contract_id = %d',$contract_id)->find();
				//权限判断
				if (empty($contract_info)) {
					$this->ajaxReturn('','合同不存在或已被删除！',0);
				}
				
				
				$sales_id = M('rContractSales')->where('contract_id = %d && sales_type = 0',$contract_id)->getField('sales_id');
				$sales_info = M('Sales')->where('sales_id = %d', $sales_id)->field('sales_price,final_discount_rate,prime_price')->find();
				$sales_product = M('SalesProduct')->where('sales_id = %d',$sales_id)->order('sales_product_id asc')->field('product_info_id,ori_price,discount_rate,unit_price,amount,unit,subtotal')->select();
				foreach ($sales_product as $k=>$v) {
					$info = $d_product_info->getNameSpec($v['product_info_id']);
					$product_list[$k]['product_name'] = $info['product_name'];
					$product_list[$k]['spec'] = $info['spec'];
				}

				$data['data'] = $sales_info ? $sales_info : array();
				$data['list'] = $sales_product ? $sales_product : array();
				$data['info'] = 'success';
				$data['status'] = 1;
				$this->ajaxReturn($data,'JSON');
			}

			if ($business_id) {
				//产品信息
				$business_info = M('Business')->where(array('business_id'=>$business_id))->field('final_price,final_discount_rate,total_subtotal_val')->find();
				//权限判断
				if (empty($business_info)) {
					$this->ajaxReturn('','商机不存在或已被删除！',0);
				}
				$product_list = M('rBusinessProduct')->where('business_id = %d', $business_id)->field('product_info_id,ori_price,discount_rate,unit_price,amount,unit,subtotal')->select();
				foreach ($product_list as $k=>$v) {
					$info = $d_product_info->getNameSpec($v['product_info_id']);
					$product_list[$k]['product_name'] = $info['product_name'];
					$product_list[$k]['spec'] = $info['spec'];
				}
				$data['data'] = $business_info ? $business_info : array();
				$data['list'] = $product_list ? $product_list : array();
				$data['info'] = 'success';
				$data['status'] = 1;
				$this->ajaxReturn($data,'JSON');
			}
		}
	}

	/**
	 * 合同添加
	 * @param 
	 * @author 
	 * @return 
	 */
	public function add(){
		$m_contract = M('Contract');
		$m_product = M('Product');
		$m_r_contacts_customer = M('RContactsCustomer');
		$contract_custom = M('Config')->where('name="contract_custom"')->getField('value');
		if (!$contract_custom) {
			$contract_custom = 'pd_crm';
		}
		if ($this->isPost()) {
			if (!is_array($_POST)) {
				$this->ajaxReturn('','非法的数据格式!',0);
			}
			//判断合同编号是否存在
			if ($m_contract->where(array('number'=>$contract_custom.trim($_POST['number'])))->find()) {
				$this->ajaxReturn('','该合同编号已存在！',0);
			}
			if (!$_POST['customer_id']) {
				$this->ajaxReturn('','请先选择客户！',0);
			}

			$d_contract = D('Contract');
			$d_contract_data = D('ContractData');
			$field_list = M('Fields')->where('model = "contract" and in_add = 1')->order('order_id')->select();
			foreach ($field_list as $v) {
				if ($v['is_validate'] == 1) {
					if ($v['is_null'] == 1) {
						if ($_POST[$v['field']] == '') {
							$this->ajaxReturn('',$v['name'].'不能为空',0);
						}
					}
					if ($v['is_unique'] == 1) {
						$res = validate('contract',$v['field'],$_POST[$v['field']]);
						if ($res) {
							$this->ajaxReturn('',$v['name'].':'.$_POST[$v['name']].'已存在',0);
						}
					}
				}
				if ($_POST[$v['field']]) {
					switch ($v['form_type']) {
						case 'address':
							$_POST[$v['field']] = $_POST[$v['field']] ? implode(chr(10),json_decode($_POST[$v['field']])) : '';
							break;
						case 'datetime':
							$_POST[$v['field']] = $_POST[$v['field']];
							break;
						case 'box':
							eval('$field_type = '.$v['setting'].';');
							if($field_type['type'] == 'checkbox'){
								$a = array_filter(json_decode($_POST[$v['field']]));
								$_POST[$v['field']] = !empty($a) ? implode(chr(10),$a) : '';
							}
							break;
						default : break;
					}
				}
			}
			
			if ($d_contract->create() && $d_contract_data->create() !== false) {
				$d_contract->type = 1;
				if (empty($_POST['customer_id']) && isset($_POST['business_id'])) {
					$customer_id = M('Business')->where('business_id = %d', $_POST['business_id'])->getField('customer_id');
					$d_contract->customer_id = empty($customer_id) ? 0 : $customer_id;
				} else {
					$d_contract->customer_id = intval($_POST['customer_id']);
				}
				$contract_examine = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
				if ($contract_examine == 1) {
					//自定义流程
					$examine_role_id = M('ContractExamine')->order('order_id asc')->getField('role_id');
				} else {
					$examine_role_id = trim($_POST['examine_role_id']);
				}
				$d_contract->examine_role_id = $examine_role_id ? : '';
				$d_contract->owner_role_id = $_POST['owner_role_id'] ? intval($_POST['owner_role_id']) : session('role_id');
				$d_contract->creator_role_id = session('role_id');
				$d_contract->create_time = time();
				$d_contract->update_time = time();
				$d_contract->count_nums = $_POST['count_nums'] ? $_POST['count_nums'] : 0; //产品总数
				$d_contract->status = L('HAS_BEEN_CREATED');

				$d_contract->number = $contract_custom.trim($_POST['number']);
				$d_contract->prefixion = $contract_custom;
				if ($contractId = $d_contract->add()) {
					$d_contract_data->contract_id = $contractId;
                	$d_contract_data->add();
					//关联日程
					$event_res = dataEvent('合同到期',$_POST['end_date'],'contract',$contractId);

					//相关附件
					if ($_POST['file']) {
						$m_contract_file = M('RContractFile');
						foreach ($_POST['file'] as $v) {
							$file_data = array();
							$file_data['contract_id'] = $contractId;
							$file_data['file_id'] = $v;
							$m_contract_file->add($file_data);
						}
					}
					//创建销售单//生成序列号
					$table_info = getTableInfo('sales');
					$m_sales = M('Sales');
					if ($m_sales->create()) {
						$m_sales->creator_role_id = session('role_id');
						$m_sales->status = 97;//未出库
						$m_sales->type = 0;
						$max_id = $m_sales->where(array('type' => 0))->count() + 1;
						$m_sales->sn_code = 'XSD_' . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);
						$m_sales->sales_time = time();
						$m_sales->create_time = time();
						$m_sales->prime_price = $_POST['total_subtotal_val'] ? $_POST['total_subtotal_val'] : 0.00; //产品合计
						$m_sales->sales_price = $_POST['final_price'] ? $_POST['final_price'] : 0.00; // 合同总计

						if ($sales_id = $m_sales->add()) {
							$m_r_contract_sales = M('rContractSales');
							$r_data['contract_id'] = $contractId;
							$r_data['sales_id'] = $sales_id;
							$m_r_contract_sales->add($r_data);

							if (!empty($_POST['product'])) {
								if ($sales_id) {
									$add_product_flag = true;
									$m_sales_product = M('salesProduct');
									foreach ($_POST['product'] as $v) {
										$data = array();
										if (!empty($v['product_id'])) {
											$count_nums += 1;
											$data['sales_id'] = $sales_id;
											$data['product_id'] = $v['product_id'];
											$data['amount'] = $v['amount'];
											$data['unit_price'] = $v['unit_price'];
											$data['discount_rate'] = $v['discount_rate'];
											$data['subtotal'] = $v['subtotal'];
											$data['unit'] = $v['unit'];
											//产品成本
											$cost_price = '0.00';
											$cost_price = $m_product->where('product_id = %d',$v['product_id'])->getField('cost_price');
											$data['cost_price'] = $cost_price ? $cost_price : 0;
											//销售时产品售价
											$data['ori_price'] = $v['ori_price'];
											$sales_product_id = $m_sales_product->add($data);
											if (empty($sales_product_id)) {
												$add_product_flag = false;
												break;
											}
										}
									}
									if (!$add_product_flag) {
										$this->ajaxReturn('','合同产品信息创建失败！',0);
									}
								}else{
									$this->ajaxReturn('','合同产品信息创建失败！',0);
								}
							}
						}
					}
					
					//商机状态改为合同签订，客户自动锁定
					$business_id = intval($_POST['business_id']);
					$customer_id = intval($_POST['customer_id']);
					$m_business = M('Business');
					$status_type_id = $m_business->where(array('business_id'=>$business_id))->getField('status_type_id');
					$status_id = M('BusinessStatus')->where(array('type_id'=>$status_type_id,'is_end'=>3))->getField('status_id');
					$m_business->where(array('business_id'=>$business_id))->setField('status_id',$status_id);
					M('Customer')->where('customer_id =%d',$customer_id)->setField('is_locked',1);
					M('RBusinessContract')->add(array('contract_id'=>$contractId,'business_id'=>$business_id));
					actionLog($contractId);
					
					//通知合同相关审核人
					$url = U('contract/view','id='.$contractId);
					//合同审批人类型
					$contract_examine = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
					$contract_examine = $contract_examine ? $contract_examine : '0';
					if ($contract_examine) {
						//自定义流程
						$role_ids_a = M('ContractExamine')->order('order_id asc')->getField('role_id');
						$role_ids_a = array_filter(explode(',', $role_ids_a));
					} else {
						//合同审核人
						$position_ids = M('Permission')->where(array('url'=>'contract/check'))->getField('position_id',true);
						$position_ids = !empty($position_ids) ? $position_ids : array();
						$role_ids_a = M('Role')->where(array('position_id'=>array('in',$position_ids)))->getField('role_id',true);
					}
					if (!$role_ids_a) {
						//管理员
						$role_ids_a = M('User')->where(array('category_id'=>1,'status'=>1))->getField('role_id',true);
					}
					foreach($role_ids_a as $v){
						sendMessage($v,$_SESSION['name'].'&nbsp;&nbsp;创建了新的合同《<a href="'.$url.'">'.trim($_POST['number']).'-'.trim($_POST['contract_name']).'</a>》<font style="color:green;">需要进行审核</font>！',1);
					}
					$this->ajaxReturn('','添加成功！',1);
				}else{
					$this->ajaxReturn('','添加失败,'.$d_contract->getError(),0);
				}
			}
		}
	}

	/**
	 * 合同修改
	 * @param 
	 * @author 
	 * @return 
	 */
	public function edit(){
		if ($this->isPost()) {
			if (!is_array($_POST)) {
				$this->ajaxReturn('','非法的数据格式!',0);
			}
			$m_contract = M('Contract');
			$m_sales = M('Sales');
			$contract_id = $_POST['id'] ? intval($_POST['id']) : '';
			$_POST['contract_id'] = $contract_id;

			$contract_info = D('ContractView')->where('contract.contract_id = %d',$contract_id)->find();
			if ($contract_info['is_checked'] == 1) {
				$this->ajaxReturn('','已审核的合同无法编辑！',0);
			}
			$m_product = M('Product');
			$m_sales_product = M('SalesProduct');
			if (!$contract_info) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			}
			if($this->_permissionRes && !in_array($contract_info['owner_role_id'], $this->_permissionRes)){
				$this->ajaxReturn('','您没有此权利！',-2);
			}
			if (!$_POST['customer_id']) {
				$this->ajaxReturn('','请先选择客户！',0);
			}
			$field_list = M('Fields')->where('model = "contract"')->order('order_id')->select();
			$d_contract = D('Contract');
			$d_contract_data = D('ContractData');
			foreach ($field_list as $v) {
				switch ($v['form_type']) {
					case 'address':
						$_POST[$v['field']] = implode(chr(10),json_decode($_POST[$v['field']]));
						break;
					case 'datetime':
						$_POST[$v['field']] = $_POST[$v['field']];
						break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$_POST[$v['field']] = implode(chr(10),json_decode($_POST[$v['field']]));
						}
						break;
					case 'editor':
						unset($_POST[$v['field']]);
						break;
				}
				if ($v['is_validate'] == 1) {
					if ($v['is_null'] == 1) {
						if($_POST[$v['field']] == ''){
							$this->ajaxReturn('',$v['name'].'不能为空',0);
						}
					}
					if ($v['is_unique'] == 1) {
						$res = validate('contract',$v['field'],$_POST[$v['name']],$contract_id);
						if($res == 1){
							$this->ajaxReturn('',$v['name'].':'.$_POST[$v['name']].'已存在',0);
						}
					}
				}
			}
			//查询合同附表是否存在
			$res_contract_data = $d_contract_data->where(array('contract_id'=>$contract_id))->find();

			if ($d_contract->create() && $d_contract_data->create() !== false) {
				$d_contract->update_time = time();
				$d_contract->contract_id = $contract_id;
				$d_contract->type = 1;
				$d_contract->owner_role_id = intval($_POST['owner_role_id']) ? : session('role_id');
				$d_contract->count_nums = $_POST['count_nums'] ? $_POST['count_nums'] : 0;

				$a = $d_contract->where(array('contract_id'=>$contract_id))->save();
				if ($res_contract_data) {
					$b = $d_contract_data->where(array('contract_id'=>$contract_id))->save();
				} else {
					$d_contract_data->contract_id = $contract_id;
					$b = $d_contract_data->add();
				}
				
				if ($a !== false && $b !== false) {
					$d_contract->where(array('contract_id'=>$contract_id))->setField('is_checked',0);
					//关联日程
					$event_res = dataEvent('合同到期',$_POST['end_date'],'contract',$contract_id);
					//修改销售单
					if ($m_sales->create()) {
						$m_sales->update_time = time();
						$m_sales->prime_price = $_POST['total_subtotal_val'];
						$m_sales->final_discount_rate = $_POST['final_discount_rate'];
						$m_sales->sales_price = $_POST['final_price'];
						$m_sales->total_amount = $_POST['total_amount'] ? $_POST['total_amount'] : 0;
						$sales_id = M('rContractSales')->where('contract_id = %d && sales_type = 0',$contract_id)->getField('sales_id');

						if (!$sales_id) {
							//处理之前编辑时没有创建相关sales数据导致无法编辑产品的问题
							$m_sales->creator_role_id = session('role_id');
							$m_sales->status = 97;//未出库
							$m_sales->type = 0;

							$max_id = $m_sales->where(array('type' => 0))->count() + 1;
							$m_sales->sn_code = 'XSD_' . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);

							$m_sales->sales_time = time();
							$m_sales->create_time = time();

							if ($sales_id = $m_sales->add()) {
								//关系表
								$m_r_contract_sales = M('rContractSales');
								$r_data['contract_id'] = $contract_id;
								$r_data['sales_id'] = $sales_id;
								$m_r_contract_sales->add($r_data);

								if ($_POST['product']) {
									$add_product_flag = true;
									$m_sales_product = M('salesProduct');
									foreach($_POST['product'] as $v){
										if(!empty($v['product_id'])){
											$count_nums += 1;
											$data_product['sales_id'] = $sales_id;
											$data_product['product_id'] = $v['product_id'];
											$data_product['amount'] = $v['amount'];
											$data_product['unit_price'] = $v['unit_price'];
											$data_product['discount_rate'] = $v['discount_rate'];
											$data_product['subtotal'] = $v['subtotal'];
											$data_product['unit'] = $v['unit'];
											//产品成本
											$cost_price = 0;
											$cost_price = $m_product->where('product_id = %d',$v['product_id'])->getField('cost_price');
											$data_product['cost_price'] = $cost_price ? $cost_price : 0;
											//销售时产品售价
											$data_product['ori_price'] = $v['ori_price'];
											$sales_product_id = $m_sales_product->add($data_product);
											if(empty($sales_product_id)){
												$add_product_flag = false;
												break;
											}
										}
									}
									if (!$add_product_flag) {
										$this->ajaxReturn('','合同产品信息创建失败！',0);
									} else {
										$this->ajaxReturn('','修改成功！',1);
									}
								} else {
									$this->ajaxReturn('','修改成功！',1);
								}
							}
						} else {
							if ($m_sales->where('sales_id = %d',$sales_id)->save() !== false) {
								//旧的sales_product_id
								$old_sales_product_ids = array();
								$old_sales_product_ids = $m_sales_product->where('sales_id = %d',$sales_id)->getField('sales_product_id',true);
								$new_sales_product_ids = array();
								if ($_POST['product']) {
									foreach ($_POST['product'] as $v) {
										$add_product_flag = true;
										$data = array();
										if (!empty($v['product_id'])) {
											if (!empty($v['sales_product_id'])) {
												$data['amount'] = $v['amount'];
												$data['unit_price'] = $v['unit_price'];
												$data['discount_rate'] = $v['discount_rate'];
												$data['subtotal'] = $v['subtotal'];
												$data['unit'] = $v['unit'];
												//产品成本
												$cost_price = 0;
												$cost_price = $m_product->where('product_id = %d',$v['product_id'])->getField('cost_price');
												$data['cost_price'] = $cost_price ? $cost_price : 0;
												$sales_product_id = $m_sales_product->where('sales_product_id = %d',$v['sales_product_id'])->save($data);
												//剩余的的sales_product_id
												$new_sales_product_ids[] = $v['sales_product_id'];
											} else {
												$data['sales_id'] = $sales_id;
												$data['product_id'] = $v['product_id'];
												$data['amount'] = $v['amount'];
												$data['unit_price'] = $v['unit_price'];
												$data['discount_rate'] = $v['discount_rate'];
												$data['subtotal'] = $v['subtotal'];
												$data['unit'] = $v['unit'];
												//产品成本
												$cost_price = 0;
												$cost_price = $m_product->where('product_id = %d',$v['product_id'])->getField('cost_price');
												$data['cost_price'] = $cost_price ? $cost_price : 0;
												//销售时产品售价
												$data['ori_price'] = $v['ori_price'];
												$sales_product_id = $m_sales_product->add($data);
												if(empty($sales_product_id)){
													$add_product_flag = false;
													break;
												}
											}
										}
									}
									//需要删除的sales_product_id
									$del_sales_product_ids = array();
									$del_sales_product_ids = array_diff($old_sales_product_ids, $new_sales_product_ids);
									if ($del_sales_product_ids) {
										if (!$m_sales_product->where(array('sales_product_id'=>array('in',$del_sales_product_ids)))->delete()) {
											$add_product_flag = false;
										}
									}
									if ($add_product_flag) {
										$m_sales->where('sales_id =%d',$sales_id)->setField('is_checked',0);
										$this->ajaxReturn('','修改成功！',1);
									} else {
										$this->ajaxReturn('','合同产品信息修改失败！',0);
									}
								} else {
									$this->ajaxReturn('','修改成功！',1);
								}
							} else {
								$this->ajaxReturn('','修改失败！',0);
							}
						}
					}
				} else {
					$this->ajaxReturn('','修改失败！',0);
				}
			} else {
				$this->ajaxReturn('','修改失败,'.$d_contract->getError().$d_contract_data->getError(),0);
			}
		}
	}

	/**
	 * 获取审批人流程
	 * @param 
	 * @author 
	 * @return 
	 */
	public function examineStatus() {
		if ($this->isPost()) {
			$contract_id = $_POST['id'] ? intval($_POST['id']) : 0;
			$m_contract = M('Contract');
			$m_contract_examine = M('ContractExamine');
			$m_contract_check = M('ContractCheck');
			$m_user = M('User');
			if ($contract_id) {
				$contract_info = $m_contract->where(array('contract_id'=>$contract_id))->find();
				$contract_examine = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
				$examine_step_info = $m_contract_examine->where(array('order_id'=>$contract_info['order_id']))->find();
				// 审批权限
				if (!$this->checkPer($contract_id)) {
					$this->ajaxReturn('','您没有审核权限或审批已通过！',0);
				}

				//下一审批相关
				if ($contract_examine == 1) {
					//当前流程是否结束
					//已审批role_id
					$is_check_role = $m_contract_check->where(array('contract_id'=>$contract_info['contract_id'],'order_id'=>$contract_info['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);
					$is_check_role[] = session('role_id');
					$examine_is_end = 0;
					if ($examine_step_info['relation'] == 1 ) {
						//并
						if ($is_check_role && !array_diff(array_merge(array_filter(explode(',',$examine_step_info['role_id']))),$is_check_role)) {
							$examine_is_end = 1;
						}
					} elseif ($examine_step_info['relation'] == 2) {
						//或
						$examine_is_end = 1;
					} else {
						$examine_is_end = 1;
					}
					$relation_name = '';
					if ($examine_is_end == 1) {
						//当前流程结束，转下一审批流程
						$next_order_id = $contract_info['order_id']+1; //下一审批流程排序ID
						//获取下一审批人
	                    $next_examine_step_info = $m_contract_examine->where(array('order_id'=>$next_order_id))->find();
	                    if ($next_examine_step_info['relation'] == 1) {
	                        $relation_name = '并';
	                    } elseif ($next_examine_step_info['relation'] == 2) {
	                        $relation_name = '或';
	                    }
						$next_role_id = $next_examine_step_info['role_id'];
						$next_role_info = D('User')->get_full_name($next_role_id);
						$next_role_id = $next_role_id;
					} else {
						//当前流程，剩余审批人
						$next_order_id = $contract_info['order_id'];
						if ($examine_step_info['relation'] == 1) {
	                        $relation_name = '并';
	                    } elseif ($examine_step_info['relation'] == 2) {
	                        $relation_name = '或';
	                    }

						$next_role_id = $is_check_role ? array_merge(array_diff(array_filter(explode(',',$examine_step_info['role_id'])),$is_check_role)) : array_filter(explode(',',$examine_step_info['role_id']));
						$next_role_info = D('User')->get_full_name($next_role_id);

						$next_role_id = $next_role_id ? ','.implode(',',$next_role_id).',' : '';
					}
				}
			} else {
				//添加时
				if ($contract_examine == 1) {
					$examine_step_info = $m_contract_examine->order('order_id asc')->find();
					$next_role_id = $examine_step_info['role_id'];
					$next_role_info = D('User')->get_full_name($next_role_id);
					$next_order_id = $examine_step_info['order_id']+1;
					if ($examine_step_info['relation'] == 1) {
                        $relation_name = '并';
                    } elseif ($examine_step_info['relation'] == 2) {
                        $relation_name = '或';
                    }
				}
			}

			$data['option'] = $contract_examine ? : '0'; //0自选 1流程
			$data['examine_role'] = $next_role_info ? : array();
			$data['next_role_id'] = $next_role_id ? : '';
			$data['next_order_id'] = $next_order_id ? : 0;
			$data['relation_name'] = $relation_name ? : '';
			$data['status'] = 1;
			$data['info'] = 'success';
			$this->ajaxReturn($data,'JSON');
		}
	}

	/**
	 * 合同撤销审核
	 * @param 
	 * @author 
	 * @return 
	 */
	public function revokeCheck(){
		if ($this->isPost()) {
			$contract_id = intval($_POST['id']);
			$m_contract = M('Contract');
			$m_receivables = M('Receivables');
			$m_receivingorder = M('Receivingorder');
			if (!$contract_id) {
				$this->ajaxReturn('',L('PARAMETER_ERROR'),0);
			}
			if (!$contract = $m_contract->where('contract_id = %d', $contract_id)->find()) {
				$this->ajaxReturn('',L('THE_ORDER_DOES_NOT_EXIST_OR_HAS_BEEN_DELETED'),0);
			}
			// if (intval($contract['is_checked']) !== 1) {
			// 	$this->ajaxReturn('','该合同未审核通过，无需撤销！',0);
			// }
			//只有管理员能撤销
			if (!session('?admin')) {
				$this->ajaxReturn('','您没有此权限！，请联系管理员撤销审核！',-2);
			}

			if ($receivables_info = $m_receivables->where('contract_id =%d and is_deleted = 0',$contract_id )->find()) {
				$receivingorder_list = $m_receivingorder->where('receivables_id = %d',$receivables_info['receivables_id'])->find();
				if ($receivingorder_list) {
					$this->ajaxReturn('','已存在收款单，无法撤销审核！如需撤销请先删除相关收款记录！',0);
				}
			}
			if ($contract['is_checked'] != 0) {
				$m_r_contract_sales = M('rContractSales');
				$r_contract_sales_info = $m_r_contract_sales->where(array('contract_id'=>$contract_id,'sales_type'=>array('neq',1)))->find();
				
				$sales_id = $m_r_contract_sales->where('contract_id = %d && sales_type = 0', $contract_id)->getField('sales_id');
				$sales_status = M('Sales')->where('sales_id =%d',$sales_id)->getField('status');
				//判断销售订单状态
				if ($sales_status == 97 || $sales_status == 99 || $r_contract_sales_info['sales_type'] == 2 || empty($r_contract_sales_info)) {
					//记录审核意见
					$c_data = array();
					$c_data['role_id'] = session('role_id');
					$c_data['is_checked'] = 0;
					$c_data['contract_id'] = $contract_id;
					$c_data['check_time'] = time();
					$m_contract_check = M('ContractCheck');
					$m_contract_check->add($c_data);

					//把审批意见至为无效
					$m_contract_check->where(array('contract_id'=>$contract_id))->setField('is_end', 2);

					$data['is_checked'] = 0;
					$data['examine_role_id'] = 0;
					$data['order_id'] = 0;
					$data['examine_type_id'] = 0;
					$result = $m_contract->where('contract_id = %d', $contract_id)->save($data);
					M('Sales')->where('sales_id =%d',$sales_id)->setField('is_checked',0);
					$m_receivables->where('contract_id =%d and is_deleted = 0',$contract_id )->delete();
					if ($result) {
						actionLog($contract_id);
						//发送站内信
						$url=U('contract/view','id='.$contract_id);
						sendMessage($contract['creator_role_id'],'您创建的合同《<a href="'.$url.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>》<font style="color:red;">已被撤销审核</font>！',1);
						$this->ajaxReturn('',L('REVOKE_CHECK_SUCCESS'),1);
					} else {
						$this->ajaxReturn('',L('REVOKE_CHECK_FAILED'),0);
					}
				} else {
					$this->ajaxReturn('',L('REVOKE_CHECK_FAILED'),0);
				}
			} else {
				$this->ajaxReturn('',L('THE_ORDER_HAS_BEEN_REVOKE_CHECKED_DO_NO_REPEAT_THE_OPERATION'),0);
			}
		}
	}

	/**
	 * 判断审批权限
	 * @param 
	 * @author 
	 * @return 
	 */
	private function checkPer($contract_id){
		$contract_info = M('Contract')->where(array('contract_id'=>$contract_id))->find();
		$m_contract_examine = M('ContractExamine');
		$m_contract_check = M('ContractCheck');
		//审核权限
		if ($contract_info['is_checked']) {
			$contract_examine = $contract_info['examine_type_id'];
		} else {
			$contract_examine = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
		}
		
		if ($contract_info['is_checked'] == 2 || $contract_info['is_checked'] == 1) {
			$add_examine = 0;
		} else {
			//是否有审批权限
			if ($contract_examine == 1) {
				$is_add_examine = 1;
				//当前步骤已审批role_id
				$is_examine_role_ids = $m_contract_check->where(array('contract_id'=>$contract_id,'order_id'=>$contract_info['order_id'],'is_end'=>0))->getField('role_id',true);

				if (in_array(session('role_id'),$is_examine_role_ids)) {
					// $this->ajaxReturn('','您已审核，请勿重复操作！',0);
					$is_add_examine = 0;
				}
				//权限判断（并、或关系的，必须是规定审批人，超级管理员也无此权限）
				$examine_step_info = M('ContractExamine')->where(array('order_id'=>$contract_info['order_id']))->find();
				if (!in_array(session('role_id'),explode(',',$examine_step_info['role_id']))) {
					// $this->ajaxReturn('','您没有审核权限或审批已通过！',0);
					$is_add_examine = 0;
				}
				$add_examine = $is_add_examine;
			} else {
				if (checkPerByAction('contract','check')) {
					if ($contract_info['examine_role_id']) {
						// 审批中（examine_role_id审批人有权限）
						if ($contract_info['examine_role_id'] && in_array(session('role_id'), array_filter(explode(',', $contract_info['examine_role_id'])))) {
							$add_examine = 1;
						}
					} else {
						$add_examine = 1;
					}
				}
			}
		}
		
		if ($add_examine == 1) {
			return true;
		} else {
			return false;
		}
	}
}