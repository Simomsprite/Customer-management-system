<?php

class MarketModel extends Model
{
    public $msg = '';                   // 提示信息
    public $market_id = 0;              // 添加成功ID || 活动ID
    public $market_info = array();      // 单个市场活动详情
    public $role_list = array();        // 员工详情列表 [创建人、负责人、参与人、日志作者]
    public $view_field = array();            // 详情展示字段

    protected $_validate = array();
    protected function _validationFieldItem($data,$val) {
        switch(strtolower(trim($val[4]))) {
            case 'function':// 使用函数进行验证
            case 'callback':// 调用方法进行验证
                $args = isset($val[6])?(array)$val[6]:array();
                if(is_string($val[0]) && strpos($val[0], ','))
                    $val[0] = explode(',', $val[0]);
                if(is_array($val[0])){
                    // 支持多个字段验证
                    foreach($val[0] as $field)
                        $_data[$field] = $data[$field];
                    array_unshift($args, $_data);
                }else{
                    array_unshift($args, $data[$val[0]]);
                }
                if('function'==$val[4]) {
                    return call_user_func_array($val[1], $args);
                }else{
                    return call_user_func_array(array(&$this, $val[1]), $args);
                }
            case 'confirm': // 验证两个字段是否相同
                return $data[$val[0]] == $data[$val[1]];
            case 'unique': // 验证某个值是否唯一
                if($data[$val[0]]){
                    if(is_string($val[0]) && strpos($val[0],','))
                        $val[0]  =  explode(',',$val[0]);
                    $map = array();
                    if(is_array($val[0])) {
                        // 支持多个字段验证
                        foreach ($val[0] as $field)
                            $map[$field]   =  $data[$field];
                    }else{
                        $map[$val[0]] = $data[$val[0]];
                    }
                    if(!empty($data[$this->getPk()])) { // 完善编辑的时候验证唯一
                        $map[$this->getPk()] = array('neq',$data[$this->getPk()]);
                    }
                    if($this->where($map)->find())   return false;
                    return true;
                }else{
                    return true;
                }
            default:  // 检查附加规则
                return $this->check($data[$val[0]],$val[1],$val[4]);
        }
    }
    
    public function _initialize(){
        $fields = M('fields')->where('(model = \'\' or model = \'market\') and is_validate=1 and is_main=1')->select();
        foreach($fields as $field){
            $validate = array();
            if($field['is_null']){
                $validate[0] = $field['field'];
                $validate[1] = 'require';
                $validate[2] = L('NOT NULL',array($field['name']));
                $validate[3] = 0;
                $validate[4] = '';
                $validate[5] = 3;
                $this->_validate[] = $validate;
            }
            
            $validate[0] = $field['field'];
            $validate[1] = '';
            $validate[2] = L('FORMAT ERROR',array($field['name']));
            $validate[3] = 0;
            $validate[4] = 'regex';
            $validate[5] = 3;
            switch ($field['form_type']){
                case 'email';
                    $validate[1] = '/|^(\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*)?$/';
                    $this->_validate[] = $validate;
                    break;
                case 'mobile';
                    $validate[1] = '/|^1[356789][0-9]{9}$/';
                    $this->_validate[] = $validate;
                    break;
                case 'phone';
                    $validate[1] = '/|^((([0+]\d{2,3}-)?(0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?)?$/';
                    $this->_validate[] = $validate;
                    break;
                case 'number';
                    $validate[1] = '/|^\d+$/';
                    $this->_validate[] = $validate;
                    break;
            }
            
            if($field['is_unique']){
                $validate[0] = $field['field'];
                $validate[1] = '';
                $validate[2] = L('ALREADY EXISTS',array($field['name']));
                $validate[3] = 0;
                $validate[4] = 'unique';
                $validate[5] = 3;
                $this->_validate[] = $validate;
            }
            
        }
    }

