<?php


class PurchaseModel extends Model{

	public $purchase_id;		// 采购单ID
	public $msg;				// 提示信息
	private $old_data = array('purchase' => null, 'product' => null);		// 修改前数据
	public $count;

	
	/**
	 * 获取列表返回数据格式
	 * @param 页码     $page_index
	 * @param 每页数量 $page_size
	 * @param 查询条件 $condition
	 * @param 排序条件 $order
	 * @return unknown
	 */
	public function getList($page, $condition, $order){
		$m_supplier = M('Supplier');
		$d_user = D('User');
		$d_exam = D('Exam');
		$condition['type'] = 1; // 采购 【2：退货】
		$condition['owner_role_id'] = array('in', getPerByAction(MODULE_NAME,ACTION_NAME)); //权限范围
		$list = $this->where($condition)->page($page)->order($order)->select();
		foreach ($list as $k => $v) {
			$list[$k]['supplier_name'] = $m_supplier->where('supplier_id = %d', $v['type_id'])->getField('name');
			$list[$k]['creator_name'] = $d_user->get_full_name((int) $v['creator_role_id']);
			$list[$k]['owner_role_name'] = $d_user->get_full_name((int) $v['owner_role_id']);
			$list[$k]['purchase_time'] = $v['purchase_time'] ? date('Y-m-d', $v['purchase_time']) : '-';
			$list[$k]['purchase_amount'] = number_format($list[$k]['purchase_amount'], 2);
			//审批信息
			if ($v['type'] == 1) {
				$list[$k] += $d_exam->checkExamPermission(1, $v['purchase_id']);
			} elseif ($v['type'] == 2) {
				$list[$k] += $d_exam->checkExamPermission(3, $v['purchase_id']);
			}
			switch ($list[$k]['exam_status']) {
				case '0':
					$list[$k]['exam_status'] = array('color' => '#F5CA00', 'name' => '待审');
					break;
				case '1':
					$list[$k]['exam_status'] = array('color' => '#F5CA00', 'name' => '审批中');
					break;
				case '2':
					$list[$k]['exam_status'] = array('color' => '#7CCA4E', 'name' => '通过');
					break;
				case '3':
					$list[$k]['exam_status'] = array('color' => '#F5715F', 'name' => '驳回');
					break;
			}
		}
		return $list ?: array();
	}

	/**
	* 获取采购单主信息
	**/
	public function getView($pk_id){
		$m_supplier = M('Supplier');
		$d_user = D('User');
		$detail = $this->where('purchase_id = %d', $pk_id)->find();
		if ($detail['type'] == 1) {
			// 采购单
			$detail['supplier'] = M('supplier')->where(array('supplier_id' => $detail['type_id']))->find();
			$this->purchase_id = $pk_id;
		} else if ($detail['type'] == 2) {
			// 销售退货单
			$this->purchase_id = $pk_id;
		}
		$detail['creator_name'] = $d_user->get_full_name((int) $detail['creator_role_id']);
		$detail['owner_name'] = $d_user->get_full_name((int) $detail['owner_role_id']);
		$detail['purchase_time'] = $detail['purchase_time'] ? date('Y-m-d', $detail['purchase_time']) : '-';

		//审批权限信息
		if ($detail['type'] == 1) {
			$detail += D('Exam')->checkExamPermission(1, $pk_id);
		} elseif ($detail['type'] == 2) {
			$detail += D('Exam')->checkExamPermission(3, $pk_id);
		} 

		// dd($detail);
		return $detail;
	}

	/**
	* 添加采购单数据
	**/
	public function addData($data){
		if ($this->where(array('number' => $data['number']))->count() > 0) {
			$this->msg = '编号已存在！';
			return false;
		}
		if ($this->create($data)) {
			$this->purchase_time = strtotime($data['purchase_time']);
			$this->create_time = time();
			$this->update_time = time();
			$purchase_id = $this->add();
			if ($purchase_id) {
				$this->purchase_id = $purchase_id;
				return $purchase_id;
			} else {
				$this->msg = '创建失败[采购单]';
				return false;
			}
		} else {
			$this->msg = '数据对象创建失败[采购单]';
			return false;
		}
	}

	/**
	* 添加采购产品数据
	**/
	public function addProductData ($data) {
		$m_purchase_product = M('PurchaseProduct');
		foreach ($data as $key => $val) {
			$val['purchase_id'] = $this->purchase_id;
			if ($m_purchase_product->create($val)) {
				$res = $m_purchase_product->add();
				if (!$res) {
					$this->msg = '创建失败[产品]';
					return false;
				}
			} else {
				$this->msg = '数据对象创建失败[产品]';
				return false;
			}
		}
		return true;
	}

