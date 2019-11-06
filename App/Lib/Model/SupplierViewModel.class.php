<?php
	class SupplierViewModel extends ViewModel {
        protected $viewFields;
		public function _initialize(){
			$main_must_field = array('*');
			$main_list = array_unique(array_merge(M('Fields')->where(array('model'=>'supplier','is_main'=>1))->getField('field', true),$main_must_field));
			$data_list = M('Fields')->where(array('model'=>'supplier','is_main'=>0))->getField('field', true);
			$data_list['_on'] = 'supplier.supplier_id = supplier_data.supplier_id';

			$data_list['_type'] = "LEFT";

			$this->viewFields = array('supplier'=>$main_list,'supplier_data'=>$data_list);
		}
	}