<?PHP 
/**
*发票模块
*
**/
class InvoiceVue extends Action{
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('adddata','viewdata','editdata','customer_invoice','check_list','getcode','check_list')
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
	 * 发票列表
	 * @param 
	 * @author 
	 * @return 
	 */
	public function index(){
		if ($this->isPost()) {
			//获取权限
			$permission_list = apppermission('invoice','index');
			if ($permission_list) {
				$invoice_data['permission_list'] = $permission_list;
			} else {
				$invoice_data['permission_list'] = array();
			}
			$d_invoice = D('InvoiceView');
			$m_customer = M('Customer');
			$m_contract = M('Contract');
			$m_user = M('User');
			$where = array();
			//排序
			$order = "";
			if ($_POST['order_field'] && $_POST['order_type']) {
				$order = trim($_POST['order_field']).' '.trim($_POST['order_type']).',invoice.update_time desc';
			}
			$order = empty($order) ? 'invoice.update_time desc' : $order;
			$p = intval($_POST['p']) ? intval($_POST['p']) : 1;
			//普通搜索
			if ($_POST["field"]) {
				$field = trim($_POST['field']);
				$search = empty($_POST['search']) ? '' : trim($_POST['search']);
				$condition = empty($_POST['condition']) ? 'is' : trim($_POST['condition']);
				if($field == 'name'){
					$where['_string'] = 'invoice.name like "%'.$search.'%" or contract.number like "%'.$search.'%" or customer.name like "%'.$search.'%"';
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
				$params = array('field='.trim($_POST['field']), 'condition='.$condition, 'search='.$search);
				//过滤不在权限范围内的role_id
				if(trim($_POST['field']) == 'create_role_id'){
					if(!in_array(trim($search),$this->_permissionRes)){
						$where['create_role_id'] = array('in',$this->_permissionRes);
					}
				}
			}
			if (!isset($where['create_role_id'])) {
				$where['create_role_id'] = array('in',$this->_permissionRes);
			}

			// 待审核的发票搜索，如果写法和高级搜索的写法不同，特殊处理
			if (isset($_POST['is_checked']) && !is_array($_POST['is_checked'])) {
				$where['is_checked'] = intval($_GET['is_checked']);
			}

			//高级搜索
			if(!$_POST['field']){
				foreach($_POST as $kd => $vd){
	                if ($kd != 'act' && $kd != 'content' && $kd != 'p' && $kd != 'search' && $kd != 'listrows' && $kd != 'by' && $kd != 'token' && $kd != 'order_field' && $kd != 'order_type') {
						if(in_array($kd,array('is_checked'))){
							if(!empty($vd)){
								$where[$kd] = $vd['value'];
							}
						}elseif(in_array($kd,array('create_time','update_time'))){
							$where[$kd] = field($vd['value'], $vd['condition']);

							//时间段查询
							if ($vd['start'] && $vd['end']) {
								$where[$kd] = array('between',array(strtotime($vd['start']),strtotime($vd['end'])+86399));
							} elseif ($vd['start']) {
								$where[$kd] = array('egt',strtotime($vd['start']));
							} else {
								$where[$kd] = array('elt',strtotime($vd['end'])+86399);
							}
						}elseif($kd =='create_role_id'){
							if(!empty($vd)){
								$where['invoice.create_role_id'] = $vd['value'];
							}
						}else{
							if(is_array($vd)) {
								if(!empty($vd['value'])){
									$where[$kd] = field($vd['value'], $vd['condition']);
								}
							}else{
								if(!empty($vd)){
									$where[$kd] = field($vd);
								} 
							}
						}
	                }
					if($kd != 'search'){
						if(is_array($vd)){
							foreach ($vd as $key => $value) {
								$params[] = $kd . '[' . $key . ']=' . $value;
							}
						}else{
							$params[] = $kd . '=' . $vd;
						} 
					} 
	            }
	            //过滤不在权限范围内的role_id
				if(isset($where['invoice.create_role_id'])){
					if(!empty($where['invoice.create_role_id']) && !in_array(intval($where['invoice.create_role_id']),$this->_permissionRes)){
						$where['invoice.create_role_id'] = array('in',implode(',', $this->_permissionRes));
					}
				}
			}

			//获取查询条件信息
			if($p == 1 && $_POST['search'] == ''){
				$invoice_status_setting = array(
					'0'=>array('key'=>0,'value'=>'全部'),
					'1'=>array('key'=>3,'value'=>'待审'),
					'2'=>array('key'=>1,'value'=>'审批通过'),
					'3'=>array('key'=>2,'value'=>'审批拒绝'),
				);
				$field_list = array(
					'0'=>array('field'=>'name','form_type'=>'text','input_tips'=>'','name'=>'发票号码','setting'=>''),
					'1'=>array('field'=>'contract_name','form_type'=>'text','input_tips'=>'','name'=>'合同编号','setting'=>''),
					'2'=>array('field'=>'is_checked','form_type'=>'box','input_tips'=>'','name'=>'审核状态','setting'=>$invoice_status_setting),
					'3'=>array('field'=>'create_role_id','form_type'=>'user','input_tips'=>'','name'=>'开票人','setting'=>''),
					'4'=>array('field'=>'customer_name','form_type'=>'text','input_tips'=>'','name'=>'客户名称','setting'=>''),
					'5'=>array('field'=>'price','form_type'=>'text','input_tips'=>'','name'=>'开票金额','setting'=>''),
					'6'=>array('field'=>'create_time','form_type'=>'datetime','input_tips'=>'','name'=>'创建时间','setting'=>''),
					'7'=>array('field'=>'update_time','form_type'=>'datetime','input_tips'=>'','name'=>'修改时间','setting'=>'')
				);
				$invoice_data['fields_list'] = $field_list ? $field_list : array();
			}else{
				$invoice_data['fields_list'] = array();
			}
			$count = $d_invoice->where($where)->count();
			$page = ceil($count/10);
			$invoice_list = $d_invoice->where($where)->page($p.',10')->order($order)->field('invoice_id,name,customer_id,number,price,create_time,is_checked,create_role_id')->select();
			foreach ($invoice_list as $k=>$v) {
				$invoice_list[$k]['customer_name'] = $m_customer->where(array('customer_id'=>$v['customer_id']))->getField('name');
				switch ($v['is_checked']) {
					case 1 : $check_name = '通过'; break;
					case 2 : $check_name = '失败'; break;
					default : $check_name = '待审'; break;
				}
				$invoice_list[$k]['check_name'] = $check_name;
			}
			$invoice_data['list'] = $invoice_list ? : array();
			$invoice_data['page'] = $page;
			$invoice_data['info'] = 'success';
			$invoice_data['status'] = 1;
			$this->ajaxReturn($invoice_data,'JSON');
		}
	}

	/**
	 * 发票动态
	 * @param 
	 * @author 
	 * @return 
	 */
	public function dynamic () {
		if ($this->isPost()) {
			$invoice_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$invoice_info = D('InvoiceView')->where('invoice.invoice_id = %d', $invoice_id)->field('invoice_id,name,customer_id,customer_name,contract_id,contract_num,price,create_time,is_checked,contract_price')->find();
			if (!$invoice_info) {
				$this->ajaxReturn('','数据不存在或已删除！',2);
			}
			//开票进度（开票金额/总合同金额）
			$sum_invoice_price = M('Invoice')->where(array('contract_id'=>$invoice_info['contract_id'],'is_checked'=>array('neq',2)))->sum('price');
			$schedule = round($sum_invoice_price/$invoice_info['contract_price'],2)*100;
			$invoice_info['schedule'] = $schedule ? : '0';

			$permission_view = getPerByAction('invoice','view');
			$permission_edit = getPerByAction('invoice','edit');
			$permission_del = getPerByAction('invoice','delete');
			$view = 0;
			$edit = 0;
			$delete = 0;
			//详情、编辑、删除权限
			if (in_array($invoice_info['create_role_id'], $permission_view) || session('?admin')) {
				$view = 1;
			}
			if (in_array($invoice_info['create_role_id'], $permission_edit) || session('?admin')) {
				$edit = 1;
			}
			if (in_array($invoice_info['create_role_id'], $permission_del) || session('?admin')) {
				$delete = 1;
			}
			$data['permission']['view'] = $view;
			$data['permission']['edit'] = $edit;
			$data['permission']['delete'] = $delete;
			//判断审批权限
			if (checkPerByAction('invoice','check')) {
				$data['permission']['check'] = 1;
			} else {
				$data['permission']['check'] = 0;
			}

			$data['data'] = $invoice_info;
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		}
	}

	/**
	 * 发票添加
	 * @param 
	 * @author 
	 * @return 
	 */
	public function add () {
		if ($this->isPost()) {
			$m_invoice = M('Invoice');
			if ($m_invoice->where(array('name'=>trim($_POST['name'])))->find()) {
				$this->ajaxReturn('','发票编号已存在！',0);
			}
			if (!intval($_POST['contract_id'])) {
				$this->ajaxReturn('','合同不能为空！',0);
			}
			if (!intval($_POST['customer_id'])) {
				$this->ajaxReturn('','客户不能为空！',0);
			}
			if ($m_invoice->create()) {
				$m_invoice->create_role_id = session('role_id');
				$m_invoice->create_time = time();
				$m_invoice->update_time = time();
				$m_invoice->invoice_time = $_POST['invoice_time'];

				if ($invoice_id = $m_invoice->add()) {
					//相关附件
					if($_POST['file']){
						$m_invoice_file = M('RFileInvoice');
						foreach($_POST['file'] as $v){
							$file_data = array();
							$file_data['invoice_id'] = $invoice_id;
							$file_data['file_id'] = $v;
							$m_invoice_file->add($file_data);
						}
					}
					$this->ajaxReturn('','创建成功！',1);
				} else {
					$this->ajaxReturn('','创建失败，请重试！',0);
				}
			}
		}
	}

	/**
	 * 发票详情
	 * @param 
	 * @author 
	 * @return 
	 */
	public function view(){
		if ($this->isPost()) {
			$invoice_id = $_POST['id'] ? intval($_POST['id']) : 0;
			$d_user = D('User');
			if(!$invoice_id){
				$this->ajaxReturn('','参数错误！',0);
			}
			$m_invoice = M('Invoice');
			$m_customer = M('Customer');
			$m_contract = M('Contract');
			$m_r_file_invoice = M('RFileInvoice');
			$m_file = M('File');
			$m_user = M('User');
			$invoice_info = $m_invoice->where(array('invoice_id'=>$invoice_id))->find();
			//权限判断
			if(empty($invoice_info)) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			}elseif(!in_array($invoice_info['create_role_id'], $this->_permissionRes)) {
				$this->ajaxReturn('',L('DO NOT HAVE PRIVILEGES'),-2);
			}
			switch ($invoice_info['billing_type']) {
				case 1 : $billing_type_name = '增值税普通发票'; break;
				case 2 : $billing_type_name = '增值税专用发票'; break;
				case 3 : $billing_type_name = '收据'; break;
			}
			$invoice_info['billing_type_name'] = $billing_type_name;
			$invoice_info['customer_name'] = $m_customer->where(array('customer_id'=>$invoice_info['customer_id']))->getField('name');
			$invoice_info['contract_num'] = $m_contract->where(array('contract_id'=>$invoice_info['contract_id']))->getField('number');
			$temp_val = $d_user->get_full_name(array($invoice_info['create_role_id']));
			$invoice_info['create_info'] = $temp_val[$invoice_info['create_role_id']];
			if ($invoice_info['is_checked']) {
				$temp_val = $d_user->get_full_name(array($invoice_info['check_role_id']));
				$check_role_info['create_info'] = $temp_val[$invoice_info['check_role_id']];
			}
			$invoice_info['check_role_info'] = $check_role_info ? $check_role_info : array();

			//附件
			$file_id_array = M('RFileInvoice')->where('invoice_id = %d',$invoice_id)->getField('file_id',true);
			$file_list = M('File')->where('file_id in (%s)',implode(',',$file_id_array))->select();
			foreach ($file_list as $key => $value) {
				$file_type = '';
				$file_type = end(explode('.',$value['name']));
				$file_list[$key]['file_type'] = $file_type;
				$file_list[$key]['size'] = round($value['size']/1024,2).'Kb';
				if (intval($value['size']) > 1024*1024) {
					$file_list[$key]['size'] = round($value['size']/(1024*1024),2).'Mb';
				}
				$file_list[$key]['file_path'] = headPathHandle($value['file_path'], 1);
				$file_list[$key]['file_path_thumb'] = headPathHandle($value['file_path_thumb'], 1);
			}
			$invoice_info['file_list'] = $file_list ? $file_list : array();

			$permission_view = getPerByAction('invoice','view');
			$permission_edit = getPerByAction('invoice','edit');
			$permission_del = getPerByAction('invoice','delete');
			$view = 0;
			$edit = 0;
			$delete = 0;
			//详情、编辑、删除权限
			if (in_array($invoice_info['create_role_id'], $permission_view) || session('?admin')) {
				$view = 1;
			}
			if (in_array($invoice_info['create_role_id'], $permission_edit) || session('?admin')) {
				$edit = 1;
			}
			if (in_array($invoice_info['create_role_id'], $permission_del) || session('?admin')) {
				$delete = 1;
			}
			$data['permission']['view'] = $view;
			$data['permission']['edit'] = $edit;
			$data['permission']['delete'] = $delete;

			$data['data'] = $invoice_info;
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		}
	}

