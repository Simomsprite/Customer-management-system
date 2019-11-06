<?php

class AnalyticsAction extends Action
{

    public function _initialize(){
		$action = array(
		'permission'=>array(),
		'allow'=>array()
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
    }
    

	/**
	 * 列表
	 */
    public function index()
    {
		$this->display();
	}
	

	/**
	 * 选择展示字段
	 */
	public function dialog_field()
	{
		$mod = trim($_GET['mod']);
		$act = trim($_GET['act']); 
		$fields_id = trim($_GET['fields']); 

		if ($mod == 'customer' && $act == 'analytics') {
			$fields= D('Customer')->analytics_fields();
			if ($fields_id) {
				$fields_id_list = explode(',', $fields_id);
				$show = array_filter($fields, function ($val) use ($fields_id_list) {
					return in_array($val['id'], $fields_id_list);
				});
				$hide = array_filter($fields, function ($val) use ($fields_id_list) {
					return !in_array($val['id'], $fields_id_list);
				});
			} else {
				$show = $fields;
				$hide = array();
			}
		}

		$this->fields = array('show' => $show, 'hide' => $hide);
		$this->display();
	}


	/**
	 * 添加报表/统计图
	 */
	public function add()
	{
		$d_analytics = D('Analytics');
		if (IS_POST) {

		} else {
			$this->type = (int) $_GET['type'];
			$this->moduleList = $d_analytics->module_list;	// 可能module_list是tp的内置变量
			$this->role = array('1' => '销售角色', '财务角色', '行政角色', '其他');
			$this->department_list = getSubDepartment(0, M('role_department')->select());
			$this->role_list = M('User')->field('full_name, role_id')->where(array('status' => 1))->select();

			if ($this->type == 1) {
				
				$this->display('add_report');
			} else {
				$this->display('add_chart');
			}
		}
	}

}