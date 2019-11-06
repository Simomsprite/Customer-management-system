<?php 
class MessageModel extends Model{
    
    public $msg = '';

    /**
     * 获取待审核发票数量
     * @author lee
     */
    public function checkInvoiceNum()
    {
        if (checkPerByAction('invoice','check')) {
            $where['is_checked'] = 0;
            $where['create_role_id'] = array('in', getPerByAction('invoice', 'index'));
            return (int) M('Invoice')->where($where)->count();
        }
        return false;
    }


    /**
     * 获取待审核的采购单数量
     * @author lee
     */
    public function checkPurchaseNum()
    {
        if (checkPerByAction('purchase','index')) {
           $ids = $this->getCheckedIds(1);
           $where['purchase_id'] = array('in', $ids ?: '');
           $where['ownre_role_id'] = getPerByAction('purchase','index');
           return (int) M('Purchase')->where($where)->count();
        }
    }


    /**
     * 获取待审核的采购退货
     * @author lee
     */
    public function checkPurchaseReturnNum()
    {
        if (checkPerByAction('purchase','return_goods')) {
            $ids = $this->getCheckedIds(2);
            $where['sales_id'] = array('in', $ids ?: '');
            $where['ownre_role_id'] = getPerByAction('purchase','return_goods');
            return (int) M('Sales')->where($where)->count();
        }
    }


    /**
     * 获取待审核的销售退货
     * @author lee
     */
    public function checkSalesReturnNum()
    {
        if (checkPerByAction('sales','return_index')) {
            $ids = $this->getCheckedIds(3);
            $where['purchase_id'] = array('in', $ids ?: '');
            $where['ownre_role_id'] = getPerByAction('sales','return_index');
            return (int) M('Purchase')->where($where)->count();
        }
    }


    /**
     * 获取待审核的库存调拨
     * @author lee
     */
    public function checkTransferNum()
    {
        if (checkPerByAction('stock','transfer')) {
            $ids = $this->getCheckedIds(4);
            $where['transfer_id'] = array('in', $ids ?: '');
            $where['ownre_role_id'] = getPerByAction('stock','transfer');
            return (int) M('Transfer')->where($where)->count();
        }
    }


    /**
     * 获取某个类型的订单需要被审核的ID集合
     * @author lee
     */
    public function getCheckedIds($type_id){
        $where['type_id'] = $type_id;
        $where['exam_status'] = array('lt', 2);
        return M('RExam')->where($where)->getField('order_id', true);
    }

    

}