<?php 
class WarehouseModel extends Model{
    
    public $msg = '';


    /**
    * 获取全部仓库列表
    * @param 调拨中，未出库的数量
    * @param $status 默认为空查询全部，1只查启用 2只查禁用
    **/
    function getWarehouseList($status = ''){
        $where['is_deleted'] = 0;
        if ($status) {
            $where['status'] = $status;
        }
        if (!session('?admin')) {
            $where['owner_role_id'] = array('like', '%,'.session('role_id').',%');
        }
        $list = $this->where($where)->select();
        foreach ($list as $k => $v) {
            $transfer_ids = array();
            $transfer_ids = M('Transfer')->where('out_warehouse_id = %d and status = 0', $v['warehouse_id'])->getField('transfer_id',true);
            $list[$k]['transfer_count'] = M('TransferProduct')->where(array('transfer_id' => array('in', $transfer_ids), 'product_info_id' => $v['product_info_id']))->sum('count') ?: 0;
        }
        return $list;
    }


    /**
    * 获取仓库信息
    **/
    function getWarehouse($warehouse_id){
        return $this->where(array('is_deleted'=>0,'warehouse_id'=>$warehouse_id))->find();
    }


    /**
     * 是否是本仓库管
     */
    public function isStorehouse($warehouse_id)
    {
        if (session('?admin')) {
            return true;
        }
        if ($this->where(array('warehouse_id' => $warehouse_id, 'owner_role_id' => array('LIKE', '%,' . session('role_id') . ',%')))->count()) {
            return true;
        } else {
            $this->msg = '您不是本库库管，没有出入库权限，请联系管理员。';
            return false;
        }
    }


    /**
     * 选择列表
     */
    public function selectList()
    {
        if (session('?admin')) {
            $where = array('is_deleted' => 0, 'status' => 1);
        } else {
            $where = array('is_deleted' => 0, 'status' => 1, 'owner_role_id' => array('LIKE' , '%' . session('role_id') . '%'));
        }
        return M('Warehouse')->where($where)->select();
    }
    

}