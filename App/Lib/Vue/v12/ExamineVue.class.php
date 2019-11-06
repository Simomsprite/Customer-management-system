<?php
/**
 *审批相关
 **/
class ExamineVue extends Action{
	/**
	 *用于判断权限
	 *@permission 无限制
	 *@allow 登录用户可访问
	 *@other 其他根据系统设置
	 **/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('examinestatus','check_list','add_examine','examinestatus','checkPer')
		);
		B('VueAuthenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
		
		Global $role;
		$this->role = $role;
		Global $roles;
		$this->roles = $roles;

		if($roles == 2){
			$this->ajaxReturn('','您没有此权限！',-2);
		}

		if($roles == 3){
			$this->ajaxReturn('','请先登录！',-1);
		}
	}

	/**
	 * 审批动态
	 * @param 
	 * @author 
	 * @return 
	 */
	public function dynamic() {
		if ($this->isPost()) {
			$m_examine = M('Examine');
			$where = array();
			$where['is_deleted'] = 0;
			$where['examine_status'] = array('lt',2);

			//我发起的
			$map1['_complex'] = $where;
			$map1['creator_role_id']  = session('role_id');
			$create_count = $m_examine->where($map1)->count();
			$data['create_count'] = $create_count ? $create_count : '0';

			//我的审批
			$map2['_complex'] = $where;
			$map2['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or');
			// $map2['examine_role_id']  = session('role_id');
			$examine_count = $m_examine->where($map2)->count();
			$data['examine_count'] = $examine_count ? $examine_count : '0';

			//审批模块是否启用
			$type_list = M('ExamineStatus')->where(array('type'=>0))->field('status,name,type')->select();
			$data['type_list'] = $type_list ? : array();
			//权限查询
			$permission_list = apppermission('examine','add');
			if($permission_list){
				$data['permission_list'] = $permission_list;
			}else{
				$data['permission_list'] = array();
			}
			$this->ajaxReturn($data,'success',1);
		}else{
			$this->ajaxReturn('','非法请求',0);
		}
	}

	/**
	 * 审批列表
	 * @param 
	 * @author 
	 * @return 
	 */
	public function index(){
		if ($this->isPost()) {
			$m_examine = M('Examine');
			$where = array();
			$where['is_deleted'] = 0;
			$order = "update_time desc,examine_status asc";
			$below_ids = getPerByAction('examine','index');
			$by = $_POST['by'] ? trim($_POST['by']) : 'create';
			switch ($by) {
				case 'today' : $where['create_time'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
				case 'week' : $where['create_time'] =  array('gt',(strtotime(date('Y-m-d', time())) - (date('N', time()) - 1) * 86400)); break;
				case 'month' : $where['create_time'] = array('gt',strtotime(date('Y-m-01', time()))); break;
				case 'add' : $order = 'create_time desc,examine_id asc';  break;
				case 'update' : $order = 'update_time desc,examine_id asc';  break;
				case 'deleted' : $where['is_deleted'] = 1; break;
				case 'subcreate' : $where['creator_role_id'] = array('in',implode(',', $below_ids)); break;
				case 'not_examine' : $where['examine_status'] = 0; break;
				case 'examining' : $where['examine_status'] = array('in',array(0,1)); break;
				case 'me_examine' : $where['examine_role_id'] = array(array('eq',session('role_id')),array('like','%,'.session('role_id').',%'),'or');
									$where['examine_status'] = array('in',array(0,1));
									break;//待我审批
				case 'create' : $where['creator_role_id'] = session('role_id'); 
								$order = "update_time desc";
								break;//我发起的
			}
			//我已审批的（包含我参与过的审批）
			$m_examine_check = M('ExamineCheck');
			if ($by == 'examined') {
				$map['role_id'] = session('role_id');
				$examine_ids = $m_examine_check->where($map)->getField('examine_id',true);
				$where['examine_id'] = array('in',implode(',',$examine_ids));
			}


			$type = '';
			$examine_status = '';
			//多选类型字段
			$check_field_arr = M('Fields')->where(array('model'=>'examine','form_type'=>'box','setting'=>array('like','%'."'type'=>'checkbox'".'%')))->getField('field',true);
			//高级搜索
			$fields_search = array();
			if(!$_POST['field']){
				if(empty($_POST['type'])){
	        		unset($_POST['type']);
	        	}
	        	if(empty($_POST['examine_status'])){
	        		unset($_POST['examine_status']);
	        	}
	        	$no_field_array = array('act','content','p','condition','listrows','daochu','this_page','current_page','export_limit','desc_order','asc_order','selectexcelxport','by','scene_id','order_field','order_type','examining');
				foreach($_POST as $k=>$v){
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
										if ($v['value'] == '0') {
											$where['examine_status'] = array('egt',0);
										} else {
											if ($v['value'] == 4) {
												$where['examine_status'] = 0;
											} else {
												$where['examine_status'] = $v['value'];
											}
										}
									}elseif($k =='type'){
										if ($v['value'] == '0') {
											$where['type'] = array('egt',0);
										} else {
											$where['type'] = $v['value'];
										}								
									}else{
										$where[$k] = field($v['value'], $v['condition']);
									}
								}
							}
						}else{
							if(!empty($v)){
								$where[$k] = field($v);
							}
					    }
					}
				}
			}
			$p = isset($_POST['p']) ? intval($_POST['p']) : 1 ;

			//获取查询条件信息
			if($p == 1 && $_POST['search'] == ''){
				$where_field = array();
				$where_field['model'] = array('in',array('examine'));
				$where_field['is_main'] = '1';
				// $where_field['field'] = array('not in',array('customer_id','business_id','delete_role_id','is_deleted','delete_time'));
				$fields_list = M('Fields')->where($where_field)->field('name,field,setting,form_type,input_tips,status')->order('order_id')->select();
				foreach($fields_list as $k=>$v){
					if ($v['setting']) {
						//将内容为数组的字符串格式转换为数组格式
						eval("\$setting = ".$v['setting'].'; ');
						$setting_arr = array();
						$data_arr = array();
						foreach ($setting['data'] as $key=>$val) {
							$key = $key-1;
							$data_arr[$key]['key'] = $val;
							$data_arr[$key]['value'] = $val;
						}
						$fields_list[$k]['setting'] = $data_arr;
						$fields_list[$k]['form_type'] = $setting['type'] == 'checkbox' ? 'checkbox' : 'select';
					} elseif ($v['field'] == 'owner_role_id' || $v['field'] == 'create_role_id') {
						$fields_list[$k]['form_type'] = 'user';
					} else {
						$fields_list[$k]['setting'] = '';
					}
				}
				$type_setting = array('0'=>array('key'=>0,'value'=>'全部'));
				$examine_status_list = M('ExamineStatus')->field('id,name')->select();
				foreach ($examine_status_list as $k=>$v) {
					$type_setting[] = array('key'=>$v['id'],'value'=>$v['name']);
				}
				$examine_status_setting = array(
											'0'=>array('key'=>0,'value'=>'全部'),
											'1'=>array('key'=>4,'value'=>'待审'),
											'2'=>array('key'=>1,'value'=>'审批中'),
											'3'=>array('key'=>2,'value'=>'审批通过'),
											'4'=>array('key'=>3,'value'=>'审批拒绝'),
											);
				$fields_list_arr = array();
				//追加其他字段信息
				$fields_list_arr['examine_status'] = array('field'=>'examine_status','form_type'=>'box','input_tips'=>'','name'=>'审批状态','setting'=>$examine_status_setting);
				$fields_list_arr['examine_type'] = array('field'=>'type','form_type'=>'box','input_tips'=>'','name'=>'审批类型','setting'=>$type_setting);
				$fields_list_arr['fields_list'] = $fields_list ? $fields_list : array();
			}

			$examine_list = $m_examine->where($where)->page($p.',10')->order($order)->field('examine_id,create_time,type,examine_status,creator_role_id,owner_role_id,examine_role_id,content,start_time,end_time,money,budget,end_address,duration,description')->select();

			$m_user = M('User');
			$d_user = D('User');
			$m_examine_check = M('ExamineCheck');
			$m_examine_status = M('ExamineStatus');
			foreach ($examine_list as $k=>$v) {
				$temp_val = $d_user->get_full_name(array($v['creator_role_id']));
				$user_info = $temp_val[$v['creator_role_id']];
				$examine_list[$k]['user_info'] = $user_info ? : array();
				$status_name = '';
				$status_name = $m_examine_status->where(array('status'=>$v['type']))->getField('name');
				$examine_list[$k]['status_name'] = $status_name ? : '';
				//审批权限
				$view = 1;
				$edit = 1;
				$delete = 1;
				
				//详情权限
				$examine_check = array();
				$examine_check = $m_examine_check->where(array('role_id'=>session('role_id'),'examine_id'=>$v['examine_id']))->find();
				if (!in_array($v['creator_role_id'],getPerByAction('examine','view')) && !$examine_check) {
					if (strpos('pd_'.$v['examine_role_id'],',')) {
						if (!in_array(session('role_id'),array_filter(explode(',',$v['examine_role_id'])))) {
							$view = 0;
						}
					} elseif ($v['examine_role_id'] != session('role_id')) {
						$view = 0;
					}
				}
				//编辑、删除权限
				if ($v['examine_status'] != 0) {
					$edit = 0;
					$delete = 0;
				} else {
					if (!in_array($v['creator_role_id'],getPerByAction('examine','edit'))) {
						$edit = 0;
					}
					if (!in_array($v['creator_role_id'],getPerByAction('examine','delete'))) {
						$delete = 0;
					}
				}				
				$examine_list[$k]['permission']['view'] = $view;
				$examine_list[$k]['permission']['edit'] = $edit;
				$examine_list[$k]['permission']['delete'] = $delete;
			}
			if ($by == 'examined') {
				foreach ($examine_list as $k=>$v) {
					//审批意见
					$examine_check_info = $m_examine_check->where('examine_id = %d',$v['examine_id'])->find();
					//审批时间
					$examine_list[$k]['examine_time'] = $examine_check_info['check_time'];
					//审批结果
					$examine_list[$k]['is_agree'] = $examine_check_info['is_checked'];
				}
			}
			$count = $m_examine->where($where)->count();
			$page = ceil($count/10);

			$data['list'] = $examine_list ? $examine_list : array();
			$data['fields_list'] = $fields_list_arr ? $fields_list_arr : array();
			$data['page'] = $page;
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		}
	}

	/**
	 * 审批添加
	 * @param 
	 * @author 
	 * @return 
	 */
	public function add(){
		if ($this->isPost()) {
			$m_examine = M('Examine');
			$m_examine_status = M('ExamineStatus');
			$params = $_POST;
			if (!is_array($params)) {
				$this->ajaxReturn('','非法的数据格式！',0);
			}
			$type = $_POST['type'] ? intval($_POST['type']) : '';
			if (!$type) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$examine_role_id = trim($params['examine_role_id']);
			if (!$examine_role_id) {
				$this->ajaxReturn('','请选择下一审批人！',0);
			}
			$examine_status = $m_examine_status->where(array('status'=>$type))->find();
			$type_name = $examine_status['name'];
			//审批流程排序ID
			$min_order_id = M('ExamineStep')->where(array('process_id'=>$type))->min('order_id');
			if ($type > 6) {
 				//自定义审批
 				$d_examine = D('Examine');
				$d_examine_data = D('ExamineData');
				$field_list = M('Fields')->where(array('model'=>'examine','in_add'=>1,'status'=>$type))->order('order_id')->select();
				foreach ($field_list as $v){
					if ($v['is_validate'] == 1) {
						if ($v['is_null'] == 1) {
							if ($params[$v['field']] == '') {
								$this->ajaxReturn('',$v['name'].'不能为空',0);
							}
						}
						if ($v['is_unique'] == 1) {
							$res = validate('customer',$v['field'],$params[$v['field']]);
							if ($res) {
								$this->ajaxReturn('',$v['name'].':'.$params[$v['name']].'已存在',0);
							}
						}
					}
					if ($params[$v['field']]) {
						switch ($v['form_type']) {
							case 'address':
								$params[$v['field']] = $params[$v['field']] ? implode(chr(10),json_decode($params[$v['field']])) : '';
								break;
							case 'datetime':
								$params[$v['field']] = $params[$v['field']] ? strtotime($params[$v['field']]) : '';
								break;
							case 'box':
								eval('$field_type = '.$v['setting'].';');
								if($field_type['type'] == 'checkbox'){
									$a = $params[$v['field']] ? array_filter(json_decode($params[$v['field']])) : '';
									$params[$v['field']] = !empty($a) ? implode(chr(10),$a) : '';
								}
								break;
							default : break;
						}
					}
				}
				if ($d_examine->create($params)) {
					if ($d_examine_data->create($params) !== false) {
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
								$this->ajaxReturn('','添加成功！',1);
							} else {
								$this->ajaxReturn('','附表添加失败，请重试！',0);
							}
						} else {
							$this->ajaxReturn('','添加失败，请重试！',0);
						}
					} else {
						$this->ajaxReturn('',$d_examine_data->getError(),0);
					}
				} else {
					$this->ajaxReturn('',$d_examine->getError(),0);
				}
			} else {
				if (!$params['content']) {
					$this->ajaxReturn('','请填写"'.$type_name.'"内容！',0);
				}
				if ($m_examine->create($params)) {
					$m_examine->creator_role_id = session('role_id');
					$m_examine->owner_role_id = session('role_id');
					$m_examine->create_time = time();
					$m_examine->update_time = time();
					$m_examine->type = $type;

					if ($examine_id = $m_examine->add()) {
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
								if ($_POST['type'] =='4') {
									$file_travel['start_time'] = $v['start_time'];
									$file_travel['end_address'] = $v['end_address'];
									$file_travel['end_time'] = $v['end_time'];
									$file_travel['vehicle'] = $v['vehicle'];
									$file_travel['duration'] = $v['duration'];
								}
								$file_travel['money'] = $v['money'];
								$file_travel['description'] = $v['description'];
								$m_examine_travel->add($file_travel);
							}
						}
						actionLog($examine_id);

						$creator = getUserByRoleId(session('role_id'));
						$message_content = $creator['user_name'].'于'.date('Y-m-d',time()).'创建的'.$type_name.'等待您的批复！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.session('role_id').'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$examine_id).'">'.$_POST['content'].'</a>';
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

						$this->ajaxReturn('','添加成功！',1);
					}else{
						$this->ajaxReturn('','添加失败！',0);
					}
				}else{
					$this->ajaxReturn('','添加失败！',0);
				}
			}
		}
	}

	/**
	 * 审批编辑
	 * @param 
	 * @author 
	 * @return 
	 */
	public function edit(){
		if ($this->isPost()) {
			$params = $_POST;
			if(!is_array($params)){
				$this->ajaxReturn('','非法的数据格式',0);
			}
			$examine_id = $params['id'] ? intval($params['id']) : 0;
			if (!$examine_id) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$m_examine = M('Examine');
			$m_examine_travel = M('ExamineTravel');
			$m_examine_status = M('ExamineStatus');
			$examine_info = $m_examine->where('examine_id = %d',$examine_id)->find();
			if (!$examine_info) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			} else {
				if (in_array($examine_info['examine_status'],array('1','2'))) {
					$this->ajaxReturn('','当前状态不允许编辑！',0);
				} elseif (!in_array($examine_info['creator_role_id'],$this->_permissionRes)) {
					$this->ajaxReturn('','您没有此权利！',-2);
				}
			}
			$examine_role_id = trim($params['examine_role_id']);
			if (!$examine_role_id) {
				$this->ajaxReturn('','请选择下一审批人！',0);
			}
			$type = $examine_info['type'];
			$type_name = $m_examine_status->where(array('status'=>$examine_info['type']))->getField('name');
			//审批流程排序ID
			$min_order_id = M('ExamineStep')->where(array('process_id'=>$type))->min('order_id');
			if ($type > 6) {
				//自定义审批
				$examine_info = D('ExamineView')->where('examine.examine_id = %d',$examine_id)->find();
				$d_examine = D('Examine');
				$d_examine_data = D('ExamineData');
				$field_list = M('Fields')->where(array('model'=>'examine','status'=>$examine_info['type']))->order('order_id')->select();
				foreach ($field_list as $v){
					if ($v['is_validate'] == 1) {
						if ($v['is_null'] == 1) {
							if ($params[$v['field']] == '') {
								$this->ajaxReturn('',$v['name'].'不能为空',0);
							}
						}
						if ($v['is_unique'] == 1) {
							$res = validate('customer',$v['field'],$params[$v['field']]);
							if ($res) {
								$this->ajaxReturn('',$v['name'].':'.$params[$v['name']].'已存在',0);
							}
						}
					}
					if ($params[$v['field']]) {
						switch ($v['form_type']) {
							case 'address':
								$params[$v['field']] = $params[$v['field']] ? implode(chr(10),json_decode(($params[$v['field']]))) : '';
								break;
							case 'datetime':
								$params[$v['field']] = $params[$v['field']] ? strtotime($params[$v['field']]) : '';
								break;
							case 'box':
								eval('$field_type = '.$v['setting'].';');
								if($field_type['type'] == 'checkbox'){
									$a = $params[$v['field']] ? array_filter(json_decode($params[$v['field']])) : '';
									$params[$v['field']] = !empty($a) ? implode(chr(10),$a) : '';
								}
								break;
							default : break;
						}
					}
				}
				if($d_examine->create($params)){
					$d_examine->examine_status = 0;
					$d_examine->order_id = $min_order_id ? : 0;
					$d_examine->update_time = time();
					if($d_examine_data->create($params) !== false){
						$a = $d_examine->where('examine_id = %d',$examine_id)->save();
						$d_examine_data->examine_id = $examine_id;
						if (M('ExamineData')->where('examine_id = %d',$examine_id)->find()) {
							$b = $d_examine_data->where('examine_id = %d',$examine_id)->save();
						} else {
							$b = $d_examine_data->where('examine_id = %d',$examine_id)->add();
						}

						//附件编辑
						$m_examine_file = M('ExamineFile');
						if ($_POST['file']) {
							$old_file_arr = $m_examine_file->where(array('examine_id'=>$examine_id))->getField('file_id', true);
							$new_file_arr = $_POST['file'];
							$del_file_arr = array_diff($old_file_arr, $new_file_arr);
							foreach ($del_file_arr as $v) {
								@unlink($v['file_path']);
							}
							$del_res = $m_examine_file->where(array('examine_id'=>$examine_id))->delete();

							foreach ($_POST['file'] as $v) {
								$file_data = array();
								$file_data['examine_id'] = $examine_id;
								$file_data['file_id'] = $v;
								$m_examine_file->add($file_data);
							}
						} else {
							if (isset($_POST['file'])) {
								$examine_files = $m_examine_file->where(array('examine_id'=>$examine_id))->getField('file_id', true);
								foreach ($examine_files as $v) {
									@unlink($v['file_path']);
								}
							}
						}

						if($a && $b !== false) {
							$type_name = $examine_status['name'];
							$creator = getUserByRoleId(session('role_id'));
							$message_content = $creator['user_name'].'于'.date('Y-m-d',time()).'编辑了'.$type_name.'等待您的批复！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.session('role_id').'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$_POST['examine_id']).'">查看详情</a>';

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
							$this->ajaxReturn('','编辑成功！',1);
						} else {
							$this->ajaxReturn('','修改失败，请重试!',0);
						}
					}else{
						$this->ajaxReturn('',$d_examine_data->getError(),0);
					}
				}else{
					$this->ajaxReturn('',$d_examine->getError(),0);
				}
			} else {
				if (!$params['content']) {
					$this->ajaxReturn('','请填写"'.$type_name.'"内容!',0);
				}
				if ($m_examine->create($params)) {
					$m_examine->update_time = time();
					$m_examine->examine_status = 0;
					$m_examine->order_id = $min_order_id ? : 0;
					if ($m_examine->where('examine_id = %d',$examine_id)->save()) {
						$operation_flag = true;
						foreach($_POST['travel'] as $v){
							if(!empty($v['money'])){
								$file_travel = array();
								$file_travel['examine_id'] = $examine_id;
								$file_travel['start_address'] = $v['start_address'];
								if($_POST['type'] =='4'){
									$file_travel['start_time'] = $v['start_time'];
									$file_travel['end_address'] = $v['end_address'];
									$file_travel['end_time'] = $v['end_time'];
									$file_travel['vehicle'] = $v['vehicle'];
									$file_travel['duration'] = $v['duration'];
								}
								$file_travel['money'] = $v['money'];
								$file_travel['description'] = $v['description'];
								//在编辑时，如果又添加，根据是否存在id来进行编辑或添加
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

						//附件编辑
						$m_examine_file = M('ExamineFile');
						if ($_POST['file']) {
							$old_file_arr = $m_examine_file->where(array('examine_id'=>$examine_id))->getField('file_id', true);
							$new_file_arr = $_POST['file'];
							$del_file_arr = array_diff($old_file_arr, $new_file_arr);
							foreach ($del_file_arr as $v) {
								@unlink($v['file_path']);
							}
							$del_res = $m_examine_file->where(array('examine_id'=>$examine_id))->delete();

							foreach ($_POST['file'] as $v) {
								$file_data = array();
								$file_data['examine_id'] = $examine_id;
								$file_data['file_id'] = $v;
								$m_examine_file->add($file_data);
							}
						} else {
							if (isset($_POST['file'])) {
								$examine_files = $m_examine_file->where(array('examine_id'=>$examine_id))->getField('file_id', true);
								foreach ($examine_files as $v) {
									@unlink($v['file_path']);
								}
							}
						}

						$creator = getUserByRoleId(session('role_id'));
						$message_content = $creator['user_name'].'于'.date('Y-m-d',time()).'编辑了'.$type_name.'等待您的批复！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.session('role_id').'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$_POST['id']).'">'.$params['content'].'</a>';
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
						$this->ajaxReturn('','修改成功！',1);
					} else {
						$this->ajaxReturn('','修改失败！',0);
					}
				} else {
					$this->ajaxReturn('','修改失败！',0);
				}
			}
		}
	}

	/**
	 * 审批详情
	 * @param 
	 * @author 
	 * @return 
	 */
	public function view(){
		if ($this->isPost()) {
			$examine_id = $_POST['id'] ? intval($_POST['id']) : 0;
			$m_examine = M('Examine');
			$m_user = M('User');
			$d_user = D('User');

			//审批权限判断
			$m_examine_step = M('ExamineStep');
			$m_examine_check = M('ExamineCheck');
			$m_examine_status = M('ExamineStatus');

			$examine_info = $m_examine->where('examine_id = %d',$examine_id)->find();
			if (!$examine_info) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			}
			if(!$this->checkPer($examine_id)){
				$this->ajaxReturn('','您没有此权利！',-2);
			}
			//申请人
			$temp_val = $d_user->get_full_name(array($examine_info['owner_role_id']));
			$examine_owner = $temp_val[$examine_info['owner_role_id']];


			if ($examine_info['type'] > 6) {
				$examine_info = D('ExamineView')->where('examine.examine_id = %d',$examine_id)->find();
				$data_list = array();
				//申请事由
				$data_list[0]['field'] = 'content';
				$data_list[0]['name'] = '申请事由';
				$data_list[0]['form_type'] = 'textarea';
				$data_list[0]['val'] = $examine_info['content'];
				$data_list[0]['type'] = 0;
				$data_list[0]['id'] = '';
				//查询固定信息
				//取得字段列表
				$where = array();
				$where['model'] = 'examine';
				$where['status'] = $examine_info['type'];
				$field_list = M('Fields')->where($where)->order('order_id')->select();
				$i = 1;
				foreach ($field_list as $k=>$v) {
					$field = trim($v['field']);
					$data_list[$i]['field'] = $field;
					$data_list[$i]['name'] = trim($v['name']);
					if ($v['setting']) { 
						//将内容为数组的字符串格式转换为数组格式
						eval("\$setting = ".$v['setting'].'; ');
						$data_list[$i]['form_type'] = $setting['type'] == 'checkbox' ? 'checkbox' : 'select';
					} else {
						$data_list[$i]['form_type'] = $v['form_type'];
					}
					$data_a = trim($examine_info[$v['field']]);
					if($v['form_type'] == 'address') {
						$address_array = str_replace(chr(10),' ',$data_a);
						$data_list[$i]['val'] = $address_array;
						$data_list[$i]['type'] = 0;
					} else {
						$data_list[$i]['val'] = $data_a;
						$data_list[$i]['type'] = 0;
					}
					$data_list[$i]['id'] = '';
					$i++;
				}
			} else {
				if ($examine_info['type'] == 3 || $examine_info['type'] == 4) {
					$travel_list = M('ExamineTravel')->where('examine_id = %d',$examine_id)->select();
					$examine_info['travel'] = $travel_list ? $travel_list : array();
				}
			}

			//附件
			$file_id_array = M('ExamineFile')->where('examine_id = %d',$examine_id)->getField('file_id',true);
			$file_list = M('File')->where('file_id in (%s)',implode(',',$file_id_array))->select();
			foreach ($file_list as $key => $value) {
				$file_type = '';
				$file_type = end(explode('.',$value['name']));
				$file_list[$key]['file_type'] = $file_type;
				$file_list[$key]['size'] = round($value['size']/1024,2).'Kb';
				if (intval($value['size']) > 1024*1024) {
					$file_list[$key]['size'] = round($value['size']/(1024*1024),2).'Mb';
				}
				$file_list[$key]['file_path'] = headPathHandle($value['file_path'], 1);
				$file_list[$key]['file_path_thumb'] = headPathHandle($value['file_path_thumb'], 1);
			}
			$examine_info['file_list'] = $file_list ? $file_list : array();

			if ($examine_info['examine_status'] == 2 || $examine_info['examine_status'] == 3) {
				// $this->ajaxReturn('','该审批已经结束！',0);
				$add_examine = 0;
			} else {
				//当前审批流程(是否有审批权限)
				$examine_status = $m_examine_status->where(array('status'=>$examine_info['type']))->find();
				if ($examine_status['option'] == 1) {
					$is_add_examine = 1;
					$examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$examine_info['order_id']))->find();
					//当前步骤已审批role_id
					$is_examine_role_ids = $m_examine_check->where(array('examine_id'=>$examine_id,'order_id'=>$examine_info['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);

					if (in_array(session('role_id'),$is_examine_role_ids)) {
						// $this->ajaxReturn('','您已审核，请勿重复操作！',0);
						$is_add_examine = 0;
					}
					//权限判断（并、或关系的，必须是规定审批人，超级管理员也无此权限）
					if (!in_array(session('role_id'),explode(',',$examine_step_info['role_id']))) {
						// $this->ajaxReturn('','您没有审核权限或审批已通过！',0);
						$is_add_examine = 0;
					}
					$add_examine = $is_add_examine;
				} else {
					if (session('?admin') || ($examine_info['examine_role_id'] == session('role_id') || in_array(session('role_id'),array_filter(explode(',',$examine_info['examine_role_id']))))) {
						$add_examine = 1;
					}
				}
			}

			//申请人
			$d_user = D('User');
			$examine_info['owner_role_info'] = $examine_owner;
			//审批人
			if ($examine_status['option'] == 1) {
				$examine_role_id = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$examine_info['order_id']))->getField('role_id');
			} else {
				$examine_role_id = $examine_info['examine_role_id'];
			}
			$examine_arr = $d_user->get_full_name(explode(',',$examine_role_id));
			$examine_info['examine_role_list'] = $examine_arr ? : array();

			//审批流
			$step_list = array();
			if ($examine_status['option'] == 1) {
				//审批流
				$step_list = $m_examine_step->where('process_id=%d',$examine_info['type'])->order('order_id')->select();
				$d_user = D('User');
				foreach ($step_list as $k=>$v) {
					$role_ids = explode(',',$v['role_id']);
					$role_list = $d_user->get_full_name($role_ids);
					if ($examine_info['order_id'] >= $v['order_id']) {
						$check_role_arr = array();
						//审批意见
						$check_role_arr = $m_examine_check->where(array('examine_id'=>$examine_info['examine_id'],'order_id'=>$v['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);
						$is_checked = '';
						$is_checked_name = '';
						if ($examine_info['order_id'] == $v['order_id']) {
							$is_checked = 1;
							$is_checked_name = '待审';
						}
						foreach ($role_list as $key=>$val) {
							if ($check_role_arr && in_array($val['role_id'],$check_role_arr)) {
								$role_list[$key]['is_checked'] = 2;
								$role_list[$key]['is_checked_name'] = '同意';
							} else {
								$role_list[$key]['is_checked_name'] = $is_checked_name;
								$role_list[$key]['is_checked'] = $is_checked;
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
			}
			$data['step_list'] = $step_list ? : array();

			//返回编辑、删除权限
			if (!in_array($examine_info['examine_status'],array('1','2'))) {
				$edit = 1;
				$delete = 1;
				if(!in_array($examine_info['creator_role_id'],getPerByAction('examine','edit')) && !session('?admin')){
					$edit = 0;
				}
				if(!in_array($examine_info['creator_role_id'],getPerByAction('examine','delete')) && !session('?admin')){
					$delete = 0;
				}
				$data['permission'] = array('edit'=>$edit,'delete'=>$delete);
			} else {
				$data['permission'] = array('edit'=>0,'delete'=>0);
			}
			$examine_info['add_examine'] = $add_examine ? $add_examine : 0;			
			
			if ($examine_info['type'] > 6) {
				$res_info['create_time'] = $examine_info['create_time'];
				$res_info['owner_role_info'] = $examine_owner;
				$data['data'] = $data_list ? : array();
				$data['add_examine'] = $add_examine;
				$data['examine_info'] = $res_info;
				$data['file_list'] = $file_list;
			} else {
				$data['data'] = $examine_info;
			}
			$data['type'] = $examine_info['type']; //自定义
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		}
	}

	/**
	 * 审批删除
	 * @param 
	 * @author 
	 * @return 
	 */
	public function delete() {
		if ($this->isPost()) {
			$examine_id = $_POST['id'] ? intval($_POST['id']) : 0;
			if (!$examine_id) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$m_examine = M('Examine');
			$examine_info = $m_examine->where('examine_id = %d',$examine_id)->find();
			if (!$examine_info) {
				$this->ajaxReturn('','数据不存在或已删除！',0);
			} else {
				if ($examine_info['examine_status'] != 0) {
					$this->ajaxReturn('','当前状态不允许删除',0);
				} elseif (!in_array($examine_info['creator_role_id'],$this->_permissionRes)) {
					$this->ajaxReturn('','您没有此权利！',-2);
				}
			}
			// $data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
			$res = $m_examine->where('examine_id = %d',$examine_id)->delete();
			if ($res) {
				actionLog($examine_id);
				$this->ajaxReturn('','删除成功！',1);
			} else {
				$this->ajaxReturn('','删除失败！',0);
			}
		}
	}

	/**
	 * 添加审批意见
	 * @param 
	 * @author 
	 * @return 
	 */
	public function add_examine() {
		if ($this->isPost()) {
			$examine_id = intval($_POST['id']) ? : '';
			$m_examine = M('Examine');
			$m_examine_step = M('ExamineStep');
			$m_examine_check = M('ExamineCheck');
			$m_examine_status = M('ExamineStatus');
			$m_user = M('User');

			$examine_info = $m_examine->where(array('examine_id'=>$examine_id))->find();
			if (!$examine_id || !$examine_info) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$examine_status = $m_examine_status->where(array('status'=>$examine_info['type']))->find();
			if ($examine_info['examine_status'] == 2 || $examine_info['examine_status'] == 3) {
				$this->ajaxReturn('','该审批已经结束！',0);
			}
			if ($examine_status['option'] == 1) {
				$examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$examine_info['order_id']))->find();
				//当前步骤已审批role_id
				$is_examine_role_ids = $m_examine_check->where(array('examine_id'=>$examine_id,'order_id'=>$examine_info['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);

				if (in_array(session('role_id'),$is_examine_role_ids)) {
					$this->ajaxReturn('','您已审核，请勿重复操作！',0);
				}
				//权限判断（并、或关系的，必须是规定审批人，超级管理员也无此权限）
				if ($examine_step_info['relation'] > 0) {
					if (!in_array(session('role_id'),explode(',',$examine_step_info['role_id']))) {
						$this->ajaxReturn('','您没有审核权限或审批已通过！',0);
					}
				} else {
					if (!session('?admin') && !in_array(session('role_id'),explode(',',$examine_step_info['role_id']))) {
						$this->ajaxReturn('','您没有审核权限或审批已通过！',0);
					}
				}
			} else {
				//权限判断
				if (!session('?admin') && ($examine_info['examine_role_id'] != session('role_id') && !in_array(session('role_id'),array_filter(explode(',',$examine_info['examine_role_id']))))) {
					$this->ajaxReturn('','您没有审核权限！',0);
				}
			}

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
						//并
						if ($is_check_role && !array_diff(array_filter(explode(',',$examine_step_info['role_id'])),$is_check_role)) {
							$examine_is_end = 1;
						}
					} elseif ($examine_step_info['relation'] == 2) {
						//或
						$examine_is_end = 1;
					} else {
						$examine_is_end = 1;
					}
					//下一审批人信息
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
						$next_role_id = $is_check_role ? array_merge(array_diff(array_filter(explode(',',$examine_step_info['role_id'])),$is_check_role)) : array_filter(explode(',',$examine_step_info['role_id']));
						$next_role_id = $next_role_id ? ','.implode(',',$next_role_id).',' : '';
					}
				} else {
					$next_role_id = trim($_POST['examine_role_id']);
					$next_order_id = 0;
				}
				if ($_POST['is_agree'] == 1) {
					if ($_POST['examine_status'] != 2 && $next_role_id == null && $examine_status['option'] !== 1) {
						$this->ajaxReturn('','请选择下一审批人！',0);
					}
					
					$m_examine->examine_role_id = $next_role_id;

					if ($_POST['examine_status'] == 2) {
						$m_examine->order_id = $next_order_id;
						$m_examine->examine_status = 2; //审批结束(通过)
					} elseif ($examine_status['option'] == 1) {
						//自定义流程
						//查询审批流程排序最大值，如果order_id和最大值相等，则审批结束
						$max_order_id = $m_examine_step->where('process_id = %d',$examine_info['type'])->max('order_id');
						$order_id = $next_order_id;
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
			if($m_examine->where(array('examine_id'=>$examine_id))->save()){
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
					$message_content = '您申请的'.$type_name.'已被审批！<a href="'.U('examine/view','id='.$examine_id).'">点击查看</a><br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.$examine_info['creator_role_id'].'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间：'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型:'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容:<a href="'.U('examine/view','id='.$examine_id).'">'.$examine_info['content'].'</a>';
						sendMessage($examine_info['creator_role_id'],$message_content,1);
				}else{
					//下一审批人
					$examine_role_ids = $next_role_id ? array_filter(explode(',',$next_role_id)) : array();
					$message_content = '您有一个'.$type_name.'审批待处理！<a href="'.U('examine/view','id='.$examine_id).'">点击查看</a><br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class="role_info" rel="'.$examine_info['creator_role_id'].'" href="javascript:void(0)">'.$creator['user_name'].'</a> ['.$creator['department_name'].' - '.$creator['role_name'].']<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 创建时间:'.date('Y-m-d',time()).'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批类型：'.$type_name.'<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 审批内容：<a href="'.U('examine/view','id='.$examine_id).'">'.$examine_info['content'].'</a>';
					foreach ($examine_role_ids as $k=>$v) {
						sendMessage($v,$message_content,1);
					}
				}
				$this->ajaxReturn('','审核成功！',1);
			}else{
				$this->ajaxReturn('','审核失败！',0);
			}
		} else {
			$this->ajaxReturn('','非法请求！',0);
		}
	}

	/**
	 * 审批权限判断
	 * @param 
	 * @author 
	 * @return 
	 */
	public function checkPer($examine_id) {
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

		if($examine_check_info || $res_info || in_array($examine_info['creator_role_id'], $below_ids) || in_array(session('role_id'),array_filter(explode(',',$examine_info['owner_role_id'])))){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 审批记录
	 * @param 
	 * @author 
	 * @return 
	 */
	public function check_list(){
		$m_examine_check = M('ExamineCheck');
		$m_user = M('User');
		$examine_id = $_POST['id'] ? intval($_POST['id']) : 0;
		//判断权限
		if (!$this->checkPer($examine_id)) {
			$this->ajaxReturn('','您没有此权利！',-2);
		}
		if ($examine_id) {
			$check_list = $m_examine_check->where('examine_id = %d',$examine_id)->order('check_id asc')->select();
			$d_user = D('User');
			foreach($check_list as $k=>$v){
				$temp_val = $d_user->get_full_name(array($v['role_id']));
				$check_list[$k]['user'] = $temp_val[$v['role_id']];
			}
			$data['list'] = $check_list ? $check_list : array();
			$data['status'] = 1;
			$data['info'] = 'success';
			$this->ajaxReturn($data,'JSON');
		} else {
			$this->ajaxReturn('','参数错误！',0);
		}
	}

	/**
	 * 审批人流程
	 * @param 
	 * @author 
	 * @return 
	 */
	public function examineStatus() {
		if ($this->isPost()) {
			$d_user = D('User');
			$status = $_POST['type'] ? intval($_POST['type']) : 0;
			$examine_id = $_POST['id'] ? intval($_POST['id']) : 0;
			if (!$status) {
				$this->ajaxReturn('','参数错误！',0);
			}

			$m_examine = M('Examine');
			$m_examine_status = M('ExamineStatus');
			$m_examine_step = M('ExamineStep');
			$m_examine_check = M('ExamineCheck');
			$m_user = M('User');
			if ($examine_id) {
				$examine_info = $m_examine->where(array('examine_id'=>$examine_id))->find();
				$examine_status = $m_examine_status->where(array('status'=>$examine_info['type']))->find();
				$examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$examine_info['order_id']))->find();
				if ($examine_info['examine_status'] == 2 || $examine_info['examine_status'] == 3) {
					// $this->ajaxReturn('','该审批已经结束！',0);
					$add_examine = 0;
				} else {
					//是否有审批权限
					if ($examine_status['option'] == 1) {
						$is_add_examine = 1;
						$examine_step_info = $m_examine_step->where(array('process_id'=>$examine_info['type'],'order_id'=>$examine_info['order_id']))->find();
						//当前步骤已审批role_id
						$is_examine_role_ids = $m_examine_check->where(array('examine_id'=>$examine_id,'order_id'=>$examine_info['order_id'],'is_end'=>0,'is_checked'=>1))->getField('role_id',true);

						if (in_array(session('role_id'),$is_examine_role_ids)) {
							// $this->ajaxReturn('','您已审核，请勿重复操作！',0);
							$is_add_examine = 0;
						}
						//权限判断（并、或关系的，必须是规定审批人，超级管理员也无此权限）
						if (!in_array(session('role_id'),explode(',',$examine_step_info['role_id']))) {
							// $this->ajaxReturn('','您没有审核权限或审批已通过！',0);
							$is_add_examine = 0;
						}
						$add_examine = $is_add_examine;
					} else {
						if (session('?admin') || ($examine_info['examine_role_id'] == session('role_id') || in_array(session('role_id'),array_filter(explode(',',$examine_info['examine_role_id']))))) {
							$add_examine = 1;
						}
					}
				}
				if (!$add_examine) {
					$this->ajaxReturn('','您没有审核权限或审批已通过！',0);
				}

				//下一审批相关
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
					$relation_name = '';
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
						$next_role_info = $d_user->get_full_name($next_role_id);
						$next_role_id = $next_role_id;
					} else {
						//当前流程，剩余审批人
						$next_order_id = $examine_info['order_id'];
						if ($examine_step_info['relation'] == 1) {
	                        $relation_name = '并';
	                    } elseif ($examine_step_info['relation'] == 2) {
	                        $relation_name = '或';
	                    }

						$next_role_id = $is_check_role ? array_merge(array_diff(array_filter(explode(',',$examine_step_info['role_id'])),$is_check_role)) : array_filter(explode(',',$examine_step_info['role_id']));
						$next_role_info = $d_user->get_full_name($next_role_id);
						$next_role_id = $next_role_id ? ','.implode(',',$next_role_id).',' : '';
					}
				}
			} else {
				//添加时
				$examine_status = $m_examine_status->where(array('status'=>$status))->find();
				if ($examine_status['option'] == 1) {
					$examine_step_info = $m_examine_step->where(array('process_id'=>$status))->order('order_id asc')->find();
					$next_role_id = $examine_step_info['role_id'];
					$next_role_info = $d_user->get_full_name($next_role_id);
					$next_order_id = $examine_step_info['order_id']+1;
					if ($examine_step_info['relation'] == 1) {
                        $relation_name = '并';
                    } elseif ($examine_step_info['relation'] == 2) {
                        $relation_name = '或';
                    }
				}
			}

			$data['option'] = $examine_status['option']  ? : '0'; //0自选 1流程
			$data['examine_role'] = $next_role_info ? : array();
			$data['next_role_id'] = $next_role_id ? : '';
			$data['next_order_id'] = $next_order_id ? : 0;
			$data['relation_name'] = $relation_name ? : '';
			$data['status'] = 1;
			$data['info'] = 'success';
			$this->ajaxReturn($data,'JSON');
		}
	}
}