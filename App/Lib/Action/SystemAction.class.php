<?php
class SystemAction extends Action {

	/**
	*  用于判断权限
	*  @permission 无限制
	*  @allow 登录用户可访问
	*  @other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('scene_add','scene_edit','scene_setting','scenesort','scenedefault','scenelistajax','cycel','topsearch','export_status','set_open_menu','not_notice')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME); 
		$this->alert = parseAlert();
	}

	/**
	 * 自定义场景（添加）
	 * @param 
	 * @author gengxiaoxu@xxx.com
	 * @return 
	 */
	public function scene_add(){
		$module_name = $_REQUEST['module'] ? trim($_REQUEST['module']) : '';
		if (!$module_name) {
			$this->ajaxReturn('','参数错误！',0);
		}
		$m_fields = M('Fields');
		$m_scene = M('Scene');

		$field_list = $m_fields->where(array('model'=>$module_name,'is_main'=>1))->select();
		if ($this->isPost()) {
			$data = $_POST['data'];
			if (!$data) {
				$this->ajaxReturn('','筛选条件不能为空！',0);
			}
			//判断重复
			if ($m_scene->where(array('module'=>$module_name,'role_id'=>session('role_id'),'name'=>trim($_POST['scene_name'])))->find()) {
				$this->ajaxReturn('','场景名称已存在！',0);
			}
			if ($m_scene->create()) {
				$m_scene->name = trim($_POST['scene_name']);
				$m_scene->role_id = session('role_id');
				$m_scene->create_time = time();
				$m_scene->update_time = time();
				$m_scene->module = $module_name;
				//处理筛选条件值
				if(!empty($data) && is_array($data)){
					$scene_data = 'array(';
					$s = array();
					$i = 0;
					foreach ($data as $k=>$v) {
						if($v != '' && !in_array($v['field'] ,$s)) {
							$i++;
							if ($i == 1) {
								$scene_data .= "$v[field]=>array(";
							}else {
								$scene_data .= ","."$v[field]=>array(";
							}
							$scene_data .= "field=>'$v[field]',";
							foreach ($_POST[$v['field']] as $k1=>$v1) {
								if ($k1 == 'condition') {
									$scene_data .= "condition=>'$v1',";
								} elseif ($k1 == 'value') {
									$scene_data .= "value=>'$v1',";
								} elseif ($k1 == 'state' || $k1 == 'city' || $k1 == 'area') {
									//处理地址数据
									$scene_data .= "$k1=>'$v1',";
								} elseif ($k1 == 'start') {
									//处理日期数据
									$scene_data .= "start=>'$v1',";
								} elseif ($k1 == 'end') {
									//处理日期数据
									$scene_data .= "end=>'$v1',";
								}
							}
							if ($v['field'] == 'create_time' || $v['field'] == 'update_time') {
								$form_type = 'datetime';
							} elseif ($v['field'] == 'owner_role_id') {
								$form_type = '';
							} else {
								$form_type = $m_fields->where(array('model'=>$module_name,'is_main'=>1,'field'=>$v['field']))->getField('form_type');
							}
							
							$scene_data .= "form_type=>'$form_type'";
							$s[] = $v['field'];
						}
						$scene_data .= ')';
					}
					$scene_data .= ')';
					$m_scene->data = $scene_data;
				}
				if ($scene_id = $m_scene->add()) {
					$scene_info = $m_scene->where(array('id'=>$scene_id))->find();
					$this->ajaxReturn($scene_info,'创建成功！',1);
				} else {
					$this->ajaxReturn('','创建失败，请重试！',0);
				}
			}
		}
		$this->module_name = $module_name;
		$this->field_list = $field_list;
		$this->display();
	}

