<?PHP 
/**
*发票模块
*
**/
class InvoiceAction extends Action{
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('adddata','viewdata','editdata','customer_invoice','check_list','getcontractinvoice')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
	}
	
	/**
	 * 发票列表
	 * @param 
	 * @author 
	 * @return 
	 */
	public function index(){
		$d_invoice_view = D('InvoiceView');
		$m_customer = M('Customer');
		$m_contract = M('Contract');
		$m_user = M('User');

		$order = 'update_time desc';
		$p = intval($_GET['p']) ? intval($_GET['p']) : 1;

		$d_invoice = D('Invoice');
		$d_search = D('Search');
//p($_GET,'');
		// 搜索
		$res = $d_search->getWhere($_GET);
//p($res);
		// 搜索where条件
		if ($res['where']) $where = $res['where'];

		// 记录分页参数
		$params = $res['page_params'];

		// 记录搜索条件
		$this->fields_search = $res['fields_search'];

		// 记录特殊搜索条件
		$this->single_list = $res['single_list'];
//p($where,'');

		// 待审核的发票搜索，如果写法和高级搜索的写法不同，特殊处理
		if (isset($_GET['is_checked']) && !is_array($_GET['is_checked'])) {
			$where['is_checked'] = intval($_GET['is_checked']);
		}

		// 发票分类，0销项发票 1进项发票
		$where['category'] = intval($_GET['category']) == 0 ? 0 : intval($_GET['category']);

		// 有role_id的查询，需要处理权限范围问题，重新整理where条件
		$where = $d_search->roleWhere('invoice.create_role_id', $where, $this->_permissionRes);

		// 导出功能
		if ($_GET['act'] == 'excel') {
			$this->excelexport($where, $order);
			exit();
		}

		if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			cookie('listrows', $listrows, 3600 * 24 * 30);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = cookie('listrows') ? cookie('listrows') : 15;
			$params[] = "listrows=".$listrows;
		}
		$p = intval($_GET['p']) ? intval($_GET['p']) : 1;
		$count = $d_invoice_view->where($where)->count();
		$p_num = ceil($count/$listrows);
		if($p_num<$p){
			$p = $p_num;
		}
	
		$invoice_list = $d_invoice_view->where($where)->page($p.','.$listrows)->order($order)->select();
