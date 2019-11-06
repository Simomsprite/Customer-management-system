<?php 
    class ProductInfoModel extends Model{

    	public $msg = '';

    	/**
        * 添加多规格产品
        * 默认始终生成一条无规格属性的产品数据 $product_default ，当启用多规格时该条数据state_default = 3标记为不可用
        * enable_spec 1启用多规格 0未启用多规格
        * @param price_cost_avg 成本价
        * @param price_cost 采购价
        * @param price 建议售价
        **/
        function addProductInfo($product_id, $enable_spec){
			if($enable_spec == 1){
				$state_default = 3;
			} else {
				$state_default = 0;
                unset($_POST['product_list']);
			}
            if ($this->where('product_id = %d', $product_id)->count() == 0) {
                //第一次添加产品的时候，需要默认生成一条非多规格数据
                $product_default = array(
                    array(
                        'spec_val_list'  => array(),
                        'number'         => trim($_POST['number']),
                        'bar_code'       => trim($_POST['bar_code']),
                        'price_cost_avg' => $_POST['cost_price'] ?: 0,
                        'price_cost'     => $_POST['price_cost'] ?: 0,
                        'price'          => $_POST['suggested_price'] ?: 0,
                        'state'          => $state_default,
                    )
                );
            }
			$product_list = array_merge($product_default ?: array(), $_POST['product_list'] ?: array());
			foreach ($product_list as $product_info) {
				if ($this->create($product_info)) {
					$this->product_id = $product_id;

					$product_info_id = '';
					$product_info_id = $this->add();
					if ($product_info_id) {
						$this->addProductSpecValue($product_info_id, $product_info['spec_val_list']);
					} else {
						$this->msg = '产品数据添加失败';
						return false;
					}
				} else {
					$this->msg = '产品数据对象创建失败';
					return false;
				}
			}
            $this->msg = '产品数据添加成功';
            return true;
        }

        /**
        * 添加产品的规格值
        **/
        function addProductSpecValue($product_info_id, $spec_val_list = array()){
			if ($spec_val_list){
				$spec_name_list = $_POST['spec_name_list'];
				foreach ($spec_name_list as $k => $spec_name) {
					$data = array();
					$data['product_info_id'] = $product_info_id;
					$data['spec_name'] = $spec_name;
					$data['spec_value'] = $spec_val_list[$k];
					D('ProductSpecValue')->add($data);
				}
			}
			$this->msg = '产品规格值保存成功';
			return;
        }

        /**
		* 产品列表
		* @param $range 默认过滤不可用产品
        **/
        function getProductInfoList($product_id, $range = 'default'){
			$where['product_id'] = $product_id;
			$where['state'] = array('lt', 3); // 3 不可用
			if($range == 'all'){
				unset($where['state']);
			}
        	$list = $this->where($where)->order('state asc')->select();
        	foreach ($list as $k => $v) {
        		$list[$k]['spec_list'] = $this->getProductSpecList($v['product_info_id']);
        	}
        	return $list;
        }

        /**
        * 获取产品关联规格列表
        **/
        function getProductSpecList($product_info_id){
        	$list = M('ProductSpecValue')->where('product_info_id = %d', $product_info_id)->order('value_id asc')->select();
        	return $list;
        }

        /**
        * 检查是否已有规格
        **/
        function checkSpec($product_id){
        	$product_spec_list = $this->getProductSpecStr($product_id);
        	$post_spec_list = $this->getPostSpecStr();
        	$repeat_row = array();
        	foreach ($post_spec_list as $k => $v) {
        		foreach ($product_spec_list as $val) {
        			if ($val == $v) {
        				//记录那些行有重复
        				$repeat_row[] = $k + 1;
        			}
        		}
        	}
        	if (!empty($repeat_row)) {
        		$data['is_repeat'] = 1;
        		$data['repeat_row'] = $repeat_row;
        		return $data;
        	} else {
        		$data['is_repeat'] = 0;
        		return $data;
        	}
        }

        /**
        * 获取产品规格综合信息
        **/
        function getProductSpecStr($product_id){
        	$product_info_list = $this->getProductInfoList($product_id);
        	foreach ($product_info_list as $k => $v) {
        		$temp = '';
        		foreach ($v['spec_list'] as $ks => $vs) {
        			$temp .= $vs['spec_name'].$vs['spec_value'];
        		}
        		$list[] = $temp;
        	}
//p($list);
        	return $list;
        }

        /**
        * 获取本次提交的产品多规格信息
        **/
        function getPostSpecStr(){
        	$spec_name_list = $_POST['spec_name_list'];
        	$product_list = $_POST['product_list'];
        	foreach ($product_list as $k => $v) {
        		$temp = '';
        		foreach ($spec_name_list as $ks => $vs) {
        			$temp .= $vs.$v['spec_val_list'][$ks];
        		}
        		$list[] = $temp;
        	}
        	return $list;
        }

        /**
        * 修改产品
        **/
        function updateProductInfo($product_info_id, $data = array()){
        	if ($this->create($data)) {
        		$sql_return = $this->where('product_info_id = %d', $product_info_id)->save($data);
        		if ($sql_return !== false) {
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
        * 获取单个产品视图信息
        **/
        function getProductInfo($product_info_id){
        	$detail = D('ProductInfoView')->where('product_info.product_info_id = %d', $product_info_id)->find();
        	$detail['spec_list'] = $this->getProductSpecList($detail['product_info_id']);
        	return $detail;
        }

        /**
        * 产品视图列表
        **/
        function getProductInfoViewList($condition = array(), $page_index='', $page_size='', $order=''){
        	$list = D('ProductInfoView')->where($condition)->page($page_index.','.$page_size)->order($order)->select();
        	return $list;
        }	

		
		/**
		 * 获取产品名和属性，用于商机和合同模块的列表展示
		 */
		public function getNameSpec($product_info_id)
		{
			$product = $this->where(array('product_info_id' => $product_info_id))->field('product_id,number,price')->find();
			$data = M('Product')->where(array('product_id' => $product['product_id']))->field('name as product_name,has_sn,standard as unit,product_id')->find();
			$data['number'] = $product['number'];
			$data['price'] = $product['price'];
			$data['spec'] = D('Product')->getProductInfoSpec($product_info_id, 'string');
			return $data;
		}
		
        /**
        * 启用或禁用产品多规格，处理pd_product_info
        **/
        function multiSpec($product_id, $enable_spec){
			$list = $this->getProductInfoList($product_id,'all');
            foreach ($list as $k => $v) {
                $spec_list = array();
                $spec_list = $this->getProductSpecList($v['product_info_id']);
                if ($enable_spec == 1) {
                    if ($spec_list) {
                        //启用，有多规格值，上架
                        $this->updateProductInfo($v['product_info_id'], array('state'=>0));
                    } else {
                        //启用，无多规格值，不可用
                        $this->updateProductInfo($v['product_info_id'], array('state'=>3));
                    }
                } else {
                    if ($spec_list) {
                        //禁用，有多规格值，不可用
                        $this->updateProductInfo($v['product_info_id'], array('state'=>3));
                    } else {
                        //禁用，无多规格值，上架
                        $this->updateProductInfo($v['product_info_id'], array('state'=>0));
                    }
                }
            }
            return true;
        }


    }