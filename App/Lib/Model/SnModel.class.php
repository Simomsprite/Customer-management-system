<?php

class SnModel extends Model
{
    public $msg;
    public $count; // 条数

    /**
     * SN 添加
     */
    public function addData($data)
    {
        $list = array();
        $time = time();
        $role_id = session('role_id');
        foreach ($data['SN'] as $key => $val) {
            $list[] = array(
                'sn' => $val,
                'product_info_id' => $data['product_info_id'],      // 产品详情ID
                'price_cost' => (float) $data['price_cost'],        // 采购价
                'create_time' => $time,
                'update_time' => $time,
                'creator_role_id' => $role_id
            );
        }
        $res = $this->addAll($list);
        return $res > 0;
    }


    /**
     * 删除
     */
    public function deleteSN($sn_id)
    {
        if ($this->where(array('sn_id' => $sn_id, 'status' => 0))->count()) {
            if ($this->delete($sn_id)) {
                return true;
            } else {
                $this->msg = '删除失败。';
                return false;
            }
        } else {
            $this->msg = '已入库，不可删除！';
            return false;
        }
    }


    /**
     * 入库
     */
    public function stockIn($stock_in_id, $type = 1)
    {
        $time = time();
        $sn_ids = '';
        foreach ($_POST['list'] as $key => $val) {
            if ($val['count'] > 0 && isset($val['sn'])) {
                $res = $this->where(array('sn_id' => array('IN', $val['sn'])))->save(array(
                    'stock_in_id' => $stock_in_id,
                    'warehouse_id' => $_POST['warehouse_id'],
                    // 1表示在库
                    'status' => 1,
                    'update_time' => $time
                ));
                $sn_ids .= ',' . $val['sn'];
                $sn_ids = trim($sn_ids, ',');
                if (!$res) {
                    $this->reset($sn_ids);
                    $this->msg = 'SN码添加失败[' + $this->getError() + ']';
                    return false;
                }
            } 
        }
        $sn_id_list = $sn_ids ? explode(',', $sn_ids) : array();
        $sn_log_data = array();
        foreach ($sn_id_list as $key => $val) {
            $sn_log_data[] = array('sn_id' => $val, 'type' => $type, 'type_id' => $stock_in_id);
        }
        if (!empty($sn_log_data)) {
            if (!$this->snLogAdd($sn_log_data)) {
                $this->reset($sn_ids);
                $this->msg = 'SN码日志添加失败';
                return false;
            }
        }
        return true;
    }

    /**
     * SN码重置
     */
    public function reset($sn_ids)
    {
        $this->where(array('sn_id' => array('IN', $sn_ids)))->save(array(
            'stock_in_id' => 0,
            'warehouse_id' => 0,
            'status' => 0
        ));
    }


    /**
     * 获取sn列表
     * @param   int     $transfer_id        调拨ID
     * @param   string  $transfer_type      调拨类型   如果$transfer_id 必须 $transfer_type 枚举【in， out】
     * @param   int     $sales_id           销售ID / 采退ID
     * @param   int     $purchase_id        采购ID / 消退ID
     * @param   int     $stock_in_id        入库ID
     * @param   int     $stock_out_id       出库ID
     * @param   int     $product_info_id    产品ID
     * @param   int     $warehouse_id       仓库ID
     * @param   int     $status             状态  0入库在途 1在库 2出库 3调拨在途 4其他
     * @param   string  $field              返回字段
     * @return  array   
     * @author  Shenyue
     */
    public function getSNList($params)
    {
        // 销售 / 采退
        if (isset($params['sales_id'])) {
            $stock_out_id_list = M('StockOut')->where(array('type' => array('IN', '1,2'), 'type_id' => $params['sales_id']))->getField('stock_out_id', true);
            $params['stock_out_id'] = $stock_out_id_list ?: array();
        }
        // 采购 / 销退
        if (isset($params['purchase_id'])) {
            $stock_in_id_list = M('StockIn')->where(array('type' => array('IN', '1,2'), 'type_id' => $params['purchase_id']))->getField('stock_in_id', true);
            $params['stock_in_id'] = $stock_in_id_list ?: array();
        }
        // 入库
        if (isset($params['stock_in_id'])) {
            $params['stock_in_id'] = is_int($params['stock_in_id']) ? (string) $params['stock_in_id'] : $params['stock_in_id'];
            $sn_id_list = M('SnLog')->where(array('type' => 1, 'type_id' => array('IN', $params['stock_in_id'])))->getField('sn_id', true);
            $where['sn_id'] = array('IN', $sn_id_list);
        }
        // 出库
        if (isset($params['stock_out_id'])) {
            $params['stock_out_id'] = is_int($params['stock_out_id']) ? (string) $params['stock_out_id'] : $params['stock_out_id'];
            $sn_id_list = M('SnLog')->where(array('type' => 2, 'type_id' => array('IN', $params['stock_out_id'])))->getField('sn_id', true);
            $where['sn_id'] = array('IN', $sn_id_list);
        }

        // 产品id
        if (isset($params['product_info_id'])) {
            $where['product_info_id'] = $params['product_info_id'];
        }
        // 仓库
        if (isset($params['warehouse_id'])) {
            $where['warehouse_id'] = $params['warehouse_id'];
        }
        // 状态
        if (isset($params['status'])) {
            $where['status'] = $params['status'];
        }
        // 字段
        if (!isset($params['field'])) {
            $params['field'] = 'sn_id,sn';
        }
        return $this->where($where)->field($params['field'])->order('sn_id asc')->select();
    }


