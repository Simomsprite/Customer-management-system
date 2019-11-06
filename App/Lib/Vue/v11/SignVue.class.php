<?php
/**
 *签到
 **/
class SignVue extends Action{
	/**
	 *用于判断权限
	 *@permission 无限制
	 *@allow 登录用户可访问
	 *@other 其他根据系统设置
	 **/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index','view','add')
		);
		B('VueAuthenticate', $action);

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
	 * 签到
	 * @param 
	 * @author 
	 * @return 
	 */
	public function add() {
		if ($this->isPost()) {
			
			$m_sign = M('Sign');
			$m_sign->create();
			$m_sign->role_id = session('role_id');
			$m_sign->create_time = time();
			$sign_id = $m_sign->add();
			if ($sign_id) {
				if ($_POST['customer_id']) {
					$m_log = M('Log');
					$m_log->role_id = session('role_id');
					$m_log->category_id = 1;
					$m_log->sign = 1;
					$m_log->create_date = time();
					$m_log->update_date = time();
					if ($log_id = $m_log->add()) {
						$data['log_id'] = $log_id;
						$data['customer_id'] = $_POST['customer_id'];
						M('RCustomerLog')->add($data);
						$m_sign->where('sign_id = %d',$sign_id)->setField('log_id',$log_id);
					}
					// 关联（完成）访客计划
					if ($visitor_plan_id = (int) $_POST['visitor_plan_id']) {
						$vp_res = D('VisitorPlan')->finishPlan(array('id' => $visitor_plan_id, 'module' => 'sign', 'module_id' => $sign_id));
					}
				}
				if (!empty($_POST['img'])) {
					$file_list = M('File')->where(array('file_id' => array('in', $_POST['img'])))->select();
					$img_data = array();
					foreach ($file_list as $key => $val) {
						$temp_val['sign_id'] = $sign_id;
						$temp_val['name'] = $val['name'];
						$temp_val['save_name'] = $val['file_path'];
						$temp_val['path'] = $val['file_path'];
						$temp_val['create_time'] = time();
						$img_data[] = $temp_val;
					}
					if (M('SignImg')->addAll($img_data)) {
						actionLog($sign_id);
						$this->ajaxReturn('','签到成功！',1);
					} else {
						$this->ajaxReturn('','签到失败，请重试！',0);
					}
				} else {
					actionLog($sign_id);
					$this->ajaxReturn('','签到成功！',1);
				}
			} else {
				$this->ajaxReturn('','签到失败，请重试！',0);
			}
		} else {
			$this->ajaxReturn('','非法请求！',0);
		}
	}

	/**
	 * 签到列表
	 * @param 
	 * @author 
	 * @return 
	 */
	public function index() {
		if ($this->isPost()) {
			$below_ids = getPerByAction('sign','index');
			$m_sign = M('Sign');
			$m_user = M('User');
			$m_customer = M('Customer');
			$m_sign_img = M('SignImg');

			$where = array();
			$date = $_POST['date'] ? $_POST['date'] : time();
			$start_time = strtotime(date('Y-m-d',$date));
			$end_time = $start_time+86400;
			$where['create_time'] = array('between',array($start_time,$end_time));

			$role_ids = array();
			
			//查询
			$department_id = $_POST['department_id'] ? $_POST['department_id'] : array(session('department_id'));
			$role_id = $_POST['role_id'];
			if (is_array($department_id)) {
				foreach ($department_id as $k=>$v) {
					foreach (getRoleByDepartmentId($v, true) as $key=>$val) {
						$role_ids[] = $val['role_id'];
					}
				}
			}
			if (is_array($role_id)) {
				$role_ids = $role_ids ? array_merge($role_ids,$role_id) : $role_id;
			} 
			// if (!$department_id && !$role_id) {
			// 	//默认本部门权限范围内role_id
			// 	$role_ids = D('RoleView')->where(array('position.role_department'=>session('department_id')))->getField('role_id',true);
			// }

			if ($role_ids) {
				//数组交集
				$role_id_array = array_intersect($role_ids, $below_ids);
			} else {
				$role_id_array = array(session('role_id'));
			}
			$where['role_id'] = array('in',$role_id_array);

			$p = isset($_POST['p']) ? intval($_POST['p']) : 1;
			$sign_list = $m_sign->where($where)->page($p,10)->order('create_time desc')->select();
			$count = $m_sign->where($where)->count();
			$page = ceil($count/10);

			$d_user = D('User');
			$v_role = D('RoleView');
			$m_visitor_plan = M('VisitorPlan');
			foreach ($sign_list as $k=>$v) {
				$role_info = array();
				$temp_val = $d_user->get_full_name(array($v['role_id']));
				$role_info = $temp_val[$v['role_id']];
				$sign_list[$k]['user_name'] = $role_info['full_name'];
				$sign_list[$k]['user_img'] = $role_info['thumb_path'];
				//客户
				$sign_customer_name = $m_customer->where('customer_id = %d',$v['customer_id'])->getField('name');
				$sign_list[$k]['sign_customer_name'] = empty($sign_customer_name) ? '' : $sign_customer_name;
				//图片
				$sign_img = $m_sign_img->where(array('sign_id'=>$v['sign_id']))->getField('path',true);
				foreach ($sign_img as $key => $val) {
					$sign_img[$key] = headPathHandle($val, 1);
				}
				$sign_list[$k]['sign_img'] = $sign_img ? $sign_img : array();
				$sign_list[$k]['visitor_plan'] = $m_visitor_plan->where(array('module' => 'sign', 'module_id' => $v['sign_id']))->field('plan_time, content')->find();
				// vdd($v);
				$sign_list[$k]['owner'] = $v_role->where('role.role_id = %d', $v['role_id'])->field('user_name,role_id,thumb_path,role_name,department_name')->find();
				$sign_list[$k]['owner']['thumb_path'] = headPathHandle($sign_list[$k]['owner']['thumb_path']);
			}
			$info = array();
			$info['list'] = $sign_list ? $sign_list : array();
			$info['today_count'] = $count;

			$data['page'] = $page;
			$data['data'] = $info;
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		} else {
			$this->ajaxReturn('','非法请求！',0);
		}
	}

	/**
	 * 足迹分布
	 * @param type=role 员工数组  sign 坐标数组
	 * @author 
	 * @return 
	 */
	public function view() {
		if ($this->isPost()) {
			$below_ids = getPerByAction('sign','index');
			$type = $_POST['type'] ? trim($_POST['type']) : 'role';
			$date = $_POST['date'] ? $_POST['date'] : time();
			$start_time = strtotime(date('Y-m-d',$date));
			$end_time = $start_time+86400;

			$where = array();
			$m_sign = M('Sign');
			$m_user = M('User');
			$role_ids = array();
			
			//查询
			$department_id = $_POST['department_id'];
			$role_id = $_POST['role_id'];

			if (is_array($department_id)) {
				foreach ($department_id as $k=>$v) {
					foreach (getRoleByDepartmentId($v, true) as $key=>$val) {
						$role_ids[] = $val['role_id'];
					}
				}
			}
			if (is_array($role_id)) {
				$role_ids = $role_ids ? array_merge($role_ids,$role_id) : $role_id;
			} 
			if (!$department_id && !$role_id) {
				//默认本部门权限范围内role_id
				$role_ids = D('RoleView')->where(array('position.role_department'=>session('department_id')))->getField('role_id',true);
			}

			if ($role_ids) {
				//数组交集
				$role_id_array = array_intersect($role_ids, $below_ids);
			} else {
				$role_id_array = array(session('role_id'));
			}

			if ($type == 'role') {
				$where['role_id'] = array('in',$role_id_array);
				$where['create_time'] = array('between',array($start_time,$end_time));
				
				$role_arr = $m_sign->where($where)->group('role_id')->getField('role_id',true);
				$role_info_list = D('User')->get_full_name($role_arr);

				$data['list'] = $role_info_list ? $role_info_list : array();
				$data['info'] = 'success';
				$data['status'] = 1;
				$this->ajaxReturn($data,'JSON');
			}
			if ($type == 'sign') {
				$where_sign['create_time'] = array('between',array($start_time,$end_time));
				$where['role_id'] = array('in',$role_id_array);
				$sign_list = $m_sign->where($where_sign)->field('x,y,address,title')->select();
				$data['list'] = $sign_list ? $sign_list : array();
				$data['info'] = 'success';
				$data['status'] = 1;
				$this->ajaxReturn($data,'JSON');
			}
			$this->ajaxReturn('','参数错误！',0);
		}
	}
}