<?php
/**
 *提醒相关
 **/
class RemindVue extends Action {
	/**
	 *用于判断权限
	 *@permission 无限制
	 *@allow 登录用户可访问
	 *@other 其他根据系统设置
	 **/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('add', 'delete', 'view', 'visitor_plan_list', 'visitor_plan_del', 'visitor_plan_edit', 'index')
		);
		B('VueAuthenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
		
		Global $role;
		$this->role = $role;
		Global $roles;
		$this->roles = $roles;

		if($roles == 2){
			$this->ajaxReturn('','您没有此权限！', -2);
		}

		if($roles == 3){
			$this->ajaxReturn('','请先登录！', -1);
		}
	}


	/**
	 * 添加提醒
	 * @param 
	 * @author 
	 * @return 
	 */
	public function add(){
		if ($this->isPost()) {
			$data['module'] = trim($_POST['module']);
			$data['module_id'] = (int) $_POST['module_id'];
			$data['remind_time'] = (int) $_POST['remind_time'];
			$data['content'] = $_POST['content'];
			$data['create_role_id'] = session('role_id');
			$m_remind = M('Remind');
			$m_remind->create($data);

			if ($remind_id = $m_remind->add()) {
				//关联日程
				$event_id = dataEvent('提醒',$data['remind_time'],'remind',$remind_id);

				if (isset($_POST['join_visitor_plan']) && $_POST['join_visitor_plan'] == 1) {
					$data = array(
						'event_id' => $event_id,
						'plan_time' => $data['remind_time'],
						'content' => $_POST['content']
					);
					M('VisitorPlan')->add($data);
				}

				$this->ajaxReturn('','设置提醒成功！',1);
			} else {
				$this->ajaxReturn('','设置提醒失败，请重试！',2);
			}
		}
	}

	/**
	 * 提醒删除
	 * @param 
	 * @author 
	 * @return 
	 */
	public function delete() {
		
		if ($this->isPost()) {
			// $module_id = intval($_POST['module_id']);
			// $module = trim($_POST['module']);
			if (!$remind_id = (int) $_POST['remind_id']) {
				$this->ajaxReturn('','参数错误！',2);
			}
			$m_remind = M('Remind');
			$where = array();
			$where['create_role_id'] = session('role_id');
			$where['remind_id'] = $remind_id;
			if (!$m_remind->where($where)->find()) {
				$this->ajaxReturn('','数据不存在或已删除！',2);
			}
			if ($m_remind->where($where)->delete()) {
				//删除关联日程
				$event_res = M('Event')->where(array('module'=>$module,'module_id'=>$module_id))->delete();;
				$this->ajaxReturn('','删除成功！',1);
			} else {
				$this->ajaxReturn('','删除失败，请重试！',2);
			}
		}
	}


	/**
	 * 访客计划列表
	 */
	public function visitor_plan_list()
	{
		if ($this->isPost()) {
			$customer_id = (int) $_POST['customer_id'];
			$v_p_where = array(
				'customer_id' => $customer_id,
				'vue' => TRUE
			);
			if ($_POST['status'] == 'today_not_done') {
				$v_p_where['status'] = array('IN', array(0, 1));		// 未完成 延期
				$v_p_where['plan_time'] = array('BETWEEN', array(strtotime(date('Y-m-d')), strtotime(date('Y-m-d 23:59:59'))));
			}
			$visitor_plan_list = D('VisitorPlan')->getList($v_p_where);
			$this->ajaxReturn(array('list' => $visitor_plan_list, 'info' => '', 'status' => 1));
		}
	}

	/**
	 * 取消（删除）访客计划
	 */
	public function visitor_plan_del()
	{
		if ($this->isPost()) {
			$id = (int) $_POST['id'];
			$status = M('VisitorPlan')->where(array('id' => $id))->getField('status');
			if ($status == 4) {
				$this->ajaxReturn(array('', '计划已完成，不能取消。', 2));
			} elseif ($status == 3) {
				$this->ajaxReturn(array('', '计划已放弃，不能取消。', 2));
			}
			$res = M('VisitorPlan')->where(array('id' => $id))->delete();
			$msg = $res ? '取消成功！' : '取消失败，请重试！';
			$status = $res ? 1 : 2;
			$this->ajaxReturn('', $msg, $status);
		}
	}


	/**
	 * 访客计划延期
	 */
	public function visitor_plan_edit()
	{
		if ($this->isPost()) {
			$id = (int) $_POST['id'];
			$view = M('VisitorPlan')->where(array('id' => $id))->find();
			if ($view['status'] == 4) {
				$this->ajaxReturn(array('', '计划已完成，不能修改。', 2));
			} elseif ($view['status'] == 3) {
				$this->ajaxReturn(array('', '计划已放弃，不能修改。', 2));
			}
			$data['plan_time'] = (int) $_POST['plan_time'] ?: time();
			$data['delay_time'] = $view['plan_time'];
			$data['status'] = 1;
			$res = M('VisitorPlan')->where(array('id' => $id))->save($data);
			$data = array();
			if ($res) {
				$msg = '延期成功！';
			} else{
				$msg = '延期失败，请重试！';
			}
			$this->ajaxReturn(array('info' => $msg, 'status' => (int) $res));
		}
	}


	/**
	 * 提醒列表
	 */
	public function index()
	{
		$module_id = (int) $_POST['module_id'];
		$module = trim($_POST['module']);
		$m_remind = M('Remind');
		if(!$module_id || !$module){
			$this->ajaxReturn(array('', '参数错误！', 2));
		}
		// 权限判断（根据模块详情权限）
		$below_ids = getPerByAction($module, 'view');

		$where = array('module'=>$module, 'module_id'=>$module_id, 'create_role_id'=>array('in',$below_ids));
		if (isset($_POST['is_remind'])) {
			$is_remind = (int) $_POST['is_remind'];
			if ($is_remind == 1) {
				$where['is_remind'] = $is_remind;
			} elseif ($is_remind == 2) {
				// pass
			} else {
				$where['is_remind'] = 0;
			}
		} else {
			$where['is_remind'] = 0;
		}
		$remind_list = $m_remind->where($where)->order('remind_time desc')->select();
		$this->ajaxReturn(array('list' => $remind_list ?: array(), 'info' => '', 'status' => 1));
	}
}