	/**
	 * 自定义场景（编辑）
	 * @param 
	 * @author gengxiaoxu@xxx.com
	 * @return 
	 */
	public function scene_edit(){
		$module_name = $_REQUEST['module'] ? trim($_REQUEST['module']) : '';
		if (!$module_name) {
			$this->ajaxReturn('','参数错误！',0);
		}
		$id = $_REQUEST['id'] ? intval($_REQUEST['id']) : '';
		if (!$id) {
			$this->ajaxReturn('','参数错误！',0);
		}
		$m_fields = M('Fields');
		$m_scene = M('Scene');
		$scene_info = $m_scene->where(array('id'=>$id,'role_id'=>session('role_id')))->find();
		if ($scene_info['type'] == 1) {
			$this->ajaxReturn('','不能编辑！',0);
		}
		//处理自定义条件
		$data = array();
		eval('$data = '.$scene_info["data"].';');
		$scene_info['data'] = $data;
		$field_list = $m_fields->where(array('model'=>$module_name,'is_main'=>1))->select();
		if ($this->isPost()) {
			$data = $_POST['data'];
			if (!$data) {
				$this->ajaxReturn('','筛选条件不能为空！',0);
			}
			//判断重复
			if ($m_scene->where(array('module'=>$module_name,'role_id'=>session('role_id'),'name'=>trim($_POST['scene_name']),'id'=>array('neq',$id)))->find()) {
				$this->ajaxReturn('','场景名称已存在！',0);
			}
			if ($m_scene->create()) {
				$m_scene->name = trim($_POST['scene_name']);
				$m_scene->update_time = time();
				$m_scene->module = $module_name;
				//处理筛选条件值
				if(!empty($data) && is_array($data)){
					$scene_data = 'array(';
					$s = array();
					$i = 0;
				// println($_POST);
					foreach ($data as $k=>$v) {
						if($v != '' && !in_array($v['field'] ,$s)) {
							$i++;
							if ($i == 1) {
								$scene_data .= "$v[field]=>array(";
							}else {
								$scene_data .= ","."$v[field]=>array(";
							}
							$scene_data .= "field=>'$v[field]',";
							foreach ($_POST[$v['field']] as $k1=>$v1) {
								if ($k1 == 'condition') {
									$scene_data .= "condition=>'$v1',";
								} elseif ($k1 == 'value') {
									$scene_data .= "value=>'$v1',";
								} elseif ($k1 == 'state_scene' || $k1 == 'state') {
									$k1 = 'state';
									//处理地址数据
									$scene_data .= "$k1=>'$v1',";
								} elseif ($k1 == 'city_scene' || $k1 == 'city') {
									$k1 = 'city';
									//处理地址数据
									$scene_data .= "$k1=>'$v1',";
								} elseif ($k1 == 'area_scene' || $k1 == 'area') {
									$k1 = 'area';
									//处理地址数据
									$scene_data .= "$k1=>'$v1',";
								} elseif ($k1 == 'start') {
									//处理日期数据
									$scene_data .= "start=>'$v1',";
								} elseif ($k1 == 'end') {
									//处理日期数据
									$scene_data .= "end=>'$v1',";
								}
							}
							if ($v['field'] == 'create_time' || $v['field'] == 'update_time') {
								$form_type = 'datetime';
							} elseif ($v['field'] == 'owner_role_id') {
								$form_type = '';
							} else {
								$form_type = $m_fields->where(array('model'=>$module_name,'is_main'=>1,'field'=>$v['field']))->getField('form_type');
							}
							
							$scene_data .= "form_type=>'$form_type'";
							$s[] = $v['field'];
						}
						$scene_data .= ')';
					}
					$scene_data .= ')';
					$m_scene->data = $scene_data;
				}
				if ($m_scene->where('id = %d',$id)->save()) {
					$new_scene_info = $m_scene->where('id = %d',$id)->find();
					$this->ajaxReturn($new_scene_info,'创建成功！',1);
				} else {
					$this->ajaxReturn('','创建失败，请重试！',0);
				}
			}
		}
		$this->module_name = $module_name;
		$this->field_list = $field_list;
		$this->scene_info = $scene_info;
		$this->display();
	}
	
	/**
	 * 自定义场景（管理）
	 * @param 
	 * @author gengxiaoxu@xxx.com
	 * @return 
	 */
	public function scene_setting(){
		$module_name = $_REQUEST['module'] ? trim($_REQUEST['module']) : '';
		if (!$module_name) {
			$this->ajaxReturn('','参数错误！',0);
		}
		$m_scene = M('Scene');
		$m_scene_default = M('SceneDefault');
		if ($this->isPost()) {
			$data = $_POST['data'];
			if ($m_scene->save()) {
				$this->ajaxReturn('','创建成功！',1);
			} else {
				$this->ajaxReturn('','创建失败，请重试！',0);
			}
		}
		$m_scene = M('Scene');
		$scene_id = $_REQUEST['scene_id'] ? intval($_REQUEST['scene_id']) : '';
		$scene_where = array();
		$scene_where['role_id']  = session('role_id');
		$scene_where['type']  = 1;
		$scene_where['_logic'] = 'or';
		$map_scene['_complex'] = $scene_where;
		$map_scene['module'] = $module_name;

		$scene_list = $m_scene->where($map_scene)->order('order_id asc,id asc')->select();
		//默认场景
		$default_scene = $m_scene_default->where(array('module'=>$module_name,'role_id'=>session('role_id')))->getField('scene_id');
		if (!$default_scene) {
			$default_scene = $m_scene->where(array('module'=>$module_name,'type'=>1))->order('id asc')->getField('id');
		}
		$this->default_scene = $default_scene;

		$this->scene_list = $scene_list;
		$this->module_name = $module_name;
		$this->display();
	}

