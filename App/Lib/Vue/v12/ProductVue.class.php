<?php
/**
 *产品相关
 **/
class ProductVue extends Action {
	/**
	 *用于判断权限
	 *@permission 无限制
	 *@allow 登录用户可访问
	 *@other 其他根据系统设置
	 **/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('category')
		);
		B('VueAuthenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
		
		Global $role;
		$this->role = $role;
		Global $roles;
		$this->roles = $roles;

		if($roles == 2){
			$this->ajaxReturn('','您没有此权限！',-2);
		}

		if($roles == 3){
			$this->ajaxReturn('','请先登录！',-1);
		}
	}
	
	/**
	 * 产品列表
	 * @param 
	 * @author 
	 * @return 
	 */
	public function index() {
		if ($this->isPost()) {
			// getDateTime('product');		
			$d_v_product = D('ProductView');
			$m_product_category = M('ProductCategory');
			
			// 搜索产品名称
			if (isset($_POST['search'])) {
				$mpa['name'] = array('like', '%'. $_POST['search'] .'%');
			}
			$map['is_deleted'] = 0; // 默认查询主产品未下架的
			$product_id_list = M('Product')->where($map)->getField('product_id', true);
			$where['product_id'] = array('IN', $product_id_list);

			$p = isset($_POST['p']) ? intval($_POST['p']) : 1 ;
			if ($_POST['category_id']) {
				$idArray = Array();
				$categoryList = getSubCategory($_POST['category_id'],$m_product_category->select(),'');
				foreach ($categoryList as $value) {
					$idArray[] = $value['category_id'];
				}
				$idList = empty($idArray) ? $_POST['category_id'] : $_POST['category_id'].','.implode(',', $idArray);
				$where['category_id'] = array('in',$idList);
			}
			//商机下的产品
			if ($_POST['business_id']) {
				$product_info_ids =  M('RBusinessProduct')->where('business_id = %d',intval($_POST['business_id']))->getField('product_info_id', true);
				$where['product_info_id'] = array('in',$product_info_ids);
			}
			if ($_POST['contract_id']) {		
				$sales_id = M('rContractSales')->where(array('contract_id'=>intval($_POST['contract_id']),'sales_type'=>0))->getField('sales_id');
				$product_info_ids = M('salesProduct')->where('sales_id = %d',$sales_id)->getField('product_info_id',true);
				$where['product_info_id'] = array('in',$product_info_ids);
			}
			// $list = $d_v_product->where($where)->order('create_time desc')->page($p.',10')->field('name,product_id,standard,suggested_price,create_time')->select();
			$where['state'] = array('lt', 3); // 3 不可用
			$product_info_id_list = M('ProductInfo')->where($where)->order('product_info_id desc')->page($p.',10')->field('product_info_id,price')->select();
			$list = array();
			$d_product_info = D('ProductInfo');
			$m_product_images = M('product_images');
			$permission = getpermission(MODULE_NAME);
			foreach ($product_info_id_list as $key => $val) {
				$temp_var = $d_product_info->getNameSpec($val['product_info_id']);
				$temp_var['product_info_id'] = $val['product_info_id'];
				$temp_var['price'] = $val['price'];

				$product_images_info = $m_product_images->where(array('product_id'=>$temp_var['product_id'],'is_main'=>1))->getField('thumb_path');
				$temp_var['main_path'] = headPathHandle($product_images_info, 1);
				// 获取操作权限
				$temp_var['permission'] = $permission;
				$list[] = $temp_var;
			}
			$list = empty($list) ? array() : $list;
			$count = $d_v_product->where($where)->count();
			$page = ceil($count/10);
			if($p == 1 && $_POST['search'] == ''){
				$category_arr = $m_product_category->field('category_id,parent_id,name')->select();
				foreach ($category_arr as $k=>$v) {
					$category_arr[$k]['id'] = $v['category_id'];
					unset($category_arr[$k]['category_id']);
				}
				$category_list = build_tree($category_arr,0);
				$category_list = empty($category_list) ? array() : $category_list ;			
				$data['category_list'] = $category_list;
			}
			$data['list'] = $list;
			$data['page'] = $page;
			$data['info'] = 'success'; 
			$data['status'] = 1; 			
			$this->ajaxReturn($data,'JSON');
		} else {
			$this->ajaxReturn('','非法请求！',0);
		}
	}

	/**
	 * 产品详情
	 * @param 
	 * @author 
	 * @return
	 */
	public function view() {
		if ($this->isPost()) {
			$product_info_id =  isset($_POST['id']) ? intval($_POST['id']) : 0;
			if (empty($product_info_id)) {
				$this->ajaxReturn('','参数错误！',0);
			}
			$info = M('ProductInfo')->where(array('product_info_id' => $product_info_id))->field('product_id, price')->find();
			$product_info = D('ProductView')->where('product.product_id = %d', $info['product_id'])->find();
			$product_info['suggested_price'] = $info['price'];
			
			//取得字段列表
			$field_list = M('Fields')->where('model = "product"')->order('order_id')->select();
			//查询固定信息
			$product_info['create'] = D('RoleView')->where('role.role_id = %d', $product_info['creator_role_id'])->find();
			$i = 0;
			foreach ($field_list as $k=>$v) {
				$data_list[$i]['field'] = trim($v['field']);
				$data_list[$i]['name'] = trim($v['name']);
				if ($v['setting']) { 
					//将内容为数组的字符串格式转换为数组格式
					eval("\$setting = ".$v['setting'].'; ');
					$data_list[$i]['form_type'] = $setting['type'] == 'checkbox' ? 'checkbox' : 'select';
				} else {
					$data_list[$i]['form_type'] = $v['form_type'];
				}
				$data_a = trim($product_info[$v['field']]);
				if ($v['form_type'] == 'address') {
					$address_array = str_replace(chr(10),' ',$data_a);
					$data_list[$i]['val'] = $address_array;
					$data_list[$i]['type'] = 0;
				} else {
					$data_list[$i]['val'] = $data_a;
					$data_list[$i]['type'] = 0;
				}
				$data_list[$i]['id'] = '';
				$i++;
			}
			//创建人信息
			$creator_role_id = $product_info['creator_role_id'];
			$creator_name = M('User')->where('role_id = %d',$creator_role_id)->getField('name');
			$data_list[$i]['field'] = 'creator_role_id';
			$data_list[$i]['name'] = '创建人';
			$data_list[$i]['val'] = $creator_name;
			$data_list[$i]['id'] = $creator_role_id;
			$data_list[$i]['type'] = 1;
			$data_list[$i]['form_type'] = 'user';

			//获取产品主图
			$path = M('ProductImages')->where(array('product_id'=>$product_id,'is_main'=>1))->getField('path');
			$data['main_path'] = $path ? $path : '';
			if ($product_info['enable_spec']) {
				$spec = D('Product')->getProductInfoSpec($product_info_id, 'string');
				$data_list[$i + 1]['field'] = 'spec';
				$data_list[$i + 1]['name'] = '规格';
				$data_list[$i + 1]['val'] = $spec;
				$data_list[$i + 1]['type'] = 1;
				$data_list[$i + 1]['form_type'] = 'text';
			}
			$data['data'] = $data_list;
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		} else {
			$this->ajaxReturn('','非法请求！',0);
		}
	}
}