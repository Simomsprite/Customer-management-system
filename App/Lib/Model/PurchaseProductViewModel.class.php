<?php 
class PurchaseProductViewModel extends ViewModel
{
	public $viewFields = array(
		'purchase_product' => array('*', '_type' => 'LEFT'),
		'product_info' => array('_type'=>'LEFT', '_on' => 'purchase_product.product_info_id = product_info.product_info_id'),
		'product' => array('_type' => 'LEFT', '_on' => 'product_info.product_id = product.product_id', 'name', 'standard', 'has_sn')
	);


	/**
	 * 采购单产品列表
	 */
	public function getPurchaseProductList($purchase_id)
	{
		$d_product = D('Product');
		$list = $this->where(array('purchase_id' => $purchase_id))->select();
		foreach ($list as $key => $val) {
			$list[$key]['spec'] = $d_product->getProductInfoSpec($val['product_info_id']);
		}
		return $list;
	}

}