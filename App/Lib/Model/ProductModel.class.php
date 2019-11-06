<?php 
    class ProductModel extends Model{
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
            $fields = M('fields')->where('(model = \'\' or model = \'product\') and is_validate=1 and is_main=1')->select();
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

		public $msg = '';

        /**
        * 获取产品类别
        **/
        function getCategoryList(){
        	$category = M('product_category');
            $category_list = $category->select();
            $categoryList = getSubCategory(0, $category_list, '');
			return $categoryList;
        }

        /**
        * 处理产品基本信息自定义字段
        **/
        function adjustProductFields(){
        	$field_list = M('Fields')->where('model = "product" and in_add = 1')->order('order_id')->select();
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
			return;
        }

        /**
        * 添加产品基本信息主表数据 
        **/
        function addMainProduct(){
        	$this->adjustProductFields();
        	if($this->create()){
				$this->create_time = time();
				$this->update_time = time();
				$this->creator_role_id = session('role_id');
				if ($pk_id = $this->add()) {
					$this->msg = '保存成功';
					return $pk_id;
				} else {
					$this->msg = '主表数据保存失败';
					return false;
				}
			} else {
				$this->msg = $this->getError();
				return false;
			}
        }
        /**
        * 添加产品基本信息副表数据 
        **/
        function addAssistantProduct($product_id){
        	$m_product_data = D('ProductData');
	    	if($m_product_data->create() !== false){
	    		$m_product_data->product_id = $product_id;
				$m_product_data->add();
				$this->msg = '保存成功';
				return true;
	    	} else {
	    		$this->msg = '副表数据对象创建失败';
	    		return false;
	    	}
        }


        /**
        * 上传产品图片
        **/
        function uploadImg($product_id)
        {
        	$m_product_images = M('productImages');

        	// 是否可上传阿里云OSS
			$d_file = D('File');
			$dir = $d_file->upload_oss();
			if ($dir) {
	        	$file_list = $d_file->getFileList($_FILES['main_pic']);
	        	foreach ($file_list as $k => $v) {
	        		//设置文件名称
		        	$file_name = date('YmdHis').rand(1000, 9999).'.'.getExtension($v['name']);
		        	//阿里云OSS的文件名称
		            $object = $dir.$file_name;
		            $res = $d_file->ossUpload($object, $v['tmp_name']);
		            if ($res) {
		            	$img_data = array();
		            	$img_data['product_id'] = $product_id;
						$img_data['name'] = $v['name'];
						$img_data['save_name'] = $file_name;
						$img_data['size'] = sprintf("%.2f", $v['size']/1024);
						$img_data['path'] = $object;
						$img_data['thumb_path'] = $object.'?x-oss-process=image/resize,m_fixed,h_100,w_100'; //缩略图

						$old_image = array();
						$old_image = $m_product_images->where('images_id = %d and is_main = 1', intval($_POST['main_images_id']))->find();
						if($old_image){
							//删除原图
	            			$d_file->ossDelete(array($old_image['path']));

	            			//存在则修改
							$m_product_images->where('images_id = %d',intval($_POST['main_images_id']))->save($img_data);
						}else{
							//不存在则添加
							$img_data['is_main'] = 1;
							$img_data['oss'] = 1;
							$img_data['create_time'] = time();
							$img_data['listorder'] = intval($m_product_images->max('listorder'))+1;
							$m_product_images->add($img_data);
						}
		            }
	        	}
	        	$this->msg = '图片信息保存成功';
				return true;
	        } else {
	        	//上传产品主图和副图至服务器
				if (array_sum($_FILES['main_pic']['size'])) {
					//如果有文件上传 上传附件
					import('@.ORG.UploadFile');
					import('@.ORG.Image');//引入缩略图类
					$Img = new Image();//实例化缩略图类
					//导入上传类
					$upload = new UploadFile();
					//设置上传文件大小
					$upload->maxSize = 20000000;
					//设置附件上传目录
					$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
					$upload->allowExts  = array('jpg','jpeg','png','gif');// 设置附件上传类型
					$upload->thumb = true;//生成缩图
					$upload->thumbRemoveOrigin = false;//是否删除原图
					if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
						$this->error(L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'));
					}
					$upload->savePath = $dirname;
					if(!$upload->upload()) {// 上传错误提示错误信息
						alert('error', $upload->getErrorMsg(), $_SERVER['HTTP_REFERER']);
					}else{// 上传成功 获取上传文件信息
						$info =  $upload->getUploadFileInfo();
						if(is_array($info[0]) && !empty($info[0])){
							$upload = $dirname . $info[0]['savename'];
						}else{
							$this->error('图片上传失败，请重试！');
						}
						$thumb_path = $Img->thumb($upload,$dirname.'thumb_'.$info[0]['savename']);
						
						//写入数据库
						foreach($info as $iv){
							$img_data['product_id'] = $product_id;
							$img_data['name'] = $iv['name'];
							$img_data['save_name'] = $iv['savename'];
							$img_data['size'] = sprintf("%.2f", $iv['size']/1024);
							$img_data['path'] = $iv['savepath'].$iv['savename'];
							$img_data['thumb_path'] = $thumb_path; //缩略图
							if($iv['key'] == 'main_pic'){
								//如果是主图，则修改
								$old_image = array();
								$old_image = $m_product_images->where('images_id = %d and is_main = 1', intval($_POST['main_images_id']))->find();
								if($old_image){
									//存在则修改
									@unlink($old_image['path']);
									@unlink($old_image['thumb_path']);
									$m_product_images->where('images_id = %d',intval($_POST['main_images_id']))->save($img_data);
								}else{
									//不存在则添加
									$img_data['is_main'] = 1;
									$img_data['create_time'] = time();
									$img_data['listorder'] = intval($m_product_images->max('listorder'))+1;
									$m_product_images->add($img_data);
								}
							}else{
								//如果是副图，则添加
								$img_data['is_main'] = 0;

								$img_data['listorder'] = intval($m_product_images->max('listorder'))+1;
								$m_product_images->add($img_data);
							}
						}
					}
				}
				$this->msg = '图片信息保存成功';
				return true;
	        }
        }

        /**
		 * 更新产品主表信息
		 * @param	int		$product_id 
		 * @param	
		 */
        function updateMainProduct($product_id, $data = array()){
        	if ($this->create($data)) {
        		$sql_return = $this->where('product_id = %d', $product_id)->save($data);
        		if ($sql_return !== fales) {
        			$this->msg = '保存成功';
					return true;
        		} else {
        			$this->msg = '保存失败';
					return false;
        		}
        	} else {
        		$this->msg = '数据对象创建失败';
				return false;
        	}
        }


		/**
		 * 获取产品规格
		 * @param	int		$product_info_id 
		 * @param	string	$type		返回值类型  string array
		 */
		public function getProductInfoSpec($product_info_id, $type = null)
		{
			$list = M('product_spec_value')->where(array('product_info_id' => $product_info_id))->field('concat(spec_name,":",spec_value) as spec')->select();
			$spec = array('array' => array(), 'string' => '-');
			if ($list) {
				$spec['array'] = $list;
				$spec['string'] = trim(implode(' ', y_array_column($list, 'spec')));
			}
			if ($type == 'string') {
				return $spec['string'];
			} else if ($type == 'array') {
				return $spec['array'];
			}
			return $spec;
		}


		/**
		 * 采购产品列表
		 * @param 	Array 	$where 		查询条件
		 * @param 	Int 	$page 		当前页码
		 * @param 	Int 	$list_rows 	每页条数
		 * @return 	Array 	$list 		产品列表 ['product_info_id', 'name', 'spec']
		 */
		public function getPurchaseProductInfoList($where = array(), $page = 1, $list_rows = 10)
		{
			$d_product_info_view = D('ProductInfoView');
			$where['is_deleted'] = 0;
			$where['product_info.state'] = 0; // state 0上架 1下架 3禁用
			$list = $d_product_info_view->where($where)->field('product_info_id,name,category_name,product_info.price as suggested_price,standard,category_name,product_info.price_cost as price_cost')->page($page, $list_rows)->select();

			$count = $d_product_info_view->where($where)->count();
			foreach ($list as $key => $val) {
				$list[$key]['spec'] = $this->getProductInfoSpec($val['product_info_id'], 'string');
			}
			return array('list' => $list, 'count' => $count);
		}


		/**
		 * 判断产品是否已有出入库记录【理论上，只判断是否有入库记录即可】
		 * @return true有入库记录 false无入库记录
		 * @author lee
		 */
		public function exist_stock($product_id)
		{
			$product_info_ids = M('ProductInfo')->where('product_id = %d', $product_id)->getField('product_info_id', true);
			$stock_in_data = M('StockInProductinfo')->where(array('product_info_id' => array('in', $product_info_ids)))->find();
			if ($stock_in_data) {
				return true;
			} else {
				return false;
			}
		}


		/**
		 * 获取多规格，用做导入导入多规格判断
		 */
		public function getSpec($param)
		{
			if (isset($param['product_id'])) {
				$category_id = M('Product')->where(array('product_id' => $param['product_id']))->getField('category_id');
				$where = array('category_id' => $category_id);
			} elseif (isset($param['product_info_id'])) {
				$product_id = M('ProductInfo')->where(array('product_info_id' => $param['product_info_id']))->getField('product_id');
				$category_id = M('Product')->where(array('product_id' => $product_id))->getField('category_id');
				$where = array('category_id' => $category_id);
			} elseif (isset($param['category_id'])) {
				$where = array('category_id' => $param['category_id']);
			}
			$list = M('ProductSpec')->where($where)->field('name,spec_val')->select();
			$data = array();
			foreach ($list as $key => $val) {
				$data[$val['name']] = explode(',', $val['spec_val']);
			}
			return $data;
		}


	}
		