    public function add($data = null) {
        if ($data == null) {
            $this->msg = '参数不正确';
            return false;
        }
        $data['create_time'] = time();
        $data['update_time'] = time();
        $fields = D('Field')->get_add_field('market');
        $main_fields = $fields['main_fields'];
        $main_fields['executor_role_ids'] = array('field' => 'executor_role_ids', 'form_type' => 'text');
        $data_fields = $fields['data_fields'];
        $main_data = $data_data = array();
        foreach ($data as $key => $val) {
            if (isset($main_fields[$key])) {
                if ($main_fields[$key]['form_type'] == 'datetime') {
                    $val = is_int($val) ? $val : strtotime($val);
                } else if ($main_fields[$key]['form_type'] == 'address') {
                    $val = implode("\n", $val);
                }
                $main_data[$key] = $val;
            } else if(isset($data_fields[$key])) {
                $data_data[$key] = $val;
            }
        }
        if ($this->create($main_data) && D('MarketData')->create($data_data) !== false) {
            $market_id = M('market')->add($main_data);
            if ($market_id) {
                $data_data['market_id'] = $market_id;
                $res = M('market_data')->add($data_data);
                if ($res) {
                    $this->market_id = $market_id;
                    $this->msg = '添加成功！';
                    $this->market_info = $main_data;
                    if ($main_data['owner_role_id'] != session('role_id')) {
                        $this->addSendMessage($main_data['owner_role_id']);
                    }
                    if ($executor_list = trim($main_data['executor_role_ids'], ',')) {
                        $this->addSendMessage(explode(',', $executor_list));
                    }
                    return true;
                } else {
                    M('market')->delete($market_id);
                    $this->msg = '添加失败，稍后重试！';
                    return false;
                }
            } else {
                $this->msg = '添加失败，稍后重试！';
                return false;
            }
        } else {
            $this->msg = $this->getError() . '  ' . D('MarketData')->getError();
            return false;
        }
        
    }

    public function save($data = null) {
        if ($data == null) {
            $this->msg = '参数不正确';
            return false;
        }
        $this->market_id = $market_id = $data['market_id'];
        if (!$this->check_auth($this->market_id)) {
            $this->msg = '您没有此权利！';
            return false;
        }
        $old_main_data = M('market')->field('owner_role_id,executor_role_ids')->find($market_id);
        $data['update_time'] = time();
        $fields = D('Field')->get_add_field('market');
        $main_fields = $fields['main_fields'];
        $main_fields['executor_role_ids'] = array('field' => 'executor_role_ids', 'form_type' => 'text');
        $data_fields = $fields['data_fields'];
        $main_data = $data_data = array();
        foreach ($data as $key => $val) {
            if (isset($main_fields[$key])) {
                if ($main_fields[$key]['form_type'] == 'datetime') {
                    $val = is_int($val) ? $val : strtotime($val);
                } else if ($main_fields[$key]['form_type'] == 'address') {
                    $val = implode("\n", $val);
                }
                $main_data[$key] = $val;
            } else if(isset($data_fields[$key])) {
                $data_data[$key] = $val;
            }
        }
        $main_data['market_id'] = $market_id;
        $data_data['market_id'] = $market_id;

        if ($this->create($main_data) && D('MarketData')->create($data_data) !== false) {
            $res1 = M('market')->save($main_data);
            if ($res1) {
                $this->market_info = $main_data;
                $old_data_data = M('market_data')->find($market_id);
                // asort($old_data_data);
                // asort($data_data);
                // if ($old_data_data == $data_data) {
                if (!checkDataDifficult($data_data, $old_data_data)) {
                    if ($old_main_data['owner_role_id'] != $main_data['owner_role_id']) {
                        $this->eidtSendMessage($old_main_data['owner_role_id'], $main_data['owner_role_id']);
                    }
                    if ($old_main_data['executor_role_ids'] != $main_data['executor_role_ids']) {
                        $this->eidtSendMessage(explode(',', trim($old_main_data['executor_role_ids'], ',')), explode(',', trim($main_data['executor_role_ids'], ',')));
                    }
                    $this->msg = '修改成功';
                    return true;
                } else {
                    $res2 = M('market_data')->where(array('market_id' => $data['market_id']))->save($data_data);
                    if ($res2) {
                        if ($old_main_data['owner_role_id'] != $main_data['owner_role_id']) {
                            $this->eidtSendMessage($old_main_data['owner_role_id'], $main_data['owner_role_id']);
                        }
                        if ($old_main_data['executor_role_ids'] != $main_data['executor_role_ids']) {
                            $this->eidtSendMessage(explode(',', trim($old_main_data['executor_role_ids'], ',')), explode(',', trim($main_data['executor_role_ids'], ',')));
                        }
                        $this->msg = '修改成功';
                        return true;
                    } else {
                        $this->msg = '修改失败[附表]';
                        return false;
                    }
                }
            } else {
                $this->msg = '修改失败[主表]';
                return false;
            }
        } else {
            $this->msg = $this->getError() . '  ' . D('MarketData')->getError();
            return false;
        }
    }


