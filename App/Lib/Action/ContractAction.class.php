<?php 
/**
*合同模块
*
**/
class ContractAction extends Action {
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('check_list','listdialog','yanchong','elide_edit','set_target','getmonthlycontract','choose','check','revokecheck','checkper','analysis_number','received_top10','target_rank')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
	}
	
	/**
	*添加合同
	*
	**/
	public function add(){
		// $m_contract = M('Contract');
		$m_contacts = M('Contacts');
		$m_contract = D('Contract');
		$m_contract_data = D('ContractData');
		$m_product = M('Product');
		$m_product_info = M('ProductInfo');
		$m_r_contacts_customer = M('RContactsCustomer');
		$contract_custom = M('config') -> where('name="contract_custom"')->getField('value');
		if(!$contract_custom) {
			$contract_custom = 'pd_crm';
		}
		if($this->isPost()){
			//判断合同编号是否存在
			if ($m_contract->where(array('number'=>$contract_custom.trim($_POST['number'])))->find()) {
				$this->error('该合同编号已存在！');
			}
			//处理自定义字段数据
			$field_list = M('Fields')->where(array('model'=>'contract','in_add'=>1))->order('order_id')->select();
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$_POST[$v['field']] = $_POST[$v['field']] ? implode(chr(10),$_POST[$v['field']]) : '';
					break;
					case 'datetime':
						$_POST[$v['field']] = strtotime($_POST[$v['field']]);
					break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$b = array_filter($_POST[$v['field']]);
							$_POST[$v['field']] = !empty($b) ? implode(chr(10),$b) : '';
						}
					break;
				}
			}
			if ($m_contract->create()) {
				if($m_contract_data->create() !== false){
					$m_contract->type = 1;
					if(empty($_POST['customer_id']) && isset($_POST['business_id'])){
						$customer_id = M('business')->where('business_id = %d', $_POST['business_id'])->getField('customer_id');
						$customer_id = empty($customer_id) ? 0 : $customer_id;
						$m_contract->customer_id = $customer_id;
					}else{
						$m_contract->customer_id = intval($_POST['customer_id']);
					}
					// $m_contract->due_time = time();
					$m_contract->owner_role_id = $_POST['owner_role_id'] ? intval($_POST['owner_role_id']) : session('role_id');
					$m_contract->examine_role_id = $_POST['examine_role_id'] ? trim($_POST['examine_role_id']) : '';
					$m_contract->creator_role_id = session('role_id');
					$m_contract->create_time = time();
					$m_contract->update_time = time();
					$m_contract->count_nums = $_POST['total_amount_val'];
					$m_contract->status = L('HAS_BEEN_CREATED');
					$m_contract->number = $contract_custom.trim($_POST['number']);
					$m_contract->prefixion = $contract_custom;
					if($contractId = $m_contract->add()){
						$m_contract_data->contract_id = $contractId;
						if ($m_contract_data->add()) {
							//关联日程
							$event_res = dataEvent('合同到期',$_POST['end_date'],'contract',$contractId);

							//相关附件
							if($_POST['file']){
								$m_contract_file = M('RContractFile');
								foreach($_POST['file'] as $v){
									$file_data = array();
									$file_data['contract_id'] = $contractId;
									$file_data['file_id'] = $v;
									$m_contract_file->add($file_data);
								}
							}
							//创建销售单//生成序列号
							$table_info = getTableInfo('sales');
							$m_sales = M('Sales');
							if($m_sales->create()){
								$m_sales->creator_role_id = session('role_id');
								$m_sales->status = 97;//未出库
								$m_sales->type = 0;
								$max_id = $m_sales->where(array('type' => 0))->count() + 1;
								$m_sales->sn_code = 'XSD_' . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);
								$m_sales->sales_time = time();
								$m_sales->create_time = time();

								$m_sales->final_discount_rate = $_POST['final_discount_rate'];
								$m_sales->sales_price = $_POST['final_price'];
								$m_sales->total_amount = $_POST['total_amount_val'];
								
								if ($sales_id = $m_sales->add()) {
									//关系表
									$m_r_contract_sales = M('rContractSales');
									$r_data['contract_id'] = $contractId;
									$r_data['sales_id'] = $sales_id;
									$m_r_contract_sales->add($r_data);

									if($_POST['business']['product']){
										$add_product_flag = true;
										$m_sales_product = M('salesProduct');
										foreach($_POST['business']['product'] as $v){
											if(!empty($v['product_info_id'])){
												$data = array();
												$count_nums += 1;
												$data['sales_id'] = $sales_id;
												$data['product_info_id'] = $v['product_info_id'];
												//$data['warehouse_id'] = $v['warehouse_id'];
												$data['amount'] = $v['amount'];		// 数量
												$data['unit_price'] = $v['unit_price'];		// 销售时商品单价
												$data['discount_rate'] = $v['discount_rate'];		// 折扣率
												$data['subtotal'] = $v['subtotal'];		// 小计
												$data['unit'] = $v['unit'];		// 单位
												//产品成本
												$cost_price = 0;
												$cost_price = $m_product_info->where('product_info_id = %d',$v['product_info_id'])->getField('price_cost_avg');
												$data['cost_price'] = $cost_price ? $cost_price : 0;
												//销售时产品售价
												$data['ori_price'] = $v['ori_price'];
												$sales_product_id = $m_sales_product->add($data);
												if(empty($sales_product_id)){
													$add_product_flag = false;
													break;
												}
											}
										}
										if(!$add_product_flag){
											alert('error','合同产品信息创建失败！',$_SERVER['HTTP_REFERER']);
										}
									}
								}
							}
							
							//商机状态改为合同签订，客户自动锁定，客户状态改为“已成交客户”
							$business_id = intval($_POST['business_id']);
							$customer_id = intval($_POST['customer_id']);
							$m_business = M('Business');
							$status_type_id = $m_business->where(array('business_id'=>$business_id))->getField('status_type_id');
							$status_id = M('BusinessStatus')->where(array('type_id'=>$status_type_id,'is_end'=>3))->getField('status_id');
							$m_business->where(array('business_id'=>$business_id))->setField('status_id',$status_id);

							$data_customer = array();
							$data_customer['is_locked'] = 1;
							$data_customer['customer_status'] = '已成交客户';							
							M('Customer')->where(array('customer_id'=>$customer_id))->save($data_customer);
							M('RBusinessContract')->add(array('contract_id'=>$contractId,'business_id'=>$business_id));
							actionLog($contractId);
							
							//通知合同相关审核人
							$url = U('contract/view','id='.$contractId);
							//合同审批人类型
							$contract_examine = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
							$contract_examine = $contract_examine ? $contract_examine : '0';
							if ($contract_examine) {
								//自定义流程
								$role_ids_a = M('ContractExamine')->order('order_id asc')->getField('role_id');
								$role_ids_a = array_filter(explode(',', $role_ids_a));
							} else {
								//合同审核人
								$position_ids = M('Permission')->where(array('url'=>'contract/check'))->getField('position_id',true);
								$position_ids = !empty($position_ids) ? $position_ids : array();
								$role_ids_a = M('Role')->where(array('position_id'=>array('in',$position_ids)))->getField('role_id',true);
							}
							if (!$role_ids_a) {
								//管理员
								$role_ids_a = M('User')->where(array('category_id'=>1,'status'=>1))->getField('role_id',true);
							}
							foreach($role_ids_a as $v){
								sendMessage($v,$_SESSION['name'].'&nbsp;&nbsp;创建了新的合同《<a href="'.$url.'">'.trim($_POST['number']).'-'.trim($_POST['contract_name']).'</a>》<font style="color:green;">需要进行审核</font>！',1);
							}
							
							if($_POST['refer_url']){
								alert('success', L('CREATE_A_CONTRACT_SUCCESSFULLY'), $_POST['refer_url'].'#tab6');
							}else{
								alert('success', L('CREATE_A_CONTRACT_SUCCESSFULLY'), U('contract/index'));
							}
						}
					}else{
						alert('error', L('FAILED_TO_CREATE_THE_CONTRACT'), U('contract/add'));
					}
				} else {
					$this->error($m_contract_data->getError());
				}
			} else {
				$this->error($m_contract->getError());
			}
		}else{
			$m_business = M('Business');
			$d_product_info = D('ProductInfo');
			if (intval($_GET['business_id'])) {
				$business_id = intval($_GET['business_id']);
				$business = $m_business->where('business_id = %d',$business_id)->find();
				//$business['customer_id'] = M('RBusinessCustomer')->where('business_id = %d',$business_id)->getField('customer_id');
				$this_customer = M('Customer')->where('customer_id = %d',$business['customer_id'])->find();
				if(!empty($this_customer['contacts_id'])){
					$contacts = $m_contacts->where('is_deleted = 0 and contacts_id = %d',$this_customer['contacts_id'])->find();
				}else{
					$contacts_customer = $m_r_contacts_customer->where('customer_id = %d',$business['customer_id'])->limit(1)->order('id desc')->select();
					if(!empty($contacts_customer)){
						$contacts = $m_contacts->where('is_deleted = 0 and contacts_id = %d',$contacts_customer[0]['contacts_id'])->find();
					}
				}
				$business['contacts'] = $contacts;
				$business['c_address'] = $this_customer['address'];
				$business['customer_name'] = $this_customer['name'];
				$business['business_name'] = $business['name'];
				//产品信息		
				$business_product_list = M('RBusinessProduct')->where('business_id = %d',$business_id)->select();
				foreach($business_product_list as $k=>$v){
					$product_info = $d_product_info->getNameSpec($v['product_info_id']);
					$business_product_list[$k] = array_merge($business_product_list[$k], $product_info);
				}
				$business['business_product_list'] = $business_product_list;
				$this->contract_info = $business;
			} elseif (intval($_GET['old_contract_id'])) {
				//续约合同
				$d_contract = D('ContractView');
				$m_contract = M('Contract');
				$old_con_id = intval($_GET['old_contract_id']);
				$contract_info = $d_contract->where('contract.contract_id = %d',$old_con_id)->find();
				$contract_info['final_price'] = $contract_info['price'];
				$contract_info['customer_name'] = M('Customer')->where('customer_id = %d',$contract_info['customer_id'])->getField('name');
				$contract_info['business_name'] = $m_business->where('business_id = %d',$contract_info['business_id'])->getField('name');
				$sales_id = M('rContractSales')->where('contract_id = %d && sales_type = 0',$old_con_id)->getField('sales_id');
				$sales_info = D('SalesView')->where('sales_id = %d', $sales_id)->find();
				$contract_info['sales_info'] = $sales_info;
				$m_sales_product = M('salesProduct');
				$m_product = M('product');
				$m_product_category = M('ProductCategory');
				$sales_product = $m_sales_product->where('sales_id = %d',$sales_info['sales_id'])->order('sales_product_id ASC')->select();
				foreach($sales_product as $k=>$v){
					$product = array();
					$sales_product[$k]['product_name'] = $m_product->where('product_id = %d',$v['product_id'])->getField('name');
					//小计
				}
				$contract_info['business_product_list'] = $sales_product;
				//续约组父类ID
				$this->renew_parent_id = $contract_info['renew_parent_id'] ? $contract_info['renew_parent_id'] : $old_con_id;
				$this->contract_info = $contract_info;
				$this->old_contract_id = $old_con_id;
			}
			$contract_max_id = $m_contract->max('contract_id');
			$contract_max_id = $contract_max_id+1;
			$contract_max_code = str_pad($contract_max_id,4,0,STR_PAD_LEFT);//填充字符串的左侧（将字符串填充为新的长度）
			$this->assign('contract_custom', $contract_custom);
			$this->assign('number', date('Ymd').'-'.$contract_max_code);
			$this->refer_url = $_SERVER['HTTP_REFERER'];
			//合同审批人类型
			$contract_examine = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
			if ($contract_examine == 1) {
				$examine_step = M('ContractExamine')->order('order_id asc')->find();
				$role_list = M('User')->where(array('role_id'=>array('in',explode(',',$examine_step['role_id']))))->field('full_name,role_id,thumb_path')->select();
				foreach ($role_list as $k=>$v) {
					$role_list[$k]['thumb_path'] = headPathHandle($v['thumb_path']);
				}
				$examine_step['role_list'] = $role_list;
				//审批关系
				$relation_name = '';
				if ($examine_step['relation'] == 1) {
					$relation_name = '并';
				} elseif ($examine_step['relation'] == 2) {
					$relation_name = '或';
				}
				$examine_step['relation_name'] = $relation_name;
				$this->examine_step = $examine_step;
			}
			$this->contract_examine = $contract_examine ? $contract_examine : '0';
			//自定义字段
			$field_list = field_list_html("add","contract");
		 	$this->field_list = $field_list;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	/**
	*编辑合同
	*
	**/
	public function edit(){
		$contract = D('ContractView');
		if($this->isPost()){
			$contract_id = $_POST['contract_id'] ? intval($_POST['contract_id']) : '';
		}else{
			$contract_id = $_GET['id'] ? intval($_GET['id']) : '';
		}
		if (!$contract_id) {
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
		$contract_info = $contract->where('contract.contract_id = %d',$contract_id)->find();

		if(!$contract_info){
			$this->error(L('PARAMETER_ERROR'));
		}elseif($this->_permissionRes && !in_array($contract_info['owner_role_id'], $this->_permissionRes)){
			alert('error',L('DO NOT HAVE PRIVILEGES'),$_SERVER['HTTP_REFERER']);
		}
		if($contract_info['is_checked'] == 1){
			alert('error','合同已审核，无法编辑！',U('contract/index'));
		}
		if($contract_info['is_checked'] == 3){
			alert('error','合同正在审批中，无法编辑！',U('contract/index'));
		}
		$m_product = M('Product');
		$m_product_info = M('ProductInfo');
		$m_sales_product = M('SalesProduct');
		// $m_contract = M('Contract');
		$m_contract = D('Contract');
		$m_contract_data = D('ContractData');
		
		$r_contract_sales_info = M('rContractSales')->where(array('contract_id'=>$contract_id,'sales_type'=>array('neq',1)))->find();
		if (is_array($contract_info)) {
			if($this->isPost()){
				$_POST['market_id'] = isset($_POST['market_id']) ? $_POST['market_id'] : 0;
				//判断合同编号是否存在
				if ($m_contract->where(array('number'=>trim($_POST['number']),'contract_id'=>array('neq',$contract_id)))->find()) {
					$this->error('该合同编号已存在！');
				}

				$field_list = M('Fields')->where('model = "contract"')->order('order_id')->select();
				foreach ($field_list as $v){
					switch($v['form_type']) {
						case 'address':
							$_POST[$v['field']] = $_POST[$v['field']] ? implode(chr(10),$_POST[$v['field']]) : '';
							break;
						case 'datetime':
							$_POST[$v['field']] = strtotime($_POST[$v['field']]);
							break;
						case 'box':
							eval('$field_type = '.$v['setting'].';');
							if($field_type['type'] == 'checkbox'){
								$_POST[$v['field']] = implode(chr(10),$_POST[$v['field']]);
							}
						break;
					}
				}
				$m_sales = M('Sales');

				//查询合同附表是否存在
				$res_contract_data = $m_contract_data->where(array('contract_id'=>$contract_id))->find();
				if ($m_contract->create()) {
					// $m_contract->due_time = time();
					$m_contract->owner_role_id = $_POST['owner_role_id'] ? $_POST['owner_role_id'] : session('role_id');
					$m_contract->update_time = time();
					$m_contract->count_nums = $_POST['total_amount_val'];

					$res_contract_data_arr = $m_contract_data->create();
					if ($res_contract_data_arr !== false) {
						$a = $m_contract->where(array('contract_id'=>$contract_id))->save();
						if ($res_contract_data) {
							$b = $m_contract_data->save();
						} else {
							$m_contract_data->contract_id = $contract_id;
							$b = $m_contract_data->add();
						}

						if (count($res_contract_data_arr) == 1 && $_POST['contract_id']) {
							$b = true;
						}
						if ($a && $b !== false) {
							// 添加操作记录
							recordAction($_POST, $contract_info, 'contract', $contract_info['contract_id']);

							$m_contract->where('contract_id = %d', $contract_id)->setField('is_checked',0);
							//关联日程
							$event_res = dataEvent('合同到期',$_POST['end_date'],'contract',$contract_id);
							//修改销售单
							if($m_sales->create()){
								$m_sales->sales_time = time();
								$m_sales->final_discount_rate = $_POST['final_discount_rate'];
								$m_sales->sales_price = $_POST['final_price'];
								$m_sales->total_amount = $_POST['total_amount_val'];
								$sales_id = intval($_POST['sales_id']);

								$r_sales_id = M('rContractSales')->where('contract_id = %d && sales_type = 0',$contract_id)->getField('sales_id');
								if (!$sales_id && !$r_sales_id) {
									//处理之前编辑时没有创建相关sales数据导致无法编辑产品的问题
									$m_sales->creator_role_id = session('role_id');
									$m_sales->status = 97;//未出库
									$m_sales->type = 0;
									$max_id = $m_sales->where(array('type' => 0))->count() + 1;
									$m_sales->sn_code = 'XSD_' . date('Ymd') . '-' . str_pad($max_id,4,0,STR_PAD_LEFT);
									$m_sales->sales_time = time();
									$m_sales->create_time = time();
									$sales_id = $m_sales->add();
									//关系表
									$m_r_contract_sales = M('rContractSales');
									$r_data['contract_id'] = $contractId;
									$r_data['sales_id'] = $sales_id;
									$m_r_contract_sales->add($r_data);

									if($_POST['business']['product'] && $sales_id){
										$add_product_flag = true;
										$m_sales_product = M('salesProduct');
										foreach($_POST['business']['product'] as $v){
											if(!empty($v['product_info_id'])){
												$count_nums += 1;
												$data_product['sales_id'] = $sales_id;
												$data_product['product_info_id'] = $v['product_info_id'];
												$data_product['amount'] = $v['amount'];
												$data_product['unit_price'] = $v['unit_price'];
												$data_product['discount_rate'] = $v['discount_rate'];
												$data_product['subtotal'] = $v['subtotal'];
												$data_product['unit'] = $v['unit'];
												//产品成本
												$cost_price = 0;
												$cost_price = $m_product_info->where('product_info_id = %d',$v['product_info_id'])->getField('price_cost_avg');
												$data_product['cost_price'] = $cost_price ? $cost_price : 0;
												//销售时产品售价
												$data_product['ori_price'] = $v['ori_price'];
												$sales_product_id = $m_sales_product->add($data_product);
												if(empty($sales_product_id)){
													$add_product_flag = false;
													break;
												}
											}
										}
										if (!$add_product_flag) {
											alert('error','合同产品信息创建失败！',$_SERVER['HTTP_REFERER']);
										} else {
											alert('success','合同信息修改成功！',U('contract/view','id='.$contract_id));
										}
									} else {
										alert('success','合同信息修改成功！',U('contract/view','id='.$contract_id));
									}
								} else {
									if($m_sales->where('sales_id = %d',$sales_id)->save()){
										if ($_POST['business']['product']) {
											//旧的sales_product_id
											$old_sales_product_ids = array();
											$old_sales_product_ids = $m_sales_product->where('sales_id = %d',$sales_id)->getField('sales_product_id',true);
											$new_sales_product_ids = array();
											foreach($_POST['business']['product'] as $v){
												$add_product_flag = true;
												$data = array();
												if(!empty($v['product_info_id'])){
													if(!empty($v['sales_product_id'])){
														$data['amount'] = $v['amount'];
														$data['unit_price'] = $v['unit_price'];
														$data['discount_rate'] = $v['discount_rate'];
														$data['subtotal'] = $v['subtotal'];
														$data['unit'] = $v['unit'];
														//产品成本
														$cost_price = 0;
														$cost_price = $m_product->where('product_id = %d',$v['product_id'])->getField('cost_price');
														$data['cost_price'] = $cost_price ? $cost_price : 0;
														$sales_product_id = $m_sales_product->where('sales_product_id = %d',$v['sales_product_id'])->save($data);
														//剩余的的sales_product_id
														$new_sales_product_ids[] = $v['sales_product_id'];
													}else{
														$data['sales_id'] = $sales_id;
														$data['product_info_id'] = $v['product_info_id'];
														//$data['warehouse_id'] = $v['warehouse_id'];
														$data['amount'] = $v['amount'];
														$data['unit_price'] = $v['unit_price'];
														$data['discount_rate'] = $v['discount_rate'];
														$data['subtotal'] = $v['subtotal'];
														$data['unit'] = $v['unit'];
														//产品成本
														$cost_price = 0;
														$cost_price = $m_product->where('product_id = %d',$v['product_id'])->getField('cost_price');
														$data['cost_price'] = $cost_price ? $cost_price : 0;
														//销售时产品售价
														$data['ori_price'] = $v['ori_price'];
														$sales_product_id = $m_sales_product->add($data);
														if (!$sales_product_id) {
															$add_product_flag = false;
															break;
														}
													}
												}
											}
											//需要删除的sales_product_id
											$del_sales_product_ids = array();
											$del_sales_product_ids = array_diff($old_sales_product_ids, $new_sales_product_ids);
											if($del_sales_product_ids){
												if(!$m_sales_product->where(array('sales_product_id'=>array('in',$del_sales_product_ids)))->delete()){
													$add_product_flag = false;
												}
											}
											if($add_product_flag){
												M('Sales')->where('sales_id =%d',$sales_id)->setField('is_checked',0);
												alert('success', L('MODIFY_THE_SUCCESS'),U('contract/view','id='.$contract_id));
											}else{
												alert('error','合同产品信息修改失败！',$_SERVER['HTTP_REFERER']);
											}
										} else {
											alert('success','修改成功！',U('contract/view','id='.$contract_id));
										}
									}else{
										alert('error','修改失败，请重试！',U('contract/view','id='.$contract_id));
									}
								}
							}else{
								alert('error','修改失败，请重试！',U('contract/view','id='.$contract_id));
							}
						} else {
							alert('error','修改失败，请重试！',U('contract/view','id='.$contract_id));
						}
					}else{
						$this->error($m_contract_data->getError());
					}
				} else {
					$this->error($m_contract->getError());
				}
			}else{
				if ($contract_info['customer_id']) {
					$this->market_list = D('Market')->get_customer_market_list($contract_info['customer_id']);
				}
				$sales_id = M('rContractSales')->where('contract_id = %d && sales_type = 0',$contract_id)->getField('sales_id');
				$d_sales = D('SalesView');
				$sales = $d_sales->where('sales_id = %d', $sales_id)->find();
				$m_sales_product = M('salesProduct');
				$m_product = M('Product');
				$m_product_category = M('ProductCategory');
				$sales_product = $m_sales_product->where('sales_id = %d',$sales['sales_id'])->order('sales_product_id ASC')->select();
				$d_product_info = D('ProductInfo');
				foreach($sales_product as $k=>$v){
					$product_info = $d_product_info->getNameSpec($v['product_info_id']);
					$sales_product[$k] = array_merge($sales_product[$k], $product_info);
				}
				$this->sales = $sales;
				$this->sales_product = $sales_product;
				$this->assign('info',$contract_info);
				//自定义字段
				$this->field_list = field_list_html('edit','contract',$contract_info);
				$this->alert = parseAlert();
				$this->display();
			}
		}else{
			$this->error(L('THERE_IS_NO_DATA'));
		}
	}
	
	/**
	*查看合同详情
	*
	**/
	public function view(){
		$d_user = D('User');
		$contract_id = intval($_REQUEST['id']);
		$d_contract = D('ContractView');
		$d_role = D('RoleView');
		$m_user = M('User');
		if (!$contract_id){
			alert('error', L('NOT CHOOSE ANY'), U('contract/index'));
		}
		//附表数据判断
		$m_contract_data = M('ContractData');
		if (!$m_contract_data->where(array('contract_id'=>$contract_id))->find()) {
			$res_data = array();
			$res_data['contract_id'] = $contract_id;
			$m_contract_data->add($res_data);
		}
		$info = $d_contract->where(array('contract_id'=>$contract_id))->find();
		//权限判断
		if(empty($info)) {
			alert('error', L('THE_CONTRACT_DOES_NOT_EXIST_OR_HAS_BEEN_DELETED'), U('contract/index'));
		}elseif(!in_array($info['owner_role_id'], $this->_permissionRes)) {
			alert('error',L('DO NOT HAVE PRIVILEGES'),$_SERVER['HTTP_REFERER']);
		}
		$creator_info = $d_user->get_full_name(array($info['creator_role_id']));
		$info['creator_info'] = $creator_info[$info['creator_role_id']];

		// 如果审批结束【通过或拒绝】，查询最后一个审批人信息
		if ($info['is_checked'] == 1 || $info['is_checked'] == 2) {
			$check_role_id = M('ContractCheck')->where('contract_id = %d', $info['contract_id'])->order('check_time desc')->limit(1)->getField('role_id');
			$examine_role_info = $d_user->get_full_name(array($check_role_id));
			$info['examine_role_info'] = $examine_role_info[$check_role_id];
		}

		// $info['business'] = M('Business')->where('business_id = %d',$info['business_id'])->find();
		$info['receivables_money'] = M('Receivingorder')->where('is_deleted <> 1 and status=1 and contract_id = %d',$info['contract_id'])->sum('money');
		$info['receivables_money'] = $info['receivables_money']?$info['receivables_money']:0;
		$info['balance'] = $info['price'] - $info['receivables_money'];
		
		//应收款
		$m_receivables = M('Receivables');
		$m_receivingorder = M('Receivingorder');
		//查询应收款列表
		$receivables_list = $m_receivables->where('is_deleted <> 1 and contract_id = %d',$info['contract_id'])->select();
		$price_all = 0.00; //应收总金额
		$ys_price_all = 0.00; //已收金额
		$d_user = D('User');
		foreach($receivables_list as $k=>$v){
			$price_all += $v['price'];
			$ys_price = 0.00; //单个应收款的已收金额
			//获取该应收款对应的收款单
			$receivingorder_list = $m_receivingorder->where('is_deleted <> 1 and receivables_id = %d',$v['receivables_id'])->select();
			foreach($receivingorder_list as $ki=>$vi){
				if($vi['status'] == 1){
					$ys_price += $vi['money'];
					$ys_price_all += $vi['money'];
				}
				$receivingorder_list[$ki]['owner_name'] = $d_user->get_full_name((int) $vi['owner_role_id']);
			}
			$receivables_list[$k]['ys_price'] = $ys_price;
			$receivables_list[$k]['receivingorder'] = $receivingorder_list;
		}
		$un_price_all = $price_all - $ys_price_all;
		$this->price_all = $price_all;
		$this->un_price_all = $un_price_all;
		$this->receivables_list = $receivables_list;
		//文件
		$file_ids = M('rContractFile')->where('contract_id = %d', $contract_id)->getField('file_id', true);
		$info['file'] = M('file')->where('file_id in (%s)', implode(',', $file_ids))->select();
		$file_count = 0;
		$d_file = D('File');
		foreach ($info['file'] as $key=>$value) {
			$info['file'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
			$info['file'][$key]['size'] = ceil($value['size']/1024);
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
			$info['file'][$key]['pic'] = $pic;
			if ($value['oss'] == 1) {
				$info['file'][$key]['file_path'] = $d_file::FILE_URL . '/' . $value['file_path'];
			}
			$file_count++;
		}
		$info['file_count'] = $file_count;

		//产品列表
		$product_list = D('Contract')->getProductList($contract_id);
		$this->assign('sales_product', $product_list['product_list']);
		$this->assign('sales', $product_list['sales']);

		// 销售退货
		$this->return_product = D('Purchase')->getSalesReturnIn($product_list['sales']['sales_id']);

		//发票信息
		$invoice_list = M('Invoice')->where(array('contract_id'=>$contract_id))->select();
		$m_user = M('User');
		$invoice_sum = '0.00';
		foreach ($invoice_list as $k=>$v) {
			$invoice_list[$k]['create_name'] = $m_user->where(array('role_id'=>$v['create_role_id']))->getField('full_name');
			switch ($v['is_checked']) {
				case 1 : $check_name = '通过'; break;
				case 2 : $check_name = '驳回'; break;
				default : $check_name = '待审'; break;
			}
			$invoice_list[$k]['check_name'] = $check_name;
			if ($v['is_checked'] != 2) {
				$invoice_sum += $v['price'];
			}
			//发票类型
			switch ($v['billing_type']) {
				case 1 : $type_name = '增值税普通发票'; break;
				case 2 : $type_name = '增值税专用发票'; break;
				case 3 : $type_name = '收据'; break;
			}
			$invoice_list[$k]['type_name'] = $type_name ? $type_name : '';
		}
		$this->invoice_sum = $invoice_sum ? round($invoice_sum,2) : '0.00';
		$this->invoice_list = $invoice_list;

		//沟通日志
		$log_ids = M('rContractLog')->where('contract_id = %d', $contract_id)->getField('log_id', true);
		$log_list = M('Log')->where('log_id in (%s)', implode(',', $log_ids))->order('log_id desc')->select();
		$m_user = M('User');
		$m_log_status = M('LogStatus');
		foreach ($log_list as $key=>$value) {
			$owner_list = array();
			$owner_list = $d_user->get_full_name(array($value['role_id']));
			$log_list[$key]['owner'] = $owner_list[$value['role_id']];
			$log_list[$key]['log_type'] = 'rContractLog';
			$status_name = $m_log_status->where('id = %d',$value['status_id'])->getField('name');
			$log_list[$key]['status_name'] = $status_name ? $status_name : '';
		}
		$info['log'] = $log_list;

		//签约历史
		// $history_list = contract_history($contract_id,'',1);
		$renew_parent_id = $info['renew_parent_id'] ? $info['renew_parent_id'] : $contract_id;
		$where_renew = array();
		$where_renew['contract.renew_parent_id'] = $renew_parent_id;
		$where_renew['contract.contract_id'] = $renew_parent_id;
		$where_renew['_logic'] = 'or';
		$map_renew['_complex'] = $where_renew;
		$map_renew['contract.contract_id']  = array('neq',$contract_id);

		$history_list = $d_contract->where($map_renew)->select();
		$this->history_list = $history_list;

		//审批流程
		if ($info['examine_type_id']) {
			$contract_examine = $info['examine_type_id'];
		} else {
			//contract_examine 1为自定义审批流
			$contract_examine = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
		}
		$d_user = D('User');
		if ($contract_examine == 1) {
			$m_contract_check = M('ContractCheck');
			//自定义流程
			$check_list = M('ContractExamine')->order('order_id')->select();
			foreach ($check_list as $k=>$v) {
				$role_ids = explode(',',$v['role_id']);
				$role_list = array();
				$role_list = $d_user->get_full_name($role_ids);
				if ($info['order_id'] >= $v['order_id']) {
					$check_role_arr = array();
					//审批意见
					$check_role_arr = $m_contract_check->where(array('contract_id'=>$contract_id,'order_id'=>$v['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);
					$is_checked_name = '';
					if ($info['is_checked'] == 1 || $info['is_checked'] == 2) {
						$is_checked_name = '';
					} else {
						if ($info['order_id'] == $v['order_id']) {
							$is_checked_name = '待审';
						}
					}
					foreach ($role_list as $key=>$val) {
						if ($check_role_arr && in_array($val['role_id'],$check_role_arr)) {
							$role_list[$key]['is_checked_name'] = '同意';
						} else {
							$role_list[$key]['is_checked_name'] = $is_checked_name;
						}
					}
				}
				$check_list[$k]['role_list'] = $role_list;
				if (count($role_ids) > 1) {
					if ($v['relation'] == 1) {
						$check_list[$k]['relation_name'] = '并';
					} elseif ($v['relation'] == 2) {
						$check_list[$k]['relation_name'] = '或';
					}
				}
			}
			$this->check_list = $check_list;
		}

		//是否有审批（撤销）权限
		$m_contract_examine = M('ContractExamine');
		$contract_examine_roles = array();
		if ($info['is_checked'] == 3) {
			//审批中
			$contract_examine_roles = array_filter(explode(',', $info['examine_role_id']));
		} elseif ($info['is_checked'] != 1 && $info['is_checked'] != 2) {
			//未审批
			if ($contract_examine == 1) {
				//自定义审批
				$contract_examine_roles = $m_contract_examine->where(array('order_id'=>$info['order_id']))->getField('role_id');
				$contract_examine_roles = array_filter(explode(',', $contract_examine_roles));
			} else {
				//有审批权限的
				if (checkPerByAction('contract','check')) {
					$check_per = 1;
				}
			}
		}
		if (in_array(session('role_id'), $contract_examine_roles)) {
			$check_per = 1;
		}
		if (session('?admin')) {
        	$re_check_per = 1;
		}
	
		$this->re_check_per = $re_check_per ? : 0;
		$this->check_per = $check_per;
		$this->contract_examine = $contract_examine;

		//获取员工审核记录
		$is_receivables = $m_user->where('role_id = %d',session('role_id'))->getField('is_receivables');
		$this->is_receivables = $is_receivables;
		$this->assign('product',$product);
		$this->assign('info',$info);

		// 操作记录
		$action_record = actionRecord($contract_id, 'contract'); 
		$this->assign('group_list', $action_record);

		//自定义字段
		$this->field_list = M('Fields')->where(array('model'=>'contract','field'=>array('not in',array('contract_name','due_time'))))->order('order_id')->select();
		$this->contract_id = $contract_id;
		$this->alert = parseAlert();
		$this->display();

	}

	/**
	*合同续签、忽略
	*
	**/
	public function elide_edit(){
		$contract_ids = is_array($_REQUEST['contract_ids']) ? $_REQUEST['contract_ids'] : array($_REQUEST['contract_ids']);
		if ('' == $contract_ids) {
			$this->ajaxReturn('',L('NOT CHOOSE ANY'),0);
		} else {
			$m_contract = M('Contract');
			$where['contract_id'] = array('in',$contract_ids); 
			$result = $m_contract ->where($where)->setField('contract_status',2);
			if($result){
				$this->ajaxReturn('','操作成功！',1);
			}else{
				$this->ajaxReturn('','操作失败！',0);
			}
			
		}
	}

	/**
	*删除合同
	*
	**/
	public function delete(){
		$contract_ids = is_array($_REQUEST['contract_id']) ? $_REQUEST['contract_id'] : array($_REQUEST['contract_id']);
		if ('' == $contract_ids) {
			$this->ajaxReturn('',L('NOT CHOOSE ANY'),0);
		} else {
			$m_contract = M('Contract');
			$m_receivables = M('Receivables');
			$m_payables = M('Payables');
			$m_r_contract_product = M('rContractProduct');
			$m_r_contract_file = M('rContractFile');
			//权限判断
			foreach ($contract_ids as $v) {
				$contracts = $m_contract->where('contract_id = %d', $v)->find();
				if (!in_array($contracts['owner_role_id'], $this->_permissionRes)){
					$this->ajaxReturn('',L('DO NOT HAVE PRIVILEGES'),0);
				}
				if($contracts['is_checked'] == 1){
					$this->ajaxReturn('','已审核的合同不能删除！',0);
				}
			}
			//如果合同下有产品，财务和文件信息，提示先删除产品，财务和文件数据。
			// $data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
			foreach($contract_ids as $k=>$v){
				$contract = $m_contract->where('contract_id = %d',$v)->find();
				$contract_product = $m_r_contract_product->where('contract_id = %d',$v)->select();//合同关联的产品记录
				$contract_file = $m_r_contract_file->where('contract_id = %d',$v)->select();//合同关联的文件
				$contract_receivables = $m_receivables->where('is_deleted <> 1 and contract_id = %d',$v)->select();//合同关联的应收款
				$contract_payables = $m_payables->where('is_deleted <> 1 and contract_id = %d',$v)->select();//合同关联的应付款
				
				if(empty($contract_product) && empty($contract_file) && empty($contract_receivables) && empty($contract_payables)){
					if (!$m_contract->where('contract_id = %d', $v)->delete()) {
						$this->ajaxReturn('',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),0);
					} else {
						//附表删除
						M('ContractData')->where(array('contract_id'=>$v))->delete();
						//关联日程
						$event_res = M('Event')->where(array('module'=>'contract','module_id'=>$v))->delete();
					}
				}else{
					if(!empty($contract_product)){
						$this->ajaxReturn('',L('DELETE_FAILED_PLEASE_DELETE_UNDER_THE_CONTRACT_OF_PRODUCT_INFORMATION',array($contract['number'])),0);
					}elseif(!empty($contract_file)){
						$this->ajaxReturn('',L('DELETE_FAILED_PLEASE_DELETE_UNDER_THE_CONTRACT_OF_FILE_INFORMATION',array($contract['number'])),0);
					}elseif(!empty($contract_receivables)){
						$this->ajaxReturn('',L('DELETE_FAILED_PLEASE_DELETE_RECEIVABLES_UNDER_THE_FINANCIAL_INFORMATION_IN_THE_CONTRACT',array($contract['number'])),0);
					}else{
						$this->ajaxReturn('',L('DELETE_FAILED_PLEASE_DELETE_RECEIVABLES_UNDER_THE_FINANCIAL_INFORMATION_IN_THE_CONTRACT',array($contract['number'])),0);
					}
				}
			}
			$this->ajaxReturn('',L('DELETED SUCCESSFULLY'),1);
		}
	}
	
	/**
	*合同列表页面（默认）
	*
	**/
	public function index(){
		global $m_user, $examine_role_list, $m_contract_examine;
		//更新最后阅读时间
		$m_user = M('User');
		$d_contract = D('ContractView');
		$by = isset($_GET['by']) ? trim($_GET['by']) : 'all';
		$this->by = $by;
		$where = array();
		$below_ids = getPerByAction(MODULE_NAME,ACTION_NAME,true);
		// if(empty($_GET['owner_role_id'])){
		// 	$where['contract.owner_role_id'] = array('in', $this->_permissionRes);
		// }
		$order = 'contract.update_time desc,contract.contract_id asc';
		if($_GET['desc_order']){
			$order = 'contract.'.trim($_GET['desc_order']).' desc,contract.contract_id asc';
		}elseif($_GET['asc_order']){
			$order = 'contract.'.trim($_GET['asc_order']).' asc,contract.contract_id asc';
		}
		switch ($by){
			case 'create':
				$where['creator_role_id'] = session('role_id');
				break;
			case 'sub' : 
				$where['contract.owner_role_id'] = array('in',implode(',', $below_ids)); 
				break;
			case 'subcreate' : 
				$where['creator_role_id'] = array('in',implode(',', $below_ids)); 
				break;
			case 'today' : 
				$where['due_time'] =  array('between',array(strtotime(date('Y-m-d')) -1 ,strtotime(date('Y-m-d')) + 86400)); 
				break;
			case 'week' : 
				$week = (date('w') == 0)?7:date('w');
				$where['due_time'] =  array('between',array(strtotime(date('Y-m-d')) - ($week-1) * 86400 -1 ,strtotime(date('Y-m-d')) + (8-$week) * 86400)); 
				break;
			case 'month' : 
				$next_year = date('Y')+1;
				$next_month = date('m')+1;
				$month_time = date('m') ==12 ? strtotime($next_year.'-01-01') : strtotime(date('Y').'-'.$next_month.'-01');
				$where['due_time'] = array('between',array(strtotime(date('Y-m-01')) -1 ,$month_time));
				break;
			case 'add' : 
				$order = 'contract.create_time desc,contract.contract_id asc'; 
				break;
			case 'deleted' : 
				$where['is_deleted'] = 1; 
				break;
			case 'update' : 
				$order = 'contract.update_time desc,contract.contract_id asc'; 
				break;
			case 'me' :
				$where['contract.owner_role_id'] = session('role_id');
				break;
			case 'check' :
				$where['contract.is_checked'] = 1;
				break;
			case 'no_check' :
				$where['contract.is_checked'] = 0;
				break;
			case 'refuse' :
				$where['contract.is_checked'] = 2;
				break;
			case 'dqcontact' :
				$days = C('defaultinfo.contract_alert_time') ? intval(C('defaultinfo.contract_alert_time')) : 30;
				$temp_time = time()+$days*86400;
				$where['contract.is_checked'] = 1;
				$where['contract.contract_status'] = 0;
				$where['contract.owner_role_id'] = session('role_id');
				$where['end_date'] = array('elt',$temp_time);
				break;
			default: $where['contract.owner_role_id'] = array('in',getPerByAction(MODULE_NAME,ACTION_NAME));break;
		}

		if (!isset($where['is_deleted'])) {
			$where['is_deleted'] = 0;
		}
		//普通搜索
		if ($_REQUEST["field"]) {
			if (trim($_REQUEST['field']) == "all") {
				$field = is_numeric(trim($_REQUEST['search'])) ? 'number|price|description' : 'number|description';
			} else {
				$field = trim($_REQUEST['field']);
			}
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if($field == 'number'){
				//获取客户ID
				$cus_where['name'] = array('like','%'.$search.'%');
				$customer_ids = M('Customer')->where($cus_where)->getField('customer_id',true);
				$customer_str = implode(',',$customer_ids);
				//获取商机ID
				$b_where['name'] = array('like','%'.$search.'%');
				$business_ids = M('business')->where($b_where)->getField('business_id',true);
				$business_str = implode(',',$business_ids);
				if($customer_str && $business_str){
					$where['_string'] = 'contract.contract_name like "%'.$search.'%" or contract.number like "%'.$search.'%" or contract.customer_id in ('.$customer_str.') or contract.business_id in ('.$business_str.')';
				}elseif($customer_str){
					$where['_string'] = 'contract.contract_name like "%'.$search.'%" or contract.number like "%'.$search.'%" or contract.customer_id in ('.$customer_str.')';
				}elseif($business_str){
					$where['_string'] = 'contract.contract_name like "%'.$search.'%" or contract.number like "%'.$search.'%" or contract.business_id in ('.$business_str.')';
				}else{
					$where['_string'] = 'contract.contract_name like "%'.$search.'%" or contract.number like "%'.$search.'%"';
				}
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
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$search);
			//过滤不在权限范围内的role_id
			if(trim($_REQUEST['field']) == 'contract[owner_role_id]'){
				if(!in_array(trim($search),$below_ids)){
					$where['contract.owner_role_id'] = array('in',$below_ids);
				}
			}
		}
		//多选类型字段
		$check_field_arr = M('Fields')->where(array('model'=>'contract','form_type'=>'box','setting'=>array('like','%'."'type'=>'checkbox'".'%')))->getField('field',true);
		//高级搜索
		if(!$_GET['field']){		
			$no_field_array = array('act','content','p','condition','listrows','search','by','contract_checked');
			foreach($_GET as $k=>$v){		
                if(!in_array($k,$no_field_array)){
                	if(is_array($v)){  		
                		if ($v['state']){
							$address_where[] = '%'.$v['state'].'%';

							if($v['city']){
								$address_where[] = '%'.$v['city'].'%';

								if($v['area']){
									$address_where[] = '%'.$v['area'].'%';
								}
							}
							if($v['search']) $address_where[] = '%'.$v['search'].'%';

							if($v['condition'] == 'not_contain'){
								$where[$k] = array('notlike', $address_where, 'OR');
							}else{
								$where[$k] = array('like', $address_where, 'AND');
							}
						} elseif (($v['start'] != '' || $v['end'] != '')) {
							if($k == 'create_time'){
								$k = 'contract.create_time';
							}elseif($k == 'update_time'){
								$k = 'contract.update_time';
							}
							//时间段查询
							if ($v['start'] && $v['end']) {
								$where[$k] = array('between',array(strtotime($v['start']),strtotime($v['end'])+86399));
							} elseif ($v['start']) {
								$where[$k] = array('egt',strtotime($v['start']));
							} else {
								$where[$k] = array('elt',strtotime($v['end'])+86399);
							}
						} elseif ($k == 'customer_name') {
							if(!empty($v['value'])){
								$c_where['name'] = array('like','%'.$v['value'].'%');
								$customer_ids = M('customer')->where($c_where)->getField('customer_id',true); 
								if($customer_ids){
									$where['customer_id'] = array('in',$customer_ids);
								}else{
									$where['customer_id'] = -1;
								}
							}
						}elseif($k =='code'){
							if(!empty($v['value'])){
								$b_where['code'] = array('like','%'.$v['value'].'%');
								$business_ids = M('business')->where($b_where)->getField('business_id',true); 
								if($business_ids){
									$where['business_id'] = array('in',$business_ids);
								}else{
									$where['business_id'] = -1;
								}
							}
						}elseif($k == 'owner_role_id' || $k == 'creator_role_id'){
							if(!empty($v)){
								$where['contract.'.$k] = field($v['value'], $v['condition']);
							}
						}elseif(($v['value']) != '') {
							if (in_array($k,$check_field_arr)) {
								$where[$k] = field($v['value'],'contains');
							} else {
								$where[$k] = field($v['value'],$v['condition']);
							}
						} elseif (in_array($v['condition'], array('is_empty', 'is_not_empty'))) {
							$where[$k] = field($v['value'],$v['condition']);
						}
                	}else{
						if(!empty($v)){
							$where[$k] = field($v);
						}
				    }
                }
                if($k == 'contract.create_time'){
					$k = 'create_time';
				}elseif($k == 'contract.update_time'){
					$k = 'update_time';
				}
				if(is_array($v)){
					foreach ($v as $key => $value) {
						$params[] = $k.'['.$key.']='.$value;
					}
				}else{
					$params[] = $k.'='.$v;
				}
            }

            //过滤不在权限范围内的role_id
			/*if(isset($where['contract.owner_role_id'])){
				if(!empty($where['contract.owner_role_id']) && !in_array(intval($where['contract.owner_role_id']),$this->_permissionRes)){
					$where['contract.owner_role_id'] = array('in',implode(',', $this->_permissionRes));
				}
			}*/

			// 需要权限范围筛选的role_id，需要重新整理where条件
			$d_search = D('Search');
			$where = $d_search->roleWhere('contract.owner_role_id', $where, $this->_permissionRes);
		}
		//待审核的合同(未审核、审核中)
		if ($_GET['contract_checked']) {
			$where['is_checked'] = array('in',array('0','3'));
		}

		//高级搜索字段
		$fields_list_data = M('Fields')->where(array('model'=>array('in',array('','contract')),'is_main'=>1))->field('field,form_type')->select();
		foreach($fields_list_data as $k=>$v){
			$fields_data_list[$v['field']] = $v['form_type'];
		}
		$fields_search = array();
		foreach($params as $k=>$v){
			if(strpos($v,'[condition]=') || strpos($v,'[value]=') || strpos($v,'[state]=') || strpos($v,'[city]=') || strpos($v,'[area]=') || strpos($v,'[start]=') || strpos($v,'[end]=')){
				$field = explode('[',$v);

				if(strpos($field[0],'.')){
					$ex_field = explode('.',$field[0]);
					$field[0] = $ex_field[1];
				}

				if (strpos($v,'[condition]=')) {
					$condition = explode('=',$v);
					$fields_search[$field[0]]['field'] = $field[0];
					$fields_search[$field[0]]['condition'] = $condition[1];
				} elseif (strpos($v,'[state]=')) {
					$state = explode('=',$field[1]);
					$fields_search[$field[0]]['state'] = $state[1];
				} elseif (strpos($v,'[city]=')) {
					$city = explode('=',$field[1]);
					$fields_search[$field[0]]['city'] = $city[1];
				} elseif (strpos($v,'[area]=')) {
					$area = explode('=',$field[1]);
					$fields_search[$field[0]]['area'] = $area[1];
				} elseif (strpos($v,'[start]=')) {
					$start = explode('=',$field[1]);
					$fields_search[$field[0]]['field'] = $field[0];
					$fields_search[$field[0]]['start'] = $start[1];
				} elseif (strpos($v,'[end]=')) {
					$end = explode('=',$field[1]);
					$fields_search[$field[0]]['end'] = $end[1];
				} else {
					$value = explode('=',$v);
					if($fields_search[$field[0]]['field']){
						$fields_search[$field[0]]['value'] = $value[1];
					}else{
						$fields_search[$field[0]]['field'] = $field[0];
						$fields_search[$field[0]]['condition'] = 'eq';
						$fields_search[$field[0]]['value'] = $value[1];
					}
				}
				$fields_search[$field[0]]['form_type'] = $fields_data_list[$field[0]];
			}
		}

		//自定义字段
 		$field_array = getIndexFields('contract');
 		$name_field_array = array();
		foreach($field_array as $k=>$v){
			if($v['field'] != 'name'){
				$name_field_array[] = $v;
			}
		}
		$this->field_array = $name_field_array;

		$count = $d_contract->where($where)->count();

		// 导出
		if (trim($_GET['act']) == 'export') {
			if(!checkPerByAction('contract','export')){
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
			if ($_GET['ids']) {
				$where = array('contract_id' => array('IN', $_GET['ids']));
			}
			$table_head = array('field' => array());
			foreach ($name_field_array as $field) {
				if ($field['field'] == 'contract_name') {
					$table_head['field']['_export_number'] = '合同编号';
					$table_head['field']['contract_name'] = '合同名称';
				} elseif ($field['field'] == 'customer_id') {
					$table_head['field']['customer_name'] = '客户';
				} elseif ($field['field'] == 'business_id') {
					$table_head['field']['business_name'] = $field['name'];
				} else {
					$table_head['field'][$field['field']] = $field['name'];
				}
			}
			$table_head['field']['owner_name'] = '签约人';
			$table_head['field']['check_status.name'] = '状态';
			$temp = $this;
			csvExport('pdcrm-合同导出', $table_head, $count, function($page) use ($d_contract, $order, $where, $temp) {
				$list = $d_contract->where($where)->page($page)->order($order)->select();
				return $temp->_handle($list);
			});
		}


		if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			cookie('listrows', $listrows, 3600 * 24 * 30);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = cookie('listrows') ? cookie('listrows') : 15;
			$params[] = "listrows=".$listrows;
		}
		$p = intval($_GET['p'])?intval($_GET['p']):1;
		$p_num = ceil($count/$listrows);
		if($p_num<$p){
			$p = $p_num;
		}

		$list = $d_contract->where($where)->page($p.','.$listrows)->order($order)->select();
		$list = $this->_handle($list);

		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		if (!empty($_GET['by'])) {
			$params[] = "by=".trim($_GET['by']);
		}
		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}
		$this->parameter = implode('&', $params);
		//by_parameter(特殊处理)
		$this->by_parameter = str_replace('by='.$_GET['by'], '', implode('&', $params));
		
		$m_user = M('User');
		//contract_examine 1为自定义审批流
		$option = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
		$m_contract_examine = M('ContractExamine');
		
		//是否审批权限(自定义流程和自选)
		
		if ($option == 1) {
			$examine_role_ids = $m_contract_examine->getField('role_id',true);
		} else {
			$check_position_ids = M('Permission')->where(array('url'=>'contract/check'))->getField('position_id',true);
			$examine_role_ids = M('Role')->where(array('position_id'=>array('in',$check_position_ids)))->getField('role_id',true);
		}
		$is_checkper = 0;
		if (session('?admin') || in_array(session('role_id'),$examine_role_ids)) {
			$is_checkper = 1;
		}
		$this->is_checkper = $is_checkper;

		$is_receivables = $m_user->where('role_id=%d',session('role_id'))->getField('is_receivables');
		$this->is_receivables = $is_receivables;
		$this->listrows = $listrows;
		$this->assign('count',$count);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());

		// $this->field_list = getMainFields('contract');
		$field_list = M('Fields')->where(array('model'=>'contract','field'=>array('not in',array('business_id','customer_id'),'is_main'=>1)))->order('order_id asc')->select();
		$this->field_list = $field_list;
		//高级搜索
		$this->fields_search = $fields_search;
		$this->assign('list',$list);
		$this->alert = parseAlert();
		$this->display();
	}
	

	/**
	 * 数据处理
	 */
	public function _handle($list = array())
	{
		global $m_user, $examine_role_list, $m_contract_examine;
		$m_business = M('Business');
		$m_remind = M('Remind');
		$d_user = D('User');

		foreach ($list as $key=>$value) {
			// dd($value);
			$list[$key]['owner_name'] = $m_user->where('role_id =%d',$value['owner_role_id'])->getField('full_name');			
			$list[$key]['business_name'] = $m_business ->where('business_id =%d',$value['business_id'])->getField('name');
			//提醒
			$remind_info = array();
			$remind_info = $m_remind->where(array('module'=>'contract','module_id'=>$value['contract_id'],'create_role_id'=>session('role_id'),'is_remind'=>array('neq',1)))->order('remind_id desc')->find();
			$list[$key]['remind_time'] = !empty($remind_info) ? $remind_info['remind_time'] : '';
			//是否可以撤销审批
			$check_per = 0;
			if (session('?admin') && $value['is_checked']) {
				$check_per = 1;
			}
			$list[$key]['check_per'] = $check_per;
			// 审批人
			$examine_role_list = array();
			$examine_role_ids = '';
			if ($value['examine_role_id']) {
				$examine_role_ids = $value['examine_role_id'];
			} else {
				if ($option == 1 && $value['is_checked'] == 0) {
					$examine_role_ids = $m_contract_examine->order('order_id asc')->getField('role_id');
				}
			}
			switch ($value['is_checked']) {
				case 2:
					$list[$key]['check_status'] = array('color' => '#F5715F', 'name' => '拒绝');
					break;
				case 1:
					$list[$key]['check_status'] = array('color' => '#7CCA4E', 'name' => '通过');
					break;
				case 3:
					$list[$key]['check_status'] = array('color' => '#FF6600', 'name' => '审批中');
					break;
				default:
					$list[$key]['check_status'] = array('color' => '#F5CA00', 'name' => '待审');
					break;
			}

			if ($_GET['act'] == 'export') {
				$list[$key]['_export_number'] = $list[$key]['number'];
				if ($value['contract_status'] == 1) {
					$list[$key]['_export_number'] =  $list[$key]['_export_number'] . '(已续约)';
				}
				if ($value['is_checked'] == 1 && $value['renew_contract_id'] > 0) {
					$list[$key]['_export_number'] =  $list[$key]['_export_number'] . '(续约合同)';
				}
			}

			foreach ($this->field_array as $val) {
				if ($val['field'] == 'price') {
					$list[$key]['price'] = number_format($list[$key]['price'], 2);
				}
				if ($val['form_type'] == 'datetime') {
					$list[$key][$val['field']] = $list[$key][$val['field']] ? date('Y-m-d', $list[$key][$val['field']]) : '###';
				}
			}


			$examine_role_list = $d_user->get_full_name(array_filter(explode(',', $examine_role_ids)));
			$list[$key]['examine_role_list'] = $examine_role_list;
		}
		return $list;
	}


	/**
	*合同Ajax弹出
	*
	**/
	public function changeContent(){
		if($this->isAjax()){
			$contract = D('ContractView');
			$where = array();
			
			$where['contract.is_deleted'] = 0;
			$where['contract.owner_role_id'] = array('in',implode(',', getSubRoleId())); 
			
			if ($_REQUEST["field"]) {
				if (trim($_REQUEST['field']) == "all") {
					$field = is_numeric(trim($_REQUEST['search'])) ? 'number|price|contract.description' : 'number|contract.description';
				} else {
					$field = trim($_REQUEST['field']);
				}
				$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
				$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);

				if	('create_time' == $field || 'update_time' == $field || 'due_date' == $field) {
					$search = is_numeric($search)?$search:strtotime($search);
				}
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
			
			$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
			$list = $contract->where($where)->page($p.',10')->order('contract.create_time desc')->select();
			$count = $contract->where($where)->count();
			foreach ($list as $key=>$value) {
				$list[$key]['owner'] = getUserByRoleId($value['owner_role_id']);
				$list[$key]['creator'] = getUserByRoleId($value['creator_role_id']);
				$list[$key]['deletor'] = getUserByRoleId($value['delete_role_id']);
			}
			$data['list'] = $list;
			$data['p'] = $p;
			$data['count'] = $count;
			$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
			$this->ajaxReturn($data,"",1);
		}
	}
	
	/**
	*合同Ajax弹出页面
	*
	**/
	public function listDialog(){
		//权限控制（是否有应收款添加、编辑权限）
		if(!checkPerByAction('finance','add_receivables') && !checkPerByAction('finance','edit_receivables') && !checkPerByAction('invoice','add') && !checkPerByAction('invoice','edit')){
			echo '<div class="alert alert-error">您没有此权利！</div>';die();
		}
		$d_contract = D('ContractView');
		$m_receivables = M('Receivables');
		$type = $_GET['type'] ? trim($_GET['type']) : '';
		$params[] = 'type='.trim($_GET['type']);
		if ($type == 'receivables') {
			//应收款下过滤已创建应收款的合同
			$contract_ids = $m_receivables->getField('contract_id',true);
		}
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$where = array();
		$where['is_deleted'] = 0;
		if($contract_ids){
			$where['contract_id'] = array('not in',$contract_ids);
		} 
		$where['is_checked'] = 1;
		$where['contract.owner_role_id'] = array('in',getPerByAction(MODULE_NAME,'index'));
		if ($_GET['customer_id']) {
			if (is_int($_GET['customer_id'])) {
				$where['customer_id'] = intval($_GET['customer_id']);
				$params[] = 'customer_id='.intval($_GET['customer_id']);
			} else {
				$_customer_id = $_GET['customer_id'];
				$_customer_id = str_replace('_', '', $_customer_id);
				if ($_customer_id) {
					$where['customer_id'] = intval($_customer_id);
					$params[] = 'customer_id='.intval($_customer_id);
					$this->_customer_name = M('customer')->where('customer_id=%d', $_customer_id)->getField('name');
					$_REQUEST['field'] = 'business.customer_id';
					$_REQUEST['condition'] = 'is';
					$_REQUEST['search'] = $this->_customer_name;
				}
			}
		}
		if ($_REQUEST["field"]) {
			if (trim($_REQUEST['field']) == "all") {
				$field = is_numeric(trim($_REQUEST['search'])) ? 'number|price|contract.description' : 'number|contract.description';
			} else {
				$field = trim($_REQUEST['field']);
			}
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);

			if ('create_time' == $field || 'update_time' == $field || 'due_date' == $field) {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			if($field =="business.customer_id"){
				$c_where['name'] = array('like','%'.$search.'%');
				$customer_ids = M('Customer')->where($c_where)->getField('customer_id',true);
				$where['customer_id'] = array('in',$customer_ids);
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
			$params = array('field='.$field, 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		import("@.ORG.DialogListPage");

		$list = $d_contract->where($where)->page($p.',10')->order('update_time desc')->select();
		$count = $d_contract->where($where)->count();
		$this->search_field = $_REQUEST;//搜索信息
		$Page = new Page($count,10);
		$Page->parameter = implode('&', $params);
		$this->assign('page',$Page->show());
		$this->assign('contractList',$list);
		$this->display();
	}
	
	/**
	*获取合同列表(pdcrm.js使用)
	*
	**/
	public function getcontractlist(){
		$list = D('ContractView')->where(array('contract.is_deleted' => 0))->select();
		$this->ajaxReturn($list, '', 1);
	}

	//审核
	public function check(){
		$m_contract = M('Contract');
		$contract_id = $_REQUEST['contract_id'] ? intval($_REQUEST['contract_id']) : '';
		if (!$contract_id) {
			if ($this->isGet()) {
				echo '<div class="alert alert-error">参数错误！</div>';die();
			} else {
				alert('error','参数错误！', U('contract/index'));
			}
		}
		$contract = $m_contract->where('contract_id = %d', $contract_id)->find();
		if (!$contract) {
			if ($this->isGet()) {
				echo '<div class="alert alert-error">数据不存在或已删除！</div>';die();
			} else {
				alert('error', '数据不存在或已删除！',$_SERVER['HTTP_REFERER']);
			}
		}
		if ($contract['examine_type_id']) {
			$option = $contract['examine_type_id'];
		} else {
			//contract_examine 1为自定义审批流
			$option = M('Config')->where(array('name'=>'contract_examine'))->getField('value');
		}
		if (in_array($contract['is_checked'],array('1','2'))) {
			if ($this->isGet()) {
				echo '<div class="alert alert-error">该合同已审核结束，请勿重复操作！</div>';die();
			} else {
				alert('error','该合同已审核结束，请勿重复操作！', U('contract/index'));
			}
		}
		$m_contract_examine = M('ContractExamine');
		$m_contract_check = M('ContractCheck');
		//判断审批权限
		if ($option == 1) {
			$examine_step_info = $m_contract_examine->where(array('order_id'=>$contract['order_id']))->find();
			//当前步骤已审批role_id
			$is_examine_role_ids = $m_contract_check->where(array('contract_id'=>$contract_id,'order_id'=>$contract['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);

			if (in_array(session('role_id'),$is_examine_role_ids)) {
				if ($this->isPost()) {
					alert('error','您已审核，请勿重复操作！',$_SERVER['HTTP_REFERER']);
				} else {
					echo '<div class="alert alert-error">您已审核，请勿重复操作！</div>';die;
				}
			}
			//权限判断（并、或关系的，必须是规定审批人，超级管理员也无此权限）
			if ($examine_step_info['relation'] > 0) {
				if (!in_array(session('role_id'),explode(',',$examine_step_info['role_id']))) {
					if ($this->isPost()) {
						alert('error','您没有审核权限或审批已通过！',$_SERVER['HTTP_REFERER']);
					} else {
						echo '<div class="alert alert-error">您没有审核权限或审批已通过！</div>';die;
					}
				}
			} else {
				if (!session('?admin') && !in_array(session('role_id'),explode(',',$examine_step_info['role_id']))) {
					if ($this->isPost()) {
						alert('error','您没有审核权限！',$_SERVER['HTTP_REFERER']);
					} else {
						echo '<div class="alert alert-error">您没有审核权限！</div>';die;
					}
				}
			}
		} else {
			//自选流程
			if (!checkPerByAction('contract','check')) {
				if ($this->isGet()) {
					echo '<div class="alert alert-error">您没有此权限！</div>';die();
				} else {
					alert('error','您没有此权限！', U('contract/index'));
				}
			}
		}
		$m_user = M('User');

		if ($this->isPost()) {
			$is_agree = intval($_POST['is_agree']);
			$is_receivables = intval($_POST['is_receivables']);
			$description = trim($_POST['description']);
			$m_r_contract_sales = M('rContractSales');
			$m_sales = M('Sales');
			//默认（是否生成应收款）
			M('User')->where('role_id =%d',session('role_id'))->setField('is_receivables',$is_receivables);
			$r_contract_sales_info = $m_r_contract_sales->where(array('contract_id'=>$contract_id,'sales_type'=>array('neq',1)))->find();
			$sales_id = $m_r_contract_sales->where('contract_id = %d && sales_type = 0', $contract_id)->getField('sales_id');
			$sales_status = $m_sales->where('sales_id = %d',$sales_id)->getField('status');
			if($contract['is_checked'] != 1){
				if($sales_status == 97 || $sales_status == 99 || $r_contract_sales_info['sales_type'] == 2 || empty($r_contract_sales_info)){
					$data = array();
					$data['check_des'] = $description;
					$data['update_time'] = time();
					$data['check_time'] = time();

					//审批流程
					$is_end = 0; //是否结束审批（发送站内信判断）

					if ($option == 1) {
						//当前单个审批流程是否结束
						$examine_step_info = $m_contract_examine->where(array('order_id'=>$contract['order_id']))->find();
						//已审批role_id
						$is_check_role = $m_contract_check->where(array('contract_id'=>$contract['contract_id'],'order_id'=>$contract['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);
						$is_check_role[] = session('role_id');
						$examine_is_end = 0;
						if ($examine_step_info['relation'] == 1 ) {
							$relation_name = '并';
							//并
							if ($is_check_role && !array_diff(array_filter(explode(',',$examine_step_info['role_id'])),$is_check_role)) {
								$examine_is_end = 1;
							}
						} elseif ($examine_step_info['relation'] == 2) {
							$relation_name = '或';
							//或
							// if (array_intersect(session('role_id'),$examine_step_info)) {
								$examine_is_end = 1;
							// }
						} else {
							$examine_is_end = 1;
						}
						if ($examine_is_end == 1) {
							//当前流程结束，转下一审批流程
							$next_order_id = $contract['order_id']+1; //下一审批流程排序ID
							//获取下一审批人
		                    $next_examine_step_info = $m_contract_examine->where(array('order_id'=>$next_order_id))->find();
		                    if ($next_examine_step_info['relation'] == 1) {
		                        $relation_name = '并';
		                    } elseif ($next_examine_step_info['relation'] == 2) {
		                        $relation_name = '或';
		                    }
							$next_role_id = $next_examine_step_info['role_id'];
						} else {
							//当前流程，剩余审批人
							$next_order_id = $contract['order_id'];
							$next_examine_step_info = $m_contract_examine->where(array('order_id'=>$next_order_id))->find();
		                    if ($next_examine_step_info['relation'] == 1) {
		                        $relation_name = '并';
		                    } elseif ($next_examine_step_info['relation'] == 2) {
		                        $relation_name = '或';
		                    }
							$next_role_id = $is_check_role ? array_merge(array_diff(array_filter(explode(',',$examine_step_info['role_id'])),$is_check_role)) : array_filter(explode(',',$examine_step_info['role_id']));
						}
						$data['examine_type_id'] = 1;
					} else {
						$next_role_id = trim($_POST['examine_role_id']);
						$next_order_id = 0;
					}
					if ($is_agree == 1) {
						if ($_POST['examine_status'] != 2 && $next_role_id == null && $option !== 1) {
							$this->ajaxReturn('','请选择下一审批人！',0);
						}
						if (is_array($next_role_id)) {
							$data['examine_role_id'] = ','.implode(',', $next_role_id).',';
						} else {
							$data['examine_role_id'] = $next_role_id;
						}
						
						if ($_POST['examine_status'] == 2) {
							$data['order_id'] = intval($next_order_id);
							$data['is_checked'] = 1;
							$is_end = 1;
						} elseif ($option == 1) {
							//自定义流程
							//查询审批流程排序最大值，如果order_id和最大值相等，则审批结束
							$max_order_id = $m_contract_examine->max('order_id');
							$order_id = intval($next_order_id);
							$new_order_id = $order_id-1;
							if ($new_order_id == $max_order_id) {
								$data['is_checked'] = 2;   //审批结束
								$is_end = 1;
							} else {
								$data['order_id'] = $order_id;
								$data['is_checked'] = 3;	//审批中
							}
						} else {
							$data['is_checked'] = 3;	//审批中
						}

					} elseif ($is_agree == 2) {
						//结束审批
						$is_end = 1;
						//如果是自定义流程,驳回至最初状态
						$step_role_id = '';					
						if($option == 1){
							$step_role_id = $m_contract_examine->order('order_id asc')->getField('role_id');
							//将自定义审批意见标记为无效
							$m_contract_check->where(array('contract_id'=>$contract['contract_id']))->setField('is_end',1);
						}
						$data['examine_role_id'] = $step_role_id ? : 0;
						$data['order_id'] = 0;
						$data['is_checked'] = 2;
						$data['examine_type_id'] = 0;
					} else {
						alert('error', '请求错误!', $_SERVER['HTTP_REFERER']);
					}
					$result = $m_contract->where('contract_id = %d', $contract_id)->save($data);

					//审批时给创建人发送站内信
					if (empty($is_end)) {
						if ($is_agree == 1) {
							$check_result = session('full_name').'<font style="color:green;">同意了</font>';
						} elseif ($is_agree == 1) {
							$check_result = session('full_name').'<font style="color:red;">驳回了</font>';
						}
						//发送站内信
						$url = U('contract/view','id='.$contract_id);
						sendMessage($contract['creator_role_id'],$check_result.'您创建的合同《<a href="'.$url.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>》',1);
					}

					//审核意见
					$c_data = array();
					$c_data['role_id'] = session('role_id');
					$c_data['is_checked'] = $is_agree;
					$c_data['contract_id'] = $contract_id;
					$c_data['content'] = $description;
					$c_data['check_time'] = time();
					$c_data['order_id'] = $contract['order_id'];
					M('ContractCheck')->add($c_data);
					if ($is_agree == 1 && $is_end == 1) {
						//客户状态改为已成交客户
						M('Customer')->where('customer_id =%d',$contract['customer_id'])->setField('customer_status','已成交客户');
						//商机状态改变为项目成功
						if($contract['business_id']){
							$m_business = M('Business');
							$status_type_id = $m_business->where(array('business_id'=>$contract['business_id']))->getField('status_type_id');
							$status_id = M('BusinessStatus')->where(array('type_id'=>$status_type_id,'is_end'=>3))->getField('status_id');
							$m_business->where(array('business_id'=>$contract['business_id']))->setField('status_id',$status_id);
						}
						if($contract['renew_contract_id'] > 0){
							$m_contract->where('contract_id =%d',$contract['renew_contract_id'])->setField('contract_status',1);
						}
					}
					$m_sales->where('sales_id =%d',$sales_id)->setField('is_checked',$data['is_checked']);
					if ($result) {
						actionLog($contract_id);
						//同时创建应收款
						if ($is_agree == 1 && $is_end == 1) {
							//判断是否生成应收款
							if ($is_receivables == 1) {
								$m_receivables = M('Receivables');
								$r_data['type'] = 1;
								//应收款编号
								$receivables_custom = M('Config')->where('name = "receivables_custom"')->getField('value');
								$receivables_max_id = $m_receivables->max('receivables_id');
								$receivables_max_id = $receivables_max_id+1;
								$receivables_max_code = str_pad($receivables_max_id,4,0,STR_PAD_LEFT);//填充字符串的左侧（将字符串填充为新的长度）
								$code = $receivables_custom.date('Ymd').'-'.$receivables_max_code;
								$r_data['name'] = $code;
								$r_data['prefixion'] = $receivables_custom;
								$r_data['price'] = $contract['price'] ? $contract['price'] : '0.00';
								$r_data['customer_id'] = $contract['customer_id'];
								$r_data['contract_id'] = $contract_id;
								$r_data['sales_id'] = $sales_id;
								$r_data['pay_time'] = $_POST['pay_time'] ? strtotime($_POST['pay_time']) : time();
								$r_data['creator_role_id'] = $contract['creator_role_id'];
								$r_data['owner_role_id'] = $contract['owner_role_id'];
								$r_data['create_time'] = time();
								$r_data['update_time'] = time();
								$r_data['status'] = 0;
								$m_receivables->add($r_data);
							} else {
								//发站内信给财务
								$receivables_userId = getRoleByPer(array('finance/add_receivables'));
								foreach($receivables_userId as $v){
									$c = U('contract/view','id='.$contract_id);
									sendMessage($v,'《<a href="'.$c.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>》<font style="color:green;">已通过审核，财务人员可添加应收款单据</font>！',1);
								}
							}
							//发送站内信
							$url = U('contract/view','id='.$contract_id);
							sendMessage($contract['creator_role_id'],'您创建的合同《<a href="'.$url.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>》<font style="color:green;">已通过审核</font>！',1);
						} elseif ($is_agree == 2 && $is_end == 1) {
							sendMessage($contract['creator_role_id'],'您创建的合同《<a href="'.$url.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>》<font style="color:red;">经审核已被拒绝！请及时更正！</font>！',1);
						}
						//发送站内信给下一审批人
						if ($is_agree == 1 && $is_end != 1) {
							//下一审批人
							if (is_array($next_role_id)) {
								$examine_role_ids = $next_role_id ? array_filter($next_role_id) : array();
							} elseif ($next_role_id) {
								$examine_role_ids = $next_role_id ? array_filter(explode(',',$next_role_id)) : array();
							} else {
								$examine_role_ids = array();
							}
							$url = U('contract/view','id='.$contract_id);
							$creator = getUserByRoleId($contract['owner_role_id']);
							$message_content = '您有一个合同<a href="'.$url.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>待审批！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.$examine_info['creator_role_id'].'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp';
							foreach ($examine_role_ids as $k=>$v) {
								sendMessage($v,$message_content,1);
							}
						}
						alert('success', L('CHECK_SUCCESS'), $_SERVER['HTTP_REFERER']);
					} else {
						alert('error', L('CHECK_FAILED'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('CHECK_FAILED'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('THE_ORDER_HAS_BEEN_CHECKED_DO_NO_REPEAT_THE_OPERATION'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			//判断审批类型
			$this->is_receivables = M('User')->where('role_id =%d',session('role_id'))->getField('is_receivables');
			$this->contract_id = $contract_id;
			if ($option == 1) {
				//当前流程是否结束
				$examine_step_info = $m_contract_examine->where(array('order_id'=>$contract['order_id']))->find();
				//当前流程是否结束
				//已审批role_id
				$is_check_role = $m_contract_check->where(array('contract_id'=>$contract['contract_id'],'order_id'=>$contract['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);
				$is_check_role[] = session('role_id');
				$examine_is_end = 0;
				if ($examine_step_info['relation'] == 1 ) {
					//并
					if ($is_check_role && !array_diff(array_merge(array_filter(explode(',',$examine_step_info['role_id']))),$is_check_role)) {
						$examine_is_end = 1;
					}
				} elseif ($examine_step_info['relation'] == 2) {
                    //或
					$examine_is_end = 1;
				} else {
					$examine_is_end = 1;
				}
				if ($examine_is_end == 1) {
					//当前流程结束，转下一审批流程
					$next_order_id = $contract['order_id']+1; //下一审批流程排序ID
					//获取下一审批人
                    $next_examine_step_info = $m_contract_examine->where(array('order_id'=>$next_order_id))->find();
                    if ($next_examine_step_info['relation'] == 1) {
                        $relation_name = '并';
                    } elseif ($next_examine_step_info['relation'] == 2) {
                        $relation_name = '或';
                    }
					$next_role_id = $next_examine_step_info['role_id'];
					$next_role_info = $m_user->where(array('role_id'=>array('in',$next_role_id)))->field('full_name,role_id,thumb_path')->select();
					$this->next_role_id = $next_role_id;
				} else {
					//当前流程，剩余审批人
					$next_order_id = $contract['order_id'];
					$next_examine_step_info = $m_contract_examine->where(array('order_id'=>$next_order_id))->find();
                    if ($next_examine_step_info['relation'] == 1) {
                        $relation_name = '并';
                    } elseif ($next_examine_step_info['relation'] == 2) {
                        $relation_name = '或';
                    }
					$next_role_id = $is_check_role ? array_merge(array_diff(array_filter(explode(',',$examine_step_info['role_id'])),$is_check_role)) : array_filter(explode(',',$examine_step_info['role_id']));
					$next_role_info = $m_user->where(array('role_id'=>array('in',$next_role_id)))->field('full_name,role_id,thumb_path')->select();
					$this->next_role_id = $next_role_id ? ','.implode(',',$next_role_id).',' : '';
				}
				
				$this->next_role_info = $next_role_info;
				$this->next_order_id = $next_order_id;
				$this->relation_name = $relation_name;
				//是否审批结束
				$contract_is_end = 0;
				if (!$next_role_info) {
					$contract_is_end = 1;
				}
			}

			$this->contract_is_end = $contract_is_end ? : 0;
			$this->option = $option;
			$this->assign('contract',$contract);
			$this->display();
		}
	}

	//审核历史
	public function check_list(){
		$m_contract_check = M('contract_check');
		$m_user = M('User');
		$contract_id = intval($_GET['id']);
		if($contract_id){
			$check_list = $m_contract_check ->where('contract_id =%d',$contract_id)->order('check_id asc')->select();
			$d_user = D('User');
			foreach($check_list as $kk=>$vv){
				$temp_val = $d_user->get_full_name(array($vv['role_id']));
				$check_list[$kk]['user'] = $temp_val[$vv['role_id']];
			}
			$this->check_list = $check_list;
		}
		$this->display();
	}
	
	//撤销审核
	public function revokeCheck(){
		$contract_id = $this->_get('id','intval');
		$m_contract = M('Contract');
		$m_receivables = M('Receivables');
		$m_receivingorder = M('Receivingorder');
		if(!$contract_id){
			alert('error', L('PARAMETER_ERROR'), U('contract/index'));
		}
		if(!$contract = $m_contract->where('contract_id = %d', $contract_id)->find()) {
			alert('error', L('THE_ORDER_DOES_NOT_EXIST_OR_HAS_BEEN_DELETED'),$_SERVER['HTTP_REFERER']);
		}

		// if (intval($contract['is_checked']) !== 1) {
		// 	alert('error','该合同未审核通过，无需撤销！', $_SERVER['HTTP_REFERER']);
		// }
		//只有管理员能撤销
		if (!session('?admin')) {
			alert('error','您没有此权限！，请联系管理员撤销审核！', U('contract/index'));
		}

		if($receivables_info = $m_receivables->where('contract_id =%d and is_deleted = 0',$contract_id )->find()){
			$receivingorder_list = $m_receivingorder->where('receivables_id = %d',$receivables_info['receivables_id'])->find();
			if($receivingorder_list){
				alert('error', '已存在收款单，无法撤销审核！如需撤销请先删除相关收款记录！',$_SERVER['HTTP_REFERER']);
			}
		}
		if($contract['is_checked'] != 0){
			$m_r_contract_sales = M('rContractSales');
			$r_contract_sales_info = $m_r_contract_sales->where(array('contract_id'=>$contract_id,'sales_type'=>array('neq',1)))->find();
			
			$sales_id = $m_r_contract_sales->where('contract_id = %d && sales_type = 0', $contract_id)->getField('sales_id');
			$sales_status = M('Sales')->where('sales_id =%d',$sales_id)->getField('status');
			
			if($sales_status == 97 || $sales_status == 99 || $r_contract_sales_info['sales_type'] == 2 || empty($r_contract_sales_info)){
				//记录审核意见
				$c_data = array();
				$c_data['role_id'] = session('role_id');
				$c_data['is_checked'] = 0;
				$c_data['contract_id'] = $contract_id;
				$c_data['check_time'] = time();
				$m_contract_check = M('ContractCheck');
				$m_contract_check->add($c_data);
				//把审批意见至为无效
				$m_contract_check->where(array('contract_id'=>$contract_id))->setField('is_end', 2);

				$data['is_checked'] = 0;
				$data['examine_role_id'] = 0;
				$data['order_id'] = 0;
				$data['examine_type_id'] = 0;
				$result = $m_contract->where('contract_id = %d', $contract_id)->save($data);
				M('Sales')->where('sales_id =%d',$sales_id)->setField('is_checked',0);
				$m_receivables->where('contract_id =%d and is_deleted = 0',$contract_id )->delete();
				if($result){
					actionLog($contract_id);
					//发送站内信
					$url=U('contract/view','id='.$contract_id);
					sendMessage($contract['creator_role_id'],'您创建的合同《<a href="'.$url.'">'.$contract['number'].'-'.$contract['contract_name'].'</a>》<font style="color:red;">已被撤销审核</font>！',1);
					alert('success', L('REVOKE_CHECK_SUCCESS'), $_SERVER['HTTP_REFERER']);
				}else{
					alert('error', L('REVOKE_CHECK_FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				alert('error', L('REVOKE_CHECK_FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error', L('THE_ORDER_HAS_BEEN_REVOKE_CHECKED_DO_NO_REPEAT_THE_OPERATION'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	 * 合同编号验重
	 * @param 
	 * @author 
	 * @return 
	 */
	public function yanchong(){
		$m_contract = M('Contract');
		$number = trim($_POST['number']);
		$prefixion = trim($_POST['prefixion']);
		$contract_id = intval($_POST['contract_id']);
		if($number){
			if($contract_id > 0){
				$contract_info = $m_contract ->where('contract_id =%d',$contract_id)->find();
				if($contract_info['number'] != $prefixion.$number){
					$result = $m_contract ->where('number ="%s"',$prefixion.$number)->find();
					if($result){
						$this->ajaxReturn('','合同编号不能重复！',0);
					}else{
						$this->ajaxReturn('','',1);
					}
				}else{
					$this->ajaxReturn('','',1);
				}
			}else{
				$result = $m_contract ->where('number ="%s"',$prefixion.$number)->find();
				if($result){
					$this->ajaxReturn('','合同编号不能重复！',0);
				}else{
					$this->ajaxReturn('','',1);
				}
			}
		}else{
			$this->ajaxReturn('','参数错误！',0);
		}
	}

	/**
	 * 合同统计
	 * @param 
	 * @author 
	 * @return 
	 */
	public function analytics() {
		$m_contract = M('Contract');
		$d_search = D('Search');
		$m_receivingorder = M('Receivingorder');

		$content_id = $_GET['content_id'] ? intval($_GET['content_id']) : 1;

		//权限判断
		$below_ids = getPerByAction(MODULE_NAME, ACTION_NAME,false);

		// 是否仅查询销售岗【默认sale只查销售、all全部】、roleIdRange() 方法已处理包含部门或员工的搜索情况
		$range = 'all'; // $_GET['user_range'] = trim($_GET['user_range']) ?: 'sale';
		$role_id_array = $d_search->roleIdRange($below_ids, $range);

		// 年份，默认当前年份
		$year = $_GET['year'] = $_GET['year'] ?: date('Y');

		if ($content_id == 1 || $content_id == 2) {
			$m_target = M('Target');

			for ($i = 1; $i <= 12; $i++) {
				$start_time = strtotime("{$year}-{$i}-01 0:0:0"); // 某个月起始时间戳
				$end_time = strtotime("+1 month", $start_time); // 某个的下个月起始时间戳

				if ($content_id == 1) {
					$where_contract = array();
					//实际合同
					$where_contract['due_time'] = array('between',array($start_time, $end_time));
					$where_contract['owner_role_id'] = array('in',$role_id_array);
					$where_contract['is_checked'] = 1;
					$contract_price[] = (float) $m_contract->where($where_contract)->sum('price');

					//目标合同
					$where_target['year'] = $year;
					if ($_GET['role'] && $_GET['role'] != 'all') {
						$where_target['id_type'] = 2;
						$where_target['id'] = intval($_GET['role']);
					} elseif ($_GET['department'] && $_GET['department'] != 'all') {
						$where_target['id_type'] = 1;
						$where_target['id'] = intval($_GET['department']);
					} else {
						$where_target['id_type'] = 1;
					}
					$where_target['target_type'] = 1;
					$target_price[] = (float) $m_target->where($where_target)->sum('month'.$i);

					// 去年同期
					$start_time = strtotime("-1 year", $start_time);
					$end_time = strtotime("+1 month", $start_time);
					$where_last_year['due_time'] = array('between',array($start_time, $end_time));
					$where_last_year['owner_role_id'] = array('in', $role_id_array);
					$where_last_year['is_checked'] = 1;
					$contract_price_last[] = (float) $m_contract->where($where_last_year)->sum('price');
				}
				
				if ($content_id == 2) {
					$where_receivingorder = array();
					//实际回款
					$where_receivingorder['pay_time'] = array('between',array($start_time, $end_time));
					$where_receivingorder['owner_role_id'] = array('in',$role_id_array);
					$where_receivingorder['status'] = 1;
					$receivingorder_price[] = (float) $m_receivingorder->where($where_receivingorder)->sum('money');

					//目标回款
					$where_target['year'] = $year;
					if ($_GET['role'] && $_GET['role'] != 'all') {
						$where_target['id_type'] = 2;
						$where_target['id'] = intval($_GET['role']);
					} elseif ($_GET['department'] && $_GET['department'] != 'all') {
						$where_target['id_type'] = 1;
						$where_target['id'] = intval($_GET['department']);
					} else {
						$where_target['id_type'] = 1;
					}
					$where_target['target_type'] = 2;
					$target_price[] = (float) $m_target->where($where_target)->sum('month'.$i);

					// 去年同期
					$start_time = strtotime("-1 year", $start_time);
					$end_time = strtotime("+1 month", $start_time);
					$where_last_year['pay_time'] = array('between',array($start_time, $end_time));
					$where_last_year['owner_role_id'] = array('in',$role_id_array);
					$where_last_year['status'] = 1;
					$receivingorder_price_last[] = (float) $m_receivingorder->where($where_last_year)->sum('money');
				}

				// 图表X轴的信息
				$tab_info['month_list'][] = $js_categories[] = "{$i}月份";
			}
			
			if ($content_id == 1) {
				$js_series[0]['name'] = '目标合同金额';
				$js_series[0]['data'] = $target_price;

				$js_series[1]['name'] = '实际合同金额';
				$js_series[1]['data'] = $contract_price;

				$js_series[2]['name'] = '去年同期金额';
				$js_series[2]['data'] = $contract_price_last;
				$type_name = '合同';
			}
			if ($content_id == 2) {
				$js_series[0]['name'] = '目标回款金额';
				$js_series[0]['data'] = $target_price;

				$js_series[1]['name'] = '实际回款金额';
				$js_series[1]['data'] = $receivingorder_price;

				$js_series[2]['name'] = '去年同期金额';
				$js_series[2]['data'] = $receivingorder_price_last;
				$type_name = '回款';
			}
// p($js_series);
			// 计算环比、同比增长率等
			foreach ($js_series[1]['data'] as $k => $v) {
				$tab_info['count_list'][$k] = $v;

				// 环比
				if ($k == 0) {
					// 1月份的和去年12月份比较计算
					$tab_info['ring_list'][$k] = growthRate($v, $js_series[2]['data'][11]);
				} else {
					$tab_info['ring_list'][$k] = growthRate($v, $js_series[1]['data'][$k-1]);
				}

				// 同比
				$tab_info['same_list'][$k] = growthRate($v, $js_series[2]['data'][$k]);

				// 合计
				$tab_info['total_count'] += $v;
			}
			$this->assign('tab_info', $tab_info);
// p($tab_info);			
			$this->type_name = $type_name;
			$this->js_categories = json_encode($js_categories);
			$this->js_series = json_encode($js_series);
		}
		if ($content_id == 3) {
			/*$where = array();
			$top_arr = array();

			$start_time = strtotime($year.'-01-01');
			$end_time = strtotime($year.'-12-31');
			foreach ($role_id_array as $k=>$v) {
				$contract_price = '';
				$where['owner_role_id'] = $v;
				$where['due_time'] = array('between',array($start_time,$end_time));
				$where['is_checked'] = array('neq',2);
				$contract_price = $m_contract->where($where)->sum('price');
				$top_arr[$k]['role_id'] = $v;
				$top_arr[$k]['price'] = $contract_price ? $contract_price : '0';
			}

			if ($top_arr) {
				$top_arr = array_sorts($top_arr,'price','desc');
				$top_arr = array_slice($top_arr,'0','10',true);
				$top_arr = array_merge($top_arr);
				$m_user = M('User');
				$user_arr = '';
				$price_arr = '';
				foreach ($top_arr as $k=>$v) {
					$user_name = $m_user->where(array('role_id'=>$v['role_id']))->getField('full_name');
					if ($k) {
						$user_arr .= ",";
						$price_arr .= ",";
					}
					$user_arr .= $user_name ? "'".$user_name."'" : "''";
					$price_arr .= $v['price'] ? $v['price'] : '0.00';
				}
				$this->user_arr = $user_arr;
				$this->price_arr = $price_arr;
			}*/
			$m_user = M('User');
			$start_time = strtotime("{$year}-1-1 0:0:0");
			$end_time = strtotime("+1 year", $start_time);
			$where['due_time'] = array('between', array($start_time, $end_time));
			$where['is_checked'] = 1;
			$where['owner_role_id'] = array('in', $role_id_array);
			$list = $m_contract->field('owner_role_id,sum(price) as sum_price')->where($where)->group('owner_role_id')->order('sum_price desc')->limit(10)->select();
			foreach ($list as $k => $v) {
				$js_categories[] = $m_user->where('role_id = %d', $v['owner_role_id'])->getField('full_name');
				$js_data[] = (float) $v['sum_price'];
			}
			$this->js_categories = json_encode($js_categories);
			$this->js_data = json_encode($js_data);
		}

		// 部门和员工搜索
		$this->roleList = getUserByRoleIdArray($below_ids);
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);

		$this->content_id = $content_id;
		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 合同数量统计
	 * @author lee
	 */
	public function analysis_number()
	{
		$m_contract = M('Contract');
		$d_search = D('Search');

		// 权限判断【共用analytics方法权限，需要单独判断】
		if (!checkPerByAction('contract', 'analytics')) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}
		// 默认权限范围
		$below_ids = getPerByAction('contract', 'analytics');

		// 是否仅查询销售岗【sale只查销售、all全部】、roleIdRange() 方法已处理包含部门或员工的搜索情况
		$range = $_GET['user_range'] = trim($_GET['user_range']) ?: 'all';
		$where['owner_role_id'] = array('in', $d_search->roleIdRange($below_ids, $range) ?: '');

		// 只统计审核通过的合同
		$where['is_checked'] = 1;

		// 年份，默认当前年份
		$year = $_GET['year'] = $_GET['year'] ?: date('Y');

		// 图表插件所需的json字符串
		$series_data[0]['name'] = $year.'年度';
		$series_data[1]['name'] = ($year - 1).'年度';

		$month_list = range(1, 12);
		foreach ($month_list as $k => $v) {
			$start_time = strtotime("{$year}-{$v}-01 0:0:0"); // 某个月起始时间戳
			$end_time = strtotime("+1 month", $start_time); // 某个的下个月起始时间戳
			// 跳过大于当前时间的搜索
			if ($start_time > time()) {
				// continue;
				$this_year_count = $series_data[0]['data'][] = 0;
			} else {
				$where_this_year['create_time'] = array('between', array($start_time, $end_time));
				$where_this_year += $where;
				$this_year_count = $series_data[0]['data'][] = (int) $m_contract->where($where_this_year)->count();
			}

			// 去年同期
			$start_time = strtotime("-1 year", $start_time);
			$end_time = strtotime("+1 month",  $start_time);
			$where_last_year['create_time'] = array('between', array($start_time, $end_time));
			$where_last_year += $where;
			$last_year_count = $series_data[1]['data'][] = (int) $m_contract->where($where_last_year)->count();

			// 图表X轴的信息
			$tab_info['month_list'][] = $js_categories[] = "{$v}月份";
		}
// p($series_data,'');
		// 计算环比、同比增长率等
		foreach ($series_data[0]['data'] as $k => $v) {
			$tab_info['count_list'][$k] = $v;

			// 计算环比
			if ($k == 0) {
				// 1月份的和去年12月份比较计算
				$tab_info['ring_list'][$k] = growthRate($v, $series_data[1]['data'][11]);
			} else {
				$tab_info['ring_list'][$k] = growthRate($v, $series_data[0]['data'][$k-1]);
			}

			// 计算同比
			$tab_info['same_list'][$k] = growthRate($v, $series_data[1]['data'][$k]);

			// 合计
			$tab_info['total_count'] += $v;
		}
		$this->assign('tab_info', $tab_info);
// p($tab_info);

		$this->assign('js_series', json_encode($series_data));
		$this->assign('js_categories', json_encode($js_categories));

		// 部门和员工搜索
		$idArray = getPerByAction(MODULE_NAME,ACTION_NAME,false);
		$this->roleList = getUserByRoleIdArray($idArray);
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);

		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 回款金额TOP10 暂定回款单的owner_role_id即为"销售人员"ID来分组统计
	 * @author lee
	 */
	public function received_top10()
	{
		$d_search = D('Search');
		$m_user = M('User');

		// 权限判断【共用analytics方法权限，需要单独判断】
		if (!checkPerByAction('contract', 'analytics')) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}
		// 默认权限范围
		$below_ids = getPerByAction('contract', 'analytics');

		// 是否仅查询销售岗【默认sale只查销售、all全部】、roleIdRange() 方法已处理包含部门或员工的搜索情况，且根据权限已过滤
		$range = 'all';
		$where['owner_role_id'] = array('in', $d_search->roleIdRange($below_ids, $range) ?: '');

		// 年份，默认当前年份
		$year = $_GET['year'] = $_GET['year'] ?: date('Y');
		$start_time = strtotime("{$year}-1-1 0:0:0");
		$end_time = strtotime("+1 year", $start_time);
		$where['pay_time'] = array('between', array($start_time, $end_time));

		$where['status'] = 1;
		$list = M('Receivingorder')->field('owner_role_id,sum(money) as sum_price')->where($where)->group('owner_role_id')->order('sum_price desc')->limit(10)->select();
		foreach ($list as $k => $v) {
			$js_categories[] = $m_user->where('role_id = %d', $v['owner_role_id'])->getField('full_name');
			$js_data[] = (float) $v['sum_price'];
		}
		$this->js_categories = json_encode($js_categories);
		$this->js_data = json_encode($js_data);

		// 部门和员工搜索
		$idArray = getPerByAction(MODULE_NAME,ACTION_NAME,false);
		$this->roleList = getUserByRoleIdArray($idArray);
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission')->where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);

		$this->alert = parseAlert();
		$this->display();
	}



	/**
	* 业绩目标管理
	**/
	public function collection()
	{
		$v_role = D('RoleView');
		$m_target = M('Target');
		$m_contract = M('Contract');
		$m_receivingorder = M('Receivingorder');

		$map['year'] = $year = intval($_GET['year']) ?: date('Y');
		$map['target_type'] = $target_type = intval($_REQUEST['target_type']) ? : 1;

		// 可搜索已停用用户
		$search_disable_user = M('Config')->where('name="search_disable_user"')->getField('value');
		$search_disable_user_where = '';
		if ($search_disable_user) {
			$search_disable_user_where = ' && status != 2 ';
			$search_disable_user_where2 = ' status != 2 ';
		}
		//部门列表
		$where = array();
		$department_id = intval($_GET['department_id']);
		if (!empty($department_id)) {
			$where['department_id'] = $department_id;
		}
		$dep_list = M('roleDepartment')->where($where)->field('department_id,parent_id,name')->select();

		//员工列表
		if (intval($_GET['department_id']) > 0 && $_GET['role_id'] == 'all') {
			$role_list = D('RoleView')
				->where('role_department.department_id = %d' . $search_disable_user_where, $_GET['department_id'])
				->field('role_id,user_name,role_name')
				->select();
		} elseif (intval($_GET['department_id']) > 0 && intval($_GET['role_id']) > 0){
			$role_list = $v_role->where('role.role_id = %d' . $search_disable_user_where, intval($_GET['role_id']))->select();  //查询个人
		} else {
			$role_list = $v_role->where($search_disable_user_where2)->field('role_id,user_name,department_name,role_name')->select(); //所有人
		}

		//合并数组
		$dep_list = is_array($dep_list) ? $dep_list : array();
		$role_list = is_array($role_list) ? $role_list : array();
		$list = array_merge($dep_list, $role_list);
		foreach ($list as $k => $v) {
			//业绩目标
			if ($v['role_id']) {
				$map['id_type'] = 2;
				$map['id'] = $v['role_id'];   //个人

				$where_finish['owner_role_id'] = $v['role_id'];
			} else {
				$map['id_type'] = 1;
				$map['id'] = $v['department_id'];  //部门

				$role_ids = $v_role->where('role_department.department_id = %d', $v['department_id'])->getField('role_id', true);
				$where_finish['owner_role_id'] = array('in', $role_ids ?: '');
			}
			$target = $m_target->where($map)->find();
			if (!$target) {
				unset($list[$k]);
				continue;
			}
			
			for ($i=1; $i < 13; $i++) { 
				$list[$k]['month'.$i.'_target'] = $target['month'.$i];
			}
			$list[$k]['first_quarter_target'] = $target['month1'] + $target['month2'] + $target['month3'];
			$list[$k]['second_quarter_target'] = $target['month4'] + $target['month5'] + $target['month6'];
			$list[$k]['third_quarter_target'] = $target['month7'] + $target['month8'] + $target['month9'];
			$list[$k]['fourth_quarter_target'] = $target['month10'] + $target['month11'] + $target['month12'];
			$list[$k]['total'] = $target['total'];

			//实际完成
			if ($target_type == 1) {  //销售额（查合同）
				$where_finish['is_deleted'] = 0;
				$where_finish['is_checked'] = 1;
				for ($i=1; $i < 13; $i++) { 
					$month_range = $this->get_month_range($year, $i);
					$where_finish['due_time'] = array(array('egt', $month_range['begin']), array('lt', $month_range['end']));
					
					$list[$k]['month'.$i.'_finish'] = $m_contract->where($where_finish)->sum('price');
				}	
			} elseif ($target_type == 2) {  //回款金额（查回款记录）
				$where_finish['is_deleted'] = 0;
				$where_finish['status'] = 1;

				for ($i=1; $i < 13; $i++) { 
					$month_range = $this->get_month_range($year, $i);
					$where_finish['pay_time'] = array(array('egt', $month_range['begin']), array('lt', $month_range['end']));
				
					$list[$k]['month'.$i.'_finish'] = $m_receivingorder->where($where_finish)->sum('money');
				}
			}

			$list[$k]['first_quarter_finish'] = $list[$k]['month1_finish'] + $list[$k]['month2_finish'] + $list[$k]['month3_finish'];
			$list[$k]['second_quarter_finish'] = $list[$k]['month4_finish'] + $list[$k]['month5_finish'] + $list[$k]['month6_finish'];
			$list[$k]['third_quarter_finish'] = $list[$k]['month7_finish'] + $list[$k]['month8_finish'] + $list[$k]['month9_finish'];
			$list[$k]['fourth_quarter_finish'] = $list[$k]['month10_finish'] + $list[$k]['month11_finish'] + $list[$k]['month12_finish'];
			$list[$k]['total_finish'] = $list[$k]['first_quarter_finish'] + $list[$k]['second_quarter_finish'] + 
										$list[$k]['third_quarter_finish'] + $list[$k]['fourth_quarter_finish'];

			//计算完成率
			for ($i=1; $i < 13; $i++) { 
				$list[$k]['month'.$i.'_rate'] = round($list[$k]['month'.$i.'_finish'] / $list[$k]['month'.$i.'_target'], 4) * 100;
			}
			$list[$k]['first_quarter_rate'] = round($list[$k]['first_quarter_finish'] / $list[$k]['first_quarter_target'], 4) * 100;
			$list[$k]['second_quarter_rate'] = round($list[$k]['second_quarter_finish'] / $list[$k]['second_quarter_target'], 4) * 100;
			$list[$k]['third_quarter_rate'] = round($list[$k]['third_quarter_finish'] / $list[$k]['third_quarter_target'], 4) * 100;
			$list[$k]['fourth_quarter_rate'] = round($list[$k]['fourth_quarter_finish'] / $list[$k]['fourth_quarter_target'], 4) * 100;
			$list[$k]['total_rate'] = round($list[$k]['total_finish'] / $list[$k]['total'], 4) * 100;
		}
		$this->assign('list', $list);

		//部门岗位
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);

		if (intval($_GET['department_id']) > 0) {
			//$this->roleList = getRoleByDepartmentId($_GET['department_id'], true); //部门所有人
			$this->roleList = D('RoleView')
				->where('role_department.department_id = %d' . $search_disable_user_where, $_GET['department_id'])
				->field('role_id,user_name,role_name')
				->select();
		} else {
			$this->roleList = $role_list;
		}
		
		//年份列表
		$this->year_list = range(2015, 2050);
		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 销售(合同)金额/回款金额业绩目标完成率排行
	 * @author lee
	 */
	public function target_rank()
	{
		$d_role_view = D('RoleView');
		$m_target = M('Target');
		$d_search = D('Search');

		// 权限判断【共用analytics方法权限，需要单独判断】
		if (!checkPerByAction('contract', 'analytics')) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}
		// 默认权限范围
		$below_ids = getPerByAction('contract', 'analytics');

		// 是否仅查询销售岗【默认sale只查销售、all全部】、roleIdRange() 方法已处理包含部门或员工的搜索情况，且根据权限已过滤
		$range = 'all';
		$where['owner_role_id'] = array('in', $d_search->roleIdRange($below_ids, $range) ?: '');

		// 时间段搜索【前台js已做限制，最大范围一年，且为必填项】
		$start_date = $_GET['start_date'] = $_GET['start_date'] ?: date('Y-m');
		$end_date = $_GET['end_date'] = $_GET['end_date'] ?: date('Y-m');
		$start_time = strtotime($start_date);
		$end_time = strtotime("$end_date +1 month") - 1;

		// 根据月份范围，拼接mysql查询语句
		$month_range = range(date('n', $start_time), date('n', $end_time));
		$field_list = array_map(create_function('$val', 'return "month{$val}";'), $month_range);
		$field_str = implode('+', $field_list);
		$sql_field = "({$field_str}) as target_price";

		// 金额类型【1销售额：合同金额、2回款额：回款单金额】
		$target_type = $_GET['target_type'] = $_GET['target_type'] ? intval($_GET['target_type']) : 1;
		if ($target_type == 1) {
			$where['due_time'] = array('between', array($start_time, $end_time));
			$where['is_checked'] = 1;
			$list = M('Contract')->field('owner_role_id,sum(price) as sum_price')->where($where)->group('owner_role_id')->select();
		} elseif ($target_type == 2) {
			$where['pay_time'] = array('between', array($start_time, $end_time));
			$where['status'] = 1;
			$list = M('Receivingorder')->field('owner_role_id,sum(money) as sum_price')->where($where)->group('owner_role_id')->select();
		}
		foreach ($list as $k => $v) {
			$where_target['target_type'] = $target_type;
			$where_target['id_type'] = 2;
			$where_target['id'] = $v['owner_role_id'];
			$where_target['year'] = date('Y', $start_time);
			$target_price = $m_target->field($sql_field)->where($where_target)->find();
			$list[$k]['target_price'] = $target_price['target_price'];
			$list[$k]['finish_rate'] = round($v['sum_price'] / $target_price['target_price'], 4) * 100;
			// 员工信息
			$role = $d_role_view->field('user_name,department_name')->where('role.role_id = %d', $v['owner_role_id'])->find();
			$list[$k] += $role;
		}

		// 根据字段finish_rate对数组进行降序排列
		// $finish_rate = array_column($list, 'finish_rate'); // php5.3不支持
		// array_multisort($finish_rate,SORT_DESC,$list);	
		$list = sort_select($list, 'finish_rate', 1);
		$this->assign('list', $list);

		// 部门搜索
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type = M('Permission')->where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);

		$this->alert = parseAlert();
		$this->display();
	}


	/**
	* 设置业绩目标
	**/
	public function set_target(){
		$m_target = M('Target');

		if ($this->isPost()) {

			$where['target_type'] = intval($_POST['target_type']);
			$where['year'] = intval($_POST['year']);

			$department_id = intval($_POST['department_id']);
			if (!empty($department_id)) {
				//检查是否已经设置
				$where['id_type'] = 1;
				$where['id'] = $department_id;
				$target = $m_target->where($where)->find();

				if ($m_target->create()) {
					$m_target->id_type = 1;
					$m_target->id = $department_id;

					if ($target) {
						$m = $m_target->where('target_id = %d', $target['target_id'])->save();
					} else {
						$m = $m_target->add();
					}
					if ($m !== false) {
						alert('success', '设置成功！', $_SERVER['HTTP_REFERER']);
					} else {
						alert('error', '保存失败！', $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', '保存失败！', $_SERVER['HTTP_REFERER']);
				}
			} else if (is_array($_POST['role_ids'])){
				foreach ($_POST['role_ids'] as $k => $v) {
					//检查是否已经设置
					$where['id_type'] = 2;
					$where['id'] = $v;
					$target = $m_target->where($where)->find();

					if ($m_target->create()) {
						$m_target->id_type = 2;
						$m_target->id = $v;

						if ($target) {
							$m = $m_target->where('target_id = %d', $target['target_id'])->save();
						} else {
							$m = $m_target->add();
						}
						if ($m === false) {
							alert('error', '保存失败！', $_SERVER['HTTP_REFERER']);
						}
					} else {
						alert('error', '保存失败！', $_SERVER['HTTP_REFERER']);
					}
				}
				alert('success', '设置成功！', $_SERVER['HTTP_REFERER']);
			}
		} else {
			$rel = intval($_GET['rel']);
			if ($rel == 1 || $rel == 2) {
				//部门岗位
				$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
				$per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
				if($per_type == 2 || session('?admin')){
					$departmentList = M('roleDepartment')->select();
				}else{
					$departmentList = M('roleDepartment')->where('department_id =%d',session('department_id'))->select();
				}
				$this->assign('departmentList', $departmentList);
			} else if ($rel == 3){ //修改部门目标
				$where['target_type'] = intval($_GET['target_type']);
				$where['id_type'] = 1;
				$where['id'] = intval($_GET['id']);
				$where['year'] = intval($_GET['year']);
				$target = $m_target->where($where)->find();
				$target['name'] = M('roleDepartment')->where('department_id = %d', intval($_GET['id']))->getField('name');
				$this->assign('target', $target);
			} else if ($rel == 4){ //修改个人目标
				$where['target_type'] = intval($_GET['target_type']);
				$where['id_type'] = 2;
				$where['id'] = intval($_GET['id']);
				$where['year'] = intval($_GET['year']);
				$target = $m_target->where($where)->find();
				$target['name'] = M('User')->where('role_id = %d', intval($_GET['id']))->getField('full_name');
				$this->assign('target', $target);
			}

			//年份列表
			$this->year_list = range(2015, 2050);

			$this->alert = parseAlert();
			$this->display();
		}
	}

	//删除年度目标
	public function target_del(){
		$m_target = M('Target');

		$target_id = intval($_GET['target_id']);
		$m = $m_target->where('target_id = %d', $target_id)->delete();
		if ($m) {
			alert('success', '操作成功！', $_SERVER['HTTP_REFERER']);
		} else {
			alert('error', '操作失败！', $_SERVER['HTTP_REFERER']);
		}
	}

	// 设置业绩目标【选择员工】
	public function choose(){
		$list = M('roleDepartment')->where(true)->field('department_id,parent_id,name')->select();

		foreach ($list as $k => $v) {
			$list[$k]['role_list'] = D('RoleView')
				->where('role_department.department_id = %d and status = 1', $v['department_id'])
				->field('role_id,user_name,role_name,thumb_path')
				->select();
		}

		//所有正常状态员工
		$this->role_list = D('RoleView')
			->where('status = 1')
			->field('role_id,user_name,role_name,thumb_path')
			->select();

		$this->assign('list', $list);
		$this->alert = parseAlert();
		$this->display();
	}


	/**
	* 获取年度每个月的时间戳范围
	**/
	private function get_month_range($year, $month){
		$data['begin'] = strtotime($year.'-'.$month.'-01 00:00:00');
		$data['end'] = strtotime("+1 month", $data['begin']);

		return $data;
	}

	/**
	 * 首页动态合同金额统计
	 * @param 
	 * @author 
	 * @return 
	 */
	public function getmonthlycontract() {
		$type = $_GET['type'] ? intval($_GET['type']) : 1;
		$m_contract = M('Contract');
		$role_id_array = array('eq',session('role_id'));

		//role_id查询范围
		$dashboard = M('user')->where('user_id = %d', session('user_id'))->getField('dashboard');
		$widget = unserialize($dashboard);
		$id = intval($_GET['id']);
		foreach($widget['dashboard'] as $k=>$v){
			if($v['widget'] == 'Productmonthlysales' && $v['id'] == $id && $type == 1){
				if($v['level'] == '1'){
					$role_id_array = array('in',getSubRoleId());
				}else{
					$role_id_array = array('eq', session('role_id'));
				}
			}
			if($v['widget'] == 'Receivemonthly' && $v['id'] == $id && $type == 2){
				if($v['level'] == '1'){
					$role_id_array = array('in',getSubRoleId());
				}else{
					$role_id_array = array('eq', session('role_id'));
				}
			}
		}
		//时间段搜索
		$years = date('Y');
		$m_target = M('Target');

		$where_target = array(); //合同目标、回款目标
		$contract_month = array();
		$contract_data = array();
		$receivingorder_data = array();

		$target_data = array();
		$k = 0;
		for ($i = 1; $i <= 12; $i++) {
			if ( in_array($i , array(01 , 03 , 05 , 07 , 08 , 10 , 12))) {
				$days = 31;
			} elseif ( $i == 2 ) {
				if ( $years%400 == 0  || ($years%4 == 0 && $years%100 !== 0) ) {
					$days = 29;
				} else {
					$days = 28;
				}
			} else {
				$days = 30;
			}
			$beginThismonth = mktime(0,0,0,$i,1,$years);
			$endThismonth = mktime(23,59,59,$i,$days,$years);
			$contract_month[] = $i.'月';

			if ($type == 1) {
				$where_contract = array();
				//实际合同
				$where_contract['create_time'] = array('between',array($beginThismonth,$endThismonth));
				$where_contract['owner_role_id'] = $role_id_array;
				$where_contract['is_checked'] = array('neq',2);
				$contract_price = $m_contract->where($where_contract)->sum('price');
				$contract_data[] = $contract_price ? $contract_price : '0.00';
				// $contract_data[$k]['color'] = '#ED7D31';
			}

			if ($type == 2) {
				$where_receivingorder = array();
				//实际回款
				$m_receivingorder = M('Receivingorder');
				$where_receivingorder['pay_time'] = array('between',array($beginThismonth,$endThismonth));
				$where_receivingorder['owner_role_id'] = $role_id_array;
				$where_receivingorder['status'] = array('neq',2);
				$receivingorder_price = $m_receivingorder->where($where_receivingorder)->sum('money');
				$receivingorder_data[] = $receivingorder_price ? $receivingorder_price : '0.00';
			}
			//目标回款
			$where_target['year'] = $years;
			$where_target['id_type'] = 2;
			if ($type == 1) {
				$where_target['target_type'] = 1;
				$where_target['id'] = $role_id_array;
			} elseif ($type == 2) {
				$where_target['target_type'] = 2;
				$where_target['id'] = $role_id_array;
			}
			$target_price = $m_target->where($where_target)->sum('month'.$i);
			$target_data[] = $target_price ? $target_price : '0.00';

			$k++;
		}
		$json_arr['contract_month'] = $contract_month;
		if ($type == 1) {
			$json_arr['contract_data'] = $contract_data;
		} elseif ($type == 2) {
			$json_arr['receivingorder_data'] = $receivingorder_data;
		}
		$json_arr['target_data'] = $target_data;
		$this->ajaxReturn($json_arr,'success',1);
	}

	/**
	 * 合同自定义审批流
	 * @param 
	 * @author 
	 * @return 
	 */
	public function examine() {
		$m_contract_examine = M('ContractExamine');
		$m_user = M('User');
		$m_config = M('Config');
		$examine_list = $m_contract_examine->order('order_id asc')->select();
		foreach ($examine_list as $k=>$v) {
			//处理order_id排序异常
			$data = array();
			$data = array('id'=>$v['id'], 'order_id'=>$k);
			$m_contract_examine->save($data);

			$role_ids = explode(',',$v['role_id']);
			$examine_list[$k]['role_list'] = $m_user->where(array('role_id'=>array('in',$role_ids)))->field('thumb_path,full_name,role_id')->select();
			if (count($role_ids) > 1) {
				if ($v['relation'] == 1) {
					$examine_list[$k]['relation_name'] = '并';
				} elseif ($v['relation'] == 2) {
					$examine_list[$k]['relation_name'] = '或';
				}
			}
		}
		//合同审批类型
		$examineinfo = array();
		$examineinfo = $m_config->where('name = "contract_examine"')->getField('value');
		$examineinfo['contract_examine'] = $examineinfo['contract_examine'] ? $examineinfo['contract_examine'] : 0;
		$this->examineinfo = $examineinfo;		
		$this->examine_list = $examine_list;
		$this->display();
	}

	/**
	 * 合同自定义审批流(编辑、类型设置)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function examineData() {
		//是否有正在使用中的合同
		if (M('Contract')->where(array('is_checked'=>3))->find()) {
			if ($this->isGet()) {
				echo '<div class="alert alert-error">有相关合同正在使用该流程，无法编辑！</div>';die();
			} else {
				$this->ajaxReturn('','有相关合同正在使用该流程，无法编辑！',0);
			}
		}
		$m_contract_examine = M('ContractExamine');
		$m_user = M('User');
		$d_role = D('RoleView');
		$m_config = M('Config');

		if ($this->isPost()) {
			//审批类型设置
			if ($_POST['openrecycle']) {
				$contract_examine = 0;
				if (intval($_POST['openrecycle']) == 2) {
					$contract_examine = 1;
				}

				//合同审批类型
				$examineinfo = $m_config->where(array('name'=>'contract_examine'))->find();
				if (!$examineinfo) {
					if ($m_config->add(array('value'=>$contract_examine, 'name'=>'contract_examine', 'description'=>'合同审批类型（1自定义了流程）'))) {
						$result_examineinfo = true;
					} else {
						$result_examineinfo = false;
					}
				} else {
					if ($m_config->where(array('name'=>'contract_examine'))->save(array('value'=>$contract_examine))) {
						$result_examineinfo = true;
					} else {
						$result_examineinfo = false;
					}
				}
				if ($result_examineinfo) {
					$this->ajaxReturn('','success',1);
				} else {
					$this->ajaxReturn('','error',0);
				}
			}
			//审批流程编辑
			$m_contract_examine->create();
			if ($_POST['step_id']) {
				$m_contract_examine->id = intval($_POST['step_id']);
			}
			if ($m_contract_examine->save()) {
				$this->ajaxReturn('',"修改成功!",1);
				// alert('success','编辑成功！',$_SERVER['HTTP_REFERER']);
			} else {
				$this->ajaxReturn('',"编辑失败，请重试！",0);
			}
		} else {
			$examine_list = $m_contract_examine->order('order_id asc')->select();
			foreach($examine_list as $k=>$v){
				$role_ids = explode(',',$v['role_id']);
				$examine_list[$k]['role_list'] = $m_user->where(array('role_id'=>array('in',$role_ids)))->field('full_name,thumb_path,role_id')->select();
				if (!strpos('5k'.$v['role_id'],',')) {
					$examine_list[$k]['role_id'] = ','.$v['role_id'].',';	
				} 
				if ($v['relation'] == 1) {
					$examine_list[$k]['relation_name'] = '并';
				} elseif ($v['relation'] == 2) {
					$examine_list[$k]['relation_name'] = '或';
				}
			}
			$this->assign('examine_list',$examine_list);
			$this->display('edit_process');
		}
	}

	/**
	 * 清空审批流程数据
	 * @param 
	 * @author 
	 * @return 
	 */
	public function clearExamine(){
		//是否有正在使用中的合同
		if (M('Contract')->where(array('examine_type_id'=>1,'is_checked'=>3))->find()) {
			$this->ajaxReturn('error','有相关合同正在使用该流程，无法清除！',0);
		}
		$m_contract_examine = M('ContractExamine');
		$result = $m_contract_examine->where(array('id'=>array('gt',0)))->delete();
		if ($result) {
			$this->ajaxReturn('success','删除成功！',1);
		} else {
			$this->ajaxReturn('error','删除失败！',0);
		}
	}

	/**
	 * 审批流程排序
	 * @param 
	 * @author 
	 * @return 
	 */
	public function examineSort(){
		//是否有正在使用中的合同
		if (M('Contract')->where(array('examine_type_id'=>1,'is_checked'=>3))->find()) {
			$this->ajaxReturn('error','有相关合同正在使用该流程，无法编辑！',0);
		}
		$m_contract_examine = M('ContractExamine');
		$postion_arr = explode(',', $_GET['postion']);
		foreach ($postion_arr as $k=>$v) {
			$data = array('id'=> $v, 'order_id'=>$k);
			$m_contract_examine->save($data);
		}
		$this->ajaxReturn('1', L('SUCCESSFULLY EDIT'), 1);
	}

	/**
	 * 增加、编辑 步骤
	 * @param 
	 * @author 
	 * @return 
	 */
	public function step(){
		$m_contract_examine = M('ContractExamine');
		$m_user = M('User');
		$id = $_REQUEST['step_id'] ? intval($_REQUEST['step_id']) : '';
		if ($this->isPost()) {
			if ($m_contract_examine->create()) {
				if ($id) {
					//编辑
					$relation = intval($_POST['relation']) ? : '';
					if (!$relation) {
						$step_relation = $m_contract_examine->where(array('step_id'=>intval($_POST['step_id'])))->getField('relation');
						if (!$step_relation) {
							$relation = 1;
						} else {
							$relation = $step_relation;
						}
					}
					$m_contract_examine->relation = $relation;
					$result = $m_contract_examine->where(array('id'=>$id))->save();
				} else {
					//添加
					$order_id = $m_contract_examine->max('order_id');
					$m_contract_examine->order_id = $order_id+1;
					$m_contract_examine->relation = 1;
					$result = $id = $m_contract_examine->add();
				}
				if ($result !== false) {
					//order重置
					$examine_list = $m_contract_examine->order('order_id asc')->select();
					// foreach ($examine_list as $k=>$v) {
					// 	$examine_data = array();
					// 	$examine_data = array('order_id'=>$k);
					// 	$m_contract_examine->where(array('id'=>$v['id']))->save($examine_data);
					// }

					$data = array();
					$role_ids = array_filter(explode(',',trim($_POST['role_id'])));
					$role_list = array();
					foreach($role_ids as $k=>$v){
						$user_info = array();
						$user_info = $m_user->where('role_id = %d',$v)->field('role_id,full_name,thumb_path')->find();
						if (empty($user_info['thumb_path'])) {
							$user_info['thumb_path'] = './Public/img/avatar_default.png';
						}
						$role_list[] = $user_info;
					}
					$data['role_list'] = $role_list;
					$data['id'] = $id;
					$this->ajaxReturn($data,'成功',1);
				} else {
					$this->ajaxReturn('','失败',0);
				}
			} else {
				$this->ajaxReturn('','操作失败',0);
			}
		}
	}

	/**
	 * 删除流程
	 * @param 
	 * @author 
	 * @return 
	 */
	public function step_delete(){
		if ($this->isAjax()) {
			$m_contract_examine = M('ContractExamine');
			$step_id = intval($_GET['step_id']);
			$info = $m_contract_examine->where('id = "%d"',$step_id)->find();
			if (empty($info)) {
				$this->ajaxReturn('',"该信息不存在",0);
			} else {
				if ($m_contract_examine->where('id = "%d"',$step_id)->delete()) {
					$sql = 'update '.C('DB_PREFIX').'contract_examine set order_id = order_id-1 where order_id > '.$info['order_id'];
					mysql_query($sql);
					$this->ajaxReturn('',"删除成功",1);
				} else {
					$this->ajaxReturn('',"删除失败",0);
				}
			}
		}
	}

	/**
	 * 统计相关数字，点击跳转到合同列表需要拼接的url参数处理
	 * @author lee
	 */
	public function search_list()
	{	
		$d_search = D('Search');
		// 默认权限范围
		$below_ids = getPerByAction('contract', 'analytics');
		// 是否仅查询销售岗【sale只查销售、all全部】、roleIdRange() 方法已处理包含部门或员工的搜索情况
		$range = $_GET['user_range'] = trim($_GET['user_range']) ?: 'all';
		// role_id范围
		$url_str = "&owner_role_id[condition]=in";
		$url_str .= "&owner_role_id[value]=".implode(',', $d_search->roleIdRange($below_ids, $range));

		// 时间范围
		if ($_GET['month'] == 'all') {
			$url_str .= "&create_time[start]="."{$_GET['year']}-1-1";
			$url_str .= "&create_time[end]=".date('Y-m-d', strtotime("{$_GET['year']}-1-1 +1 year -1 day"));
		} else {
			$url_str .= "&create_time[start]="."{$_GET['year']}-{$_GET['month']}-1";
			$url_str .= "&create_time[end]=".date('Y-m-d', strtotime("{$_GET['year']}-{$_GET['month']}-1 +1 month -1 day"));
		}
		$url_str .= "&create_time[form_type]=date";

		// 只统计审核通过的合同
		$url_str .= "&is_checked[condition]=is";
		$url_str .= "&is_checked[value]=1";	

		$this->ajaxReturn($url_str, 'success', 1);
	}

}