// p($invoice_list,'');
// echo sqlFormat($d_invoice_view->_sql()); die();
		foreach ($invoice_list as $key => $val) {
			$contract_id = $val['contract_id'];
			$contract_price_list = M('Invoice')->where('contract_id="%d" && is_checked!=2', $contract_id)->getField('price', true);
			$invoiced_price = 0;
			foreach ($contract_price_list as $k => $v) {
				$invoiced_price += $v;
			}
			$no_invoiced_price = $val['contract_price'] - $invoiced_price; 
			$no_invoiced_price = $no_invoiced_price > 0 ? $no_invoiced_price : 0; 
			$invoice_list[$key]['invoiced_price'] = $invoiced_price;
			$invoice_list[$key]['no_invoiced_price'] = $no_invoiced_price;
		}
		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		$this->listrows = $listrows;
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());

		$this->count = $count;
		$this->invoice_list = $invoice_list;


		// 所有可搜索的字段
		$this->field_list = $d_invoice->search_field_list();

		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 发票导出
	 * @author lee
	 */
	public function excelexport($where = array(), $order = '')
	{
		if(checkPerByAction('invoice','excelexport')){
			$d_invoice_view = D('InvoiceView');
			$m_customer = M('Customer');
			$m_contract = M('Contract');
			$m_user = M('User');

			$select_ids = $_GET['daochu'];
			if($select_ids !=''){
				$where['invoice.invoice_id'] = array('in', $select_ids);
			}

			// 导出的字段列表
			$field_list = array(
				'name' => array('name' => '发票编号', 'form_type' => 'text'),
				'customer_name' => array('name' => '客户名称', 'form_type' => 'text'),
				'contract_num' => array('name' => '合同编号', 'form_type' => 'text'),
				'invoice_time' => array('name' => '开票时间', 'form_type' => 'date'),
				'price' => array('name' => '开票金额', 'form_type' => 'price'),
				'billing_type' => array('name' => '开票类型', 'form_type' => 'invoice.billing_type'),
				'contract_price' => array('name' => '合同总金额', 'form_type' => 'price'),
				'invoiced_price' => array('name' => '已开票金额', 'form_type' => 'price'),
				'no_invoiced_price' => array('name' => '未开票金额', 'form_type' => 'price'),
				'express' => array('name' => '快递单号', 'form_type' => 'text'),
				'invoice_header' => array('name' => '开票抬头', 'form_type' => 'text'),
				'taxes_num' => array('name' => '纳税识别号', 'form_type' => 'text'),
				'opening_bank' => array('name' => '开户行', 'form_type' => 'text'),
				'account_number' => array('name' => '开户账号', 'form_type' => 'text'),
				'billing_address' => array('name' => '开票地址', 'form_type' => 'text'),
				'telephone' => array('name' => '电话', 'form_type' => 'text'),
				'full_name' => array('name' => '负责人', 'form_type' => 'text'),
			);

			// 文件名
			$file_name = 'jxcrm_发票导出_'.date('Ymd');
			// 循环导出第N次，同前台js中的times
			$current_page = intval($_GET['current_page']);
			// 前台设置单次导出的总数量
			$total_export_count = $export_limit = intval($_GET['export_limit']);
			// 已完成导出的数量【首次是0】
			$already_export_count = $export_limit * ($current_page - 1);

			exportCsv($file_name, $field_list, $total_export_count, $already_export_count,function($page) use ($d_invoice_view, $order, $where){
				$list = $d_invoice_view->where($where)->order($order)->limit($page)->select();
				return $list;
			});
		}else{
			alert('error',  L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
		}
	}


	/**
	 * 发票添加
	 * @param 
	 * @author 
	 * @return 
	 */
	public function add(){
		$m_invoice = M('Invoice');
		if ($this->isPost()) {
			if ($m_invoice->where(array('name'=>trim($_POST['name'])))->find()) {
				$this->error('发票编号已存在！');
			}
			/*if (!intval($_POST['contract_id'])) {
				$this->error('合同不能为空！');
			}
			if (!intval($_POST['customer_id'])) {
				$this->error('客户不能为空！');
			}*/

			if ($m_invoice->create()) {
				$m_invoice->create_role_id = session('role_id');
				$m_invoice->create_time = time();
				$m_invoice->update_time = time();
				$m_invoice->invoice_time = strtotime($_POST['invoice_time']);

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
					alert('success', '添加成功！', U('invoice/index','category='.$_POST['category']));
				} else {
					$this->error('添加失败，请重试！');
				}
			}
		}
		$contract_id = $_GET['contract_id'] ? intval($_GET['contract_id']) : 0;
		if ($contract_id) {
			$contract_info = M('Contract')->where(array('contract_id'=>$contract_id))->field('contract_id,customer_id,price,number')->find();
			//未开票金额
			$is_checked_price = M('Invoice')->where(array('contract_id'=>$contract_id,'is_checked'=>array('neq',2)))->sum('price');
			$no_price = round(($contract_info['price']-$is_checked_price),2);
			$contract_info['no_price'] = ($no_price > 0) ? $no_price : '0.00';
			$customer_name = M('Customer')->where(array('customer_id'=>$contract_info['customer_id']))->getField('name');
			//发票数据
			$invoice_data = M('RCustomerInvoice')->where(array('customer_id'=>$contract_info['customer_id']))->find();
			$data = array();
			$data['contract_info'] = $contract_info;
			$data['customer_name'] = $customer_name;
			$data['invoice_data'] = $invoice_data;
			$this->assign('data',$data);
		}
		//生成编号
		$invoice_max_id = $m_invoice->max('invoice_id');
		$invoice_max_id = $invoice_max_id+1;
		$invoice_max_code = str_pad($invoice_max_id,4,0,STR_PAD_LEFT);//填充字符串的左侧（将字符串填充为新的长度）
		$this->name = 'NO'.date('Ymd').'-'.$invoice_max_code;
		$this->display();
	}

	/**
	 * 发票详情
	 * @param 
	 * @author 
	 * @return 
	 */
	public function view(){
		$invoice_id = $_GET['id'] ? intval($_GET['id']) : 0;
		if(!$invoice_id){
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$d_file = D('File');
		$m_invoice = M('Invoice');
		$m_customer = M('Customer');
		$m_contract = M('Contract');
		$m_r_file_invoice = M('RFileInvoice');
		$m_file = M('File');
		$d_user = D('User');
		$m_user = M('User');
		$invoice_info = $m_invoice->where(array('invoice_id'=>$invoice_id))->find();
		// 权限判断
		if(empty($invoice_info)) {
			alert('error', L('THE_CONTRACT_DOES_NOT_EXIST_OR_HAS_BEEN_DELETED'), U('invoice/index'));
		}elseif(!in_array($invoice_info['create_role_id'], $this->_permissionRes)) {
			alert('error',L('DO NOT HAVE PRIVILEGES'),$_SERVER['HTTP_REFERER']);
		}
		// 发票分类
		switch ($invoice_info['category']) {
			case 0 : $invoice_info['category_name'] = '销项发票'; break;
			case 1 : $invoice_info['category_name'] = '进项发票'; break;
		}
		// 发票类型
		switch ($invoice_info['billing_type']) {
			case 1 : $invoice_info['billing_type_name'] = '增值税普通发票'; break;
			case 2 : $invoice_info['billing_type_name'] = '增值税专用发票'; break;
			case 3 : $invoice_info['billing_type_name'] = '收据'; break;
		}
		$invoice_info['customer_name'] = $m_customer->where(array('customer_id'=>$invoice_info['customer_id']))->getField('name');
		$invoice_info['contract_num'] = $m_contract->where(array('contract_id'=>$invoice_info['contract_id']))->getField('number');
		$create_info = $d_user->get_full_name(array($invoice_info['create_role_id']));
		$invoice_info['create_info'] = $create_info[$invoice_info['create_role_id']];
		if ($invoice_info['is_checked']) {
			$check_role_info_temp = $d_user->get_full_name(array($invoice_info['check_role_id']));
			$check_role_info = $check_role_info_temp[$invoice_info['check_role_id']];
		}
		$invoice_info['check_role_info'] = $check_role_info ? $check_role_info : array();
		//附件信息
		$file_ids = $m_r_file_invoice->where('invoice_id = %d', $invoice_id)->getField('file_id', true);
		$invoice_info['file'] = $m_file->where('file_id in (%s)', implode(',', $file_ids))->select();
		$file_count = 0;
		foreach ($invoice_info['file'] as $key=>$value) {
			$invoice_info['file'][$key]['owner'] = $m_user->where('role_id = %d', $value['role_id'])->field('full_name')->find();
			$invoice_info['file'][$key]['size'] = ceil($value['size']/1024);
			/*判断文件格式 对应其图片*/
			$houzhui = getExtension($value['name']);
			switch ($houzhui) {
				case 'doc':
					$pic = 'doc.png';
					break;
				case 'docx':
					$pic = 'doc.png';
					break;
				case 'pptx':
					$pic = 'ppt.png';
					break;
				case 'ppt':
					$pic = 'ppt.png';
					break;
				case 'xls':
					$pic = 'excel.png';
					break;
				case 'zip':
					$pic = 'zip.png';
					break;
				case 'zip':
					$pic = 'zip.png';
					break;
				case 'pdf':
					$pic = 'pdf.png';
					break;
				case 'png':
					$pic = 'pic.png';
					break;
				case 'jpg':
					$pic = 'pic.png';
					break;
				case 'jpeg':
					$pic = 'pic.png';
					break;
				case 'gif':
					$pic = 'pic.png';
					break;
				default:
					$pic = 'unknown.png';
					break;
			}
			$invoice_info['file'][$key]['pic'] = $pic;
			if ($value['oss'] == 1) {
				$invoice_info['file'][$key]['file_path'] = $d_file::FILE_URL . '/' . $value['file_path'];
			}
			$file_count++;
		}
		$invoice_info['file_count'] = $file_count;

		$this->invoice_info = $invoice_info;
		$this->alert = parseAlert();
		$this->display();
	}

	/**
	 * 发票编辑
	 * @param 
	 * @author 
	 * @return 
	 */
	public function edit(){
		$invoice_id = $_REQUEST['id'] ? intval($_REQUEST['id']) : 0;
		if(!$invoice_id){
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$m_invoice = M('Invoice');
		$m_customer = M('Customer');
		$m_contract = M('Contract');
		$invoice_info = $m_invoice->where(array('invoice_id'=>$invoice_id))->find();
		//权限判断
		if(empty($invoice_info)) {
			alert('error', L('THE_CONTRACT_DOES_NOT_EXIST_OR_HAS_BEEN_DELETED'), U('invoice/index'));
		}elseif(!in_array($invoice_info['create_role_id'], $this->_permissionRes)) {
			alert('error',L('DO NOT HAVE PRIVILEGES'),$_SERVER['HTTP_REFERER']);
		}
		$invoice_info['customer_name'] = $m_customer->where(array('customer_id'=>$invoice_info['customer_id']))->getField('name');
		$invoice_info['contract_info'] = $m_contract->where(array('contract_id'=>$invoice_info['contract_id']))->field('number,price')->find();

		if ($invoice_info['is_checked'] == 1) {
			alert('error','该发票已审核，不能编辑！',$_SERVER['HTTP_REFERER']);
		}

		if ($this->isPost()) {
			if ($m_invoice->where(array('invoice_id'=>array('neq',$invoice_id),'name'=>trim($_POST['name'])))->find()) {
				$this->error('发票编号已存在！');
			}
			/*if (!intval($_POST['contract_id'])) {
				$this->error('合同不能为空！');
			}
			if (!intval($_POST['customer_id'])) {
				$this->error('客户不能为空！');
			}*/
			if ($m_invoice->create()) {
				$m_invoice->invoice_time = strtotime($_POST['invoice_time']);
				$m_invoice->update_time = time();
				$m_invoice->is_checked = 0;
				$m_invoice->check_role_id = 0;
				$m_invoice->check_time = 0;
				if ($invoice_id = $m_invoice->where(array('invoice_id'=>$invoice_id))->save()) {
					alert('success', '修改成功！', U('invoice/index','category='.$_POST['category']));
				} else {
					$this->error('修改失败，请重试！');
				}
			}
		}
		$this->invoice_info = $invoice_info;
		$this->alert = parseAlert();
		$this->display();
	}

	/**
	 * 发票删除
	 * @param 
	 * @author 
	 * @return 
	 */
	public function delete(){
		$m_invoice = M('Invoice');
		$invoice_ids = is_array($_REQUEST['invoice_id']) ? $_REQUEST['invoice_id'] : array($_REQUEST['invoice_id']);
		if ($invoice_ids) {
			//过滤出已审核的发票
			$is_invoice_ids = array();
			$is_invoice_ids = $m_invoice->where(array('invoice_id'=>array('in',$invoice_ids),'is_checked'=>'1'))->getField('invoice_id',true);
			$del_invoice_ids = array();
			if (empty($is_invoice_ids)) {
				$del_invoice_ids = $invoice_ids;
			} else {
				foreach ($invoice_ids as $v) {
					if (!in_array($v,$is_invoice_ids)) {
						$del_invoice_ids[] = $v;
					}
				}
			}
			
			if ($del_invoice_ids) {
				//权限判断
				foreach ($del_invoice_ids as $v) {
					$invoice_info = $m_invoice->where('invoice_id = %d', $v)->find();
					if (!in_array($invoice_info['create_role_id'], $this->_permissionRes)){
						$this->ajaxReturn('','部分发票无权限，不能批量删除！',0);
					}
				}
				if ($m_invoice->where(array('invoice_id'=>array('in',$del_invoice_ids)))->delete()) {
					//删除相关附件信息
					M('RFileInvoice')->where(array('invoice_id'=>array('in',$del_invoice_ids)))->delete();

					if ($is_invoice_ids) {
						$this->ajaxReturn('','部分发票已审核不能删除，请撤销审核后重新操作！',0);
					} else {
						$this->ajaxReturn('','删除成功！',1);
					}
				} else {
					$this->ajaxReturn('','删除失败，请重试！',0);
				}
			} else {
				$this->ajaxReturn('','已审核的发票不能删除，请撤销审核后重新操作！',0);
			}
		} else {
			$this->ajaxReturn('','参数错误！',0);
		}
	}

	/**
	 * 发票审核
	 * @param 
	 * @author 
	 * @return 
	 */
	public function check(){
		$invoice_id = $_POST['invoice_id'] ? intval($_POST['invoice_id']) : 0;
		$is_agree = $this->_post('is_agree','intval');
		$m_invoice = M('Invoice');
		$invoice_info = $m_invoice->where('invoice_id = %d', $invoice_id)->find();
		if (!$invoice_info) {
			alert('error', '参数错误！',$_SERVER['HTTP_REFERER']);
		}
		if ($invoice_info['is_checked'] != 1) {
			$data = array();
			$is_agree = $_POST['is_agree'] ? intval($_POST['is_agree']) : 0;
			if ($is_agree == 1) {
				$data['is_checked'] = 1;

				// 发票号码和快递编号
				$data['number'] = trim($_POST['number']);
				$data['express'] = trim($_POST['express']);
			} elseif ($is_agree == 2) {
				$data['is_checked'] = 2;
			} else {
				alert('error', '请求错误!', $_SERVER['HTTP_REFERER']);
			}
			$data['check_role_id'] = session('role_id');
			$data['check_time'] = time();
			if ($m_invoice->where(array('invoice_id'=>$invoice_id))->save($data)) {
				//审核记录
				$check_data = array();
				$check_data['invoice_id'] = $invoice_id;
				$check_data['role_id'] = session('role_id');
				$check_data['is_checked'] = $data['is_checked'];
				$check_data['content'] = $_POST['description'];
				$check_data['check_time'] = time();
				M('InvoiceCheck')->add($check_data);
				//发送站内信
				if ($is_agree == 1) {
					$url = U('invoice/view','id='.$invoice_id);
					sendMessage($invoice_info['create_role_id'],'您创建的发票《<a href="'.$url.'">'.$invoice_info['name'].'</a>》<font style="color:green;">已通过审核</font>！',1);
				} else {
					sendMessage($invoice_info['create_role_id'],'您创建的发票《<a href="'.$url.'">'.$invoice_info['name'].'</a>》<font style="color:red;">经审核已被拒绝！请及时更正！</font>！',1);
				}
				alert('success','发票审核成功！',$_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', '该发票已审核，请勿重复操作！',$_SERVER['HTTP_REFERER']);
		}
	}

	/**
	 * 发票撤销审核
	 * @param 
	 * @author 
	 * @return 
	 */
	public function revokeCheck(){
		$invoice_id = $_GET['invoice_id'] ? intval($_GET['invoice_id']) : 0;
		$m_invoice = M('Invoice');
		$invoice_info = $m_invoice->where(array('invoice_id'=>$invoice_id))->find();
		if (!$invoice_info) {
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
		//权限判断
		if (!checkPerByAction('invoice', 'check')) {
			alert('error',L('DO NOT HAVE PRIVILEGES'),$_SERVER['HTTP_REFERER']);
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
				alert('success','撤销审核成功！',$_SERVER['HTTP_REFERER']);
			} else {
				alert('error','撤销审核操作失败！',$_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error','该发票已撤销审核，请勿重复操作！',$_SERVER['HTTP_REFERER']);
		}
	}

	//审核历史
	public function check_list(){
		$m_invoice_check = M('InvoiceCheck');
		$m_user = M('user');
		$d_user = D('User');
		$invoice_id = intval($_GET['id']);
		if ($invoice_id) {
			$check_list = $m_invoice_check ->where('invoice_id =%d',$invoice_id)->order('check_id asc')->select();
			foreach ($check_list as $k=>$v) {
				$temp_val = $d_user->get_full_name(array($v['role_id']));
				$check_list[$k]['user'] = $temp_val[$v['role_id']];
			}
			$this->check_list = $check_list;
		}
		$this->display();
	}

	/**
	 * 客户下发票数据添加
	 * @param 
	 * @author 
	 * @return 
	 */
	public function addData () {
		$customer_id = $_REQUEST['customer_id'] ? intval($_REQUEST['customer_id']) : 0;
		$m_r_customer_invoice = M('RCustomerInvoice');
		if (!$customer_id) {
			if ($this->isAjax()) {
				$this->ajaxReturn('','参数错误！',0);
			} else {
				echo '<div class="alert alert-error">参数错误！</div>';die();
			}
		}
		$invoice_info = $m_r_customer_invoice->where(array('customer_id'=>$customer_id))->find();
		if ($this->isPost()) {
			if ($m_r_customer_invoice->create()) {
				$m_r_customer_invoice->create_time = time();
				$m_r_customer_invoice->update_time = time();
				$m_r_customer_invoice->create_role_id = session('role_id');
				if ($m_r_customer_invoice->add()) {
					$this->ajaxReturn('','success',1);
				} else {
					$this->ajaxReturn('','添加失败！',0);
				}
			}
		}
		$this->invoice_info = $invoice_info;
		$this->display('add_dialog');
	}

	/**
	 * 客户下发票数据编辑
	 * @param 
	 * @author 
	 * @return 
	 */
	public function editData () {
		$invoice_id = $_REQUEST['invoice_id'] ? intval($_REQUEST['invoice_id']) : 0;
		$m_r_customer_invoice = M('RCustomerInvoice');
		if (!$invoice_id) {
			if ($this->isAjax()) {
				$this->ajaxReturn('','参数错误！',0);
			} else {
				echo '<div class="alert alert-error">参数错误！</div>';die();
			}
		}
		$invoice_info = $m_r_customer_invoice->where(array('id'=>$invoice_id))->find();
		if ($this->isPost()) {
			if ($m_r_customer_invoice->create()) {
				$m_r_customer_invoice->update_time = time();
				if ($m_r_customer_invoice->where(array('id'=>$invoice_id))->save()) {
					$this->ajaxReturn('','success',1);
				} else {
					$this->ajaxReturn('','修改失败！',0);
				}
			}
		}
		$this->invoice_info = $invoice_info;
		$this->display('edit_dialog');
	}

	/**
	 * 客户下发票数据查看
	 * @param 
	 * @author 
	 * @return 
	 */
	public function viewData () {
		$invoice_id = $_REQUEST['invoice_id'] ? intval($_REQUEST['invoice_id']) : 0;
		if (!$invoice_id) {
			echo '<div class="alert alert-error">参数错误！</div>';die();
		}
		$invoice_info = M('RCustomerInvoice')->where(array('id'=>$invoice_id))->find();
		$invoice_info['customer_name'] = M('Customer')->where(array('customer_id'=>$invoice_info['customer_id']))->getField('name');
		//判断编辑权限

		$this->invoice_info = $invoice_info;
		$this->display('view_dialog');
	}

	/**
	 * ajax获取客户下发票信息
	 * @param
	 * @author 
	 * @return 
	 */
	public function customer_invoice() {
		$customer_id = $_POST['customer_id'] ? intval($_POST['customer_id']) : 0;
		if (!$customer_id) {
			$this->ajaxReturn('','参数错误！',0);
		}
		$m_r_customer_invoice = M('RCustomerInvoice');
		$invoice_info = $m_r_customer_invoice->where(array('customer_id'=>$customer_id))->find();
		$this->ajaxReturn($invoice_info ? $invoice_info : array(),'',1);
	}

	/**
	 * ajax获取合同下已收发票总额
	 * @param
	 * @author 
	 * @return 
	 */
	public function getContractInvoice() {
		if ($this->isAjax()) {
			$contract_id = $_GET['contract_id'] ? intval($_GET['contract_id']) : 0;
			if (!$contract_id) {
				$this->ajaxReturn('','error',0);
			}
			$invoice_price = M('Invoice')->where(array('contract_id'=>$contract_id,'is_checked'=>array('neq',2)))->sum('price');
			$data['data'] = $invoice_price ? $invoice_price : '0';
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		}
	}
}