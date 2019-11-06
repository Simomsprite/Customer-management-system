<?php 
    class SupplierModel extends Model{
        protected $_validate = array();
		
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
		
        public function _initialize(){
            $fields = M('fields')->where('(model = \'\' or model = \'supplier\') and is_validate=1 and is_main=1')->select();
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
	     * 获取列表返回数据格式
	     * @param unknown $page_index
	     * @param unknown $page_size
	     * @param unknown $condition
	     * @param unknown $order
	     * @return unknown
	     */
	    function getList($page_index, $page_size, $condition, $order){
	        $list = $this->where($condition)->page($page_index.','.$page_size)->order($order)->select();
	        foreach ($list as $k => $v) {
				$supplier_contacts = array();
				$supplier_contacts = M('SupplierContacts')->where('supplier_id = %d', $v['supplier_id'])->find();
				$list[$k]['contacts_name'] = $supplier_contacts['name'];
				$list[$k]['telephone'] = $supplier_contacts['telephone'];
			}
			return $list ?: array();
	    }

	    /**
	    * 获取详情
	    * @param $pk_id 主键
	    **/
	    function getView($pk_id){
	    	$detail = D('SupplierView')->where('supplier.supplier_id = %d', $pk_id)->find();
	    	$detail['creator_name'] = M('User')->where('role_id = %d', $detail['creator_role_id'])->getField('full_name');
	    	return $detail;
	    }

	    /**
	    * 保存数据
	    * @param $pk_id 主键
	    **/
	    function saveData($pk_id = ''){
			$field_list = M('Fields')->where('model = "supplier" and in_add = 1')->order('order_id')->select();
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$_POST[$v['field']] = $_POST[$v['field']] ? implode(chr(10),$_POST[$v['field']]) : '';
					break;
					case 'datetime':
						$_POST[$v['field']] = strtotime($_POST[$v['field']]);
					break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$b = array_filter($_POST[$v['field']]);
							$_POST[$v['field']] = !empty($b) ? implode(chr(10),$b) : '';
						}
					break;
				}
			}
			if (!empty($pk_id)) {
				$ret = $this->saveMainData($pk_id);
				if ($ret) {
					$this->saveExtraData($pk_id);
					return true;
				} else {
					return false;
				}
			} else {
				$supplier_id = $this->addMainData();
				if ($supplier_id) {
					$this->addExtraData($supplier_id);
					return $supplier_id;
				} else {
					return false;
				}
			}
	    }

	    /**
	    * 添加主表数据
	    **/
	    function addMainData(){
	    	if($this->create()){
				$this->create_time = time();
				$this->update_time = time();
				$this->creator_role_id = session('role_id');
				if ($supplier_id = $this->add()) {
					return $supplier_id;
				} else {
					return false;
				}
			} else {
				return false;
			}
	    }

	    /**
	    * 添加附表数据
	    **/
	    function addExtraData($supplier_id){
	    	$d_supplier_data = D('SupplierData');
	    	if($d_supplier_data->create() !== false){
	    		$d_supplier_data->supplier_id = $supplier_id;
				$d_supplier_data->add();
				return true;
	    	} else {
	    		return false;
	    	}
	    }

	    /**
	    * 更新主表数据
	    **/
	    function saveMainData($pk_id){
	    	if($this->create()){
				$this->update_time = time();
				if ($this->where('supplier_id = %d', $pk_id)->save() !== false) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
	    }

	    /**
	    * 更新附表数据
	    **/
	    function saveExtraData($pk_id){
	    	$d_supplier_data = D('SupplierData');
	    	if($d_supplier_data->create() !== false){
				$d_supplier_data->where('supplier_id = %d', $pk_id)->save();
				return true;
	    	} else {
	    		return false;
	    	}
	    }


		/**
		 * 根据采购单获取供应商id和名称
		 */
		public function getInfobyPurchase($purchase_id)
		{
			$supplier_id = M('Purchase')->where(array('purchase_id' => $purchase_id, 'type' => 1))->getField('type_id');
			if ($supplier_id) {
				$supplier_name = $this->where(array('supplier_id' => $supplier_id))->getField('name');
				return array('supplier_id' => $supplier_id, 'supplier_name' => $supplier_name);
			} else {
				return false;
			}
		}

    }