<?php

class StockOutModel extends Model
{
    public $msg;
    public $id;
    public $stock_out_status_array = array('未出库', '部分出库', '完成出库');
    public $stock_out_status;
    public $d_stock_out_product_info;


    public function _initialize()
    {
        $this->d_stock_out_product_info = D('StockOutProductinfo');
    }


    /**
     * 获取合同（销售单）出库列表
     */
    public function getListBySales($sales_id, $type = 1)
    {
        $d_user = D('User');
        $m_warehouse = D('Warehouse');
        $where = array('type' => $type, 'type_id' => $sales_id);
        $list = $this->where($where)->select();
        foreach ($list as $key => $val) {
            $list[$key]['warehouse_name'] = $m_warehouse->where(array('warehouse_id' => $val['warehouse_id']))->getField('name');
            $list[$key]['owner_role_nane'] = $d_user->get_full_name((int) $val['owner_role_id']);
            $list[$key]['create_time'] = date('Y-m-d', $val['create_time']);
            $list[$key]['update_time'] = date('Y-m-d', $val['update_time']);
            $list[$key]['product_info_list'] = $this->getProductInfoList($val['stock_out_id']);
        }
        return $list;
    }

    /**
     * 获取出库库产品列表
     */
    public function getProductInfoList($stock_out_id)
    {
        $d_product_info = D('ProductInfo');
        $list = $this->d_stock_out_product_info->where(array('stock_out_id' => $stock_out_id))->select();
        foreach ($list as $key => $val) {
            $product_info = $d_product_info->getNameSpec($val['product_info_id']);
            $list[$key] = array_merge($val, $product_info);
        }
        return $list;
    }


    /**
     * 获取销售单/采退单 产品已出库数
     * @param       int     $type       默认1销售单，2采退单，3调拨，4其他
     */
    public function getStockOutCount($type_id, $product_info_id, $type = 1)
    {
        // 获取出库列表
        $stock_out_id_list = $this->where(array('type_id' => $type_id, 'type' => $type))->getField('stock_out_id', true);
        $count = $this->d_stock_out_product_info->where(array('product_info_id' => $product_info_id, 'stock_out_id' => array('IN', $stock_out_id_list)))->sum('count');
        return (int) $count;
    }

    /**
     * 添加出库单
     */
    public function addData($type, $type_field)
    {   
        // 判断出库单是否重复
        if ($this->where('number = "%s"', trim($_POST['number']))->find()) {
            $this->msg = '出库单号重复！';
            return false;
        }

        $data['type'] = $type;
        $data['number'] = $_POST['number'];
        $data['type_id'] = $_POST[$type_field];
        $data['warehouse_id'] = $_POST['warehouse_id'];
        $data['owner_role_id'] = $_POST['owner_role_id'];
        $data['express'] = $_POST['express'] ?: '';
        $data['remark'] = $_POST['remark'];
        $data['create_time'] = time();
        $data['update_time'] = strtotime($_POST['update_time']);
        if ($this->create($data)) {
            if ($stock_out_id = $this->add()) {
                $this->id = $stock_out_id;
                return true;
            } else {
                $this->msg = '保存失败';
            }
        } else {
            $this->msg = '数据对象创建失败[stock_out]';
        }
        return false;
    }

    /**
     * 添加出库产品(调用前需调用addData)
     */
    public function addProduct()
    {
        $warehouse_id = $_POST['warehouse_id'];
        $d_stock = D('Stock');
        $d_sn = D('Sn');
        $sn_id_list = $stock_data = $data = array();
        foreach ($_POST['product_info_list'] as $key => $val) {
            if ($val['count'] > 0) {
                $val['product_info_id'] = $key;
                $val['stock_out_id'] = $this->id;
                if ($temp_val = $this->d_stock_out_product_info->create($val)) {
                    $data[] = $temp_val;
                    $stock_data[] = array(
                        'warehouse_id' => $warehouse_id,
                        'product_info_id' => $key,
                        'count' => - $val['count']
                    );
                    $sn_id_list = array_merge($sn_id_list, explode(',', trim($val['sn'], ',')));
                } else {
                    $this->msg = '数据对象创建失败[stock_out]';
                    return false;
                }
            }
        }
        if ($data) {
            if (M('StockOutProductinfo')->addAll($data)) {
                $d_stock->updateStocks($stock_data);
                if (!empty($sn_id_list)) {
                    $d_sn->stockOut($sn_id_list, $this->id);
                }
                $this->msg = '保存成功';
                return true;
            } else {
                $this->msg = '保存失败[stock_out_product]';
            }
        } else {
            $this->msg = '出库产品数量不能为空';
        }
        return false;
    }


