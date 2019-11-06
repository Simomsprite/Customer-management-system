<?php

class StockInProductinfoModel extends Model
{
    public $msg;
    /**
     * 入库产品列表
     */
    public function getStockInProductList($stock_in_id)
    {
        $d_product_info_view = D('ProductInfoView');
        $d_product = D('Product');
        $d_purchase = D('Purchase');
        $list = $this->where(array('stock_in_id' => $stock_in_id))->select();
        foreach ($list as $key => $val) {
            $product_info = $d_product_info_view->where(array('product_info_id' => $val['product_info_id']))->field('name,standard,has_sn')->find();
            $product_info['spec'] = $d_product->getProductInfoSpec($val['product_info_id']);
            $val['purchase_product_count'] = $d_purchase->getPurchaseProductCount($val['product_info_id']);
            $list[$key] = $val + $product_info;
        }
        return $list;
    }

    /**
     * 入库产品添加
     */
    public function addData($stock_in_id)
    {
        $d_stock = D('Stock');
        $warehouse_id = $_POST['warehouse_id'];
        $stock_data = $data = array();
        foreach ($_POST['list'] as $key => $val) {
            if ($val['count'] > 0) {
                $val['stock_in_id'] = $stock_in_id;
                $val['product_info_id'] = $key;
                if ($val = $this->create($val)) {
                    $data[] = $val;
                    $stock_data[] = array(
                        'warehouse_id' => $warehouse_id,
                        'product_info_id' => $val['product_info_id'],
                        'count' => $val['count']
                    );
                } else {
                    $this->msg = $this->getError();
                    return false;
                }
            } 
        }
        if ($data) {
            if ($this->addAll($data)) {
                $d_stock->updateStocks($stock_data);
                return true;
            } else {
                $this->msg = $this->getError();
                return false;
            }
        } else {
            $this->msg = '入库产品数量不能为空';
            return false;
        }
    }

    /**
     * 删除添加失败的错误数据
     */
    public function deleteStockIn($stock_in_id)
    {
        $this->where(array('stock_in_id' => $stock_in_id))->delete();
    }

}