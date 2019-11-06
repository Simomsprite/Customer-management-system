<?php 
    class ContractModel extends Model{
		protected function _validationFieldItem($data,$val) {
			switch(strtolower(trim($val[4]))) {
				case 'function':// 使用函数进行验证
				case 'callback':// 调用方法进行验证
					$args = isset($val[6])?(array)$val[6]:array();
					if(is_string($val[0]) && strpos($val[0], ','))
						$val[0] = explode(',', $val[0]);
					if(is_array($val[0])){
						// 支持多个字段验证
						foreach($val[0] as $field)
							$_data[$field] = $data[$field];
						array_unshift($args, $_data);
					}else{
						array_unshift($args, $data[$val[0]]);
					}
					if('function'==$val[4]) {
						return call_user_func_array($val[1], $args);
					}else{
						return call_user_func_array(array(&$this, $val[1]), $args);
					}
				case 'confirm': // 验证两个字段是否相同
					return $data[$val[0]] == $data[$val[1]];
				case 'unique': // 验证某个值是否唯一
					if($data[$val[0]]){
						if(is_string($val[0]) && strpos($val[0],','))
							$val[0]  =  explode(',',$val[0]);
						$map = array();
						if(is_array($val[0])) {
							// 支持多个字段验证
							foreach ($val[0] as $field)
								$map[$field]   =  $data[$field];
						}else{
							$map[$val[0]] = $data[$val[0]];
						}
						if(!empty($data[$this->getPk()])) { // 完善编辑的时候验证唯一
							$map[$this->getPk()] = array('neq',$data[$this->getPk()]);
						}
						if($this->where($map)->find())   return false;
						return true;
					}else{
						return true;
					}
				default:  // 检查附加规则
					return $this->check($data[$val[0]],$val[1],$val[4]);
			}
		}
		
        protected $_validate = array();
		public function _initialize(){
            $fields = M('fields')->where('(model = \'\' or model = \'contract\') and is_validate=1 and is_main=1')->select();
			foreach($fields as $field){
				$validate = array();
				if($field['is_null']){
					$validate[0] = $field['field'];
					$validate[1] = 'require';
					$validate[2] = L('NOT NULL',array($field['name']));
					$validate[3] = 0;
					$validate[4] = '';
					$validate[5] = 3;
					$this->_validate[] = $validate;
				}
				
				$validate[0] = $field['field'];
				$validate[1] = '';
				$validate[2] = L('FORMAT ERROR',array($field['name']));
				$validate[3] = 0;
				$validate[4] = 'regex';
				$validate[5] = 3;
				switch ($field['form_type']){
					case 'email';
						$validate[1] = '/|^(\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*)?$/';
						$this->_validate[] = $validate;
						break;
					case 'mobile';
						$validate[1] = '/|^1[356789][0-9]{9}$/';
						$this->_validate[] = $validate;
						break;
					case 'phone';
						$validate[1] = '/|^((([0+]\d{2,3}-)?(0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?)?$/';
						$this->_validate[] = $validate;
						break;
					case 'number';
						$validate[1] = '/|^\d+$/';
						$this->_validate[] = $validate;
						break;
				}
				
				if($field['is_unique']){
					$validate[0] = $field['field'];
					$validate[1] = '';
					$validate[2] = L('ALREADY EXISTS',array($field['name']));
					$validate[3] = 0;
					$validate[4] = 'unique';
					$validate[5] = 3;
					$this->_validate[] = $validate;
				}
				
			}
        }

        /**
		 * 获取合同关联销售单的产品列表
		 * @param contract_id
		 * 返回值特别说明：stock_count 该产品库存数量 amount 销售数量  out_count 已出库数量
		 */
        function getProductList($contract_id){
			$d_product_info = D('ProductInfo');
			$d_stock_out = D('StockOut');
			$d_stock = D('Stock');

			//销售单信息
			$sales_id = M('rContractSales')->where('contract_id = %d && sales_type = 0',$contract_id)->getField('sales_id');
			$sales = D('SalesView')->where('sales_id = %d', $sales_id)->find();

			//商机编号
			$business_id = D('ContractView')->where(array('contract_id'=>$contract_id))->getField('business_id');
			$sales['business'] = M('Business')->where('business_id = %d',$business_id)->getField('code');

			//产品信息
			$product_list = M('salesProduct')->where('sales_id = %d',$sales['sales_id'])->order('sales_product_id ASC')->select();
			foreach($product_list as $k=>$v){
				$product_info = $d_product_info->getNameSpec($v['product_info_id']);
				$product_list[$k] = array_merge($product_list[$k], $product_info);
				$product_list[$k]['out_count'] = $d_stock_out->getStockOutCount($sales_id, $v['product_info_id']);
				$product_list[$k]['stock_count'] = $d_stock->getProductStock($v['product_info_id']);
			}
			return array('product_list'=>$product_list, 'sales'=>$sales);
        }



        /**
		 * 通过销售id获取合同信息
		 * @param sales_id
		 */
        function getContractBySalesId($sales_id){
        	$contract_id = M('rContractSales')->where('sales_id = %d', $sales_id)->getField('contract_id');
        	return $this->where('contract_id = %d', $contract_id)->field('contract_id,number,contract_name')->find();
        }


	}
		
