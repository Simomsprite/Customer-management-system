<?php 
/**
* 业绩统计中心
*
**/
class ReportAction extends Action {
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('rank')
		);
		B('Authenticate', $action);
	}


	/**
	 * 合同金额、回款金额、签约合同数、产品销量、新增客户数、新增联系人数、跟进次数统计排行榜
	 * @param $rank_type 
	 * @author lee
	 */
	public function rank()
	{
		$d_role_view = D('RoleView');
		$d_search = D('Search');

		// 统计类型
		$rank_type = $_GET['rank_type'] = $_GET['rank_type'] ? trim($_GET['rank_type']) : 'contract_amount';

		// 权限共用对应模块的统计方法的权限,特殊处理
		$below_ids = $this->per_judge($rank_type);
		if ($below_ids === false) {
			alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
		}

		// 是否仅查询销售岗【默认sale只查销售、all全部】、roleIdRange() 方法已处理包含部门或员工的搜索情况，且根据权限已过滤
		$range = 'all';
		$role_ids = $d_search->roleIdRange($below_ids, $range);

		// 时间段搜索
		if ($_GET['between_date']) {
			$between_date = explode(' - ',trim($_GET['between_date']));
			$start_time = strtotime($between_date[0]);
			$end_time = strtotime($between_date[1]);
		} else {
			// 默认当月 
			$start_time = strtotime(date('Y-m-01 00:00:00'));
			$end_time = time();
		}
		$this->start_date = date('Y-m-d', $start_time);
		$this->end_date = date('Y-m-d', $end_time);

		switch ($rank_type) {
			case 'contract_amount':
				// 合同金额
				$where['create_time'] = array('between', array($start_time, $end_time));
				$where['owner_role_id'] = array('in', $role_ids ?: '');
				$where['is_checked'] = 1;
				$list = $this->contract_amount($where);
				$this->rank_name = '合同金额排行榜';
				$this->rank_template = 'Report:contract_amount';
				break;
			case 'receive_amount':
				// 回款金额
				$where['pay_time'] = array('between', array($start_time, $end_time));
				$where['owner_role_id'] = array('in', $role_ids ?: '');
				$where['status'] = 1;
				$list = $this->receive_amount($where);
				$this->rank_name = '回款金额排行榜';
				$this->rank_template = 'Report:receive_amount';
				break;
			case 'contract_count':
				// 签约合同数
				$where['due_time'] = array('between', array($start_time, $end_time));
				$where['owner_role_id'] = array('in', $role_ids ?: '');
				$where['is_checked'] = 1;
				$list = $this->contract_count($where);
				$this->rank_name = '签约合同数排行榜';
				$this->rank_template = 'Report:contract_count';
				break;
			case 'contract_product_count':
				// 产品销量
				$where = "(contract.create_time BETWEEN {$start_time} AND {$end_time})";
				$role_ids_str = implode(',', $role_ids);
				$where .= "AND (contract.owner_role_id IN ({$role_ids_str}))";
				$where .= "AND (contract.is_checked = 1)";
				$list = $this->contract_product_count($where);
				$this->rank_name = '产品销量排行榜';
				$this->rank_template = 'Report:contract_product_count';
				break;
			case 'customer_add':
				// 新增客户数
				$where['create_time'] = array('between', array($start_time, $end_time));
				$where['creator_role_id'] = array('in', $role_ids ?: '');
				$where['is_delete'] = 0;
				$list = $this->customer_add($where);
				$this->rank_name = '新增客户数排行榜';
				$this->rank_template = 'Report:customer_add';
				break;
			case 'contacts_add':
				// 新增联系人数
				$where['create_time'] = array('between', array($start_time, $end_time));
				$where['creator_role_id'] = array('in', $role_ids ?: '');
				$where['is_delete'] = 0;
				$list = $this->contacts_add($where);
				$this->rank_name = '新增联系人数排行榜';
				$this->rank_template = 'Report:contacts_add';
				break;
			case 'follow_count':
				// 跟进次数
				$where['create_date'] = array('between', array($start_time, $end_time));
				$where['role_id'] = array('in', $role_ids ?: '');
				$list = $this->follow_count($where);
				$this->rank_name = '跟进次数排行榜';
				$this->rank_template = 'Report:follow_count';
				break;
			default:
				break;
		}
		foreach ($list as $k => $v) {
			$role = $d_role_view->field('user_name,department_name')->where('role.role_id = %d', $v['role_id'])->find();
			$list[$k] += $role;
		}	
		$this->assign('list', $list);
// p($list,'');
		// 部门搜索
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type = M('Permission')->where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('roleDepartment')->select();
		}else{
			$departmentList = M('roleDepartment')->where('department_id = %d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);
		
		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * 合同金额排行榜
	 * @author lee
	 */
	public function contract_amount($where)
	{
		$list = M('Contract')->field('owner_role_id as role_id,sum(price) as amount')->where($where)->group('role_id')->order('amount desc')->select();
		return $list;
	}


	/**
	 * 回款金额排行榜
	 * @author lee
	 */
	public function receive_amount($where)
	{
		$list = M('Receivingorder')->field('owner_role_id as role_id,sum(money) as amount')->where($where)->group('role_id')->order('amount desc')->select();
		return $list;
	}


	/**
	 * 合同签约数排行榜
	 * @author lee
	 */
	public function contract_count($where)
	{
		$list = M('Contract')->field('owner_role_id as role_id,count(*) as count')->where($where)->group('role_id')->order('count desc')->select();
		return $list;
	}


	/**
	 * 产品销量排行榜
	 * @author lee
	 */
	public function contract_product_count($where)
	{
		$pre = C('DB_PREFIX'); // 获取表前缀
		$sql = "SELECT 
					contract.owner_role_id as role_id,sum(sales_product.amount) as product_sale_sum
				FROM 
				 	{$pre}sales_product sales_product 
					LEFT JOIN {$pre}sales sales ON sales_product.sales_id = sales.sales_id 
					LEFT JOIN {$pre}r_contract_sales r_contract_sales ON sales.sales_id = r_contract_sales.sales_id
					LEFT JOIN {$pre}contract contract ON r_contract_sales.contract_id = contract.contract_id
				WHERE 
					{$where} 
				GROUP BY 
					contract.owner_role_id
				ORDER BY 
  					product_sale_sum desc";
		$list = M()->query($sql);
		// echo sqlFormat(M()->_sql());
		// p($list);
		return $list;
	}


	/**
	 * 新增客户数排行榜
	 * @author lee
	 */
	public function customer_add($where)
	{
		$list = M('Customer')->field('creator_role_id as role_id,count(*) as count')->where($where)->group('role_id')->order('count desc')->select();
		return $list;
	}


	/**
	 * 新增联系人数排行榜
	 * @author lee
	 */
	public function contacts_add($where)
	{
		$list = M('Contacts')->field('creator_role_id as role_id,count(*) as count')->where($where)->group('role_id')->order('count desc')->select();
		return $list;
	}

	
	/**
	 * 跟进次数排行榜
	 * @author lee
	 */
	public function follow_count($where)
	{
		$list = M('Log')->field('role_id,count(*) as count')->where($where)->group('role_id')->order('count desc')->select();
		return $list;
	}


	/**
	 * 权限共用对应模块的统计方法的权限,特殊处理
	 * @author lee
	 */
	public function per_judge($rank_type)
	{
		switch ($rank_type) {
			case 'contract_amount': // 合同金额
			case 'receive_amount':  // 回款金额
			case 'contract_count':  // 签约合同数
				$module_name = 'contract';
				$action_name = 'analytics';
				break;
			case 'contract_product_count':  // 产品销量
				$module_name = 'product';
				$action_name = 'analytics';
				break;
			case 'customer_add':  // 新增客户数
			case 'follow_count':  // 跟进次数
				$module_name = 'customer';
				$action_name = 'analytics';
				break;
			case 'contacts_add':  // 新增联系人数
				$module_name = 'contacts';
				$action_name = 'analytics';
				break;
			default:
				break;
		}
		if (!checkPerByAction($module_name, $action_name)) {
			return false;
		} else {
			// 默认权限范围
			return getPerByAction($module_name, $action_name);
		}
	}
	

}