    /**
     * 单个市场活动
     */
    public function first($market_id, $action)
    {
        $this->market_info = D('MarketView')->where(array('market_id' => $market_id))->find();
        if ($action == 'view') {
            $this->market_id = $market_id;
            // 格式化时间
            $this->market_info['week_time'] = getTimeWeek($this->market_info['update_time']);
            $this->market_info['create_time'] = date('Y-m-d H:i:s', $this->market_info['create_time']);
            $this->market_info['update_time'] = date('Y-m-d H:i:s', $this->market_info['update_time']);
            $this->view_field = D('Field')->get_view_field('market');
            foreach ($this->view_field as $val) {
                if ($val['form_type'] == 'datetime') {
                    if ($this->market_info[$val['field']] != 0) {
                        $this->market_info[$val['field']] = date('Y-m-d', $this->market_info[$val['field']]);
                    } else {
                        $this->market_info[$val['field']] = '--';
                    }
                }
            }

            $role_id_list = array();        // 员工信息列表
            $role_id_list[] = $this->market_info['creator_role_id'];       // 创建人
            $role_id_list[] = $this->market_info['owner_role_id'];     // 负责人

            $log_ids = M('RMarketLog')->where(array('market_id' => $market_id))->getField('log_id', true); 
            $log_list = M('log')->where(array('log_id' => array('IN', implode(',', $log_ids))))->field('log_id,role_id,create_date,content')->order('create_date')->select();
            foreach ($log_list as $key => $val) {
                $log_list[$key]['create_date'] = date('Y-m-d H:i:s', $val['create_date']);
                $log_list[$key]['del'] = $val['role_id'] == session('role_id') ? 1 : 0;  // 添加人可删除
                $role_id_list[] = $val['role_id'];
            }
            $this->market_info['log_list'] = $log_list;     // 日志列表
            $executor_str = trim($this->market_info['executor_role_ids'], ',');
            $this->market_info['executor_role_list'] = $executor_str ? explode(',', $executor_str) : array();        // 参与人列表
            $role_id_list = array_unique(array_merge($role_id_list, $this->market_info['executor_role_list']));
            $this->role_list = D('User')->get_full_name($role_id_list);     // 员工详情

            $file_id_array = M('RFileMarket')->where(array('market_id' => $market_id))->getField('file_id',true);
            $this->market_info['file_list'] = M('File')->where('file_id in (%s)',implode(',',$file_id_array))->select();          // 附件列表
            $d_file = D('File');
            foreach ($this->market_info['file_list'] as $key => $value) {
                $this->market_info['file_list'][$key]['size'] = ceil($value['size']/1024);
                $this->market_info['file_list'][$key]['pic'] = show_picture($value['name']);
                if ($value['oss'] == 1) {
                    $this->market_info['file_list'][$key]['file_path'] = $d_file::FILE_URL . '/' . $value['file_path'];
                }
            }
            $this->market_info['file_count'] = $file_id_array ? count($file_id_array) : '';     // 附件数量
        }
        return $this->market_info;
    }

 
    /**
     * 销售线索
     */
    public function get_leads()
    {
        $leads_ids = M('RMarketLeads')->where(array('market_id' => $this->market_id))->getField('leads_id', true);
        $leads = D('LeadsView')->where(array('leads_id' => array('IN', implode(',', $leads_ids))))->field('leads_id,name,owner_role_id,owner_role_full_name,create_time,contacts_name')->select();
        foreach ($leads as $key => $val) {
            $leads[$key]['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
        }
        return $leads;
    }

    /**
     * 相关客户
     */
    public function get_customer()
    {
        $customer_ids = M('RMarketCustomer')->where(array('market_id' => $this->market_id))->getField('customer_id', true);
        $customer = D('CustomerView')->where(array('customer_id' => array('IN', implode(',', $customer_ids))))->field('customer_id,name,create_time,owner_role_id,owner_role_full_name')->select();
        foreach ($customer as $key => $val) {
            $customer[$key]['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
        }
        return $customer;
    }

    
    /**
     * 获取用户参与活动列表
     */
    public function get_customer_market_list($customer_id)
    {
        $market_ids = M('RMarketCustomer')->where(array('customer_id' => $customer_id))->getField('market_id', true);
        return M('market')->where(array('market_id' => array('IN', implode(',', $market_ids)), 'is_deleted' => 0))->field('name,market_id')->select();
    }


    /**
     * 判断是否是负责人或者参与人或者管理员
     */
    public function check_auth($market_id = null) 
    {
        $role_id = session('role_id');
        if ($market_id) {
            $count = $this->where('market_id=' . $market_id . ' AND (owner_role_id=' . $role_id . ' OR executor_role_ids LIKE "%,' . $role_id . ',%")')->count();
            return ($count == 1 || session('?admin'));
        }
        return  ($role_id == $this->market_info['owner_role_id'] || in_array($role_id, $this->market_info['executor_role_list']) || session('?admin'));
    }


    private function sendMessage($to_role_ids, $content)
    {
        if (is_array($to_role_ids)) {
            foreach ($to_role_ids as $val) {
                sendMessage($val, $content, 1);
            }
        } else {
            sendMessage($to_role_ids, $content, 1);
        }
    }

    public function addSendMessage($to_role_ids)
    {
        $content = '<a class="role_info" rel="'. session('role_id') .'" href="javascript:void(0);">'. session('full_name') .'</a> 添加了市场活动 <a href="javascript:void(0);" class="market_view" rel="'. $this->market_id .'">'. $this->market_info['name'] .'</a>';
        if (is_array($to_role_ids)) {
            $content .= ' 指定你为参与人';
        } else {
            $content .= ' 指定你为负责人';
        }
        $this->sendMessage($to_role_ids, $content);
    }

    public function eidtSendMessage($old_role_ids, $new_role_ids)
    {
        $content = '<a class="role_info" rel="'. session('role_id') .'" href="javascript:void(0);">'. session('full_name') .'</a> 修改了市场活动 <a href="javascript:void(0);" class="market_view" rel="'. $this->market_id .'">'. $this->market_info['name'] .'</a>';
        if (is_array($old_role_ids) && is_array($new_role_ids)) {
            $this->sendMessage(array_diff($new_role_ids, $old_role_ids), $content .' 指定你为参与人');
            $this->sendMessage(array_diff($old_role_ids, $new_role_ids), $content .' 移除你为参与人');
        } else {
            $this->sendMessage($new_role_ids, $content .' 指定你为负责人');
            $this->sendMessage($old_role_ids, $content .' 移除你为负责人');
        }
    }

}
