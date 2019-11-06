<?php 
class UserModel extends Model{

	private  $_salt = "";
	
	protected $_validate;
	public function _initialize(){
		$validate[0] = 'name';
		$validate[1] = '';
		$validate[2] = L("ACCOUNT NAME ALREADY EXISTS");
		$validate[3] = 0;
		$validate[4] = 'unique';
		$validate[5] = 1;
		$this->_validate[] = $validate;
	}
	protected $_auto = array(
		array('reg_time', 'time', 1, 'function'),
		array('reg_ip', 'get_client_ip', 1, 'function'),
		array('salt','getSalt',1,'callback'),
		array('password','getPassword',1,'callback'),
	);
	
	
	protected function getSalt(){
		return $this->_salt = substr(md5(time().rand(1000,9999)),0,4);			
	}
	protected function getPassword(){
		return md5(md5(trim($_POST["password"])) . $this->_salt);
	}
	

	/**
	 * 获取员工名称
	 */
	public function get_full_name($role_id) {
		if (is_int($role_id)) {
			return M('user')->where(array('role_id' => $role_id))->getField('full_name');
		} elseif (is_array($role_id) && !empty($role_id)) {
			$role_arr = M('user')->field('role_id,full_name,thumb_path')->where(array('role_id' => array('IN', $role_id)))->select();
			foreach ($role_arr as $val) {
				$val['img'] = $val['thumb_path'] = headPathHandle($val['thumb_path']);

				$role_list[$val['role_id']] = $val;
			}
			return $role_list;
		}
		return false;
	}