    /**
     * 删除错误添加的数据
     */
    public function deleteData()
    {
        $this->delete($this->id);
        M('StockOutProductinfo')->where(array('stock_out_id' => $this->id))->delete();
    }


    /**
     * 采购退货出库产品信息
     */
    public function getReturnProduct($sales_id)
    {
        $stock_out_id_list =  $this->where(array('type_id' => array('IN', $sales_id)))->getField('stock_out_id', true);
        $list = M('StockOutProductinfo')->where(array('stock_out_id' => array('IN', $stock_out_id_list)))->field('product_info_id,count')->select();
        $res = array();
        foreach ($list as $key => $val) {
            if (!isset($res[$val['product_info_id']])) {
                $res[$val['product_info_id']] = 0;
            }
            $res[$val['product_info_id']] += $val['count'];
        }
        return $res;
    }

    /**
     * 查询采退单是否有出库
     */
    public function getPurchaseHasStockOut($sales_id)
    {
        return (bool) $this->where(array('type_id' => $sales_id, 'type' => 2))->count();
    }


    /**
     * 采退产品出库数量
     */
    public function purchaseReturnProductCount($purchase_id, $product_info_id)
    {
        $sales_id_list = M('sales')->where(array('customer_id' => $purchase_id, 'type' => 1))->getField('sales_id', true);
        $count = 0;
        foreach ($sales_id_list as $key => $val) {
            $count += $this->getStockOutCount($val, $product_info_id, 2);
        }
        return $count;
    }


    /**
     * 更新出库信息
     * @param where 条件示例 ['stock_out_id' => $stock_out_id]
     * @param data 数据
     * @author lee
     */
    public function updateData($where, $data)
    {
        if ($this->create($data)) {
            $m = $this->where($where)->save($data);
            if ($m !== false) {
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
     * 获取全部入库单列表
     * @author lee
     */
    public function getList($where = array(), $p = 1, $listrows = 15, $order = '')
    {   
        $m_sales = M('Sales');
        $d_contact = D('Contract');
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
                    $list[$k]['type_name'] = '销售出库';
                    $type_r_number = $m_sales->where('sales_id = %d', $v['type_id'])->getField('number');

                    $contract = $d_contact->getContractBySalesId($v['type_id']);
                    $contract['contract_id'] = $contract['contract_id'].'#tab7';
                    $list[$k]['type_r_number'] = "<a href=".U('contract/view', array('id'=> $contract['contract_id'])).">{$contract['number']}</a>";
                    break;
                case '2':
                    $list[$k]['type_name'] = '采购退货出库';
                    $type_r_number = $m_sales->where('sales_id = %d', $v['type_id'])->getField('number');

                    $contract = $d_contact->getContractBySalesId($v['type_id']);
                    $contract['contract_id'] = $contract['contract_id'].'#tab7';
                    $list[$k]['type_r_number'] = "<a href=".U('contract/view', array('id'=> $contract['contract_id'])).">{$contract['number']}</a>";
                    break;
                case '3':
                    $list[$k]['type_name'] = '库存调拨出库';
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
            array('field' => 'number', 'name' => '出库单号', 'form_type' => 'text'),
            array('field' => 'owner_role_id', 'name' => '负责人', 'form_type' => 'role'),
            array('field' => 'create_time', 'name' => '出库时间', 'form_type' => 'date'),
            array('field' => 'remark', 'name' => '备注', 'form_type' => 'text'),
        );
        return $search_field_list;
    }


}
