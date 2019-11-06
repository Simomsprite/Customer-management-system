<?php
/**
 *合同订单相关
 **/
class SalesAction extends Action {
	/**
	 *用于判断权限
	 *@permission 无限制
	 *@allow 登录用户可访问
	 *@other 其他根据系统设置
	 **/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('addstockout', 'stockoutproductlist', 'stock_list', 'product_list', 'product_list', 'stockoutsnselect', 'stockoutsnview', 'in_sn_list', 'out_sn_select', 'contract_sn_list')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
	}

	/**
	 * 出库单创建
	 * @param 
	 * @author 
	 * @return 
	 */
	public function addStockOut() {
		$sales_id = (int) $_REQUEST['sales_id'];
		if (isset($_REQUEST['contract_id']) && $_REQUEST['contract_id']) {
			$contract_id = (int) $_REQUEST['contract_id'];
			// 审批状态
			if (M('Contract')->where(array('contract_id' => $contract_id))->getField('is_checked') != 1) {
				$msg = '合同未审核，不可出库。';
				if (IS_POST) {
					$this->ajaxReturn(array('msg' => $msg, 'status' => 0));
				} else {
					echo '<div class="alert alert-error">' . $msg . '</div>';
					exit;
				}
			}
			$type = 1;
		} else {
			if (D('Exam')->orderStatus(array('type_id' => 2, 'order_id' => $sales_id)) !== 2) {
				$msg = '采购退货单未审核，不可出库。';
				if (IS_POST) {
					$this->ajaxReturn(array('msg' => $msg, 'status' => 0));
				} else {
					echo '<div class="alert alert-error">' . $msg . '</div>';
					exit;
				}
			}
			$type = 2;
		}
		$d_warehouse = D('Warehouse');
		if ($this->isPost()) {
			if (!$d_warehouse->isStorehouse($_POST['warehouse_id'])) {
				$this->ajaxReturn(array('msg' => $d_warehouse->msg, 'status' => 0));
			}
			$d_stock_out = D('StockOut');
			// 1 表示销售  2 表示采购退货
			if ($status = $d_stock_out->addData($type, 'sales_id')) {
				$status = $d_stock_out->addProduct();
				if (!$status) {
					$d_stock_out->deleteData();
				}
			}
			$msg = $d_stock_out->msg;
			$this->ajaxReturn(array('msg' => $msg, 'status' => (int) $status));
		} else {
			// 是否允许超库存销售
			$this->over_stock_sales = M('config')->where(array('name' => 'over_stock_sales'))->getField('value');
			$this->contract_id = $contract_id;
			$this->sales_id = $sales_id;
			$this->warehouse_list = D('Warehouse')->selectList();
			
			$max_id = M('StockOut')->max('stock_out_id') + 1;
			$this->number = D('Config')->getValue('stock_out_prefix') . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);
			$this->display('add_stock_out');
		}
	}


	/**
	 * 出库单产品列表
	 */
	public function StockOutProductList()
	{
		if (IS_AJAX) {
			$d_sales = D('Sales');
			$warehouse_id = $_REQUEST['warehouse_id'];
			$sales_id = $_REQUEST['sales_id'];
			$contract_id = M('RContractSales')->where(array('sales_id' => $sales_id))->getField('contract_id');
			if ($contract_id) {
				$type = 1;
			} else {
				$type = 2;
			}
			$product_list = $d_sales->productDataList($sales_id, $warehouse_id, $type);
			if ($product_list) {
				$this->ajaxReturn(array('data' => $product_list, 'status' => 1));
			} else {
				$this->ajaxReturn(array('msg' => '没有相关产品信息！', 'status' => 0));
			}
		}
	}


	/**
	 * 合同订单详情(出库记录)
	 */
	public function stock_list() 
	{
		$contract_id = (int) $_GET['contract_id'];
		if (!$contract_id) {
			echo '<div class="alert alert-error">参数错误，刷新后重试！</div>';
			exit;
		}
		// 审批状态
		if (M('Contract')->where(array('contract_id' => $contract_id))->getField('is_checked') != 1) {
			echo '<div class="alert alert-error">合同未审核，不可出库。</div>';
			exit;
		}
		$sales_id = M('RContractSales')->where(array('contract_id' => $contract_id))->getField('sales_id');

		// 出库记录
		$d_stock_out = D('StockOut');
		$this->stock_out_list = $d_stock_out->getListBySales($sales_id);
		//dd($this->stock_out_list);

		//是否有销售退货单
		$this->return_count = M('Purchase')->where('type = 2 and type_id = %d', $sales_id)->count();

		$this->display();
	}


	/**
	 * 销售退货单列表
	 */
	public function return_index()
	{
		$d_sales = D('Sales');
		$m_user = M('User');

		//待审核的
		if (intval($_GET['unchecked']) > 0) {
			$purchase_ids = D('Exam')->getOrderIds(array('type_id' => 3, 'exam_status' => array('lt', 2)));
			$where['purchase_id'] = array('in', $purchase_ids ?: '');
			$params[] = "unchecked=".intval($_GET['unchecked']);
		}

		//销售id
		if (intval($_GET['sales_id']) > 0) {
			$where['type_id'] = intval($_GET['sales_id']);
		}
		$where['is_deleted'] = 0;
		//简单查询
		$search = $this->_get('search','trim');
		if ($search) {
			//退货单号、退货名称
			$map['number|name'] = array('like','%'.$search.'%');
			
			//负责人
			$where_role['_string'] = 'full_name like %'.$search.'%';
			$role_ids = $m_user->where($where_role)->getField('role_id', true);
			$map['owner_role_id'] = array('in', $role_ids ?: '');

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

		$list = $d_sales->getReturnList($p, $listrows, $where, 'update_time desc');
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
		$this->type_id = 3;

		$this->display();
	}


	/**
	 * 退货单创建
	 */
	public function return_add() {
		if ($this->isPost()) {
			$d_sales = D('Sales');
            $purchase_id = $d_sales->addReturnData($_POST);
            if ($purchase_id) {

            	//保存初始审批信息
				$d_exam = D('Exam');
				$exam_data = $d_exam->examInitData((int) $_POST['process_status'], (int) $_POST['temp_id'], $purchase_id, 3);
				$exam_data['type_id'] = 3;
				$exam_data['order_id'] = $purchase_id;
				if (!$d_exam->addOrderExam($exam_data)) {
					$data = array('msg'=> '初始审批信息保存失败', 'status'=>2);
				}

            	$data = array('info' => $d_sales->msg, 'status' => 1);
            } else {
                $data = array('info' => $d_sales->msg, 'status' => 2);
            }
            $this->ajaxReturn($data);
		} else {
			$contract_id = intval($_GET['contract_id']);
			if ($contract_id) {
				$contract = M('Contract')->where('contract_id = %d', $contract_id)->field('contract_id,contract_name,number')->find();
				$this->assign('contract',$contract);
			}

			$max_id = M('Purchase')->where(array('type' => 2))->count() + 1;
			$this->number = D('Config')->getValue('sales_return_prefix') . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);


			//查询审批流信息 process_status 审批开启状态，1开启，2禁用
			$exam_init_info = D('Exam')->initData(3);
			$this->process_status = $exam_init_info['process_status'];
			$this->process_list = $exam_init_info['process_list'];

			$this->display();
		}
	}


	/**
	 * 获取合同（销售单）关联的产品列表
	 */
	public function product_list(){
		$contract_id = intval($_POST['contract_id']);
		//产品列表
		$product_list = D('Contract')->getProductList($contract_id);
		// 退货入库数
		$return_in_count_list = D('Purchase')->getSalesReturnIn($product_list['sales']['sales_id']);
		// 退货产品汇总数
		$return_count_list = D('Purchase')->getSalesReturn($product_list['sales']['sales_id']);

		$data = array(
			'product_list' => $product_list['product_list'],
			'status' => 1,
			'return_count_list' => $return_count_list,
			'return_in_count_list' => $return_in_count_list
		);
		$this->ajaxReturn($data);
	}

	/**
	 * 退货单编辑
	 */
	public function return_edit()
	{
		//权限判断
		if (!in_array(M('Purchase')->where('purchase_id = %d', $_REQUEST['purchase_id'])->getField('owner_role_id'), getPerByAction(MODULE_NAME,ACTION_NAME))) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}

		//根据审批状态判断是否有编辑权限
		$exam_info = D('Exam')->checkExamPermission(3, (int) $_REQUEST['purchase_id']);
		if ($exam_info['exam_status'] == 1 || $exam_info['exam_status'] == 2) {
			echo '<div class="alert alert-error">已进入审批流程，无法编辑</div>';
			exit;
		}

		if ($this->isPost()) {
			$d_sales = D('Sales');
            $res = $d_sales->updateReturnData($_POST);
            if ($res) {

            	//更新初始审批信息
				$d_exam = D('Exam');
				$exam_data = $d_exam->examInitData((int) $_POST['process_status'], (int) $_POST['temp_id'], (int) $_POST['purchase_id'], 3);
				$exam_data['type_id'] = 3;
				$exam_data['order_id'] = (int) $_POST['purchase_id'];
				if (!$d_exam->editOrderExam(array('type_id' => $exam_data['type_id'], 'order_id' => $exam_data['order_id']),$exam_data)) {
					$data = array('msg'=> '初始审批信息保存失败', 'status'=>2);
				}

            	$data = array('info' => $d_sales->msg,'status' => 1,);
            } else {
                $data = array('info' => $d_sales->msg,'status' => 2,);
            }
            $this->ajaxReturn($data);
		} else {
			$purchase_id = intval($_GET['purchase_id']);
			$purchase = M('Purchase')->where('purchase_id = %d', $purchase_id)->find();
			$purchase['owner_role_name'] = D('User')->get_full_name((int) $purchase['owner_role_id']);

			$purchase['product_list'] = M('PurchaseProduct')->where('purchase_id = %d', $purchase_id)->field('product_info_id,price_cost,count')->select();
			$this->assign('purchase', $purchase);
			//合同信息
			$sales_id = M('Purchase')->where('purchase_id = %d', $purchase_id)->getField('type_id');
			$this->assign('contract', D('Contract')->getContractBySalesId($sales_id));

			//查询审批流信息 process_status 审批开启状态，1开启，2禁用
			$exam_init_info = D('Exam')->initData(3, $purchase_id);
			$this->process_status = $exam_init_info['process_status'];
			$this->process_list = $exam_init_info['process_list'];
			$this->order_exam = $exam_init_info['order_exam'];

			$this->display();
		}
	}


	/**
	 * 销售退货单详情
	 * 实为采购单详情代码逻辑，因为需要进行权限判断处理，所以又复制了一份
	 */
	public function return_view()
	{
		$m_purchase_product = M('purchaseProduct');
		$m_supplier = M('Supplier');
		$m_user = M('User');
		$d_purchase = D('Purchase');

		$purchase_id = $this->_request('id','intval');

		// 采购单信息
		$purchase = $d_purchase->getView($purchase_id);

		//权限判断
		if (!in_array($purchase['owner_role_id'], getPerByAction(MODULE_NAME,ACTION_NAME))) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}

		// 关联产品信息  
		$purchase['product_info_list'] = $d_purchase->getPurcharseProductInfoList();  // 获取产品信息

		// 员工信息
		$user_list = D('RoleView')->where('status = 1')->select();
		$this->assign('user_list', $user_list);

		// 入库记录
		$d_stock_in = D('StockIn');
		$this->stock_in_list = $d_stock_in->getPurchaseStockInList($purchase_id);

		// 入库状态
		$this->stock_in_status = $d_stock_in->stock_in_status;
		
		$this->purchase = $purchase;

		//根据type值显示不同的文字，type 1采购单 2销售退货单
		$this->type_name = $d_purchase->getTypeName('purchase', $purchase['type']);

		//pd_exam_type中对应的type_id值
		if ($purchase['type'] == 1) {
			$this->type_id = 1;
		} else if ($purchase['type'] == 2){
			$this->type_id = 3;
		}
		$this->order_id = $purchase['purchase_id'];
		
		$this->display('Puchase:view');
	}


	/**
	 * 出库SN码选择
	 */
	public function stockOutSnSelect()
	{
		$warehouse_id = (int) $_GET['warehouse_id'];
		if ($warehouse_id == 0) {
			echo '<div class="alert alert-error">参数错误, 刷新后重试！</div>';
			exit;
		}
		$this->product_info_id = (int) $_GET['product_info_id'];		// 产品ID
		$this->name = $_GET['name'];		// 产品名称
		$this->surplus = $_GET['surplus'];
		$this->sn_ids = empty($_GET['sn_ids']) ? array() : explode(',', $_GET['sn_ids']);
		$this->list = D('Sn')->getSNList(array('product_info_id' => $this->product_info_id, 'warehouse_id' => $warehouse_id, 'status' => 1));
		$this->display('Purchase:dialog_select_SN');
	}


	/**
	 * 出库SN码查看
	 */
	public function stockOutSnView()
	{
		$sales_id = (int) $_GET['sales_id'];
		$stock_out_id = (int) $_GET['stock_out_id'];
		$product_info_id = (int) $_GET['product_info_id'];
		$where = array('product_info_id' => $product_info_id);
		if ($sales_id != 0) {
			$where['sales_id'] = $sales_id;
		}
		if ($stock_out_id != 0) {
			$where['stock_out_id'] = $stock_out_id;
		}
		$this->name = $_GET['name'];
		$this->list = D('Sn')->getSNList($where);
		$this->display('Purchase:dialog_view_SN');
	}

	/**
	 * 销售退货单查看已入库sn
	 */
	public function in_sn_list()
	{
		$product_info_id = intval($_GET['product_info_id']);
		$purchase_id = intval($_GET['purchase_id']);

		$this->name = $_GET['name'];
		$this->list = D('Sn')->getSNList(array('purchase_id' => $purchase_id, 'product_info_id' => $product_info_id));
		$this->display('Purchase:dialog_view_SN');
	}

	/**
	 * 退货单入库操作
	 * 选择相关销售单已出库的SN码
	 */
	public function out_sn_select()
	{
		$this->product_info_id = (int) $_GET['product_info_id'];		// 产品ID
		$sales_id = (int) $_GET['sales_id'];
		$this->name = $_GET['name'];
		$this->surplus = $_GET['surplus'];
		$this->sn_ids = empty($_GET['sn_ids']) ? array() : explode(',', $_GET['sn_ids']);
		$this->list = D('Sn')->getSNList(array('sales_id' => $sales_id, 'product_info_id' => $this->product_info_id, 'status' => 2));
		$this->display('Purchase:dialog_select_SN');
	}


	/**
	 * 合同查看已出库sn
	 */
	public function contract_sn_list(){
		$sales_id = (int) $_GET['sales_id'];
		$product_info_id = (int) $_GET['product_info_id'];
		$this->list = D('Sn')->getSNList(array('sales_id' => $sales_id, 'product_info_id' => $product_info_id));
		$this->name = $_GET['name'];
		$this->display('Purchase:dialog_view_SN');
	}


	/**
	 * 删除销售退货记录
	 * @author lee
	 */
	public function return_delete()
	{
		$obj_purchase = A('Purchase');
		$obj_purchase->delete(3);
	}

}
