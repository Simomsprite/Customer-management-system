<?php 
    class StockModel extends Model{
        
    	public $msg = '';

    	/**
    	* 添加库存信息
    	* 库存 = 产品 * 仓库，即唯一产品和唯一仓库生成一条库存信息
    	**/
    	function addStock(){
    		$product_info_ids = D('ProductInfo')->getField('product_info_id', true);
    		$warehouse_ids = D('Warehouse')->where('is_deleted = 0')->getField('warehouse_id', true);
    		foreach ($product_info_ids as $product_info_id) {
    			foreach ($warehouse_ids as $warehouse_id) {
    				$list[] = array('product_info_id'=>$product_info_id, 'warehouse_id'=>$warehouse_id);
    			}
    		}
    		foreach ($list as $k => $v) {
    			$stock = array();
    			$stock = $this->where($v)->find();
    			if (!$stock) {
    				$v['create_time'] = time();
    				$this->add($v);
    			}
    		}
    		return;
    	}


    	/**
    	 * 库存视图列表
         * @param state 0正常 1下架 2删除 3不可用 
         * @param transfer_count 调拨中的库存数量
    	**/
    	function getStockViewList($where = array(), $page_index='', $page_size='', $order=''){
            if ($page_index !== '') {
                $page = $page_size === null ? $page_index : $page_index.','.$page_size;
                $list = D('StockView')->where($where)->page($page)->order($order)->select();
            } else {
                $where['count'] = array('gt', 0);
                $list = D('StockView')->where($where)->order($order)->select();
            }
    		foreach ($list as $k => $v) {
                $list[$k]['spec_list'] = D('ProductInfo')->getNameSpec($v['product_info_id']);
                $transfer_ids = array();
                $transfer_ids = M('Transfer')->where('out_warehouse_id = %d and status = 0', $v['warehouse_id'])->getField('transfer_id',true);
                if (intval($_GET['transfer_id']) > 0) {
                    //如果是调拨单编辑，则不能把该单子的产品调拨数量算在内
                    $transfer_ids = array_diff($transfer_ids, array(intval($_GET['transfer_id'])));
                }
                $list[$k]['transfer_count'] = M('TransferProduct')->where(array('transfer_id' => array('in', $transfer_ids), 'product_info_id' => $v['product_info_id']))->sum('count') ?: 0;

                // 超出上、下限提醒
                if ($v['count'] <= $v['lower_limit'] || $v['count'] >= $v['upper_limit']) {
                    $list[$k]['over_range'] = true;
                }
    		}
    		return $list;
    	}


        /**
        * 入库操作，添加入库基本信息记录
        * data数据，默认取post过来的表单数据
        **/
        function addStockInRecord($data = array()){
            $data = $data ? $data : $_POST;
            $d_stock_in_recod = D('StockInRecord');
            if ($d_stock_in_recod->create($data)) {
                $pk_id = $d_stock_in_recod->add();
                if ($pk_id) {
                    $this->msg = '保存成功';
                    return $pk_id;
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
        * 添加入库详细信息
        **/
        function addStockInDetail($data = array()){
            $data = $data ? $data : $_POST;
            $d_stock_in_detail = D('StockInDetail');
            if ($d_stock_in_detail->create($data)) {
                $pk_id = $d_stock_in_detail->add();
                if ($pk_id) {
                    $this->msg = '保存成功';
                    return $pk_id;
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
         * 入库更新库存（多条）
         * @param array $data 二维数组
         * @author Shenyue
         */
        public function updateStocks($data)
        {
            foreach ($data as $key => $val) {
                $this->updateStock($val);
            }
        }

        /**
         * 入库更新库存（单条）
         * @param array $data 
         * @author Shenyue
         * 注意： 所有计数采用加法，如果是出库减少库存 count 应为负数
         */
        public function updateStock($data)
        {
            $where = array(
                'product_info_id' => $data['product_info_id'],
                'warehouse_id' => $data['warehouse_id']
            );
            $new_data = array();
            // // 入库在途量
            // if (isset($data['purchase_count']) && $data['purchase_count'] != 0) {
            //     $new_data['purchase_count'] = $data['purchase_count'] + (int) $this->where($where)->getField('purchase_count');
            // }
            // // 出库在途 （订单）
            // if (isset($data['order_count']) && $data['order_count'] != 0) {
            //     $new_data['order_count'] = $data['order_count'] + (int) $this->where($where)->getField('order_count');
            // }
            // 库存量
            if (isset($data['count']) && $data['count'] != 0) {
                $new_data['count'] = $data['count'] + (int) $this->where($where)->getField('count');
            }
            $new_data['last_change_time'] = time();
            $this->create($new_data);
            $this->where($where)->save();
        }


        /**
         * 获取单个产品的库存量
         * @param int $product_info_id  产品ID
         * @param int $warehouse_id     仓库ID
         * @author  Shenyue
         */
        public function getProductStock($product_info_id, $warehouse_id = 0)
        {
            $where = array('product_info_id' => $product_info_id);
            if ($warehouse_id) {
                $where['warehouse_id'] = $warehouse_id;
            }
            $count = $this->where($where)->field('sum(count) as count')->find();
            return (int) $count['count'];
        }

        /**
         * 更新库存信息
         * @param int 
         * @param int 
         * @author  
         */
        function saveStock($where, $data){
            if ($this->create($data)) {
                $sql_res = $this->where($where)->save();
                if ($sql_res !== false) {
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
         * 添加库存调拨记录
         * @param int 
         * @param int 
         * @author  
         */
        function addTransfer($data){
            $m_transfer = M('Transfer');
            if ($m_transfer->create($data)) {
                $m_transfer->transfer_out_date = strtotime($data['transfer_out_date']);
                $m_transfer->create_time = time();
                $m_transfer->update_time = time();
                $m_transfer->creator_role_id = session('role_id');
                $transfer_id = $m_transfer->add();
                if ($transfer_id) {
                    //保存调拨相关产品
                    foreach ($data['product_info_list'] as $k => $v) {
                        if ($v['count'] > 0) {
                            $v['transfer_id'] = $transfer_id;
                            $v['product_info_id'] = $k;
                            $this->addTransferProduct($v);
                        }
                    }
                    $this->msg = '保存成功';
                    return $transfer_id;
                } else {
                    $this->msg = '调拨信息保存失败';
                    return false;
                }
            } else {
                $this->msg = '数据对象创建失败';
                return false;
            }
        }

        /**
         * 添加库存调拨关联产品记录
         * @param int 
         * @param int 
         * @author  
         */
        function addTransferProduct($data){
            return M('TransferProduct')->add($data);
        }
        /**
         * 编辑库存调拨关联产品记录
         * @param int 
         * @param int 
         * @author  
         */
        function updateTransferProduct($where, $data){
            return M('TransferProduct')->where($where)->save($data);
        }
        /**
         * 编辑库存调拨关联产品记录
         * @param int 
         * @param int 
         * @author  
         */
        function deleteTransferProduct($where){
            return M('TransferProduct')->where($where)->delete();
        }
        /**
         * 编辑库存调拨关联产品记录
         * @param int 
         * @param int 
         * @author  
         */
        function checkTransferProduct($where){
            if (M('TransferProduct')->where($where)->find()) {
                return true;  
            } else {
                return false;
            }
        }

        /**
         * 库存调拨列表
         * @param int 
         * @param int 
         * @author  
         */
        function getTransferList($where = array(), $p = '', $listrows = '', $order = ''){
            $d_exam = D('Exam');
            $list =  M('Transfer')->where($where)->page($p.','.$listrows)->order($order)->select();
            foreach ($list as $k => $v) {
                $out_warehouse = D('Warehouse')->getWarehouse($v['out_warehouse_id']);
                $list[$k]['out_warehouse_name'] = $out_warehouse['name'];

                $in_warehouse = D('Warehouse')->getWarehouse($v['in_warehouse_id']);
                $list[$k]['in_warehouse_name'] = $in_warehouse['name'];

                $list[$k]['owner_role_name'] = D('User')->get_full_name(intval($v['owner_role_id']));

                //审批信息
                $list[$k] += $d_exam->checkExamPermission(4, $v['transfer_id']);
            }
            return $list;
        }

         /**
         * 库存调拨信息
         * @param int 
         * @param int 
         * @author  
         */
        function getTransferDetail($where){
            $transfer = M('Transfer')->where($where)->find();
            $out_warehouse = D('Warehouse')->getWarehouse($transfer['out_warehouse_id']);
            $transfer['out_warehouse_name'] = $out_warehouse['name'];

            $in_warehouse = D('Warehouse')->getWarehouse($transfer['in_warehouse_id']);
            $transfer['in_warehouse_name'] = $in_warehouse['name'];

            $transfer['owner_role_name'] = D('User')->get_full_name(intval($transfer['owner_role_id']));
            $transfer['creator_role_name'] = D('User')->get_full_name(intval($transfer['creator_role_id']));

            //审批权限信息
            $transfer += D('Exam')->checkExamPermission(4, $transfer['transfer_id']);

            return $transfer;
        }

        /**
         * 库存调拨相关产品信息
         * @param int 
         * @param int 
         * @author  
        */
        function getTransferProcut($transfer_id){
            $product_list = M('TransferProduct')->where('transfer_id = %d', $transfer_id)->select();
            foreach ($product_list as $k => $v) {
                $product_list[$k] = array_merge($product_list[$k], D('ProductInfo')->getNameSpec($v['product_info_id']));
            }
            return $product_list;
        }

        /**
         * 更新库存调拨
         * @param int 
         * @param int 
         * @author  
         */
        function updateTransfer($where, $data){
            $m_transfer = M('Transfer');

            if ($m_transfer->create($data)) {
                $m_transfer->update_time = time();
                $sql_res = $m_transfer->where($where)->save();
                if ($sql_res !== false) {
                    //保存调拨相关产品
                    foreach ($data['product_info_list'] as $k => $v) {
                        $where_product = array();
                        $where_product = array('transfer_id' => $data['transfer_id'], 'product_info_id' => $k); //关联产品查询条件，单个调拨单关联的产品具有唯一性
                        if ($v['count'] > 0) {
                            $v['transfer_id'] = $data['transfer_id'];
                            $v['product_info_id'] = $k;
     
                            //检查某个调拨单是否关联有该产品，是：更新，否：新增
                            if ($this->checkTransferProduct($where_product)) {
                                $this->updateTransferProduct($where_product, $v);
                            } else {
                                $this->addTransferProduct($v);
                            }
                        } else {
                            //检查某个调拨单是否关联有该产品，是：删除，否：不做处理
                            if ($this->checkTransferProduct($where_product)) {
                                $this->deleteTransferProduct($where_product);
                            }
                        }
                    }
                    $this->msg = '保存成功';
                    return true;
                } else {
                    $this->msg = '调拨信息保存失败';
                    return false;
                }
            } else {
                $this->msg = '数据对象创建失败';
                return false;
            }
        }


        /**
         * 获取出库或入库基本信息
         * @param int type out或in
         * @param int 
         * @author  
         */
        function getStockOutOrIn($where, $type){
            $table_name = 'stock_'.$type;
            $data = M($table_name)->where($where)->find();
            $data['warehouse_name'] = M('Warehouse')->where('warehouse_id = %d', $data['warehouse_id'])->getField('name');
            $data['owner_role_name'] = D('User')->get_full_name(intval($data['owner_role_id']));
            $data['create_time'] = $data['create_time'] > 0 ? date('Y-m-d', $data['create_time']) : '';
            $data['update_time'] = $data['update_time'] > 0 ? date('Y-m-d', $data['update_time']) : '';
            return $data;
        }


        /**
         * 库存调拨搜索字段list信息
         * @author lee
         */
        public function transfer_search_field()
        {
            $search_field_list = array(
                array('field' => 'number', 'name' => '调拨单号', 'form_type' => 'text'),
                array('field' => 'transfer_out_date', 'name' => '调出时间', 'form_type' => 'date'),
                array('field' => 'transfer_in_date', 'name' => '调入时间', 'form_type' => 'date'),
                array('field' => 'owner_role_id', 'name' => '负责人', 'form_type' => 'role'),
                array('field' => 'status', 'name' => '状态', 'form_type' => 'transfer_status'),
                array('field' => 'exam_status', 'name' => '审核状态', 'form_type' => 'exam_status'),
            );
            return $search_field_list;
        }


        /**
         * 删除库存调拨单及关联产品
         * @author lee
         */
        public function deleteTransfer($transfer_id)
        {
            if ($transfer_id) {
                M('Transfer')->where('transfer_id = %d', $transfer_id)->delete();
                M('TransferProduct')->where('transfer_id = %d', $transfer_id)->delete();
            }
        }


    }