	/**
	 * 自定义场景（排序）
	 * @param 
	 * @author gengxiaoxu@xxx.com
	 * @return 
	 */
	public function scenesort(){
		//权限判断

		$m_scene = M('Scene');
		if(isset($_GET['postion'])){
			$m_scene = M('Scene');
			foreach(explode(',', $_GET['postion']) as $k=>$v) {
				$data = array('id'=> $v, 'order_id'=>$k);
				$m_scene->save($data);
			}
			$this->ajaxReturn('1', 'success', 1);
		} else {
			$this->ajaxReturn('0', 'error', 1);
		}
	}

	/**
	 * 自定义场景（是否默认、隐藏）
	 * @param 
	 * @author gengxiaoxu@xxx.com 
	 * @return 
	 */
	public function sceneDefault(){
		$m_scene = M('Scene');
		$scene_id = $this->_request('scene_id','intval',0);
		if ($this->isAjax()) {
			$scene_where = array();
			$scene_where['role_id']  = session('role_id');
			$scene_where['type']  = 1;
			$scene_where['_logic'] = 'or';
			$map_scene['_complex'] = $scene_where;
			$map_scene['id'] = $scene_id;

			$scene_info = $m_scene->where($map_scene)->find();
			if (!$scene_info) {
				$this->ajaxReturn('',L('PARAMETER_ERROR'),0);
			}
			if (trim($_GET['type']) == 'default') {
				//默认场景
				$m_scene_default = M('SceneDefault');
				$res_scene = $m_scene_default->where(array('module'=>$scene_info['module'],'role_id'=>session('role_id')))->find();
				if ($res_scene) {
					$res = $m_scene_default->where(array('module'=>$scene_info['module'],'role_id'=>session('role_id')))->setField('scene_id',$scene_id);
				} else {
					$data = array();
					$data['module'] = $scene_info['module'];
					$data['role_id'] = session('role_id');
					$data['scene_id'] = $scene_id;
					$res = $m_scene_default->add($data);
				}
				if ($res) {
					$this->ajaxReturn('','success',1);
				} else {
					$this->ajaxReturn('','修改失败，请重试！',0);
				}
			}
			if (trim($_GET['type']) == 'hide') {
				if ($scene_info['is_hide']) {
					if ($m_scene->where('id = %d', $scene_id)->setField('is_hide', 0)) {
						$this->ajaxReturn(0,'success',1);
					} else {
						$this->ajaxReturn('','修改失败，请重试！',0);
					}
				} else {
					if ($m_scene->where('id = %d', $scene_id)->setField('is_hide', 1)) {
						$this->ajaxReturn(1,'success',1);
					} else {
						$this->ajaxReturn('','修改失败，请重试！',0);
					}
				}
			}
			if (trim($_GET['type']) == 'del') {
				if ($m_scene->where('id = %d', $scene_id)->delete()) {
					//删除默认场景
					$res = M('SceneDefault')->where('scene_id = %d',$scene_id)->delete();
					$this->ajaxReturn('','success',1);
				} else {
					$this->ajaxReturn('','删除失败，请重试！',0);
				}
			}		
		}
	}

	/**
	 * 自定义场景（列表刷新）
	 * @param 
	 * @author gengxiaoxu@xxx.com 
	 * @return 
	 */
	public function sceneListAjax(){
		if ($this->isAjax()) {
			$module = $_REQUEST['module'] ? trim($_REQUEST['module']) : '';
			if (!$module) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$m_scene = M('Scene');
			$scene_where = array();
			$scene_where['role_id']  = session('role_id');
			$scene_where['type']  = 1;
			$scene_where['_logic'] = 'or';
			$map_scene['_complex'] = $scene_where;
			$map_scene['module'] = 'customer';
			$map_scene['is_hide'] = 0;

			$scene_list = $m_scene->where($map_scene)->order('order_id asc,id asc')->select();
			$this->ajaxReturn($scene_list,'',1);
		}
	}

