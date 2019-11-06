<?php

class VisitorPlanModel extends Model
{
    // 状态：
    public $status_list = array(
        0 => array('name' => '未完成', 'color' => '#57C7D4'),
        1 => array('name' => '延期', 'color' => '#62A8EA'),
        2 => array('name' => '逾期', 'color' => '#F2A654'),
        3 => array('name' => '放弃', 'color' => '#F96868'),
        4 => array('name' => '完成', 'color' => '#46BE8A')
    );

    // 完成类型
    public $finish_type = array(
        'log' => '日志',
        'sign' => '签到'
    );

    public $msg = '';

    /**
     * 单条数据查询
     */
    public function getView($where)
    {
        $res = $this->where($where)->find();
        if (!$res) return array();
        if (in_array($res['status'], array(0, 1)) && $res['plan_time'] < strtotime(date('Y-m-d'))) {
            $this->where(array('id' => $res['id']))->setField('status', 2);
            $res['status'] = 2;
        }
        $status = $this->status_list[$res['status']];
        $res['status'] = $status['name'];
        $res['status_color'] = $status['color'];
        $res['plan_time'] = date('Y-m-d H:i', $res['plan_time']);
        $res['delay_time'] = $res['delay_time'] ? date('Y-m-d H:i', $res['delay_time']) : '-';
        return $res;
    }


