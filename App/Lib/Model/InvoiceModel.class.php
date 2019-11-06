<?php 
class InvoiceModel extends Model{
    
    public $msg = '';


    /**
     * 搜索字段list信息
     * @param field的值命名说明，field的值是对应的字段名，但是因为有联表查询的情况，有的字段名需要命名为 "表名.字段名",由于php会把带有'.'的参数解析为'_'，'|'和'\'也会影响的url的解析，所以这里用'-'代替，然后处理where条件时再做处理即可
     * @param form_type类型决定了搜索的表单类型 text【普通文本】contract_check【下拉选择审核状态】role【下拉选择系统员工】data【选择日期】
     * @author lee
     */
    public function search_field_list()
    {
    	$search_field_list = array(
			array('field' => 'name', 'name' => '发票编号', 'form_type' => 'text'),
			array('field' => 'is_checked', 'name' => '审核状态', 'form_type' => 'contract_check'),
			array('field' => 'invoice-create_role_id', 'name' => '开票人', 'form_type' => 'role'),
			array('field' => 'customer_name', 'name' => '客户名称', 'form_type' => 'text'),
			array('field' => 'price', 'name' => '开票金额', 'form_type' => 'number'),
			array('field' => 'invoice_time', 'name' => '开票日期', 'form_type' => 'date'),
			array('field' => 'create_time', 'name' => '创建时间', 'form_type' => 'date'),
			array('field' => 'update_time', 'name' => '修改时间', 'form_type' => 'date'),
			//array('field' => 'address_a', 'name' => '测试地址', 'form_type' => 'address'),
		);
    	return $search_field_list;
    }



}