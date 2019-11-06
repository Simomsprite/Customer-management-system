<?php
/**
*任务模块
*
**/
class StockAction extends Action{
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('transfer_out', 'transfer_in', 'warehouse_stock_list')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
		$this->alert = parseAlert();
	}


	/**
	* 库存首页
	**/
	public function index(){
		// state 0正常 1下架 2删除 3不可用 
		$where['product_info.state'] = array('neq', 3);

		$category_id = $this->_get('category_id','intval');
		if ($category_id > 0) {
			$where['category_id'] = $category_id;
		}

		$warehouse_id = $this->_get('warehouse_id','intval');

		//根据仓库负责，判断是否库存权限
		if (!session('?admin')) {
			$warehouse_ids = M('Warehouse')->where(array('owner_role_id', array('like','%,'.session('role_id').',%')))->getField('warehouse_id', true);
			if ($warehouse_id > 0) {
				$where['warehouse_id'] = $warehouse_id;
			} else {
				$where['warehouse_id'] = array('in', $warehouse_ids);
			}
			$params[] = 'warehouse_id=' . $_GET['warehouse_id'];
		} else {
			if ($warehouse_id > 0) {
				$where['warehouse_id'] = $warehouse_id;
				$params[] = 'warehouse_id=' . $_GET['warehouse_id'];
			}
		}

		//普通搜索
		$search = $this->_get('search','trim');
		if ($search) {
			//商品名称
			$map['product.name'] = array('like','%'.$search.'%');

			//商品编码或条形码
			$where_product_info['number|bar_code'] = array('like','%'.$search.'%');
			$product_info_ids = D('ProductInfo')->where($where_product_info)->getField('product_info_id', true) ?: array();
			//规格
			$where_product_spec['spec_value'] = array('like','%'.$search.'%');
			$product_info_ids2 = D('ProductSpecValue')->where($where_product_spec)->getField('product_info_id', true) ?: array();
			$map['product_info.product_info_id'] = array('in', array_unique(array_merge($product_info_ids, $product_info_ids2)) ?: '');

			$map['_logic'] = 'or';
			$where['_complex'] = $map;
			$params[] = 'search=' . $_GET['search'];
		}

		// 库存数筛选
		$_GET['filter_count'] = isset($_GET['filter_count']) ? $_GET['filter_count'] : 'gt'; // 默认搜索大于0的库存
		if (in_array($_GET['filter_count'], array('eq', 'lt', 'gt'))) {
			$where['count'] = array($_GET['filter_count'], 0);
			$params[] = 'filter_count='.$_GET['filter_count'];
		}

		// 库存数范围 筛选
		if (in_array($_GET['over_range'], array('upper', 'lower', 'both', 'not'))) {
			switch ($_GET['over_range']) {
				case 'upper':
					$where['_string'] = 'count >= upper_limit';
					break;
				case 'lower':
					$where['_string'] = 'count <= lower_limit';
					break;
				case 'both':
					$where['_string'] = 'count >= upper_limit || count <= lower_limit';
					break;
				case 'not':
					$where['_string'] = 'count < upper_limit && count > lower_limit';
					break;
			}
			$params[] = 'over_range='.$_GET['over_range'];
		}

		// 排序
		$order = '';
		if ($_GET['order_type'] == 'asc') {
			$order = $_GET['order_field'] . ' asc';
			$params[] = 'order_type=asc';
			$params[] = 'order_field='.$_GET['order_field'];
		} elseif ($_GET['order_type'] == 'desc') {
			$order = $_GET['order_field'] . ' desc';
			$params[] = 'order_type=desc';
			$params[] = 'order_field='.$_GET['order_field'];
		}

		// 导出
		if (trim($_GET['act']) == 'export') {
			if(!checkPerByAction('stock','export')){
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
			if ($_GET['ids']) {
				$where = array('stock_id' => array('IN', $_GET['ids']));
			}
			$d_stock = D('Stock');
			$count = D('StockView')->where($where)->count();
			$table_head = array(
				'field' => array(
					'product_name' => '商品名称',
					'number' => '商品编码',
					'bar_code' => '商品条形码',
					'spec_list.spec' => '规格',
					'standard' => '单位',
					'warehouse_name' => '所属仓库',
					'lower_limit' => '库存下限',
					'upper_limit' => '库存上限',
					'count' => '库存量'
				)
			);
			csvExport('pdcrm-商品库存导出', $table_head, $count, function($page) use ($d_stock, $order, $where) {
				return $d_stock->getStockViewList($where, $page, null, $order);
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
		$count = D('StockView')->where($where)->count();
		$p_num = ceil($count/$listrows);
		if($p_num < $p) $p = $p_num;

		$list = D('Stock')->getStockViewList($where, $p, $listrows, $order);

		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		$this->assign('listrows', $listrows);

		//产品分类列表
		$category_list = D('Product')->getCategoryList();
		$this->assign('category_list', $category_list);

		//仓库列表【只查启用的】
		$warehouse_list = D('Warehouse')->getWarehouseList(1);
		$this->assign('warehouse_list', $warehouse_list);

		$this->assign('list',$list);
		
		$this->display();
	}

	/**
	 * 编辑库存信息
	 */
	public function edit(){
		if (IS_POST) {
			$d_stock = D('Stock');
			$stock_id = intval($_POST['stock_id']);
			$res = $d_stock->saveStock(array('stock_id'=>$stock_id), $_POST);
			if ($res) {
				$data = array('info' => $d_stock->msg, 'status' => 1);
			} else {
				$data = array('info' => $d_stock->msg, 'status' => 2);
			}
			$this->ajaxReturn($data);
		}
	}


	/**
	* 库存调拨首页
	**/
	public function transfer(){
		$d_stock = D('Stock');
		$d_search = D('Search');

		// 搜索
		$res = $d_search->getWhere($_GET);

		// 搜索where条件
		if ($res['where']) $where = $res['where'];

		// 记录分页参数
		$params = $res['page_params'];

		// 记录搜索条件
		$this->fields_search = $res['fields_search'];

		// 记录特殊搜索条件
		$this->single_list = $res['single_list'];

		// 调出仓
		$out_warehouse_id = $this->_get('out_warehouse_id','intval');
		if ($out_warehouse_id > 0) {
			$where['out_warehouse_id'] = $out_warehouse_id;
		}
		// 调入仓
		$in_warehouse_id = $this->_get('in_warehouse_id','intval');
		if ($in_warehouse_id > 0) {
			$where['in_warehouse_id'] = $in_warehouse_id;
		}

		// 搜索审核状态，特殊处理【优先级要大于从待办事项点击的】
		if (isset($where['exam_status'])) {
			$transfer_ids = D('Exam')->getOrderIds(array('type_id' => 4, 'exam_status' => $where['exam_status']));
			$where['transfer_id'] = array('in', $transfer_ids ?: '');
			unset($where['exam_status']);
		} else if (intval($_GET['unchecked']) > 0){
			// 从【待办事项】点击查询待审核的
			$transfer_ids = D('Exam')->getOrderIds(array('type_id' => 4, 'exam_status' => array('lt', 2)));
			$where['transfer_id'] = array('in', $transfer_ids ?: '');
		}

		// 有role_id的查询，需要处理权限范围问题，重新整理where条件
		$where = $d_search->roleWhere('onwer_role_id', $where, getPerByAction(MODULE_NAME, ACTION_NAME));

		// 分页
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1;
		if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			cookie('listrows', $listrows, 3600 * 24 * 30);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = cookie('listrows') ? cookie('listrows') : 15;
			$params[] = "listrows=".$listrows;
		}
		$count = M('Transfer')->where($where)->count();
		$p_num = ceil($count/$listrows);
		if($p_num < $p) $p = $p_num;

		$list = $d_stock->getTransferList($where, $p, $listrows, 'create_time desc');
//p($list);
		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		$this->assign('listrows', $listrows);

		//仓库列表
		$warehouse_list = D('Warehouse')->getWarehouseList();
		$this->assign('warehouse_list', $warehouse_list);

		// 所有可搜索的字段
		$this->field_list = $d_stock->transfer_search_field();

		//pd_exam_type中对应的type_id值
		$this->type_id = 4;

		$this->assign('list',$list);
		$this->display();
	}

	/**
	 * 库存调拨新增
	 * @author lee
	 */
	public function transfer_add(){
		if (IS_POST) {
			$d_stock = D('Stock');
			$transfer_id = D('Stock')->addTransfer($_POST);
			if ($transfer_id) {

				//保存初始审批信息
				$d_exam = D('Exam');
				$exam_data = $d_exam->examInitData((int) $_POST['process_status'], (int) $_POST['temp_id'], $transfer_id, 4);
				$exam_data['type_id'] = 4;
				$exam_data['order_id'] = $transfer_id;
				if (!$d_exam->addOrderExam($exam_data)) {
					$data = array('msg'=> '初始审批信息保存失败', 'status'=>2);
				}

				$data = array('msg'=>$d_stock->msg, 'status'=>1);
			} else {
				$data = array('msg'=>$d_stock->msg, 'status'=>2);
			}
			$this->ajaxReturn($data);
		} else {
			$this->warehouse_list = D('Warehouse')->getWarehouseList();
			
			$max_id = M('Transfer')->max('transfer_id') + 1;
			$this->number = 'DBD_' . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);

			//查询审批流信息 process_status 审批开启状态，1开启，2禁用
			$exam_init_info = D('Exam')->initData(4);
			$this->process_status = $exam_init_info['process_status'];
			$this->process_list = $exam_init_info['process_list'];

			$this->display();
		}
	}


	/**
	 * 库存调拨编辑
     * @author lee
	**/
	public function transfer_edit(){
		$transfer_id = intval($_REQUEST['transfer_id']);
		$transfer = D('Stock')->getTransferDetail(array('transfer_id' => $transfer_id));

		//权限判断
		if (!in_array($transfer['owner_role_id'], getPerByAction(MODULE_NAME,ACTION_NAME))) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}

		if ($transfer['status'] > 0) {
			echo '<div class="alert alert-error">调拨单只在出库之前可编辑</div>';
			exit;
		}

		//根据审批状态判断是否有编辑权限
		$exam_info = D('Exam')->checkExamPermission(4, $transfer_id);
		if ($exam_info['exam_status'] == 1 || $exam_info['exam_status'] == 2) {
			echo '<div class="alert alert-error">已进入审批流程，无法编辑</div>';
			exit;
		}

		if (IS_POST) {
			$d_stock = D('Stock');
			$res = D('Stock')->updateTransfer(array('transfer_id' => $transfer_id), $_POST);
			if ($res) {

				//更新初始审批信息
				$d_exam = D('Exam');
				$exam_data = $d_exam->examInitData((int) $_POST['process_status'], (int) $_POST['temp_id'], $transfer_id, 4);
				$exam_data['type_id'] = 4;
				$exam_data['order_id'] = $transfer_id;
				if (!$d_exam->editOrderExam(array('type_id' => $exam_data['type_id'], 'order_id' => $exam_data['order_id']),$exam_data)) {
					$data = array('msg'=> '初始审批信息保存失败', 'status'=>2);
				}

				$data = array('msg'=>$d_stock->msg, 'status'=>1);
			} else {
				$data = array('msg'=>$d_stock->msg, 'status'=>2);
			}
			$this->ajaxReturn($data);
		} else {
			$transfer['product_list'] = D('Stock')->getTransferProcut($transfer_id);
			$this->assign('transfer', $transfer);

			//查询审批流信息 process_status 审批开启状态，1开启，2禁用
			$exam_init_info = D('Exam')->initData(4, $transfer_id);
			$this->process_status = $exam_init_info['process_status'];
			$this->process_list = $exam_init_info['process_list'];
			$this->order_exam = $exam_init_info['order_exam'];

			$this->warehouse_list = D('Warehouse')->getWarehouseList();
			$this->display();
		}
	}
	

	/**
	 * 单个仓库的库存信息
	*/
	public function warehouse_stock_list(){
		$warehouse_id = intval($_GET['warehouse_id']);
		$list = D('Stock')->getStockViewList(array('warehouse_id'=>$warehouse_id,'product_info.state' => array('neq', 3)));
		$this->ajaxReturn(array('data'=>$list, 'status'=>1));
	}

	/**
	* 库存调拨详情
	**/
	public function transfer_view(){
		$transfer_id = intval($_GET['transfer_id']);
		$transfer = D('Stock')->getTransferDetail(array('transfer_id'=>$transfer_id));

		//权限判断
		if (!in_array($transfer['owner_role_id'], getPerByAction(MODULE_NAME,ACTION_NAME))) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}

		$transfer['product_list'] = D('Stock')->getTransferProcut($transfer_id);
		
		if ($transfer['status'] == 1){
			//查询已出库产品和出库的SN信息
			foreach ($transfer['product_list'] as $k => $v) {
				$transfer['product_list'][$k]['stock_out_id'] = M('StockOut')->where(array('type' => 3, 'type_id' => $transfer_id))->getField('stock_out_id');

				$sn_list = D('Sn')->getSNList(array('stock_out_id' => $transfer['product_list'][$k]['stock_out_id'], 'product_info_id' => $v['product_info_id']));
				foreach ($sn_list as $k_sn => $v_sn) {
					$transfer['product_list'][$k]['sn_ids'] .= $v_sn['sn_id'].',';
				}
			}

			//出库信息
			$this->stock_out = D('Stock')->getStockOutOrIn(array('type' => 3, 'type_id' => $transfer_id), 'out');
		} elseif($transfer['status'] == 2) {
			//查询已入库产品和入库SN信息
			foreach ($transfer['product_list'] as $k => $v) {
				$transfer['product_list'][$k]['stock_in_id'] = M('StockIn')->where(array('type' => 3, 'type_id' => $transfer_id))->getField('stock_in_id');
				$sn_list = D('Sn')->getSNList(array('stock_in_id' => $transfer['product_list'][$k]['stock_in_id'], 'product_info_id' => $v['product_info_id']));
				foreach ($sn_list as $k_sn => $v_sn) {
					$transfer['product_list'][$k]['sn_ids'] .= $v_sn['sn_id'].',';
				}
			}

			//出库信息
			$this->stock_out = D('Stock')->getStockOutOrIn(array('type' => 3, 'type_id' => $transfer_id), 'out');

			//入库信息
			$this->stock_in = D('Stock')->getStockOutOrIn(array('type' => 3, 'type_id' => $transfer_id), 'in');
		}

		//pd_exam_type中对应的type_id值
		$this->type_id = 4;
		$this->order_id = $transfer['transfer_id'];

		$this->assign('transfer', $transfer);
		$this->display();
	}


	/**
	* 库存调拨出库
	**/
	public function transfer_out(){
		$transfer_id = intval($_REQUEST['transfer_id']);

		//根据库管判断是否有出库权限
		$d_warehouse = D('Warehouse');
		if (!$d_warehouse->isStorehouse(M('Transfer')->where('transfer_id = %d', $transfer_id)->getField('out_warehouse_id'))) {
			dialogAlert($d_warehouse->msg);
		}

		//根据审批状态判断是否有出库权限
		$exam_info = D('Exam')->checkExamPermission(4, (int) $transfer_id);
		if ($exam_info['exam_status'] != 2) {
			dialogAlert('调拨单未审核，不能出库');
		}

		if ($this->isPost()) {
			$d_stock_out = D('StockOut');
			// 1 表示销售  2 表示采购退货 3 库存调拨出库
			if ($res = $d_stock_out->addData(3, 'transfer_id')) {
				$res = $d_stock_out->addProduct();
				if (!$res) {
					$d_stock_out->deleteData();
				}
			}
			if ($res == 1) {
				//更新调拨单信息
				$data = array('status' => 1, 'transfer_out_date' => strtotime($_POST['update_time']));
				D('Stock')->updateTransfer(array('transfer_id' => $transfer_id), $data);

				alert('success', $d_stock_out->msg, $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', $d_stock_out->msg, $_SERVER['HTTP_REFERER']);
			}
		} else {
			//出库单号
			$max_id = M('StockOut')->max('stock_out_id') + 1;
			$this->number = D('Config')->getValue('stock_out_prefix') . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);

			$this->transfer = D('Stock')->getTransferDetail(array('transfer_id'=>$transfer_id));
			$this->product_list = D('Stock')->getTransferProcut($transfer_id);

			$this->display();
		}
	}


	/**
	* 库存调拨入库
	**/
	public function transfer_in(){
		$transfer_id = intval($_REQUEST['transfer_id']);

		//根据库管判断是否有出库权限
		$d_warehouse = D('Warehouse');
		if (!$d_warehouse->isStorehouse(M('Transfer')->where('transfer_id = %d', $transfer_id)->getField('in_warehouse_id'))) {
			alert('error', $d_warehouse->msg, $_SERVER['HTTP_REFERER']);
		}

		$transfer = D('Stock')->getTransferDetail(array('transfer_id' => $transfer_id));
		if ($transfer['status'] != 1) {
			alert('error', '调拨单不在已出库状态，不能入库', $_SERVER['HTTP_REFERER']);
		}

		if ($this->isPost()) {
			$d_stock_in = D('StockIn');
			$_POST['type'] = 3;
			$_POST['type_id'] = $transfer_id;
			$_POST['create_time'] = time();
			$_POST['update_time'] = strtotime($_POST['update_time']);
			$_POST['list'] = $_POST['product_info_list'];
			unset($_POST['product_info_list']);
			$res = $d_stock_in->addData();
			if ($res == 1) {

				//更新调拨单信息
				$data = array('status' => 2, 'transfer_in_date' => $_POST['update_time']);
				D('Stock')->updateTransfer(array('transfer_id' => $transfer_id), $data);

				alert('success', $d_stock_in->msg, $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', $d_stock_in->msg, $_SERVER['HTTP_REFERER']);
			}
		} else {
			//入库单号
			$max_id = M('StockIn')->max('stock_in_id') + 1;
			$this->number = D('Config')->getValue('stock_in_prefix') . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);

			$transfer['product_list'] = D('Stock')->getTransferProcut($transfer_id);

			//查询相关出库和sn信息
			foreach ($transfer['product_list'] as $k => $v) {
				$transfer['product_list'][$k]['stock_out_id'] = M('StockOut')->where(array('type' => 3, 'type_id' => $transfer_id))->getField('stock_out_id');

				$sn_list = D('Sn')->getSNList(array('stock_out_id' => $transfer['product_list'][$k]['stock_out_id'], 'product_info_id' => $v['product_info_id']));
				foreach ($sn_list as $k_sn => $v_sn) {
					$transfer['product_list'][$k]['sn_ids'] .= $v_sn['sn_id'].',';
				}
			}

			$this->assign('transfer', $transfer);
			$this->display();
		}
	}


	/**
	 * 产品入库列表
	 * @author lee
	 */
	public function instock()
	{
		$d_stock_in = D('StockIn');
		$d_search = D('Search');

		// 搜索
		$res = $d_search->getWhere($_GET);

		// 搜索where条件
		if ($res['where']) $where = $res['where'];

		// 记录分页参数
		$params = $res['page_params'];

		// 记录搜索条件
		$this->fields_search = $res['fields_search'];

		// 记录特殊搜索条件
		$this->single_list = $res['single_list'];

		// 搜索类型【特殊项】
		if ($_GET['type']) {
			$where['type'] = intval($_GET['type']);
		}
		// 搜索仓库【特殊项】
		if ($_GET['warehouse_id']) {
			$where['warehouse_id'] = intval($_GET['warehouse_id']);
		}

		// 有role_id的查询，需要处理权限范围问题，重新整理where条件
		$where = $d_search->roleWhere('owner_role_id', $where, getPerByAction(MODULE_NAME,ACTION_NAME));

		// 分页
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
        if($_GET['listrows']){
            $listrows = intval($_GET['listrows']);
            cookie('listrows', $listrows, 3600 * 24 * 30);
            $params[] = "listrows=" . intval($_GET['listrows']);
        }else{
            $listrows = cookie('listrows') ? cookie('listrows') : 15;
            $params[] = "listrows=".$listrows;
        }

		$list_data = $d_stock_in->getList($where, $p, $listrows, 'create_time desc');
		$count = $list_data['count'];
		$list = $list_data['list'];
//p($list);		

		import("@.ORG.Page");
		$Page = new Page($count, $listrows);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		$this->assign('listrows', $listrows);

		//仓库列表
		$warehouse_list = D('Warehouse')->getWarehouseList();
		$this->assign('warehouse_list', $warehouse_list);

		// 所有可搜索的字段
		$this->field_list = $d_stock_in->search_field_list();

		$this->assign('list',$list);
		$this->display();
	}


	/**
	 * 产品出库库列表
	 * @author lee
	 */
	public function outstock()
	{
		$d_stock_out = D('StockOut');
		$d_search = D('Search');

		// 搜索
		$res = $d_search->getWhere($_GET);

		// 搜索where条件
		if ($res['where']) $where = $res['where'];

		// 记录分页参数
		$params = $res['page_params'];

		// 记录搜索条件
		$this->fields_search = $res['fields_search'];

		// 记录特殊搜索条件
		$this->single_list = $res['single_list'];

		// 搜索类型【特殊项】
		if ($_GET['type']) {
			$where['type'] = intval($_GET['type']);
		}
		// 搜索仓库【特殊项】
		if ($_GET['warehouse_id']) {
			$where['warehouse_id'] = intval($_GET['warehouse_id']);
		}

		// 有role_id的查询，需要处理权限范围问题，重新整理where条件
		$where = $d_search->roleWhere('owner_role_id', $where, getPerByAction(MODULE_NAME,ACTION_NAME));

		// 分页
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
        if($_GET['listrows']){
            $listrows = intval($_GET['listrows']);
            cookie('listrows', $listrows, 3600 * 24 * 30);
            $params[] = "listrows=" . intval($_GET['listrows']);
        }else{
            $listrows = cookie('listrows') ? cookie('listrows') : 15;
            $params[] = "listrows=".$listrows;
        }

		$list_data = $d_stock_out->getList($where, $p, $listrows, 'create_time desc');
		$count = $list_data['count'];
		$list = $list_data['list'];
//p($list);		

		import("@.ORG.Page");
		$Page = new Page($count, $listrows);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		$this->assign('listrows', $listrows);

		//仓库列表
		$warehouse_list = D('Warehouse')->getWarehouseList();
		$this->assign('warehouse_list', $warehouse_list);

		// 所有可搜索的字段
		$this->field_list = $d_stock_out->search_field_list();

		$this->assign('list',$list);
		$this->display();
	}


	/**
	 * 删除调拨单
	 * @author lee
	 */
	public function transfer_delete()
	{
		$transfer_ids = $_POST['transfer_ids'];
		foreach ($transfer_ids as $k => $v) {
			if(order_can_del(4, $v) == false){
				$flag = 1;
				break;
			}
		}
		if ($flag == 1) {
			$data['info'] = '只能删除待审或驳回且未进行入库操作的记录！请重新选择';
			$data['status'] = 2;
		} else {
			$d_stock = D('Stock');
			foreach ($transfer_ids as $k => $v) {
				$d_stock->deleteTransfer($v);
			}
			$data['info'] = '删除成功';
			$data['status'] = 1;
		}
		$this->ajaxReturn($data);
	}

	/**
	 * 调拨单打印功能【示例】
	 * @author lee
	 */
	/*public function preview()
	{
		$transfer_id = intval($_GET['transfer_id']);
		$transfer = D('Stock')->getTransferDetail(array('transfer_id'=>$transfer_id));
		switch ($transfer['status']) {
			case '0':
				$transfer['status_name'] = '未出库';
				break;
			case '1':
				$transfer['status_name'] = '已出库';
				break;
			case '2':
				$transfer['status_name'] = '已入库';
				break;
			default:
				break;
		}
		// 详细信息
		$detail_list = array(
			array('name' => '调拨单号', 'value' => $transfer['number']),
			array('name' => '调拨状态', 'value' => $transfer['status_name']),
			array('name' => '调出仓', 'value' => $transfer['out_warehouse_name']),
			array('name' => '调入仓', 'value' => $transfer['in_warehouse_name']),
			array('name' => '负责人', 'value' => $transfer['owner_role_name']),
			array('name' => '创建人', 'value' => $transfer['creator_role_name']),
			array('name' => '备注', 'value' => $transfer['remark']),
		);

		// tab标题
		$title_list = array('product_name' => '商品名称', 'spec' => '规格', 'unit' => '单位', 'count' => '调拨数量', 'remark' => '备注');

		// 产品列表
		$product_list = D('Stock')->getTransferProcut($transfer_id);
		foreach ($product_list as $k => $v) {
			foreach ($title_list as $field => $name) {
				$tab_list[$k][$field] = $v[$field];
			}
		}

		$this->title = '库存调拨单';
		$this->detail_list = $detail_list;
		$this->title_list = $title_list;
		$this->tab_list = $tab_list;
		$this->display();
	}*/


	// /**
	// * 商品收发汇总
	// **/
	// public function analytics(){
	// 	//时间段搜索
	// 	if($_GET['between_date']){
	// 		$between_date = explode(' - ',trim($_GET['between_date']));
	// 		if($between_date[0]){
	// 			$start_time = strtotime($between_date[0]);
	// 		}
	// 		$end_time = $between_date[1] ?  strtotime(date('Y-m-d 23:59:59',strtotime($between_date[1]))) : strtotime(date('Y-m-d 23:59:59',time()));
	// 	}else{
	// 		$start_time = strtotime(date('Y-m-01 00:00:00'));
	// 		$end_time = strtotime(date('Y-m-d H:i:s'));
	// 	}
	// 	$this->between_date = $_GET['between_date'] ? trim($_GET['between_date']) : date('Y-m-01').' - '.date('Y-m-d');
	// 	$this->start_date = date('Y-m-d',$start_time);
	// 	$this->end_date = date('Y-m-d',$end_time);

	// 	//时间插件
	// 	$daterange = daterange();
	// 	$this->assign('daterange', $daterange);
		
	// 	$this->display();
	// }


	/**
	 * 出入库删除
	 * @author	shen
	 */
	public function delete()
	{
		$type = trim($_POST['type']);
		$id = (int) $_POST['id'];
		$info = M($type)->find($id);
		if (($type !== 'stock_in' && $type !== 'stock_out') || $id === 0 || !$info) {
			$this->ajaxReturn(array('msg' => '参数错误', 'status' => 0));
		}

		$over_stock_sales = D('Config')->getValue('over_stock_sales');		// 是否允许超库存销售
		$type_id = $type === 'stock_in' ? 1 : 2;		// 区分出入库
		$product_info_list = M($type . '_productinfo')->where(array($type . '_id' => $id))->field('product_info_id, count')->select();		// 出入库单对应产品表
		
		$d_product_info = D('ProductInfo');
		$d_sn = D('Sn');
		$d_stock = D('Stock');
		$m_sn_log = M('SnLog');
		
		$sn_log_id_list = array();	// SN log 删除ID
		
		foreach ($product_info_list as $val) {
			$product_info = $d_product_info->getNameSpec($val['product_info_id']);
			// 产品是否有SN
			if ($product_info['has_sn']) {
				// 获取产品SN列表
				$sn_list = $d_sn->getSNList(array($type . '_id' => $id, 'product_info_id' => $val['product_info_id']));
				foreach ($sn_list as $v) {
					$tmp_sn_log_id = $m_sn_log->where(array('sn_id' => $v['sn_id'], 'type' => $type_id, 'type_id' => $id))->getField('id');
					if ($tmp_sn_log_id) {
						// 判断该SN是否有后续操作，如果有则不允许删除
						if ($m_sn_log->where(array('sn_id' => $v['sn_id'], 'id' => array('gt', $tmp_sn_log_id)))->limit(1)->count() == 0) {
							$sn_log_id_list[] = $tmp_sn_log_id;
						} else {
							$this->ajaxReturn(array('msg' => '删除失败！', 'info' => '产品：'.$product_info['product_name'].'[SN：'. $v['sn'] .']已有后续出入库操作，如需继续删除，请先删除其他的出入库操作。', 'status' => 0));
						}
					}
				}
			} else {
				// 删除入库单 不允许超库存销售时 需要判断库存数是否不小于入库数
				if ($type_id == 1 && !$over_stock_sales) {
					$stock_count = $d_stock->getProductStock($val['product_info_id'], $info['warehouse_id']);
					if ($stock_count < $val['count']) {
						$this->ajaxReturn(array('msg' => '删除失败！', 'info' => '产品'.$product_info['product_name'].'在本库库存小于入库单数量，无法删除，可设置允许超库存销售。', 'status' => 0));
					}
				}
			}
		}

		// 调拨单特殊处理
		if ($info['type'] == 3) {
			$m_transfer = M('Transfer');
			$transfer = $m_transfer->find($info['type_id']);
			if ($transfer['status'] == 2) {
				if ($type == 'stock_out') {
					$this->ajaxReturn(array('msg' => '删除失败！', 'info' => '调拨单删除出库需要先删除入库单。', 'status' => 0));
				}
				$m_transfer->where(array('transfer_id' => $info['type_id']))->setField('status', 1);
			} else {
				$m_transfer->where(array('transfer_id' => $info['type_id']))->setField('status', 0);
			}
		}

		// 无法采用事务
		foreach ($product_info_list as $val) {
			$product_info = $d_product_info->getNameSpec($val['product_info_id']);
			if ($product_info['has_sn']) {
				$sn_list = $d_sn->getSNList(array($type . '_id' => $id, 'product_info_id' => $val['product_info_id']));
				foreach ($sn_list as $v) {
					$tmp_data = array('update_time' => time(), 'stock_in_id' => 0, 'stock_out_id' => 0, 'status' => 0);
					$last_in = $m_sn_log->where(array('sn_id' => $v['sn_id'], 'type' => 1, 'id' => array('NOT IN', $sn_log_id_list)))->order('id desc')->find();	// 入库
					$last_out = $m_sn_log->where(array('sn_id' => $v['sn_id'], 'type' => 2, 'id' => array('NOT IN', $sn_log_id_list)))->order('id desc')->find();	// 出库
					if ($last_in && $last_out) {
						$tmp_data['stock_in_id'] = (int) $last_in['type_id'];
						$tmp_data['stock_out_id'] = (int) $last_out['type_id'];
						if ($last_in['id'] > $last_out['id']) {
							$tmp_data['status'] = 1;
							$tmp_data['warehouse_id'] = M('StockIn')->where(array($type.'_id' => $last_in['type_id']))->getField('warehouse_id');
						} else {
							$tmp_data['status'] = 2;
							$tmp_data['warehouse_id'] = M('StockOut')->where(array($type.'_id' => $last_out['type_id']))->getField('warehouse_id');
						}
					} elseif ($last_in) {
						$tmp_data['stock_in_id'] = (int) $last_in['type_id'];
						$tmp_data['status'] = 1;
						$tmp_data['warehouse_id'] = M('StockIn')->where(array($type.'_id' => $last_in['type_id']))->getField('warehouse_id');
					} elseif ($last_out) {
						$tmp_data['stock_out_id'] = (int) $last_out['type_id'];
						$tmp_data['status'] = 2;
						$tmp_data['warehouse_id'] = M('StockOut')->where(array($type.'_id' => $last_out['type_id']))->getField('warehouse_id');
					}
					// 回滚SN记录
					$d_sn->where(array('sn_id' => $v['sn_id']))->setField($tmp_data);
				}
			}
			// 修改库存
			$count = $type_id == 1 ? - $val['count'] : $val['count'];		// 入库单 数量取反
			$d_stock->where(array('warehouse_id' => $info['warehouse_id'], 'product_info_id' => $val['product_info_id']))->setInc('count', $count);
		}
		// 删除SN记录表
		if (!empty($sn_log_id_list)) {
			$m_sn_log->where(array('id' => array('IN', $sn_log_id_list)))->delete();
		}
		// 删除出入库产品表
		M($type . '_productinfo')->where(array($type . '_id' => $id))->delete();
		// 删除出入库表
		M($type)->delete($id);
		$this->ajaxReturn(array('msg' => '删除成功', 'status' => 1));
	}

}
