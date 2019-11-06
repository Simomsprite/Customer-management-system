<?php
/**
 *消息相关
 **/
class MessageVue extends Action{
	/**
	 *用于判断权限
	 *@permission 无限制
	 *@allow 登录用户可访问
	 *@other 其他根据系统设置
	 **/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index','roleList','send', 'tips')
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
	 * 获取负责人列表（审批等）
	 * @param 
	 * @author 
	 * @return 
	 */
	public function roleList() {
		$d_role = D('RoleView');
		$type = $_GET['type'] ? trim($_GET['type']) : '';
		if ($type == 'all') {
			$list = $d_role->where(array('status'=>1))->field('user_name,department_name,role_name,role_id,img')->select();
			$this->ajaxReturn($list,'success',1);
		} elseif($type == 'department') {
			$departments_list = M('roleDepartment')->field('department_id,name')->select();
			$list = array();
			if ($departments_list) {
				foreach ($departments_list as $v) {
					$tmp['department_name'] = $v['name'];
					$roleList = $d_role->where('position.department_id = %d and status = 1', $v['department_id'])->field('user_name,department_name,role_name,role_id,img')->select();
					$tmp['list'] = $roleList ? $roleList : array();
					$list[] = $tmp;
				}
			}
		} elseif ($type == 'examine') {
			//返回有审批权限的列表
			$position_ids = M('Permission')->where("url = 'examine/add_examine'")->getField('position_id',true);
			$role_ids_array = $d_role->where('role.position_id in(%s)', implode(',', $position_ids))->getField('role_id', true);
			$user_ids_array =  $d_role->where('user.status = 1 and user.category_id = 1')->getField('role_id',true);
			if ($role_ids_array) {
				$role_array = array_merge($role_ids_array,$user_ids_array);
				$role_array = array_unique($role_array);
			} else {
				$role_array = $user_ids_array;
			}
			foreach ($role_array as $k=>$v) {
				$user_info = $d_role->where('role.role_id = %d and status = 1',$v)->field('user_name,role_id,img')->find();
				if($user_info){
					$list[] = $user_info;
				}
			}
			if (empty($list)) {
				$list = array();
			} else {
				foreach ($list as $k=>$v) {
					$list[$k]['img'] = empty($v['img']) ? '' : $v['img'];
					//$user_list[$k]['k'] = $py->getFirstPY($v['user_name']); //传入名称 返回汉字首字母
				}
			}
		}
		$this->ajaxReturn($list,'success',1);
	}

