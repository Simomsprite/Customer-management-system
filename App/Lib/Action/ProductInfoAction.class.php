<?php
class ProductInfoAction extends Action {
	
	public function _initialize(){
		$action = array(
			'permission'=>array('spec_name','spec_val','product_spec_add','upper_or_lower','edit'),
			'allow'=>array()
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME); 
	}


	/**
	* 产品规格配置
	* author lee
	**/
	public function spec(){
		$m_product_spec = M('ProductSpec');

		$where = array();
		$list = $m_product_spec->where($where)->select();
		foreach ($list as $k => $v) {
			$list[$k]['category_name'] = M('ProductCategory')->where('category_id = %d', $v['category_id'])->getField('name');
		}

		$this->assign('list', $list);
		$this->alert = parseAlert();
		$this->display();
	}


	/**
	* 产品规格配置添加
	* author lee
	**/
	public function spec_add(){
		if (IS_POST) {
			$m_product_spec = M('ProductSpec');
			$data['name'] = $this->_post('name','trim');
			$data['category_id'] = $this->_post('category_id','intval');

			$spec_val = array_filter($this->_post('spec_val'));
			$data['spec_val'] = implode(',', $spec_val);
			$spec_id = $m_product_spec->add($data);
			if ($spec_id) {
				alert('success', '保存成功', $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', '保存失败', $_SERVER['HTTP_REFERER']);
			}
		} else {
			//产品类别
			$category_list = D('Product')->getCategoryList();
			$this->assign('category_list', $category_list);

			$this->display();
		}
	}


	/**
	* 产品规格配置编辑
	* author lee
	**/
	public function spec_edit(){
		$m_product_spec = M('ProductSpec');
		$spec_id = $this->_request('spec_id','intval');
		if (IS_POST) {
			$data['name'] = $this->_post('name','trim');
			$data['category_id'] = $this->_post('category_id','intval');

			$spec_val = array_filter($this->_post('spec_val'));
			$data['spec_val'] = implode(',', $spec_val);
			$m = $m_product_spec->where('spec_id = %d', $spec_id)->save($data);
			if ($m !== false) {
				alert('success', '保存成功', $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', '保存失败', $_SERVER['HTTP_REFERER']);
			}
		} else {
			$spec = $m_product_spec->where('spec_id = %d', $spec_id)->find();
			$spec['spec_val'] = explode(',', $spec['spec_val']);
			$this->assign('spec', $spec);

			//产品类别
			$category_list = D('Product')->getCategoryList();
			$this->assign('category_list', $category_list);

			$this->display();
		}
	}


	/**
	* 产品规格配置删除
	* author lee
	**/
	public function spec_delete(){
		$m_product_spec = M('ProductSpec');

		$spec_ids = $this->_post('spec_ids');
		$where['spec_id'] = array('in', $spec_ids);
		$m = $m_product_spec->where($where)->delete();
		if ($m) {
			alert('success', '删除成功', $_SERVER['HTTP_REFERER']);
		} else {
			alert('error', '删除失败', $_SERVER['HTTP_REFERER']);
		}
	}


	/**
	* 在做商品属性时设计到多个属性直接参数的组合，因此用到了笛卡尔积这个概念。主要函数原理是利用递归的原理和求两个数组的笛卡尔积。
	* 递归求笛卡尔积函数
	**/
	public function combineDika($dikad,$dalen){
		$data = $dikad;
		$cnt = $dalen;
		 
		$result = array();
		foreach($data[0] as $item) {
			$result[] = array($item);
		}

		//这里的for循环，递归$result这个数组，最后获取填入完整的数组的笛卡尔积
		for($i = 1; $i < $cnt; $i++) {
		 	$result = $this->combineArray($result,$data[$i]);
		}
		return $result;
	}
	//求两个数组的笛卡尔积
	function combineArray($arr1,$arr2){
	    $result = array();
	    foreach ($arr1 as $item1) {
	        foreach ($arr2 as $item2) {
	            $temp = $item1;
	            $temp[] = $item2;
	            $result[] = $temp;
	        }
	  	}
	    return $result;
	}


	//动态加载不同规格的tabel格式
	public function spec_val(){
		$list = $_REQUEST['list'];
		$this->assign('list', $list);
//p($list,false);
		//递归格式化数组的笛卡尔积
		$list_format = $this->combineDika(array_values($list), count($list));
		$this->assign('list_format', $list_format);
//p($list_format);
		$this->display();
	}


	//动态加载不同规格属性
	public function spec_name(){
		$m_product_spec = M('ProductSpec');
		$category_id = $this->_request('category_id','intval',0);

		if ($category_id > 0) {
			$spec_list = $m_product_spec->where('category_id = %d', $category_id)->select();
		} else {
			//产品类别
			$category_list = D('Product')->getCategoryList();
			//产品规格分类
			$spec_list = $m_product_spec->where('category_id = %d', $category_list[0]['category_id'])->select();
		}
		foreach ($spec_list as $key => $val) {
			$spec_list[$key]['val_list'] = explode(',', $val['spec_val']);
		}
		$this->assign('spec_list', $spec_list);

		$this->display();
	}


	/**
	* 产品信息详情下增加产品多规格
	* 共用产品添加(product/add)的权限
	**/
	public function product_spec_add(){
		$d_product_info = D('ProductInfo');
		$product_id = $this->_request('product_id','intval');
		
		//权限判断
		if (!checkPerByAction('product', 'add')) {
			dialogAlert('您没有此权利');
		}

		if (IS_POST) {
			//检查是否已有重复规格
			$ret = $d_product_info->checkSpec($product_id);
			if ($ret['is_repeat'] == 1) {
				$data['status'] = 0;
				$data['repeat_row'] = $ret['repeat_row'];
				$this->ajaxReturn($data);
			}

			//保存多规格产品
			$result = $d_product_info->addProductInfo($product_id, 1);
			if ($result) {

				//生成库存信息
				D('Stock')->addStock();

				$data['status'] = 1;
				$data['info'] = $d_product_info->msg;
				$this->ajaxReturn($data);
			} else {
				$data['status'] = 2;
				$data['info'] = $d_product_info->msg;
				$this->ajaxReturn($data);
			}
		} else {
			$this->display();
		}
	}


	/**
	 * 上下架产品规格
	 * 共用产品编辑(product/edit)的权限
	 */
	public function upper_or_lower(){
		//权限判断
		if (!checkPerByAction('product', 'edit')) {
			dialogAlert('您没有此权利');
		}

		$d_product_info = D('ProductInfo');
		if (IS_POST) {
			$product_info_id = $this->_post('product_info_id','intval');
			$state = $this->_post('state','intval');
			
			$ret = $d_product_info->updateProductInfo($product_info_id, array('state'=>$state));
			if ($ret) {
				$data['status'] = 1;
				$data['info'] = $d_product_info->msg;
				$this->ajaxReturn($data);
			} else {
				$data['info'] = $d_product_info->msg;
				$this->ajaxReturn($data);
			}
		} else {
			$data['info'] = '请求出错';
			$this->ajaxReturn($data);
		}
	}


	/**
	* 编辑产品规格
	* 共用产品编辑(product/edit)的权限
	**/
	public function edit(){
		//权限判断
		if (!checkPerByAction('product', 'edit')) {
			dialogAlert('您没有此权利');
		}

		$d_product_info = D('ProductInfo');
		$product_info_id = $this->_request('product_info_id','intval');
		$product_info = $d_product_info->getProductInfo($product_info_id);
		if (IS_POST) {
			$ret = $d_product_info->updateProductInfo($product_info_id, $_POST);
			if ($ret) {
				alert('success', $d_product_info->msg, U('product/view','id='.$product_info['product_id'].'#tab4'));
			} else {
				alert('error', $d_product_info->msg, U('product/view','id='.$product_info['product_id'].'#tab4'));
			}
		} else {
			$this->assign('product_info', $product_info);
			$this->display();
		}
	}



}