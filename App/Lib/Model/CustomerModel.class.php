<?php 
    class CustomerModel extends Model{
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
            $fields = M('fields')->where('(model = \'\' or model = \'customer\') and is_validate=1 and is_main=1')->select();
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
						$validate[1] = '/|^1[358][0-9]{9}$/';
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
				
				if($field['is_unique']) {
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
         * 通过销售退货Id获取销售退货信息、合同信息和客户信息
         * purchase_id 采购单type=2时，即为销售退货
         */
        function getInfoByPurchaseId($purchase_id)
       	{
        	$data['return_info'] = M('Purchase')->where('purchase_id = %d', $purchase_id)->field('purchase_id,number,type_id as sales_id,purchase_amount as return_money')->find();

        	$contract_id = M('rContractSales')->where('sales_id = %d', $data['return_info']['sales_id'])->getField('contract_id');
        	$data['contract_info'] = M('Contract')->where('contract_id = %d', $contract_id)->field('contract_id,number,customer_id')->find();

        	$data['customer_info'] = M('Customer')->where('customer_id = %d', $data['contract_info']['customer_id'])->field('customer_id,name')->find();
        	return $data;
        }


        /**
         * 返回属于客户或属于客户池的where条件【系统设置专用】
         * @param $belong 默认 private 【客户查询条件】  public 【客户池查询条件】
         * @param $table 默认 '' 表重命名  不为空尾部须加 "."   
         * @author lee
         */
        public function where_belong($belong = 'private', $table = '')
        {
        	$m_config = M('Config');

        	// 客户未跟进时间期限
			$outdays = $m_config->where('name="customer_outdays"')->getField('value');
			$outdate = empty($outdays) ? 0 : time()-86400*$outdays;

			// 客户get_time时间期限
			$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
			$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
			$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;

			$openrecycle = $m_config->where('name="openrecycle"')->getField('value');
			if($openrecycle == 1){
				// 未启用自动回收
				if ($belong == 'private') {
					$where[$table .'owner_role_id'] = array('neq', 0);
				} else if($belong == 'public') {
					$where[$table .'owner_role_id'] = array('eq', 0);
				}
			} else if ($openrecycle == 2) {
				// 启用自动回收
				if ($belong == 'private') {
					$where['_string'] = '('. $table .'update_time > '.$outdate.' AND '. $table .'get_time > '.$contract_outdays.') OR '. $table .'is_locked = 1';
				} else if($belong == 'public') {
					$where['_string'] = $table ."owner_role_id=0 or (". $table ."update_time < $outdate and ". $table ."is_locked = 0) or (". $table ."get_time < $contract_outdays and ". $table ."is_locked = 0)";
				}
			}
			return $where;
        }



        /**
         * 返回属于客户或属于客户池的where条件，用于客户信息是联表查询的情况
         * @param $belong 默认 private 【客户查询条件】  public 【客户池查询条件】
         * @author lee
         */
        public function where_customer($belong = 'private')
        {
        	$m_config = M('Config');

        	// 客户未跟进时间期限
			$outdays = $m_config->where('name="customer_outdays"')->getField('value');
			$outdate = empty($outdays) ? 0 : time()-86400*$outdays;

			// 客户get_time时间期限
			$c_outdays = $m_config->where('name="contract_outdays"')->getField('value');
			$c_outdays = empty($c_outdays) ? 0 : $c_outdays;
			$contract_outdays = empty($c_outdays) ? 0 : time()-86400*$c_outdays;

			$openrecycle = $m_config->where('name="openrecycle"')->getField('value');
			if($openrecycle == 1){
				// 未启用自动回收
				if ($belong == 'private') {
					// owner_role_id是自己或在自己的权限内，请求方法会根据不同的情况已做处理，这里直接返回空数组即可
					$where = array();
				} else if($belong == 'public') {
					$where['customer.owner_role_id'] = array('eq', 0);
				}
			} else if ($openrecycle == 2) {
				// 启用自动回收
				if ($belong == 'private') {
					$where['_string'] = '(customer.update_time > '.$outdate.' AND customer.get_time > '.$contract_outdays.') OR customer.is_locked = 1';
				} else if($belong == 'public') {
					$where['_string'] = "customer.owner_role_id=0 or (customer.update_time < $outdate and customer.is_locked = 0) or (customer.get_time < $contract_outdays and customer.is_locked = 0)";
				}
			}
			return $where;
        }


		/**
		 * 员工客户分析列表展示字段
		 */
		public function analytics_fields()
		{
			return array(
				array('id' => 1, 'name' => '员工编号','field' => 'number'),
				array('id' => 2, 'name' => '客户数','field' => 'own_customer_count'),
				array('id' => 3, 'name' => '成交客户数','field' => 'success_customer_count'),
				array('id' => 4, 'name' => '客户成交率','field' => 'success_customer_rate'),
				array('id' => 5, 'name' => '商机数','field' => 'own_business_count'),
				array('id' => 6, 'name' => '赢单商机数','field' => 'success_business_count'),
				array('id' => 7, 'name' => '商机赢单率','field' => 'success_business_rate'),
				array('id' => 8, 'name' => '合同总金额(元)','field' => 'contract_price'),
				array('id' => 9, 'name' => '平均合同金额(元)','field' => 'contract_average'),
				array('id' => 10, 'name' => '回款金额(元)','field' => 'receivingorder_price'),
				array('id' => 11, 'name' => '未回款金额(元)','field' => 'un_receivingorder_price'),
				array('id' => 12, 'name' => '回款比例','field' => 'receivingorder_rate'),
				array('id' => 13, 'name' => '有效客户数','field' => 'effect_customer_count'),
				array('id' => 14, 'name' => '新增客户数','field' => 'newadd_customer_count'),
				array('id' => 15, 'name' => '首次成交客户数','field' => 'first_deal_customer_count'),
				array('id' => 16, 'name' => '再次成交客户数','field' => 'again_deal_customer_count'),
			);
		}


    }