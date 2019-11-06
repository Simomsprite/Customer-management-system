<?php 
class SettingModel extends Model{

	/**
     * "基本参数"字段信息
     * @author lee
	 */
	public function configField()
	{
		return array(
			'contract_alert_time' => array('field' => 'contract_alert_time', 'name' => '合同提醒时间'),
			'receivables_time' => array('field' => 'receivables_time', 'name' => '回款到期提醒'),
			'business_custom' => array('field' => 'business_custom', 'name' => '商机前缀编号'),
			'contract_custom' => array('field' => 'contract_custom', 'name' => '合同编码前缀'),
			'receivables_custom' => array('field' => 'receivables_custom', 'name' => '应收款编码前缀'),
			'user_custom' => array('field' => 'user_custom', 'name' => '员工编码前缀'),
			'openrecycle' => array('field' => 'openrecycle', 'name' => '客户回收周期'),
			'customer_recovery_cycle' => array('field' => 'customer_recovery_cycle', 'name' => '客户回收周期'),
			'opennum' => array('field' => 'opennum', 'name' => '客户数限制'),
			'leads_outdays' => array('field' => 'leads_outdays', 'name' => '线索回收周期'),
			'customer_receive_cycle' => array('field' => 'customer_receive_cycle', 'name' => '客户领取周期'),
			'task_model' => array('field' => 'task_model', 'name' => '任务分配模式'),
			'search_disable_user' => array('field' => 'search_disable_user', 'name' => '可搜索已停用用户'),
			'address' => array('field' => 'address', 'name' => '默认地区'),
		);
	}


	/**
	 * 获取原配置信息【操作记录】
	 * @author lee
	 */
	public function getOldData()
	{
		$m_config = M('Config');

		// 查询原配置数据
		$config_list = $m_config->select();
		foreach ($config_list as $k => $v) {
			if ($v['name'] == 'defaultinfo') {
				$defaultinfo = unserialize($v['value']);
				$data['contract_alert_time'] = $defaultinfo['contract_alert_time'];
				$data['task_model'] = $defaultinfo['task_model'];
				$data['state'] = $defaultinfo['state'];
				$data['city'] = $defaultinfo['city'];
			} else {
				$data[$v['name']] = $v['value']; 
			}
		}
		$old_data = $this->formatData($data);
		return $old_data;
	}


	/**
	 * 获取修改后的数据【操作记录】
	 * @author lee
	 */
	public function getNewData($data)
	{
		$new_data = $this->formatData($data);
		return $new_data;
	}


	/**
	 * 格式化所需要的数据格式
	 * @author lee
	 */
	public function formatData($data)
	{
		switch ($data['customer_limit_condition']) {
			case 'day':
				$data['customer_limit_condition'] = '本天';
				break;
			case 'week':
				$data['customer_limit_condition'] = '本周';
				break;
			case 'month':
				$data['customer_limit_condition'] = '本月';
				break;
			default:
				break;
		}
		$format_data = array(
			'contract_alert_time' => $data['contract_alert_time'],
			'receivables_time' => $data['receivables_time'],
			'business_custom' => $data['business_custom'],
			'contract_custom' => $data['contract_custom'],
			'receivables_custom' => $data['receivables_custom'],
			'user_custom' => $data['user_custom'],

			// 客户回收周期
			'openrecycle' => $data['openrecycle'] == 1 ? '不启用回收规则' : '启用',
			'customer_recovery_cycle' => $data['customer_outdays'].'天未跟进或'.$data['contract_outdays'].'天未签合同',

			'opennum' => $data['opennum'] == 1 ? '启用' : '不启用',
			'leads_outdays' => $data['leads_outdays'],
			// 客户领取周期
			'customer_receive_cycle' => $data['customer_limit_condition'].'内限制领取'.$data['customer_limit_counts'].'条',
			'task_model' => $data['task_model'] == 1 ? '只允许分配给下级' : '随意分配',
			'search_disable_user' => $data['search_disable_user'] == 1 ? '是' : '否',
			'address' => $data['state'].' '.$data['city'],
		);
		return $format_data;
	}

}
