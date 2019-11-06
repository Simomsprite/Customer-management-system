<?php 
class SalesModel extends Model{

	public $msg = '';
	public $sales_id = 0;

	/**
	 * 订单相关（出库记录）
	 * @param 
	 * @author 
	 * @return 
	 */
	public function getStockOutList($sales_id) {
		$stock_out_list = M('StockOutBaseinfo')->where(array('type'=>1,'type_id'=>$sales_id))->select();
		$d_product_info = D('ProducrInfoView');
		foreach ($stock_out_list as $k=>$v) {
			$stock_out_list[$k]['product_data'] = $d_product_info->where(array('product_info_id'=>$v['product_info_id']))->field('name,number')->find();
		}
		return $stock_out_list;
	}


	/**
	 * 出库单数据删除
	 */
	public function deleteData() {
		// if ($this->stock_out_id) {
		// 	$this->where(array('out_id' => $this->stock_out_id))->delete();
		// 	M('StockOutProductinfo')->where(array('stock_out_id' => $this->stock_out_id))->delete();
		// }
	}

	
	/**
	 * 销售单产品返回
	 */
	public function productDataList($sales_id, $warehouse_id = null, $type = 1) {
		if ($sales_id) {
			$d_stock = D('Stock');
			$m_stock_out = M('StockOut');
			$m_stock_out_productinfo = M('StockOutProductinfo');
			$d_product_info = D('ProductInfo');
			$d_stock_out = D('StockOut');
			$product_list = M('SalesProduct')->where(array('sales_id'=>$sales_id))->select();
			// 是否退货
			$return_product = D('Purchase')->getSalesReturnIn($sales_id);
			foreach ($product_list as $k => $v) {
				$product_info = $d_product_info->getNameSpec($v['product_info_id']);
				$product_list[$k] = $v + $product_info;
				$product_list[$k]['amount_out'] = $d_stock_out->getStockOutCount($sales_id, $v['product_info_id'], $type);
				$product_list[$k]['stock_count'] = $d_stock->getProductStock($v['product_info_id'], $warehouse_id);
				$product_list[$k]['return_count'] = $return_product[$v['product_info_id']] ?: 0;
			}
			return $product_list;
		}
	}

	/**
	 * 获取列表返回数据格式
	 */
	public function getList($field = array('*'), $where = array(), $page = '1, 10', $order = '')
	{
		$d_supplier = D('Supplier');
		$d_user = D('User');
		$list = $this->where($where)->page($page)->order($order)->field($field)->select();
		$count = $this->where($where)->count();
		foreach ($list as $key => $val) {
			// 采购退货获取供应商信息
			if ($where['type'] == 1 && isset($val['customer_id'])) {
				$list[$key] = $val + $d_supplier->getInfobyPurchase($val['customer_id']);
			}
			if (isset($val['owner_role_id'])) {
				$list[$key]['owner_role_name'] = $d_user->get_full_name((int) $val['owner_role_id']);
			}
			if (isset($val['sales_time'])) {
				$list[$key]['sales_time'] = date('Y-m-d', $val['sales_time']);
			}
		}
		$list = $list ?: array();
		return array('list' => $list, 'count' => $count);
	}



	/**
	 * 采购单退货
	 * 
	 */
	public function addDataPurchase()
	{
		$_POST['type'] = 1;		// 采购退货
		$_POST['is_checked'] = 0;
		$_POST['status'] = 97;		// 未出库
		$_POST['sales_time'] = strtotime($_POST['sales_time']);
		$_POST['customer_id'] = $_POST['purchase_id'];
		$_POST['update_time'] = $_POST['create_time'] = time();
		if ($this->create()) {
			if ($sales_id = $this->add()) {
				$this->sales_id = $sales_id;
				return true;
			} else {
				$this->msg = '添加失败[sales]';
			}
		} else {
			$this->msg = '数据对象创建失败[sales]';
		}
		return false;
	}

