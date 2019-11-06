<?php 
class SystemModel extends Model{

	/**
	 * 根据判断设置需要打开的菜单的html id值
	 * @author lee
	 */
	public function splicingId($module_name, $action_name, $param)
	{
		$second_str = 'index'; // 默认拼接ID值的变量
		if (($module_name == 'customer' && $action_name == 'nearby') // 附近的客户
			|| ($module_name == 'stock' && $action_name == 'instock') // 入库记录
			|| ($module_name == 'stock' && $action_name == 'outstock') // 出库记录
			|| ($module_name == 'user' && $action_name == 'contacts')
			|| ($module_name == 'setting' && $action_name == 'sendsms')
			|| ($module_name == 'setting' && $action_name == 'smsrecord')
			|| ($module_name == 'setting' && $action_name == 'sendemail')) {
			$second_str = $action_name;
		} else if ($module_name == 'stock' && ($action_name == 'transfer' || $action_name == 'transfer_view')){
			$second_str = 'transfer'; // 库存调拨
		} else if ($module_name == 'purchase' && ($action_name == 'return_goods' || $action_name == 'return_goods_view')){
			$second_str = 'return_goods'; // 采购退货
		} else if ($action_name == 'analytics' 
			|| ($module_name == 'customer' && $action_name == 'top_10')
			|| ($module_name == 'customer' && $action_name == 'new_add')
			|| ($module_name == 'business' && $action_name == 'trend_number')
			|| ($module_name == 'business' && $action_name == 'map_number')
			|| ($module_name == 'contract' && $action_name == 'analysis_number')
			|| ($module_name == 'contract' && $action_name == 'received_top10')
			|| ($module_name == 'contract' && $action_name == 'collection')
			|| ($module_name == 'contract' && $action_name == 'target_rank')
			|| ($module_name == 'report' && $action_name == 'rank')
			|| ($module_name == 'product' && $action_name == 'product_analytics')
			|| ($module_name == 'product' && $action_name == 'sales_volume_analytics')
			|| ($module_name == 'product' && $action_name == 'top_10')
			|| ($module_name == 'kaoqin' && $action_name == 'record')
			|| ($module_name == 'remind' && $action_name == 'visitor_plan_analytics')){
			$module_name = 'analytics'; // 数据分析
		} else if ($module_name == 'setting'
			|| ($module_name == 'user' && $action_name == 'department')
			|| ($module_name == 'user' && $action_name == 'index')
			|| ($module_name == 'product' && $action_name == 'category')
			|| ($module_name == 'knowledge' && $action_name == 'category')
			|| ($module_name == 'template' && $action_name == 'index')
			|| ($module_name == 'contract' && $action_name == 'examine')
			|| ($module_name == 'exam' && $action_name == 'index')
			|| ($module_name == 'kaoqin' && $action_name == 'setting')
			|| ($module_name == 'system' && $action_name == 'psssetup')){
			$module_name = 'setting'; // 系统设置
		}
		$menu_html_id = "{$module_name}-{$second_str}";
		if ($param) {
			$menu_html_id .= "-{$param}";
		}
		return $menu_html_id;
	}


