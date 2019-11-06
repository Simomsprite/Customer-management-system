<?php
/**
*相关提醒模块
*
**/
class RemindAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('add', 'delete', 'view', 'visitor_plan', 'visitor_plan_del', 'visitor_plan_edit', 'visitor_plan_dialog')
		);
		B('Authenticate', $action);
	}

	/**
	*  创建提醒
	*
	**/
	public function add(){
		if($this->isPost()){
			$module = trim($_REQUEST['module']);
			$module_ids =  $_REQUEST['module_id'];
			$module_id_list = array_unique(array_filter(explode(',', $module_ids)));
			$m_remind = M('Remind');
			$remind_type = intval($_POST['remind_type']);
			if($remind_type){
				switch($remind_type){
					case 1 : $remind_time = time()+3600; break;
					case 2 : $remind_time = time()+3600*2; break;
					case 3 : $remind_time = time()+3600*3; break;
					case 4 : $remind_time = time()+3600*6; break;
					case 5 : $remind_time = time()+3600*8; break;
					case 6 : $remind_time = time()+3600*24; break;
				}
			}else{
				$remind_time_a = $_POST['remind_time_a'];
				$remind_time_b = $_POST['remind_time_b'];
				if($remind_time_a == '' || $remind_time_b == ''){
					$remind_time = time()+600;
				}else{
					$remind_time_a = str_replace('年','-',$remind_time_a);
					$remind_time_a = str_replace('月','-',$remind_time_a);
					$remind_time_a = str_replace('日','',$remind_time_a);
					$remind_time = strtotime($remind_time_a)+(strtotime($remind_time_b)-strtotime(date('Y-m-d',time())));
				}
			}
			foreach ($module_id_list as $module_id) {
				$data = array(
					'module' => $_POST['module'],
					'module_id' => $module_id,
					'content' => $_POST['content'],
					'remind_time' => $remind_time,
					'create_role_id' => session('role_id')
				);

				$m_remind->create($data);
				if($remind_id = $m_remind->add()){
					//关联日程
					$module = $_POST['module'];
					if($module == 'customer'){}
					$event_id = dataEvent('提醒',$remind_time,'remind',$remind_id);
					// 计划任务
					if (isset($_POST['join_visitor_plan']) && $_POST['join_visitor_plan'] == 1) {
						$data = array(
							'event_id' => $event_id,
							'plan_time' => $remind_time,
							'content' => $_POST['content']
						);
						M('VisitorPlan')->add($data);
					}
				}else{
					alert('error', '设置提醒失败，请重试！',$_SERVER['HTTP_REFERER']);
				}
			}
			alert('success','设置提醒成功！',$_SERVER['HTTP_REFERER']);
		} elseif ($_GET['module'] && $_GET['module_id']) {
			$this->module = $_GET['module'];
			$this->module_id = $_GET['module_id'];
			$this->create_role_id = session('role_id');
			$this->display();
		} else {
			alert('error', L('PARAMETER ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}

	/**
	 * 提醒详情
	 *
	**/
	public function view(){
		$module_id = intval($_REQUEST['module_id']);
		$module = trim($_REQUEST['module']);
		$m_remind = M('Remind');
		if(!$module_id){
			echo '<div class="alert alert-error">参数错误！</div>';die();
		}
		//权限判断（根据客户详情权限）
		$below_ids = getPerByAction($module, 'view');

		$remind_list = $m_remind->where(array('module'=>$module,'module_id'=>$module_id,'create_role_id'=>array('in',$below_ids),'is_remind'=>0))->order('remind_time asc')->select();
		// if(!$remind_list){
		// 	echo '<div class="alert alert-error">数据不存在或已删除！</div>';die();
		// }
		$this->remind_list = $remind_list;
		$this->module_id = $module_id;
		$this->module = $module;
		$this->display();
	}

	/**
	 * 提醒删除
	 *
	 */
	public function delete(){
		$module_id = intval($_REQUEST['module_id']);
		$module = trim($_REQUEST['module']);
		$remind_id = intval($_REQUEST['remind_id']);
		$m_remind = M('Remind');
		if($this->isPost()){
			$where = array();
			$where['create_role_id'] = session('role_id');
			if($remind_id){
				$where['remind_id'] = $remind_id;
			}else{
				$where['module'] = $module;
				$where['module_id'] = $module_id;
			}
			if(!$m_remind->where($where)->find()){
				$this->ajaxReturn('','数据不存在或已删除！',0);
			}
			if($m_remind->where($where)->delete()){
				//删除关联日程
				$event_res = M('Event')->where(array('module'=>$module,'module_id'=>$module_id))->delete();;
				$this->ajaxReturn('','删除成功！',1);
			}else{
				$this->ajaxReturn('','删除失败，请重试！',0);
			}
		}
	}


	/**
	 * 访客计划列表
	 */
	public function visitor_plan()
	{
		if ($this->isAjax())
		{
			$p = $_POST['p'] ?: 1;
			$d_visitor_plan = D('VisitorPlan');
			$d_visitor_plan->getList();
			// 访客计划
			$where = array(
				'customer_id' => (int) $_POST['customer_id'],
				// 'status' => array('IN', array(0, 1)),		// 未完成 延期
				// 'plan_time' => array('BETWEEN', array(strtotime(date('Y-m-d')), strtotime(date('Y-m-d 23:59:59')))),	// 今天
				'page' => $p .', 10'
			);
			$list = $d_visitor_plan->getList($where);
			$this->ajaxReturn(array('data' => $list, 'msg' => '', 'status' => (int) $list));
		}
	}


	/**
	 * 取消（删除）访客计划
	 */
	public function visitor_plan_del()
	{
		$id = (int) $_POST['id'];
		$status = M('VisitorPlan')->where(array('id' => $id))->getField('status');
		if ($status == 4) {
			$this->ajaxReturn(array('msg' => '计划已完成，不能取消。', 'status' => 0));
		} elseif ($status == 3) {
			$this->ajaxReturn(array('msg' => '计划已放弃，不能取消。', 'status' => 0));
		}
		$res = M('VisitorPlan')->where(array('id' => $id))->delete();
		$msg = $res ? '取消成功！' : '取消失败，请重试！';
		$this->ajaxReturn(array('msg' => $msg, 'status' => (int) $res));
	}


	/**
	 * 访客计划延期
	 */
	public function visitor_plan_edit()
	{
		$id = (int) $_POST['id'];
		$view = M('VisitorPlan')->where(array('id' => $id))->find();
		if ($view['status'] == 4) {
			$this->ajaxReturn(array('msg' => '计划已完成，不能修改。', 'status' => 0));
		} elseif ($view['status'] == 3) {
			$this->ajaxReturn(array('msg' => '计划已放弃，不能修改。', 'status' => 0));
		}
		$data['plan_time'] = strtotime($_POST['plan_time']);
		$data['delay_time'] = $view['plan_time'];
		$data['status'] = 1;
		$res = M('VisitorPlan')->where(array('id' => $id))->save($data);
		$data = array();
		if ($res) {
			$msg = '延期成功！';
		} else{
			$msg = '延期失败，请重试！';
		}
		$this->ajaxReturn(array('msg' => $msg, 'status' => (int) $res));
	}

	/**
	 * 访客计划统计
	 */
	public function visitor_plan_analytics()
	{
		$content_id = $_GET['content_id'] ? intval($_GET['content_id']) : 1;
		//权限范围
		$below_ids = getPerByAction(MODULE_NAME, ACTION_NAME);
		$m_log = M('Log');
		$m_user = M('User');
		$m_examine = M('Examine');
		$role_id_array = array();
		if (intval($_GET['role'])) {
			$role_id_array = array(intval($_GET['role']));
			$params[] = "role=" . intval($_GET['role']);
		} else {
			if (intval($_GET['department'])) {
				$department_id = intval($_GET['department']);
				$params[] = "department=" . intval($_GET['department']);
				foreach (getRoleByDepartmentId($department_id, true) as $k=>$v) {
					$role_id_array[] = $v['role_id'];
				}
			}
		}
		//过滤权限范围内的role_id
		if ($role_id_array) {
			//数组交集
			$idArray = array_intersect($role_id_array,$below_ids);
		} else {
			$idArray = getPerByAction(MODULE_NAME,ACTION_NAME,false);
		}
		$p = $_GET['p'] ? intval($_GET['p']) : 1;
		import("@.ORG.Page");
		
		// 分页功能
		$role_list = $m_user->where(array('role_id'=>array('in', $idArray), 'status'=>1))->page($p.',15')->field('role_id,full_name,thumb_path')->order('user_id')->select();
		$count = $m_user->where(array('role_id'=>array('in', $idArray), 'status'=>1))->count();
		$Page = new Page($count,15);
		$this->count = $count;
		$this->assign('count',$count);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		//时间段搜索
		$search_time_year = $_GET['search_year'] ? intval($_GET['search_year']) : date('Y',time());
		$params[] = "search_year=" . intval($_GET['search_year']);
		$search_time_month = $_GET['search_month'] ? intval($_GET['search_month']) : date('m',time());
		$params[] = "search_month=" . intval($_GET['search_month']);
		$search_time = $search_time_year.'-'.$search_time_month;
		//查询使用年份、月份数组
		$min_time = M('Log')->min('create_date');
		$min_year = $min_time ? date('Y',$min_time) : date('Y');
		$max_year = date('Y');
		$year_array = array();
		for ($i=$min_year; $i <= $max_year; $i++) { 
			$year_array[] = $i;
		}
		$month_array = array('1','2','3','4','5','6','7','8','9','10','11','12');
		$this->year_array = $year_array;
		$this->month_array = $month_array;
		$this->search_time_year = $search_time_year;
		$this->search_time_month = $search_time_month;

		//当前时间
		$date = $search_time;
		$this->date_now = $date;
		//根据月份计算天数
		$days = getmonthdays(strtotime($date));
		$this->days = $days;

		//部门岗位
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission')->where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('RoleDepartment')->select();
		}else{
			$departmentList = M('RoleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);
		
		//本月时间戳范围
		$month_start_time = strtotime(date($search_time_year.'-'.$search_time_month.'-01')); 
		$month_end_time = strtotime($search_time_year."-".$search_time_month."-".date("t",strtotime($search_time)))+86400;
		$month_time = array('between',array($month_start_time,$month_end_time));
		//本年时间戳范围
		$year_start_time = strtotime(date($search_time_year."-01-01"));
		$year_end_time = strtotime(date($search_time_year."-12-31"));
		$year_time = array('between',array($year_start_time,$year_end_time));

		//获取时间范围内的每日时间戳数组(当月)
		$start = strtotime($date.'-'.'01');
		$end = strtotime($date.'-'.$days);
		$day_list = dateList($start,$end);

		//自定义时间
		$m_workrule = M('Workrule');
		//计算年休息的天数
		$year_no = $m_workrule->where(array('year'=>$search_time_year,'type'=>1))->count;
		//年总天数
		$year_count_total = round(($year_end_time-$year_start_time)/86400);
		//计算月休息天数
		$month_no_array = $m_workrule->where(array('sdate'=>$month_time,'type'=>1))->getField('sdate',true);
		$month_no = count($month_no_array);
		//月总天数
		$month_count_total = $days;

		$week_array = array(); //星期六、星期日的日期数组
		foreach($day_list as $k=>$v){
			$no_work = 1;
			$week = '';
			$week = getTimeWeek($v['sdate']);
			if(!in_array($v['sdate'],$month_no_array)){
				$no_work = 0;
			}
			$day_list[$k]['no_work'] = $no_work;
			//判断星期六、日
			if($week == '星期六' || $week == '星期日'){
				$week_array[] = $k+1;
			}
		}
		$this->week_array = $week_array;
		$now = time();
		
		$v_visitor_plan = D('VisitorPlanView');
		//沟通日志
		foreach($role_list as $k=>$v){
			//本月日志数
			$month_count = 0;
			$month_count = $v_visitor_plan->where(array('owner_role_id' => $v['role_id'], 'plan_time' => $month_time))->count();
			$done_month_count = $v_visitor_plan->where(array('status' => 4, 'owner_role_id' => $v['role_id'], 'plan_time' => $month_time))->count();
			$role_list[$k]['month_count'] = $month_count;
			$role_list[$k]['done_month_count'] = $done_month_count;

			//本年日志数
			$year_count = 0;
			$year_count = $v_visitor_plan->where(array('owner_role_id' => $v['role_id'], 'plan_time' => $year_time))->count();
			$done_year_count = $v_visitor_plan->where(array('status' => 4, 'owner_role_id' => $v['role_id'], 'plan_time' => $year_time))->count();
			$role_list[$k]['year_count'] = $year_count;
			$role_list[$k]['done_year_count'] = $done_year_count;

			//每日数据
			foreach($day_list as $key=>$val){
				if ($now > $val['sdate']) {
					$plan_count = $v_visitor_plan->where(array('owner_role_id' => $v['role_id'], 'plan_time' => array('between',array($val['sdate'],$val['edate']))))->count();
					$done_plan_count = $v_visitor_plan->where(array('status' => 4, 'owner_role_id' => $v['role_id'], 'plan_time' => array('between',array($val['sdate'],$val['edate']))))->count();
					$role_list[$k]['log_count'][$key+1]['plan_count'] = $plan_count;
					$role_list[$k]['log_count'][$key+1]['done_plan_count'] = $done_plan_count;
					$role_list[$k]['log_count'][$key+1]['sdate'] = $val['sdate'];
					$role_list[$k]['log_count'][$key+1]['lt_time'] = 1;
				} else {
					$role_list[$k]['log_count'][$key+1]['lt_time'] = 0;
				}
			}
		}

		$this->role_list = $role_list;
		$this->type_id = intval($_GET['type_id']);
		$this->content_id = intval($_GET['content_id']);
		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 访客计划 弹框列表
	 */
	public function visitor_plan_dialog()
	{
		$role_id = (int) $_GET['role_id'];
		$sdate = $_GET['sdate'];
		$where = array(
			'owner_role_id' => $role_id,
			'plan_time' => array('BETWEEN', array($sdate, $sdate + 86400))
		);
		if (isset($_GET['done']) && $_GET['done'] == 'true') {
			$where['status'] = 4;
		}
		$this->list = D('VisitorPlan')->getList($where);
		$this->display();
	}
}