    /**
     * 多条数据
     */
    public function getList($param)
    {
        if (isset($param['plan_time'])) {
            $where['plan_time'] = $param['plan_time'];
        }
        if (isset($param['delay_time'])) {
            $where['delay_time'] = $param['delay_time'];
        }
        if (isset($param['status'])) {
            $where['status'] = $param['status'];
        }
        $order = 'plan_time desc';
        if (isset($param['order'])) {
            $order = $param['order'];
        }
        if ($customer_id = $param['customer_id']) {
            $business_id = M('Business')->where(array('customer_id' => $param['customer_id']))->getField('business_id', true);
            $business_id = $business_id ? implode(',', $business_id) : '0';
            $remind_ids = M('Remind')->where(array('module' => 'customer', 'module_id' => $customer_id))->getField('remind_id', true);
            $remind_ids = $remind_ids ? implode(',', $remind_ids) : '0';
            $where_sql = '(module="customer" && module_id='. $customer_id .') or (module="remind" && module_id IN ('. $remind_ids .')) or ( module="business" && module_id IN ('. $business_id .') )';
            $event_ids = M('Event')->where($where_sql)->getField('event_id', true);
            $where['event_id'] = array('IN', $event_ids ?: '');
            $page = isset($param['page']) ? $param['page'] : '1, 10';
            $list = $this->where($where)->page($page)->order($order)->select();
        } else {
            if (isset($param['owner_role_id'])) {
                $where['owner_role_id'] = $param['owner_role_id'];
            }
            $list = D('VisitorPlanView')->where($where)->order($order)->select();
        }
        $d_user = D('User');
        $m_event = M('Event');
        $m_remind = M('Remind');
        $m_customer = M('Customer');
        $m_r_business_log = M('RBusinessLog');
        $m_business = M('Business');
        $m_r_customer_log = M('RCustomerLog');
        $m_contacts = M('Contacts');
        $v_role = D('RoleView');
        $m_sign_img = M('SignImg');
        foreach ($list as $key => $val) {
            if (in_array($val['status'], array(0, 1)) && $val['plan_time'] < strtotime(date('Y-m-d'))) {
                $this->where(array('id' => $val['id']))->setField('status', 2);
                $val['status'] = 2;
            }
            $status = $this->status_list[$val['status']];
            $list[$key]['status_id'] = $val['status'];
            $list[$key]['status'] = $status['name'];
            $list[$key]['status_color'] = $status['color'];
            $list[$key]['plan_time'] = date('Y-m-d H:i', $val['plan_time']);
            $list[$key]['delay_time'] = $val['delay_time'] ? date('Y-m-d H:i', $val['delay_time']) : '-';
            // 关联完成模块
            if ($val['module_id'] && $val['status'] == 4) {
                $list[$key]['finish_type_name'] = $this->finish_type[$val['module']];
            }
            if (isset($val['owner_role_id'])) {
                $temp_val = $d_user->get_full_name(array($val['owner_role_id']));
                $list[$key]['owner_role_info'] = $temp_val[$val['owner_role_id']];
            }
            if (!$customer_id) {
                if ($val['module'] == 'customer') {
                    $list[$key]['customer_id'] = $val['module_id'];
                } elseif ($val['module'] == 'remind') {
                    $temp_customer_id = $m_remind->where(array('remind_id' => $val['module_id']))->getField('module_id');
                    $list[$key]['customer_id'] = $temp_customer_id ?: 0;
                }
                $list[$key]['customer_name'] = $m_customer->where(array('customer_id' => $list[$key]['customer_id']))->getField('name') ?: '客户详情';
            }
            // 仅用作手机端
            if ($param['vue']){
                if ($val['status'] == 4 && $val['module_id']) {
                    if ($val['module'] == 'sign') {
                        $temp = M('Sign')->where(array('sign_id' => $val['module_id']))->find();
                        $temp['owner'] = $v_role->where('role.role_id = %d', $temp['role_id'])->field('user_name,role_id,thumb_path,role_name,department_name')->find();
                        $temp['owner']['thumb_path'] = headPathHandle($temp['owner']['thumb_path']);
                        $sign_img = $m_sign_img->where(array('sign_id' => $val['sign_id']))->getField('path',true);
                        foreach ($sign_img as $k => $v) {
                            $sign_img[$k] = headPathHandle($v, 1);
                        }
                        $temp['sign_img'] = $sign_img ? $sign_img : array();
                        $role_info = $d_user->get_full_name(array($val['role_id']));
                        $role_info = $role_info[$val['role_id']];
                        $temp['user_name'] = $role_info['full_name'];
                        $temp['user_img'] = $role_info['thumb_path'];
                    } elseif ($val['module'] == 'log') {
                        $temp = M('Log')->where(array('log_id' => $val['module_id']))->find();
                        $temp['owner'] = D('RoleView')->where('role.role_id = %d', $temp['role_id'])->field('user_name,role_id,thumb_path,role_name,department_name')->find();
                        $temp['owner']['thumb_path'] = headPathHandle($temp['owner']['thumb_path']);
                        if ($business_id = $m_r_business_log->where('log_id = %d', $temp['log_id'])->getField('business_id')) {
                            $business_name = $m_business->where('business_id = %d',$business_id)->getField('name');
                        }
                        $temp['business_name'] = $business_name ? $business_name : '';
                        $temp['business_id'] = $business_id ? $business_id : '';
                        if ($temp['business_id']) {
                            $contacts_id = $m_r_business_log->where('business_id = %d and log_id = %d', $temp['business_id'], $temp['log_id'])->getField('contacts_id');
                        } else {
                            $contacts_id = $m_r_customer_log->where('customer_id = %d and log_id = %d', $_POST['customer_id'], $temp['log_id'])->getField('contacts_id');
                        }
                        $contacts = $m_contacts->where('contacts_id = %d', (int) $contacts_id)->field('contacts_id,name as contacts_name,telephone as contacts_phone')->find();
                        $temp['contacts'] = $contacts ?: null;
                    }

                    $list[$key]['finish_data'] = $temp;
                } else {
                    $list[$key]['finish_data'] = null;
                }
            }
        }
        return $list ?: array();
    }

    /**
     * 完成计划
     * @param   int     id          visitor_plan_id
     * @param   string  module      完成模块 签到sign 日志log
     * @param   int     module_id   模块ID sign_id  log_id
     */
    public function finishPlan($param)
    {
        $where = array(
            'id' => $param['id'],
            'module' => '',
            'module_id' => '0'
        );
        $res = $this->where($where)->find();
        if (in_array($res['status'], array(0, 1))) {
            return $this->where($where)->save(array(
                'status' => 4,
                'module' => $param['module'],
                'module_id' => $param['module_id']
            ));
        } else {
            $this->msg = '该计划不可操作';
            return false;
        }
    }
}