	/**
	 * 授权
	 */
	public function authorize()
	{
		$arr = array(
			array(
				'name' => '客户管理',
				'list' => array(
					array(
						'name' => '线索',
						'list' => array(
							array('name' => '列表', 'url' => 'leads/index', 'type' => 1),
							array('name' => '详情', 'url' => 'leads/view', 'type' => 1),
							array('name' => '创建', 'url' => 'leads/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'leads/edit', 'type' => 1),
							array('name' => '删除', 'url' => 'leads/delete', 'type' => 1),
							array('name' => '导入', 'url' => 'leads/excelimport', 'type' => 0),
							array('name' => '导出', 'url' => 'leads/excelexport', 'type' => 0),
							array('name' => '统计', 'url' => 'leads/analytics', 'type' => 1),
						)
					),
					array(
						'name' => '线索池',
						'list' => array(
							array('name' => '删除', 'url' => 'leads/del_public', 'type' => 0),
						)
					),
					array(
						'name' => '客户',
						'list' => array(
							array('name' => '列表', 'url' => 'customer/index', 'type' => 1),
							array('name' => '详情', 'url' => 'customer/view', 'type' => 1),
							array('name' => '创建', 'url' => 'customer/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'customer/edit', 'type' => 1),
							array('name' => '删除', 'url' => 'customer/delete', 'type' => 1),
							array('name' => '锁定客户', 'url' => 'customer/customerlock', 'type' => 0),
							array('name' => '导入', 'url' => 'customer/excelimport', 'type' => 0),
							array('name' => '导出', 'url' => 'customer/excelexport', 'type' => 0),
							array('name' => '统计', 'url' => 'customer/analytics', 'type' => 1),
							array('name' => '转移', 'url' => 'customer/transfer_edit', 'type' => 0),
							array('name' => '分享', 'url' => 'customer/share', 'type' => 0),
							array('name' => '取消分享', 'url' => 'customer/close_share', 'type' => 0),
							array('name' => '附近的客户', 'url' => 'customer/nearby', 'type' => 1),
							array('name' => '访客计划统计', 'url' => 'remind/visitor_plan_analytics', 'type' => 0)
						)
					),
					array(
						'name' => '客户池',
						'list' => array(
							array('name' => '删除', 'url' => 'customer/del_resource', 'type' => 0),
						)
					),
					array(
						'name' => '联系人',
						'list' => array(
							array('name' => '列表', 'url' => 'contacts/index', 'type' => 1),
							array('name' => '详情', 'url' => 'contacts/view', 'type' => 1),
							array('name' => '创建', 'url' => 'contacts/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'contacts/edit', 'type' => 1),
							array('name' => '删除', 'url' => 'contacts/delete', 'type' => 1),
							array('name' => '导入', 'url' => 'contacts/excel_import', 'type' => 0),
							array('name' => '导出', 'url' => 'contacts/excel_export', 'type' => 0),
						)
					),
				)
			),
			array(
				'name' => '合同、商机',
				'list' => array(
					array(
						'name' => '合同',
						'list' => array(
							array('name' => '列表', 'url' => 'contract/index', 'type' => 1),
							array('name' => '详情', 'url' => 'contract/view', 'type' => 1),
							array('name' => '创建', 'url' => 'contract/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'contract/edit', 'type' => 1),
							array('name' => '删除', 'url' => 'contract/delete', 'type' => 1),
							array('name' => '导出', 'url' => 'contract/export', 'type' => 0),
							array('name' => '业绩管理', 'url' => 'contract/collection', 'type' => 0),
							array('name' => '统计', 'url' => 'contract/analytics', 'type' => 1),
							array('name' => '撤销', 'url' => 'contract/revokeCheck', 'type' => 0, 'hide' => 1),
							array('name' => '审核/撤销', 'url' => 'contract/check', 'type' => 0)
						)
					),
					array(
						'name' => '商机',
						'list' => array(
							array('name' => '列表', 'url' => 'business/index', 'type' => 1),
							array('name' => '详情', 'url' => 'business/view', 'type' => 1),
							array('name' => '创建', 'url' => 'business/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'business/edit', 'type' => 1),
							array('name' => '删除', 'url' => 'business/delete', 'type' => 1),
							array('name' => '导出', 'url' => 'business/export', 'type' => 0),
							array('name' => '统计', 'url' => 'business/analytics', 'type' => 1),
						)
					),
				)
			),
			array(
				'name' => '产品管理',
				'list' => array(
					array(
						'name' => '产品',
						'list' => array(
							array('name' => '列表', 'url' => 'product/index', 'type' => 0),
							array('name' => '详情', 'url' => 'product/view', 'type' => 0),
							array('name' => '创建', 'url' => 'product/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'product/edit', 'type' => 0),
							array('name' => '上架', 'url' => 'product/delete', 'type' => 0),
							array('name' => '下架', 'url' => 'product/revert', 'type' => 0),
							array('name' => '导入', 'url' => 'product/excelimport', 'type' => 0),
							array('name' => '导出', 'url' => 'product/excelexport', 'type' => 0),
							array('name' => '统计', 'url' => 'product/analytics', 'type' => 1)
						)
					)
				)
			),
			array(
				'name' => '财务管理',
				'list' => array(
					array(
						'name' => '公共',
						'list' => array(
							array('name' => '统计', 'url' => 'finance/analytics', 'type' => 1),
							array('name' => '审核/撤销', 'url' => 'finance/revokeCheck', 'type' => 0, 'hide' => 1),
							array('name' => '审核/撤销', 'url' => 'finance/check', 'type' => 0),
							array('name' => '首页指标', 'url' => 'finance/target', 'type' => 1)
						)
					),
					array(
						'name' => '应收款',
						'list' => array(
							array('name' => '列表', 'url' => 'finance/index_receivables', 'type' => 1),
							array('name' => '详情', 'url' => 'finance/view_receivables', 'type' => 1),
							array('name' => '创建', 'url' => 'finance/add_receivables', 'type' => 0),
							array('name' => '编辑', 'url' => 'finance/edit_receivables', 'type' => 1),
							array('name' => '删除', 'url' => 'finance/delete_receivables', 'type' => 1)
						)
					),
					array(
						'name' => '应付款',
						'list' => array(
							array('name' => '列表', 'url' => 'finance/index_payables', 'type' => 1),
							array('name' => '详情', 'url' => 'finance/view_payables', 'type' => 1),
							array('name' => '创建', 'url' => 'finance/add_payables', 'type' => 0),
							array('name' => '编辑', 'url' => 'finance/edit_payables', 'type' => 1),
							array('name' => '删除', 'url' => 'finance/delete_payables', 'type' => 1)
						)
					),
					array(
						'name' => '收款单',
						'list' => array(
							array('name' => '列表', 'url' => 'finance/index_receivingorder', 'type' => 1),
							array('name' => '详情', 'url' => 'finance/view_receivingorder', 'type' => 1),
							array('name' => '创建', 'url' => 'finance/add_receivingorder', 'type' => 0),
							array('name' => '编辑', 'url' => 'finance/edit_receivingorder', 'type' => 1),
							array('name' => '删除', 'url' => 'finance/delete_receivingorder', 'type' => 1),
							array('name' => '导出', 'url' => 'finance/export_receivingorder', 'type' => 0)
						)
					),
					array(
						'name' => '付款单',
						'list' => array(
							array('name' => '列表', 'url' => 'finance/index_paymentorder', 'type' => 1),
							array('name' => '详情', 'url' => 'finance/view_paymentorder', 'type' => 1),
							array('name' => '创建', 'url' => 'finance/add_paymentorder', 'type' => 0),
							array('name' => '编辑', 'url' => 'finance/edit_paymentorder', 'type' => 1),
							array('name' => '删除', 'url' => 'finance/delete_paymentorder', 'type' => 1)
						)
					),
					array(
						'name' => '发票',
						'list' => array(
							array('name' => '列表', 'url' => 'invoice/index', 'type' => 1),
							array('name' => '详情', 'url' => 'invoice/view', 'type' => 1),
							array('name' => '创建', 'url' => 'invoice/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'invoice/edit', 'type' => 1),
							array('name' => '删除', 'url' => 'invoice/delete', 'type' => 1),
							array('name' => '审核', 'url' => 'invoice/revokeCheck', 'type' => 0, 'hide' => 1),
							array('name' => '审核', 'url' => 'invoice/check', 'type' => 0)
						)
					),
				)
			),
			array(
				'name' => '办公',
				'list' => array(
					array(
						'name' => '工作日志',
						'list' => array(
							array('name' => '列表', 'url' => 'log/index', 'type' => 1),
							array('name' => '详情', 'url' => 'log/mylog_view', 'type' => 1),
							array('name' => '弹框详情', 'url' => 'log/viewajax', 'type' => 1, 'hide' => 1),
							array('name' => '创建', 'url' => 'log/mylog_add', 'type' => 0),
							array('name' => '编辑', 'url' => 'log/mylog_edit', 'type' => 0),
							array('name' => '删除', 'url' => 'log/log_delete', 'type' => 0),
							array('name' => '统计', 'url' => 'log/analytics', 'type' => 1),
						)
					),
					array(
						'name' => '审批',
						'list' => array(
							array('name' => '列表', 'url' => 'examine/index', 'type' => 1),
							array('name' => '详情', 'url' => 'examine/view', 'type' => 1),
							array('name' => '创建', 'url' => 'examine/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'examine/edit', 'type' => 1),
							array('name' => '删除', 'url' => 'examine/delete', 'type' => 1),
							array('name' => '统计', 'url' => 'examine/analytics', 'type' => 1),
							array('name' => '审核', 'url' => 'examine/add_examine', 'type' => 0)
						)
					),
					array(
						'name' => '知识',
						'list' => array(
							array('name' => '列表', 'url' => 'knowledge/index', 'type' => 1),
							array('name' => '详情', 'url' => 'knowledge/view', 'type' => 1),
							array('name' => '创建', 'url' => 'knowledge/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'knowledge/edit', 'type' => 1),
							array('name' => '删除', 'url' => 'knowledge/delete', 'type' => 1),
							array('name' => '导入', 'url' => 'knowledge/excelimport', 'type' => 1),
							array('name' => '导出', 'url' => 'knowledge/excelexport', 'type' => 0)
						)
					),
					array(
						'name' => '公告',
						'list' => array(
							array('name' => '列表', 'url' => 'announcement/index', 'type' => 0),
							array('name' => '详情', 'url' => 'announcement/view', 'type' => 0),
							array('name' => '创建', 'url' => 'announcement/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'announcement/edit', 'type' => 1),
							array('name' => '删除', 'url' => 'announcement/delete', 'type' => 1),
							array('name' => '阅读记录', 'url' => 'announcement/read_list', 'type' => 1)
						)
					),
					array(
						'name' => '签到',
						'list' => array(
							array('name' => '列表', 'url' => 'sign/index', 'type' => 1)
						)
					),
					array(
						'name' => '日程',
						'list' => array(
							array('name' => '列表', 'url' => 'event/index', 'type' => 0)
						)
					),
					array(
						'name' => '任务',
						'list' => array(
							array('name' => '列表', 'url' => 'task/index', 'type' => 0),
							array('name' => '详情', 'url' => 'task/view', 'type' => 0),
							array('name' => '创建', 'url' => 'task/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'task/edit', 'type' => 0),
							array('name' => '归档', 'url' => 'task/delete', 'type' => 0),
							array('name' => '删除', 'url' => 'task/del', 'type' => 0)
						)
					),
					array(
						'name' => '考勤',
						'list' => array(
							array('name' => '统计', 'url' => 'kaoqin/analytics', 'type' => 1),
							array('name' => '考勤导出', 'url' => 'kaoqin/export', 'type' => 1),
							array('name' => '考勤规则', 'url' => 'kaoqin/setting', 'type' => 0)
						)
					),
					array(
						'name' => '模板打印',
						'list' => array(
							array('name' => '列表', 'url' => 'template/index', 'type' => 0),
							array('name' => '创建', 'url' => 'template/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'template/edit', 'type' => 0)
						)
					)
				)
			),
			array(
				'name' => '营销',
				'list' => array(
					array(
						'name' => '营销',
						'list' => array(
							array('name' => '发送短信', 'url' => 'setting/sendsms', 'type' => 0),
							array('name' => '短信发件箱', 'url' => 'setting/smsrecord', 'type' => 0),
							array('name' => '发送邮件', 'url' => 'setting/sendemail', 'type' => 0)
						)
					),
					array(
						'name' => '市场活动',
						'list' => array(
							array('name' => '列表', 'url' => 'market/index', 'type' => 1),
							array('name' => '详情', 'url' => 'market/view', 'type' => 0),
							array('name' => '创建', 'url' => 'market/add', 'type' => 0),
							array('name' => '编辑', 'url' => 'market/edit', 'type' => 0),
						)
					),
					array(
						// 呼叫中心须开启才展示
						'name' => '呼叫中心',
						'list' => array(
							array('name' => '统计', 'url' => 'call/analytics', 'type' => 1)
						)
					)
				)
			),
			array(
				'name' => '用户',
				'list' => array(
					array(
						'name' => '用户',
						'list' => array(
							array('name' => '列表', 'url' => 'user/index', 'type' => 0),
							array('name' => '详情', 'url' => 'user/view', 'type' => 0),
							array('name' => '创建', 'url' => 'user/user_add', 'type' => 0),
							array('name' => '编辑', 'url' => 'user/edit', 'type' => 0)
						)
					),
					array(
						'name' => '通讯录',
						'list' => array(
							array('name' => '列表', 'url' => 'user/contacts', 'type' => 0)
						)
					)
				)
			)
		);

		// 开启进销存
		if (C('PSS_STATUS')) {
			$arr[] = $this->pssAuthorize();
		}

		return $arr;
	}

	/**
	 * 进销存授权
	 */
	private function pssAuthorize()
	{
		return array(
			'name' => '进销存',
			'remark' => '出、入库权限由相关仓库负责人拥有',
			'list' => array(
				array(
					'name' => '采购管理',
					'list' => array(
						array('name' => '列表', 'url' => 'purchase/index', 'type' => 1),
						array('name' => '详情', 'url' => 'purchase/view', 'type' => 1),
						array('name' => '创建', 'url' => 'purchase/add', 'type' => 0),
						array('name' => '编辑', 'url' => 'purchase/edit', 'type' => 1),
						array('name' => '删除', 'url' => 'purchase/delete', 'type' => 0),
						array('name' => '导出', 'url' => 'purchase/export', 'type' => 0),
						array('name' => '统计', 'url' => 'purchase/analytics', 'type' => 0)
					)
				),
				array(
					'name' => '采购退货',
					'list' => array(
						array('name' => '列表', 'url' => 'purchase/return_goods', 'type' => 1),
						array('name' => '详情', 'url' => 'purchase/return_goods_view', 'type' => 1),
						array('name' => '创建', 'url' => 'purchase/return_goods_add', 'type' => 0),
						array('name' => '编辑', 'url' => 'purchase/return_goods_edit', 'type' => 1),
						array('name' => '删除', 'url' => 'purchase/return_delete', 'type' => 0),
					)
				),
				array(
					'name' => '销售退货',
					'list' => array(
						array('name' => '列表', 'url' => 'sales/return_index', 'type' => 1),
						array('name' => '详情', 'url' => 'sales/return_view', 'type' => 1),
						array('name' => '创建', 'url' => 'sales/return_add', 'type' => 0),
						array('name' => '编辑', 'url' => 'sales/return_edit', 'type' => 1),
						array('name' => '删除', 'url' => 'sales/return_delete', 'type' => 0),
					)
				),
				array(
					'name' => '库存管理',
					'list' => array(
						array('name' => '库存列表', 'url' => 'stock/index', 'type' => 0),
						array('name' => '库存编辑', 'url' => 'stock/edit', 'type' => 0),
						array('name' => '库存导出', 'url' => 'stock/export', 'type' => 0),
						array('name' => '入库记录', 'url' => 'stock/instock', 'type' => 1),
						array('name' => '出库记录', 'url' => 'stock/outstock', 'type' => 1),
						array('name' => '库存调拨', 'url' => 'stock/transfer', 'type' => 1),
						array('name' => '调拨详情', 'url' => 'stock/transfer_view', 'type' => 1),
						array('name' => '调拨添加', 'url' => 'stock/transfer_add', 'type' => 0),
						array('name' => '调拨编辑', 'url' => 'stock/transfer_edit', 'type' => 1),
						array('name' => '仓库列表', 'url' => 'warehouse/index', 'type' => 0),
						array('name' => '仓库创建', 'url' => 'warehouse/add', 'type' => 0),
						array('name' => '仓库编辑', 'url' => 'warehouse/edit', 'type' => 0),
					)
				),
				array(
					'name' => 'SN(产品序列号)',
					'list' => array(
						array('name' => '列表', 'url' => 'goods/sn_track', 'type' => 0),
						array('name' => '详情', 'url' => 'goods/sn_view', 'type' => 0),
						array('name' => '创建', 'url' => 'purchase/dialogaddsn', 'type' => 0),
						array('name' => '删除', 'url' => 'purchase/deletesn', 'type' => 0),
					)
				),
				array(
					'name' => '供应商管理',
					'list' => array(
						array('name' => '列表', 'url' => 'supplier/index', 'type' => 0),
						array('name' => '详情', 'url' => 'supplier/view', 'type' => 0),
						array('name' => '创建', 'url' => 'supplier/add', 'type' => 0),
						array('name' => '编辑', 'url' => 'supplier/edit', 'type' => 0)
					)
				),
				array(
					'name' => '供应商联系人',
					'list' => array(
						array('name' => '创建', 'url' => 'supplier/contacts_add', 'type' => 0),
						array('name' => '编辑', 'url' => 'supplier/contacts_edit', 'type' => 0),
						array('name' => '删除', 'url' => 'supplier/contacts_delete', 'type' => 0)
					)
				)
			)
		);
	}

}
