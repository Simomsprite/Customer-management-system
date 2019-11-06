<?php

/**
* 
*/
class TemplateModel extends Model
{
	private $field_model = array('customer' => '客户', 'contract' => '合同', 'product' => '产品', 'contacts' => '联系人', 'leads' => '线索', '' => '预设', 'business' => '商机');

	private $object = array();

	public function __construct()
	{
		$object = M('template_object')->field('id,models')->select();
		foreach ($object as $key => $val) {
			$this->object[$val['id']] = $val['models'];
		}
	}


	public function get_field($object)
	{
		if (!isset($this->object[$object])) {
			return false;
		}
		$field = array();
		$field['sales']['name'] = '合同产品信息';
		$field['sales']['fields'][] = array('model' => 'sales', 'field' => 'business', 'name' => '相关商机');
		$field['sales']['fields'][] = array('model' => 'sales', 'field' => 'prime_price', 'name' => '产品合计');
		$field['sales']['fields'][] = array('model' => 'sales', 'field' => 'sales_price', 'name' => '销售订单金额');
		$field['sales']['fields'][] = array('model' => 'sales', 'field' => 'final_discount_rate', 'name' => '整单折扣');

		$where = array('field' => array('NOT IN', array('customer_id')), 'model' => array('IN', $this->object[$object]));
		$fields = M('fields')->where($where)->field('model,field,name')->select();
		foreach ($fields as $key => $val) {
			$field[$val['model']]['name'] = $this->field_model[$val['model']];
			$field[$val['model']]['fields'][] = $val;
		}

		if (isset($field['customer'])) {
			$field['customer']['fields'][] = array('model' => 'customer', 'field' => 'creator_role_id', 'name' => '创建人');
			$field['customer']['fields'][] = array('model' => 'customer', 'field' => 'owner_role_id', 'name' => '负责人');
		}

		if (isset($field['contract'])) {
			$field['contract']['fields'][] = array('model' => 'contract', 'field' => 'creator_role_id', 'name' => '创建人');
			$field['contract']['fields'][] = array('model' => 'contract', 'field' => 'owner_role_id', 'name' => '负责人');
			$field['contract']['fields'][] = array('model' => 'contract', 'field' => 'number', 'name' => '合同编号');
		}

		// 字段块
		$field_block = M('template_field_block')->select();
		return array('field' => $field, 'block' => $field_block);
	}


	/**
	 * 进销存模块单子打印模板
	 */
	public function pssTemplate($data = array())
	{
		$html = '<div style="font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">';
		// 标题
		if (isset($data['title'])) {
			$html .= '<p style="text-align: center; margin-bottom: 20px; font-size: 24px;">'. $data['title'] .'</p>';
		} else {
			$html .= '<p style="text-align: center; margin-bottom: 20px; font-size: 24px;">进销存产品订单</p>';
		}
		// 表头
		if (isset($data['header'])) {
			$html .= '<div style="float: left; width: 100%;">';
			$i = 1;
			foreach ($data['header'] as $key => $val) {
				$html .= '<div style="float: left; width: 48%; margin:5px 1%;"><span style="font-size: 18px;">'. $key .'：</span><span style="font-size: 16px;">'. $val .'</span></div>';
				if ($i % 2 == 0) {
					$html .= '</div>';
					if ($i < count($data['header'])) {
						$html .= '<div style="float: left; width: 100%;">';
					}
				} elseif ($i == count($data['header'])) {
					$html .= '</div>';
				}
				$i++;
			}
		}
		// 产品
		if (isset($data['product'])) {
			$html .= '<div style="float: left; width: 100%; margin-top: 20px;"><table style="width: 100%;"><tbody>';
			$th = true;
			foreach ($data['product'] as $val) {
				$html .= '<tr>';
					foreach ($val as $v) {
						if ($th) {
							$html .= '<th style="border: 1px solid #ccc; text-align: center;">'. $v .'</th>';
						} else {
							$html .= '<td style="border: 1px solid #ccc; text-align: center;">'. $v .'</td>';
						}
					}
				$html .= '</tr>';
				$th = false;
			}
			$html .= '</tbody></table></div>';
		}
		$html .= '</div>';
		return $html;
	}
}