	/**
	 * 消息列表
	 * @param by = dynamic 动态首页,index 系统消息,remind 提醒
	 * @author 
	 * @return 
	 */
	public function index() {
		if ($this->isPost()) {
			$m_r_message = D('MessageReceiveView');
			$m_s_message = D('MessageSendView');
			$m_message = M('Message');
			$m_user = M('User');
			$p = isset($_POST['p']) ? intval($_POST['p']) : 1 ;
			$by = $_POST['by'] ? trim($_POST['by']) : 'index';

			if ($by == 'dynamic') {
				//未读消息（并查询最新一条信息）
				$r_where = array();
				$r_where['to_role_id'] = session('role_id');
				$r_where['message.status'] = array('neq', 1);
				$r_where['from_role_id'] = 0;
				$new_receive_info = $m_r_message->where($r_where)->order('send_time desc,read_time asc')->find();
				$new_receive_info = str_replace('&nbsp;', '', $new_receive_info);
				$r_where['read_time'] = 0; 
				$receive_noread_num = $m_r_message->where($r_where)->count();

				//未读公告（并查询最新一条信息）
				$m_announcement = M('Announcement');
				$m_announcement_data = M('AnnouncementData');
				$a_where = array();
				$a_where['department'] = array('like', '%('.session('department_id').')%');
				$a_where['status'] = array('eq', 1);
				//公告列表权限判断
				// $a_where['role_id'] = array('in', getPerByAction('announcement','index'));
				$new_announcement_info = $m_announcement->where($a_where)->order('create_time desc')->find();
				//公告未读数
				$announcement_ids = $m_announcement->where($a_where)->getField('announcement_id',true);
				$announcement_read_num = M('AnnouncementData')->where(array('announcement_id'=>array('in',$announcement_ids),'role_id'=>session('role_id')))->count();
				$announcement_noread_num = $announcement_ids ? count($announcement_ids)-$announcement_read_num : 0;

				//提醒
				$m_remind = M('Remind');
				$m_business = M('Business');
				$m_customer = M('Customer');
				$remind_info = $m_remind->where(array('create_role_id'=>session('role_id'),'remind_time'=>array('lt',time())))->order('remind_id desc')->find();
				$remind_noread_num = $m_remind->where(array('create_role_id'=>session('role_id'),'remind_time'=>array('eq','')))->count();

				//消息人员列表
				// $m_message = M('message');
				// $to_role_ids = $m_message ->where('from_role_id =%d and to_role_id !=%d and status = 0',session('role_id'),session('role_id'))->order('message_id desc')->field('to_role_id,send_time')->select();
				// $from_role_ids = $m_message ->where('to_role_id =%d and from_role_id >0 and from_role_id !=%d and status = 0',session('role_id'),session('role_id'))->order('message_id desc')->field('from_role_id,send_time')->select();
				// if ($to_role_ids && $from_role_ids) {
				// 	$owner_role_id_array = array_merge($to_role_ids, $from_role_ids);//合并数组
				// } else {
				// 	if ($to_role_ids) {
				// 		$owner_role_id_array = $to_role_ids;
				// 	} elseif ($from_role_ids) {
				// 		$owner_role_id_array = $from_role_ids;
				// 	} else {
				// 		$owner_role_id_array = array();
				// 	}
				// }
				// $owner_role_id_array = sort_select($owner_role_id_array,'send_time',1);//排序
				// $message_role_id_array = array();
				// foreach ($owner_role_id_array as $v) {
				// 	if ($v['from_role_id']) {
				// 		$message_role_id_array[] = $v['from_role_id'];
				// 	}
				// 	if ($v['to_role_id']) {
				// 		$message_role_id_array[] = $v['to_role_id'];
				// 	}
				// }
				// if ($message_role_id_array) {
				// 	$message_role_id_array = array_unique($message_role_id_array);//去重
				// 	$message_role_id_array = array_values($message_role_id_array);//重置索引
				// 	// $message_session_role_id[] = session('role_id');
				// 	$user_list = array();
				// 	$m_user =  M('User');
				// 	$i = 0;
				// 	foreach ($message_role_id_array as $k=>$v) {
				// 		$user_info = array();
				// 		$user_info = $m_user->where('role_id = %d',$v)->field('name,thumb_path,role_id,full_name')->find();
				// 		if ($user_info) {
				// 			$user_list[$i]['user_info'] = $user_info ? $user_info : array();

				// 			$message_to_role_id = $m_message->where('from_role_id =%d and to_role_id =%d and status = 0',session('role_id'),$v)->max('message_id');
				// 			$message_from_role_id = $m_message->where('from_role_id =%d and to_role_id =%d and status = 0',$v,session('role_id'))->max('message_id');
				// 			if ($message_to_role_id >$message_from_role_id) {
				// 				$message_info = $m_message->where('message_id =%d',$message_to_role_id)->field('file_id,content,send_time')->find();
				// 			} else {
				// 				$message_info = $m_message->where('message_id =%d',$message_from_role_id)->field('file_id,content,send_time')->find();
				// 			}
				// 			if ($message_info['file_id']) {
				// 				$message_info['content'] = '文件已上传，请查收。';
				// 			} else {
				// 				$message_info['content'] = $message_info['content'] ? cutString($message_info['content'],15) : '';
				// 			}
				// 			$user_list[$i]['message'] = $message_info;
				// 			//未读消息数
				// 			$n_where['read_time'] = 0;
				// 			$n_where['to_role_id'] = session('role_id');
				// 			$n_where['from_role_id'] = $v;
				// 			$n_where['status'] = array('neq', 2);
				// 			$user_list[$i]['noread_count'] = $m_message->where($n_where)->count();
				// 			$i++;
				// 		}
				// 	}
				// }

				$message_data = array();
				$message_data['new_receive_info'] = $new_receive_info ? msubstr(strip_tags($new_receive_info['content']),0,15) : '';
				$message_data['receive_time'] = $new_receive_info['send_time'];
				$message_data['receive_noread_num'] = $receive_noread_num ? $receive_noread_num : '0';

				$message_data['new_announcement_info'] = $new_announcement_info ? msubstr($new_announcement_info['content'],0,15) : '';
				$message_data['announcement_noread_num'] = $announcement_noread_num ? $announcement_noread_num : '0';
				$message_data['announcement_time'] = $new_announcement_info['create_time'];

				$message_data['remind_info'] = $remind_info ? msubstr($remind_info['content'],0,15) : '';
				$message_data['remind_time'] = $remind_info['remind_time'];
				$message_data['remind_noread_num'] = $remind_noread_num ? $remind_noread_num : '0';

				$message_data['user_list'] = $user_list ? $user_list : array();

				// 待办事项
				$message_data['backlog'] = $this->tips(True);

				$data['data'] = $message_data;
				$data['info'] = 'success'; 
				$data['status'] = 1; 			
				$this->ajaxReturn($data,'JSON');
			} elseif ($by == 'index') {
				$r_where = array();
				$r_where['to_role_id'] = session('role_id');
				$r_where['message.status'] = array('neq', 1);
				$r_where['from_role_id'] = 0;
				//系统消息
				$receive_list = $m_r_message->where($r_where)->order('send_time desc,read_time asc')->page($p.',10')->field('content,send_time')->select();
				$receive_count = $m_r_message->where($r_where)->count();
				$page = ceil($receive_count/10);
				// foreach ($receive_list as $k=>$v) {
				// 	$pre_content = strip_tags($v['content']);
				// 	$pre_content_len = mb_strlen($pre_content,'utf-8');
				// 	if ($pre_content_len <= 25) {
				// 		$receive_list[$k]['pre_content'] = $pre_content;
				// 	} else {
				// 		$pre_content = mb_substr($pre_content,0,25,'utf-8');
				// 		$receive_list[$k]['pre_content'] = $pre_content.' . . .';
				// 	}
				// }
				//系统消息标记为已读
				$m_message->where(array('to_role_id'=>session('role_id'),'from_role_id'=>0,'read_time'=>0))->setField('read_time',time());

				$data['list'] = $receive_list ? $receive_list : array();
				$data['page'] = $page;
				$data['info'] = 'success'; 
				$data['status'] = 1; 			
				$this->ajaxReturn($data,'JSON');

			} elseif ($by == 'remind') {
				$m_remind = M('Remind');
				$m_business = M('Business');
				$m_customer = M('Customer');
				$remind_list = $m_remind->where(array('create_role_id'=>session('role_id'),'remind_time'=>array('lt',time())))->order('remind_id desc')->page($p,10)->select();
				$remind_count = $m_remind->where(array('create_role_id'=>session('role_id'),'remind_time'=>array('lt',time())))->count();
				$page = ceil($remind_count/10);
				foreach ($remind_list as $k=>$v) {
					$customer_id = '';
					if ($v['module'] == 'business') {
						$customer_id = $m_business->where(array('business_id'=>$v['module_id']))->getField('customer_id');
					} elseif ($v['module'] == 'customer') {
						$customer_id = $v['module_id'];
					}
					$customer_info = $m_customer->where(array('customer_id'=>$customer_id))->field('name,customer_id')->find();
					if ($customer_info) {
						$message_content = '';
						$message_content = '您有一个新的提醒需要跟进  相关客户【'.$customer_info['name'].'】,提醒内容：'.$v['content'];

						$remind_list[$k]['content'] = $message_content; 
					}
				}
				$data['list'] = $remind_list ? $remind_list : array();
				$data['page'] = $page;
				$data['info'] = 'success'; 
				$data['status'] = 1; 			
				$this->ajaxReturn($data,'JSON');
			}
		}
	}