	/**
	 * 自定义周期设置
	 * @param 
	 * @author gengxiaoxu@xxx.com 
	 * @return 
	 */
	public function cycel(){
		$m_cycel = M('Cycel');
		$module = trim($_REQUEST['module']);
		$module_id = intval($_REQUEST['module_id']);
		if (!$module || !$module_id) {
			$this->ajaxReturn('','参数错误！',0);
		}
		$cycel_info = $m_cycel->where(array('module'=>$module,'module_id'=>$module_id))->find();
		if ($this->isPost()) {
			if ($m_cycel->create()) {
				$m_cycel->update_time = time();
				$m_cycel->end_time = strtotime(trim($_POST['end_time']));
				if ($cycel_info) {
					if ($m_cycel->save()) {
						$this->ajaxReturn('','设置成功',1);
					} else {
						$this->ajaxReturn('','设置失败，请重试！',0);
					}
				} else {
					$m_cycel->create_role_id = session('role_id');
					$m_cycel->start_time = strtotime(date('Y-m-d',time()));
					if ($m_cycel->add()) {
						$this->ajaxReturn('','设置成功',1);
					} else {
						$this->ajaxReturn('','设置失败，请重试！',0);
					}
				}
			} else {
				$this->ajaxReturn('','设置失败，请重试！',0);
			}
		}
		$this->cycel_info = $cycel_info;
		$this->display(); 
	}

	/**
	 * 系统顶部全局搜索（暂定客户名称、线索名称、联系人名称、手机号码查询），不涉及权限，最多展示10条
	 * @param 
	 * @author gengxiaoxu@xxx.com
	 * @return 
	 */
	public function topsearch(){
		$m_customer = M('Customer');
		$m_leads = M('Leads');
		$m_contacts = M('Contacts');
		$m_user = M('User');
		$search = trim($_GET['search']);
		//先查客户
		$where = array();
		$c_where = array();
		$c_where['_string'] = 'name like "%'.$search.'%" or telephone like "%'.$search.'%"';
		$contacts_ids = $m_contacts->where($c_where)->getField('contacts_id',true);
		$contacts_str = implode(',',$contacts_ids);
		if($contacts_str){
			$contacts_customer_ids = M('rContactsCustomer')->where(array('contacts_id'=>array('in',$contacts_str)))->getField('customer_id',true);
			$field_where = array();
			$field_where['name'] = array('like','%'.$search.'%');
			$field_where['customer_id'] = array('in',$contacts_customer_ids);
			$field_where['_logic'] = 'OR';
			$where['_complex'] = $field_where;
		}else{
			$where['name'] = array('like','%'.$search.'%');
		}
		$res_list_a = $m_customer->where($where)->order('update_time desc')->limit(10)->field('name,owner_role_id,customer_id')->select();
		foreach ($res_list_a as $k=>$v) {
			$res_list_a[$k]['user_info'] = $m_user->where(array('role_id'=>$v['owner_role_id']))->field('full_name,role_id')->find();
			$res_list_a[$k]['module_name'] = '客户';
			$res_list_a[$k]['url'] = U('customer/view','id='.$v['customer_id']);
		}
		$res_count = count($res_list_a);

		if ($res_count < 10) {
			//查询线索
			$l_where = array();
			$l_where['_string'] = 'name like "%'.$search.'%" or mobile like "%'.$search.'%"';
			$l_limit = $res_count ? 10-$res_count : 10;
			$res_list_b = $m_leads->where($l_where)->order('update_time desc')->limit($l_limit)->field('name,owner_role_id,leads_id')->select();
			foreach ($res_list_b as $k=>$v) {
				$res_list_b[$k]['user_info'] = $m_user->where(array('role_id'=>$v['owner_role_id']))->field('full_name,role_id')->find();
				$res_list_b[$k]['module_name'] = '线索';
				$res_list_b[$k]['url'] = U('leads/view','id='.$v['leads_id']);
			}
		}
		$res_list = array();
		if ($res_list_a && $res_list_b) {
			$res_list = array_merge($res_list_a,$res_list_b);
		} elseif ($res_list_a) {
			$res_list = $res_list_a;
		} else {
			$res_list = $res_list_b;
		}
		if ($this->isAjax()) {
			$this->ajaxReturn($res_list,'',1);
		} else {
			$this->res_list = $res_list;
		}
		$this->display();
	}

