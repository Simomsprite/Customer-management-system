<?php
/**
*商机模块
*
**/
class BusinessAction extends Action{
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('validate','check','revert','getsalesfunnel','getcurrentstatus','choose','return_choose','product_view','advance_search','addduibi','view_ajax','getbusinessstatus','trend_number','trend_money','map_number')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
	}

	/**
	*Ajax检测商机名称
	*
	**/
	public function check() {
		if($_REQUEST['business_id']){
			$where['business_id'] = array('neq',$_REQUEST['business_id']);
		}
		import("@.ORG.SplitWord");
		$sp = new SplitWord();
		$m_business = M('Business');
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
			$name_list = $m_business->where($where)->getField('name', true);
			$seach_array = array();
			foreach($name_list as $k=>$v){
				$search = 0;
				foreach($result_array as $k2=>$v2){
					if(strpos($v, $v2) > -1){
						$v = str_replace("$v2","<span style='color:red;'>$v2</span>", $v, $count);
						$search += $count;
					}
				}
				if($search > 2) $seach_array[$k] = array('value'=>$v,'search'=>$search);
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
	*商机列表页（默认页面）
	*
	**/
	public function index(){
		global $m_business_status, $m_customer, $m_contacts;
		$d_v_business = D('BusinessTopView');
		$m_customer = M('Customer');
		$m_contacts = M('Contacts');
		$d_business = D('Business');
		$d_search = D('Search');
		$m_business_status = M('BusinessStatus');
		

		$below_ids = getPerByAction(MODULE_NAME,ACTION_NAME,true);
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;

		$order = "top.set_top desc, top.top_time desc ,business_id desc";
		if($_GET['desc_order']){
			$order = 'top.set_top desc, top.top_time desc ,'.trim($_GET['desc_order']).' desc,business_id asc';
		}elseif($_GET['asc_order']){
			$order = 'top.set_top desc, top.top_time desc ,'.trim($_GET['asc_order']).' asc,business_id asc';
		}

		// 搜索
		$res = $d_search->getWhere($_GET);
		// 搜索where条件
		if ($res['where']) $where = $res['where'];
		
		// 记录分页参数
		$params = $res['page_params'];

		// 记录搜索条件
		$this->fields_search = $res['fields_search'];
		
		// 记录特殊单一搜索条件
		$this->single_list = $res['single_list'];

		// 过滤已删除的数据
		$where['is_deleted'] = 0;

		// 处理特殊字段【客户名称】查询条件
		if (isset($where['customer_name'])) {			
			$customer_ids = $m_customer->where(array('name' => $where['customer_name']))->getField('customer_id', true);
			$where['customer_id'] = array('in', $customer_ids ?: '');
			unset($where['customer_name']);
		}

		// 处理特殊字段【联系人名称、联系人手机】查询条件
		if (isset($where['contacts_name'])) {
		 	$where_contacts['name'] = $where['contacts_name'];
		 	unset($where['contacts_name']);
		}
		if (isset($where['contacts_telephone'])) {
		 	$where_contacts['telephone'] = $where['contacts_telephone'];
		 	unset($where['contacts_telephone']);
		}
		if ($where_contacts) {
			$contacts_ids = $m_contacts->where($where_contacts)->getField('contacts_id', true);
			$where['contacts_id'] = array('in', $contacts_ids);
		}
		
		// 需要权限范围筛选的role_id，需要重新整理where条件
		$where = $d_search->roleWhere('business.owner_role_id', $where, $this->_permissionRes);

		// 特殊参数
		$by = isset($_GET['by']) ? trim($_GET['by']) : 'me';
		switch ($by) {
			case 'sub' : 
				$where['business.owner_role_id'] = array('in', $below_ids); 
				break;
			case 'me' : 
				$where['business.owner_role_id'] = session('role_id'); 
				break;
			case 'all' : 
				$where['business.owner_role_id'] = array('in', $this->_permissionRes);
				break;
			default : 
				break;
		}

		//商机状态分组
		if($_GET['status_type_id']){
			$where['status_type_id'] = intval($_GET['status_type_id']);
			$params[] = 'status_type_id='.intval($_GET['status_type_id']);
		}
		//商机状态【进度】
		if($_GET['status_id']){
			// 如果提交参数有status_id，则必有condiition 条件【order_id在同一状态组也唯一，可代替status_id作为条件查询】
			$order_id = M('BusinessStatus')->where('status_id = %d and type_id = %d', intval($_GET['status_id']), intval($_GET['status_type_id']))->getField('order_id');
			switch ($_GET['status_condition']) {
				case 'eq':
					$where['business_status.order_id'] = $order_id;
					break;
				case 'neq':
					$where['business_status.order_id'] = array('neq', $order_id);
					break;
				case 'gt':
					$where['business_status.order_id'] = array('gt', $order_id);
					break;
				case 'lt':
					$where['business_status.order_id'] = array('lt', $order_id);
					break;
				default:
					break;
			}
			$params[] = 'status_id='.intval($_GET['status_id']);
			$params[] = 'status_condition='.trim($_GET['status_condition']);
		}

		$count =  $d_v_business->where($where)->count();

 		//自定义字段
 		$field_array = getIndexFields('business');
 		$name_field_array = array();
		foreach($field_array as $k=>$v){
			if($v['field'] != 'name'){
				$name_field_array[] = $v;
			}
		}
		$this->field_array = $name_field_array;

		// 导出
		if (trim($_GET['act']) == 'export') {
			if(!checkPerByAction('business','export')){
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
			$table_head = array('field' => array('name' => '商机名称'));
			foreach ($name_field_array as $field) {
				if ($field['field'] == 'contacts_id') {
					$table_head['field']['contacts_name'] = '联系人';
					$table_head['field']['c_telephone'] = '联系电话';
				} elseif ($field['field'] == 'customer_id') {
					$table_head['field']['customer_name'] = '客户';
				} elseif ($field['field'] == 'status_id') {
					$table_head['field']['status_info.name'] = $field['name'];
				} else {
					$table_head['field'][$field['field']] = $field['name'];
				}
			}
			$table_head['field']['schedule'] = '收款进度';
			$table_head['field']['owner.full_name'] = '商机负责人';
			$table_head['field']['create_time'] = '创建时间';
			$temp = $this;
			if ($_GET['ids']) {
				$where = array('business_id' => array('IN', $_GET['ids']));
			}
			csvExport('pdcrm-商机导出', $table_head, $count, function($page) use ($d_v_business, $order, $where, $temp) {
				$list = $d_v_business->where($where)->order($order)->page($page)->select();
				return $temp->_handle($list);
			});
		}

		if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			cookie('listrows', $listrows, 3600 * 24 * 30);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = cookie('listrows') ? cookie('listrows') : 15;
			$params[] = "listrows=".$listrows;
		}
		import("@.ORG.Page");

		$list = $d_v_business->where($where)->order($order)->page($p.','.$listrows)->select();
		$list = $this->_handle($list);
		$p_num = ceil($count/$listrows);
		if($p_num<$p){
			$p = $p_num;
		}
		
		$Page = new Page($count,$listrows);
		if (!empty($_GET['by'])) {
			$params[] = "by=".trim($_GET['by']);
		}
		$this->parameter = implode('&', $params);
		//by_parameter(特殊处理)
		$this->by_parameter = str_replace('by='.$_GET['by'], '', implode('&', $params));

		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}

		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		
 		//商机状态分类
 		$m_business_type = M('BusinessType');
 		$this->status_type_list = $m_business_type->select();
 		$business_type_id = $m_business_type->order('id asc')->getField('id');
 		$status_type_id = $_GET['status_type_id'] ? intval($_GET['status_type_id']) : $business_type_id;
 		$this->status_type_id = $status_type_id;
 		//商机状态
 		$status_list = $m_business_status->where(array('type_id'=>$status_type_id))->order('order_id asc')->select();
 		$this->status_list = $status_list;

		// 搜索字段列表
		$this->field_list = $d_business->search_field_list();

		$this->listrows = $listrows;
		$this->assign('list',$list);
		$this->assign('count',$count);
		$this->alert = parseAlert();
	    $this->display();
	}


	/**
	 * 列表页数据处理
	 */
	public function _handle($list = array())
	{
		$m_user = M('User');
		global $m_business_status, $m_customer, $m_contacts;
		$m_r_business_contacts = M('RBusinessContacts');
		$m_r_contacts_customer = M('RContactsCustomer');

		$m_remind = M('Remind');
		$m_receivables = M('Receivables');
		$m_receivingorder = M('Receivingorder');
		$m_contract = M('Contract');
		$m_r_business_product = M('RBusinessProduct');
		$d_business_product = D('BusinessProductView');
		$m_business_data = M('BusinessData');
		foreach($list as $k => $v){
			//判断附表
			if (!$m_business_data->where(array('business_id'=>$v['business_id']))->find()) {
				$res_data = array();
				$res_data['business_id'] = $v['business_id'];
				$m_business_data->add($res_data);
			}
			$list[$k]['owner'] = $m_user->where('role_id = %d', $v['owner_role_id'])->field('full_name,role_id')->find();
			$list[$k]['creator'] = $m_user->where('role_id = %d', $v['creator_role_id'])->field('full_name,role_id')->find();
			//相关客户
			$list[$k]['customer_name'] = $m_customer->where('customer_id = %s',$v['customer_id'])->getField('name');
			//商机联系人
			$business_contacts_id = $m_r_business_contacts->where('business_id = %d',$v['business_id'])->order('id desc')->getField('contacts_id');
			if(!$business_contacts_id){
				//客户联系人
				$contacts_id = $m_r_contacts_customer->where('customer_id = %d',$v['customer_id'])->order('id desc')->getField('contacts_id');
			}else{
				$contacts_id = $business_contacts_id;
			}
			if($contacts_id){
				$contacts_info = array();
				$contacts_info = $m_contacts->where('contacts_id = %d',$contacts_id)->field('name,telephone')->find();
				$list[$k]['c_telephone'] = $contacts_info['telephone'];
				$list[$k]['contacts_name'] = $contacts_info['name'];
				$list[$k]['contacts_id'] = $contacts_id;
			}

			//提醒
			$remind_info = array();
			$remind_info = $m_remind->where(array('module'=>'business','module_id'=>$v['business_id'],'create_role_id'=>session('role_id'),'is_remind'=>array('neq',1)))->order('remind_id desc')->find();
			$list[$k]['remind_time'] = !empty($remind_info) ? $remind_info['remind_time'] : '';
			//产品名称
			$product_name = array();
			$product_name = $d_business_product->where('r_business_product.business_id = (%d)', $v['business_id'])->getField('name',true);
			if($product_name){
				if(count($product_name) > 1){
					$list[$k]['product_name'] = $product_name[0].'、...';
				}else{
					$list[$k]['product_name'] = $product_name[0];
				}
			}
			//进度
			$status_info = $m_business_status->where(array('status_id'=>$v['status_id'],'type_id'=>$v['status_type_id']))->field('name,order_id,is_end')->find();
			$status_count = $m_business_status->where(array('type_id'=>$v['status_type_id']))->count();
			$list[$k]['status_info'] = $status_info;
			$status_order = $status_info['order_id'];
			$progress = intval($status_order/$status_count > 1 ? 100 : $status_order*100/$status_count);
			$list[$k]['progress'] = $progress;
			//收款进度
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
			$list[$k]['schedule'] = $schedule . '%';
			$list[$k]['create_time'] = date('Y-m-d H:i:s', $list[$k]['create_time']);;
			foreach ($this->field_array as $field) {
				if ($field['form_type'] == 'datetime') {
					$list[$k][$field['field']] = date('Y-m-d H:i:s', $list[$k][$field['field']]);
				} elseif (in_array($field['form_type'], array('number', 'floatnumber', 'phone', 'mobile'))) {
					$list[$k][$field['field']] = $list[$k][$field['field']] . ' ';
				}
			}
		}
		return $list;
	}


	/**
	*添加商机
	*
	**/
	public function add(){
		$m_config = M('Config');
		$m_business = D('Business');
		$m_business_data = D('BusinessData');
		if ($this->isPost()) {
			$m_r_business_product = M('RBusinessProduct');

			$customer_id = intval($_POST['customer_id']);
			if(empty($customer_id)){
				$this->error(L('THE_CUSTOMER_CANNOT_BE_EMPTY'));
			}
			$field_list = M('Fields')->where(array('model'=>'business','in_add'=>1))->order('order_id')->select();
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
			if($m_business->create()){
				if($m_business_data->create() !== false){
					//商机状态
					$m_business->status_id = $_POST['status_id'] ? intval($_POST['status_id']) : 0;
					$m_business->status_type_id = $_POST['status_type_id'] ? intval($_POST['status_type_id']) : 1;
					$m_business->create_time = $m_business->update_time = time();
					$m_business->creator_role_id = session('role_id');
					$m_business->owner_role_id = intval($_POST['owner_role_id']) ? : session('role_id');
					//商机编号
					$business_custom = $m_config->where('name = "business_custom"')->getField('value');
					$business_max_id = $m_business->max('business_id');
					$business_max_code = str_pad($business_max_id+1,4,0,STR_PAD_LEFT);//填充字符串的左侧（将字符串填充为新的长度）
					$code = $business_custom.date('Ymd').'-'.$business_max_code;

					if (empty($_POST['name'])) {
						$m_business->name = $code;
					}
					$m_business->code = $_POST['code'] ? trim($_POST['code']) : $code;
					$m_business->prefixion = $business_custom;
					if ($business_id = $m_business->add()) {
						$m_business_data->business_id = $business_id;
						if ($m_business_data->add()) {
							//商机关联联系人
							if($_POST['contacts_id']){
								$contacts_data = array();
								$contacts_data['business_id'] = $business_id;
								$contacts_data['contacts_id'] = intval($_POST['contacts_id']);
								$res = M('RBusinessContacts')->add($contacts_data);
							}
							if($business_id){
								//客户到期时间
								$m_customer = M('Customer');
								$m_customer->where('customer_id = %d',$customer_id)->setField('update_time',time());
								//关联产品信息
								$business_product_ids = $_POST['business']['product'];
								foreach($business_product_ids as $k=>$v){
									$product_data = array();
									$product_data['business_id'] = $business_id;
									$product_data['product_info_id'] = $v['product_info_id'];
									$product_data['ori_price'] = $v['ori_price'];
									$product_data['discount_rate'] = $v['discount_rate'];
									$product_data['unit_price'] = $v['unit_price'];
									$product_data['amount'] = $v['amount'];
									$product_data['subtotal'] = $v['subtotal'];
									$product_data['unit'] = $v['unit'];
									$m_r_business_product->add($product_data);
								}
								//相关附件
								if($_POST['file']){
									$m_business_file = M('RBusinessFile');
									foreach($_POST['file'] as $v){
										$file_data = array();
										$file_data['business_id'] = $business_id;
										$file_data['file_id'] = $v;
										$m_business_file->add($file_data);
									}
								}
								//判断商机状态
								$status_info = M('BusinessStatus')->where(array('type_id'=>intval($_POST['status_type_id']),'status_id'=>intval($_POST['status_id'])))->find();

								if($status_info['is_end'] == 3){
									$m_customer->where('customer_id = %d', $customer_id)->setField('is_locked',1);
								}
								actionLog($business_id);
								if ($_POST['submit'] == L('SAVE') || $_POST['submit'] == '保存商机') {
									// alert('success', L('ADD_BUSINESS_SUCCESS'), U('customer/view','id='.$customer_id));
									alert('success', L('ADD_BUSINESS_SUCCESS'), U('business/view','id='.$business_id));
								} else {
									alert('success', L('ADD_BUSINESS_SUCCESS'), $_SERVER['HTTP_REFERER']);
								}
							} else {
								$this->error(L('ADD_BUSINESS_FAILURE'));
							}
						}
					}
				} else {
					$this->error($m_business_data->getError());
				}
			}else{
				$this->error($m_business->getError());
			}
		}else{
			//商机编号
			$business = array();
			$business_custom = $m_config->where('name = "business_custom"')->getField('value');
			// $business_max_id = $m_config->where(array('name'=>'business_code'))->getField('value');
			$business_max_id = $m_business->max('business_id');
			$business_max_code = str_pad($business_max_id+1,4,0,STR_PAD_LEFT);//填充字符串的左侧（将字符串填充为新的长度）
			$code = date('Ymd').'-'.$business_max_code;
			$business['code'] = $code;
			$business['business_custom'] = $business_custom;
			$business['name'] = $business_custom.$code;
			//商机状态组
			$this->type_list = M('BusinessType')->order('id')->select();
			if (!$this->type_list) {
				alert('warning', '请联系管理员，添加商机状态组。', $_SERVER['HTTP_REFERER']);
			}

			//商机状态
			$status_list = M('BusinessStatus')->where(array('type_id' => $this->type_list[0]['id']))->order('order_id asc')->select();
			if($_GET['customer_id']){
				$customer_id = intval($_GET['customer_id']);
				$customer_info = M('Customer')->where('customer_id = %d',$customer_id)->field('name,contacts_id')->find();
				if(!$customer_info){
					alert('error','参数错误！',U('business/index'));
				}
				//如果存在首要联系人，则查出首要联系人。否则查出联系人中第一个。
				$contacts = array();
				$m_contacts = M('Contacts');
				if(!empty($customer_info['contacts_id'])){
					$contacts_info = $m_contacts->where('is_deleted = 0 and contacts_id = %d',$customer_info['contacts_id'])->field('name')->find();
					$business['contacts_id'] = $customer_info['contacts_id'];
					$business['contacts_name'] = $contacts_info['name'];
				}else{
					$contacts_customer = M('RContactsCustomer')->where('customer_id = %d',$customer_id)->limit(1)->order('id desc')->select();

					if(!empty($contacts_customer)){
						$contacts_info = $m_contacts->where('is_deleted = 0 and contacts_id = %d',$contacts_customer[0]['contacts_id'])->field('name')->find();
					}
					$business['contacts_id'] = $contacts_customer[0]['contacts_id'];
					$business['contacts_name'] = $contacts_info['name'];
				}
				$business['customer_id'] = $customer_id;
				$business['customer_name'] = $customer_info['name'];
			}
			$this->status_list = $status_list;
			//可能性
			$possibility_list = array();
			for ($x=1; $x<=10; $x++) {
				$possibility_list[] = $x*10;
			}
			$this->possibility_list = $possibility_list;
			$this->business = $business;
			//自定义字段
			$this->field_list = field_list_html('add','business');
			$this->alert = parseAlert();
			$this->display();
		}
	}

	/**
	*修改商机
	*
	**/
	public function edit(){
		if($this->isPost()){
			$business_id = $_POST['business_id'] ? intval($_POST['business_id']) : '';
		}else{
			$business_id = $_GET['id'] ? intval($_GET['id']) : '';
		}
		$d_business = D('BusinessView');
		$m_customer = M('Customer');
		$m_contacts = M('Contacts');
		$m_r_business_product = M('RBusinessProduct');
		$m_product = M('product');

		$business_info = $d_business->where(array('business.business_id'=>$business_id))->find();
		if(!$business_info){
			alert('error','数据不存在或已删除！',$_SERVER['HTTP_REFERER']);
		}
		$m_business_data = M('BusinessData');
		//判断附表
		if (!$m_business_data->where(array('business_id'=>$business_info['business_id']))->find()) {
			$res_data = array();
			$res_data['business_id'] = $business_info['business_id'];
			$m_business_data->add($res_data);
		}

		//判断权限
		$below_ids = getPerByAction('business','edit');
		if($business_info && !in_array($business_info['owner_role_id'],$below_ids)){
			alert('error','您没有此权利！',$_SERVER['HTTP_REFERER']);
		}
		$field_list = M('Fields')->where('model = "business"')->order('order_id')->select();
		
		if($this->isPost()){
			$m_business = D('Business');
			$m_business_data = D('BusinessData');
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
			if($m_business->create()){
				$m_business->update_time = time();
				if ($m_business_data->create() !== false) {
					$a = $m_business->save();
					if ($m_business_data->where(array('business_id'=>$business_id))->count() > 0) {
						$b = $m_business_data->where(array('business_id'=>$business_id))->save();
					} else {
						$m_business_data->business_id = $business_id;
						$b = $m_business_data->add();
					}
					if ($a && $b !== false) {
						if($_POST['contacts_id']){
							$m_r_business_contacts = M('RBusinessContacts');
							$contacts_data = array();
							$contacts_data['contacts_id'] = intval($_POST['contacts_id']);
							$res = $m_r_business_contacts->where('business_id = %d',$business_id)->find();
							if ($res) {
								$m_r_business_contacts->where('business_id = %d',$business_id)->save($contacts_data);
							} else {
								$contacts_data['business_id'] = $business_id;
								$m_r_business_contacts->add($contacts_data);
							}
						}
						$update_res = true;
						$add_res = true;
						$delete_res = true;
						//有r_id的为更新，之前有现在无的为删除，其他的为新增
						$old_r_ids = $m_r_business_product->where('business_id = %d', $business_id)->getField('id',true);
						$new_r_ids = array();
						$business_product_ids = $_POST['business']['product'];
						foreach($business_product_ids as $v){
							$new_r_ids[] = $v['r_id'];
						}
						//获取差集(需要删除的r_id)
						$delete_r_ids = array_diff($old_r_ids,$new_r_ids);
						foreach($business_product_ids as $v){
							$product_data = array();
							$product_data['business_id'] = $business_id;
							$product_data['product_info_id'] = $v['product_info_id'];
							$product_data['ori_price'] = $v['ori_price'];
							$product_data['discount_rate'] = $v['discount_rate'];
							$product_data['unit_price'] = $v['unit_price'];
							$product_data['amount'] = $v['amount'];
							$product_data['subtotal'] = $v['subtotal'];
							$product_data['unit'] = $v['unit'];
							if(!empty($v['r_id'])){
								//更新
								$update_res = $m_r_business_product->where('id = %d',$v['r_id'])->save($product_data);
							}else{
								//添加
								$add_res = $m_r_business_product->add($product_data);
							}
						}
						//删除
						if($delete_res){
							$delete_res = $m_r_business_product->where(array('id'=>array('in',$delete_r_ids)))->delete();
						}

						// 添加操作记录
						recordAction($_POST, $business_info, 'business', $business_info['business_id']);
						alert('success','修改商机成功！',U('business/view','id='.$business_id));
					}else{
						alert('error','修改失败，请重试！',$_SERVER['HTTP_REFERER']);
					}
				}
			} else {
				$this->error($m_business->getError());
			}
		}else{
			//商机状态
			$status_list = M('BusinessStatus')->where(array('type_id'=>$business_info['status_type_id']))->order('order_id asc')->select();
			//商机状态组
			$this->type_list = M('BusinessType')->select();
			//客户
			$customer_name = $m_customer->where('customer_id = %d',$business_info['customer_id'])->getField('name');
			$business_info['customer_name'] = $customer_name;
			//联系人
			$contacts_name = $m_contacts->where('contacts_id = %d',$business_info['contacts_id'])->getField('name');
			$business_info['contacts_name'] = $contacts_name;
			//商品信息
			$product_list = $m_r_business_product->where('business_id = %d',$business_id)->select();
			$m_product_info = M('ProductInfo');
			foreach($product_list as $k=>$v){
				$product = array();
				$product_id = $m_product_info->where(array('product_info_id' => $v['product_info_id']))->getField('product_id');
				$product = $m_product->where('product_id = %d', $product_id)->field('name,category_id')->find();
				$product_list[$k]['product'] = $product;
			}
			$this->product_list = $product_list;
			$this->status_list = $status_list;
			//可能性
			$possibility_list = array();
			for ($x=1; $x<=10; $x++) {
				$possibility_list[] = $x*10;
			}
			$this->possibility_list = $possibility_list;
			$this->business_info = $business_info;
			//自定义字段
			$this->field_list = field_list_html('edit','business',$business_info);
			$this->alert = parseAlert();
			$this->display();
		}
	}

	//商机详情
	public function view(){
		$business_id = $_GET['id'] ? intval($_GET['id']) : '';
		if(!$business_id){
			alert('error','参数错误！',U('business/index'));
		}
		//判断附表有无数据（没有则新建）
		$m_business_data = M('BusinessData');
		$res_data = $m_business_data->where(array('business_id'=>$business_id))->find();
		if (!$res_data) {
			$bus_data = array();
			$bus_data['business_id'] = $business_id;
			$m_business_data->add($bus_data);
		}

		$d_business = D('BusinessView');
		$m_customer = M('Customer');
		$m_contacts = M('Contacts');
		$m_business_status = M('BusinessStatus');
		$below_ids = getPerByAction('business','view');
		//判断权限
		$business_info = $d_business->where(array('business.business_id'=>$business_id))->find();
		if($business_info && !in_array($business_info['owner_role_id'],$below_ids)){
			alert('error','您没有此权利！',$_SERVER['HTTP_REFERER']);
		}
		$customer_info = $m_customer->where(array('customer_id'=>$business_info['customer_id']))->field('name')->find();
		//商机联系人
		$contacts_info = $m_contacts->where(array('contacts_id'=>$business_info['contacts_id']))->field('name,telephone')->find();
		$business_info['customer_info'] = $customer_info;
		$business_info['contacts_info'] = $contacts_info;
		//商机状态
		$business_info['status_order_id'] = $m_business_status->where(array('status_id'=>$business_info['status_id'],'type_id'=>$business_info['status_type_id']))->getField('order_id');
		$status_list = $m_business_status->where(array('type_id'=>$business_info['status_type_id']))->order('order_id asc')->select();
		foreach ($status_list as $key => $val) {
			if ($val['status_id'] == 99) {
				unset($status_list[$key]);
				continue;
			}
			$status_list[$key]['name_2'] = cutString($val['name'], 4);
		}
		$this->status_list = $status_list;
		$this->business_info = $business_info;
		$this->business_id = $business_id;
		//自定义字段
		$this->field_list = M('Fields')->where(array('model'=>'business','field'=>array('not in',array('name','status_id'))))->order('is_main desc, order_id asc')->select();
		$this->alert = parseAlert();
		$this->display();
	}

	//商机详情加载
	public function view_ajax(){
		/*根据 传过来的 customer_id 或者 business_id  判断是单个还是所有*/
		$customer_id = $this->_request('customer_id','intval');
		$d_contract = D('ContractView');
		$m_contract = M('Contract');
		$m_business = M('Business');
		$m_customer = M('Customer');
		$m_invoice = M('Invoice');
		$m_user = M('User');
		$m_visitor_plan = M('VisitorPlan');
		$below_ids = getPerByAction('business','view');
		$where_pre = array();
		// 判断权限
		if(!empty($customer_id)){
			$where_pre['customer_id'] = $customer_id;

			//权限判断（根据客户）
			$m_config = M('Config');
			$customer_info = D('CustomerView')->where('customer.customer_id = %d', $customer_id)->find();
			$outdays = $m_config->where('name="customer_outdays"')->getField('value');
			$outdate = empty($outdays) ? 0 : time()-86400*$outdays;

			$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
			$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
			$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
			$openrecycle = $m_config->where('name="openrecycle"')->getField('value');
			if ($openrecycle == 2) {
				if ($customer_info['owner_role_id'] != 0 && (($customer_info['update_time'] > $outdate && $customer_info['get_time'] > $contract_outdays) || $customer_info['is_locked'] == 1) && !$_REQUEST['share_num']) {
					if (!in_array($customer_info['owner_role_id'], getPerByAction('customer','view'))) {
						echo '<div class="alert alert-error">您没有此权利！</div>';die();
					}
				}
			}
		}else{
			$business_id = $this->_request('id','intval');
			$where_pre['customer_id'] = $m_business->where('business_id = %d',$business_id)->getField('customer_id');

			//权限判断（根据商机）
			$business_info = $m_business->where(array('business_id'=>$business_id))->find();
			if(!session('?admin') && $business_info && !in_array($business_info['owner_role_id'],$below_ids)){
				echo '<div class="alert alert-error">您没有此权利！</div>';die();
			}
		}
		// 判断权限 END
		
		//逻辑有点乱
		if(!empty($customer_id)){
			$cowner_role_id = M('Customer')->where('customer_id =%d',$customer_id)->getField('owner_role_id');
			$cowner_role_ids = getPerByAction('business','index');
			$bc_where['customer_id'] = $customer_id;
			$bc_where['is_deleted'] = 0;
			$bc_where['owner_role_id'] = array('in',$cowner_role_ids);
			$business_ids = $m_business->where($bc_where)->getField('business_id', true);
			$business = array();
			if ($business_ids) {
				$business = $m_business->where(array('business_id'=>array('in',$business_ids),'code'=>array('neq','')))->order('business_id desc')->select();
			}
			//联系人列表
			$mail_contacts_id = M('customer')->where('customer_id = %d and is_deleted=0', $customer_id)->getField('contacts_id');
			$contacts_ids = M('RContactsCustomer')->where('customer_id = %d',$customer_id)->getField('contacts_id',true);
			$contacts_ids = $contacts_ids ? : -1;
			$contacts_list = M('contacts')->where(array('contacts_id'=>array('in',$contacts_ids),'is_deleted'=>'0'))->select();
			$this->mail_contacts_id = $mail_contacts_id;
			$this->contacts_list = $contacts_list;
			//销售合同
			$htowner_role_ids = getPerByAction('contract','index');
			if($htowner_role_ids){
				$b_where['owner_role_id'] = array('in',$htowner_role_ids);
			}else{
				$b_where['owner_role_id'] = -1;
			}
			$b_where['customer_id'] = $customer_id;
			$contract_list = $m_contract ->where($b_where)->select();
			
			foreach($contract_list as $c_k=>$c_v){
				$contract_list[$c_k]['owner'] = getUserByRoleId($c_v['owner_role_id']);
				$contract_list[$c_k]['business'] = $m_business ->where('business_id =%d',$c_v['business_id'])->field('code')->find();
				//发票金额
				$contract_list[$c_k]['invoice_price'] = $m_invoice->where(array('contract_id'=>$c_v['contract_id'],'is_checked'=>array('neq',2)))->sum('price');
			}
			$this->contract_list = $contract_list;
			//相关发票信息
			$this->invoice_info = M('RCustomerInvoice')->where(array('customer_id'=>$customer_id))->find();

			// 客户操作记录
			$action_record = actionRecord($customer_id, 'customer'); 
			$this->assign('group_list', $action_record);

			//应收款
			$rv_where = array();
			$rv_where['customer_id'] = $customer_id;
			$rv_where['owner_role_id'] = array('in',implode(',', getPerByAction('finance','index_receivables')));
			$receivables_ids = M('Receivables')->where($rv_where)->getField('receivables_id',true);
			$receivables_ids = $receivables_ids ? $receivables_ids : array('-1');
			$re_where = array();
			$re_where['receivables_id'] = array('in',$receivables_ids);
			$re_where['owner_role_id'] = array('in',implode(',', getPerByAction('finance','index_receivingorder')));
			$receivingorder_list = M('Receivingorder')->where($re_where)->select();
			foreach($receivingorder_list as $kr=>$vr){
				$receivingorder_list[$kr]['owner'] = getUserByRoleId($vr['owner_role_id']);
			}
			$this->receivingorder_list = $receivingorder_list;

			//应付款
			$pv_where = array();
			$pv_where['customer_id'] = $customer_id;
			$pv_where['owner_role_id'] = array('in',implode(',', getPerByAction('finance','index_payables')));
			$payables_ids = M('Payables')->where($pv_where)->getField('payables_id',true);
			$payables_ids = $payables_ids ? $payables_ids : array('-1');
			$pa_where = array();
			$pa_where['payables_id'] = array('in',$payables_ids);
			$pa_where['owner_role_id'] = array('in',implode(',', getPerByAction('finance','index_paymentorder')));
			$paymentorder_list = M('Paymentorder')->where($pa_where)->select();
			foreach($paymentorder_list as $kr=>$vr){
				$paymentorder_list[$kr]['owner'] = getUserByRoleId($vr['owner_role_id']);
			}
			$this->paymentorder_list = $paymentorder_list;
		}else{
			$business_id = $this->_request('id','intval');
			$business = $m_business->where('business_id = %d',$business_id)->select();
			$this->is_business_code = 1;
			//联系人列表
			$contacts_ids = M('RBusinessContacts')->where('business_id = %d',$business_id)->getField('contacts_id',true);
			$contacts_ids = $contacts_ids ? : -1;
			$contacts_list = M('Contacts')->where(array('contacts_id'=>array('in',$contacts_ids),'is_deleted'=>'0'))->select();
			$this->contacts_list = $contacts_list;
			//销售合同
			$htowner_role_ids = getPerByAction('contract','index');
			if($htowner_role_ids){
				$b_where['owner_role_id'] = array('in',$htowner_role_ids);
			}else{
				$b_where['owner_role_id'] = -1;
			}
			$b_where['business_id'] = $business_id;
			$contract_info = $m_contract ->where($b_where)->find();
			$is_find = $m_contract ->where('business_id =%d',$business_id)->getField('contract_id');
			$this->is_find = $is_find;
			$contract_info['customer_name'] = $m_customer->where('customer_id=%d', $contract_info['customer_id'])->getField('name');
			$contract_info['creator_info'] = $m_user->where('role_id = %d', $contract_info['owner_role_id'])->field('full_name,role_id,img')->find();
			$contract_info['owner_name'] = $m_user->where('role_id = %d', $contract_info['owner_role_id'])->getField('full_name');
			$contract_info['examine'] = $m_user->where('role_id = %d', $contract_info['examine_role_id'])->find();
			//发票金额
			$contract_info['invoice_price'] = $m_invoice->where(array('contract_id'=>$contract_info['contract_id'],'is_checked'=>array('neq',2)))->sum('price');
			//审批流程
			if ($contract_info['examine_type_id']) {
				$contract_examine = $contract_info['examine_type_id'];
			} else {
				//contract_examine 1为自定义审批流
				$contract_examine = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
			}
			if ($contract_examine == 1) {
				//自定义流程
				$check_list = M('ContractExamine')->order('order_id')->select();
				foreach ($check_list as $k=>$v) {
					$check_list[$k]['user_info'] = $m_user->where(array('role_id'=>$v['role_id']))->field('full_name,role_id,thumb_path')->find();
				}
				$this->check_list = $check_list;
			}
			$this->contract_examine = $contract_examine;

			$this->c_business_id = $business_id;
			$this->contract_info = $contract_info;

			// 商机操作记录
			$action_record = actionRecord($business_id, 'business'); 
			$this->assign('group_list', $action_record);
		}

        $business_logs = array();
        $business_products = array();

        $m_product = M('Product');
        $m_r_business_log = M('rBusinessLog');
        $m_log = M('Log');
        $m_r_business_file = M('rBusinessFile');
        $m_file = M('File');
        $m_r_business_product = M('rBusinessProduct');
        $m_product_category = M('ProductCategory');
        $m_log_status = M('LogStatus');
		$m_contacts = M('Contacts');
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
		}else{
			$file_ids = $business[0]['business_file_ids']; //商机附件

			$logs_list = $business_logs; //商机日志
			$this->business_id = $business_id;
			$customer_id = $business[0]['customer_id'];//赋值客户id,关联联系人用
		}
		$file_info = array();
		if ($file_ids) {
			$file_info = M('File')->where(array('file_id'=>array('in',$file_ids)))->select();
		}
		$d_file = D('File');
        foreach ($file_info as $fk=>$fv){
            $file_info[$fk]['owner'] = D('RoleView')->where('role.role_id = %d',$fv['role_id'])->find();
            $file_info[$fk]['size'] = ceil($fv['size']/1024);
			/*判断文件格式 对应其图片*/
			$file_info[$fk]['pic'] = show_picture($fv['name']);
			if ($fv['oss'] == 1) {
				$file_info[$fk]['file_path'] = $d_file::FILE_URL . '/' . $fv['file_path'];
			}
        }
		$this->alert = parseAlert();
		$this->status_counts =$status_counts;
		$this->customer_id = $customer_id;
		$this->business = $business;
        //附件的输出模板
        $this->assign('file_info',$file_info);
		$this->business_products = $business_products;
		$this->log_list = $logs_list;
		//自定义快捷回复
		$this->status_list = M('LogStatus')->select();
		$where_reply = array();
		$where_reply['type']  = 1;
		$where_reply['role_id']  = session('role_id');
		$where_reply['_logic'] = 'or';
		$map['_complex'] = $where_reply;
		$reply_list = M('LogReply')->where($map)->select();
		foreach ($reply_list as $k=>$v) {
			$reply_list[$k]['str_content'] = cutString($v['content'],'12');
		}
		
		// 客户关联日程信息
		$m_event = M('Event');
		$new_customer_id = $this->_request('customer_id','intval');
		$business_id = $this->_request('id','intval');
		if($new_customer_id){
			$business_ids = $m_business->where('customer_id =%d',$new_customer_id)->getField('business_id',true);
			if($business_ids){
				$where = array();
				$business_str = implode(',',$business_ids);
				$where['_string'] = '(module_id ='.$customer_id.' AND module ="customer") or (module ="business" and module_id in ('.$business_str.'))';
			}else{
				$where = array();
				$where['_string'] = '(module_id ='.$customer_id.' AND module ="customer")';
			}
		}elseif($business_id){
			$where = array();
			$where['_string'] = '(module_id ='.$business_id.' AND module ="business")';
		}
		$event_list = $m_event ->where($where)->page('1, 10')->select();
		foreach($event_list as $k=>$v){
			$event_list[$k]['create_role_name'] = $m_user ->where('role_id =%d',$v['creator_role_id'])->getField('full_name');
			if($v['module'] == 'business'){
				$event_list[$k]['bus_num'] = $m_business ->where('business_id =%d',$v['module_id'])->getField('name');
			}
			$temp_img = $m_user->where('role_id =%d',$v['creator_role_id'])->getField('img');
			$event_list[$k]['img'] = $temp_img ? $temp_img : './Public/img/avatar_default.png"';
		}
		$this->module = $this->_request('module');
		$this->event_list = $event_list;
		$this->reply_list = $reply_list;
		$this->content = $this->_request('content');
		$this->share_num = $this->_request('share_num');
		$this->display();
	}
	
	
	//客户列表产品详情
	public function product_view(){
		$business_id = $this->_get('id','intval');
		$m_business = M('Business');
		$m_product = M('Product');
		$m_r_business_product = M('RBusinessProduct');
		$m_product_category = M('ProductCategory');
		$business_product = $m_r_business_product->where('business_id = %d', $business_id)->select();
		$business_info = $m_business->where('business_id = %d',$business_id)->find();
		foreach ($business_product as $k => $v) {
			$info = $m_product->where('product_id = %d', $v['product_id'])->find();
			$business_product[$k]['info'] = $info;
			$business_product[$k]['name'] = $info['name'];
			$business_product[$k]['category_name'] = $m_product_category->where('category_id = %d',$info['category_id'])->getField('name');
		}
		$this->business_info = $business_info;
		$this->business_product = $business_product;
		$this->display();
	}

	/**
	*删除商机
	*
	**/
	public function delete(){
		$m_business = M('Business');
		$m_contract = M('Contract');
		$m_log = M('Log');
		$r_module = array('RBusinessCustomer', 'Event'=>'RBusinessEvent', 'File'=>'RBusinessFile', 'Log'=>'RBusinessLog', 'RBusinessProduct', 'Task'=>'RBusinessTask');
		if (intval($_GET['id']) || $_POST['business_id']) {
			$business_id = array();
			if ($_GET['id']) {	
				$business_id[] = intval($_GET['id']);
			} elseif ($_POST['business_id']){
				$business_id = $_POST['business_id'];
			}
			$business_list = $m_business->where(array('business_id'=>array('in', $business_id)))->select();
			$m_customer = M('Customer');
			$m_config = M('Config');
			$delete_business_ids = array();
			$rel_business = array();
			$error_message = array();

			$below_ids = getPerByAction('business','view');
			foreach($business_list as $k=>$v){

				//判断权限
				if (!in_array($v['owner_role_id'], $below_ids)) {
					$error_message[] = '商机《'.$v['name'].'》删除失败,您没有此权利！';
				} else {
					$delete_business_ids[$k]['business_id'] = $v['business_id'];
					$delete_business_ids[$k]['name'] = $v['name'] ? $v['name'] : $v['code'];
				}
			}
			
			if (is_array($business_list)) {
				if ($delete_business_ids) {
					//判断是否有相关合同(如有合同，则需先删除合同信息)
					foreach($delete_business_ids as $k=>$v){
						$contract_info = $m_contract->where(array('business_id'=>$v['business_id']))->find();
						if($contract_info){
							$error_message[] = '商机《'.$v['name'].'》下已有合同，请先删除相关合同信息！';
						}else{
							if($m_business->where('business_id = %d', $v['business_id'])->delete()){
								M('BusinessData')->where(array('business_id'=>$v['business_id']))->delete();
								actionLog($v['business_id']);
								foreach ($r_module as $key2=>$value2) {
									if(!is_int($key2)){
										$module_ids = M($value2)->where('business_id = %d', $v['business_id'])->getField($key2 . '_id',true);
										$m_key = M($key2);
										$m_key->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
										M($value2)->where('business_id = %d', $v['business_id'])->delete();
									}
								}
							} else {
								$error_message[] = '商机《'.$v['name'].'》删除失败！';
							}
						}
					}
					if($error_message){
						$message_data = '部分商机删除失败,失败原因如下：';
						foreach($error_message as $v){
							$message_data .= $v;
						}
						$this->ajaxReturn('',$message_data,0);
					}else{
						$this->ajaxReturn('',L('DELETE_THE_SUCCESS'),1);
					}
				} else {
					$this->ajaxReturn('','您没有此权利！',0);
				}
			} else {
				$this->ajaxReturn('',L('YOU_WANT_TO_DELETE_THE_RECORD_DOES_NOT_EXIST'),0);
			}
		} else {
			$this->ajaxReturn('',L('PLEASE_SELECT_ITEMS_TO_DELETE'),0);
		}
	}

	public function listDialog(){
		$d_business = D('BusinessTopView');
		$where['business_status.is_end'] = array('gt',0);
		$where['owner_role_id'] = array('in', $this->_permissionRes);
		$where['is_deleted'] = 0;
		if($_GET['customer_id']){
			$where['customer_id'] = intval($_GET['customer_id']);
		}

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

		$list = $d_business->order('business.create_time desc')->where($where)->page($p.',10')->select();
		
		$m_customer = M('Customer');
		$m_business_status = M('BusinessStatus');
		$m_contacts = M('Contacts');
		$m_r_contacts_customer = M('RContactsCustomer');

		foreach($list as $k=>$v){
			$customer_info = array();
			$customer_info = $m_customer->where('customer_id = %d', $v['customer_id'])->field('name,contacts_id')->find();
			$list[$k]['customer_name'] = $customer_info['name'];
			//阶段
			$list[$k]['status_name'] = $m_business_status->where(array('status_id'=>$v['status_id'],'type_id'=>$v['status_type_id']))->getField('name');

			//联系人
			//如果存在首要联系人，则查出首要联系人。否则查出联系人中第一个。
			$contacts_info = $m_contacts->where('is_deleted = 0 and contacts_id = %d',$customer_info['contacts_id'])->field('contacts_name,telephone')->find();
			if($contacts_info){
				$list[$k]['contacts_id'] = $customer_info['contacts_id'];
				$list[$k]['contacts_name'] = $contacts_info['name'];
				$list[$k]['telephone'] = $contacts_info['telephone'];
			}else{
				$contacts_customer = $m_r_contacts_customer->where('customer_id = %d',$v['customer_id'])->limit(1)->order('id desc')->select();
				if(!empty($contacts_customer)){
					$contacts = $m_contacts->where('is_deleted = 0 and contacts_id = %d',$contacts_customer[0]['contacts_id'])->find();
					$list[$k]['contacts_id'] = $contacts['contacts_id'];
					$list[$k]['contacts_name'] = $contacts['name'];
					$list[$k]['telephone'] = $contacts['telephone'];
				}
			}

		}
		$count = $d_business->where($where)->count();

		$this->search_field = $_REQUEST;//搜索信息
		$Page = new Page($count,10);
		$Page->parameter = implode('&', $params);
		$this->assign('page',$Page->show());

		$this->assign('businessList',$list);
		$this->display();
	}
	
	/**
	*商机推进
	*
	**/
	public function advance(){
		if($this->isPost()){
			$business_id = $_REQUEST['business_id'] ? intval($_REQUEST['business_id']) : 0;
			$is_updated = false;
			$m_r_bs = M('RBusinessStatus');
			$m_customer = M('Customer');
			$m_business = M('Business');
			$business = $m_business->where('business_id = %d', $business_id)->find();
			if(!in_array($business['owner_role_id'] , getPerByAction('business','edit'))){
				alert('error',  L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
			}
			//推进历史
			$data['business_id'] = $business_id;
			$data['status_id'] = intval($_REQUEST['status_id']);
			$data['description'] = '';
			$data['owner_role_id'] = $business['owner_role_id'];
			$data['update_time'] = time();
			$data['update_role_id'] = session('role_id');
			$data['total_price'] = $business['final_price'];
			$m_r_bs->add($data);
			
			$data2['update_time'] = time();

			if($_POST['status_check']){
				//项目失败
				$data2['status_id'] = intval($_POST['status_check']);
			}else{
				$data2['status_id'] = intval($_REQUEST['status_id']);
				$status_info = M('BusinessStatus')->where(array('type_id'=>$business['status_type_id'],'status_id'=>intval($_REQUEST['status_id'])))->find();
				if($status_info['is_end'] == 3){
					//锁定客户
					$m_customer->where('customer_id = %d', $business['customer_id'])->setField('is_locked',1);
				}
			}
			if($_POST['nextstep_time']){
				$data2['nextstep_time'] = strtotime($_POST['nextstep_time']);
			}
			$data2['nextstep'] = trim($_POST['nextstep']);
			$data2['update_role_id'] = session('role_id');
			if (isset($_POST['possibility'])) {
				$data2['possibility'] = $_POST['possibility'];
			}
			if($m_business->where('business_id = %d', $business_id)->save($data2)){
				//客户到期时间
				$m_customer->where('customer_id = %d',$business['customer_id'])->setField('update_time',time());
				actionLog($business_id);
				if($this->isAjax()){
					$this->ajaxReturn('',L('TO_PROMOTE_SUCCESS'),1);
				}else{
					alert('success', L('TO_PROMOTE_SUCCESS'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				if($this->isAjax()){
					$this->ajaxReturn('',L('PROMOTE_FAILURE_DATA_NO_CHANGE'),0);
				}else{
					alert('error', L('PROMOTE_FAILURE_DATA_NO_CHANGE'),$_SERVER['HTTP_REFERER']);
				}
			}
		}elseif($this->isGet()){
			$business_id = intval($_GET['id']);
			if($business_id > 0){
				$m_business_status = M('BusinessStatus');
				$business_info = M('Business')->where('business_id = %d', $business_id)->field('status_id,status_type_id,possibility')->find();
				$order_id = $m_business_status->where(array('status_id'=>$business_info['status_id'],'type_id'=>$business_info['status_type_id']))->getField('order_id');
				if(!$order_id) {
					$order_id = 0;
				}
				$statusList = $m_business_status->where(array('order_id'=>array('egt',$order_id),'type_id'=>$business_info['status_type_id'],'is_end'=>array('neq',2)))->order('order_id')->select();
				//项目失败id
				$fail_status_id = $m_business_status->where(array('type_id'=>$business_info['status_type_id'],'is_end'=>2))->getField('status_id');
				$this->fail_status_id = $fail_status_id;
				$this->statusList = $statusList;
				$this->business_id = $business_id;
				$this->possibility = $business_info['possibility'];
				$this->display();
			}else{
				alert('error',  L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
			}
		}
	}

	/**
	*商机统计
	*
	**/
	public function analytics(){
		$m_business = M('Business');
		$m_contract = M('Contract');
		$m_business_status = M('BusinessStatus');
		$m_user = M('User');

		$content_id = $_GET['content_id'] ? intval($_GET['content_id']) : 1;
		if($_GET['dbname'] && $content_id == 2){
			$status_type_id = $_GET['status_type_id'] ? intval($_GET['status_type_id']) : 1;
			$this->type_list = M('BusinessType')->select();
			//商机漏斗对比
			$statusList = M('BusinessStatus')->order('order_id desc')->where(array('type_id'=>$status_type_id,'is_end'=>array('neq',2)))->select();
			if(is_array($_GET['dbname'])){
				$dbname = $_GET['dbname'];
				$this->dbname = implode(',',$_GET['dbname']);
			}else{
				$dbname = explode(',', $_GET['dbname']);
				$this->dbname = $_GET['dbname'];
			}
			foreach($dbname as $k=>$v){
				$user = getUserByRoleId($v);
				$where_status['owner_role_id'] = $v;
				if($_GET['select_type'] == 1){
					$start=strtotime(date('Y-m-01 00:00:00'));
					$end = strtotime(date('Y-m-d H:i:s'));
					$where_status['create_time'] = array('between',array($start,$end));
				}elseif($_GET['select_type'] == 2){
					$month=date('m');
					if($month==1 || $month==2 ||$month==3){
						$start=strtotime(date('Y-01-01 00:00:00'));
						$end=strtotime(date("Y-03-31 23:59:59"));
					}elseif($month==4 || $month==5 ||$month==6){
						$start=strtotime(date('Y-04-01 00:00:00'));
						$end=strtotime(date("Y-06-30 23:59:59"));
					}elseif($month==7 || $month==8 ||$month==9){
						$start=strtotime(date('Y-07-01 00:00:00'));
						$end=strtotime(date("Y-09-30 23:59:59"));
					}else{
						$start=strtotime(date('Y-10-01 00:00:00'));
						$end=strtotime(date("Y-12-31 23:59:59"));
					}
					$where_status['create_time'] = array('between',array($start,$end));
				}elseif($_GET['select_type'] == 3){
					$year = strtotime(date('Y-01-01 00:00:00'));
					$where_status['create_time'] = array('egt',$year);
				}elseif($_GET['select_type'] == 4){
					if($_GET['start_time']) $start_time = strtotime(date('Y-m-d',strtotime($_GET['start_time'])));
					$end_time = $_GET['end_time'] ?  strtotime(date('Y-m-d 23:59:59',strtotime($_GET['end_time']))) : strtotime(date('Y-m-d 23:59:59',time()));
					if($start_time){
						$where_status['create_time'] = array(array('lt',$end_time),array('gt',$start_time), 'and');
					}else{
						$where_status['create_time'] = array('lt',$end_time);
					}
				}
				if($_GET['select_type'] < 3){
					$this->start_date = date('Y-m-d',$start);
					$this->end_date = date('Y-m-d',$end);
				}else{
					if($_GET['select_type'] == 3){
						$this->start_date = date('Y-m-d',$year);
						$this->end_date = date('Y-m-d',time());
					}elseif($_GET['select_type'] == 4){
						$this->start_date = date('Y-m-d',$start_time);
						$this->end_date = date('Y-m-d',$end_time);
					}
				}
				
				$this->select_type = $_GET['select_type'];

				//商机阶段统计图
				$status_count_array = array();
				$where_status['is_deleted'] = 0;
				//总的商机数量
				$target_count_total = 0;
				$where_status['status_id'] = array('not in',array('0','99'));
				$target_count_total = $m_business ->where($where_status)->count();
				$status_count = '';
				foreach($statusList as $val){
					unset($where_status['status_id']);
					$where_status['status_id'] = $val['status_id'];
					$target_count = $m_business->where($where_status)->count();
					$status_count_array[] = '['.'"'.$val['name'].'",'.$target_count.']';
				}
				$status_count = implode(',', array_reverse($status_count_array));
				$duibi_array[$k]['status_count_array'] = $status_count;
				$duibi_array[$k]['user'] = $user;
			}
			$this->duibi_array = $duibi_array;
		}else{
			// 默认权限
			$below_ids = getPerByAction(MODULE_NAME,ACTION_NAME);

			// 是否仅查询销售岗【sale只查销售、all全部】、roleIdRange() 方法已处理包含部门或员工的搜索情况
			$d_search = D('Search');
			$range = $_GET['user_range'] = trim($_GET['user_range']) ?: 'all';
			$role_id_array = $d_search->roleIdRange($below_ids, $range);
			$where['owner_role_id'] = array('in',$role_id_array ?: '');

			//时间段搜索
			if($_GET['between_date']){
				$between_date = explode(' - ',trim($_GET['between_date']));
				$start_time = strtotime($between_date[0]);
				$end_time = strtotime($between_date[1]);
			}else{
				$start_time = strtotime(date('Y-m-01 00:00:00'));
				$end_time = time();
			}
			$where['create_time'] = array('between', array($start_time, $end_time));
			$this->start_date = date('Y-m-d',$start_time);
			$this->end_date = date('Y-m-d',$end_time);

			$where_source['creator_role_id'] = array('in', implode(',', $role_id_array));
			$where_status['owner_role_id'] = array('in', implode(',', $role_id_array));
			$where_money['owner_role_id'] = array('in', implode(',', $role_id_array));
			$where_day_create['creator_role_id'] = array('in', implode(',', $role_id_array));
			$where_day_success['owner_role_id'] = array('in', implode(',', $role_id_array));

			if($start_time){
				$where_source['create_time'] = array(array('lt',$end_time),array('gt',$start_time), 'and');
				$where_status['create_time'] = array(array('lt',$end_time),array('gt',$start_time), 'and');
				$where_money['create_time'] = array(array('lt',$end_time),array('gt',$start_time), 'and');
				$create_time= array(array('lt',$end_time),array('gt',$start_time), 'and');
			}else{
				$where_source['create_time'] = array('lt',$end_time);
				$where_status['create_time'] = array('lt',$end_time);
				$where_money['create_time'] = array('lt',$end_time);
				$create_time = array('lt',$end_time);
			}

			$d_business_customer = D('BusinessCustomer');
			$m_contract = M('Contract');
			$d_receivables_contract = D('ReceivablesContract');
			$m_receivingorder = M('Receivingorder');

			if($content_id == 1){
				$own_count_total = 0;
				$follow_count_total = 0;
				$success_count_total = 0;
				$deal_count_total = 0;
				$business_rate_total = 0;
				$contract_price_total = 0;
				$contract_average_total = 0;
				$receivingorder_price_total = 0;
				$un_receivingorder_price_total = 0;
				$receivingorder_rate_total = 0;
				$contract_count_total = 0;

				//跟进中状态ID
				$follow_status_ids = $m_business_status->where(array('is_end'=>array('lt',2)))->getField('status_id',true);
				$this->follow_status_ids = implode(',', $follow_status_ids);
				//项目成功状态ID
				$success_status_ids = $m_business_status->where(array('is_end'=>array('eq',3)))->getField('status_id',true);
				$this->success_status_ids = implode(',', $success_status_ids);
				// 失败ID
				$fail_status_ids = $m_business_status->where(array('is_end'=>array('eq',2)))->getField('status_id',true);
				$this->fail_status_ids = implode(',', $fail_status_ids);

				foreach($role_id_array as $v){
					$user = getUserByRoleId($v);
					//过滤已停用用户
					if($user['status'] == 1){
						$add_count = 0;
						$add_count = $m_business->where(array('is_deleted'=>0, 'creator_role_id'=>$v, 'create_time'=>$create_time))->count();
						// 作废 商机数（商机负责人为所属客户的负责人）
						// ！！ 还是按商机负责人统计
						$own_business_ids = $d_business_customer->where(array('business.owner_role_id'=>$v, 'create_time'=>$create_time ))->getField('business_id',true);
						$own_count = $own_business_ids ? count($own_business_ids) : '0';
						
						//跟进中
						$follow_count = 0;
						$follow_count = $m_business->where(array('business_id'=>array('in',$own_business_ids),'status_id'=>array('in',$follow_status_ids)))->count();
						//已成交
						$success_count = 0;
						$success_count = $m_business->where(array('business_id'=>array('in',$own_business_ids),'status_id'=>array('in',$success_status_ids)))->count();
						//已失败
						$deal_count = (int) $m_business->where(array('business_id'=>array('in',$own_business_ids),'status_id'=>array('in',$fail_status_ids)))->count();
						//商机赢单率
						$business_rate = round($success_count/$own_count,2)*100;
						//商机成交金额
						$contract_price = '0';
						$contract_price = $m_contract->where(array('business_id'=>array('in',$own_business_ids),'is_checked'=>1,'create_time'=>$create_time))->sum('price');
						$contract_price = round($contract_price,2);

						//平均商机金额
						$contract_count = $m_contract->where(array('business_id'=>array('in',$own_business_ids),'is_checked'=>1,'create_time'=>$create_time))->count();
						$contract_average = $contract_price ? round($contract_price/$contract_count,0) : '0';

						//回款金额
						$receivables_ids = $d_receivables_contract->where(array('contract.business_id'=>array('in',$own_business_ids)))->getField('receivables_id',true);
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

						$reportList[] = array(
							"user" => $user,
							"add_count" => $add_count,
							"own_count" => $own_count,
							"follow_count" => $follow_count,
							"success_count" => $success_count,
							"deal_count" => $deal_count,
							"business_rate" => $business_rate,
							"contract_price" => $contract_price,
							"contract_average" => $contract_average,
							"receivingorder_price" => $receivingorder_price,
							"un_receivingorder_price" => $un_receivingorder_price,
							"receivingorder_rate" => $receivingorder_rate
						);

						$own_count_total += $own_count;
						$follow_count_total += $follow_count;
						$success_count_total += $success_count;
						$deal_count_total += $deal_count;
						$contract_price_total += $contract_price;
						$contract_count_total += $contract_count;
						$receivingorder_price_total += $receivingorder_price;
						$un_receivingorder_price_total += $un_receivingorder_price;
					}
				}
				//总商机赢单率
				$business_rate_total = round($success_count_total/$own_count_total,2)*100;
				//合同平均金额
				$contract_average_total = $contract_price_total ? round($contract_price_total/$contract_count_total,0) : '0';
				//总回款比例
				$receivingorder_rate_total = $receivingorder_price_total ? round($receivingorder_price_total/$contract_price_total,2)*100 : '0';

				$this->total_report = array("own_count_total"=>$own_count_total, "follow_count_total"=>$follow_count_total,"success_count_total"=>$success_count_total,"deal_count_total"=>$deal_count_total,"business_rate_total"=>$business_rate_total,"contract_price_total"=>$contract_price_total,"contract_average_total"=>$contract_average_total,"receivingorder_price_total"=>$receivingorder_price_total,"un_receivingorder_price_total"=>$un_receivingorder_price_total,"receivingorder_rate_total"=>$receivingorder_rate_total);

				$this->reportList = $reportList;
			}

			//商机阶段统计图
			if($content_id == 2){
				$where['is_deleted'] = 0;

				// 商机某个状态组下的【商机阶段】列表
				$status_type_id = intval($_GET['status_type_id']) ?: 1; // 商机状态组ID
				$status_list = $m_business_status->where('type_id = %d', $status_type_id)->field('status_id,name')->order('order_id asc')->select();
				foreach ($status_list as $k => $v) {
					$where['status_id'] = $v['status_id'];

					$status_info = $m_business->field('count(*) as count,sum(final_price) as sum_money')->where($where)->find();			
					$status_list[$k]['count'] = $status_info['count'];
					$status_list[$k]['sum_money'] = $status_info['sum_money'];

					$funnel_data[] = array($v['name'], (int) $status_info['count']);

					// 商机数量合计
					$total_count += $status_info['count'];
					// 项目失败的数量，即status_id = 99
					if ($v['status_id'] == 99) {
						$fail_count = $status_info['count'];
					}
				}

				// 计算转化率【大于当前阶段的数量之和/大于等于当前阶段的数量之和，项目失败数量的不在计算在内】
				foreach ($status_list as $k => $v) {
					$past_count += $v['count']; // 小于等于当前阶段的商机数之和
					if ($v['status_id'] != 99 && $v['status_id'] != 100) {
						// 失败和成功阶段，都不计算
						$status_list[$k]['conversion_rate'] = (round(($total_count - $past_count - $fail_count) / ($total_count - $past_count - $fail_count + $v['count']), 4) * 100).'%';
					} else {
						$status_list[$k]['conversion_rate'] = '-';
					}
				}
// p($status_list);
				$this->status_list = $status_list;
				$total_info['total_count'] = $total_count; // 商机总数
				$total_info['total_money'] = number_format(array_sum(array_map(create_function('$val', 'return $val["sum_money"];'), $status_list)), 2); // 商机总金额
				$this->total_info = $total_info;

				// 图表js字符串
				$this->funnel_data_js = json_encode($funnel_data);

				// 商机状态组
				$this->type_list = M('BusinessType')->select();
			}

			$idArray = getPerByAction(MODULE_NAME,ACTION_NAME,false);
			$this->roleList = getUserByRoleIdArray($idArray);
			$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
			$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
			if($per_type == 2 || session('?admin')){
				$departmentList = M('roleDepartment')->select();
			}else{
				$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
			}
			$this->assign('departmentList', $departmentList);
		}

		$this->type_id = intval($_GET['type_id']);
		$this->content_id = intval($_GET['content_id']);
		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 商机数与商机赢单数趋势分析
	 * 注：商机数按create_time统计，赢单数按update_time统计,且商机状态是【成功】
	 * @author lee
	 */
	public function trend_number()
	{
		$m_business = M('Business');
		$m_business_status = M('BusinessStatus');
		$d_search = D('Search');

		// 权限判断【共用analytics方法权限，需要单独判断】
		if (!checkPerByAction('business', 'analytics')) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}
		// 默认权限范围
		$below_ids = getPerByAction('business', 'analytics');

		$where_create = array();

		// 成功的商机状态ID集合【统计赢单数】
		$success_status_ids = $m_business_status->where(array('is_end'=>3))->getField('status_id', true);
		$where_success['status_id'] = array('in', $success_status_ids);

		// 是否仅查询销售岗【sale只查销售、all全部】、roleIdRange() 方法已处理包含部门或员工的搜索情况
		$range = $_GET['user_range'] = trim($_GET['user_range']) ?: 'all';
		$where['owner_role_id'] = array('in', $d_search->roleIdRange($below_ids, $range) ?: '');

		// 商机状态组ID
		if ($_GET['status_type_id']) {
			$where['status_type_id'] = intval($_GET['status_type_id']);
		}

		$series_data[0]['name'] = '商机新增数';  
		$series_data[1]['name'] = '赢单数';  

		// 年份,默认当前年份
		$year = $_GET['year'] = $_GET['year'] ?: date('Y');
		if ($_GET['month']) {
			// 按月统计
			$mohth_days = date('t', strtotime($year.'-'.$_GET['month'])); // 获取某个月份的天数
			$day_list = range(1, $mohth_days);
			foreach ($day_list as $k => $v) {
				$start_time = strtotime("{$year}-{$_GET['month']}-{$v}"); // 某个月的某天起始时间戳
				$end_time = strtotime("+1 day", $start_time); // 某一天的下一天的起始时间戳
				// 跳过大于当前时间的搜索
				if ($start_time > time()) {
					continue;
				}
				// 商机新增数
				$where_create['create_time'] = array('between', array($start_time, $end_time));
				$where_create += $where;
				$total_create_count += $series_data[0]['data'][] = $create_count = (int) $m_business->where($where_create)->count();
				// 商机赢单数
				$where_success['update_time'] = array('between', array($start_time, $end_time));
				$where_success += $where;
				$total_success_count += $series_data[1]['data'][] = $success_count = (int) $m_business->where($where_success)->count();
				// 计算赢单率
				$success_rate_list[] = (round($success_count / $create_count, 4) * 100)."%";
				$js_categories[] = "{$v}号";
			}
		} else {
			// 按年统计
			$month_list = range(1, 12);
			foreach ($month_list as $k => $v) {
				$start_time = strtotime("{$year}-{$v}-01"); // 某个月起始时间戳
				$end_time = strtotime("+1 month", $start_time); // 某个的下个月起始时间戳
				// 跳过大于当前时间的搜索
				if ($start_time > time()) {
					continue;
				}
				// 商机新增数
				$where_create['create_time'] = array('between', array($start_time, $end_time));
				$where_create += $where;
				$total_create_count += $series_data[0]['data'][] = $create_count = (int) $m_business->where($where_create)->count();
				// 商机赢单数
				$where_success['update_time'] = array('between', array($start_time, $end_time));
				$where_success += $where;
				$total_success_count += $series_data[1]['data'][] = $success_count = (int) $m_business->where($where_success)->count();
				// 计算赢单率
				$success_rate_list[] = (round($success_count / $create_count, 4) * 100)."%";
				$js_categories[] = "{$v}月份";
			}
		}
// p($series_data,'');
// p($js_categories);
		// 表格内容
		$tab_info['date_list'] = $js_categories;
		$tab_info['create_list'] = $series_data[0]['data'];
		$tab_info['success_list'] = $series_data[1]['data'];
		$tab_info['success_rate_list'] = $success_rate_list;
		$tab_info['total_info']['total_create_count'] = $total_create_count;
		$tab_info['total_info']['total_success_count'] = $total_success_count;
		$tab_info['total_info']['total_success_rate'] = (round($total_success_count / $total_create_count, 4) * 100)."%";
		$this->assign('tab_info', $tab_info);
// p($tab_info);
		$this->assign('js_series', json_encode($series_data));
		$this->assign('js_categories', json_encode($js_categories));

		// 部门和员工搜索
		$idArray = getPerByAction(MODULE_NAME,ACTION_NAME,false);
		$this->roleList = getUserByRoleIdArray($idArray);
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);

		// 商机状态组
		$this->type_list = M('BusinessType')->select();

		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 商机金额趋势分析【按create_time统计】
	 * @author lee
	 */
	public function trend_money()
	{
		$m_business = M('Business');
		$d_search = D('Search');

		// 权限判断【共用analytics方法权限，需要单独判断】
		if (!checkPerByAction('business', 'analytics')) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}
		// 默认权限范围
		$below_ids = getPerByAction('business', 'analytics');

		// 是否仅查询销售岗【sale只查销售、all全部】、roleIdRange() 方法已处理包含部门或员工的搜索情况
		$range = $_GET['user_range'] = trim($_GET['user_range']) ?: 'all';
		$where['owner_role_id'] = array('in', $d_search->roleIdRange($below_ids, $range) ?: '');

		// 商机状态组ID
		if ($_GET['status_type_id']) {
			$where['status_type_id'] = intval($_GET['status_type_id']);
		}

		// 年份,默认当前年份
		$year = $_GET['year'] = $_GET['year'] ?: date('Y');

		if ($_GET['month']) {
			// 按月统计
			$mohth_days = date('t', strtotime($year.'-'.$_GET['month'])); // 获取某个月份的天数
			$day_list = range(1, $mohth_days);
			foreach ($day_list as $k => $v) {
				$start_time = strtotime("{$year}-{$_GET['month']}-{$v}"); // 某个月的某天起始时间戳
				$end_time = strtotime("+1 day", $start_time); // 某一天的下一天的起始时间戳
				// 跳过大于当前时间的搜索
				if ($start_time > time()) {
					continue;
				}
				$where['create_time'] = array('between', array($start_time, $end_time));
				$sum_money = (float) $m_business->where($where)->sum('final_price') ?: 0;

				$js_data[] = $sum_money;
				$js_categories[] = "{$v}号";
			}
			$this->assign('js_name', '按天分析');
		} else {
			// 按年统计
			$month_list = range(1, 12);
			foreach ($month_list as $k => $v) {
				$start_time = strtotime("{$year}-{$v}-01"); // 某个月起始时间戳
				$end_time = strtotime("+1 month", $start_time); // 某个的下个月起始时间戳
				// 跳过大于当前时间的搜索
				if ($start_time > time()) {
					continue;
				}
				$where['create_time'] = array('between', array($start_time, $end_time));
				$sum_money = (float) $m_business->where($where)->sum('final_price') ?: 0;

				$js_data[] = $sum_money;
				$js_categories[] = "{$v}月份";
			}
			$this->assign('js_name', '按月分析');
		}

		$this->assign('js_categories', json_encode($js_categories));
		$this->assign('js_data', json_encode($js_data));

		// 部门和员工搜索
		$idArray = getPerByAction(MODULE_NAME,ACTION_NAME,false);
		$this->roleList = getUserByRoleIdArray($idArray);
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);

		// 商机状态组
		$this->type_list = M('BusinessType')->select();

		$this->alert = parseAlert();
		$this->display();
	}

	/**
	 * 商机区域分析【根据关联客户的地址】
	 * @author lee
	 */
	public function map_number()
	{
		$m_business = M('Business');
		$d_search = D('Search');

		// 权限判断【共用analytics方法权限，需要单独判断】
		if (!checkPerByAction('business', 'analytics')) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}
		// 默认权限范围
		$below_ids = getPerByAction('business', 'analytics');

		// 是否仅查询销售岗【sale只查销售、all全部】、roleIdRange() 方法已处理包含部门或员工的搜索情况
		$range = $_GET['user_range'] = trim($_GET['user_range']) ?: 'all';
		$role_ids = implode(',', $d_search->roleIdRange($below_ids, $range)) ?: 0;
		$where = "(business.owner_role_id IN ({$role_ids}))";

		// 年份，默认当前年份
		$year = $_GET['year'] = $_GET['year'] ?: date('Y');
		$start_time = strtotime("{$year}-1-1 0:0:0");
		$end_time = strtotime("+1 year", $start_time);
		$where .= "AND (business.create_time BETWEEN {$start_time} AND {$end_time})";

		// 商机状态组ID
		if ($_GET['status_type_id']) {
			$where .= "AND (business.status_type_id = {$_GET['status_type_id']})";
		}

		// 按商机关联客户的地址分组统计【地址不规律，用mysql截取字符串做为新的省份名称再分组】
		$pre = C('DB_PREFIX'); // 获取表前缀
		$sql = "SELECT 
					count(*) as count, SUBSTR(customer.address FROM 1 FOR 2) as province
				FROM 
				 	{$pre}business business LEFT JOIN {$pre}customer customer ON business.customer_id = customer.customer_id 
				WHERE 
					{$where} 
				GROUP BY 
					customer.address";
		$list = M()->query($sql);

		$province_list =  mapProvince();
		foreach ($list as $k => $v) {
			foreach ($province_list as $key => $val) {
				if ($val == $v['province']) {
					$js_data[$key] = array('hc-key' => $key, 'value' => (int) $v['count']);
					continue;
				} else if (!isset($js_data[$key])){
					$js_data[$key] = array('hc-key' => $key, 'value' => 0);
					continue;
				}
			}
		}

		$js_data = json_encode(array_values($js_data));
		$this->assign('js_data', $js_data);
		
		// 部门和员工搜索
		$idArray = getPerByAction(MODULE_NAME,ACTION_NAME,false);
		$this->roleList = getUserByRoleIdArray($idArray);
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);

		// 商机状态组
		$this->type_list = M('BusinessType')->select();

		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 首页销售漏斗统计
	 **/
	public function getSalesFunnel(){
		$dashboard = M('user')->where('user_id = %d', session('user_id'))->getField('dashboard');
		$widget = unserialize($dashboard);
		$id = intval($_GET['id']);
		$status_type_id = 1;
		foreach($widget['dashboard'] as $k=>$v){
			if($v['widget'] == 'Salesfunnel' && $v['id'] == $id){
				if($v['level'] == '1'){
					$where['owner_role_id'] = array('in',getSubRoleId());
				}else{
					$where['owner_role_id'] = array('eq', session('role_id'));
				}
				$status_type_id = $v['status_type_id'] ? $v['status_type_id'] : 1;
			}
		}
		$fail_status_id = M('BusinessStatus')->where(array('is_end'=>array('eq',2)))->getField('status_id',true);
		$m_business = M('Business');
		$status_count_array = array();
		$status = M('BusinessStatus')->order('order_id desc')->where(array('type_id'=>$status_type_id,'status_id'=>array('not in',$fail_status_id)))->order('order_id asc')->getField('status_id,name',true);
		$statusList = array();
		$where['is_deleted'] = array('eq',0);
		foreach($status as $k=>$v){
			$where['status_id'] = array('eq',$k);
			$status_count = $m_business ->where($where)->count();
			$statusList[] = array($v, intval($status_count));
		}
		$this->ajaxReturn($statusList,'success',1);
	}
	
	//销售漏斗对比
	public function addduibi(){
		if($_GET['dbname']){
			$dbname = explode(',',$_GET['dbname']);
		}
		$this->dbname = $dbname;
		$this->dbname_count = count($dbname);

		$idArray = getPerByAction(MODULE_NAME,ACTION_NAME,false);
		//$idArray = getSubRoleId(true, 1);
		$roleList = array();
		foreach($idArray as $roleId){
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->dbroleList = $roleList;
		$this->display();
	}

	//商机统计高级搜索
	public function advance_search(){
		$module_name = trim($_GET['module_name']);
		$action_name = trim($_GET['action_name']);
		$idArray = getPerByAction($module_name,$action_name,false);
		//$idArray = getSubRoleId(true, 1);
		$roleList = array();
		foreach($idArray as $roleId){
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->roleList = $roleList;
		$url = getCheckUrlByAction($module_name,$action_name);
		$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);
		$this->type_id = intval($_GET['type_id']);
		$this->content_id = intval($_GET['content_id']);
		$this->display();
	}

	/**
	 * 获取商机状态
	 * @param 
	 * @author 
	 * @return 
	 */
	public function getbusinessStatus(){
		$type_id = $_GET['type_id'] ? intval($_GET['type_id']) : 0;
		if (!$type_id) {
			$this->ajaxReturn('','参数错误！',0);
		}
		$status_list = M('BusinessStatus')->where(array('type_id'=>$type_id))->order('order_id asc')->select();
		$this->ajaxReturn($status_list,'',1);
	}
	

	/**
	 *	获取当前用户的客户
	 *	
	 */	
	public function get_business_customer()
	{
		$role_id_array = getSubRoleId();
		$d_v_business = D('BusinessTopView');
		$customer_id_array = $d_v_business->where('business.creator_role_id in (%s)', implode(',', $role_id_array))->field('business.customer_id as customer_id')->group('business.customer_id')->select();
		$where = '';
		foreach ($customer_id_array as $key => $val) {
			$where .= $val['customer_id'].',';
		}
		$where = trim($where, ',');
		$customer = M('customer')->field('customer_id,name')->where('customer_id in (%s)', $where)->select();
		$this->ajaxReturn($customer, '', 1);
	}

	public function get_business_contacts()
	{
		$role_id_array = getSubRoleId();
		$d_v_business = D('BusinessTopView');
		$customer_id_array = $d_v_business->where('business.creator_role_id in (%s)', implode(',', $role_id_array))->field('business.customer_id as customer_id')->group('business.customer_id')->select();
		$where = '';
		foreach ($customer_id_array as $key => $val) {
			$where .= $val['customer_id'].',';
		}
		$where = trim($where, ',');
		$customer = M('customer')->field('customer_id,contacts_id')->where('customer_id in (%s)', $where)->select();
		$contacts_array = array();
		foreach ($customer as $key => $val) {
			if ($val['contacts_id']) {
				$contacts = M('contacts')->where('contacts_id = '.$val['contacts_id'])->field('contacts_id,name')->find();
			} else {
				$contacts_id = M('r_contacts_customer')->where('customer_id=%d', $val['customer_id'])->getField('contacts_id');
				$contacts = M('contacts')->where('contacts_id = '.$contacts_id)->field('contacts_id,name')->find();
			}
			if ($contacts) {
				$contacts_array[] = $contacts;
			}
		}
		$this->ajaxReturn($contacts_array, '', 1);
	}
}