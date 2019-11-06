<?php 
	class BusinessProductViewModel extends ViewModel{
		public $viewFields = array(
			'r_business_product'=>array('business_id','product_info_id','sales_price','amount','description','_type'=>'LEFT'),
			'business'=>array('name'=>'name', '_on'=>'r_business_product.business_id=business.business_id','_type'=>'LEFT'),
			'product_info'=>array('price_cost','_on'=>'r_business_product.product_info_id=product_info.product_info_id'),
			'product'=>array('name'=>'name','_on'=>'product.product_id=product_info.product_id'),
		);
	}