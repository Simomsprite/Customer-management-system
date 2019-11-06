<?php 
	class SalesViewModel extends ViewModel{
		public $viewFields = array(
			'sales'=>array('*','_type'=>'LEFT'),
			'customer'=>array('name'=>'customer_name','_on'=>'sales.customer_id=customer.customer_id'),
		);
	}

	