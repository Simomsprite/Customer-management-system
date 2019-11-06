<?php 
	class ProductInfoViewModel extends ViewModel{
		public $viewFields;
		public function _initialize(){

			$main_must_field = array('product_id','creator_role_id','create_time','update_time','is_deleted','has_sn');
            
			$main_list = array_unique(array_merge(M('Fields')->where(array('model'=>'product','is_main'=>1))->getField('field', true),$main_must_field));
			foreach ($main_list as $k => $v) {
				if (in_array($v, array('cost_price','suggested_price'))) {
					// 移除产品信息表的相关价格，逻辑不走这里，而是查询product_info中相关价格
					unset($main_list[$k]);
				}
			}

			$main_list['_on'] = 'product_info.product_id = product.product_id';
			$main_list['_type'] = 'LEFT';

			$data_list = M('Fields')->where(array('model'=>'product','is_main'=>0))->getField('field', true);
			$data_list['_on'] = 'product.product_id = product_data.product_id';
            $data_list['_type'] = 'LEFT';
			
			$this->viewFields = array(
				'product_info' => array('*','_type'=>'LEFT'),
				'product' => $main_list,
				'product_data' => $data_list, 
				'product_category' => array('name'=>'category_name', '_on'=>'product.category_id=product_category.category_id', '_type'=>'LEFT'),
			);
		}
	}