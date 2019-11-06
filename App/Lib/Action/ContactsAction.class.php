<?php 
class ContactsAction extends Action {

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('checklistdialog','revert', 'mdelete','add_dialog','qrcode','validate','reltobusiness','changetofirstcontact','getcurrentstatus','excel_model_download','excel_import_act','error_data_download')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
	}
	
	/**
	*Ajax检测联系人唯一字段
	*
	**/
	public function validate() {
		if($this->isAjax()){
			if(!$this->_request('clientid','trim') || !$this->_request($this->_request('clientid','trim'),'trim')){
				$this->ajaxReturn("","",3);
			}
			$field = M('Fields')->where('model = "contacts" and field = "%s"',$this->_request('clientid','trim'))->find();
			$m_contacts = $field['is_main'] ? D('contacts') : D('ContactsData');
			$where[$this->_request('clientid','trim')] = array('eq',$this->_request($this->_request('clientid','trim'),'trim'));
			if($this->_request('id','intval',0)){
				$where[$m_contacts->getpk()] = array('neq',$this->_request('id','intval',0));
			}
			if($this->_request('clientid','trim')) {
				if ($m_contacts->where($where)->find()) {
					$this->ajaxReturn("","",1);
				} else {
					$this->ajaxReturn("","",0);
				}
			}else{
				$this->ajaxReturn("","",0);
			}
		}
	}
	
	public function add(){
		if ($_GET['r'] && $_GET['module'] && $_GET['id']) {
			$this -> r = $_GET['r'];
			$this -> module = $_GET['module'];
			$this -> id = $_GET['id'];
			$this->display('Contacts:add_dialog');
		}elseif($this->isPost()){
			$name = trim($_POST['name']);
			$customer_id = trim($_POST['customer_id']);
			if ($name == '' || $name == null) {
				$this->error(L('CONTACT NAME CANNOT BE EMPTY'));
			}
			if ($customer_id == '' || $customer_id == null) {
				$this->error(L('CUSTOMER NAME CANNOT BE EMPTY'));
			}
			$contacts = D('Contacts');
			$contacts_data = D('ContactsData');
			//自定义字段
			$field_list = M('Fields')->where('model = "contacts" and in_add = 1')->order('order_id')->select();
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
							$a =array_filter($_POST[$v['field']]);
							$_POST[$v['field']] = !empty($a) ? implode(chr(10),$a) : '';
						}
					break;
				}
			}
			if ($contacts->create()) {
				$contacts->create_time = time();
				$contacts->update_time = time();
				$contacts->creator_role_id = session('role_id');
				if($contacts_id = $contacts->add()){
					if($contacts_id){
						$RContactsCustomer['contacts_id'] = $contacts_id;
						$RContactsCustomer['customer_id'] = $_POST['customer_id'];
						if(M('RContactsCustomer') ->add($RContactsCustomer)){
							//商机关联联系人
							if($_POST['redirect'] == 'business'){
								$RBusinessContavts = array();
								$RBusinessContavts['business_id'] = intval($_POST['redirect_id']);
								$RBusinessContavts['contacts_id'] = $contacts_id;
								$res = M('RBusinessContacts')->add($RBusinessContavts);
							}
							$contacts_data->create();
							$contacts_data->contacts_id = $contacts_id;
							if($contacts_data->add()){
								if($_POST['refer_url']){
									alert('success', '添加联系人成功!', $_POST['refer_url'].'#tab3');
								}else{
									alert('success',L('ADD A SUCCESS'),U('contacts/view','id='.$contacts_id));
								}
							}
						}
					}else{
						alert('success',L('ADD A SUCCESS'),U('contacts/view','id='.$contacts_id));
					}
				}else{
					$this->error(L('ADD FAILURE'));
				}
			} else {
				$this->error($contacts->getError());
			}
		}else{
			$m_customer = M('Customer');
			if($_GET['redirect']){
				$this->redirect_id = intval($_GET['redirect_id']);
				if(trim($_GET['redirect']) == 'customer'){
					$customer_id = $this->redirect_id;
				}elseif(trim($_GET['redirect']) == 'business'){
					$customer_id = M('Business')->where('business_id = %d',intval($_GET['redirect_id']))->getField('customer_id');
				}
				// $d_module = $_GET['redirect'] == 'customer' ? array('customer_id'=>$this->redirect_id) : array();
				$d_module = array('customer_id'=>$customer_id);
				$this->redirect = trim($_GET['redirect']);
			}
			if(!empty($_GET['redirect_id'])){
				if(trim($_GET['redirect']) == 'customer'){
					$this->customer = $m_customer->where('customer_id =%d', intval($_GET['redirect_id']))->find();
				}elseif(trim($_GET['redirect']) == 'business'){
					$customer_id = M('Business')->where('business_id = %d',intval($_GET['redirect_id']))->getField('customer_id');
					$this->customer = $m_customer->where('customer_id =%s',$customer_id)->find();
				}
			}
			if($_GET['customer_id']){
				$this->customer_id = $customer_id = intval($_GET['customer_id']);
				$this->customer_name = $m_customer->where('customer_id =%d',$customer_id)->getField('name');
			}
			$this->refer_url = $_SERVER['HTTP_REFERER'];
			$this->field_list = field_list_html('add','contacts',$d_module);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	//联系人编辑
	public function edit(){
		$d_contacts = D('ContactsView');
		$RContactsCustomer = M('RContactsCustomer');
		$contacts_id = $_GET['id'] ? intval($_GET['id']) : intval($_POST['contacts_id']);
		if(empty($contacts_id)){
			alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
		//检查联系人date表中无关联时创建
		$contacts_data = M('ContactsData');
		$contactsid = $contacts_data->where('contacts_id = %d',$contacts_id)->find();
		if(!$contactsid){
			$data_a['contacts_id'] = $contacts_id;
			$contacts_data->add($data_a);
		}
		$field_list = M('Fields')->where('model = "contacts"')->order('order_id')->select();

		//检查权限
		$customer_id = M('RContactsCustomer')->where('contacts_id = %d', $contacts_id)->getField('customer_id');
		//判断联系人所在客户是否在客户池，如果在则不判断权限
		$customer_info = M('Customer')->where(array('customer_id'=>$customer_id))->find();
		$m_config = M('Config');
		$outdays = $m_config->where('name="customer_outdays"')->getField('value');
		$outdate = empty($outdays) ? 0 : time()-86400*$outdays;

		$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
		$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
		$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
		$openrecycle = $m_config->where('name="openrecycle"')->getField('value');
		if ($openrecycle == 2) {
			if ($customer_info['owner_role_id'] != 0 && (($customer_info['update_time'] > $outdate && $customer_info['get_time'] > $contract_outdays) || $customer_info['is_locked'] == 1)) {
				if (!in_array($customer_info['owner_role_id'], $this->_permissionRes) && !session('?admin')) {
					$this->error(L('HAVE NOT PRIVILEGES'));
				}
			}
		} 
		$contacts = $d_contacts->where(array('contacts_id'=>$contacts_id))->find();

		if(empty($contacts)){
			alert('error', L('RECORD_NOT_EXIST_OR_HAVE_BEEN_DELETED',array(L('CONTACTS'))),U('contacts/index'));
		} 
		if ($this->isPost()) {
			$m_contacts = D('Contacts');
			$m_contacts_data = D('ContactsData');
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$_POST[$v['field']] = implode(chr(10),$_POST[$v['field']]);
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
			if($m_contacts->create()){
				if($m_contacts_data->create() !== false){
					$m_contacts->update_time = time();
					if (!empty($_POST['customer_id'])) {
						if (empty($customer_id)) {
							$data['contacts_id'] = $_POST['contacts_id'];
							$data['customer_id'] = $_POST['customer_id'];
							$RContactsCustomer ->where('contacts_id = %d', $_POST['contacts_id'])->delete();
							$RContactsCustomer -> add($data);
						}elseif ($_POST['customer_id'] != $customer_id) {
							M('RContactsCustomer') -> where('contacts_id = %d' , $_POST['contacts_id']) -> setField('customer_id',$_POST['customer_id']);
						}	
					}else{
						alert('error', L('NOT NULL',array(L('CUSTOMER'))), $_SERVER['HTTP_REFERER']);
					}
					$a = $m_contacts->where('contacts_id= %d',$contacts['contacts_id'])->save();
					$contacts_field = M('Fields')->where('model = "%s" and is_main = 0','contacts')->find();
					if($contacts_field){
						$b = $m_contacts_data->where('contacts_id= %d', $contacts['contacts_id'])->save();
					}else{
						$b = 0;
					}
					if ($a !== false && $b !== false) {
						if($_POST['refer_url']){
							// 添加操作记录
							recordAction($_POST, $contacts, 'contacts', $contacts['contacts_id']);
							alert('success', '联系人信息修改成功!', $_POST['refer_url'].'#tab3');
						}else{
							alert('success',L('THE CONTACT INFORMATION OF SUCCESS'),U('contacts/view') . "&id=" . $_POST['contacts_id']);
						}
					} else {
						$this->error(L('THE CONTACT INFORMATION CHANGE FAILED'));
					}
				}else{
					alert('error', $m_contacts_data->getError());
					$this->alert = parseAlert();				
					$this->error();
				}
			}else{
				$this->error($m_contacts->getError(), U('contacts/edit')."&id=".$_POST['contacts_id']);
			}
		}else{
			$this->contacts = $contacts;
			$this->refer_url = $_SERVER['HTTP_REFERER'];
			$this->alert = parseAlert();
			$this->field_list = field_list_html("edit","contacts",$contacts);
			$this->display();
		}
	}
	
	//联系人详情
	public function view(){
		$contacts_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$RContactsCustomer = M('RContactsCustomer');
		$d_contacts = D('ContactsView');

		if (empty($contacts_id)) {
			alert('error', L('PARAMETER_ERROR'), U('contacts/index'));
		} else {
			//检查联系人date表中无关联时创建
			$contacts_data = M('ContactsData');
			$contactsid = $contacts_data->where('contacts_id = %d',$contacts_id)->find();
			if(!$contactsid){
				$data['contacts_id'] = $contacts_id;
				$contacts_data->add($data);
			}

			$customer_id = $RContactsCustomer->where('contacts_id = %d', $contacts_id)->getField('customer_id');

			//判断联系人所在客户是否在客户池，如果在则不判断权限
			$customer_info = M('Customer')->where('customer_id = %d', $customer_id)->find();
			$m_config = M('Config');
			$outdays = $m_config->where('name="customer_outdays"')->getField('value');
			$outdate = empty($outdays) ? 0 : time()-86400*$outdays;

			$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
			$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
			$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
			$openrecycle = $m_config->where('name="openrecycle"')->getField('value');

			if ($openrecycle == 2) {
				if($customer_info['owner_role_id'] != 0 && (($customer_info['update_time'] > $outdate && $customer_info['get_time'] > $contract_outdays) || $customer_info['is_locked'] == 1)){
					if(!in_array($customer_info['owner_role_id'], getPerByAction('contacts','view')) && !session('?admin')){
						$this->error(L('HAVE NOT PRIVILEGES'));
					}
				}
			}

			//查询关联商机
			$m_r_business = M('RBusinessContacts');
			$business_list = $m_r_business->where('contacts_id = %d', $contacts_id)->select();
			$m_business = M('Business');
			$m_business_status = M('BusinessStatus');
			foreach ($business_list as $k => $v) {
				$business_info = $m_business->where('business_id = %d', $v['business_id'])->field('code,status_id,status_type_id')->find();
				if ($business_info) {
					$business_list[$k]['status'] = $m_business_status->where(array('status_id'=>$business_info['status_id'],'type_id'=>$business_info['status_type_id']))->getField('name');
					$business_list[$k]['code'] = $business_info['code'];
				} else {
					unset($business_list[$k]);
				}
			}
			//自定义字段显示
			$field_list = M('Fields')->where('model = "contacts"')->order('order_id')->select();
			$contacts = $d_contacts->where('contacts.contacts_id = %d' , $contacts_id)->find();
			//日程信息 
			$m_event = M('Event');
			$m_user = M('User');
			$event_list = $m_event ->where('module ="contacts" and module_id =%d',$contacts_id)->select();
			foreach($event_list as $k=>$v){
				$user_info = $m_user ->where('role_id =%d',$v['creator_role_id'])->field('full_name,img')->find();
				$event_list[$k]['create_role_name'] = $user_info['full_name'];
				$event_list[$k]['img'] = $user_info['img'];
			}

			//联系人操作记录
			$action_record = actionRecord($contacts_id, 'contacts'); 
			$this->assign('group_list', $action_record);

			$this->event_list = $event_list;
			$this->contacts = $contacts;
			$this->field_list = $field_list;	
			$this->business_list = $business_list;	
			$this->alert = parseAlert();
			$this->display();
		}		
	}

	//联系人和商机解除关联
	public function relToBusiness(){
		$act_n = $_GET['act_n'];
		$business_id = $_GET['business_id'];
		$contacts_id = $_GET['contacts_id'];
		if (!intval($business_id) || !$contacts_id) {
			$this->ajaxReturn(array('error','参数错误！',$_SERVER['HTTP_REFERER']),'JSON');
		}
		if ($act_n == 1) {//关联商机
			$contacts_id_array = explode(',',$contacts_id);
			if (count($contacts_id_array)>1) {
				$data = array();	
				foreach ($contacts_id_array as $k => $v) {
					$data['business_id'] = $business_id;
					$data['contacts_id'] = $v;
					$is_rel = M('RBusinessContacts')->where('business_id = %d and contacts_id = %d',$business_id,$v)->find();
					if ($is_rel) {
						continue;
					}
					$ret = M('RBusinessContacts')->add($data);
					if (!$ret) {
						$this->ajaxReturn(array('error','批量绑定出错！',$_SERVER['HTTP_REFERER']),'JSON');
					}
				}
				$this->ajaxReturn(array('success','绑定成功！',$_SERVER['HTTP_REFERER']),'JSON');
			}
			$data = array();
			$data['business_id'] = $business_id;
			$data['contacts_id'] = $contacts_id;
			$is_rel = M('RBusinessContacts')->where('business_id = %d and contacts_id = %d',$business_id,$contacts_id)->find();
			if ($is_rel) {
				$this->ajaxReturn(array('error','联系人已绑定该商机！',$_SERVER['HTTP_REFERER']),'JSON');
			}
			$ret = M('RBusinessContacts')->add($data);
			if ($ret) {
				$this->ajaxReturn(array('success','绑定成功！',$_SERVER['HTTP_REFERER']),'JSON');
			} else {
				$this->ajaxReturn(array('error','绑定失败！',$_SERVER['HTTP_REFERER']),'JSON');
			}
			
		} else {//解绑关联商机
			$ret = M('RBusinessContacts')->where('business_id = %d and contacts_id = %d',$business_id,$contacts_id)->delete();
			if ($ret) {
				$this->ajaxReturn(array('success','解绑商机成功！',$_SERVER['HTTP_REFERER']),'JSON');
			} else {
				$this->ajaxReturn(array('error','解绑商机失败！',$_SERVER['HTTP_REFERER']),'JSON');
			}
		}
	}

	public function index(){
        $d_contacts = D('ContactsView');
		$m_customer = M('Customer');
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$below_ids = getPerByAction('contacts', ACTION_NAME);
		$where = array();
		$where_customer = array();
		$b_where = array();
		$c_where = array();
		$params = array();
		$order = "contacts.update_time desc,contacts.contacts_id asc";
		
		if ($_GET['desc_order']) {
			$order = 'contacts.'.trim($_GET['desc_order']).' desc,contacts.contacts_id asc';
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif ($_GET['asc_order']) {
			$order = 'contacts.'.trim($_GET['asc_order']).' asc,contacts.contacts_id asc';
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}
		switch ($by) {
			case 'today' : $where['create_time'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_time'] =  array('gt',(strtotime(date('Y-m-d', time())) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_time'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'add' : $order = 'contacts.create_time desc,contacts.contacts_id asc'; break;
			case 'update' : $order = 'contacts.update_time desc,contacts.contacts_id asc'; break;
			case 'deleted' : $where['is_deleted'] = 1; break;
			default : 
				// $where['customer.owner_role_id'] = array(array('in', $below_ids),array('EXP','IS NULL'), 'or');
				$where['_string'] = ' (customer.owner_role_id in ('.implode(',',$below_ids).'))  OR ( customer.owner_role_id is null) '; break;
		}
		if (!isset($where['customer.owner_role_id'])) {
			// $where['customer.owner_role_id'] = array('in', $below_ids);
			// $where['customer.owner_role_id'] = array(array('in', $below_ids),array('EXP','IS NULL'), 'or');
			$where['_string'] = ' (customer.owner_role_id in ('.implode(',',$below_ids).'))  OR ( customer.owner_role_id is null) ';
		}
		if (!isset($where['is_deleted'])) {
			$where['is_deleted'] = 0;
		}
		
		if ($_REQUEST["field"]) {
			$field = $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if ($this->_request('state')){
				$state = $this->_request('state', 'trim');
				$address_where[] = '%'.$state.'%';
				if($this->_request('city')){
					$city = $this->_request('city', 'trim');
					$address_where[] = '%'.$city.'%';
					if($this->_request('area')){
						$area = $this->_request('area', 'trim');
						$address_where[] = '%'.$this->_request('area', 'trim').'%';
					}
				}
				if($search){
					$address_where[] = '%'.$search.'%';
				}
				$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'state='.$this->_request('state','trim'), 'city='.$this->_request('city','trim'),'area='.$this->_request('area','trim'),'search='.$this->_request('search','trim'));
				$where[$field] = array('like', $address_where, 'AND');
			}else{ 
				$field_date = M('Fields')->where('is_main=1 and (model="" or model="contacts") and form_type="datetime"')->select();
				foreach($field_date as $v){
					if($field == $v['field'] || $field == 'customer.create_time' || $field == 'customer.update_time') $search = is_numeric($search)?$search:strtotime($search);
				}
				if($field =="customer_id"){
					//所属客户
					$b_where['name'] = array('like','%'.$where['customer_id'][1].'%');
					$c_where['is_deleted'] = 0;
					$c_where['customer_id'] = array('in',$owner_customer_ids); //过滤权限
					$customer_str = M('Customer')->where($c_where)->getField('customer_id',true);
					unset($where['customer_id']);
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
		}
		//多选类型字段
		$check_field_arr = M('Fields')->where(array('model'=>'contacts','form_type'=>'box','setting'=>array('like','%'."'type'=>'checkbox'".'%')))->getField('field',true);
		//高级搜索
		if(!$_GET['field']){
			foreach($_GET as $k=>$v){
				if($k != 'act' && $k != 'content' && $k != 'p' && $k !='condition' && $k != 'listrows' && $k != 'asc_order' && $k != 'desc_order'){
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
								$k = 'contacts.create_time';
							}elseif($k == 'update_time'){
								$k = 'contacts.update_time';
							}
							//时间段查询
							if ($v['start'] && $v['end']) {
								$where[$k] = array('between',array(strtotime($v['start']),strtotime($v['end'])+86399));
							} elseif ($v['start']) {
								$where[$k] = array('egt',strtotime($v['start']));
							} else {
								$where[$k] = array('elt',strtotime($v['end'])+86399);
							}
						}elseif(($v['value']) != ''){
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
				if(is_array($v)){
					foreach ($v as $key => $value) {
						$params[] = $k.'['.$key.']='.$value;
					}
				}else{
					$params[] = $k.'='.$v;
				}
			}	
			//所属客户
			if(isset($where['customer_id'])){
				$c_where['name'] = array('like','%'.$where['customer_id'][1].'%');
				$c_where['is_deleted'] = 0;
				$c_where['owner_role_id'] = array('in',$below_ids); //过滤权限
				$customer_str = M('Customer')->where($c_where)->getField('customer_id',true);
				unset($where['customer_id']);
			}
		}
		//高级搜索字段
		$fields_list_data = M('Fields')->where(array('model'=>array('in',array('','contacts')),'is_main'=>1))->field('field,form_type')->select();
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
				if(strpos($v,'[condition]=')){
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
				}else{
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
		$this->fields_search = $fields_search;

		//所属客户
		if ($customer_str) {
			$where['r_contacts_customer.customer_id'] = array('in',$customer_str);
		}
		if(trim($_GET['act']) == 'excel'){
			$dc_id = $_GET['daochu'];
			if($dc_id !=''){
				$where['contacts_id'] = array('in',$dc_id);
			}
			$current_page = intval($_GET['current_page']);
			$export_limit = intval($_GET['export_limit']);
			unset($where['current_page']);
			unset($where['export_limit']);
			unset($where['daochu']);
			$limit = ($export_limit*($current_page-1)).','.$export_limit;
			if(session('?admin')){
				$contactsList = $d_contacts->order('contacts_id desc')->where($where)->limit($limit)->select();
				session('export_status', 1);
				$this->excel_export($contactsList);
			}else{
				if(checkPerByAction('contacts','excel_export')){
					$contactsList = $d_contacts->order('contacts_id desc')->where($where)->limit($limit)->select();
					session('export_status', 1);
					$this->excel_export($contactsList);
				}else{
					alert('error',  L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
				}
			}
		}else{
			if($_GET['listrows']){
				$listrows = intval($_GET['listrows']);
				cookie('listrows', $listrows, 3600 * 24 * 30);
				$params[] = "listrows=" . intval($_GET['listrows']);
			}else{
				$listrows = cookie('listrows') ? cookie('listrows') : 15;
				$params[] = "listrows=".$listrows;
			}
			if (!empty($_GET['by'])) {
				$params[] = "by=".trim($_GET['by']);
			}
			$count = $d_contacts->where($where)->count();
			$p_num = ceil($count/$listrows);
			if ($p_num < $p) {
				$p = $p_num;
			}
			$contacts_list = $d_contacts->where($where)->order($order)->page($p.','.$listrows)->select();

	// println($where);
			foreach ($contacts_list as $k => $v) {		
				$contacts_list[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
				$contacts_list[$k]["name"] = $v['name'] ? trim($v['name']) : '查看详情';
			}
			import("@.ORG.Page");
			$Page = new Page($count,$listrows);	
			$Page->parameter = implode('&', $params);
			$this->assign('page',$Page->show());
			$this->assign("count",$count);
			$this->listrows = $listrows;
			$this->assign('contactsList',$contacts_list);
			$this->field_array = getIndexFields('contacts');
			$this->field_list = getMainFields('contacts');
			$this->alert = parseAlert();
			$this->display();
		}
	}

	//弹出框列表
	public function listDialog(){
		$d_contacts = D('ContactsView');
		$m_customer = M('Customer');
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$where = array();
		$params = array();
		$order = "contacts.update_time desc,contacts.contacts_id asc";
		if (!isset($where['is_deleted'])) {
			$where['is_deleted'] = 0;
		}
		
		if ($_REQUEST["field"]) {
			$field = $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			$field_date = M('Fields')->where('is_main=1 and (model="" or model="contacts") and form_type="datetime"')->select();
			foreach($field_date as $v){
				if($field == $v['field'] || $field == 'customer.create_time' || $field == 'customer.update_time') $search = is_numeric($search)?$search:strtotime($search);
			}
			if($field =="customer_id"){
				$c_where['name'] = array('like','%'.$search.'%');
				$customer_ids = $m_customer->where($c_where)->getField('customer_id',true);
				$where[$field] = array('in',$customer_ids);
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

		//权限控制(根据联系人列表权限)
		$below_ids = getPerByAction('contacts','index');
		$where_customer['is_deleted'] = 0;
		$where_customer['owner_role_id'] = array('in', $below_ids);
		//权限范围内的customer_id
		$owner_customer_ids = $m_customer->where($where_customer)->getField('customer_id',true);
		$customer_str = $owner_customer_ids;
		$where['r_contacts_customer.customer_id'] = array('in',$customer_str);

		import("@.ORG.DialogListPage");
		$contactsList = $d_contacts->where($where)->order($order)->page($p.',10')->select();
		foreach ($contactsList as $k => $v) {		
			$contactsList[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
			$contactsList[$k]["customer_name"] = $m_customer ->where('customer_id =%d',$v['customer_id'])->getField('name');
		}
		$count = $d_contacts->where($where)->count();
		$this->search_field = $_REQUEST;//搜索信息
		$Page = new Page($count,10);
		$Page->parameter = implode('&', $params);
		$this->assign('page',$Page->show());
		$this->assign('contactsList',$contactsList);
		$this->display();
	}
	
	public function delete(){
		$m_contacts = M('contacts');
		$RContactsCustomer = M('RContactsCustomer');

		$m_config = M('Config');
		$outdays = $m_config->where('name="customer_outdays"')->getField('value');
		$outdate = empty($outdays) ? 0 : time()-86400*$outdays;

		$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
		$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
		$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
		$openrecycle = $m_config->where('name="openrecycle"')->getField('value');
		
		if ($_POST['contacts_id']) {
			if (!session('?admin')) {
				foreach ($_POST['contacts_id'] as $value) {
					//检查权限
					$customer_id = $RContactsCustomer->where('contacts_id = %d', $value)->getField('customer_id');
					//判断联系人所在客户是否在客户池，如果在则不判断权限
					$customer_info = M('Customer')->where(array('customer_id'=>$customer_id))->find();
					if ($openrecycle == 2 && ($customer_info['owner_role_id'] != 0 && (($customer_info['update_time'] > $outdate && $customer_info['get_time'] > $contract_outdays) || $customer_info['is_locked'] == 1))) {
						if (!in_array($customer_info['owner_role_id'], $this->_permissionRes) && !session('?admin')) {
							$this->ajaxReturn('','您没有此权利！',0);
						}
					}
				}
			}
			if ($m_contacts->where('contacts_id in (%s)', implode(',', $_POST['contacts_id']))->delete()) {
				$this->ajaxReturn('',L('DELETED SUCCESSFULLY'),1);
			} else {
				$this->ajaxReturn('',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),0);
			}
		}elseif($_GET['id']){
			$contacts_id = intval($_GET['id']);
			$contacts = $m_contacts->where('contacts_id = %d', $contacts_id)->find();
			if (!$contacts) {
				$this->ajaxReturn('',L('YOU WANT TO DELETE THE RECORD DOES NOT EXIST'),0);
			}
			//检查权限
			$customer_id = $RContactsCustomer->where('contacts_id = %d', $contacts_id)->getField('customer_id');
			//判断联系人所在客户是否在客户池，如果在则不判断权限
			$customer_info = M('Customer')->where(array('customer_id'=>$customer_id))->find();
			if ($openrecycle == 2) {
				if ($customer_info['owner_role_id'] != 0 && (($customer_info['update_time'] > $outdate && $customer_info['get_time'] > $contract_outdays) || $customer_info['is_locked'] == 1)) {
					if (!in_array($customer_info['owner_role_id'], $this->_permissionRes) && !session('?admin')) {
						$this->ajaxReturn('','您没有此权利！',0);
					}
				}
			}
			if($m_contacts->where('contacts_id = %d', $contacts_id)->delete()){
				$this->ajaxReturn('',L('DELETED SUCCESSFULLY'),1);
			} else {
				$this->ajaxReturn('',L('DELETE FAILED'),0);
			}
		}else{
			$this->ajaxReturn('',L('PLEASE CHOOSE TO DELETE THE CONTACT'),0);
		}
	}

	//当联系人为首要联系人时调用的删除方法
	public function mDelete(){
		$contacts_id = intval($_GET['id']);
		$module_id = intval($this->_get('module_id'));
		$m_customer = M('Customer');
		$m_contacts = M('Contacts');
		$RContactsCustomer = M('RContactsCustomer');

		//检查权限
		$customer_id = $RContactsCustomer->where('contacts_id = %d', $contacts_id)->getField('customer_id');
		
		//判断联系人所在客户是否在客户池，如果在则不判断权限
		$customer_info = M('Customer')->where(array('customer_id'=>$customer_id))->find();
		$m_config = M('Config');
		$outdays = $m_config->where('name="customer_outdays"')->getField('value');
		$outdate = empty($outdays) ? 0 : time()-86400*$outdays;

		$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
		$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
		$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;
		$openrecycle = $m_config->where('name="openrecycle"')->getField('value');
		if ($openrecycle == 2) {
			if ($customer_info['owner_role_id'] != 0 && (($customer_info['update_time'] > $outdate && $customer_info['get_time'] > $contract_outdays) || $customer_info['is_locked'] == 1)) {
				if (!in_array($customer_info['owner_role_id'], $this->_permissionRes)) {
					$this->ajaxReturn('','您没有此权利！',-2);
				}
			}
		}
		
		if($m_customer->where("customer_id = %d and contacts_id = %d", $module_id, $contacts_id)->setField('contacts_id', 0)){
			if($m_contacts->where('contacts_id = %d', $contacts_id)->delete()){
				$this->ajaxReturn('',L('DELETED SUCCESSFULLY'),1);
			} else {
				$this->ajaxReturn('',L('DELETE FAILED'),0);
			}
		}else{
			$this->ajaxReturn('',L('DELETE FAILED'),0);
		}
	}

	//商机详情下关联联系人
	public function checkListDialog(){
		if(empty($_GET['id']) || empty($_GET['business_id'])){
			echo '<div class="alert alert-error">参数错误！</div>';die();
		}
		$customer_id = intval($_GET['id']);
		//权限控制(根据联系人列表权限)
		$below_ids = getPerByAction('contacts','index');
		$where_customer = array();
		$where_customer['is_deleted'] = 0;
		$where_customer['owner_role_id'] = array('in', $below_ids);
		//权限范围内的customer_id
		$owner_customer_ids = M('Customer')->where($where_customer)->getField('customer_id',true);
		if(in_array($customer_id,$owner_customer_ids)){
			$contacts_ids = M('RContactsCustomer') ->where('customer_id = %d', $customer_id)->getField('contacts_id', true);
		}else{
			$contacts_ids = array();
		}
		
		$contacts_ids[] = '-1';
		$m_contacts = M('Contacts');
		$where = array();
		$business_id = intval($_GET['business_id']);

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
		$params[] = 'id='.$customer_id;
		$params[] = 'business_id='.$business_id;
		//过滤已关联的联系人
		$relation_contacts_ids = M('RBusinessContacts')->where('business_id = %d',$business_id)->getField('contacts_id',true);
		//数组差集
		if($relation_contacts_ids){
			$diff_contacts_ids = array_diff($contacts_ids, $relation_contacts_ids);
		}else{
			$diff_contacts_ids = $contacts_ids;
		}
		$where['contacts_id'] = array('in',$diff_contacts_ids);
		$where['is_deleted'] = array('neq',1);

		import("@.ORG.DialogListPage");
		$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
		$contactsList = $m_contacts->where($where)->order('create_time desc')->page($p.',10')->select();
		$count = $m_contacts->where($where)->count();
		$this->contactsList = $contactsList;
		$this->search_field = $_REQUEST;//搜索信息

		$Page = new Page($count,10);
		$Page->parameter = implode('&', $params);
		$this->assign('page',$Page->show());

		$this->customer_id = $customer_id;
		$this->business_id = $business_id;
		$this->display();
	}
	
	//设为首要联系人
	public function changeToFirstContact(){
		$id = $_GET['id'];
		$customer_id = $_GET['customer_id'];
		if(isset($id) && isset($customer_id)){
			$m_customer = M('Customer');
			$data['contacts_id'] = $id;
			if($m_customer->where('customer_id = %d',$customer_id)->save($data)){
				alert('success', L('SET THE FIRST CONTACT SUCCESS') ,$_SERVER['HTTP_REFERER'].'#tab3');
			}else{
				alert('error', L('NO CHANGE INFORMATION') ,$_SERVER['HTTP_REFERER'].'#tab3');
			}
		}else{
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	//弹出框
	public function radioListDialog(){
		$customer_id = $_GET['id'] ? intval($_GET['id']) : '';
		$rcc =  M('RContactsCustomer');
		$m_contacts = D('ContactsTempView');
		$m_customer = M('Customer');
		$where = array();
		$where['customer.owner_role_id'] = array('in', getPerByAction('customer','index'));
		$where['is_deleted'] = 0;
		if($customer_id){
			$contacts_id = $rcc->where('customer_id = %d', $customer_id)->getField('contacts_id', true);
			$where['contacts_id'] = array('in', implode(',', $contacts_id));
			$this->customer_id = $customer_id;
		}
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']);
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);

			if ('create_time' == $field || 'update_time' == $field) {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			if($field =="customer_id"){
				$c_where['name'] = array('like','%'.$search.'%');
				$customer_ids = M('Customer')->where($c_where)->getField('customer_id',true);
				$contacts_ids = M('RContactsCustomer')->where(array('customer_id'=>array('in',$contacts_ids)))->getField('contacts_id',true);
				$where['contacts_id'] = array('in',$contacts_ids);
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
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
		import("@.ORG.DialogListPage");

		$list = $m_contacts->where($where)->order('update_time desc')->page($p.',10')->select();
		$count = $m_contacts->where($where)->order('update_time desc')->count();
		foreach ($list as $k=>$value) {
			$customer_id = '';
			$customer_id = $rcc->where('contacts_id = %d', $value['contacts_id'])->getField('customer_id');
			$list[$k]['customer'] = $m_customer->where('customer_id = %d', $customer_id)->field('name,customer_id')->find();
		}
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$this->count_num = $count;
		//获取下级和自己的岗位列表,搜索用
		$below_ids = getSubRoleId(false);
		$d_role_view = D('RoleView');
		$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
		$this->contactsList = $list;

		$this->search_field = $_REQUEST;//搜索信息
		$Page = new Page($count,10);
		$Page->parameter = implode('&', $params);
		$this->assign('page',$Page->show());

		$this->field_array = getIndexFields('contacts');
		$this->display();
	}
	
	//联系人二维码
	public function qrcode(){
		$contacts_id = intval($_GET['contacts_id']);
		//判断权限
		$r_contacts_customer = M('RContactsCustomer');
		$below_ids = getPerByAction('contacts','view');
		$customer_idArr = M('Customer')->where(array('owner_role_id'=>array('in', $below_ids)))->getField('customer_id', true);
		$customer_id = $r_contacts_customer->where('contacts_id = %d', $contacts_id)->getField('customer_id');
		
		//判断联系人所在客户是否在客户池，如果在则不判断权限
		//查询客户数据
		$customer = D('CustomerView')->where('customer.customer_id = %d', $customer_id)->find();
		$outdays = M('Config') -> where('name="customer_outdays"')->getField('value');
		$outdate = empty($outdays) ? 0 : time()-86400*$outdays;

		if($customer['owner_role_id'] != 0 && ($customer['update_time'] > $outdate || $customer['is_locked'] == 1) && !in_array($customer_id, $customer_idArr)){
			echo 3;$this->error('您没有此权利！');
		}
		if($contacts = M('Contacts')->where('contacts_id = %d', $contacts_id)->find()){
			$customer_id = M('RContactsCustomer')->where('contacts_id = %d',$contacts_id)->getField('customer_id');
			$contacts['customer'] = M('Customer')->where('customer_id = %d', $customer_id)->getField('name');
			$qrOpt = '';
			$qrOpt = "BEGIN:VCARD\nVERSION:3.0\n";
			$qrOpt .= $contacts['name'] ? ("N:".$contacts['name']."\n") : "";
			$qrOpt .= $contacts['telephone'] ? ("TEL:".$contacts['telephone']."\n") : "";
			$qrOpt .= $contacts['email'] ? ("EMAIL;PREF;INTERNET:".$contacts['email']."\n") : "";
			$qrOpt .= $contacts['customer'] ? ("ORG:".$contacts['customer']."\n") : "";	
			$qrOpt .= $contacts['post'] ? ("TITLE:".$contacts['post']."\n") : "";
			$qrOpt .= $contacts['address'] ? ("ADR;WORK;POSTAL:".$contacts['address']."\n") : "";
			$qrOpt .= "END:VCARD";
			
			$png_temp_dir = UPLOAD_PATH.'/qrpng/';
			$filename = $png_temp_dir.$contacts['contacts_id'].'.png';
			if (!is_dir($png_temp_dir) && !mkdir($png_temp_dir, 0777, true)) { echo 3;$this->error('二维码保存目录不可写'); }

			import("@.ORG.QRCode.qrlib");
			QRcode::png($qrOpt, $filename, 'M', 4, 2);
			header('Content-type: image/png');	
			header("Content-Disposition: attachment; filename=".$contacts['contacts_id'].'.png');
			echo file_get_contents($filename);
			unlink($filename);
		}
	}

	/**
	 * 导出状态
	 */
	public function getcurrentstatus()
	{
		$this->ajaxReturn(intval(session('export_status')), 'success', 1);
	}

	/**
	 *	联系人导出
	 */
	public function excel_export($contactsList){
		if (empty($contactsList)) {
			session('export_status', 0);
			alert('error', '数据为空，请变更查询条件。', $_SERVER['HTTP_REFERER']);
		}
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();
		$objProps = $objPHPExcel->getProperties();
		$objProps->setCreator("pdcrm");
		$objProps->setLastModifiedBy("pdcrm");
		$objProps->setTitle("pdcrm Contacts");
		$objProps->setSubject("pdcrm Contacts Data");
		$objProps->setDescription("pdcrm Contacts Data");
		$objProps->setKeywords("pdcrm Contacts Data");
		$objProps->setCategory("pdcrm");
		$objPHPExcel->setActiveSheetIndex(0);
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('Sheet1');

		$field_list = M('Fields')->field('form_type,name,field')->where(array('model'=>'contacts','field'=>array('not in',array('customer_owner_id'))))->order('order_id asc,field_id asc')->select();
		$col = 0;
		foreach($field_list as $key => $field){
			if ($field['field'] == 'customer_id') {
				$field_list[$key]['field'] = 'customer_name';
			}
			if($field['form_type'] == 'address'){
				for($a=0;$a<=4;$a++){
					$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col); //生成Excel
					$address = array('所在省','所在市','所在县/区','街道信息');
					$objActSheet->setCellValue($pCoordinate.'2', $address[$a]);
					$col++;
				}
				$col--;
			}else{
				$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col); //生成Excel
				$objActSheet->setCellValue($pCoordinate.'2', $field['name']);
				$col++;
			}
		}
		$row = 3;
		foreach ($contactsList as $key => $val) {
			$col = 0;
			foreach ($field_list as $field) {
				if ($field['form_type'] == 'address') {
					$address = explode("\n", $val[$field['field']]);
					for ($i=0; $i < 4; $i++) { 
						$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col); //生成Excel
						$objActSheet->setCellValue($pCoordinate.$row, $address[$i]);
						$col++;
					}
				} elseif (is_numeric($val[$field['field']]) && in_array($field['form_type'], array('number', 'floatnumber', 'form_type', 'phone', 'mobile', 'text'))) {
					$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col); //生成Excel
					$objActSheet->setCellValue($pCoordinate.$row, ' '.$val[$field['field']]);
					$col++;
				} else {
					$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col); //生成Excel
					$objActSheet->setCellValue($pCoordinate.$row, $val[$field['field']]);
					$col++;
				}
			}
			$row++;
		}
			
		//设置边框样式
		$color='00000000';
		$styleArray = array(  
			'borders' => array(  
				'allborders' => array(  
					'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框  
					'color' => array('argb' => $color),  
				),  
			),  
		);
		$col = PHPExcel_Cell::stringFromColumnIndex($col - 1);
		$row -= 1;
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$col.'1');		// 合并单元格
		$objActSheet->setCellValue('A1', '客户联系人导出数据');
		$objActSheet->getStyle('A1:'.$col.$row)->applyFromArray($styleArray);
		$objActSheet->getStyle('A1:'.$col.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平居中
		$objActSheet->getStyle('A1:'.$col.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
		$objActSheet->getRowDimension(1)->setRowHeight(28); //设置行高
		$objActSheet->getRowDimension(2)->setRowHeight(28); //设置行高

		$current_page = intval($_GET['current_page']);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		header("Content-Type: application/vnd.ms-excel;");
		header("Content-Disposition:attachment;filename=pdcrm_contacts_".date('Y-m-d').'_'.$current_page.".xls");
		header("Pragma:no-cache");
		header("Expires:0");
		$objWriter->save('php://output');
		session('export_status', 0);
		exit;
	}

	/**
	 *	导入excel - excel 文件上传
	 */
	public function excel_import(){
		if (IS_POST) {
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
					$this->ajaxReturn(array('data' => $savepath, 'msg' => 'success', 'status' => 1));
				}else{
					$this->ajaxReturn(array('data' => 0, 'msg' => 'error', 'status' => 0));
				}
			}else{
				alert('error', L('UPLOAD_FAILED'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->display('excelimport');
		}
	}

	/**
	 *	导入excel - 写入数据库
	 */
	public function excel_import_act(){
		if (!checkPerByAction('contacts','excel_import')) {
			$this->ajaxReturn('','您没有此权利！',0);
		}
		$d_contacts = D('Contacts');
		$d_contacts_data = D('ContactsData');
		$r_contacts_customer = M('r_contacts_customer');
		import("ORG.PHPExcel.PHPExcel");
		$PHPExcel = new PHPExcel();
		$PHPReader = new PHPExcel_Reader_Excel2007();
		$savePath = $_GET['path'];
		if(!$PHPReader->canRead($savePath)){
			$PHPReader = new PHPExcel_Reader_Excel5();
		}
		$PHPExcel = $PHPReader->load($savePath);
		$currentSheet = $PHPExcel->getSheet(0);
		$allRow = $currentSheet->getHighestRow();
		$row_size = 99; // 每次导入数据条数
		$currentRow = intval($_GET['num']);	// 开始行数
		if($currentRow + $row_size <= $allRow){
			$rows_excel = $currentRow + $row_size;	// 截至行数
		}else{
			$rows_excel = $allRow;			// 截至行数
		}
		$field = M('fields')->where('model="%s"', 'contacts')->field('field, form_type, is_main')->order('order_id asc,field_id asc')->select();
		// 信息 错误数据
		$message = $error_data = array();
		for ($currentRow; $currentRow <= $rows_excel; $currentRow++) {
			// 检测空行
			$check_row_empty = $check_last_row = 0;
			$next_row = $currentRow + 1;
			// 10无实际意义  表示10列，可以随便定义，建议大点。
			for ($i = 0; $i< count($field) + 10; $i++) {
				$col = PHPExcel_Cell::stringFromColumnIndex($i);
				$tmp_val = $currentSheet->getCell($col.$currentRow)->getValue();
				$tmp_val_2 = $currentSheet->getCell($col.$next_row)->getValue();
				$tmp_val = trim($tmp_val);
				$tmp_val_2 = trim($tmp_val_2);
				if ($tmp_val === null) {
					$check_row_empty++;
					if ($tmp_val_2 === null) {
						$check_last_row++;
					}
				}
			}
			if ($check_row_empty == count($field) + 10) {
				if ($currentRow != $allRow) {
					if ($check_last_row == count($field) + 10) {
						$allRow--;
						break;
					}
					$error_message = '第'.$currentRow.'行为空，请删除该行或完善数据。';
				} else {
					$allRow--;
					break;
				}
			} else {
				$error_flag = 0;
				$error_message = '';
				$data_fu = $data = array();
				$data['creator_role_id'] = session('role_id');
				$data['create_time'] = time();
				$data['update_time'] = time();
				$col =  0;
				foreach ($field as $key => $val) {
					// 地址
					if ($val['form_type'] == 'address') {
						$excel_val = '';
						for ($i=0; $i < 4; $i++) {
							$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col);
							$excel_val .= (String) $currentSheet->getCell($pCoordinate.$currentRow)->getValue()."\n";
							$col++;
						}
						if ($val['is_main']) {
							$data[$val['field']] = trim($excel_val);
						} else {
							$data_fu[$val['field']] = trim($excel_val);
						}
					} elseif ($val['form_type'] == 'customer') {
						$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col);
						$excel_val = (String)$currentSheet->getCell($pCoordinate.$currentRow)->getValue();
						if (!$excel_val) {
							$error_message = '联系人所属客户信息不能为空！';
							$error_flag = 1;
							break;
						}
						$customer_count = M('customer')->where('name="%s"', trim($excel_val))->count();
						if ($customer_count > 1) {
							$error_message = '联系人所属客户信息不正确，所属客户姓名重复！';
							$error_flag = 1;
							break;
						} else if ($customer_count == 0) {
							$error_message = '联系人所属客户信息不正确，找不到所属客户！';
							$error_flag = 1;
							break;
						}	
						$customer_id = M('customer')->where('name="%s"', trim($excel_val))->getField('customer_id');	// 所属客户ID
						$data['customer_id'] = $customer_id;
						$col++;
					} elseif (in_array($val['form_type'], array('number', 'floatnumber', 'form_type', 'phone', 'mobile', 'text'))) {
						$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col);
						$excel_val = (String)$currentSheet->getCell($pCoordinate.$currentRow)->getValue();
						if ($val['is_main']) {
							$data[$val['field']] = trim($excel_val);
						} else {
							$data_fu[$val['field']] = trim($excel_val);
						}
						$col++;
					} else {
						$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col);
						$excel_val = (String)$currentSheet->getCell($pCoordinate.$currentRow)->getValue();
						if ($val['is_main']) {
							$data[$val['field']] = trim($excel_val);
						} else {
							$data_fu[$val['field']] = trim($excel_val);
						}
						$col++;
					}
				}
			}
			if (empty($error_message)) {
				if ($d_contacts->create($data)) {
					$contacts_id = $d_contacts->add();
					$data_fu['contacts_id'] = $contacts_id;
					if ($d_contacts_data->create($data_fu) !== false) {
						$d_contacts_data->add($data_fu);
						$r_data = array('customer_id' => $customer_id, 'contacts_id' => $contacts_id);
						$r_contacts_customer->add($r_data);
					}
				} else {			
					$error_message = L('LINE ERROR',array($currentRow, $d_contacts->getError().$d_contacts_data->getError()));
					$d_contacts->clearError();
					$d_contacts_data->clearError();
					$error_flag = 1;
				}
			}
			$temp['error_message'] = $error_message;
			$temp['no'] = $currentRow;
			$message[] = $temp;

			//出现错误时候停止
			if ($error_flag == 1) {
				$error_data[$currentRow] = $error_message;
				if (intval($_GET['is_jump']) == 2) {
					break;
				}
			}
		}
		$error_data['error_data'] = json_encode($error_data);
		$error_data['excel'] = $savePath;
		M('import_error_data')->add($error_data);
		$return['excel'] = $savePath;
		$return['allrow'] = $allRow;
		$return['message'] = $message;
		if($return){
			$this->ajaxReturn($return,'success',1);
		}else{
			$this->ajaxReturn('','error',0);
		}
	}


	/**
	 * 导入错误数据下载
	 * 
	 */
	public function error_data_download()
	{
		$excel = $_POST['excel'];
		
		$field = M('fields')->where('model="%s"', 'contacts')->getField('form_type', true);
		$field_count = 0;
		foreach ($field as $val) {
			if ($val == 'address') {
				$field_count += 4;
			} else {
				$field_count++;
			}
		}
		import("ORG.PHPExcel.PHPExcel");
		$PHPExcel_old = new PHPExcel();
		$PHPReader = new PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($excel)){
			$PHPReader = new PHPExcel_Reader_Excel5();
		}
		$PHPExcel_old = $PHPReader->load($excel);
		$sheet_old = $PHPExcel_old->getSheet(0);
		$objProps = $PHPExcel_old->getProperties();

		$PHPExcel_new = new PHPExcel();
		$PHPExcel_new->setActiveSheetIndex(0);
		$sheet_new = $PHPExcel_new->getActiveSheet();
		

		// 错误数据
		$error_data = M('import_error_data')->where('excel="%s"', $excel)->select();
		$row = 3;
		foreach ($error_data as $key => $val) {
			$tmp_arr = json_decode($val['error_data'], 1);
			foreach ($tmp_arr as $k => $v) {
				for ($i = 0; $i <= $field_count; $i++) {
					$col = PHPExcel_Cell::stringFromColumnIndex($i);
					$tmp_val = $sheet_old->getCell($col.$k)->getValue();
					$sheet_new->setCellValue($col.$row, $tmp_val);
					if ($i == $field_count) $sheet_new->setCellValue($col.$row, $v);
				}
				$row++;
			}
		}

		$A1 = $sheet_old->getCell('A1')->getValue();
		$sheet_new->setCellValue('A1', $A1);
		for ($i = 0; $i <= $field_count; $i++) {
			$col = PHPExcel_Cell::stringFromColumnIndex($i);
			$sheet_new->getColumnDimension($col)->setWidth(12);			// 列宽
			$tmp_val = $sheet_old->getCell($col.'2')->getValue();
			$sheet_new->setCellValue($col.'2', $tmp_val);
			if ($i == $field_count) {
				$sheet_new->setCellValue($col.'2', '错误原因');
				$sheet_new->getColumnDimension($col)->setWidth(40);			// 列宽
			}
		}


		// 样式
		$borderStyle = array(  
			'borders' => array(  
				'allborders' => array(  
					'style' => PHPExcel_Style_Border::BORDER_THIN, 		// 细边框  
					'color' => array('argb' => '00000000'),  
				),  
			),  
		);
		$row--;
		$sheet_new->getStyle('A1:'.$col.$row)->applyFromArray($borderStyle);

		$sheet_new->mergeCells('A1:'.$col.'1');		// 合并
		$sheet_new->getRowDimension(1)->setRowHeight(32);		// 行高
		$sheet_new->getRowDimension(2)->setRowHeight(32);
		$sheet_new->getStyle('A1:'.$col.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sheet_new->getStyle('A1:'.$col.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
		

		$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel_new, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
        header("Content-Disposition:attachment;filename=pdcrm_导入失败错误数据.xls");
        header("Pragma:no-cache");
        header("Expires:0");
		$objWriter->save('php://output');
		exit();
	}

	/**
	 *	导入模板下载
	 */
	public function excel_model_download()
	{
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();
		$objProps = $objPHPExcel->getProperties();
		$objProps->setCreator("pdcrm");
		$objProps->setLastModifiedBy("pdcrm");
		$objProps->setTitle("pdcrm Contacts");
		$objProps->setSubject("pdcrm Contacts Data");
		$objProps->setDescription("pdcrm Contacts Data");
		$objProps->setKeywords("pdcrm Contacts Data");
		$objProps->setCategory("pdcrm");
		$objPHPExcel->setActiveSheetIndex(0);
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('Sheet1');

		$objActSheet->setCellValue('A1', "导入须知：1.保留前两行数据 2.红色带*字段为必填项");
		$field_list = M('Fields')->field('form_type,name,is_null,is_validate,setting')->where(array('model'=>'contacts','field'=>array('not in',array('customer_owner_id'))))->order('order_id')->select();
		$col = 0;	// 列数
		$is_null_arr = array();		// 必填字段
		foreach ($field_list as $key => $val) {
			$is_null = ($val['is_null'] == 1 && $val['is_validate'] == 1) ? '*' : '';
			if ($val['form_type'] == 'address') {
				$address = array('所在省', '所在市', '所在县/区', '街道信息');
				for ($i=0; $i < 4; $i++) { 
					$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col);
					$objActSheet->setCellValue($pCoordinate.'2', $is_null.$address[$i]);
					if ($is_null) $is_null_arr[] = $col;
					$col++;
				}
			} elseif ($val['form_type'] == 'box') {
				$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col);
				$objActSheet->setCellValue($pCoordinate.'2', $is_null.$val['name']);
				if ($is_null) $is_null_arr[] = $col;
				eval('$setting='.$val['setting'].';');
				$select_value = implode(',',$setting['data']);
				//数据有效性   start
				$objValidation = $objActSheet->getCell($pCoordinate.'3')->getDataValidation();
				$objValidation -> setType(PHPExcel_Cell_DataValidation::TYPE_LIST)  
					-> setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)  
					-> setAllowBlank(false)  
					-> setShowInputMessage(true)  
					-> setShowErrorMessage(true)  
					-> setShowDropDown(true)  
					-> setErrorTitle('输入的值有误')  
					-> setError('您输入的值不在下拉框列表内.')  
					-> setPromptTitle('--请选择--')  
					-> setFormula1('"'.$select_value.'"');
				// 数据有效性  end
				$col++;
			} else {
				if ($val['form_type'] == 'customer') {
					$is_null = '*';
				}
				$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($col);
				$objActSheet->setCellValue($pCoordinate.'2', $is_null.$val['name']);
				if ($is_null) $is_null_arr[] = $col;
				$col++;
			}
		}
		$style_is_null = array(
			'font' => array(
				'color' => array('argb' => '#0ff0000')
			)
		);
		$styleArray = array(  
			'borders' => array(  
				'allborders' => array(  
					'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框  
					'color' => array('argb' => '00000000'),  
				),  
			),  
		);
		foreach ($is_null_arr as $key => $val) {
			$pCoordinate = PHPExcel_Cell::stringFromColumnIndex($val);
			$objActSheet->getStyle($pCoordinate.'2')->applyFromArray($style_is_null);
		}
		$col = PHPExcel_Cell::stringFromColumnIndex($col - 1);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$col.'1');
		$objActSheet->getStyle('A1:'.$col.'2')->applyFromArray($styleArray);
		$objActSheet->getStyle('A1:'.$col.'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平居中
		$objActSheet->getStyle('A1:'.$col.'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
		$objActSheet->getRowDimension(1)->setRowHeight(30); //设置行高
		$objActSheet->getRowDimension(2)->setRowHeight(28); //设置行高

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		header("Content-Type: application/vnd.ms-excel;");
		header("Content-Disposition:attachment;filename=pdcrm_contacts_".date('Y-m-d').'_'.$current_page.".xls");
		header("Pragma:no-cache");
		header("Expires:0");
		$objWriter->save('php://output');
	}
}
