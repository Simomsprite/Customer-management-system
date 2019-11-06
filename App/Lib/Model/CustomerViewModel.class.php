<?php
	class CustomerViewModel extends ViewModel {
        protected $viewFields;
		public function _initialize(){
			$main_must_field = array('customer_id','owner_role_id','is_locked','creator_role_id','contacts_id','delete_role_id','create_time','delete_time','update_time','get_time','is_deleted');
			$main_list = array_unique(array_merge(M('Fields')->where(array('model'=>'customer','is_main'=>1))->getField('field', true),$main_must_field));
			$data_list = M('Fields')->where(array('model'=>'customer','is_main'=>0))->getField('field', true);
			$data_list['_on'] = 'customer.customer_id = customer_data.customer_id';

			$data_list['_type'] = "LEFT";
			//置顶逻辑
			$data_top = array('set_top','top_time');

			$data_top['_on'] = "customer.customer_id = top.module_id and top.module = 'customer' and top.create_role_id = ".session('role_id');
			$data_top['_type'] = "LEFT";

			//首要联系人（姓名、电话）
			$data_contacts = array('name'=>'contacts_name', 'telephone'=>'contacts_telephone', 'saltname'=>'contacts_saltname');
			$data_contacts['_on'] = "customer.contacts_id = contacts.contacts_id";
			$data_contacts['_type'] = "LEFT";

			$this->viewFields = array(
				'customer' => $main_list,
				'customer_data' => $data_list,
				'top' => $data_top,
				'contacts' => $data_contacts,
				'user' => array('_type' => 'LEFT', '_on' => 'customer.owner_role_id=user.role_id', 'full_name' => 'owner_role_full_name'),
			);
		}

		public function export()
		{
			$main_must_field = array('customer_id','owner_role_id','is_locked','creator_role_id','contacts_id','delete_role_id','create_time','delete_time','update_time','get_time','is_deleted');
			$main_field = array_unique(array_merge(M('Fields')->where(array('model'=>'customer','is_main'=>1))->getField('field', true),$main_must_field));
			$field = array_map(function ($val) { return 'customer.' . $val; }, $main_field);
			$data_field = M('Fields')->where(array('model'=>'customer','is_main'=>0))->getField('field', true);
			$data_field = array_map(function ($val) { return 'cd.' . $val; }, $data_field);
			$top_field = array_map(function ($val) { return 'top.' . $val; }, array('set_top','top_time'));
			$user_field = array('u.full_name' => 'owner_role_name');
			$business_field = array('b.code', 'b.business_id', 'bs.order_id', 'bs.is_end', 'bs.name' => 'status');
			$field = array_merge($field, $data_field, $top_field, $user_field, $business_field);

			$p = C('DB_PREFIX');
			$m = M('Customer customer');
				$m->join('LEFT JOIN '. $p .'customer_data cd ON customer.customer_id = cd.customer_id')
				->join('LEFT JOIN '. $p .'top top ON customer.customer_id=top.module_id and top.module = "customer" and top.create_role_id=' . session('role_id'))
				->join('LEFT JOIN '. $p .'r_contacts_customer r ON customer.customer_id=r.customer_id')
				->join('LEFT JOIN '. $p .'contacts co ON r.contacts_id=co.contacts_id')
				->join('LEFT JOIN '. $p .'user u ON customer.owner_role_id=u.role_id')
				->join('LEFT JOIN '. $p .'business b ON customer.customer_id=b.customer_id')
				->join('LEFT JOIN '. $p .'business_status bs ON b.status_id = bs.status_id')
				->field($field);
			return $m;
		}
	}