	/**
	 * 设置左侧可显示的父级菜单栏，如果没有任何子菜单的权限，则父级菜单也不显示
	 * 举个栗子：如果没有线索、客户等权限，则整个【客户管理】都不显示
	 * @author lee
	 */
	public function showModuleList()
	{	
		// 客户管理
		if (checkPerByAction('leads','index')
			|| checkPerByAction('customer','index')
			|| checkPerByAction('contacts','index')
			|| checkPerByAction('customer','nearby')) {
			$show_module_list[] = 'customer';
		}
		// 商机管理
		if (checkPerByAction('business','index')) {
			$show_module_list[] = 'business';
		}
		// 合同管理
		if (checkPerByAction('contract','index') || checkPerByAction('sales','return_index')) {
			$show_module_list[] = 'contract';
		}
		// 财务管理
		if (checkPerByAction('finance','index_receivables') 
			|| checkPerByAction('finance','index_receivingorder')
			|| checkPerByAction('finance','index_payables')
			|| checkPerByAction('finance','index_paymentorder')
			|| checkPerByAction('invoice','index')) {
			$show_module_list[] = 'finance';
		}
		// 采购管理
		if (checkPerByAction('purchase','index') 
			|| checkPerByAction('purchase','return_goods') 
			|| checkPerByAction('supplier','index')) {
			$show_module_list[] = 'purchase';
		}
		// 库存管理
		if (checkPerByAction('stock','index') 
			|| checkPerByAction('stock','transfer')
			|| checkPerByAction('stock','instock')
			|| checkPerByAction('stock','outstock')
			|| checkPerByAction('warehouse','index')) {
			$show_module_list[] = 'stock';
		}
		// 产品管理
		if (checkPerByAction('product','index') 
			|| checkPerByAction('product_info','spec')
			|| checkPerByAction('goods','sn_track')) {
			$show_module_list[] = 'product';
		}
		// 数据分析
		if (checkPerByAction('leads','analytics') 
			|| checkPerByAction('customer','analytics')
			|| checkPerByAction('business','analytics')
			|| checkPerByAction('contract','analytics')
			|| checkPerByAction('finance','analytics')
			|| checkPerByAction('purchase','analytics')
			|| checkPerByAction('product','analytics')
			|| checkPerByAction('log','analytics')
			|| checkPerByAction('kaoqin','analytics')
			|| checkPerByAction('kaoqin','record')
			|| checkPerByAction('call','record')) {
			$show_module_list[] = 'analytics';
		}
		// 办公
		if (checkPerByAction('log','index') 
			|| checkPerByAction('examine','index')
			|| checkPerByAction('knowledge','index')
			|| checkPerByAction('announcement','index')
			|| checkPerByAction('sign','index')
			|| checkPerByAction('event','index')
			|| checkPerByAction('task','index')
			|| checkPerByAction('kaoqin','index')) {
			$show_module_list[] = 'office';
		}
		// 通讯录
		if (checkPerByAction('user','contacts')) {
			$show_module_list[] = 'contacts';
		}
		// 营销
		if (checkPerByAction('market','index') 
			|| checkPerByAction('setting','sendsms') 
			|| checkPerByAction('setting','smsrecord')
			|| checkPerByAction('setting','sendemail')) {
			$show_module_list[] = 'marketing';
		}
		// 系统设置
		if (checkPerByAction('user','index') 
			|| checkPerByAction('kaoqin','setting') 
			|| checkPerByAction('template','index')) {
			$show_module_list[] = 'setting';
		}
		return $show_module_list;
	}


	/**
	 * 客户列表默认场景url
	 * @author lee
	 */
	public function customerUrl()
	{
		$m_scene_default = M('SceneDefault');
		$m_scene = M('Scene');
		$customer_default_scene = $m_scene_default->where(array('role_id'=>session('role_id'),'module'=>'customer'))->getField('scene_id');
		if (!$customer_default_scene) {
			$customer_default_info = $m_scene->where(array('module'=>'customer','type'=>1))->order('id asc')->find();
		} else {
			$customer_default_info = $m_scene->where(array('id'=>$customer_default_scene))->find();
		}
		if ($customer_default_info['type'] == 1) {
			$customer_url = U('customer/index','by='.$customer_default_info['by']);
		} else {
			$customer_url = U('customer/index','scene_id='.$customer_default_info['id']);
		}
		return $customer_url ?: '#';
	}


	/**
	 * 根据权限判断【数据分析】默认的url
	 * @author lee
	 */
	public function analyticsUrl()
	{
		if (checkPerByAction('leads','analytics')) {
			$analytics_url = U('leads/analytics','content_id=1');
		} elseif (checkPerByAction('customer','analytics')){
			$analytics_url = U('customer/analytics','content_id=1');
		} elseif (checkPerByAction('business','analytics')){
			$analytics_url = U('business/analytics','content_id=1');
		} elseif (checkPerByAction('finance','analytics')){
			$analytics_url = U('finance/analytics','content_id=1');
		} elseif (checkPerByAction('product','analytics')){
			$analytics_url = U('product/analytics','content_id=1');
		} elseif (checkPerByAction('log','analytics')){
			$analytics_url = U('log/analytics','content_id=1');
		} elseif (checkPerByAction('remind','visitor_plan_analytics')){
			$analytics_url = U('remind/visitor_plan_analytics');
		} else {
			$analytics_url = U('customer/analytics','content_id=1');
		}
		return $analytics_url;
	}


	/**
	 * 根据权限判断【系统设置】默认的url
	 * @author lee
	 */
	public function settingUrl()
	{
		if (session('?admin')) {
			$setting_url = U('setting/defaultinfo');
		} elseif (checkPerByAction('user','index')){
			$setting_url = U('user/index');
		} elseif (checkPerByAction('kaoqin','setting')){
			$setting_url = U('kaoqin/setting');
		} elseif (checkPerByAction('template','index')){
			$setting_url = U('template/index');
		} else {
			$setting_url = U('setting/defaultinfo');
		}
		return $setting_url;
	}


}
