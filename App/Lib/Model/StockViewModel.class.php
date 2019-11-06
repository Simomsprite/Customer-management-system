<?php 
	class StockViewModel extends ViewModel{
		public $viewFields = array(
			'stock'=>array('*', '_type'=>'LEFT'),
			'warehouse'=>array('name'=>'warehouse_name','status','_on'=>'stock.warehouse_id = warehouse.warehouse_id','_type'=>'LEFT'),
			'product_info'=>array('number','bar_code','price','price_cost','state','_on'=>'product_info.product_info_id = stock.product_info_id','_type'=>'LEFT'),
			'product'=>array('product_id','category_id','name'=>'product_name','standard','_on'=>'product.product_id = product_info.product_id','_type'=>'LEFT'),
		);
	}