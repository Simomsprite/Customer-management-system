<?php

class FinanceModel extends Model
{
    
    /**
     * 添加应收款
     * @param array $data = [type = 2, contract_id, customer_id, name, price, pay_time, owner_role_id, description]
     */
    public function addReceivables($data)
    {
		//应收款编号前缀
        $m_receivables = M('Receivables');
        $receivables_custom = M('Config')->where('name="receivables_custom"')->getField('value');
        $data['prefixion'] = $receivables_custom;
        $data['type'] = $data['type'] ?: 2;
        $data['price'] = round($data['price'], 2);

        $receivables_max_id = $m_receivables->max('receivables_id');
        $receivables_max_id += 1;
		$receivables_max_code = str_pad($receivables_max_id,4,0,STR_PAD_LEFT);//填充字符串的左侧（将字符串填充为新的长度）
        $data['name'] = $receivables_custom.date('Ymd').'-'.$receivables_max_code;

        if ($m_receivables->create($data)) {
            if ($m_receivables->add()) {
                $this->msg = '应收款添加成功';
                return true;
            } else {
                $this->msg = '应收款添加失败';
            }
        } else {
            $this->msg = '数据对象创建失败';
        }
        return false;
    }


    /**
     * 添加应付款
     * @param array $data = [type_id, contract_id, customer_id, name, price, pay_time, owner_role_id, description]
     */
    public function addPayment($data)
    {
        $payables = M('payables');
        //应付款编号
        $payables_max_id = $payables->max('payables_id');
        $payables_max_id = $payables_max_id+1;
        $payables_max_code = str_pad($payables_max_id,4,0,STR_PAD_LEFT);//填充字符串的左侧（将字符串填充为新的长度）
        $payables_custom = M('config') -> where('name="payables_custom"')->getField('value');
        $data['name'] = $payables_custom.date('Ymd').'-'.$payables_max_code;

        if ($payables->create($data)) {
            if ($payables->add()) {
                $this->msg = '应付款添加成功';
                return true;
            } else {
                $this->msg = '应付款添加失败';
            }
        } else {
            $this->msg = '数据对象创建失败';
        }
        return false;
    }

    /**
     * 整理数据
     * @param array $init_data = [type_id, order_id, finance_time]
     * @param type_id 1采购 2采购退货 3销售退货
     */
    public function addFinance($init_data)
    {
        switch ($init_data['type_id']) {
            case '1':
                $order = M('Purchase')->where('type = 1 and purchase_id = %d', $init_data['order_id'])->find();
                $data['contract_id'] = $order['purchase_id'];
                $data['customer_id'] = $order['type_id'];
                $data['price'] = $order['purchase_amount'];
                $data['type_id'] = -1;
                break;
            case '2':
                $order = M('Sales')->where('type = 1 and sales_id = %d', $init_data['order_id'])->find();
                $data['contract_id'] = $order['sales_id'];
                $data['customer_id'] = M('Purchase')->where('purchase_id = %d', $order['customer_id'])->getField('type_id');
                $data['price'] = $order['sales_price'];
                $data['type'] = 2;
                break;
            case '3':
                $order = M('Purchase')->where('type = 2 and purchase_id = %d', $init_data['order_id'])->find();
                $data['contract_id'] = $order['purchase_id'];
                $data['price'] = $order['purchase_amount'];
                $data['type_id'] = -2;
                break;
            default:
                # code...
                break;
        }
        $data['pay_time'] = strtotime($init_data['finance_time']);
        $data['owner_role_id'] = $order['owner_role_id'];
        $data['creator_role_id'] = session('role_id');
        $data['create_time'] = $data['update_time'] = time();
        if ($init_data['type_id'] == 1 || $init_data['type_id'] == 3) {
            $res = $this->addPayment($data);
        } else if ($init_data['type_id'] == 2){
            $res = $this->addReceivables($data);
        }
        return $res;
    }


    /**
     * 采购单、销退单详情、采退详情 应付、收款列表
     */
    public function ablesList($where = array(), $table = 'payables')
    {
        $table = strtolower($table);
        $name = $table == 'payables' ? '付款' : '收款';
        $list = M($table)->where($where)->field($table .'_id, name, creator_role_id, owner_role_id, create_time, price, status')->select();
        $d_user = D('User');
        foreach ($list as $key => $val) {
            $list[$key]['creator_role_name'] = $d_user->get_full_name((int) $val['creator_role_id']);
            $list[$key]['owner_role_name'] = $d_user->get_full_name((int) $val['owner_role_id']);
            $list[$key]['create_time'] = date('Y-m-d', $val['create_time']);
            $list[$key]['price'] = number_format($val['price'], 2);
            // 状态0未收1部分收2已收
            switch ($val['status']) {
                case '0':
                    $list[$key]['status_name'] = '未'. $name;
                    break;
                case '1':
                    $list[$key]['status_name'] = '部分'. $name;
                    break;
                case '2':
                    $list[$key]['status_name'] = '完成'. $name;
                    break;
            }
        }
        return $list;
    }


    /**
     * 财务判断是否有审核权限
     * @author lee
     */
    public function do_examine($status, $m = 'finance', $a = 'check')
    {
        if ($status == 0 && (checkPerByAction($m, $a) || session('?admin'))) {
            return true;
        } else {
            return false;
        }
    }



}