	/**
	 * 发送站内信
	 * @param 
	 * @author 
	 * @return 
	 */
	public function send(){
		if($this->isPost()){
			$to_role = $_POST['to_role_id'];
			$m_user = M('User');
			if(is_array($to_role)){
				foreach($to_role as $k=>$v){
					sendMessage($v, trim($_POST['content']));
				}
				$this->ajaxReturn('','发送成功！',1);
			}else{
				if(sendMessage(intval($_POST['to_role_id']),trim($_POST['content']))){
					$this->ajaxReturn('','发送成功！',1);
				}else{
					$this->ajaxReturn('','发送失败，请重试！',0);
				}
			}
		}
	}


	/**
	 * 待办事项提醒
	 * @author lee
	 * @param	bool	$call	是否内部调用
	 */
	public function tips($call = false)
	{
		$new_num = array('todo_num' => 0);	// 待办事项总数

		// 今日需拜访客户
		$today_customer = 0;
		if (checkPerByAction('customer','index')) {
			$m_customer = M('Customer');
			$today_where = array();
			$today_where['owner_role_id'] = session('role_id');
			//过滤客户池条件
			$m_config = M('Config');
			$outdays = $m_config -> where('name="customer_outdays"')->getField('value');
			$outdate = empty($outdays) ? 0 : time()-86400*$outdays;
			$c_outdays = $m_config -> where('name="contract_outdays"')->getField('value');
			$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
			$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
			$openrecycle = $m_config -> where('name="openrecycle"')->getField('value');
			if($openrecycle == 2){
				$today_where['_string'] = '(update_time > '.$outdate.' AND get_time > '.$contract_outdays.') OR is_locked = 1';
			}
			$today_where['is_deleted'] = array('neq',1);
			$today_where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-d', time()))+86400), array('gt', strtotime(date('Y-m-d', time()))), 'and');
			$today_customer = (int) $m_customer->where($today_where)->count();
		}

		// 待审批的合同（数量以合同列表权限范围为准）
		$check_contract_num = 0;
		$contract_examine_list = M('ContractExamine')->select();
		foreach ($contract_examine_list as $k=>$v) {
			$role_arr = array();
			$role_arr = array_filter(explode(',',$v['role_id']));
			foreach ($role_arr as $key=>$val) {
				$contract_examine_role_ids[] = $val;
			}
		}
		if (checkPerByAction('contract','check') || in_array(session('role_id'),$contract_examine_role_ids)) {
			$where_check_contract = array();
			// $where_check_contract['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),array('eq',''),'or');
			$where_check_contract['owner_role_id'] = array('in', getPerByAction('contract','index'));
			$where_check_contract['is_checked'] = array('in', array('0','3'));
			$check_contract_num = (int) M('Contract')->where($where_check_contract)->count();
		}

		// 到期合同
		$contract_num = 0;
		if (checkPerByAction('contract', 'index')) {
			$m_contract = M('Contract');
			$days = C('defaultinfo.contract_alert_time') ? intval(C('defaultinfo.contract_alert_time')) : 30;
			$temp_time = time()+$days*86400;
			$contract_num = (int) $m_contract->where("owner_role_id = %d and is_checked = 1 and contract_status = 0 and is_deleted <> 1 and $temp_time >= end_date", session('role_id'))->count();
		}


		// 应收款提醒
		$receivables_num = 0;
		if (checkPerByAction('finance','index_receivables')) {
			$m_receivables = M('receivables');
			$receivables_time = M('config')->where('name="receivables_time"')->getField('value');
			$f_outdate = empty($receivables_time) ? 0 : time()-86400*$receivables_time;
			$r_where['pay_time'] = array('elt',time()+$f_outdate);
			$r_where['status'] = array('lt',2);
			$r_where['owner_role_id'] = session('role_id');
			$receivables_num = (int) $m_receivables ->where($r_where)->count();
		}
		
		// 待审批的办公审批（数量以审批列表权限范围为准）
		$examine_num = 0;
		if (checkPerByAction('examine','index')) {
			$where_examine = array();
			$where_examine['is_deleted'] = 0;
			// $where_examine['examine_role_id'] = session('role_id');
			$where_examine['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or');
			$where_examine['examine_status'] = array('lt',2);
			$examine_num = (int) M('Examine')->where($where_examine)->count();
		}
		
		//待确认的回款
		$receivingorder_num = 0;
		if (checkPerByAction('finance','check')) {
			$where_check_receivingorder = array();
			$where_check_receivingorder['owner_role_id'] = array('in',getPerByAction('finance','index_receivingorder'));
			$where_check_receivingorder['status'] = 0;
			$receivingorder_num = (int) M('Receivingorder')->where($where_check_receivingorder)->count();
		}

		$d_message = D('Message');

		// 待审核的发票
		$check_invoice_num = $d_message->checkInvoiceNum();
		if ($check_invoice_num !== false) {
			$new_num['todo_num'] += $new_num['check_invoice_num'] = $check_invoice_num;
		}
		
		// 进销存相关		
		if (C('PSS_STATUS')) {
			// 待审核的采购单
			$new_num['todo_num'] += $new_num['check_purchase_num'] = $d_message->checkPurchaseNum();

			// 待审核的采购退货
			$new_num['todo_num'] += $new_num['check_purchase_return_num'] = $d_message->checkPurchaseReturnNum();

			// 待审核的销售退货
			$new_num['todo_num'] += $new_num['check_sales_return_num'] = $d_message->checkSalesReturnNum();

			// 待审核的库存调拨
			$new_num['todo_num'] += $new_num['check_transfer_num'] = $d_message->checkTransferNum();
		}

		$new_num['check_contract_num'] = $check_contract_num;
		$new_num['examine_num'] = $examine_num;
		$new_num['receivingorder_num'] = $receivingorder_num;
		$new_num['today_customer'] = $today_customer;
		$new_num['contract_num'] = $contract_num;
		$new_num['receivables_num'] = $receivables_num;

		$new_num['todo_num'] += $check_contract_num + $examine_num + $receivingorder_num + $today_customer + $contract_num + $receivables_num;

		if ($call) {
			return $new_num['todo_num'];
		}
		$this->ajaxReturn(array('data' => $new_num, 'info' => '', 'status' => 1));
	}


}