	/**
	 * 发票编辑
	 * @param 
	 * @author 
	 * @return 
	 */
	public function edit(){
		if ($this->isPost()) {
			$invoice_id = $_POST['id'] ? intval($_POST['id']) : 0;
			if (!$invoice_id) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$m_invoice = M('Invoice');
			$m_customer = M('Customer');
			$m_contract = M('Contract');
			$invoice_info = $m_invoice->where(array('invoice_id'=>$invoice_id))->find();
			//权限判断
			if(empty($invoice_info)) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			}elseif(!in_array($invoice_info['create_role_id'], $this->_permissionRes)) {
				$this->ajaxReturn('',L('DO NOT HAVE PRIVILEGES'),-2);
			}
			if ($invoice_info['is_checked'] == 1) {
				$this->ajaxReturn('','该发票已审核，不能编辑！',0);
			}
			if ($m_invoice->where(array('invoice_id'=>array('neq',$invoice_id),'name'=>trim($_POST['name'])))->find()) {
				$this->ajaxReturn('','发票编号已存在！',0);
			}
			if (!intval($_POST['contract_id'])) {
				$this->ajaxReturn('','合同不能为空！',0);
			}
			if (!intval($_POST['customer_id'])) {
				$this->ajaxReturn('','客户不能为空！',0);
			}
			if ($m_invoice->create()) {
				$m_invoice->invoice_time = trim($_POST['invoice_time']);
				$m_invoice->update_time = time();
				$m_invoice->is_checked = 0;
				$m_invoice->check_role_id = 0;
				$m_invoice->check_time = 0;
				if ($invoice_id = $m_invoice->where(array('invoice_id'=>$invoice_id))->save()) {
					//附件编辑
					$m_invoice_file = M('RFileInvoice');
					if ($_POST['file']) {
						$old_file_arr = $m_invoice_file->where(array('invoice_id'=>$invoice_id))->getField('file_id', true);
						$new_file_arr = $_POST['file'];
						$del_file_arr = array_diff($old_file_arr, $new_file_arr);
						foreach ($del_file_arr as $v) {
							@unlink($v['file_path']);
						}
						$del_res = $m_invoice_file->where(array('invoice_id'=>$invoice_id))->delete();

						foreach ($_POST['file'] as $v) {
							$file_data = array();
							$file_data['invoice_id'] = $invoice_id;
							$file_data['file_id'] = $v;
							$m_invoice_file->add($file_data);
						}
					} else {
						if (isset($_POST['file'])) {
							$examine_files = $m_invoice_file->where(array('invoice_id'=>$invoice_id))->getField('file_id', true);
							foreach ($examine_files as $v) {
								@unlink($v['file_path']);
							}
						}
					}
					$this->ajaxReturn('','修改成功！',1);
				} else {
					$this->ajaxReturn('','修改失败，请重试！',0);
				}
			}
		}
	}

	/**
	 * 发票删除
	 * @param 
	 * @author 
	 * @return 
	 */
	public function delete(){
		if ($this->isPost()) {
			$m_invoice = M('Invoice');
			$invoice_id = $_POST['id'] ? intval($_POST['id']) : '';
			if ($invoice_id) {
				$invoice_info = $m_invoice->where(array('invoice_id'=>$invoice_id))->find();
				if (!$invoice_info) {
					$this->ajaxReturn('','数据不存在或已删除！',0);
				}
				//权限判断
				if (!in_array($invoice_info['create_role_id'], $this->_permissionRes)){
					$this->ajaxReturn('',L('DO NOT HAVE PRIVILEGES'),-2);
				}
				if ($invoice_info['is_checked'] == 1) {
					$this->ajaxReturn('','已审核的发票不能删除，请撤销审核后重新操作！',0);
				}
				if ($m_invoice->where(array('invoice_id'=>$invoice_id))->delete()) {
					//删除相关附件信息
					M('RFileInvoice')->where(array('invoice_id'=>$invoice_id))->delete();
					$this->ajaxReturn('','删除成功！',1);
				} else {
					$this->ajaxReturn('','删除失败，请重试！',0);
				}
			} else {
				$this->ajaxReturn('','参数错误！',0);
			}
		}
		
	}

	/**
	 * 发票审核
	 * @param 
	 * @author 
	 * @return 
	 */
	public function check(){
		if ($this->isPost()) {
			$invoice_id = $_POST['invoice_id'] ? intval($_POST['invoice_id']) : 0;
			$m_invoice = M('Invoice');
			$invoice_info = $m_invoice->where('invoice_id = %d', $invoice_id)->find();
			if (!$invoice_info) {
				$this->ajaxReturn('','参数错误！',0);
			}
			if ($invoice_info['is_checked'] != 1) {
				$data = array();
				$is_agree = $_POST['is_agree'] ? intval($_POST['is_agree']) : 0;
				if ($is_agree == 1) {
					$data['is_checked'] = 1;
				} elseif ($is_agree == 2) {
					$data['is_checked'] = 2;
				} else {
					$this->ajaxReturn('','请求错误',0);
				}
				$data['check_role_id'] = session('role_id');
				$data['check_time'] = time();
				if ($m_invoice->where(array('invoice_id'=>$invoice_id))->save($data)) {
					//审核记录
					$check_data = array();
					$check_data['invoice_id'] = $invoice_id;
					$check_data['role_id'] = session('role_id');
					$check_data['is_checked'] = $data['is_checked'];
					$check_data['content'] = trim($_POST['content']);
					$check_data['check_time'] = time();
					M('InvoiceCheck')->add($check_data);
					//发送站内信
					if ($is_agree == 1) {
						$url = U('invoice/view','id='.$invoice_id);
						sendMessage($invoice_info['create_role_id'],'您创建的发票《<a href="'.$url.'">'.$invoice_info['name'].'</a>》<font style="color:green;">已通过审核</font>！',1);
					} else {
						sendMessage($invoice_info['create_role_id'],'您创建的发票《<a href="'.$url.'">'.$invoice_info['name'].'</a>》<font style="color:red;">经审核已被拒绝！请及时更正！</font>！',1);
					}
					$this->ajaxReturn('','发票审核成功！',1);
				}
			} else {
				$this->ajaxReturn('','该发票已审核，请勿重复操作！',0);
			}
		}
	}

	/**
	 * 发票撤销审核
	 * @param 
	 * @author 
	 * @return 
	 */
	public function revokeCheck(){
		if ($this->isPost()) {
			$invoice_id = $_POST['invoice_id'] ? intval($_POST['invoice_id']) : 0;
			$m_invoice = M('Invoice');
			$invoice_info = $m_invoice->where(array('invoice_id'=>$invoice_id))->find();
			if (!$invoice_info) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			}
			//权限判断
			if (!checkPerByAction('invoice','check')) {
				$this->ajaxReturn('',L('DO NOT HAVE PRIVILEGES'),-2);
			}
			if ($invoice_info['is_checked'] != 0) {
				$data = array();
				$data['is_checked'] = 0;
				$data['check_role_id'] = '';
				if ($m_invoice->where(array('invoice_id'=>$invoice_id))->save($data)) {
					//审核记录
					$check_data = array();
					$check_data['invoice_id'] = $invoice_id;
					$check_data['role_id'] = session('role_id');
					$check_data['is_checked'] = 0;
					$check_data['check_time'] = time();
					M('InvoiceCheck')->add($check_data);
					//发送站内信
					$url = U('invoice/view','id='.$invoice_id);
					sendMessage($invoice_info['create_role_id'],'您创建的发票《<a href="'.$url.'">'.$invoice_info['name'].'</a>》<font style="color:red;">已被撤销审核</font>！',1);
					$this->ajaxReturn('','撤销审核成功！',1);
				} else {
					$this->ajaxReturn('','撤销审核操作失败！',0);
				}
			} else {
				$this->ajaxReturn('','该发票已撤销审核，请勿重复操作！',0);
			}
		}
	}

	/**
	 * ajax获取客户下发票信息
	 * @param
	 * @author 
	 * @return 
	 */
	public function customer_invoice() {
		if ($this->isPost()) {
			$customer_id = $_POST['customer_id'] ? intval($_POST['customer_id']) : 0;
			if (!$customer_id) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$m_r_customer_invoice = M('RCustomerInvoice');
			$invoice_info = $m_r_customer_invoice->where(array('customer_id'=>$customer_id))->find();
			$this->ajaxReturn($invoice_info ? $invoice_info : array(),'',1);
		}
	}

	/**
	 * 获取发票添加时相关信息
	 * @param 
	 * @author 
	 * @return 
	 */
	public function getCode() {
		if ($this->isPost()) {
			$m_invoice = M('Invoice');
			//生成编号
			$invoice_max_id = $m_invoice->max('invoice_id');
			$invoice_max_id = $invoice_max_id+1;
			$invoice_max_code = str_pad($invoice_max_id,4,0,STR_PAD_LEFT);//填充字符串的左侧（将字符串填充为新的长度）
			$invoice_num = 'NO'.date('Ymd').'-'.$invoice_max_code;
			$data['data'] = $invoice_num ? $invoice_num : '';
			$data['status'] = 1;
			$data['info'] = 'success';
			$this->ajaxReturn($data,'JSON');
		}
	}

	/**
	 * 审核记录
	 * @param 
	 * @author 
	 * @return 
	 */
	public function check_list(){
		if ($this->isPost()) {
			$id = $_POST['id'] ? intval($_POST['id']) : '';
			if ($id == 0) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$m_user = M('User');
			$res_info = M('Invoice')->where(array('invoice_id'=>$id))->find();
			if (!$res_info) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			}
			$d_user = D('User');
			$temp_val = $d_user->get_full_name(array($res_info['create_role_id']));
			$owner_info = $temp_val[$res_info['create_role_id']];
			//审批意见
			$check_content = '';
			if ($res_info['check_role_id']) {
				$temp_val = $d_user->get_full_name(array($res_info['check_role_id']));
				$examine_info = $temp_val[$res_info['check_role_id']];
				$check_content = M('InvoiceCheck')->where(array('invoice_id'=>$id))->order('check_id desc')->getField('content');
			} else {
				$examine_info = array();
			}
			$res_info['content'] = $check_content;
			
			$info = array();
			$info['res_info'] = $res_info;
			$info['owner_info'] = $owner_info;
			$info['examine_info'] = $examine_info;
			$data['data'] = $info ? $info : array();
			$data['status'] = 1;
			$data['info'] = 'success';
			$this->ajaxReturn($data,'JSON');
		}
	}
}