	/**
	 * 采购单退货
	 * 
	 */
	public function editDataPurchase()
	{
		$_POST['is_checked'] = 0;
		$_POST['sales_time'] = strtotime($_POST['sales_time']);
		$_POST['customer_id'] = $_POST['purchase_id'];
		$_POST['update_time'] = time();
		if ($this->create()) {
			if ($sales_id = $this->save()) {
				return true;
			} else {
				$this->msg = '修改失败[sales]';
			}
		} else {
			$this->msg = '数据对象创建失败[sales]';
		}
		return false;
	}


	/**
	 * 采购单退货添加产品
	 * 
	 */
	public function addProductPurchase()
	{
		$m_salse_product = M('SalesProduct');
		$data = array();
		foreach ($_POST['product_info_list'] as $key => $val) {
			if ($val['count'] > 0) {
				$data_temp = array(
					'customer_id' => $_POST['purchase_id'],
					'sales_id' => $this->sales_id,
					'product_info_id' => $key,
					'amount' => $val['count'],
					'unit' => $val['unit'],
					'ori_price' => $val['ori_price'],
					'unit_price' => $val['unit_price'],
					'subtotal' => $val['count'] * $val['unit_price']
				);
				if (!$m_salse_product->create($data_temp)) {
					$this->msg = '数据对象创建失败[salse_product]';
					return false;
				}
				$data[] = $data_temp;
			}
		}
		if ($m_salse_product->addAll($data)) {
			$this->msg = '创建成功';
			return true;
		}
		return false; 
	}


	/**
	 * 采购单退货修改添加产品
	 * 
	 */
	public function editProductPurchase()
	{
		$m_sales_product = M('SalesProduct');
		foreach ($_POST['product_info_list'] as $key => $val) {
			if ($val['count'] > 0) {
				$data = array(
					'sales_id' => $_POST['sales_id'],
					'purchase_id' => $purchase_id,
					'product_info_id' => $key,
					'price_cost' => $val['price_cost'],
					'amount' => $val['count'],
					'subtotal' => $val['count'] * $val['unit_price'],
					'ori_price' => $val['ori_price'],
					'unit_price' => $val['unit_price'],
				);
				$m_sales_product->create($data);
				//检查某个退货（采购）单是否关联有该产品，是：更新，否：新增
				if ($val['sales_product_id']) {
					$m_sales_product->where(array('sales_product_id' => $val['sales_product_id']))->save();
				} else {
					$m_sales_product->add();
				}
			} else {
				//检查某个退货（采购）单是否关联有该产品，是：删除，否：不做处理
				if ($val['sales_product_id']) {
					$m_sales_product->where(array('sales_product_id' => $val['sales_product_id']))->delete();
				}
			}
		}
		$this->msg = '修改成功';
		return true;
	}

	

	/**
	 * 删除添加失败错误数据
	 */
	public function delete_error_data()
	{
		if ($this->sales_id) {
			$this->delete($this->sales_id);
			M('SalesProduct')->where(array('sales_id' => $this->sales_id))->delete();
		}
	}


	/**
	 * 获取销售退回列表返回数据
	 * @param 页码     $page_index
	 * @param 每页数量 $page_size
	 * @param 查询条件 $condition
	 * @param 排序条件 $order
	 * @return unknown
	 */
	function getReturnList($page_index, $page_size, $condition, $order){
		$d_user = D('User');
		$d_exam = D('Exam');
		$condition['type'] = 2; // 采购 【2：退货】
		$condition['owner_role_id'] = array('in', getPerByAction(MODULE_NAME, ACTION_NAME));
		$list = M('Purchase')->where($condition)->page($page_index.','.$page_size)->order($order)->select();
		foreach ($list as $k => $v) {
			$list[$k]['creator_name'] = $d_user->get_full_name((int) $v['creator_role_id']);
			$list[$k]['owner_role_name'] = $d_user->get_full_name((int) $v['owner_role_id']);
			$list[$k]['purchase_time'] = $v['purchase_time'] ? date('Y-m-d', $v['purchase_time']) : '-';

			$contract = D('Contract')->getContractBySalesId($v['type_id']);
			$list[$k]['contract_number'] = $contract['number'];
			$list[$k]['contract_id'] = $contract['contract_id'];

			//审批信息
			$list[$k] += $d_exam->checkExamPermission(3, $v['purchase_id']);
		}
		return $list ?: array();
	}