	/**
	 * 编辑采购单数据
	 * @param $data  数据
	 * @param $pk_id 主键
	 */
	public function saveData()
	{
		$this->purchase_id = $_POST['purchase_id'];
		$this->old_data['purchase'] = $this->find($this->purchase_id);
		$_POST['examine'] = 0;	// 修改之后改变审核状态 (驳回  -> 待审)
		if ($this->create()) {
			$this->purchase_time = strtotime($_POST['purchase_time']);
			$this->update_time = time();
			$res = $this->save();
			if (!$res) $this->msg = '修改失败';
			return (bool) $res;
		} else {
			$this->msg = '数据对象创建失败';
			return false;
		}
	}

	/**
	 * 编辑采购单产品信息
	 */
	public function saveProductData()
	{
		$m_purchase_product = M('PurchaseProduct');

		$old_list = $m_purchase_product->where(array('purchase_id' => $this->purchase_id))->select();
		$this->old_data['product'] = $old_list;
		$old_list2 = array();
		foreach ($old_list as $key => $val) {
			$old_list2[$val['id']] = $val;
		}
		$old_list = $old_list2;
		$old_ids = y_array_column($old_list, 'product_info_id');
		$new_list = $_POST['product_info_list'];
		$new_ids = y_array_column($new_list, 'product_info_id');
		// 交集 修改/添加
		$update_ids = array_intersect($new_ids, $old_ids);
		// 差集 删除
		$delete_ids = array_diff($old_ids, $new_ids);

		foreach ($new_list as $key => $val) {
			if (in_array($val['product_info_id'], $update_ids)) {
				if ($val = $m_purchase_product->create($val)) {
					if (checkDataDifficult($val, $old_list2[$val['id']])) {
						$res = $m_purchase_product->save();
						if (!$res) {
							$this->msg = '修改失败[产品]';
							return false;
						}
					}
				} else {
					$this->msg = '修改失败[产品]';
					return false;
				}
			} else {
				$val['purchase_id'] = $this->purchase_id;
				if ($m_purchase_product->create($val)) {
					$res = $m_purchase_product->add();
					if (!$res) {
						$this->msg = '修改失败[产品]';
						return false;
					}
				} else {
					$this->msg = '修改失败[产品]';
					return false;
				}
			}
		}
		if ($delete_ids){
			$m_purchase_product->where(array('purchase_id' => $this->purchase_id, 'product_info_id' => array('IN', implode(',', $delete_ids))))->delete();
		}
		$this->msg = '修改成功';
		return true;
	}

	/**
	 * 修改失败，已改数据恢复
	 */
	public function reset()
	{
		$m_purchase_product = M('PurchaseProduct');
		$this->create($this->old_data['purchase']);
		$this->save();
		foreach ($this->old_data['product'] as $key => $val) {
			$m_purchase_product->create($val);
			$m_purchase_product->save();
		}
	}


	/**
	 * 删除添加失败的数据
	 */
	public function deleteData()
	{
		if ($this->purchase_id) {
			$this->where(array('purchase_id' => $this->purchase_id))->delete();
			M('PurchaseProduct')->where(array('purchase_id' => $this->purchase_id))->delete();
		}
	}

	/**
	 * 获取采购单关联产品信息
	 * 
	 */
	public function getPurcharseProductInfoList()
	{
		if ($this->purchase_id) {
			$d_stock_in = D('StockIn');
			$d_stock_out = D('StockOut');
			$product_info_list = D('PurchaseProductView')->getPurchaseProductList($this->purchase_id);
			foreach ($product_info_list as $key => $val) {
				$product_info_list[$key]['subtotal'] = $val['count'] * $val['price_discount'];
				$product_info_list[$key]['stock_in_count'] = $d_stock_in->getPurchaseProductCount($this->purchase_id, $val['product_info_id']);
				$product_info_list[$key]['return_count'] = $d_stock_out->purchaseReturnProductCount($this->purchase_id, $val['product_info_id']);
			}
			return $product_info_list;
		} else {
			$this->msg = "参数异常";
			return false;
		}
	}


	/**
	 * 采购单某产品数量
	 */
	public function getPurchaseProductCount($product_info_id)
	{
		if (!$this->purchase_id) return false;
		return M('PurchaseProduct')->where(array('product_info_id' => $product_info_id, 'purchase_id' => $this->purchase_id))->getField('count');
	}
	