	/**
	 * 授权信息获取
	 * @param 
	 * @author gengxiaoxu@xxx.com
	 * @return 
	 */
	public function anthorize(){
		if ($this->isAjax() && session('?admin')) {
			if ($this->isPost()) {
				$this->ajaxReturn('', 'success', 1);
			} else {
				$authorize_setting = array();	
				$key_file = file_get_contents(CONF_PATH.'license.dat');
				$days_remaining = '100';

				if ($key_file) {
					$key_info = F('key_info');
					//解密
					import("@.ORG.Kakarote");
					$key = 'KAKAROTE@pdcrm！@#'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
					$des = new Kakarote();
					$dre_key_info = $des->decrypt(hex2bin($key_info), $key);
					$dre_key_info = unserialize($dre_key_info);

					$authorize_setting['NUM'] = $dre_key_info['number'];
					$authorize_setting['OPENTIME'] = '20170523';
					$authorize_setting['ENDTIME'] = $dre_key_info['end_time'] ? date('Y-m-d', $dre_key_info['end_time']) : '无限制';

					if (!empty($dre_key_info['end_time'])) {
						$days_remaining = round(($dre_key_info['end_time']-time())/86400);
					} else {
						$days_remaining = 100;
					}
				} else {
					$authorize_setting = C('AUTHORIZE_SETTING');

					if ($authorize_setting['NUM'] <= 5 && !empty($authorize_setting['OPENTIME']) && intval($authorize_setting['OPENTIME']) > 20171116) {
						if ($authorize_setting['NUM'] > 2 || C('PSS_STATUS') == true) {
							$days_remaining = round((strtotime($authorize_setting['ENDTIME'])-time())/86400);
							$authorize_setting['ENDTIME'] = date('Y年m月d日',strtotime($authorize_setting['ENDTIME']));
						} else {
							$authorize_setting['ENDTIME'] = '永久免费';
						}
					} else {
						if (($authorize_setting['NUM'] > 5)) {
							$days_remaining = round((strtotime($authorize_setting['ENDTIME'])-time())/86400);
							$authorize_setting['ENDTIME'] = date('Y年m月d日',strtotime($authorize_setting['ENDTIME']));
						} else {
							$authorize_setting['ENDTIME'] = '永久免费';
						}
					}
				}
				
				$call_setting = C('CALL_SETTING');
				$this->days_remaining = $days_remaining;
				$this->authorize_setting = $authorize_setting;
				$this->call_setting = $call_setting ? : array();
				$this->display();
			}
		} else {
			$this->ajaxReturn('', 'error', 0);
		}
	}


	/**
	 * 进销存设置
	 */
	public function pssSetup()
	{
		if (!session('?admin')) return false;
		$m_config = M('Config');
		$config = array('over_stock_sales', 'purchase_prefix', 'purchase_return_prefix', 'sales_return_prefix', 'stock_in_prefix', 'stock_out_prefix');
		$config_array = $m_config->where(array('name' => array('IN', $config)))->field('id,value,name')->select();
		if (IS_POST) {
			if ($_POST['purchase_return_prefix'] == 'XSD_') {
				alert('error', '采购退货单号前缀不能为 XSD_', $_SERVER['HTTP_REFERER']);
			}
			if ($_POST['purchase_prefix'] == $_POST['sales_return_prefix']) {
				alert('error', '采购单号前缀不能与销售退货单号前缀相同', $_SERVER['HTTP_REFERER']);
			}
			$old_data = y_array_column($config_array, 'value', 'name');
			$res_col_num = 0;
			foreach ($old_data as $key => $val) {
				if ($old_data[$key] != $_POST[$key]) {
					if ($m_config->where(array('name' => $key))->save(array('value' => $_POST[$key]))) {
						$res_col_num++;
					}
				}
			}
			if ($res_col_num > 0) {
				alert('success', '保存成功', $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', '保存失败，未进行任何修改。', $_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->config_array = y_array_column($config_array, 'value', 'name');
			$this->pss_status = C('PSS_STATUS');
			$this->display('pss_setup');
		}
	}


	/** 
	 * 系统导出功能，自动分批次导出时设置的session状态
	 * @return export_status的状态 0可以执行导出功能 1不能执行导出功能
	 * @author lee
	 */
	public function export_status()
	{
		$this->ajaxReturn(intval(session('export_status')), 'success', 1);
	}


	/**
	 * 设置需要打开的菜单栏
	 * 常规情况下，在html中将ID的值设置为"模块名称-index"即可，特殊情况特殊处理
	 * @author lee
	 */
	public function set_open_menu()
	{
		$module_name = trim($_GET['module_name']);
		$action_name = trim($_GET['action_name']);
		$param = trim($_GET['param']);
		$menu_html_id = D('System')->splicingId($module_name, $action_name, $param);
// p($menu_html_id,'');
		$data['data'] = $menu_html_id;
		$data['status'] = 1;
		$this->ajaxReturn($data);
	}


	/**
	 * 设置公告是否不再展示
	 * @author lee
	 */
	public function not_notice()
	{
		if (IS_AJAX) {
			session('not_notice', 1);
			$this->ajaxReturn('', 'success', 1);
		}
	}


}