	/**
	 * 添加销售（合同）退货，即相当于走 采购->入库 流程
	 * @param number 退货单编号
	 * @param name 退货标题
	 * @param type 代表销售退货
	 * @param type_id 代表sales_id
	 * @param consult_total_price 协商总退货价
	 * @param total_price 原计算总退货价
	 * @param return_time 退货时间
	 * @return
	 */
	function addReturnData($data){
		$purchase = array(
			'number' => $data['number'],
			'name' => $data['name'],
			'type' => 2,
			'type_id' => M('rContractSales')->where('contract_id = %d', $data['contract_id'])->getField('sales_id'),
			'purchase_amount' => $data['consult_total_price'],
			//'other_amount' => $data['total_price'],
			'purchase_time' => strtotime($data['return_time']),
			'create_time' => time(),
			'update_time' => time(),
			'creator_role_id' => session('role_id'),
			'owner_role_id' => $data['owner_role_id'],
			'remark' => $data['remark'],
		);

		$purchase_id = M('Purchase')->add($purchase);
		if ($purchase_id) {
			$res = $this->addReturnProduct($purchase_id, $data['product_info_list']);
			if ($res) {
				$this->msg = '保存成功';
				return $purchase_id;
			} else {
				return false;
			}
		} else {
			$this->msg = '退货信息保存失败';
			return false;
		}
	}

