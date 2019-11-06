<?php

class StockInModel extends Model
{
    public $msg;
    public $stock_in_status_array = array('未入库', '部分入库', '完成入库');
    public $stock_in_status;

    /**
     * 获取采购单已入库某产品数量
     */
    public function getPurchaseProductCount($purchase_id, $product_info_id, $warehouse_id = 0)
    {
        if ($product_info_id && $purchase_id) {
            $where = array('type' => M('Purchase')->where('purchase_id = %d', $purchase_id)->getField('type'), 'type_id' => $purchase_id);
            if ($warehouse_id) {
                $where['warehouse_id'] = $warehouse_id;
            }
            $stock_in_id_list = $this->where($where)->getField('stock_in_id', true);
            $count_arr = M('StockInProductinfo')->where(array('stock_in_id' => array('IN', implode(',', $stock_in_id_list)), 'product_info_id' => $product_info_id))->getField('count', true);
            return $count_arr ? array_sum($count_arr) : 0;
        } else {
            return false;
        }
    }

    /**
     * 获取采购单入库详情列表
     * @param type 1采购 2销售退货
     */
    public function getPurchaseStockInList($purchase_id, $type = 1)
    {
        $d_stock_in_productinfo = D('StockInProductinfo');
        $d_user = D('User');
        $m_warehouse = D('Warehouse');
        $stock_in_list = $this->where(array('type' => $type, 'type_id' => $purchase_id))->order('update_time')->select();
        $in_list = array();
        foreach ($stock_in_list as $key => $val) {
            $stock_in_list[$key]['warehouse_name'] = $m_warehouse->where(array('warehouse_id' => $val['warehouse_id']))->getField('name');
            $stock_in_list[$key]['in_list'] = $d_stock_in_productinfo->getStockInProductList($val['stock_in_id']);
            if ($stock_in_list[$key]['in_list']) $in_list = array_merge($in_list, $stock_in_list[$key]['in_list']);
            $stock_in_list[$key]['create_time'] = date('Y-m-d', $val['create_time']);
            $stock_in_list[$key]['owner_role_name'] = $d_user->get_full_name((int) $val['owner_role_id']);
        }
        $stock_in_status = $this->getPurchaseStockInStatus($in_list, $purchase_id);
        $this->stock_in_status = array('name' => $this->stock_in_status_array[$stock_in_status], 'code' => $stock_in_status);
        return $stock_in_list;
    }

    /**
     * 采购单入库进度
     */
    public function getPurchaseStockInStatus($in_list, $purchase_id)
    {
        // 获取采购单所有产品采购数量
        if (empty($in_list)) return 0;      // 未入库
        $list = array();
        $product_info_count = M('PurchaseProduct')->where(array('purchase_id' => $purchase_id))->count();
        foreach ($in_list as $key => $val) {
            $list[$val['product_info_id']]['purchase_product_count'] = $val['purchase_product_count'];
            $list[$val['product_info_id']]['stock_in_count_list'][] = $val['count'];
        }
        foreach ($list as $key => $val) {
            $list[$key]['status'] = (int) ($val['purchase_product_count'] == array_sum($val['stock_in_count_list']));
        }
        if ($product_info_count == count($list) && count($list) == array_sum(y_array_column($list, 'status'))) {
            return 2;       // 完成入库
        } else {
            return 1;       // 部分入库
        }
    }


    /**
     * 添加入库单
     */
    public function addData()
    {
        // 判断入库单是否重复
        if ($this->where('number = "%s"', trim($_POST['number']))->find()) {
            $this->msg = '入库单号重复！';
            return false;
        }

        $d_stock_in_productinfo = D('StockInProductinfo');
        $d_sn = D('Sn');
        if ($this->create($_POST)) {
            if ($stock_in_id = $this->add()) {
                if ($d_stock_in_productinfo->addData($stock_in_id)) {
                    if ($d_sn->stockIn($stock_in_id)) {
                        $this->msg = '入库成功';
                        return true;
                    } else {
                        $d_stock_in_productinfo->deleteStockIn($stock_in_id);
                        $this->delete($stock_in_id);
                        $this->msg = $d_sn->msg;
                        return false;
                    }
                } else {
                    $this->delete($stock_in_id);
                    $this->msg = $d_stock_in_productinfo->msg;
                    return false;
                }
            } else {
                $this->msg = '入库失败';
                return false;
            }
        } else {
            $this->msg = $this->getError();
            return false;
        }
    }