    /**
     * SN记录
     * @param   $data   日志列表    [['sn_id', 'type', 'type_id']、]
     */
    public function snLogAdd($data)
    {
        return (bool) M('SnLog')->addAll($data);
    }


    /**
     * 添加sn码信息
     */
    function addSn($data = array()){
        $data = $data ? $data : $_POST;
        if ($this->create($data)) {
            $pk_id = $this->add();
            if ($pk_id) {
                $this->msg = '添加成功';
                return true;
            } else {
                $this->msg = '添加失败';
                return false;
            }
        } else {
            $this->msg = '数据对象创建失败';
            return false;
        }
    }

    /**
     * 更新sn表信息
     */
    function updateSn($pk_id,$data = array()){
        if ($this->create($data)) {
            $sql_return = $this->where('sn_id = %d', $pk_id)->save();
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
     * 获取列表
     * @param   array   $where  
     * @param   string  $page
     * @param   string  $order
     * @author Shenyue
     */
    public function getList($where, $page, $order)
    {
        $m_sn_log = M('SnLog');
        $d_product = D('Product');
        $d_product_info_view = D('ProductInfoView');
        $m_warehouse = M('Warehouse');
        $m_supplier = M('Supplier');
        $m_purchase = M('Purchase');
        $m_stock_in = M('StockIn');
        $status_arr = array('入库在途', '在库', '出库', '退货在途', '其他');
        $type_arr = array(1 => '采购入库', '销售出库', '销售退货' , '采购退货');
        $this->count = $this->where($where)->count();
        $list = $this->where($where)->page($page)->order($order)->select();
        foreach ($list as $key => $val) {
            $list[$key]['status'] = $status_arr[$val['status']];
            $list[$key]['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
            $list[$key]['product_info'] = $d_product_info_view->where(array('product_info_id' => $val['product_info_id']))->find();
            $spec = $d_product->getProductInfoSpec($val['product_info_id']);
            $list[$key]['spec'] = $spec['string'];
            $list[$key]['warehouse_name'] = $m_warehouse->where(array('warehouse_id' => $val['warehouse_id']))->getField('name');
            
            // 供应商
            $stock_in_id = $m_sn_log->where(array('sn_id' => $val['sn_id'], 'type' => 1))->getField('type_id');  // 入库单ID
            $purchase_id = $m_stock_in->where(array('stock_in_id' => $stock_in_id))->getField('type_id');
            $list[$key]['supplier_id'] = $m_purchase->where(array('purchase_id' => $purchase_id))->getField('type_id');
            $list[$key]['supplier_name'] = $m_supplier->where(array('supplier_id' => $list[$key]['supplier_id']))->getField('name');
            
            // 最新状态
            $type = $m_sn_log->where(array('sn_id' => $val['sn_id']))->order('id desc')->getField('type');
            $list[$key]['type'] = $type_arr[$type];
        }
        return $list;
    }

    /**
     * 根据SN获取产品信息
     */
    public function getProductInfo($sn_id)
    {
        $m_purchase = M('Purchase');
        $m_stock_in = M('StockIn');
        $m_stock_out = M('StockOut');
        $m_warehouse = M('Warehouse');
        $m_contract = M('Contract');
        $m_sales = M('sales');
        $m_r_contract_sales = M('RContractSales');
        $m_transfer = M('transfer');
        $d_user = D('User');
        $data = $this->where(array('sn_id' => $sn_id))->find();
        $data['product_info'] = D('ProductInfoView')->where(array('product_info_id' => $data['product_info_id']))->find();
        $spec = D('Product')->getProductInfoSpec($data['product_info_id']);
        $data['product_info']['spec'] = $spec['string'];
        $data['sn_log'] = M('SnLog')->where(array('sn_id' => $sn_id))->order('id asc')->select();
        $data['warehouse_name'] = $m_warehouse->where(array('warehouse_id' => $data['warehouse_id']))->getField('name');
        // 1：入库  2：出库
        foreach($data['sn_log'] as $key => $val) {
            if ($val['type'] == 1) {
                $stock = $m_stock_in->where(array('stock_in_id' => $val['type_id']))->field('type, type_id, update_time, owner_role_id')->find();
                // 1采购  2销退  3调拨
                if ($stock['type'] == 1 || $stock['type'] == 2) {
                    $stock['name'] = $m_purchase->where(array('purchase_id' => $stock['type_id']))->getField('number');
                    $stock['url'] = U('purchase/view', array('id' => $stock['type_id']));
                    if ($stock['type'] == 1) {
                        $data['sn_log'][$key]['type_name'] = '采购入库';
                    } else {
                        $data['sn_log'][$key]['type_name'] = '销售退货入库';
                    }
                } else if ($stock['type'] == 3) {
                    $stock['name'] = $m_transfer->where(array('transfer_id' => $stock['type_id']))->getField('number');
                    $stock['url'] = U('stock/transfer_view', array('transfer_id' => $stock['type_id']));
                    $data['sn_log'][$key]['type_name'] = '调拨入库';
                }
            } else if ($val['type'] == 2) {
                $stock = $m_stock_out->where(array('stock_out_id' => $val['type_id']))->field('type, type_id, update_time, owner_role_id')->find();
                // if ($val['type_id'] == 49) {
                //     dd($stock);
                // }
                // 1销售   2采退   3调拨
                if ($stock['type'] == 1) {
                    $stock['type_id'] = $m_r_contract_sales->where(array('sales_id' => $stock['type_id']))->getField('contract_id');
                    $stock['name'] = $m_contract->where(array('contract_id' => $stock['type_id']))->getField('number');
                    $stock['url'] = U('contract/view', array('id' => $stock['type_id']));
                    $data['sn_log'][$key]['type_name'] = '销售出库';
                } else if ($stock['type'] == 2) {
                    $stock['name'] = $m_sales->where(array('sales_id' => $stock['type_id']))->getField('subject');
                    $stock['url'] = U('purchase/return_goods_view', array('id' => $stock['type_id']));
                    $data['sn_log'][$key]['type_name'] = '采购退货出库';
                } else if ($stock['type'] == 3) {
                    $stock['name'] = $m_transfer->where(array('transfer_id' => $stock['type_id']))->getField('number');
                    $stock['url'] = U('stock/transfer_view', array('transfer_id' => $stock['type_id']));
                    $data['sn_log'][$key]['type_name'] = '调拨出库';
                }
            }
            $stock['owner_role_name'] = $d_user->get_full_name((int) $stock['owner_role_id']);
            $data['sn_log'][$key]['detail'] = $stock;
            if ($key == 0) {
                $data['purchase_time'] = date('Y-m-d H:i:s', $stock['update_time']);     // 采购入库时间
            }
        }
        return $data;
    }

    
    /**
     * 出库
     * @param   array   $sn_id_list     更新条件 [sn_id, ]
     * @param   int     $stock_out_id   出库单ID
     */
    public function stockOut($sn_id_list, $stock_out_id)
    {
        $data = $sn_log_data = array();
        $data['stock_out_id'] = $stock_out_id;
        $data['status'] = 2;            //  0入库在途 1在库 2出库 3退货在途
        $data['update_time'] = time();
        $this->where(array('sn_id' => array('IN', $sn_id_list)))->save($data);
        foreach ($sn_id_list as $key => $val) {
            $sn_log_data[] = array('sn_id' => $val, 'type' => 2, 'type_id' => $stock_out_id);
        }
        $this->snLogAdd($sn_log_data);
    }


}