	/**
	 * 添加销售（合同）退货，保存退货产品信息
	 * @param price_cost 退货单价
	 * @return
	 */
	function addReturnProduct($purchase_id, $product_list){
		foreach ($product_list as $k => $v) {
			//只处理有退货数的产品
			if ($v['count'] > 0) {
				$data = array(
					'purchase_id' => $purchase_id,
					'product_info_id' => $k,
					'price_cost' => $v['price_cost'],
					'price_discount' => $v['price_cost'],
					'count' => $v['count'],
				);
				$id = '';
				$id = M('PurchaseProduct')->add($data);
				if (!$id) {
					$this->msg = '产品'.$k.'添加退货信息失败';
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * 更新销售（合同）退货，即相当于走 采购->入库 流程
	 * @param number 退货单编号
	 * @param name 退货标题
	 * @param type 代表销售退货
	 * @param type_id 代表sales_id
	 * @param consult_total_price 协商总退货价
	 * @param total_price 原计算总退货价
	 * @param return_time 退货时间
	 * @return
	 */
	function updateReturnData($data){
		$purchase = array(
			'number' => $data['number'],
			'name' => $data['name'],
			'purchase_amount' => $data['consult_total_price'],
			//'other_amount' => $data['total_price'],
			'purchase_time' => strtotime($data['return_time']),
			'update_time' => time(),
			'owner_role_id' => $data['owner_role_id'],
			'remark' => $data['remark'],
		);

		$sql_res = M('Purchase')->where('purchase_id = %d', $data['purchase_id'])->save($purchase);
		if ($sql_res) {
			$res = $this->updateReturnProduct($data['purchase_id'], $data['product_info_list']);
			if ($res) {
				$this->msg = '保存成功';
				return true;
			} else {
				return false;
			}
		} else {
			$this->msg = '退货信息保存失败';
			return false;
		}
	}

	/**
	 * 更新销售（合同）退货，保存退货产品信息
	 * @param price_cost 退货单价
	 * @return
	 */
	function updateReturnProduct($purchase_id, $product_list){
		foreach ($product_list as $k => $v) {
			//只处理有退货数的产品
			if ($v['count'] > 0) {
				$data = array(
					'purchase_id' => $purchase_id,
					'product_info_id' => $k,
					'price_cost' => $v['price_cost'],
					'count' => $v['count'],
				);

				//检查某个退货（采购）单是否关联有该产品，是：更新，否：新增
				if ($this->checkReturnProduct($purchase_id, $k)) {
					M('PurchaseProduct')->where('purchase_id = %d and product_info_id = %d', $purchase_id, $k)->save($data);
				} else {
					M('PurchaseProduct')->add($data);
				}
			} else {
				//检查某个退货（采购）单是否关联有该产品，是：删除，否：不做处理
				if ($this->checkReturnProduct($purchase_id, $k)) {
					M('PurchaseProduct')->where('purchase_id = %d and product_info_id = %d', $purchase_id, $k)->delete();
				}
			}
		}
		return true;
	}

	/**
	 * 检查某个退货（采购）单是否关联有该产品
	 * 单个退货（采购）单下的产品具有唯一性
	 * @param price_cost 退货单价
	 * @return
	 */
	function checkReturnProduct($purchase_id, $product_info_id){
		return (bool) M('PurchaseProduct')->where('purchase_id = %d and product_info_id = %d', $purchase_id, $product_info_id)->find();
	}


	/**
	 * 获取采购退货产品 （退货单产品汇总）
	 */
	public function getPurchaseReturn($purchase_id)
	{
		$sales_id = $this->where(array('customer_id' => $purchase_id, 'type' => 1))->getField('sales_id', true);
		$list = M('SalesProduct')->where(array('sales_id' => array('IN', $sales_id)))->field('product_info_id,amount')->select();
		foreach ($list as $val) {
			if (!isset($res[$val['product_info_id']])) {
				$res[$val['product_info_id']] = 0;
			}
			$res[$val['product_info_id']] += $val['amount'];
		}
		return $res;
	}


	/**
	 * 采购退货出库信息
	 */
	public function getPurchaseReturnOut($purchase_id)
	{
		$sales_id = $this->where(array('customer_id' => $purchase_id, 'type' => 1))->getField('sales_id', true);
		return D('StockOut')->getReturnProduct($sales_id);
	}


	/**
	 * 采退单详情
	 */
	public function getView($sales_id)
	{
		$data = $this->find($sales_id);
		$data['create_time'] = date('Y-m-d', $data['create_time']);
		$data['sales_time'] = date('Y-m-d', $data['sales_time']);
		$purchase_info = M('Purchase')->where(array('purchase_id' => $data['customer_id']))->field('number,type_id as supplier_id')->find();
		$data['purchase_number'] = $purchase_info['number'];
		$data['creator_role_name'] = D('User')->get_full_name((int) $data['creator_role_id']);
		$data['owner_role_name'] = D('User')->get_full_name((int) $data['owner_role_id']);
		$data['supplier_name'] = M('Supplier')->where(array('supplier_id' => $purchase_info['supplier_id']))->getField('name');

		//审批权限信息
		$data += D('Exam')->checkExamPermission(2, $sales_id);

		return $data;
	}


	/**
	 * 采退单产品详情
	 */
	public function getProductList($sales_id)
	{
		$d_product_info = D('ProductInfo');
		$d_stock_out = D('StockOut');
		$list = M('SalesProduct')->where(array('sales_id' => $sales_id))->field('product_info_id, ori_price, unit_price, unit, amount as count')->select();
		foreach ($list as $key => $val) {
			$list[$key] += $d_product_info->getNameSpec($val['product_info_id']);
			$list[$key]['stock_out_count'] = $d_stock_out->getStockOutCount($sales_id, $val['product_info_id'], 2);
			$list[$key]['subtotal'] = $val['count'] * $val['unit_price'];
		}
		return $list;
	}


	
}

