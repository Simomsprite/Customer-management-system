<?php 
/**
 * Template 
 * 模板模块
 *
 */ 

class TemplateAction extends Action {

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index', 'ajax_get_template', 'add', 'option', 'delete', 'ajax_template_check_name', 'edit', 'ajax_reset', 'preview', 'output_pdf', 'get_content', 'contract', 'status_update')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
	}


	/**
	 * 模板列表页
	 */
	public function index()
	{
		$this->object = M("template_object")->field('id,name')->select();
		$this->action = ACTION_NAME;
		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 模板列表数据获取
	 */
	public function ajax_get_template()
	{
		$object = $_POST['object'];
		$where = '';
		if ($object != 0) {
			$where = 'object_id='.$object;
		}
		$templates = M('template')->field('template_id,title,object_id,creator_role_id,create_time,update_time,is_default,system,status')->where($where)->select();
		$m_template_object = M('template_object');
		$d_user = D('User');
		if ($templates) {
			foreach ($templates as $key => $value) {
				$templates[$key]['create_time'] = date('Y-m-d H:i', $value['create_time']);
				$templates[$key]['update_time'] = date('Y-m-d H:i', $value['update_time']);
				$templates[$key]['creator_name'] = $d_user->get_full_name((int) $value['creator_role_id']);
				$templates[$key]['object_name'] = $m_template_object->where(array('id' => $value['object_id']))->getField('name');
			}
			$msg = '';
		} else {
			$msg = '暂无数据！';
		}
		$this->ajaxReturn(array('data' => $templates, 'msg' => $msg));
	}


	/**	
	 * 模板添加
	 */
	public function add()
	{
		if(IS_POST)
		{
			$data = $_POST;
			$template = M('template');
			$data['creator_role_id'] = session('role_id');
			$data['create_time'] = time();
			$data['update_time'] = time();
			$res = $template->add($data);
			if ($res) {
				$arr = array('msg' => '保存成功。', 'status' => 1);
			} else {
				$arr = array('msg' => '系统异常，稍后重试。', 'status' => 2);
			}
			$this->ajaxReturn($arr);
		} else {
			$object_id = $_GET['object_id'];
			$is_copy = $_GET['is_copy'];
			$content = '';
			if ($is_copy) {
				$tpl_id = $_GET['tpl_id'];
				$copy = M('template')->field('content')->where('template_id='.$tpl_id)->find();
				$content = $copy['content'];
			}
			$field = D('Template')->get_field($object_id);
			$this->field = $field;
			$this->is_default = $is_copy;
			$this->object_id = $object_id;
			$this->content = $content;
			$this->display();
		}
	}


	/**
	 *	模板添加选项
	 */
	public function option()
	{
		if (!checkPerByAction('template','add')) {
			echo '<div class="alert alert-error">您没有此权利！</div>';die();
		}
		$this->object = M('template_object')->field('id,name')->select();
		$this->exist_tpl = M('template')->field('template_id,title')->select();
		$this->display();
	}

	/**
	 * 模板删除
	 */
	public function delete()
	{
		if (!checkPerByAction('template','delete')) {
			$this->ajaxReturn(array('msg' => '您没有此权限！', 'status' => 2));
		}
		$id = $_POST['id'];
		$res = M('template')->where('template_id='.$id)->delete();
		if ($res) {
			$arr = array('msg' => '删除成功', 'status' => 1);
		} else {
			$arr = array('msg' => '删除失败，稍后重试。', 'status' => 2);
		}
		$this->ajaxReturn($arr);
	}


	/**
	 * 判断模板名称是否已经存在
	 */
	public function ajax_template_check_name()
	{
		$title = $_GET['title'];
		$where = isset($_GET['template_id']) ? ' && template_id!='. $_GET['template_id'] : '';
		$res = M('template')->where("title='{$title}'" . $where)->field('title')->find();
		$this->ajaxReturn(array('unique' => $res != null));
	}


	/**
	 * 	模板修改-加载视图
	 */
	public function edit()
	{
		if (!checkPerByAction('template','edit')) {
			if (IS_POST) {
				alert('error','您没有此权限！',0);
			} else {
				echo '<div class="alert alert-error">您没有此权利！</div>';die();
			}
		}
		if (IS_POST) {
			$data = $_POST;
			$data['update_time'] = time();
			$res = M('template')->save($data);
			if ($res) {
				$arr = array('status' => 1, 'msg' => '模板保存成功');
			} else {	
				$template = M('template')->where('template_id='.$_POST['template_id'])->field('template_id,title,content')->find();
				if ($template === $_POST) {
					$arr = array('status' => 1, 'msg' => '模板保存成功');
				} else {
					$arr = array('status' => 2, 'msg' => '系统异常，稍后重试。');
				}
			}
			$this->ajaxReturn($arr);
		} else {
			$template_id = $_GET['template_id'];
			$template = M('template')->field('default', true)->where('template_id=%d', $template_id)->find();
			$field = D('Template')->get_field($template['object_id']);
			$this->field = $field;
			$this->template = $template;
			$this->display();
		}
	}


	/**
	 * 	模板修改-初始化
	 * 	@return array 
	 * 	status 1：初始化成功；7：没有默认模板；3：未知错误，稍后重试；
	 * 	msg 提示信息
	 */
	public function ajax_reset()
	{
		if (!checkPerByAction('template','ajax_reset')) {
			$this->ajaxReturn(array('status' => '3', 'msg' => '您没有此权限！'));
		}
		$id = $_POST['id'];
		$default = M('template')->where('template_id='.$id)->field('is_default,default')->find();
		if ($default) {
			if ($default['is_default']) {
				$res = M('template')->where('template_id='.$id)->save(array('content' => $default['default']));
				if ($res) {
					$arr = array('status' => '1', 'msg' => '初始化成功。');
				} else {
					$arr = array('status' => '3', 'msg' => '未知错误，稍后重试。');
				}
			} else {
				$arr = array('status' => '7', 'msg' => '没有默认模板，无法初始化，可手动清空。');
			}
		} else {
			$arr = array('status' => '3', 'msg' => '未知错误，稍后重试。');
		}
		$this->ajaxReturn($arr);
	}


	/**
	 * 模板预览 && 模板数量查询
	 */
	public function preview()
	{
		if (IS_POST) {
			$object = $_POST['object'];
			$count = M('template')->where(array('object_id' => $object, 'status' => 1))->count();
			$this->ajaxReturn(array('count' => $count));
		} else {
			$has_content = false;
			if (isset($_GET['object']) && isset($_GET['id'])) {
				$object = $_GET['object'];
				$id = $_GET['id'];
				if (in_array($object, array('purchase', 'purchase_return', 'sales_return', 'stock_in', 'stock_out', 'transfer'))) {
					$object = '_' . $object;
					$data = $this->$object($id);
					$this->content = D('Template')->pssTemplate($data);
					$this->display('pss_print');
					exit;
				}
				$has_content = true;
				// 当前对象下所有模板
				$templates = M('template')->where(array('object_id' => $object, 'status' => 1))->field('template_id, title')->select();
				$this->object = $object;
				$this->id = $id;
				$this->templates = $templates;
			}
			$this->has_content = $has_content;
			$this->display();
		}
	}


	/**
	 *	输出pdf 用于预览和打印
	 */
	public function output_pdf()
	{
		$content = $_POST['content'];
		$aspect = $_POST['aspect'];
		$download = $_POST['download'] == 'yes';
		$pdfname = $_POST['pdfname'];
		$variable = array();
		$ecah = array();
		if (!empty($_POST['id']) && !empty($_POST['object'])) {
			$object = '_' . $_POST['object'];
			$id = $_POST['id'];
			$res = $this->$object($id, $content);
			$variable = $res['variable'];
			$each = $res['each'];
		}
		$content = analysis($content, $variable, $each);
		$content = '<style type="text/css" media="print">
			/*控制打印宽高自适应*/
			@page { size: portrait; }
			@page { size: landscape; }
			/*去除页眉页脚*/
			@page { margin: 0mm; }
			</style>'.$content;
		// 输出原html
		$this->content = $content;
		$this->display();
	}


	/**
	 * 	获取模板的内容
	 */
	public function get_content()
	{
		$id = $_POST['id'];
		$content = M('template')->where('template_id='.$id)->field('content')->find();
		if ($content) {
			$arr = array('msg' => '', 'data' => $content, 'status' => 1);
		} else {
			$arr = array('msg' => '系统异常，稍后重试', 'status' => 0);
		}
		$this->ajaxReturn($arr);
	}

	/**
	 *	合同对象 获取数据
	 *	@param 	$contract_id 	int   		合同ID
	 * 	@param 	$content 		string		模板内容
	 *  @return $res 			array = array(
	 *								'variable' => array(
	 *									'model_1' => array(
	 *										'field_1' => 'value_1',...
	 *									), ...
	 *								),
	 *								'each' => array(
	 *									'model_1' => array(
	 *										'field_1' => 'value_1',...
	 *									), ...
	 * 								)
	 *							)
	 */
	private function _contract($contract_id, $content)
	{
		preg_match_all("/{{([\w]+)\.[\w]+}}|{{[\w]+}}/", $content, $res);
		$models = array_unique($res[1]);

		$res = array();
		$variable = array();
		$each = array();
		$contract = D('ContractView')->where('contract.contract_id='.$contract_id)->find();
		$contract['creator_role_id'] = M('user')->where('role_id=%d', $contract['creator_role_id'])->getField('full_name');
		$contract['owner_role_id'] = M('user')->where('role_id=%d', $contract['owner_role_id'])->getField('full_name');
		$contract['business_id'] = M('business')->where('business_id=%d', $contract['business_id'])->getField('name');
		$variable['contract'] = $contract;

		// 客户信息
		if (in_array('customer', $models) || in_array('contacts', $models)) {
			$customer = D('CustomerView')->where('customer.customer_id='.$contract['customer_id'])->find();
			$customer_id = $customer['customer_id'];
			$contacts_id = $customer['contacts_id'];
			$customer['creator_role_id'] = M('user')->where('role_id=%d', $customer['creator_role_id'])->getField('full_name');
			$customer['owner_role_id'] = M('user')->where('role_id=%d', $customer['owner_role_id'])->getField('full_name');
			$variable['customer'] = $customer;
		}
			
		// 客户联系人
		if (in_array('contacts', $models)) {
			// 联系人
			if ($contacts_id == 0) {
				$contacts_id = M('r_contacts_customer')->where('customer_id = %d', $customer_id)->getField('contacts_id');
			}
			if ($contacts_id) {
				$contacts = D('ContactsView')->where('contacts.contacts_id=%d', $contacts_id)->find();
				$contacts['customer_id'] = $customer['name'];
				$variable['contacts'] = $contacts;
			}
		}

		// 产品 (遍历输出)
		if (in_array('product', $models) || in_array('sales', $models)) {
			/*$m_r_contract_sales = M('rContractSales');
			$sales_id = $m_r_contract_sales->where('contract_id = %d && sales_type = 0',$contract_id)->getField('sales_id');
			$sales = D('SalesView')->where('sales_id = %d', $sales_id)->find();
			$business_id = D('ContractView')->where(array('contract_id'=>$contract_id))->getField('business_id');
			$sales['business'] = M('Business')->where('business_id = %d',$business_id)->getField('code');
			$variable['sales'] = $sales;

			if (in_array('product', $models)) {
				$m_product = M('Product');
				$m_product_category = M('ProductCategory');
				$m_sales_product = M('salesProduct');
				$sales_product = $m_sales_product->where('sales_id = %d',$sales['sales_id'])->order('sales_product_id ASC')->select();
				foreach ($sales_product as $k=>$v) {
					$product = $m_product->where('product_id = %d',$v['product_id'])->find();
					$product['category_name'] = $m_product_category->where('category_id =%d',$product['category_id'])->getField('name');
					$sales_product[$k]['product_name'] = $product['name'];
					$sales_product[$k]['product'] = $product;
				}
				$each['product'] = $sales_product;
			}*/

			//产品信息
			$product_info = D('Contract')->getProductList($contract_id);
			$each['product'] = $product_info['product_list'];
			$variable['sales'] = $product_info['sales'];
		}
// p($variable['sales'],'');
// p($each);
		$res['variable'] = $variable;	
		$res['each'] = $each;
		return $res;
	}

	/**
	 * 	模板的启用
	 */
	public function status_update()
	{
		if (!checkPerByAction('template','edit')) {
			$arr = array('msg' => '您没有此权限！', 'status' => 0);
			$this->ajaxReturn($arr);
		}
		if (IS_POST) {
			if (in_array($_POST['status'], array(1, 0))) {
				$res = M('template')->save($_POST);
				if ($res) {
					$arr = array('msg' => '模板状态修改成功', 'status' => 1);
				} else {
					$arr = array('msg' => '修改失败，稍后重试！', 'status' => 0);
				}
			} else {
				$arr = array('msg' => '数据错误，刷新后重试！', 'status' => 0);
			}
			$this->ajaxReturn($arr);
		} else {
			$this->ajaxReturn('', '错误的请求方式', 0);
		}
	}


	/**
	 * 采购单
	 */
	public function _purchase($id)
	{
		$purchase_id = $this->_request('id','intval');
		$d_purchase = D('Purchase');
		// 采购单信息
		$purchase = $d_purchase->getView($purchase_id);
		$product_list = $d_purchase->getPurcharseProductInfoList();  // 获取产品信息
		$product = array(array('序号', '产品名称', '规格', '单位', '采购价格(元)', '折扣(%)', '采购数量', '小计'));
		$i = 1;
		foreach ($product_list as $val) {
			$product[] = array(
				$i, $val['name'], $val['spec']['string'], $val['standard'], number_format($val['price_discount'], 2), $val['discount'], $val['count'], number_format($val['subtotal'], 2) 
			);
			$i++;
		}
		$data = array(
			'title' => '采购单-' . $purchase['name'],
			'header' => array(
				'采购单号' => $purchase['number'],
				'制单人' => $purchase['creator_name'],
				'供应商' => $purchase['supplier']['name'],
				'负责人' => $purchase['owner_name'],
				'采购日期' => $purchase['purchase_time'],
				'产品合计(元)' => number_format($purchase['prime_price'], 2),
				'整单折扣(%)' => $purchase['discount'],
				'采购金额(元)' => number_format($purchase['purchase_amount'], 2),
				'其他金额(元)' => number_format($purchase['other_amount'], 2),
				'最终合计(元)' => number_format($purchase['purchase_amount'] + $purchase['other_amount'], 2),
				'备注' => $purchase['remark']
			),
			'product' => $product
		);
		return $data;
	}


	/**
	 * 销售退货
	 */
	public function _sales_return($id)
	{
		$purchase_id = $this->_request('id','intval');
		$d_purchase = D('Purchase');
		// 采购单信息
		$purchase = $d_purchase->getView($purchase_id);
		$purchase['contract_id'] = M('RContractSales')->where(array('sales_id' => $purchase['type_id']))->getField('contract_id');
		$purchase['contract_number'] = M('Contract')->where(array('contract_id' => $purchase['contract_id']))->getField('number');
		$product_list = $d_purchase->getPurcharseProductInfoList();  // 获取产品信息
		$product = array(array('序号', '产品名称', '规格', '单位', '销售退货价格(元)', '折扣(%)', '销售退货数量', '小计'));
		$i = 1;
		foreach ($product_list as $val) {
			$product[] = array(
				$i, $val['name'], $val['spec']['string'], $val['standard'], number_format($val['price_discount'], 2), $val['discount'], $val['count'], number_format($val['subtotal'], 2) 
			);
			$i++;
		}
		$data = array(
			'title' => '采购单-' . $purchase['name'],
			'header' => array(
				'销售退货单号' => $purchase['number'],
				'关联合同' => $purchase['contract_number'],
				'制单人' => $purchase['creator_name'],
				'负责人' => $purchase['owner_name'],
				'退货日期' => $purchase['purchase_time'],
				'产品合计(元)' => number_format($purchase['prime_price'], 2),
				'最终销售退货金额(元)' => number_format($purchase['purchase_amount'], 2),
				'备注' => $purchase['remark']
			),
			'product' => $product
		);
		return $data;
	}


	/**
	 * 采购退货
	 */
	public function _purchase_return($id)
	{
		$d_sales = D('Sales');
		$sales = $d_sales->getView($id);
		$product_list = $d_sales->getProductList($id);
		$product = array(array('序号', '产品名称', '规格', '单位', '采购价格(元)', '退货价格(元)', '退货数量', '小计'));
		$i = 1;
		foreach ($product_list as $val) {
			$product[] = array(
				$i, $val['product_name'], $val['spec'], $val['unit'], number_format($val['ori_price'], 2), number_format($val['unit_price'], 2), $val['count'], number_format($val['subtotal'], 2) 
			);
			$i++;
		}
		$data = array(
			'title' => '采购退货单-' . $sales['subject'],
			'header' => array(
				'采退订单号' => $sales['sn_code'],
				'关联采购单' => $sales['purchase_number'],
				'供应商' => $sales['supplier_name'],
				'制单人' => $sales['creator_role_name'],
				'负责人' => $sales['owner_role_name'],
				'退货日期' => $sales['sales_time'],
				'退货金额(元)' => number_format($sales['sales_price'], 2),
				'产品合计(元)' => number_format($sales['prime_price'], 2),
				'备注' => $sales['description']
			),
			'product' => $product
		);
		return $data;
	}


	/**
	 * 入库
	 */
	public function _stock_in($id)
	{
		$stock_in = M('StockIn')->find($id);
		$d_purchase = D('Purchase');
		$d_purchase->purchase_id = $stock_in['type_id'];
		$warehouse_name = M('Warehouse')->where(array('warehouse_id' => $stock_in['warehouse_id']))->getField('name');
		$owner_role_name = M('User')->where(array('role_id' => $stock_in['owner_role_id']))->getField('full_name');
		$product_list = D('StockInProductinfo')->getStockInProductList($stock_in['stock_in_id']);
		$product = array(array('序号', '产品名称', '规格', '单位', '采购数量', '本次入库数', '备注'));
		$i = 1;
		foreach ($product_list as $val) {
			$product[] = array(
				$i, $val['name'], $val['spec']['string'], $val['standard'], $val['purchase_product_count'], $val['count'], $val['remark']
			);
			$i++;
		}
		$data = array(
			'title' => '入库单',
			'header' => array(
				'入库单号' => $stock_in['number'],
				'入库日期' => $stock_in['create_time'] ? date('Y-m-d', $stock_in['create_time']) : '-',
				'入库单号' => $stock_in['number'],
				'入库仓' => $warehouse_name,
				'经办人' => $owner_role_name,
				'备注' => $stock_in['remark']
			),
			'product' => $product
		);
		return $data;
	}


	/**
	 * 出库
	 */
	public function _stock_out($id)
	{
		$stock_out = M('StockOut')->find($id);
		$warehouse_name = M('Warehouse')->where(array('warehouse_id' => $stock_out['warehouse_id']))->getField('name');
		$owner_role_name = M('User')->where(array('role_id' => $stock_out['owner_role_id']))->getField('full_name');
		$product_list = D('StockOut')->getProductInfoList($id);
		$product = array(array('序号', '产品名称', '规格', '单位', '本次出库数', '备注'));
		$i = 1;
		foreach ($product_list as $val) {
			$product[] = array(
				$i, $val['product_name'], $val['spec'], $val['unit'], $val['count'], $val['remark']
			);
			$i++;
		}
		$data = array(
			'title' => '出库单',
			'header' => array(
				'出库单号' => $stock_out['number'],
				'入库日期' => $stock_out['create_time'] ? date('Y-m-d', $stock_out['create_time']) : '-',
				'出库仓' => $warehouse_name,
				'经办人' => $owner_role_name,
				'物流' => $stock_out['express'],
				'备注' => $stock_out['remark']
			),
			'product' => $product
		);
		return $data;
	}


	/**
	 * 调拨
	 */
	public function _transfer($id)
	{
		$transfer = D('Stock')->getTransferDetail(array('transfer_id' => $id));
		switch ($transfer['status']) {
			case '0':
				$status = '未出库';
				break;
			case '1':
				$status = '在途';
				break;
			case '2':
				$status = '已入库';
				break;
		}
		$header = array(
			'调拨单号' => $transfer['number'],
			'状态' => $status,
			'创建时间' => $transfer['create_time'] ? date('Y-m-d', $transfer['create_time']) : '-',
			'调出仓' => $transfer['out_warehouse_name'],
			'调入仓' => $transfer['in_warehouse_name'],
			'负责人' => $transfer['owner_role_name'],
			'创建人' => $transfer['creator_role_name'],
			'备注' => $transfer['remark']
		);
		if ($transfer['status'] > 0) {
			$stock_out = D('Stock')->getStockOutOrIn(array('type' => 3, 'type_id' => $id), 'out');
			$header['出库单号'] = $stock_out['number'];
			$header['出库日期'] = $stock_out['create_time'];
			$header['出库负责人'] = $stock_out['owner_role_name'];
			$header['出库备注'] = $stock_out['remark'];
		}
		if ($transfer['status'] == 2) {
			$stock_in = D('Stock')->getStockOutOrIn(array('type' => 3, 'type_id' => $id), 'in');
			$header['入库单号'] = $stock_in['number'];
			$header['入库日期'] = $stock_in['create_time'];
			$header['入库负责人'] = $stock_in['owner_role_name'];
			$header['入库备注'] = $stock_in['remark'];
		}

		$product_list = D('Stock')->getTransferProcut($id);
		$product = array(array('序号', '产品名称', '规格', '单位', '数量', '备注'));
		$i = 1;
		foreach ($product_list as $val) {
			$product[] = array(
				$i, $val['product_name'], $val['spec'], $val['unit'], $val['count'], $val['remark']
			);
			$i++;
		}
		$data = array(
			'title' => '调拨单',
			'header' => $header,
			'product' => $product
		);
		return $data;
	}
}
