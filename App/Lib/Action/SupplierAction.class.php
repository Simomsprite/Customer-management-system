<?php
/**
*任务模块
*
**/
class SupplierAction extends Action{
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('list_dialog')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
	}
	
	/**
	*供应商列表
	*
	**/
	public function index(){
		$d_supplier = D('SupplierView');
		$m_supplier_contacts = M('SupplierContacts');

		$where['is_deleted'] = 0;
		$status = $this->_get('status','intval');
		if ($status) {
			$where['status'] = $status;
		}
		//简单查询
		$search = $this->_get('search','trim');
		if ($search) {
			$map['number'] = $map['name'] = array('like','%'.$search.'%');

			$where_contacts['_string'] = 'name like %'.$search.'% or telephone like %'.$search.'%';
			$supplier_ids = $m_supplier_contacts->where($where_contacts)->getField('supplier_id', true) ?: '';
			$map['supplier_id'] = array('in', $supplier_ids);
			$map['_logic'] = 'or';
			$where['_complex'] = $map;
		}

		//高级查询
		//多选类型字段
		$check_field_arr = M('Fields')->where(array('model'=>'suulier','form_type'=>'box','setting'=>array('like','%'."'type'=>'checkbox'".'%')))->getField('field',true);
		//高级搜索
		if(!$_GET['field']){
			$no_field_array = array('act','content','p','condition','listrows','search','by');
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
								$k = 'supplier.create_time';
							}elseif($k == 'update_time'){
								$k = 'supplier.update_time';
							}
							//时间段查询
							if ($v['start'] && $v['end']) {
								$where[$k] = array('between',array(strtotime($v['start']),strtotime($v['end'])+86399));
							} elseif ($v['start']) {
								$where[$k] = array('egt',strtotime($v['start']));
							} else {
								$where[$k] = array('elt',strtotime($v['end'])+86399);
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
						}elseif(($v['value']) != '') {
							if (in_array($k,$check_field_arr)) {
								$where[$k] = field($v['value'],'contains');
							} else {
								$where[$k] = field($v['value'],$v['condition']);
							}
						}
                	}else{
						if(!empty($v)){
							$where[$k] = field($v);
						}
				    }
                }
                if($k == 'supplier.create_time'){
					$k = 'create_time';
				}elseif($k == 'supplier.update_time'){
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
		}

		//高级搜索字段
		$fields_list_data = M('Fields')->where(array('model'=>array('in',array('','supplier')),'is_main'=>1))->field('field,form_type')->select();
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
		$this->assign('fields_search', $fields_search);  //高级搜索信息

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

		//排序
		$order = 'update_time desc';

		//获取列表数据
		$list = D('Supplier')->getList($p, $listrows, $where, $order);
		$this->assign('list', $list);

		//分页
		$count = $d_supplier->where($where)->count();
		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		if (!empty($_GET['status'])) {
			$params[] = 'status='.trim($_GET['status']);
		}
		$this->parameter = implode('&', $params);
		$this->by_parameter = str_replace('status='.$_GET['status'], '', implode('&', $params));
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());

		//自定义字段列表显示
		$field_array = getIndexFields('supplier');
 		$name_field_array = array();
		foreach($field_array as $k=>$v){
			$name_field_array[] = $v;
		}
		$this->field_array = $name_field_array;
		$field_list = M('Fields')->where(array('model'=>'supplier','is_main'=>1))->order('order_id asc')->select();
		$this->field_list = $field_list;

		$this->alert = parseAlert();
		$this->display();
	}

	/**
	*供应商添加
	*
	**/
	public function add(){
		if($this->isPost()){		
			$d_supplier = D('Supplier');
			$supplier_id = $d_supplier->saveData();
			if ($supplier_id) {
				actionLog($supplier_id);
				alert('success', '保存成功', U('supplier/view','id='.$supplier_id));
			} else {
				$this->error('添加失败');
			}
		}else{	
			$field_list = field_list_html("add","supplier");
			$count = (int) M('Supplier')->count();
			$count += 1;
			$count = str_pad((string) $count, 4, '0', STR_PAD_LEFT);
			$this->number = 'S_' . date('Ymd') . '_' . $count;
		 	$this->field_list = $field_list;
			$this->alert = parseAlert();
			$this->display();
		}
	}

	/**
	*供应商编辑
	*
	**/
	public function edit(){
		$d_supplier = D('Supplier');
		$d_supplier_data = D('SupplierData');
		$supplier_id = $this->_request('id','intval',intval($_POST['supplier_id']));

		$supplier = D('SupplierView')->where('supplier.supplier_id = %d',$supplier_id)->find();
		if (!$supplier) {
            alert('error', '供应商不存在',$_SERVER['HTTP_REFERER']);
        }

        $field_list = M('Fields')->where('model = "supplier"')->order('order_id')->select();

		if (IS_POST) {
			$ret = $d_supplier->saveData($supplier_id);
			if ($ret) {
				actionLog($supplier_id);
				alert('success', '保存成功', U('supplier/view','id='.$supplier_id));
			} else {
				$this->error('保存失败');
			}
		} else {
			//自定义字段
			$this->field_list = field_list_html('edit','supplier',$supplier);

			$this->assign('supplier', $supplier);
			$this->assign('jump_url', U('supplier/view','id='.$this->_request('id','intval',intval($_POST['supplier_id']))));
			$this->alert = parseAlert();
			$this->display();
		}
	}

	/**
	*供应商详情
	*
	**/
	public function view(){
		$supplier_id = $this->_get('id','intval');
		$supplier = D('Supplier')->getView($supplier_id);
		$this->assign('supplier', $supplier);

		//自定义字段输出
		$field_list = field_list_html("edit","supplier",$supplier);
		$array_field = array();
		foreach ($field_list['main'] as $k => $v) {
			$array_field[] = $v;
		}

		foreach ($field_list['data'] as $k => $v) {
			$array_field[] = $v;
		}
		$field_list = $array_field;
		if(count($field_list)%2 == 1){
			$field_list[] = array('name'=>'','field'=>null);
		}
		$this->assign('field_list', $field_list);

		//联系人信息
		$m_supplier_contacts = M('SupplierContacts');
		$contacts_list = $m_supplier_contacts->where('supplier_id = %d', $supplier_id)->select();
		$this->assign('contacts_list', $contacts_list);

		$this->alert = parseAlert();
		$this->display();
	}


	/**
	* 供应商删除
	* 因为需要查询供应商下的是否存在采购等关联信息等，由此判断是否可删除，所以暂定只能单个删除
	**/
	// public function delete(){
	// 	$m_supplier = M('Supplier');

	// 	$supplier_id = $this->_post('supplier_id','intval');

	// 	//需要先处理，查询是否可删除逻辑
	// 	#code...

	// 	$m = $m_supplier->where('supplier_id = %d', $supplier_id)->setField('is_deleted', 1);
	// 	if ($m) {
	// 		$data['status'] = 1;
	// 		$data['info'] = 'success';
	// 		$this->ajaxReturn($data);
	// 	} else {
	// 		$data['status'] = 2;
	// 		$data['info'] = '删除失败';
	// 		$this->ajaxReturn($data);
	// 	}
	// }



	/**
	*供应商联系人添加
	*
	**/
	public function contacts_add(){
		if (IS_POST) {
			$m_supplier_contacts = M('SupplierContacts');
			if ($m_supplier_contacts->create()) {
				$contacts_id = $m_supplier_contacts->add();
				if ($contacts_id) {
					alert('success', '保存成功', U('supplier/view','id='.$this->_post('supplier_id','intval').'#tab2'));
				} else {
					alert('error', '保存失败', U('supplier/view','id='.$this->_post('supplier_id','intval').'#tab2'));
				}
			} else {
				alert('error', '数据写入失败', U('supplier/view','id='.$this->_post('supplier_id','intval').'#tab2'));
			}
		} else {
			$this->display();
		}
	}

	
	/**
	*供应商联系人编辑
	*
	**/
	public function contacts_edit(){
		$m_supplier_contacts = M('SupplierContacts');
		$contacts_id = $this->_request('contacts_id','','intval');
		$contacts = $m_supplier_contacts->where('contacts_id = %d', $contacts_id)->find();
		if (IS_POST) {
			if ($m_supplier_contacts->create()) {
				$m = $m_supplier_contacts->where('contacts_id = %d', $contacts_id)->save();
				if ($m !== false) {
					alert('success', '保存成功', U('supplier/view','id='.$contacts['supplier_id'].'#tab2'));
				} else {
					alert('error', '保存失败', U('supplier/view','id='.$contacts['supplier_id'].'#tab2'));
				}
			} else {
				alert('error', '数据写入失败', U('supplier/view','id='.$contacts['supplier_id'].'#tab2'));
			}
		} else {
			$this->assign('contacts', $contacts);
			$this->display();
		}
	}

	/**
	*供应商联系人删除
	*
	**/
	public function contacts_delete(){
		$m_supplier_contacts = M('SupplierContacts');

		$contacts_id = $this->_request('contacts_id','','intval');
		$contacts = $m_supplier_contacts->where('contacts_id = %d', $contacts_id)->find();

		$m = $m_supplier_contacts->where('contacts_id = %d', $contacts_id)->delete();
		if ($m) {
			alert('success', '删除成功', U('supplier/view','id='.$contacts['supplier_id'].'#tab2'));
		} else {
			alert('error', '删除失败', U('supplier/view','id='.$contacts['supplier_id'].'#tab2'));
		}
	}

	/**
	*供应商弹窗列表
	*
	**/
	public function list_dialog(){
		$d_supplier = D('Supplier');

		$where['is_deleted'] = array('neq', 1);

		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']);
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);

			if ('create_time' == $field || 'update_time' == $field) {
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
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		$this->search_field = $_REQUEST;//搜索信息

		$p = !$_REQUEST['p'] || $_REQUEST['p'] <= 0 ? 1 : intval($_REQUEST['p']);

		$list = $d_supplier->getList($p, 10, $where, 'create_time desc');

		$count = $d_supplier->where($where)->count();
		import("@.ORG.DialogListPage");
		$Page = new Page($count,10);
		$Page->parameter = implode('&', $params);
		$this->assign('page',$Page->show());

		$data = getIndexFields('supplier');
		$this->field_num = sizeof($data)+1;
        $this->field_array = $data;

        $this->assign('list', $list);
		$this->display();
	}

	

}