    /**
     * 查询采购单是否有入库
     */
    public function getPurchaseHasStockIn($purchase_id)
    {
        return (bool) $this->where(array('type' => 1, 'type_id' => $purchase_id))->count();
    }


  
    /**
     * 获取全部入库单列表
     * @author lee
     */
	public function getList($where = array(), $p = 1, $listrows = 15, $order = '')
    {   
        $m_purchase = M('Purchase');
        $m_transfer = M('Transfer');
        $m_warehouse = M('Warehouse');
        $m_user = M('User');

        $count = $this->where($where)->count();
        $p_num = ceil($count/$listrows);
        if($p_num < $p) $p = $p_num;

        $list = $this->where($where)->page($p, $listrows)->order($order)->select();
        foreach ($list as $k => $v) {
            // 入库类型
            switch ($v['type']) {
                case '1':
                    $list[$k]['type_name'] = '采购入库';
                    $type_r_number = $m_purchase->where('purchase_id = %d', $v['type_id'])->getField('number');

                    $v['type_id'] = $v['type_id'].'#tab2';
                    $list[$k]['type_r_number'] = "<a href=".U('purchase/view', array('id'=> $v['type_id'])).">{$type_r_number}</a>";
                    break;
                case '2':
                    $list[$k]['type_name'] = '销售退货入库';
                    $type_r_number = $m_purchase->where('purchase_id = %d', $v['type_id'])->getField('number');

                    $v['type_id'] = $v['type_id'].'#tab2';
                    $list[$k]['type_r_number'] = "<a href=".U('purchase/view', array('id'=> $v['type_id'])).">{$type_r_number}</a>";
                    break;
                case '3':
                    $list[$k]['type_name'] = '库存调拨入库';
                    $type_r_number = $m_transfer->where('transfer_id = %d', $v['type_id'])->getField('number');
                    $list[$k]['type_r_number'] = "<a href=".U('stock/transfer_view', array('transfer_id'=> $v['type_id'])).">{$type_r_number}</a>";
                    break;
                default:
                    break;
            }
            $list[$k]['warehouse_name'] = $m_warehouse->where('warehouse_id = %d', $v['warehouse_id'])->getField('name');
            $list[$k]['create_time'] = date('Y-m-d', $v['create_time']);
            $list[$k]['owner_name'] = $m_user->where('role_id = %d', $v['owner_role_id'])->getField('full_name');
        }
        return array('list' => $list, 'count' => $count);
    }

    /**
     * 入库记录 搜索字段list信息
     * @param field的值命名说明，field的值是对应的字段名，但是因为有联表查询的情况，有的字段名需要命名为 "表名.字段名",由于php会把带有'.'的参数解析为'_'，'|'和'\'也会影响的url的解析，所以这里用'-'代替，然后处理where条件时再做处理即可
     * @param form_type类型决定了搜索的表单类型 text【普通文本】contract_check【下拉选择审核状态】role【下拉选择系统员工】data【选择日期】
     * @author lee
     */
    public function search_field_list()
    {
        $search_field_list = array(
            array('field' => 'number', 'name' => '入库单号', 'form_type' => 'text'),
            array('field' => 'owner_role_id', 'name' => '负责人', 'form_type' => 'role'),
            array('field' => 'create_time', 'name' => '入库时间', 'form_type' => 'date'),
            array('field' => 'remark', 'name' => '备注', 'form_type' => 'text'),
        );
        return $search_field_list;
    }


    /**
     * 获取销售退货的产品信息
     */
    public function getReturnProduct($purchase_id)
    {
        $stock_in_id_list =  $this->where(array('type_id' => array('IN', $purchase_id)))->getField('stock_in_id', true);
        $list = M('StockInProductinfo')->where(array('stock_in_id' => array('IN', $stock_in_id_list)))->field('product_info_id,count')->select();
        $res = array();
        foreach ($list as $key => $val) {
            if (!isset($res[$val['product_info_id']])) {
                $res[$val['product_info_id']] = 0;
            }
            $res[$val['product_info_id']] += $val['count'];
        }
        return $res;
    }
}