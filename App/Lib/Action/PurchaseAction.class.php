<?php
/**
*任务模块
*
**/
class PurchaseAction extends Action{
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('into_stock', 'excel_templet_download', 'excelimport', 'readsnexcel', 'stockinsn', 'stockinsnview', 'dialoglist', 'returnproductlist', 'returngoodsnview', 'purchase_return_list_dialog')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);

		//实例化模型
		$this->d_purchase = D('Purchase');
		$this->m_purchase_product = M('PurchaseProduct');
		$this->alert = parseAlert();
	}

	/**
	* 采购单首页
	***/
	public function index(){
		$d_purchase = D('Purchase');
		$m_supplier = M('Supplier');
		$m_user = M('User');

		//待审核的
		if (intval($_GET['unchecked']) > 0) {
			$purchase_ids = D('Exam')->getOrderIds(array('type_id' => 1, 'exam_status' => array('lt', 2)));
			$where['purchase_id'] = array('in', $purchase_ids ?: '');
			$params[] = "unchecked=".intval($_GET['unchecked']);
		}
		//普通搜索
		$search = $this->_get('search','trim');
		if ($search) {
			//采购单号、采购员
			$map['number|name'] = array('like','%'.$search.'%');

			//供应商名称
			$where_supplier['_string'] = 'name like "%'.$search.'%"';
			$supplier_ids = $m_supplier->where($where_supplier)->getField('supplier_id', true) ?: '';
			$map['type_id'] = array('in', $supplier_ids);
			
			$map['_logic'] = 'or';
			$where['_complex'] = $map;
		}
		
		$count = $d_purchase->where($where)->count();

		// 导出
		if (trim($_GET['act']) == 'export') {
			if(!checkPerByAction('purchase','export')){
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
			if ($_GET['ids']) {
				$where = array('purchase_id' => array('IN', $_GET['ids']));
			}
			$table_head = array('field' => array());
			$table_head['field']['number'] = '采购单编号';
			$table_head['field']['name'] = '采购名称';
			$table_head['field']['supplier_name'] = '供应商名称';
			$table_head['field']['purchase_amount'] = '采购金额';
			$table_head['field']['owner_role_name'] = '采购员';
			$table_head['field']['creator_name'] = '制单人';
			$table_head['field']['purchase_time'] = '日期';
			$table_head['field']['exam_status.name'] = '审批状态';
			csvExport('pdcrm-采购单导出', $table_head, $count, function($page) use ($d_purchase, $where) {
				return $d_purchase->getList($page, $where, 'update_time desc');
			});
		}
	

		//分页
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1;
		if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			cookie('listrows', $listrows, 3600 * 24 * 30);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = cookie('listrows') ? cookie('listrows') : 15;
			$params[] = "listrows=".$listrows;
		}
		$this->listrows = $listrows;
		$count = $d_purchase->where($where)->count();
		$p_num = ceil($count/$listrows);
		if($p_num < $p) $p = $p_num;

		$list = $d_purchase->getList($p . ',' . $listrows, $where, 'update_time desc');
		$this->assign('list',$list);

		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		if (!empty($_GET['status'])) {
			$params[] = 'status='.trim($_GET['status']);
		}
		$this->parameter = implode('&', $params);
		$this->by_parameter = str_replace('status='.$_GET['status'], '', implode('&', $params));
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		
		//pd_exam_type中对应的type_id值
		$this->type_id = 1;

		$this->display();
	}

	/**
	* 采购单添加
	**/
	public function add(){
		if (IS_POST) {
			if (empty($_POST['product_info_list'])) alert('error', '最少添加一件产品', $_SERVER['HTTP_REFERER']);
			$_POST['type'] = 1;
			$_POST['type_id'] = $_POST['supplier_id'];
			$purchase_id = $this->d_purchase->addData($_POST);
			if ($purchase_id) {
				$res = $this->d_purchase->addProductData($_POST['product_info_list']);
				if ($res) {
					//保存初始审批信息
					$d_exam = D('Exam');
					$exam_data = $d_exam->examInitData((int) $_POST['process_status'], (int) $_POST['temp_id'], $purchase_id, 1);
					$exam_data['type_id'] = 1;
					$exam_data['order_id'] = $purchase_id;
					if (!$d_exam->addOrderExam($exam_data)) {
						$data = array('msg'=> '初始审批信息保存失败', 'status'=>2);
					}

					// alert('success', '保存成功', U('purchase/view','id='.$purchase_id));
					$this->ajaxReturn(array('msg' => '保存成功', 'status' => 1, 'id' => $purchase_id));
				} else {
					$this->d_purchase->deleteData();	// 删除错误数据
					$this->ajaxReturn(array('msg' => $this->d_purchase->msg, 'status' => 0));
				}
			} else {
				$this->ajaxReturn(array('msg' => $this->d_purchase->msg, 'status' => 0));
			}
		} else {
			//查询审批流信息 process_status 审批开启状态，1开启，2禁用
			$exam_init_info = D('Exam')->initData(1);
			$this->process_status = $exam_init_info['process_status'];
			$this->process_list = $exam_init_info['process_list'];

			$max_id = M('Purchase')->where(array('type' => 1))->count() + 1;
			$this->number = D('Config')->getValue('purchase_prefix') . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);
			$this->display();
		}
	}

	/**
	* 采购单编辑
	**/
	public function edit(){
		$purchase_id = $this->_request('id','intval',intval($_REQUEST['purchase_id']));
		$purchase = $this->d_purchase->getView($purchase_id);

		//权限判断
		if (!in_array($purchase['owner_role_id'], getPerByAction(MODULE_NAME,ACTION_NAME))) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}

		//是否能编辑
		if (!$this->d_purchase->canEdit($purchase_id)) {
			alert('error', $this->d_purchase->msg, $_SERVER['HTTP_REFERER']);
		}

		//根据审批状态判断是否有编辑权限
		$exam_info = D('Exam')->checkExamPermission(1, $purchase_id);
		if ($exam_info['exam_status'] == 1 || $exam_info['exam_status'] == 2) {
			alert('error', '已进入审批流程，无法编辑', $_SERVER['HTTP_REFERER']);
		}

		if (IS_POST) {
			if (empty($_POST['product_info_list'])) alert('error', '最少添加一件产品', $_SERVER['HTTP_REFERER']);
			if ($this->d_purchase->saveData()) {
				if ($this->d_purchase->saveProductData()) {

					//更新初始审批信息
					$d_exam = D('Exam');
					$exam_data = $d_exam->examInitData((int) $_POST['process_status'], (int) $_POST['temp_id'], $purchase_id, 1);
					$exam_data['type_id'] = 1;
					$exam_data['order_id'] = $purchase_id;
					if (!$d_exam->editOrderExam(array('type_id' => $exam_data['type_id'], 'order_id' => $exam_data['order_id']),$exam_data)) {
						$data = array('msg'=> '初始审批信息保存失败', 'status'=>2);
					}

					alert('success', $this->d_purchase->msg, U('purchase/view', array('id' => $purchase_id)));
				} else {
					$this->d_purchase->reset();		// 数据重置
					alert('error', $this->d_purchase->msg, $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', $this->d_purchase->msg, $_SERVER['HTTP_REFERER']);
			}
		} else {
			$purchase['product_info_list'] = $this->d_purchase->getPurcharseProductInfoList();  // 获取产品信息
			$this->assign('purchase', $purchase);

			//查询审批流信息 process_status 审批开启状态，1开启，2禁用
			$exam_init_info = D('Exam')->initData(1, $purchase_id);
			$this->process_status = $exam_init_info['process_status'];
			$this->process_list = $exam_init_info['process_list'];
			$this->order_exam = $exam_init_info['order_exam'];

			$this->display();
		}
	}

	/**
	 * 采购单详情
	 */
	public function view(){
		$m_purchase_product = M('purchaseProduct');
		$m_supplier = M('Supplier');
		$m_user = M('User');

		$purchase_id = $this->_request('id','intval');

		// 采购单信息
		$purchase = $this->d_purchase->getView($purchase_id);

		//权限判断
		if (!in_array($purchase['owner_role_id'], getPerByAction(MODULE_NAME,ACTION_NAME))) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}

		// 关联产品信息  
		$purchase['product_info_list'] = $this->d_purchase->getPurcharseProductInfoList();  // 获取产品信息

		// 仓库信息
		// $warehouse_list = M('Warehouse')->where('is_deleted = 0')->select();
		// $this->assign('warehouse_list', $warehouse_list);

		// 员工信息
		$user_list = D('RoleView')->where('status = 1')->select();
		$this->assign('user_list', $user_list);

		// 入库记录
		$d_stock_in = D('StockIn');
		$this->stock_in_list = $d_stock_in->getPurchaseStockInList($purchase_id, $purchase['type']);

		// 入库状态 旧逻辑，只有退货，没有换货
		// $this->stock_in_status = $d_stock_in->stock_in_status;

		//根据type值显示不同的文字，type 1采购单 2销售退货单
		$this->type_name = $this->d_purchase->getTypeName('purchase', $purchase['type']);

		//pd_exam_type中对应的type_id值
		if ($purchase['type'] == 1) {
			$this->type_id = 1;
			$finance_type = -1;		// 采购
		} else if ($purchase['type'] == 2){
			$this->type_id = 3;
			$purchase['contract_id'] = M('RContractSales')->where(array('sales_id' => $purchase['type_id']))->getField('contract_id');
			$purchase['contract_number'] = M('Contract')->where(array('contract_id' => $purchase['contract_id']))->getField('number');
			$finance_type = -2;		// 销售退货
		}
		$this->order_id = $purchase['purchase_id'];

		// 财务，应付款列表
		$this->payables = D('Finance')->ablesList(array('contract_id' => $purchase_id, 'type_id' => $finance_type));
		
		$this->purchase = $purchase;
		$this->display();
	}


	/**
	* 采购产品入库
	**/
	public function into_stock(){
		$type = M('Purchase')->where(array('purchase_id' => $_REQUEST['purchase_id']))->getField('type');
		if ($type == 1) {
			$type_id = 1;
		} else if ($type == 2) {
			$type_id = 3;
		}

		if (IS_POST) {
			if (D('Exam')->orderStatus(array('type_id' => $type_id, 'order_id' => (int) $_POST['purchase_id'])) !== 2) {
				$this->ajaxReturn(array('msg' => '订单没有审核，产品无法入库！', 'status' => 0));
			}

			$d_warehouse = D('Warehouse');
			if (!$d_warehouse->isStorehouse($_POST['warehouse_id'])) {
				$this->ajaxReturn(array('msg' => $d_warehouse->msg, 'status' => 0));
			}
			$d_stock_in = D('StockIn');
			$_POST['type'] = $type;
			$_POST['type_id'] = $_POST['purchase_id'];
			$_POST['create_time'] = strtotime($_POST['create_time']);
			$_POST['update_time'] = time();
			$res = $d_stock_in->addData();
			$this->ajaxReturn(array('msg' => $d_stock_in->msg, 'status' => (int) $res));
		} else {
			$purchase_id = (int) $_GET['purchase_id'];
			if (D('Exam')->orderStatus(array('type_id' => $type_id, 'order_id' => $purchase_id)) !== 2) {
				echo '<div class="alert alert-error">订单没有审核，产品无法入库！</div>';
				die();
			}
			$this->purchase = $this->d_purchase->getView($purchase_id);

			$this->product_info_list = $this->d_purchase->getPurcharseProductInfoList($purchase_id);
			if ($type == 1) {
				$this->has_return = array_sum(y_array_column($this->product_info_list, 'return_count'));
			}
			$this->warehouse = D('Warehouse')->selectList();
			$max_id = M('StockIn')->max('stock_in_id') + 1;
			$this->number = D('Config')->getValue('stock_in_prefix') . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);
			//$role_ids = getPerByAction(MODULE_NAME, ACTION_NAME);
			$role_ids = M('User')->where('status = 1')->getField('role_id', true);
			$this->role_list = D('User')->get_full_name($role_ids);
			$this->display();
		}
	}


	/**
	 * 采购退货首页
	 */
	public function return_goods(){
		$d_sales = D('Sales');
		$m_supplier = M('Supplier');
		$d_user = D('User');
		$d_exam = D('Exam');
		$where = array('type' => 1);

		//待审核的
		if (intval($_GET['unchecked']) > 0) {
			$sales_ids = D('Exam')->getOrderIds(array('type_id' => 2, 'exam_status' => array('lt', 2)));
			$where['sales_id'] = array('in', $sales_ids ?: '');
			$params[] = "unchecked=".intval($_GET['unchecked']);
		}

		//权限范围
		$where['owner_role_id'] = array('in', getPerByAction(MODULE_NAME, ACTION_NAME));

		//简单查询
		$search = $this->_get('search','trim');
		if ($search) {
			//采购单号、采购员
			$map['sn_code|subject'] = array('like','%'.$search.'%');
			$map['_logic'] = 'or';
			$where['_complex'] = $map;
		}

		//分页
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1;
		if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			cookie('listrows', $listrows, 3600 * 24 * 30);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = cookie('listrows') ? cookie('listrows') : 15;
			$params[] = "listrows=".$listrows;
		}
		$count = $d_sales->where($where)->count();
		$p_num = ceil($count/$listrows);
		if($p_num < $p) $p = $p_num;

		$order = 'sales_time desc';
		$list = $d_sales->where($where)->page($p, $listrows)->order($order)->select();
		foreach ($list as $key => $val) {
			$purchase = $this->d_purchase->where(array('purchase_id' => $val['customer_id']))->field('name, type_id')->find();
			$list[$key]['purchase_name'] = $purchase['name'];
			$list[$key]['supplier_id'] = $purchase['type_id'];
			$list[$key]['supplier_name'] = $m_supplier->where(array('supplier_id' => $purchase['type_id']))->getField('name');
			$list[$key]['sales_time'] = date('Y-m-d', $val['sales_time']);
			$list[$key]['creator_role_name'] = $d_user->get_full_name((int) $val['creator_role_id']);
			$list[$key]['is_checked'] = $val['is_checked'] ? '已审核' : '未审核';

			//审批信息
            $list[$key] += $d_exam->checkExamPermission(2, $val['sales_id']);
		}

		//pd_exam_type中对应的type_id值
		$this->type_id = 2;

		$this->list = $list;
		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		$this->display();
	}

	/**
	 * 采购退货添加
	 */
	public function return_goods_add(){
		if (IS_POST) {
			$d_sales = D('Sales');
			if ($res = $d_sales->addDataPurchase()) {
				if (!$res = $d_sales->addProductPurchase()) {
					$d_sales->delete_error_data();
				}
			}
			// $d_sales->delete_error_data();

			//保存初始审批信息
			$d_exam = D('Exam');
			$exam_data = $d_exam->examInitData((int) $_POST['process_status'], (int) $_POST['temp_id'], $d_sales->sales_id, 2);
			$exam_data['type_id'] = 2;
			$exam_data['order_id'] = $d_sales->sales_id;
			if (!$d_exam->addOrderExam($exam_data)) {
				$data = array('msg'=> '初始审批信息保存失败', 'status'=>2);
			}

			$this->ajaxReturn(array('msg' => $d_sales->msg, 'status'=> $res));
		} else {
			$purchase_id = (int) $_GET['purchase_id'];
			if ($purchase_id) {
				$exam_status = M('RExam')->where(array('type_id' => 1, 'order_id' => $purchase_id))->getField('exam_status');
				if ($exam_status != 2) {
					echo '<div class="alert alert-danger">采购单未审核，无法退货。</div>';
					die;
				}
				$purchase = $this->d_purchase->where(array('purchase_id' => $purchase_id))->field('name, type_id')->find();
				$supplier_name = M('Supplier')->where(array('supplier_id' => $purchase['type_id']))->getField('name');
				$this->purchase = array('purchase_id' => $purchase_id, 'purchase_name' => $purchase['name'], 'supplier_id' => $purchase['type_id'], 'supplier_name' => $supplier_name);
			}

			//查询审批流信息 process_status 审批开启状态，1开启，2禁用
			$exam_init_info = D('Exam')->initData(2);
			$this->process_status = $exam_init_info['process_status'];
			$this->process_list = $exam_init_info['process_list'];
			
			$max_id = M('Sales')->where(array('type' => 1))->count() + 1;
			$this->number = D('Config')->getValue('purchase_return_prefix') . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);
			$this->display('return_goods_edit');
		}
	}
	

	/**
	 * 采购退货详情
	 */
	public function return_goods_view(){
		$sales_id = (int) $_GET['id'];

		//权限判断
		if (!in_array(M('Sales')->where('sales_id = %d', $sales_id)->getField('owner_role_id'), getPerByAction(MODULE_NAME,ACTION_NAME))) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}

		$d_sales = D('Sales');
		$this->sales = $d_sales->getView($sales_id);
		$this->product_info_list = $d_sales->getProductList($sales_id);
		$this->stock_out_list = D('StockOut')->getListBySales($sales_id, 2);

		//pd_exam_type中对应的type_id值
		$this->type_id = 2;
		$this->order_id = $sales_id;
		$this->receivables = D('Finance')->ablesList(array('contract_id' => $sales_id, 'type' => 2), 'receivables');

		$this->display();
	}


	/**
	 * 采退单编辑
	 */
	public function return_goods_edit()
	{
		//权限判断
		if (!in_array(M('Sales')->where('sales_id = %d', (int) $_REQUEST['sales_id'])->getField('owner_role_id'), getPerByAction(MODULE_NAME,ACTION_NAME))) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}

		//根据审批状态判断是否有编辑权限
		$exam_info = D('Exam')->checkExamPermission(2, (int) $_REQUEST['sales_id']);
		if ($exam_info['exam_status'] == 1 || $exam_info['exam_status'] == 2) {
			echo '<div class="alert alert-error">已进入审批流程，无法编辑!</div>';
			exit;
		}
		$d_sales = D('Sales');
		if (IS_POST) {
			if ($res = $d_sales->editDataPurchase()) {
				$res = $d_sales->editProductPurchase();
			}
			//更新初始审批信息
			$d_exam = D('Exam');
			$exam_data = $d_exam->examInitData((int) $_POST['process_status'], (int) $_POST['temp_id'], (int) $_POST['sales_id'], 2);
			$exam_data['type_id'] = 2;
			$exam_data['order_id'] = (int) $_POST['sales_id'];
			if (!$d_exam->editOrderExam(array('type_id' => $exam_data['type_id'], 'order_id' => $exam_data['order_id']),$exam_data)) {
				$data = array('msg'=> '初始审批信息保存失败', 'status'=>2);
			}
			$this->ajaxReturn(array('msg' => $d_sales->msg, 'status'=> $res));
		} else {
			$sales_id = (int) $_GET['sales_id'];
			if ($sales_id == 0) {
				echo '<div class="alert alert-error">参数错误，刷新后重试！</div>';
				exit;
			}
			$sales =  $d_sales->getView($sales_id);
			$this->sales = $sales;

			$purchase = $this->d_purchase->where(array('purchase_id' => $sales['customer_id']))->field('name, type_id')->find();
			$supplier_name = M('Supplier')->where(array('supplier_id' => $purchase['type_id']))->getField('name');
			$this->purchase = array('purchase_id' => $sales['customer_id'], 'purchase_name' => $purchase['name'], 'supplier_id' => $purchase['type_id'], 'supplier_name' => $supplier_name);

			// 产品
			$d_stock = D('Stock');
			$d_stock_in = D('StockIn');
			$m_sales_product = M('SalesProduct');
			$this->d_purchase->purchase_id = $sales['customer_id'];
			$product_info_list = $this->d_purchase->getPurcharseProductInfoList();
			$return_count_list = D('Sales')->getPurchaseReturn($sales['customer_id']);		// 退货数
			$return_out_count_list = D('Sales')->getPurchaseReturnOut($sales['customer_id']);		// 退货已出库数
			foreach ($product_info_list as $key => $val) {
				$product_info_list[$key]['stock_count'] = $d_stock->getProductStock($val['product_info_id']);
				$product_info_list[$key]['purchase_stock_in_count'] = $d_stock_in->getPurchaseProductCount($sales['customer_id'], $val['product_info_id']);
				$return_count = $return_count_list[$val['product_info_id']] ?: 0;
				// 本次退货产品数
				$temp_data = $m_sales_product->where(array('sales_id' => $sales_id, 'product_info_id' => $val['product_info_id']))->field('amount, ori_price, unit_price, sales_product_id')->find();
				$product_info_list[$key]['sales_product_id'] = $temp_data['sales_product_id'];
				$product_info_list[$key]['amount'] = $temp_data['amount'] ?: 0;
				$product_info_list[$key]['ori_price'] = $temp_data['ori_price'] ?: $val['price_cost'];
				$product_info_list[$key]['unit_price'] = $temp_data['unit_price'] ?: $val['price_cost'];
				$product_info_list[$key]['return_count'] = $return_count - $temp_data['amount'];
				$return_out_count = $return_out_count_list[$val['product_info_id']] ?: 0;
				$product_info_list[$key]['return_out_count'] = $return_out_count;
				// SN在库数
				$sn_stock_count = D('Sn')->getSNlist(array(
					'purchase_id' => $sales['customer_id'], 
					'product_info_id' => $val['product_info_id'],
					'status' => 1,
					'field' => 'count(*) as count'
				));
				$product_info_list[$key]['sn_stock_count'] = $sn_stock_count[0]['count'] ?: 0;
			}
			$this->product_info_list = $product_info_list;

			//查询审批流信息 process_status 审批开启状态，1开启，2禁用
			$exam_init_info = D('Exam')->initData(2, $sales_id);
			$this->process_status = $exam_init_info['process_status'];
			$this->process_list = $exam_init_info['process_list'];
			$this->order_exam = $exam_init_info['order_exam'];

			$this->display();
		}
	}


	/**
	 * 采购汇总
	 */
	public function analytics()
	{
		$where = array('type' => 1);		// 1表示采购
		// 时间段搜索
		if($_GET['between_date']){
			$between_date = explode(' - ',trim($_GET['between_date']));
			$start_time = $between_date[0] ? strtotime(date('Y-m-d 23:59:59', strtotime($between_date[0]))) : strtotime(date('Y-m-01 00:00:00'));
			$end_time = $between_date[1] ? strtotime(date('Y-m-d 23:59:59', strtotime($between_date[1]))) : strtotime(date('Y-m-d 23:59:59',time()));
			$params[] = "between_date=" . $_GET['between_date'];
		}else{
			$start_time = strtotime(date('Y-m-01 00:00:00'));
			$end_time = strtotime(date('Y-m-d H:i:s'));
		}
		$this->between_date = $_GET['between_date'] ? trim($_GET['between_date']) : date('Y-m-01').' - '.date('Y-m-d');
		$this->start_date = date('Y-m-d',$start_time);
		$this->end_date = date('Y-m-d',$end_time);
		$this->daterange = daterange();
		$where['purchase_time'] = array('between',array($start_time, $end_time));

		// 供应商
		if ($supplier_id = (int) $_GET['supplier_id']) {
			$this->supplier_id = $supplier_id;
			$this->supplier_name = M('Supplier')->where(array('supplier_id' => $supplier_id))->getField('name');
			$where['type_id'] = $supplier_id;
			$params[] = "supplier_id=" . $supplier_id;
		}
		$pruchase_id_list = M('Purchase')->where($where)->getField('purchase_id', true);
		$r_exam = M('RExam');
		foreach ($pruchase_id_list as $key => $val) {
			if ($r_exam->where(array('type_id' => 1, 'order_id' => $val))->getField('exam_status') != 2) {
				unset($pruchase_id_list[$key]);
			}
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

		//产品分类列表
		$category_list = D('Product')->getCategoryList();
		$this->assign('category_list', $category_list);
		$product_info_where = array();
		if ($category_id = (int) $_GET['category_id']) {
			$product_info_where = array('category_id' => $category_id);
		}

		$count = D('ProductInfoView')->where($product_info_where)->count();
		import("@.ORG.Page");
		$Page = new Page($count, $listrows);
		$Page->parameter = implode('&', $params);
		$this->count = $count;
		$this->listrows = $listrows;
		$this->page = $Page->show();

		$list = D('ProductInfoView')->where($product_info_where)->page($p, $listrows)->field('product_info_id')->select();
		$d_product_info = D('ProductInfo');
		$m_purchase_product = M('PurchaseProduct');
		foreach ($list as $key => $val) {
			$list[$key] += $d_product_info->getNameSpec($val['product_info_id']);
			$temp_count = $m_purchase_product->where(array('purchase_id' => array('IN', $pruchase_id_list), 'product_info_id' => $val['product_info_id']))->sum('count');
			$list[$key]['count'] = $temp_count ?: 0;
			$temp_price = $m_purchase_product->where(array('purchase_id' => array('IN', $pruchase_id_list), 'product_info_id' => $val['product_info_id']))->sum('price_discount');
			$list[$key]['price'] = $temp_price ?: 0;
		};

		$this->list = $list;

		$this->display();
	}


	/**
	 * 产品添加SN
	 */
	public function dialogAddSN()
	{
		if (IS_POST) {
			$d_SN = D('Sn');
			$data = $_POST;
			$data['price_cost'] = M('purchase_product')->where(array('purchase_id' => $data['purchase_id'], 'product_info_id' => $data['product_info_id']))->getField('price_discount');
			if (!$data['price_cost']) $this->ajaxReturn(array('msg' => '添加失败', 'status' => 0));
			$res = $d_SN->addData($data);
			if ($res) {
				$this->ajaxReturn(array('msg' => '添加成功', 'status' => 1));
			} else {
				$this->ajaxReturn(array('msg' => '添加失败', 'status' => 0));
			}
		} else {
			$this->product_info_id = (int) $_GET['product_info_id'];		// 产品ID
			$this->purchase_id = (int) $_GET['purchase_id'];
			$this->name = $_GET['name'];		// 产品名称
			$this->list = D('Sn')->getSNList(array('product_info_id' => $this->product_info_id, 'status' => 0));
			$this->display('dialog_add_SN');
		}
	}


	/**
	 * 删除SN
	 */
	public function deleteSN()
	{
		$d_sn = D('Sn');
		$sn_id = (int) $_REQUEST['sn_id'];
		if ($d_sn->deleteSN($sn_id)) {
			$this->ajaxReturn(array('status' => 1));
		} else {
			$this->ajaxReturn(array('msg' => $d_sn->msg, 'status' => 0));
		}
	}


	/**
	 * SN导入模板下载
	 */
	public function excel_templet_download()
	{
		download('./Public/excelmodel/pd_SN_download.xlsx');
	}


	/**
	 * excel导入
	 */
	public function excelImport()
	{
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
					$data = $this->readSNExcel($savepath);
					$this->ajaxReturn(array('data' => $data, 'msg' => 'success', 'status' => 1));
				}else{
					$this->ajaxReturn(array('msg' => '导入失败', 'status' => 0));
				}
			}else{
				alert('error', L('UPLOAD_FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	

	/**
	 * 读取导入SN文件
	 */
	private function readSNExcel($path)
	{
		import("ORG.PHPExcel.PHPExcel");
		$PHPExcel = new PHPExcel();
		$PHPReader = new PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($path)){
			$PHPReader = new PHPExcel_Reader_Excel5();
		}
		$PHPExcel = $PHPReader->load($path);
		$currentSheet = $PHPExcel->getSheet(0);
		$allRow = $currentSheet->getHighestRow();
		$data = array();
		for ($i = 2; $i <= $allRow; $i ++) {
			$data[] = $currentSheet->getCell('A' . $i)->getValue();
		}
		return $data;
	}


	/**
	 * 入库选择SN
	 */
	public function stockInSn()
	{
		$this->product_info_id = (int) $_GET['product_info_id'];		// 产品ID
		$this->name = $_GET['name'];		// 产品名称
		$this->surplus = $_GET['surplus'];
		$this->sn_ids = empty($_GET['sn_ids']) ? array() : explode(',', $_GET['sn_ids']);
		$list = D('Sn')->getSNList(array('product_info_id' => $this->product_info_id, 'status' => 0));
		if ((int) $_GET['has_return'] > 0) {
			$sales_id_list = M('sales')->where(array('customer_id' => $this->purchase_id, 'type' => 1))->getField('sales_id', true);
			if ($return_list = D('Sn')->getSNList(array('field' => 'sn_id,sn,status', 'status' => 2, 'product_info_id' => $this->product_info_id, array('IN', $sales_id_list)))) {
				$list = array_merge($list, $return_list);
			}
		}
		$this->list = $list;
		$this->display('dialog_select_SN');
	}


	/**
	 * 入库产品SN码查看
	 */
	public function stockInSnView()
	{
		$stock_in_id = (int) $_GET['stock_in_id'];
		$product_info_id = (int) $_GET['product_info_id'];
		$this->name = $_GET['name'];
		$this->list = D('Sn')->getSNList(array('stock_in_id' => $stock_in_id, 'product_info_id' => $product_info_id));
		$this->display('dialog_view_SN');
	}


	/**
	 * 弹窗展示采购单 未完成
	 */
	public function dialogList()
	{
		$p = $_GET['p'] ?: 1;
		$this->purchase_id = (int) $_GET['purchase_id'];

		$where['type'] = $_GET['type'] ? intval($_GET['type']) : 1;
		if ($_GET['field']) {
			if ($_GET['field'] == 'owner_role_name') {
				$role_id_list = M('User')->where(array('full_name' => array('LIKE', $_GET['value'])))->getField('role_id', true);
				$where['owner_role_id'] = array('IN', $role_id_list);
			} else {
				$where[$_GET['field']] = array('LIKE', '%' . $_GET['value'] . '%');
			}
		}
		$this->list = $this->d_purchase->getPurchaseList($p.',10', $where);
		$count = $this->d_purchase->count;
		import("@.ORG.Page");
		$Page = new Page($count, 10);
		$this->page = $Page->show();
		$this->display('dialog_list');
	}


	/**
	 * 采购退货单产品列表
	 */
	public function returnProductList()
	{
		$purchase_id = (int) $_POST['purchase_id'];
		// $warehouse_id = (int) $_POST['warehouse_id'];
		if ($purchase_id) {
			$d_stock = D('Stock');
			$d_stock_in = D('StockIn');
			$this->d_purchase->purchase_id = $purchase_id;
			$supplier = $this->d_purchase->getSupplier();
			$product_info_list = $this->d_purchase->getPurcharseProductInfoList();
			foreach ($product_info_list as $key => $val) {
				$product_info_list[$key]['stock_count'] = $d_stock->getProductStock($val['product_info_id']);
				$product_info_list[$key]['purchase_stock_in_count'] = $d_stock_in->getPurchaseProductCount($purchase_id, $val['product_info_id']);
				// SN在库数
				$sn_stock_count = D('Sn')->getSNlist(array('product_info_id' => $val['product_info_id'], 'purchase_id' => $purchase_id, 'status' => 1, 'field' => 'count(*) as count'));
				$product_info_list[$key]['sn_stock_count'] = $sn_stock_count[0]['count'] ?: 0;
			}
			// 退货数
			$return_count_list = D('Sales')->getPurchaseReturn($purchase_id);
			$return_count_list = $return_count_list ?: array();
			// 退货已出库数
			$return_out_count_list = D('Sales')->getPurchaseReturnOut($purchase_id);
			$return_out_count_list = $return_out_count_list ?: array();
			
			$data = array(
				'supplier' => $supplier,
				'product' => $product_info_list,
				'return_count_list' => $return_count_list,
				'return_out_count_list' => $return_out_count_list,
			);
			$this->ajaxReturn(array('data' => $data, 'msg' => null, 'status' => 1));
		} else {
			$this->ajaxReturn(array('msg' => '参数错误', 'status' => 0));
		}
	}


	/**
	 * 退货SN码选择
	 */
	public function returnGoodSnView()
	{
		$purchase_id = (int) $_GET['purchase_id'];
		$this->product_info_id = (int) $_GET['product_info_id'];
		$this->surplus = (int) $_GET['surplus'];
		$this->name = $_GET['name'];
		$this->sn_ids = empty($_GET['sn_ids']) ? array() : explode(',', $_GET['sn_ids']);
		$this->list = D('Sn')->getSNList(array('status' => 1, 'product_info_id' => $this->product_info_id, 'purchase_id' => $purchase_id));
		$this->returnGood = true;
		$this->display('dialog_select_SN');
	}


	/**
	 * 退货单列表dialog
	 */
	public function purchase_return_list_dialog()
	{
		$id = (int) $_GET['id'];
		$supplier_id = (int) $_GET['supplier_id'];
		$d_sales = D('Sales');
		$where = array('type' => 1);
		$p = $_GET['p'] ?: 1;
		$page_size = 10;
		if ($supplier_id) {
			$pruchase_id_list = M('Purchase')->where(array('type_id' => $supplier_id, 'type' => 1))->getField('purchase_id', true);
			$where['customer_id'] = array('IN', $pruchase_id_list);
		}
		$res = $d_sales->getList(array('subject', 'sales_id', 'sn_code', 'customer_id', 'owner_role_id', 'sales_time', 'sales_price') , $where, $p . ',' . $page_size);
		$this->list = $res['list'];
		$this->page = array('count' => $res['count'], 'p' => $p, 'size' => ceil($res['count'] / $page_size), 'url' => U('purchase/purchase_return_list_dialog', array('id' => $id, 'supplier_id' => $supplier_id)));
		$this->display();
	}


	/**
	 * 采购单删除【只能删除还审核且未进行入库等操作的采购单】
	 * @param $type_id 1采购单 3销售退货单
	 * @author lee
	 */
	public function delete($type_id = 1)
	{
		$purchase_ids = $_POST['purchase_ids'];
		foreach ($purchase_ids as $k => $v) {
			if(order_can_del($type_id, $v) == false){
				$flag = 1;
				break;
			}
		}
		if ($flag == 1) {
			$data['info'] = '只能删除待审或驳回且未进行入库操作的记录！请重新选择';
			$data['status'] = 2;
		} else {
			$d_purchase = D('Purchase');
			foreach ($purchase_ids as $k => $v) {
				$d_purchase->purchase_id = $v;
				$d_purchase->deleteData();
			}
			$data['info'] = '删除成功';
			$data['status'] = 1;
		}
		$this->ajaxReturn($data);
	}


	/**
	 * 采购退货单删除
	 * @author lee
	 */
	public function return_delete()
	{
		$sales_ids = $_POST['sales_ids'];
		foreach ($sales_ids as $k => $v) {
			if(order_can_del(2, $v) == false){
				$flag = 1;
				break;
			}
		}
		if ($flag == 1) {
			$data['info'] = '只能删除待审或驳回且未进行入库操作的记录！请重新选择';
			$data['status'] = 2;
		} else {
			$d_purchase = D('Purchase');
			foreach ($sales_ids as $k => $v) {
				$d_purchase->deleteReturn($v);
			}
			$data['info'] = '删除成功';
			$data['status'] = 1;
		}
		$this->ajaxReturn($data);
	}


	/**
	 * 导出采购单
	 * @param file_name 导出文件名称
	 * @param field_list 导出字段列表
	 * @param name_list 导出表头列表
	 * @param total_export_count 导出总行数
	 * @param export_tab 导出数据相关的数据表名称，后面需要根据此名称做处理以便导出实际数据
	 * @param where 导出数据的where条件
	 * @author lee
	 **/
	public function export_purchase()
	{
		$file_name = '采购单_'.date('Ymd');
		$field_list = array('number','name');
		$name_list = array('采购单编号','标题');

		$where = array();
		$total_export_count = M('Purchase')->where($where)->count();
		
		$export_tab = 'purchase';
		exportCsv($file_name, $field_list, $name_list, $total_export_count, $export_tab, $where);
	}



}