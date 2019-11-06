<?php 
class ExamineAction extends Action {

	/**
	*  author ZengZhiQiang
	*  用于判断权限
	*  @permission 无限制
	*  @allow 登录用户可访问
	*  @other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('getcurrentstatus','travel_business','travel_two','checktype','check_list','getanalycurrentstatus','getstatuslist')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
	}

	/**
	*审批列表
	**/
	public function travel_business(){
		$this->now_rows = intval($_POST['now_rows']);
		$this->display();
	}
	public function travel_two(){
		$this->now_rows = intval($_POST['now_rows']);
		$this->display();
	}
	public function index(){
		$d_examine = D('ExamineView');
		$m_examine = M('Examine');
		$m_examine_checke = M('ExamineCheck');
		$where = array();
		$params = array();
		$order = "update_time desc,examine_id asc";
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc,examine_id asc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc,examine_id asc';
		}
		$below_ids = $this->_permissionRes;
		$module = isset($_GET['module']) ? trim($_GET['module']) : '';
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		switch ($by) {
			case 'today' : $where['create_time'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_time'] =  array('gt',(strtotime(date('Y-m-d', time())) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_time'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'add' : $order = 'create_time desc,examine_id asc';  break;
			case 'update' : $order = 'update_time desc,examine_id asc';  break;
			case 'deleted' : $where['is_deleted'] = 1; break;
			case 'create' : $where['creator_role_id'] = session('role_id'); break;
			case 'subcreate' : $where['creator_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'not_examine' : $where['examine_status'] = 0; break;
			case 'examining' : $where['examine_status'] = array('in',array(0,1)); break;
			case 'examined' : $where['examine_status'] = array('in',array(2,3)); break;
			case 'me_examine' : $where['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or'); break;
			default : 
				if(!session('?admin')){	//非管理员权限限制
					$c_where['creator_role_id'] = array('in',implode(',', $below_ids)); 
					$c_where['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or');
					$c_where['_logic']='or';
					$where['_complex']=$c_where;
				}break;
		}
	
		if (!isset($where['creator_role_id'])) {
			if(!session('?admin')){	//非管理员权限限制
				$c_where['creator_role_id'] = array('in',implode(',', $below_ids)); 
				$c_where['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or');
				$c_where['_logic']='or';
				$where['_complex']=$c_where;
			}
		}
		if (!isset($where['is_deleted'])) {
			$where['is_deleted'] = 0;
		}
		$type = '';
		$examine_status = '';
		//多选类型字段
		$check_field_arr = M('Fields')->where(array('model'=>'examine','form_type'=>'box','setting'=>array('like','%'."'type'=>'checkbox'".'%')))->getField('field',true);
		//高级搜索
		$fields_search = array();
		if(!$_GET['field']){
			if(empty($_GET['type'])){
        		unset($_GET['type']);
        	}
        	if(empty($_GET['examine_status'])){
        		unset($_GET['examine_status']);
        	}
        	$no_field_array = array('act','content','p','condition','listrows','daochu','this_page','current_page','export_limit','desc_order','asc_order','selectexcelxport','by','scene_id','order_field','order_type','examining');
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
								$k = 'examine.create_time';
							}elseif($k == 'update_time'){
								$k = 'examine.update_time';
							}
							//时间段查询
							if ($v['start'] && $v['end']) {
								$where[$k] = array('between',array(strtotime($v['start']),strtotime($v['end'])+86399));
							} elseif ($v['start']) {
								$where[$k] = array('egt',strtotime($v['start']));
							} else {
								$where[$k] = array('elt',strtotime($v['end'])+86399);
							}
						} elseif (($v['value']) != '') {
							if (in_array($k,$check_field_arr)) {
								$where[$k] = field($v['value'],'contains');
							} else {
								if($k =='examine_status'){
									if ($v['value'] == 'all') {
										$where['examine_status'] = array('egt',0);
									} else {
										if ($v['value'] == 4) {
											$where['examine_status'] = 0;
										} else {
											$where['examine_status'] = $v['value'];
										}
									}
									$fields_search['examine_status']['field'] = 'examine_status';
									$fields_search['examine_status']['value'] = $v['value'];
									$examine_status = $v['value'];
								}elseif($k =='type'){
									if ($v['value'] == 'all') {
										$where['type'] = array('egt',0);
									} else {
										$fields_search['type']['field'] = 'type';
										$fields_search['type']['value'] = intval($v['value']);
										$where['type'] = $v['value'];
										$type = $v['value'];
									}								
								}else{
									$where[$k] = field($v['value'], $v['condition']);
								}
							}
						}
					}else{
						if(!empty($v)){
							if ($k == 'type' || $k == 'examine_status') {
								if ($k =='examine_status') {
									if ($v == 'all') {
										$where['examine_status'] = array('egt',0);
									} else {
										if ($v == 4) {
											$where['examine_status'] = 0;
										} else {
											$where['examine_status'] = $v['value'];
										}
									}
									$fields_search['examine_status']['field'] = 'examine_status';
									$fields_search['examine_status']['value'] = $v;
									$examine_status = $v;
								} elseif ($k =='type') {
									if ($v == 'all') {
										$where['type'] = array('egt',0);
									} else {
										$fields_search['type']['field'] = 'type';
										$fields_search['type']['value'] = intval($v);
										$where['type'] = $v;
										$type = $v['value'];
									}
									$type = $v;
								} else {
									$fields_search[$k]['field'] = $k;
									$fields_search[$k]['value'] = $v;
								}
							} else {
								$where[$k] = field($v);
							}
						}
				    }
				}
				if($k == 'examine.create_time'){
					$k = 'create_time';
				}elseif($k == 'examine.update_time'){
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
		if ($_GET['type'] == 'all' || empty($_GET['type'])) {
			$fields_list_data = M('Fields')->where(array('model'=>array('in',array('')),'is_main'=>1))->field('field,form_type')->select();
		} elseif (intval($_GET['type'])) {
			$fields_list_data = M('Fields')->where(array('model'=>array('in',array('','examine')),'is_main'=>1,'type'=>intval($_GET['type'])))->field('field,form_type')->select();
		}
		
		foreach($fields_list_data as $k=>$v){
			$fields_data_list[$v['field']] = $v['form_type'];
		}
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
		$this->fields_search = $fields_search;
		//待我审核的审批
		if (intval($_GET['examining']) == 1) {
			$params[] = 'by=me_examine';
			$params[] = 'examining=1';
		}

		if(trim($_GET['act']) == 'export'){
			if(checkPerByAction('examine','excelexport')){
				$dc_id = $_GET['export_id'];
				if($dc_id !=''){
					$where['examine_id'] = array('in',$dc_id);
				}
				$current_page = intval($_GET['current_page']);
				$export_limit = intval($_GET['export_limit']);
				$limit = ($export_limit*($current_page-1)).','.$export_limit;
				$examineList = $d_examine->where($where)->order($order)->limit($limit)->select();
				session('export_status', 1);
				$this->excelExport($examineList);
			}else{
				alert('error',  L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
			}
		}
		if(trim($_GET['act']) == 'print'){
			if(checkPerByAction('examine','prevprint')){
				$print_id = $_GET['ids'];
				if($print_id !=''){
					$where['examine_id'] = array('in',$print_id);
				}
				$printList = $d_examine->where($where)->order($order)->select();
				$res = $this->prevprint($printList);
				if($res){
					$this->assign('list',$res);
					$this->alert = parseAlert();
					$this->display('prevprint');
				}else{
					$this->display('index');
				}
			}else{
				alert('error',  L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
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
			import("@.ORG.Page");
			$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;

			//待我审批
			if ($by == 'me_examine' && $_GET['examining'] == 1) {
				$where['examine_status'] = array('in',array(0,1));
				$where['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or');
			}
			//我已审批(审批意见里有我)
			if ($_GET['by'] == 'isexamine') {
				$sum_check_examines = $m_examine_checke->where(array('role_id'=>session('role_id')))->group('examine_id')->getField('examine_id',true);
				$count = $sum_check_examines ? count($sum_check_examines) : 0;
				$p_num = ceil($count/$listrows);
				if ($p_num < $p) {
					$p = $p_num;
				}
				$is_check_examines = $m_examine_checke->where(array('role_id'=>session('role_id')))->group('examine_id')->page($p.','.$listrows)->order('check_time desc')->getField('examine_id',true);
				$is_check_examine = $is_check_examines ? array_unique($is_check_examines) : array();	
				$list = $d_examine->where(array('examine_id'=>array('in',$is_check_examine)))->order($order)->select();
			} else {
				$count = $d_examine->where($where)->count();
				$p_num = ceil($count/$listrows);
				if ($p_num < $p) {
					$p = $p_num;
				}
				$list = $d_examine->where($where)->page($p.','.$listrows)->order($order)->select();
			}
			//审批类型
			$status_list = M('ExamineStatus')->order('status asc')->select();
			$status_arr = array();
			foreach ($status_list as $k=>$v) {
				$status_arr[$v['status']] = $v['name'];
			}
			$m_user = M('User');
			$m_examine_step = M('ExamineStep');
			$d_user = D('User');
			foreach ($list as $k=>$v) {
				if ($v['content'] && $v['type'] !== '2') {
					$content = $v['content'];
				} else {
					$content = $v['description'];
				}
				$list[$k]['content'] = $content ? msubstr($content,0,20) : '查看详情';
				$list[$k]['type_name'] = $status_arr[$v['type']];
				$list[$k]['owner'] = getUserByRoleId($v['owner_role_id']);
				$examine_role_list = array();
				$examine_role_ids = '';
				if ($v['examine_role_id'] && $v['examine_status'] < 2) {
					$examine_role_list = $d_user->get_full_name(explode(',',$v['examine_role_id']));
				} elseif ($v['examine_status'] < 2) {
					$examine_role_ids = $m_examine_step->where(array('order_id'=>$v['order_id'],'process_id'=>$v['type']))->getField('role_id');
					$examine_role_list = $d_user->get_full_name(array($examine_role_ids));
				}
				$list[$k]['examine_role_list'] = $examine_role_list;
			}
			//合计
			$all_days = 0;
			$all_money = 0.00;
			if (in_array(intval($_GET['type']),array(2,3,4,6))) {
				if (intval($_GET['type']) == 2) {
					$where['type'] = 2;
					$where['examine_status'] = array('neq',3);
					$all_days = $m_examine->where($where)->count('duration');
				}
				if (in_array(intval($_GET['type']),array('3','4','6'))) {
					$where['type'] = intval($_GET['type']);
					$where['examine_status'] = array('neq',3);
					$all_money = $m_examine->where($where)->count('budget');
					$all_money = number_format($all_money,2);
				}
			}
			
			$Page = new Page($count,$listrows);
			if (!empty($_REQUEST['by'])){
				$params['by'] = 'by=' . trim($_REQUEST['by']);
			}
			
			$this->parameter = implode('&', $params);
			if ($_GET['desc_order']) {
				$params[] = "desc_order=" . trim($_GET['desc_order']);
			} elseif($_GET['asc_order']){
				$params[] = "asc_order=" . trim($_GET['asc_order']);
			}
			$Page->parameter = implode('&', $params);
			$show = $Page->show();		
			$this->assign('page',$show);
			
			$this->listrows = $listrows;
			$this->assign('list',$list);
			$this->assign("count",$count);
			
			//审批类型
			$this->status_list = M('examine_status') ->where('type=0')->select();
			$this->type = $type;
			$this->all_money = $all_money;
			$this->all_days = $all_days;
			$this->examine_status = $examine_status;
			if (intval($_GET['type']) > 6 || $_GET['type']['value'] > 6) {
				if (is_array($_GET['type'])) {
					$type = intval($_GET['type']['value']) ? : '';
				} else {
					$type = intval($_GET['type']) ? : '';
				}
				//自定义字段
				$this->field_array = getIndexFields('examine',$type);
				$this->field_list = getMainFields('examine',$type);
			}
			$this->alert = parseAlert();
			$this->display();
		}
	}
	/**
	*添加审批
	**/
	public function add(){
		if($this->isPost()){
			$m_examine = M('Examine');
			$type = intval($_POST['type']);
			$examine_status = M('ExamineStatus')->where('status = %d',$type)->find();
			$type_name = $examine_status['name'];
			$examine_role_id = trim($_POST['examine_role_id']);
			if (!$examine_role_id) {
				$this->error('请选择下一审批人！');
			}
			//审批流程排序ID
			$min_order_id = M('ExamineStep')->where(array('process_id'=>$type))->min('order_id');
			if ($type > 6) {
				//自定义审批
				$d_examine = D('Examine');
				$d_examine_data = D('ExamineData');
				$field_list = M('Fields')->where(array('model'=>'examine','in_add'=>1,'status'=>$type))->order('order_id')->select();
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
								$b = array_filter($_POST[$v['field']]);
								$_POST[$v['field']] = !empty($b) ? implode(chr(10),$b) : '';
							}
						break;
					}
				}
				if ($d_examine->create()) {
					if ($examine_id = $d_examine_data->create() !== false) {
						$d_examine->creator_role_id = session('role_id');
						$d_examine->create_time = time();
						$d_examine->update_time = time();
						$d_examine->order_id = $min_order_id ? : 0;
						if ($examine_id = $d_examine->add()) {
							$d_examine_data->examine_id = $examine_id;
							if ($d_examine_data->add()) {
								if($_POST['file']){
									$m_examine_file = M('ExamineFile');
									foreach($_POST['file'] as $v){
										$file_data = array();
										$file_data['examine_id'] = $examine_id;
										$file_data['file_id'] = $v;
										$m_examine_file->add($file_data);
									}
								}
								$creator = getUserByRoleId(session('role_id'));
								$message_content = $creator['user_name'].'于'.date('Y-m-d',time()).'创建的'.$type_name.'等待您的批复！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.session('role_id').'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$examine_id).'">查看详情</a>';
								//审批人
								if ($examine_status['option'] == 1) {
									$examine_role_id = M('ExamineStep')->where(array('process_id'=>$type))->order('order_id asc')->getField('role_id');
									$examine_role_ids = array_filter(explode(',',$examine_role_id));
								} else {
									$examine_role_ids = array(trim($_POST['examine_role_id']));
								}
								foreach ($examine_role_ids as $k=>$v) {
									sendMessage($v,$message_content,1);
								}
								actionLog($examine_id);
								alert('success','添加成功',U('examine/view','id='.$examine_id,'&type='.$type));
							} else {
								$this->error('附表添加失败，请重试！');
							}
						} else {
							$this->error('添加失败，请重试！');
						}
					} else {
						$this->error($d_examine_data->getError());
					}
				} else {
					$this->error($d_examine->getError());
				}
			} else {
				if ($m_examine->create()) {
					$m_examine->create_time = time();
					$m_examine->update_time = time();
					$m_examine->start_time = strtotime($_POST['start_time']);
					$m_examine->end_time = strtotime($_POST['end_time']);
					$m_examine->creator_role_id = session('role_id');
					$m_examine->order_id = $min_order_id ? : 0;
					if($examine_id = $m_examine->add()){
						if($_POST['file']){
							$m_examine_file = M('ExamineFile');
							foreach($_POST['file'] as $v){
								$file_data = array();
								$file_data['examine_id'] = $examine_id;
								$file_data['file_id'] = $v;
								$m_examine_file->add($file_data);
							}
						}
						if($_POST['travel']){
							$m_examine_travel = M('ExamineTravel');
							foreach($_POST['travel'] as $v){
								$file_travel = array();
								$file_travel['examine_id'] = $examine_id;
								$file_travel['start_address'] = $v['start_address'];
								if($_POST['type'] =='4'){
									$file_travel['start_time'] = strtotime($v['start_time']);
									$file_travel['end_address'] = $v['end_address'];
									$file_travel['end_time'] = strtotime($v['end_time']);
									$file_travel['vehicle'] = $v['vehicle'];
									$file_travel['duration'] = $v['duration'];
								}
								$file_travel['money'] = $v['money'];
								$file_travel['description'] = $v['description'];
								$m_examine_travel->add($file_travel);
							}
						}
						$creator = getUserByRoleId(session('role_id'));
						$message_content = $creator['user_name'].'于'.date('Y-m-d',time()).'创建的'.$type_name.'等待您的批复！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.session('role_id').'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$examine_id).'">'.$_POST['content'].'</a>';
						$email_content = $creator['user_name'].'于'.date('Y-m-d',time()).'创建的'.$type_name.'等待您的批复！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.session('role_id').'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$examine_id,'','',true).'">'.$_POST['content'].'</a>';
						//审批人
						if ($examine_status['option'] == 1) {
							$examine_role_id = M('ExamineStep')->where(array('process_id'=>$type))->order('order_id asc')->getField('role_id');
							$examine_role_ids = array_filter(explode(',',$examine_role_id));
						} else {
							$examine_role_ids = array(trim($_POST['examine_role_id']));
						}
						foreach ($examine_role_ids as $k=>$v) {
							sendMessage($v,$message_content,1);
						}
						/* if(intval($_POST['email_alert']) == 1){
							sysSendEmail($_POST['examine_role_id'],'CRM 通知',$email_content);
						} */
						actionLog($examine_id);
						alert('success','添加成功',U('examine/view','id='.$examine_id,'&type='.$_POST['type']));
					}else{
						alert('error','添加失败',$_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error','添加失败',$_SERVER['HTTP_REFERER']);
				}
			}
		}else{
			$type = intval($_GET['type']);
			$examine_status = M('ExamineStatus')->where('status=%d',$type)->find();
			$m_user = M('User');
			if ($examine_status['option'] == 1) {
				$examine_step = M('ExamineStep')->where('process_id =%d',$type)->order('order_id asc')->find();
				if (trim($examine_step['role_id'], ',') == '') {
					alert('error', '该审批未设置审批流程，请联系管理员。', U('examine/index'));
				}
				$role_list = M('User')->where(array('role_id'=>array('in',explode(',',trim($examine_step['role_id'], ',')))))->field('full_name,role_id,thumb_path')->select();
				foreach ($role_list as $k=>$v) {
					$role_list[$k]['thumb_path'] = $v['thumb_path'] ? : './Public/img/avatar_default.png';
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
			} else {

			}
			$this->examine_step = $examine_step;
			$this->option = $examine_status['option'];
			$this->examine_status = $examine_status;
			$this->alert = parseAlert();
			if ($type > 6) {
				$this->field_list = field_list_html("add","examine","","",$type);
				$this->display('new_add');
			} else {
				$this->display();
			}
		}
	}
	/**
	*修改审批
	**/
	public function edit(){
		$m_examine = M('Examine');
		$m_examine_travel = M('ExamineTravel');
		$m_examine_file = M('ExamineFile');
		$m_user = M('User');
		if($_GET['id']){
			$examine_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		}else{
			$examine_id = intval($_POST['examine_id']);
		}
		if(!$examine_id){
			alert('error', '参数错误！', U('examine/index'));
		}
		$below_ids = $this->_permissionRes;
		$where['examine_id'] = $examine_id;
		$examine_info = $m_examine->where($where)->find();
		if(!$examine_info){
			alert('error', '数据不存在或已删除！', U('examine/index'));
		}else{
			if(in_array($examine_info['examine_status'],array('1','2'))){
				alert('error', '当前状态不允许编辑!',$_SERVER['HTTP_REFERER']);
			}elseif(!in_array($examine_info['creator_role_id'],$below_ids) && !session('?admin')){
				$this->error(L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
			}
		}
		$examine_status = M('ExamineStatus')->where(array('status'=>$examine_info['type']))->find();
		//审批流程排序ID
		$min_order_id = M('ExamineStep')->where(array('process_id'=>$examine_info['type']))->min('order_id');

		if($this->isPost()){
			if ($examine_info['type'] > 6) {
				//自定义审批
				$examine_info = D('ExamineView')->where('examine.examine_id = %d',$examine_id)->find();
				$d_examine = D('Examine');
				$d_examine_data = D('ExamineData');
				$field_list = M('Fields')->where(array('model'=>'examine','status'=>$examine_info['type']))->order('order_id')->select();
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
				if($d_examine->create()){
					$d_examine->examine_status = 0;
					$d_examine->order_id = $min_order_id ? : 0;
					$d_examine->update_time = time();
					if($d_examine_data->create()!==false){
						$a = $d_examine->where('examine_id = %d',$examine_id)->save();
						if (M('ExamineData')->where('examine_id = %d',$examine_id)->find()) {
							$b = $d_examine_data->where('examine_id = %d',$examine_id)->save();
						} else {
							$d_examine_data->examine_id = $examine_id;
							$b = $d_examine_data->where('examine_id = %d',$examine_id)->add();
						}
						
						if($a && $b!==false) {
							$type_name = $examine_status['name'];
							$creator = getUserByRoleId(session('role_id'));
							$message_content = $creator['user_name'].'于'.date('Y-m-d',time()).'编辑了'.$type_name.'等待您的批复！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.session('role_id').'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$_POST['examine_id']).'">'.$_POST['content'].'</a>';

							if ($examine_status['option'] == 1) {
								$examine_role_id = M('ExamineStep')->where(array('process_id'=>$type))->order('order_id asc')->getField('role_id');
								$examine_role_ids = array_filter(explode(',',$examine_role_id));
							} else {
								$examine_role_ids = array(trim($_POST['examine_role_id']));
							}
							foreach ($examine_role_ids as $k=>$v) {
								sendMessage($v,$message_content,1);
							}
							actionLog($examine_id);
							alert('success', '编辑成功', U('examine/index','type='.$_POST['type']));
						} else {
							$this->error('修改失败，请重试！');
						}
					}else{
						$this->error($d_examine_data->getError());
					}

				}else{
					$this->error($d_examine->getError());
				}
			} else {
				if ($m_examine->create()) {
					$m_examine->update_time = time();
					$m_examine->start_time = strtotime($_POST['start_time']);
					$m_examine->end_time = strtotime($_POST['end_time']);
					$m_examine->examine_status = 0;
					$m_examine->order_id = $min_order_id ? : 0;
					if($m_examine->where('examine_id = %d', $_POST['examine_id'])->save()){
						$operation_flag = true;
						foreach($_POST['travel'] as $v){
							if(!empty($v['money'])){
								$file_travel = array();
								$file_travel['examine_id'] = $examine_id;
								$file_travel['start_address'] = $v['start_address'];
								if($_POST['type'] =='4'){
									$file_travel['start_time'] = strtotime($v['start_time']);
									$file_travel['end_address'] = $v['end_address'];
									$file_travel['end_time'] = strtotime($v['end_time']);
									$file_travel['vehicle'] = $v['vehicle'];
									$file_travel['duration'] = $v['duration'];
								}
								$file_travel['money'] = $v['money'];
								$file_travel['description'] = $v['description'];
								//在编辑时，如果又添加商品，根据是否存在id来进行编辑或添加
								if(empty($v['id'])){
									//添加
									$result_examine = $m_examine_travel->add($file_travel);
									if(empty($result_examine)){
										$operation_flag = false;
										break;
									}
								}else{
									//编辑
									$result_examine = $m_examine_travel->where('id = %d', $v['id'])->save($file_travel);
									if($result_examine === false){
										$operation_flag = false;
										break;
									}
								}
							}
							//在编辑时，如果从原来的数据中去除一条信息，则删除
							if($v['id'] && empty($v['money'])){
								$result_examine = $m_examine_travel->where('id = %d', $v['id'])->delete();
								if($result_examine == 0 || $result_examine === false){
									$operation_flag = false;
								}
							}
						}
						if($_POST['file']){
							foreach($_POST['file'] as $v){
								$file_info = $m_examine_file->where('file_id = %d',$v)->find();
								if(!$file_info){
									$file_data = array();
									$file_data['examine_id'] = $examine_id;
									$file_data['file_id'] = $v;
									$m_examine_file->add($file_data);
								}
							}
						}
						$type_name = $examine_status['name'];
						$creator = getUserByRoleId(session('role_id'));
						$message_content = $creator['user_name'].'于'.date('Y-m-d',time()).'编辑了'.$type_name.'等待您的批复！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.session('role_id').'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$_POST['examine_id']).'">'.$_POST['content'].'</a>';
						if ($examine_status['option'] == 1) {
							$examine_role_id = M('ExamineStep')->where(array('process_id'=>$type))->order('order_id asc')->getField('role_id');
							$examine_role_ids = array_filter(explode(',',$examine_role_id));
						} else {
							$examine_role_ids = array(trim($_POST['examine_role_id']));
						}
						foreach ($examine_role_ids as $k=>$v) {
							sendMessage($v,$message_content,1);
						}
						actionLog($examine_id);
						alert('success', '编辑成功', U('examine/index','type='.$_POST['type']));
					}else{
						alert('error', '编辑失败', $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', '编辑失败', $_SERVER['HTTP_REFERER']);
				}
			}
		}else{
			if ($examine_info['examine_status'] == 2) {
				alert('error', '该审批已经结束！',U('examine/index','type='.$_POST['type']));
			}
			if ($examine_status['option'] == 1) {
				$examine_step = M('ExamineStep')->where('process_id =%d',$examine_info['type'])->order('order_id asc')->find();
				$examine_step['role_list'] = $m_user->where(array('role_id'=>array('in',explode(',',$examine_step['role_id']))))->field('full_name,role_id,thumb_path')->select();
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

			$examine_info['owner_name'] = $m_user->where('role_id =%d',$examine_info['owner_role_id'])->getField('full_name');
			$examine_info['travel'] = M('ExamineTravel')->where('examine_id = %d',$examine_id)->select();

			//附件
			$file_id_array = M('ExamineFile')->where('examine_id = %d',$examine_id)->getField('file_id',true);
			$examine_info['file_list'] = M('File')->where('file_id in (%s)',implode(',',$file_id_array))->select();
			$d_file = D('File');
			foreach ($examine_info['file_list'] as $key => $value) {
				$examine_info['file_list'][$key]['size'] = ceil($value['size']/1024);
				$examine_info['file_list'][$key]['pic'] = show_picture($value['name']);
				if ($value['oss'] == 1) {
					$examine_info['file_list'][$key]['file_path'] = $d_file::FILE_URL . '/' . $value['file_path'];
				}
			}
			$this->info = $examine_info;
			$this->option = $examine_status['option'];
			$this->examine_status = $examine_status;
			$this->alert = parseAlert();
			if ($examine_info['type'] > 6) {
				$this->field_list = field_list_html("edit","examine",$examine_info,'',$examine_info['type']);
				$this->display('new_edit');
			} else {
				$this->display();
			}
		}
	}

	//检查是否有权限
	public function checkPer($examine_id){
		$m_examine = M('Examine');
		if(!session('?admin')){
			//非管理员权限限制
			//已审核的人
			$examine_check_info = M('ExamineCheck')->where(array('role_id'=>session('role_id'),'examine_id'=>$examine_id))->find();
			//审核人或自己
			$c_where['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or');
			$c_where['creator_role_id'] = session('role_id'); 
			$c_where['owner_role_id'] = session('role_id');
			$c_where['_logic'] = 'or';
			$where['_complex'] = $c_where;
		}
		$where['examine_id'] = $examine_id;
		$res_info = $m_examine->where($where)->find();
		$examine_info = $m_examine->where('examine_id = %d',$examine_id)->field('creator_role_id,owner_role_id')->find();
		//授权判断
		$below_ids = getPerByAction('examine','view');
		if($examine_check_info || $res_info || in_array($examine_info['creator_role_id'], $below_ids)){
			return true;
		}else{
			return false;
		}
	}

	/**
	*查看审批详情
	**/
	public function view(){
		$m_examine = M('Examine');
		$m_examine_check = M('ExamineCheck');
		$m_examine_step = M('ExamineStep');
		$m_user = M('User');
		$d_file = D('File');
		$examine_id = intval($_GET['id']);
		$where['examine_id'] = $examine_id;
		$examine_info = $m_examine->where($where)->find();
		$examine_status = M('ExamineStatus')->where('status=%d',$examine_info['type'])->find();
		if ($examine_info) {
			if (!$this->checkPer($examine_id)) {
				alert('error',L('HAVE NOT PRIVILEGES'),U('examine/index'));
			}
			if ($examine_info['type'] > 6) {
				$examine_info = D('ExamineView')->where('examine.examine_id = %d',$examine_id)->find();
			}
			$examine_info['owner'] = M('User')->where('role_id = %d', $examine_info['owner_role_id'])->field('full_name,role_id')->find();
			//附件
			$file_ids = M('ExamineFile')->where('examine_id = %d', $examine_id)->getField('file_id', true);
			if (!empty($file_ids)) {
				$file_list = M('file')->where('file_id in (%s)', implode(',', $file_ids))->select();
				$file_count = 0;
				foreach ($file_list as $key=>$value) {
					$file_list[$key]['owner'] = $m_user->where('role_id = %d', $value['role_id'])->field('full_name,role_id')->find();
					$file_list[$key]['size'] = ceil($value['size']/1024);
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
					$file_list[$key]['pic'] = $pic;
					$file_count++;
					if ($value['oss'] == 1) {
						$file_list[$key]['file_path'] = $d_file::FILE_URL . '/' . $value['file_path'];
					}
				}
				$examine_info['file_list'] = $file_list ? : array();
				$examine_info['file_count'] = $file_count;
			}
			//审批人流程
			if ($examine_status['option'] == 1) {
				$examine_arr = array();
				$examine_role_id = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$examine_info['order_id']))->getField('role_id');
				$examine_arr = $m_user->where(array('role_id'=>array('in',explode(',',$examine_role_id))))->field('full_name,role_id')->select();
				$examine_info['examine'] = $examine_arr;
				//审批流
				$step_list = $m_examine_step->where('process_id=%d',$examine_info['type'])->order('order_id')->select();
				foreach ($step_list as $k=>$v) {
					$role_ids = explode(',',$v['role_id']);
					$role_list = array();
					$role_list = $m_user->where(array('role_id'=>array('in',$role_ids)))->field('thumb_path,full_name,role_id')->select();
					if ($examine_info['order_id'] >= $v['order_id']) {
						$check_role_arr = array();
						//审批意见
						$check_role_arr = $m_examine_check->where(array('examine_id'=>$examine_info['examine_id'],'order_id'=>$v['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);
						$is_checked_name = '';
						if ($examine_info['order_id'] == $v['order_id']) {
							$is_checked_name = '待审';
						}
						foreach ($role_list as $key=>$val) {
							if ($check_role_arr && in_array($val['role_id'],$check_role_arr)) {
								$role_list[$key]['is_checked_name'] = '同意';
							} else {
								$role_list[$key]['is_checked_name'] = $is_checked_name;
							}
						}
					}
					$step_list[$k]['role_list'] = $role_list;
					if (count($role_ids) > 1) {
						if ($v['relation'] == 1) {
							$step_list[$k]['relation_name'] = '并';
						} elseif ($v['relation'] == 2) {
							$step_list[$k]['relation_name'] = '或';
						}
					}
				}
				$this->assign('step_list',$step_list);
			} else {
				if (is_numeric($examine_info['examine_role_id'])) {
					$examine_info['examine'] = $m_user->where(array('role_id'=>$examine_info['examine_role_id']))->field('full_name,role_id')->select();
				} else {
					$examine_info['examine'] = $m_user->where(array('role_id'=>array('in',array_filter(explode(',', $examine_info['examine_role_id'])))))->field('full_name,role_id')->select();
				}
			}
			if ($examine_info['type'] > 6) {
				//自定义审批
				$field_list = M('Fields')->where(array('model'=>'examine','status'=>$examine_info['type']))->order('order_id')->select();
				foreach($field_list as $k=>$v){
					if(trim($v['input_tips'])){
						$input_tips = ' &nbsp; <span style="color:red">('.L('INFUSE').$v['input_tips'].')</span>';
					}else{
						$input_tips = '';
					}
				}
				$this->info = $examine_info;
				$this->examine_status = $examine_status;
				$this->field_list = $field_list;
				$this->alert = parseAlert();
				$this->display('new_view');
			} else {
				if($examine_info['type'] == 2){
					$examine['cate'] = $this->cate[$examine_info['cate']-1];
				}else{
					$examine_info['cate'] = $examine_info['cate'];
				}
				$examine_info['travel'] = M('ExamineTravel')->where('examine_id = %d',$examine_id)->select();
				$this->info = $examine_info;
				$this->examine_status = $examine_status;
				$this->alert = parseAlert();
				$this->display();
			}
		}else{
			alert('error', '数据不存在或已删除！',$_SERVER['HTTP_REFERER']);
		}
	}

	/**
	*删除审批
	**/
	public function delete() {
		$examine_id = $_REQUEST['ids'];
		if(!$examine_id){
			$this->ajaxReturn('','参数错误！',0);
		}
		$m_examine = M('Examine');
		$examine_info = $m_examine->where('examine_id = %d',$examine_id)->find();
		$below_ids = $this->_permissionRes;
		if(!$examine_info){
			$this->ajaxReturn('','数据不存在或已删除！',0);
		}else{
			if($examine_info['examine_status'] != 0 && !session('?admin')){
				$this->ajaxReturn('','当前状态不允许删除!',0);
			}elseif(!in_array($examine_info['creator_role_id'],$below_ids) && !session('?admin')){
				$this->ajaxReturn('','您没有此权利!',0);
			}
		}
		$where['examine_id'] = array('in',$_REQUEST['ids']);
		if($m_examine->where($where)->delete()){
			foreach($_REQUEST['ids'] as $k=>$v){
				actionLog($v);
			}
			$this->ajaxReturn('','删除成功!',1);
		}else{
			$this->ajaxReturn('','删除失败!',0);
		}
	}
	
	/**
	*添加审批进度
	**/
	public function add_examine() {
		$m_examine = M('Examine');
		$m_examine_step = M('ExamineStep');
		$m_examine_check = M('ExamineCheck');
		$m_examine_status = M('ExamineStatus');
		$m_user = M('user');

		if ($this->isPost()) {
			$examine_id = intval($_POST['examine_id']);
		} else {
			$examine_id = intval($_REQUEST['id']);
		}
		$examine_info = $m_examine->where(array('examine_id'=>$examine_id))->find();
		$examine_status = $m_examine_status->where(array('status'=>$examine_info['type']))->find();
		if ($examine_info['examine_status'] == 2 || $examine_info['examine_status'] == 3) {
			if ($this->isPost()) {
				alert('error','该审批已经结束！',$_SERVER['HTTP_REFERER']);
			} else {
				echo '<div class="alert alert-error">该审批已经结束！</div>';die;
			}
		}

		if ($examine_status['option'] == 1) {
			$examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$examine_info['order_id']))->find();
			//当前步骤已审批role_id
			$is_examine_role_ids = $m_examine_check->where(array('examine_id'=>$examine_id,'order_id'=>$examine_info['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);

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
			//权限判断
			$isnot_add_examine = false;
			if (!session('?admin') && ($examine_info['examine_role_id'] != session('role_id') && !in_array(session('role_id'),array_filter(explode(',',$examine_info['examine_role_id']))))) {
				if ($this->isPost()) {
					alert('error','您没有审核权限！',$_SERVER['HTTP_REFERER']);
				} else {
					echo '<div class="alert alert-error">您没有审核权限！</div>';die;
				}
			}
		}

		if($this->isPost()){
			$examine_status = $m_examine_status->where('status=%d',$examine_info['type'])->find();
			if ($m_examine->create()) {
				$m_examine->update_time = time();
				$is_end = 0; //是否结束审批（发送站内信判断）

				if ($examine_status['option'] == 1) {
					//当前单个审批流程是否结束
					$examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$examine_info['order_id']))->find();
					//已审批role_id
					$is_check_role = $m_examine_check->where(array('examine_id'=>$examine_info['examine_id'],'order_id'=>$examine_info['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);
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
						$next_order_id = $examine_info['order_id']+1; //下一审批流程排序ID
						//获取下一审批人
	                    $next_examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$next_order_id))->find();
	                    if ($next_examine_step_info['relation'] == 1) {
	                        $relation_name = '并';
	                    } elseif ($next_examine_step_info['relation'] == 2) {
	                        $relation_name = '或';
	                    }
						$next_role_id = $next_examine_step_info['role_id'];
					} else {
						//当前流程，剩余审批人
						$next_order_id = $examine_info['order_id'];
						$next_examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$next_order_id))->find();
	                    if ($next_examine_step_info['relation'] == 1) {
	                        $relation_name = '并';
	                    } elseif ($next_examine_step_info['relation'] == 2) {
	                        $relation_name = '或';
	                    }
						$next_role_id = $is_check_role ? array_merge(array_diff(array_filter(explode(',',$examine_step_info['role_id'])),$is_check_role)) : array_filter(explode(',',$examine_step_info['role_id']));
					}		
				} else {
					$next_role_id = trim($_POST['examine_role_id']);
					$next_order_id = 0;
				}
				if ($_POST['is_agree'] == 1) {
					if ($_POST['examine_status'] != 2 && $next_role_id == null && $examine_status['option'] !== 1) {
						alert('error','请选择下一审批人！',$_SERVER['HTTP_REFERER']);
					}
					if (is_array($next_role_id)) {
						$m_examine->examine_role_id = ','.implode(',',$next_role_id).',';
					} else {
						$m_examine->examine_role_id = $next_role_id;
					}
					
					if ($_POST['examine_status'] == 2) {
						$m_examine->order_id = intval($next_order_id);
						$m_examine->examine_status = 2; //审批结束(通过)
					} elseif ($examine_status['option'] == 1) {
						//自定义流程
						//查询审批流程排序最大值，如果order_id和最大值相等，则审批结束
						$max_order_id = $m_examine_step->where('process_id = %d',$examine_info['type'])->max('order_id');
						$order_id = intval($next_order_id);
						$new_order_id = $order_id-1;
						if ($new_order_id == $max_order_id) {
							$m_examine->examine_status = 2;//审批结束(通过)
							$is_end = 1;
						} else {
							$m_examine->order_id = $order_id;
							$m_examine->examine_status = 1;	//审批中
						}
					} else {
						$m_examine->examine_status = 1;	//审批中
					}
				} else {
					//结束审批
					$is_end = 1;
					//如果是自定义流程,驳回至最初状态
					$step_role_id = '';				
					if($examine_status['option'] == 1){
						$step_role_id = $m_examine_step->where(array('process_id'=>intval($_POST['type'])))->order('order_id asc')->getField('role_id');
						//将自定义审批意见标记为无效
						$m_examine_check->where(array('examine_id'=>$examine_info['examine_id']))->setField('is_end',1);
					}
					$m_examine->examine_role_id = $step_role_id ? : 0;
					$m_examine->order_id = 0;
					$m_examine->examine_status = 3;
				}
			}

			if($m_examine->where($where)->save()){
				$c_data['role_id'] = session('role_id');
				$c_data['is_checked'] = intval($_POST['is_agree']);
				$c_data['examine_id'] = $examine_id;
				$c_data['content'] = trim($_POST['opinion']);
				$c_data['check_time'] = time();
				$c_data['order_id'] = $examine_info['order_id'];
				if ($_POST['is_agree'] == 2 && $examine_status['option'] == 1) {
					$c_data['is_end'] = 1;
				}
				$m_examine_check->add($c_data);
				
				$type_name = $examine_status['name'];
				$creator = getUserByRoleId($examine_info['creator_role_id']);
				if($_POST['examine_status'] == 2 || $is_end == 1){
					if ($_POST['message_alert'] == 1) {
						$message_content = '您申请的'.$type_name.'已被审批！<a href="'.U('examine/view','id='.$examine_id).'">点击查看</a><br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.$examine_info['creator_role_id'].'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间：'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$examine_id).'">'.$examine_info['content'].'</a>';
						sendMessage($examine_info['creator_role_id'],$message_content,1);
					}
					if ($_POST['email_alert'] == 1) {
						$email_content = '您申请的'.$type_name.'已被审批！<a href="'.U('examine/view','id='.$examine_id,'','',true).'">点击查看</a><br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.$examine_info['creator_role_id'].'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$examine_id).'">'.$examine_info['content'].'</a>';
						sysSendEmail($examine_info['creator_role_id'],'CRM 通知',$email_content);
					}
				} else {
					//下一审批人
					if (is_array($next_role_id)) {
						$examine_role_ids = $next_role_id ? array_filter($next_role_id) : array();
					} elseif ($next_role_id) {
						$examine_role_ids = $next_role_id ? array_filter(explode(',',$next_role_id)) : array();
					} else {
						$examine_role_ids = array();
					}

					if ($_POST['message_alert'] == 1) {
						$message_content = '您有一个'.$type_name.'审批待处理！<a href="'.U('examine/view','id='.$examine_id).'">点击查看</a><br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.$examine_info['creator_role_id'].'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型：'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容：<a href="'.U('examine/view','id='.$examine_id).'">'.$examine_info['content'].'</a>';
						foreach ($examine_role_ids as $k=>$v) {
							sendMessage($v,$message_content,1);
						}
					}
					if ($_POST['email_alert'] == 1) {
						$email_content = '您有一个'.$type_name.'审批待处理！<a href="'.U('examine/view','id='.$examine_id,'','',true).'">点击查看</a><br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.$examine_info['creator_role_id'].'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间：'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型：'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容：<a href="'.U('examine/view','id='.$examine_id).'">'.$examine_info['content'].'</a>';
						foreach ($examine_role_ids as $k=>$v) {
							sysSendEmail($v,'CRM 通知',$email_content);
						}
					}
				}
				alert('success','审核成功', $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', '审核失败', $_SERVER['HTTP_REFERER']);
			}
		}else{
			if ($examine_status['option'] == 1) {
				$examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$examine_info['order_id']))->find();
				//当前流程是否结束
				//已审批role_id
				$is_check_role = $m_examine_check->where(array('examine_id'=>$examine_info['examine_id'],'order_id'=>$examine_info['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);
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
					$next_order_id = $examine_info['order_id']+1; //下一审批流程排序ID
					//获取下一审批人
                    $next_examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$next_order_id))->find();
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
					$next_order_id = $examine_info['order_id'];
					$next_examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$next_order_id))->find();
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
			}
			$this->option = $examine_status['option']; 
			$this->type = $type;
			$this->assign('examine',$examine_info);
			$this->display();
		}
	}

	/**
	*审批记录
	**/
	public function check_list(){
		$m_examine_check = M('examine_check');
		$m_user = M('user');
		$examine_id = intval($_GET['id']);
		//判断权限
		if(!$this->checkPer($examine_id)){
			echo '<div class="alert alert-error">您没有此权利！</div>';die();
		}
		if($examine_id){
			$check_list = $m_examine_check ->where('examine_id =%d',$examine_id)->order('check_id asc')->select();
			foreach($check_list as $kk=>$vv){
				$check_list[$kk]['user'] = $m_user ->where('role_id =%d',$vv['role_id'])->field('role_id,full_name,thumb_path')->find();
			}
			$this->check_list = $check_list;
		}
		$this->display();
	}

	/**
	*审批统计
	**/
	public function analytics(){
		$m_examine = M('Examine');
		$m_examine_status = M('ExamineStatus');
		$m_user = M('User');
		$m_sign = M('Sign');
		//权限范围
		$below_ids = getPerByAction(MODULE_NAME,ACTION_NAME);
		if (intval($_GET['role'])) {
			$params[] = "role=" . intval($_GET['role']);
			$role_id_array = array(intval($_GET['role']));
		} else {
			if (intval($_GET['department'])) {
				$params[] = "department=" . intval($_GET['department']);
				$department_id = intval($_GET['department']);
				foreach(getRoleByDepartmentId($department_id, true) as $k=>$v){
					$role_id_array[] = $v['role_id'];
				}
			} else {
				$role_id_array = getSubRoleId(true, 1);
			}
		}
		//过滤权限范围内的role_id
		if ($role_id_array) {
			//数组交集
			$idArray = array_intersect($role_id_array,$below_ids);
		} else {
			$idArray = getPerByAction(MODULE_NAME,ACTION_NAME,false);
		}
		//时间段搜索
		if ($_GET['between_date']) {
			$between_date = explode(' - ',trim($_GET['between_date']));
			if($between_date[0]){
				$start_time = strtotime($between_date[0]);
			}
			$end_time = $between_date[1] ?  strtotime(date('Y-m-d 23:59:59',strtotime($between_date[1]))) : strtotime(date('Y-m-d 23:59:59',time()));
		} else {
			$start_time = strtotime(date('Y-m-01 00:00:00'));
			$end_time = strtotime(date('Y-m-d H:i:s'));
		}
		$this->between_date = $_GET['between_date'] ? trim($_GET['between_date']) : date('Y-m-01').' - '.date('Y-m-d');
		$this->start_date = date('Y-m-d',$start_time);
		$this->end_date = date('Y-m-d',$end_time);

		$where = array('role_id'=>array('in', $idArray));
		$search_disable_user = M('Config')->where('name="search_disable_user"')->getField('value');
		if (!$search_disable_user) {
			$where['status'] = array('neq', 2);
		}

		$count = $m_user->where($where)->count();
		$p = $_GET['p'] ? intval($_GET['p']) : 1;
		$listrows = $_GET['listrows'] ? intval($_GET['listrows']) : 15;
		$params[] = "listrows=" . $listrows;
		$p_num = ceil($count/$listrows); 
		if ($p_num < $p) {
			$p = $p_num;
		}
		import("@.ORG.Page");
		//分页功能
		if (trim($_GET['act']) == 'excel') {
			$role_list = $m_user->where($where)->field('role_id,full_name,thumb_path')->select();
		} else {
			$role_list = $m_user->where($where)->page($p.','.$listrows)->field('role_id,full_name,thumb_path')->select();
		}

		$examine_total = array();
		$status_a_total = '0'; //请假合计
		$status_b_total = '0.00'; //报销合计
		$status_c_total = '0.00'; //差旅合计
		$status_d_total = '0.00'; //出差合计
		$status_e_total = '0.00'; //借款合计
		$status_f_total = '0'; //外勤签到合计
		foreach ($role_list as $k=>$v) {
			$status_a = '0'; //请假
			$status_b = '0.00'; //报销
			$status_c = '0.00'; //差旅
			$status_d = '0.00'; //出差
			$status_e = '0.00'; //借款
			$status_f = '0'; //外勤签到次数
			$examine_list = array();

			$examine_list = $m_examine->where(array('owner_role_id'=>$v['role_id'],'create_time'=>array('between',array($start_time,$end_time)),'examine_status'=>array('eq',2)))->field('duration,budget,type,examine_id')->select();
			foreach ($examine_list as $key=>$val) {
				switch ($val['type']) {
					case 2 : $status_a += $val['duration']; break;
					case 3 : $status_b += $val['budget']; break;
					case 4 : $status_c += $val['budget']; break;
					case 5 : $status_d += $val['budget']; break;
					case 6 : $status_e += $val['budget']; break;
				}
			}

			//签到
			$status_f = $m_sign->where(array('role_id'=>$v['role_id'],'create_time'=>array('between',array($start_time,$end_time))))->count();

			$role_list[$k]['status_a'] = $status_a;
			$role_list[$k]['status_b'] = $status_b;
			$role_list[$k]['status_c'] = $status_c;
			$role_list[$k]['status_d'] = $status_d;
			$role_list[$k]['status_e'] = $status_e;
			$role_list[$k]['status_f'] = $status_f;
		}
		//由于分页原因，合计需单独查
		$status_a_total = $m_examine->where(array('owner_role_id'=>array('in', $idArray),'create_time'=>array('between',array($start_time,$end_time)),'examine_status'=>array('eq',2),'type'=>2))->sum('duration');
		$status_b_total = $m_examine->where(array('owner_role_id'=>array('in', $idArray),'create_time'=>array('between',array($start_time,$end_time)),'examine_status'=>array('eq',2),'type'=>3))->sum('budget');
		$status_c_total = $m_examine->where(array('owner_role_id'=>array('in', $idArray),'create_time'=>array('between',array($start_time,$end_time)),'examine_status'=>array('eq',2),'type'=>4))->sum('budget');
		$status_d_total = $m_examine->where(array('owner_role_id'=>array('in', $idArray),'create_time'=>array('between',array($start_time,$end_time)),'examine_status'=>array('eq',2),'type'=>5))->sum('budget');
		$status_e_total = $m_examine->where(array('owner_role_id'=>array('in', $idArray),'create_time'=>array('between',array($start_time,$end_time)),'examine_status'=>array('eq',2),'type'=>6))->sum('budget');
		$status_f_total = $m_sign->where(array('role_id'=>array('in',$idArray),'create_time'=>array('between',array($start_time,$end_time))))->count();
		$examine_total = array(
							'status_a_total'=>$status_a_total ? : '0',
							'status_b_total'=>$status_b_total ? : '0.00',
							'status_c_total'=>$status_c_total ? : '0.00',
							'status_d_total'=>$status_d_total ? : '0.00',
							'status_e_total'=>$status_e_total ? : '0.00',
							'status_f_total'=>$status_f_total ? : '0'
							);

		if (trim($_GET['act']) == 'excel') {	
			// if (!checkPerByAction('examine','analyexcelexport')) {
			// 	$this->ajaxReturn('',L('DO NOT HAVE PRIVILEGES'),1);
			// } else {
				session('analy_export_status', 1);
				$this->analyExcelExport($role_list,$examine_total);
			// }
		}

		$this->role_list = $role_list;
		$this->examine_total = $examine_total;

		$Page = new Page($count,$listrows);
		$this->count = $count;
		$this->assign('count',$count);
		$this->parameter = implode('&', $params);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		$this->listrows = $listrows;

		$this->daterange = daterange();

		$idArray = getSubRoleId(true, 1);
		$roleList = array();
		foreach($idArray as $roleId){
			$user = getUserByRoleId($roleId);
			if ($search_disable_user) {
				$roleList[$roleId] = $user;
			} elseif ($user['status'] != 2) {
				$roleList[$roleId] = $user;
			}
		}
		$this->roleList = $roleList;
		$departmentList = M('roleDepartment')->select();
		$this->assign('departmentList', $departmentList);
		$this->alert = parseAlert();
		$this->display();
	}
	
	/**
	 * 详情页打印
	 **/
	public function prevprint($printList=false){
		$examine_id = intval($_GET['id']);
		$m_examine = M('Examine');
		$m_opinion = M('ExamineOpinion');
		if(!$examine_id){
			$list = $printList;
			if(empty($list)){
				alert('error', '当前打印的数据为空！不能打印！',$_SERVER['HTTP_REFERER']);
			}
			foreach($list as $k=>$v){
				$list[$k]['creator'] = getUserByRoleId($v['creator_role_id']);
				$list[$k]['examine'] = getUserByRoleId($v['examine_role_id']);
				if($list[$k]['type'] == 2){
					$list[$k]['cate'] = $this->cate[$list[$k]['cate']-1];
				}else{
					$list[$k]['cate'] = $list[$k]['cate'];
				}
				if($list[$k]['type'] == 3){
					$list[$k]['expense'] = M('ExamineExpense')->where('examine_id = %d', $v['examine_id'])->select();
					$list[$k]['money_total'] = M('ExamineExpense')->where('examine_id = %d', $v['examine_id'])->sum('money');
				}
				$list[$k]['opinions'] = $m_opinion->where('examine_id = %d', $v['examine_id'])->select();
				foreach($list[$k]['opinions'] as $key=>$value){
					$list[$k]['opinions'][$key]['examine_role'] = M('User')->where('role_id=%d',$value['examine_role_id'])->getField('name');
				}
			}
			return $list;
		}elseif(!$list = $m_examine->where('is_deleted = 0 and examine_id = %d', $examine_id)->select()) {
			alert('error', '该条审批不存在或已被删除！',$_SERVER['HTTP_REFERER']);
		} else {
			$list[0]['creator'] = getUserByRoleId($list[0]['creator_role_id']);
			$list[0]['examine'] = getUserByRoleId($list[0]['examine_role_id']);
			if($list[0]['type'] == 2){
				$list[0]['cate'] = $this->cate[$list[0]['cate']-1];
			}else{
				$list[0]['cate'] = $list[0]['cate'];
			}
			if($list[0]['type'] == 3){
				$list[0]['expense'] = M('ExamineExpense')->where('examine_id = %d', $examine_id)->select();
				$list[0]['money_total'] = M('ExamineExpense')->where('examine_id = %d', $examine_id)->sum('money');
			}
			$list[0]['opinions'] = $m_opinion->where('examine_id = %d', $examine_id)->select();
			foreach($list[0]['opinions'] as $key2=>$value2){
				$list[0]['opinions'][$key2]['examine_role'] = M('User')->where('role_id=%d',$value2['examine_role_id'])->getField('name');
			}
			$this->assign('list',$list);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	/**
	*导出审批到excel表格
	*
	**/
	public function excelExport($examineList=false){
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("pdcrm");    
		$objProps->setLastModifiedBy("pdcrm");    
		$objProps->setTitle("pdcrm Examine Data");    
		$objProps->setSubject("pdcrm Examine Data");    
		$objProps->setDescription("pdcrm Examine Data");    
		$objProps->setKeywords("pdcrm Examine Data");    
		$objProps->setCategory("Examine");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		$objActSheet->setTitle('Sheet1');
		
		$objActSheet->setCellValue('A1', '创建时间');
		$objActSheet->setCellValue('B1', '申请人');
		$objActSheet->setCellValue('C1', '审批类型');
		$objActSheet->setCellValue('D1', '审批内容');

		if($_GET['type'] == 1){//普通审批
			$objActSheet->setCellValue('E1', '审批意见');
			$objActSheet->setCellValue('F1', '审批状态');
			$examine_ = '普通审批';
		}else if($_GET['type'] == 2){//请假单
			$objActSheet->setCellValue('E1', '请假时长');
			$objActSheet->setCellValue('F1', '审批意见');
			$objActSheet->setCellValue('G1', '审批状态');
			$examine_ = '请假单';
		}else if($_GET['type'] == 3){//报销单
			$objActSheet->setCellValue('E1', '报销金额(元)');
			$objActSheet->setCellValue('F1', '审批意见');
			$objActSheet->setCellValue('G1', '审批状态');
			$examine_ = '报销单';
		}else if($_GET['type'] == 4){//差旅单
			$objActSheet->setCellValue('E1', '开始时间');
			$objActSheet->setCellValue('F1', '结束时间');
			$objActSheet->setCellValue('G1', '出差地点');
			$objActSheet->setCellValue('H1', '预算金额(元)');
			$objActSheet->setCellValue('I1', '审批意见');
			$objActSheet->setCellValue('J1', '审批状态');
			$examine_ = '差旅单';
		}else if($_GET['type'] == 5){//借款单
			$objActSheet->setCellValue('E1', '借款金额(元)');
			$objActSheet->setCellValue('F1', '审批意见');
			$objActSheet->setCellValue('G1', '审批状态');
			$examine_ = '借款单';
		}
		//$objActSheet->setCellValue('E1', '请假时长');
		//$objActSheet->setCellValue('F1', '审批意见');
		//$objActSheet->setCellValue('F1', '下一审批人');
		//$objActSheet->setCellValue('G1', '更新时间');
		
		/* if(is_array($examineList)){
			$list = $examineList;
		}else{
			$where['creator_role_id'] = array('in', $this->_permissionRes);
			$where['is_deleted'] = 0;
			$list = M('Examine')->where($where)->select();
		} */
		$list = $examineList;
		if(empty($list)){
			alert('error', '当前导出的数据为空！不能导出！',$_SERVER['HTTP_REFERER']);
		}
		$i = 1;
		foreach ($list as $k => $v) {
			$i++;
			$role_id = array_filter(explode(',',$v['owner_role_id']));
			$where1['role_id'] = array('in',$role_id);
			$role_name = M('user') ->where($where1)->getField('name',true);
			$role_name_str = implode(',',$role_name);
			$creator = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
			$examine = D('RoleView')->where('role.role_id = %d', $v['examine_role_id'])->find();
			$objActSheet->setCellValue('A'.$i, date("Y-m-d", $v['create_time']));
			if($_GET['type'] == 4){
				$objActSheet->setCellValue('B'.$i, $role_name_str);
			}else{
				$objActSheet->setCellValue('B'.$i, $creator['user_name']);
			}
			switch($v['type']){
				case 1:$type = '普通审批';break;
				case 2:$type = '请假单';break;
				case 3:$type = '报销单';break;
				case 4:$type = '差旅单';break;
				case 5:$type = '借款单';break;
			}
			$objActSheet->setCellValue('C'.$i, $type);
			$objActSheet->setCellValue('D'.$i, $v['content']);
			$is_agree = M('ExamineOpinion')->where('examine_id=%d',$v['examine_id'])->order('id desc')->limit(1)->getField('is_agree');
			$examine_status = M('ExamineOpinion')->where('examine_id=%d',$v['examine_id'])->order('id desc')->limit(1)->getField('examine_status');
			$money_sum = M('ExamineExpense')->where('examine_id = %d',$v['examine_id'])->sum('money');
			switch($is_agree){
				case 1: $agree = '同意';break;
				case 2: $agree = '不同意';break;
				default:$agree = '';
			}
			switch($examine_status){ 
				case 0: $e_status = '审批中';break;
				case 1: $e_status = '审批中';break;
				case 2: $e_status = '审批通过';break;
				case 3: $e_status = '审批未通过';break;
			}
			
			if($v['type'] == 1){//普通审批
				$objActSheet->setCellValue('E'.$i, $agree);
				$objActSheet->setCellValue('F'.$i, $e_status);
			}else if($v['type'] == 2){//请假单
				$objActSheet->setCellValue('E'.$i, $v['duration'].'天');
				$objActSheet->setCellValue('F'.$i, $agree);
				$objActSheet->setCellValue('G'.$i, $e_status);
			}else if($v['type'] == 3){//报销单
				$objActSheet->setCellValue('E'.$i, $money_sum);
				$objActSheet->setCellValue('F'.$i, $agree);
				$objActSheet->setCellValue('G'.$i, $e_status);
			}else if($v['type'] == 4){//差旅单
				$objActSheet->setCellValue('E'.$i, date("Y-m-d", $v['start_time']));
				$objActSheet->setCellValue('F'.$i, date("Y-m-d", $v['end_time']));
				$objActSheet->setCellValue('G'.$i, $v['end_address']);
				$objActSheet->setCellValue('H'.$i, $v['budget'].'元');
				$objActSheet->setCellValue('I'.$i, $agree);
				$objActSheet->setCellValue('J'.$i, $e_status);
			}else if($v['type'] == 5){//借款单
				$objActSheet->setCellValue('E'.$i, $v['money'].'元');
				$objActSheet->setCellValue('F'.$i, $agree);
				$objActSheet->setCellValue('G'.$i, $e_status);
			}
		}
		$current_page = intval($_GET['current_page']);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=".$examine_.date('Y-m-d',mktime())."_".$current_page.".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output');
		session('export_status', 0);
	}
	
	public function getCurrentStatus(){
		$this->ajaxReturn(intval(session('export_status')), 'success', 1);
	}
	
	public function checktype(){
		$m_examine_status = M('examine_status');
		$status_list = $m_examine_status ->where('type =0')->select();
		foreach ($status_list as $k=>$v) {
			if ($v['status'] > 18) {
				$status = $v['status']-12;
			} else {
				$status = $v['status'];
			}
            if ($status > 9) {
                $status_list[$k]['img'] = "__PUBLIC__/img/menu/0".$status."-1.png";
            } else {
                $status_list[$k]['img'] = "__PUBLIC__/img/menu/00".$status."-1.png";
            }
			$status_list[$k]['status'] = $status;
		}
		$this->status_list = $status_list;
		$this->display();
	}
	
	//增加、编辑 步骤
	public function step(){
		$m_examine_step = M('ExamineStep');
		$m_position = M('Position');
		$d_role = D('RoleView');
		$m_user = M('User');
		$process_id = intval($_GET['process_id']);
		$step_id = intval($_GET['step_id']);
		if($this->isPost()){
			if($m_examine_step->create()){
				if(intval($_POST['step_id'])){
					//编辑
					$relation = intval($_POST['relation']) ? : '';
					if (!$relation) {
						$step_relation = $m_examine_step->where(array('step_id'=>intval($_POST['step_id'])))->getField('relation');
						if (!$step_relation) {
							$relation = 1;
						} else {
							$relation = $step_relation;
						}
					}
					$m_examine_step->relation = $relation;
					$result = $m_examine_step->save();
					if($result !== false){
						$info = array();
						$role_ids = array_filter(explode(',',trim($_POST['role_id'])));
						$role_list = array();
						foreach($role_ids as $k=>$v){
							$user_info = $m_user->where('role_id = %d',$v)->field('role_id,full_name,thumb_path')->find();
							if (empty($user_info['thumb_path'])) {
								$user_info['thumb_path'] = './Public/img/avatar_default.png';
							}
							$role_list[] = $user_info;
						}
						$info['role_list'] = $role_list;
						$this->ajaxReturn($info,"修改成功",1);
					}else{
						$this->ajaxReturn('',"修改失败",0);
					}
				}elseif(intval($_POST['process_id'])){
					$examine_step_res = $m_examine_step->where('process_id=%d',intval($_POST['process_id']))->select();
					if ($examine_step_res) {
						//添加
						$order_id = $m_examine_step->where('process_id=%d',intval($_POST['process_id']))->max('order_id');
						$m_examine_step->order_id = $order_id+1;
					} else {
						$m_examine_step->order_id = 0;
					}
					$m_examine_step->relation = 1;
					if($id = $m_examine_step->add()){
						//role_id更改逻辑
						$role_ids = array_filter(explode(',',trim($_POST['role_id'])));
						$info['step_id'] = $id;

						$role_list = array();
						foreach($role_ids as $k=>$v){
							$user_info = $m_user->where('role_id = %d',$v)->field('role_id,full_name,thumb_path')->find();
							if (empty($user_info['thumb_path'])) {
								$user_info['thumb_path'] = './Public/img/avatar_default.png';
							}
							$role_list[] = $user_info;
						}
						$info['role_list'] = $role_list;
						$this->ajaxReturn($info,"添加成功",1);
					}else{
						$this->ajaxReturn('',"添加失败",0);
					}
				}else{
					$this->ajaxReturn('',"参数错误",0);
				}
			}else{
				$this->ajaxReturn('',"操作失败",0);
			}
		}else{
			$this->ajaxReturn('',"参数错误",0);
		}
	}
	//删除流程
	public function step_delete(){
		if($this->isAjax()){
			$m_examine_step = M('ExamineStep');
			$step_id = intval($_GET['step_id']);
			$info = $m_examine_step->where('step_id=%d',$step_id)->find();
			if(empty($info)){
				$this->ajaxReturn('',"该信息不存在",0);
			}else{
				if($m_examine_step->where('step_id=%d',$step_id)->delete()){
					$sql = 'update '.C('DB_PREFIX').'examine_step set order_id = order_id-1 where order_id > '.$info['order_id'].' and process_id = '.$info['process_id'];
					mysql_query($sql);
					$this->ajaxReturn('',"删除成功",1);
				}else{
					$this->ajaxReturn('',"删除失败",0);
				}
			}
		}
	}

	/**
	 * 审批统计导出
	 * @param 
	 * @author 
	 * @return 
	 */
	public function analyExcelExport($role_list, $examine_total){
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("pdcrm");    
		$objProps->setLastModifiedBy("pdcrm");    
		$objProps->setTitle("pdcrm Examine");    
		$objProps->setSubject("pdcrm Examine Data");    
		$objProps->setDescription("pdcrm Examine Data");    
		$objProps->setKeywords("pdcrm Examine");    
		$objProps->setCategory("pdcrm");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
		$objActSheet->setCellValue('A1', '员工');
		$objActSheet->setCellValue('B1', '请假（天）');
		$objActSheet->setCellValue('C1', '报销（元）');
		$objActSheet->setCellValue('D1', '差旅（元）');
		$objActSheet->setCellValue('E1', '出差（元）');
		$objActSheet->setCellValue('F1', '借款（元）');
		$objActSheet->setCellValue('G1', '外勤签到（次）');

		$objActSheet->setCellValue('A2', '合计');
		$objActSheet->setCellValue('B2', $examine_total['status_a_total']);
		$objActSheet->setCellValue('C2', $examine_total['status_b_total']);
		$objActSheet->setCellValue('D2', $examine_total['status_c_total']);
		$objActSheet->setCellValue('E2', $examine_total['status_d_total']);
		$objActSheet->setCellValue('F2', $examine_total['status_e_total']);
		$objActSheet->setCellValue('G2', $examine_total['status_f_total']);
		
		$i = 2;
		foreach ($role_list as $k => $v) {
			$i++;
			$objActSheet->setCellValue('A'.$i, $v['full_name']);
			$objActSheet->setCellValue('B'.$i, $v['status_a']);
			$objActSheet->setCellValue('C'.$i, $v['status_b']);
			$objActSheet->setCellValue('D'.$i, $v['status_c']);
			$objActSheet->setCellValue('E'.$i, $v['status_d']);
			$objActSheet->setCellValue('F'.$i, $v['status_e']);
			$objActSheet->setCellValue('G'.$i, $v['status_f']);
		}
		$current_page = intval($_GET['current_page']);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=pdcrm_examine_".date('Y-m-d',mktime()).".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
		session('analy_export_status', 0);
	}

	public function getAnalyCurrentStatus(){
		$this->ajaxReturn(intval(session('analy_export_status')), 'success', 1);	
	}

	/**
	 * 审批类型(添加)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function status_add() {
		//判断权限
		if ($this->isPost()) {
			$m_examine_status = M('ExamineStatus');
			if (!trim($_POST['name'])) {
				alert('error','请填写审批名称！',$_SERVER['HTTP_REFERER']);
			}
			//验重
			$examine_info = $m_examine_status->where('name = %s',trim($_POST['name']))->find();
			if ($examine_info) {
				alert('error', '该审批名称已存在！', $_SERVER['HTTP_REFERER']);
			}
			if ($m_examine_status->create()) {
				$max_status = $m_examine_status->max('status');
				$m_examine_status->status = $max_status+1;
				$m_examine_status->update_time = time();
				if ($m_examine_status->add()) {
					alert('success', '审批类型添加成功，请尽快配置审批字段！', $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', '审批类型添加失败！', $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', '审批类型添加失败！', $_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->alert=parseAlert();
			$this->display();
		}
	}

	/**
	 * 审批类型(编辑)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function status_edit() {
		//判断权限
		$status_id = intval($_REQUEST['status_id']) ? : '';
		$m_examine_status = M('ExamineStatus');
		$examine_status = $m_examine_status->where(array('id'=>$status_id))->find();
		if ($this->isPost()) {
			if (!$status_id) {
				alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
			}
			if (!$examine_status) {
				alert('error','数据不存在或已删除！',$_SERVER['HTTP_REFERER']);
			}
			if ($examine_status['status'] < 7) {
				alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
			}
			//审批未结束，不能编辑
			if (M('Examine')->where(array('type'=>$status_id,'examine_status'=>1))->find()) {
				$this->ajaxReturn('error','此流程有审批正在使用中，不能编辑！',0);
			}
			if (!trim($_POST['name'])) {
				alert('error','请填写审批名称！',$_SERVER['HTTP_REFERER']);
			}
			//验重
			$examine_info = $m_examine_status->where(array('name'=>trim($_POST['name']),'id'=>array('neq',intval($_POST['status_id']))))->find();
			if ($examine_info) {
				alert('error', '该审批名称已存在！', $_SERVER['HTTP_REFERER']);
			}
			unset($_POST['status']);
			if ($m_examine_status->create()) {
				$m_examine_status->update_time = time();
				if ($m_examine_status->where(array('id'=>$status_id))->save()) {
					alert('success', '审批类型修改成功！', $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', '审批类型修改失败！', $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', '审批类型修改失败！', $_SERVER['HTTP_REFERER']);
			}
			$this->alert=parseAlert();
		} else {
			if (!$status_id) {
				echo '<div class="alert alert-error">参数错误！</div>';die;
			}
			if (!$examine_status) {
				echo '<div class="alert alert-error">数据不存在或已删除！</div>';die;
			}
			if ($examine_status['status'] < 7) {
				echo '<div class="alert alert-error">参数错误！</div>';die;
			}
			$this->examine_status = $examine_status;
			$this->display();
		}
	}

	/**
	 * 获取审批类型（pdcrm_more.js）
	 * @param 
	 * @author 
	 * @return 
	 */
	public function getStatusList() {
		$status_list = M('ExamineStatus')->select();
		$this->ajaxReturn($status_list,'success',1);
	}

	/**
	 * 我已审批
	 * @param 
	 * @author 
	 * @return 
	 */
	public function isexamine () {
		$d_examine = D('ExamineView');
		$m_examine = M('Examine');
		$where = array();
		$params = array();
		$order = "update_time desc,examine_id asc";
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc,examine_id asc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc,examine_id asc';
		}
		$below_ids = $this->_permissionRes;
		$module = isset($_GET['module']) ? trim($_GET['module']) : '';
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		switch ($by) {
			case 'today' : $where['create_time'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_time'] =  array('gt',(strtotime(date('Y-m-d', time())) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_time'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'add' : $order = 'create_time desc,examine_id asc';  break;
			case 'update' : $order = 'update_time desc,examine_id asc';  break;
			case 'deleted' : $where['is_deleted'] = 1; break;
			case 'create' : $where['creator_role_id'] = session('role_id'); break;
			case 'subcreate' : $where['creator_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'not_examine' : $where['examine_status'] = 0; break;
			case 'examining' : $where['examine_status'] = array('in',array(0,1)); break;
			case 'examined' : $where['examine_status'] = array('in',array(2,3)); break;
			case 'me_examine' : $where['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or'); break;
			default : 
				if(!session('?admin')){	//非管理员权限限制
					$c_where['creator_role_id'] = array('in',implode(',', $below_ids)); 
					$c_where['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or');
					$c_where['_logic']='or';
					$where['_complex']=$c_where;
				}break;
		}
	
		if (!isset($where['creator_role_id'])) {
			if(!session('?admin')){	//非管理员权限限制
				$c_where['creator_role_id'] = array('in',implode(',', $below_ids)); 
				$c_where['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or');
				$c_where['_logic']='or';
				$where['_complex']=$c_where;
			}
		}
		if (!isset($where['is_deleted'])) {
			$where['is_deleted'] = 0;
		}
		$type = '';
		$examine_status = '';
		//多选类型字段
		$check_field_arr = M('Fields')->where(array('model'=>'examine','form_type'=>'box','setting'=>array('like','%'."'type'=>'checkbox'".'%')))->getField('field',true);
		//高级搜索
		$fields_search = array();
		if(!$_GET['field']){
			if(empty($_GET['type'])){
        		unset($_GET['type']);
        	}
        	if(empty($_GET['examine_status'])){
        		unset($_GET['examine_status']);
        	}
        	$no_field_array = array('act','content','p','condition','listrows','daochu','this_page','current_page','export_limit','desc_order','asc_order','selectexcelxport','by','scene_id','order_field','order_type','examining');
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
								$k = 'examine.create_time';
							}elseif($k == 'update_time'){
								$k = 'examine.update_time';
							}
							//时间段查询
							if ($v['start'] && $v['end']) {
								$where[$k] = array('between',array(strtotime($v['start']),strtotime($v['end'])+86399));
							} elseif ($v['start']) {
								$where[$k] = array('egt',strtotime($v['start']));
							} else {
								$where[$k] = array('elt',strtotime($v['end'])+86399);
							}
						} elseif (($v['value']) != '') {
							if (in_array($k,$check_field_arr)) {
								$where[$k] = field($v['value'],'contains');
							} else {
								if($k =='examine_status'){
									if ($v['value'] == 'all') {
										$where['examine_status'] = array('egt',0);
									} else {
										if ($v['value'] == 4) {
											$where['examine_status'] = 0;
										} else {
											$where['examine_status'] = $v['value'];
										}
									}
									$fields_search['examine_status']['field'] = 'examine_status';
									$fields_search['examine_status']['value'] = $v['value'];
									$examine_status = $v['value'];
								}elseif($k =='type'){
									if ($v['value'] == 'all') {
										$where['type'] = array('egt',0);
									} else {
										$fields_search['type']['field'] = 'type';
										$fields_search['type']['value'] = intval($v['value']);
										$where['type'] = $v['value'];
										$type = $v['value'];
									}								
								}else{
									$where[$k] = field($v['value'], $v['condition']);
								}
							}
						}
					}else{
						if(!empty($v)){
							if ($k == 'type' || $k == 'examine_status') {
								if ($k =='examine_status') {
									if ($v == 'all') {
										$where['examine_status'] = array('egt',0);
									} else {
										if ($v == 4) {
											$where['examine_status'] = 0;
										} else {
											$where['examine_status'] = $v['value'];
										}
									}
									$fields_search['examine_status']['field'] = 'examine_status';
									$fields_search['examine_status']['value'] = $v;
									$examine_status = $v;
								} elseif ($k =='type') {
									if ($v == 'all') {
										$where['type'] = array('egt',0);
									} else {
										$fields_search['type']['field'] = 'type';
										$fields_search['type']['value'] = intval($v);
										$where['type'] = $v;
										$type = $v['value'];
									}
									$type = $v;
								} else {
									$fields_search[$k]['field'] = $k;
									$fields_search[$k]['value'] = $v;
								}
							} else {
								$where[$k] = field($v);
							}
						}
				    }
				}
				if($k == 'examine.create_time'){
					$k = 'create_time';
				}elseif($k == 'examine.update_time'){
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
		if ($_GET['type'] == 'all' || empty($_GET['type'])) {
			$fields_list_data = M('Fields')->where(array('model'=>array('in',array('')),'is_main'=>1))->field('field,form_type')->select();
		} elseif (intval($_GET['type'])) {
			$fields_list_data = M('Fields')->where(array('model'=>array('in',array('','examine')),'is_main'=>1,'type'=>intval($_GET['type'])))->field('field,form_type')->select();
		}
		
		foreach($fields_list_data as $k=>$v){
			$fields_data_list[$v['field']] = $v['form_type'];
		}
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
		$this->fields_search = $fields_search;

		//我已审核的审批（有效的审批记录里面有的）
		if ($_GET['examining'] == 1) {
			$params[] = 'by=me_examine';
			$params[] = 'examining=1';
		}
		
		if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			cookie('listrows', $listrows, 3600 * 24 * 30);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = cookie('listrows') ? cookie('listrows') : 15;
			$params[] = "listrows=".$listrows;
		}
		import("@.ORG.Page");
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;

		$list = $d_examine->where($where)->page($p.','.$listrows)->order($order)->select();
		//审批类型
		$status_list = M('ExamineStatus')->order('status asc')->select();
		$status_arr = array();
		foreach ($status_list as $k=>$v) {
			$status_arr[$v['status']] = $v['name'];
		}
		$m_user = M('User');
		$m_examine_step = M('ExamineStep');
		foreach ($list as $k=>$v) {
			if ($v['content'] && $v['type'] !== '2') {
				$content = $v['content'];
			} else {
				$content = $v['description'];
			}
			$list[$k]['content'] = $content ? msubstr($content,0,20) : '查看详情';
			$list[$k]['type_name'] = $status_arr[$v['type']];
			$list[$k]['owner'] = getUserByRoleId($v['owner_role_id']);
			$examine_role_list = array();
			$examine_role_ids = '';
			if ($v['examine_role_id'] && $v['examine_status'] < 2) {
				$examine_role_list = $m_user->where(array('role_id'=>array('in',explode(',',$v['examine_role_id']))))->field('thumb_path,full_name,role_id')->select();
			} elseif ($v['examine_status'] < 2) {
				$examine_role_ids = $m_examine_step->where(array('order_id'=>$v['order_id'],'process_id'=>$v['type']))->getField('role_id');
				$examine_role_list = $m_user->where(array('role_id'=>array('in',explode(',',$examine_role_ids))))->field('thumb_path,full_name,role_id')->select();
			}
			$list[$k]['examine_role_list'] = $examine_role_list;
		}
		//合计
		$all_days = 0;
		$all_money = 0.00;
		if (in_array(intval($_GET['type']),array(2,3,4,6))) {
			if (intval($_GET['type']) == 2) {
				$where['type'] = 2;
				$where['examine_status'] = array('neq',3);
				$all_days = $m_examine->where($where)->count('duration');
			}
			if (in_array(intval($_GET['type']),array('3','4','6'))) {
				$where['type'] = intval($_GET['type']);
				$where['examine_status'] = array('neq',3);
				$all_money = $m_examine->where($where)->count('budget');
				$all_money = number_format($all_money,2);
			}
		}
		$count = $d_examine->where($where)->count();
		$p_num = ceil($count/$listrows);
		if ($p_num < $p) {
			$p = $p_num;
		}
		$Page = new Page($count,$listrows);
		if (!empty($_REQUEST['by'])){
			$params['by'] = 'by=' . trim($_REQUEST['by']);
		}
		
		$this->parameter = implode('&', $params);
		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}
		$Page->parameter = implode('&', $params);
		$show = $Page->show();		
		$this->assign('page',$show);
		
		$this->listrows = $listrows;
		$this->assign('list',$list);
		$this->assign("count",$count);
		
		//审批类型
		$this->status_list = M('examine_status') ->where('type=0')->select();
		$this->type = $type;
		$this->all_money = $all_money;
		$this->all_days = $all_days;
		$this->examine_status = $examine_status;
		if (intval($_GET['type']) > 6 || $_GET['type']['value'] > 6) {
			if (is_array($_GET['type'])) {
				$type = intval($_GET['type']['value']) ? : '';
			} else {
				$type = intval($_GET['type']) ? : '';
			}
			//自定义字段
			$this->field_array = getIndexFields('examine',$type);
			$this->field_list = getMainFields('examine',$type);
		}
		$this->alert = parseAlert();
		$this->display();
	}
}