	/**
	 * 判断是否能编辑
	 */
	public function canEdit($purchase_id)
	{
		// 防止数据异常
		if (M('StockIn')->where(array('type' => 1 , 'type_id' => $purchase_id))->count() > 0) {
			$this->msg = '已有入库信息，不可编辑';
			return false;
		}
		return true;
	}

	/**
	 * 执行审核
	 * @param Array $_POST['examine' => Int, 'purchase_id' => Int]
	 * @author Shenyue
	 */
	public function examine()
	{
		$examine_action = array('撤销', '审核', '驳回');
		if ($_POST['examine'] == 0 && D('StockIn')->getPurchaseHasStockIn($_POST['purchase_id'])){
			$this->msg = '已有产品入库，无法撤销';
		} else {
			$data = array(
				'purchase_id' => $_POST['purchase_id'],
				'examine' => $_POST['examine']
			);
			if ($this->create($data)) {
				if ($res = $this->save()) {
					$this->msg = $examine_action[(int) $_POST['examine']] . '成功';
					return true;
				} else {
					$this->msg = $examine_action[(int) $_POST['examine']] . '失败！';
				}
			} else {
				$this->msg = $examine_action[(int) $_POST['examine']] . '数据对象创建失败';
			}
		}
		return false;
	}

	
	/**
	 * dialog弹框展示使用
	 * @author Shenyue
	 */
	public function getPurchaseList($page, $_where = array())
	{
		$d_user = D('User');
		$below_ids = getPerByAction('purchase', 'index');
		$where = array('owner_role_id' => array('IN', $below_ids));
		$where += $_where;

		$this->count = $this->where($where)->count();
		$list = $this->where($where)->page($page)->field('purchase_id,name,owner_role_id,purchase_time,type,type_id,number')->order('purchase_time asc')->select();
		foreach ($list as $key => $val) {
			$list[$key]['purchase_time'] = date('Y-m-d', $val['purchase_time']);
			$list[$key]['owner_role_name'] = $d_user->get_full_name((int) $val['owner_role_id']);

			if ($val['type'] == 1) {
				$list[$key]['supplier_name'] = M('Supplier')->where('supplier_id = %d', $val['type_id'])->getField('name');
			} else if ($val['type'] == 2){
				$list[$key]['sales_number'] = M('Sales')->where('sales_id = %d', $val['type_id'])->getField('sn_code');
			}
		}
		return $list;
	}


	/**
	 * 获取供应商信息
	 */
	public function getSupplier($field = null)
	{
		if (!$this->purchase_id) {
			$this->msg = '没有主键';
			return false;
		}
		$m_supplier = M('Supplier');
		$supplier_id = $this->where(array('purchase_id' => $this->purchase_id))->getField('type_id');
		if ($field) {
			return $m_supplier->where(array('supplier_id' => $supplier_id))->getField($field);
		} else {
			return $m_supplier->where(array('supplier_id' => $supplier_id))->find();
		}
	}
	
	/**
	 * 获取销售退货入库信息信息
	 */
	public function getSalesReturnIn($sales_id)
	{
		// type 1 采购 2销退
		$purchase_id = $this->where(array('type' => 2, 'type_id' => $sales_id))->getField('purchase_id', true);
		return D('StockIn')->getReturnProduct($purchase_id);
	}

	/**
	 * 获取销售退货信息（合同相关退货单所有产品的订单数汇总）
	 */
	public function getSalesReturn($sales_id)
	{
		$purchase_id = $this->where(array('type' => 2, 'type_id' => $sales_id))->getField('purchase_id', true);
		$list = M('PurchaseProduct')->where(array('purchase_id' => array('IN', $purchase_id)))->field('product_info_id,count')->select();
		$res = array();
		foreach ($list as $key => $val) {
			if (!isset($res[$val['product_info_id']])) {
				$res[$val['product_info_id']] = 0;
			}
			$res[$val['product_info_id']] += $val['count'];
		}
		return $res;
	}

	/**
	 * 根据不同的表名和type值，返回不同的信息名称
	 * @param type
	 */
	function getTypeName($table_name, $type){
		if ($table_name == 'purchase') {
			if ($type == 1) {
				$type_name = '采购';
			} else if ($type == 2){
				$type_name = '销售退货';
			}
		}
		return $type_name;
	}


	/**
	 * 删除销售退货单及关联产品
	 * @author lee
	 */
	public function deleteReturn($sales_id)
	{
		if ($sales_id) {
			M('Sales')->where('sales_id = %d', $sales_id)->delete();
			M('SalesProduct')->where('sales_id = %d', $sales_id)->delete();
		}
	}


}