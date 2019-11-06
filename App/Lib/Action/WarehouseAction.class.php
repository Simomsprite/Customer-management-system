<?php
/**
*任务模块
*
**/
class WarehouseAction extends Action{
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('create_new_product','create_stock','owner_role_list','express_edit')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
	}


	/**
	* 仓库管理首页
	***/
	public function index(){
		$list = D('Warehouse')->getWarehouseList();
		
		$role_id_list = array_unique(array_filter(explode(',', implode(',', y_array_column($list, 'owner_role_id')))));
		$this->role_list = D('User')->get_full_name($role_id_list);
		foreach ($list as $key => $val) {
			$list[$key]['role_id_list'] = array_filter(explode(',', $val['owner_role_id']));
		}
		$this->assign('list',$list);
		$this->alert = parseAlert();
		$this->display();
	}

	/**
	* 仓库管理添加
	**/
	public function add(){
		if (IS_POST) {
			$m_warehouse = M('Warehouse');
			if ($m_warehouse->create()) {
				$warehouse_id = $m_warehouse->add();
				if ($warehouse_id) {

					//生成库存信息
					D('Stock')->addStock();

					alert('success', '保存成功', $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', '保存失败', $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', '数据创建失败', $_SERVER['HTTP_REFERER']);
			}
		} else {
			$count = (int) M('Warehouse')->count();
			$count += 1;
			$count = str_pad((string) $count, 4, '0', STR_PAD_LEFT);
			$this->number = 'W_' . date('Ymd') . '_' . $count;
			$this->display();
		}
	}

	/**
	* 仓库管理编辑
	**/
	public function edit(){
		$m_warehouse = M('Warehouse');
		$warehouse_id = $this->_request('warehouse_id','intval');
		if (IS_POST) {
			$status = 0;
			if ($m_warehouse->create()) {
				$m = $m_warehouse->where('warehouse_id = %d', $warehouse_id)->save();
				if ($m !== false) {
					$msg = '保存成功';
					$status = 1;
					$type = 'success';
				} else {
					$msg = '保存失败';
					$type = 'error';
				}
			} else {
				$msg = '数据创建失败';
				$type = 'error';
			}
			if (IS_AJAX) {
				$this->ajaxReturn(array('status' => $status, 'msg' => $msg));
			} else {
				alert($type, $msg, $_SERVER['HTTP_REFERER']);
			}
		} else {
			$warehouse = $m_warehouse->where('warehouse_id = %d', $warehouse_id)->find();
			// 库管
			$role_id_list = array_filter(explode(',', $warehouse['owner_role_id']));
			$this->role_list = D('User')->get_full_name($role_id_list);
			$this->assign('warehouse', $warehouse);
			$this->display();
		}
	}


	/**
	* 仓库管理删除
	**/
	// public function delete(){
	// 	die('方法不可用');
	// 	$m_warehouse = M('Warehouse');
	// 	$warehouse_ids = $this->_post('warehouse_ids');
		
	// 	foreach ($warehouse_ids as $k => $v) {
	// 		//是否需要过滤掉存在库存的仓库，不能删除？
	// 		#code...
	// 		// if ('存在库存') {
	// 		// 	$flag = 1;
	// 		// 	continue;
	// 		// }
	// 		$del_warehouse_ids[] = $v;
	// 	}
	// 	if (!empty($del_warehouse_ids)) {
	// 		$m = $m_warehouse->where(array('warehouse_id'=>array('in', $del_warehouse_ids)))->setField('is_deleted', 1);
	// 		if ($m) {
	// 			$data['status'] = 1;
	// 			if ($flag == 1) {
	// 				$data['info'] = '已过滤存在库存的仓库，删除成功';
	// 			} else {
	// 				$data['info'] = '全部删除成功';
	// 			}
	// 			$this->ajaxReturn($data);
	// 		} else {
	// 			$data['status'] = 2;
	// 			$data['info'] = '删除失败';
	// 			$this->ajaxReturn($data);
	// 		}
	// 	} else {
	// 		$data['status'] = 2;
	// 		$data['info'] = '所选仓库下都存在库存，不能删除';
	// 		$this->ajaxReturn($data);
	// 	}
	// }


	/**
	 * 库管列表
	 */
	public function owner_role_list()
	{
		$warehouse_id = (int) $_GET['id'];
		$warehouse = M('Warehouse')->where('warehouse_id = %d', $warehouse_id)->find();
		// 库管
		$role_id_list = array_filter(explode(',', $warehouse['owner_role_id']));
		$this->info = $warehouse;
		$this->role_list = D('User')->get_full_name($role_id_list);
		$this->display();
	}


	/**
	 * 修改出库物流信息
	 * @author lee
	 */
	public function express_edit()
	{
		$stock_out_id = intval($_REQUEST['stock_out_id']);
		if (IS_POST) {
			$d_stock_out = D('StockOut');
			$res = $d_stock_out->updateData(array('stock_out_id' => $stock_out_id), $_POST);
			if ($res) {
				$data['info'] = $d_stock_out->msg;
				$data['status'] = 1;
				$this->ajaxReturn($data);
			} else {
				$data['info'] = $d_stock_out->msg;
				$data['status'] = 0;
				$this->ajaxReturn($data);
			}
		} else {
			$d_stock = D('Stock');
			$stock_out = $d_stock->getStockOutOrIn(array('stock_out_id' => $stock_out_id), 'out');
			$this->assign('stock_out', $stock_out);
			$this->display();
		}
	}


	/**
	 * 转存 pd_product 表的部分数据到 pd_product_info
	 * 内置方法，用于处理生成新的产品
	 */
	public function create_new_product()
	{
		// die('禁止访问');
		if ($_GET['sign'] == '5ksoftware') {
			$m_product_info = M('ProductInfo');
			$list = M('Product')->field('product_id as product_info_id,product_id,suggested_price as price,cost_price as price_cost,cost_price as price_cost_avg')->select();
			//$m = M('ProductInfo')->addAll($list);
			foreach ($list as $k => $v) {
				if (!$m_product_info->where(array('product_info_id' => $v['product_info_id']))->find()) {
					if ($m_product_info->add($v)) {
						$res['success_product_id'] = $v['product_id'];
					} else {
						$res['error_product_id'] = $v['product_id'];
					}
				}
			}
			$file = './product_change_log.txt';
			if(false !== fopen($file,'w+')){
				file_put_contents($file, print_r($res, true));
				echo 'end success';
			}else{
				p($res,'');
				echo '文件创建失败';
			}
		}
	}


	/**
	 * 内置方法,用于生成库存
	**/
	public function create_stock()
	{
		if ($_GET['sign'] == '5ksoftware') {
			D('Stock')->addStock();
			echo 'success';
		}
	}


}