<?php

class ExamModel extends Model
{
    public $msg;


    /**
     * 查询审批流列表
     * @param   int     $type_id    模块ID
     * @param   bool    $step       步骤详情
     * @author  shen
     */
    public function processList($where, $step = null)
    {
        $list = M('ExamProcess')->where($where)->select();
        if ($step === true) {
            foreach ($list as $key => $val) {
                $list[$key]['step'] = $this->processStep(array('process_id' => $val['process_id']));
            }
        }
        return $list;
    }


    /**
     * 查询单条审批流所有步骤
     * @param   int     $process_id 
     * @author  shen
     */
    public function processStep($where)
    {
        if (!empty($where['process_id'])) {
            $process_id = (int) $where['process_id'];
        } elseif (!empty($where['step_id'])) {
            $process_id = (int) M('ExamStep')->where(array('step_id' => $where['step_id']))->getField('process_id');
        }
        if ($process_id == 0) {
            $this->msg = '参数异常';
            return false;
        }

        $d_user = D('User');
        $list = M('ExamStep')->where(array('process_id' => $process_id))->order('step asc')->select();
        $role_id_list = array_unique(array_filter(explode(',', implode(',', y_array_column($list, 'role_ids')))));
        $role_info_arr = $d_user->get_full_name($role_id_list);

        foreach ($list as $key => $val) {
            $list[$key]['role_list'] = array();
            $role_ids = trim($val['role_ids'], ',');
            if ($role_ids != '') {
                foreach (explode(',', $role_ids) as $role_id) {
                    $list[$key]['role_list'][] = $role_info_arr[$role_id];
                }
            }
        }

        return $list;
    }


    /**
     * 创建订单审批信息
     * @author lee
     */
    public function addOrderExam($data)
    {
        $m_r_exam = M('RExam');
        if ($m_r_exam->create($data)) {
            $id = $m_r_exam->add();
            if ($id) {
                $r_type_table = array(
                    1 => array('url' => 'purchase/view', 'id' => 'id'),    // 采购
                    2 => array('url' => 'purchase/return_goods_view', 'id' => 'id'),       // 采购退货
                    3 => array('url' => 'purchase/view', 'id' => 'id'),    // 销售退货
                    4 => array('url' => 'stock/transfer_view', 'id' => 'transfer_id')     // 库存调拨
                );
                $type_id = $data['type_id'];
                $order_id = $data['order_id'];
                $type_name = M('ExamType')->where(array('type_id' => $type_id))->getField('name');
                $temp_val = array_filter(explode(',', $data['role_ids']));
                foreach ($temp_val as $val) {
                    sendMessage($val, '<span>'. $type_name .'单<span>需要您的审批。<a href="'. U($r_type_table[$type_id]['url'], array($r_type_table[$type_id]['id'] => $order_id)) .'" target="_blank">查看详情</a>', 1);
                }
                $this->msg = '审批成功';
                return $id;
            } else {
                $this->msg = '审批失败';
                return false; 
            }
        } else {
            $this->msg = '数据对象创建失败';
            return false;
        }
    }


    /**
     * 修改订单审批信息
     * @author lee
     */
    public function editOrderExam($where, $data)
    {
        $m_r_exam = M('RExam');
        if ($m_r_exam->create($data)) {
            $m = $m_r_exam->where($where)->save();
            if ($m !== false) {
                $this->msg = '审批成功';
                return true;
            } else {
                $this->msg = '审批失败';
                return false; 
            }
        } else {
            $this->msg = '数据对象创建失败';
            return false;
        }
    }


    /**
     * 查询订单审批记录
     * @param   range   是否只展示本轮次记录，默认是空，查询条件下全部的日志，ture时只查询最新轮次的审批日志
     * @author lee
     */
    public function logList($where, $range = null)
    {
        $d_user = D('User');
        if ($range) {
            $max_log_id = $this->maxLogId($where['type_id'], $where['order_id']);
            if ($max_log_id) $where['log_id'] = array('gt', $max_log_id);
        }
        $list = M('ExamLog')->where($where)->field('create_time, result, role_id, remark')->select();
        foreach ($list as $k => $v) {
            $role_info = $d_user->get_full_name(array($v['role_id']));
            $list[$k] += $role_info[$v['role_id']];
        }
        return $list;
    }


    /**
     * 查询订单单个审批记录信息
     * @author lee
     */
    public function logDetail($where)
    {
        return M('ExamLog')->where($where)->find();
    }


    /**
     * 添加审批记录
     * @author lee
     */
    public function addLog($data)
    {
        $m_exam_log = M('ExamLog');
        if ($m_exam_log->create($data)) {
            $m_exam_log->create_time = time();
            $log_id = $m_exam_log->add();
            if ($log_id) {
                $this->msg = '审批日志记录成功';
                return $log_id;
            } else {
                $this->msg = '审批日志记录失败';
                return false; 
            }
        } else {
            $this->msg = '数据对象创建失败';
            return false;
        }
    }

    /**
     * 当审批有驳回或撤销时，审批会从第一步重新开始，需要找出上一次重新审批记录的最大log_id
     * 若无则返回0，即为第一轮
     * @author lee
     */
    public function maxLogId($type_id, $order_id){
        return (int) M('ExamLog')->where(array('type_id' => $type_id, 'order_id' => $order_id, 'result' => array(array('eq', 0), array('eq', 3), 'or')))->max('log_id');
    }


    /**
     * 修改审批模块(暂时不用)
     * @author  shen
     */
    public function edit()
    {
        $m_exam_type = M('ExamType');
        if ($m_exam_type->create()) {
            if ($m_exam_type->save()) {
                $this->msg = '修改成功！';
                return true;
            } else {
                $this->msg = '修改失败！';
            }
        } else {
            $this->msg = '数据对象创建失败[ExamType]';
        }
        return false;
    }


    /**
     * 模块状态修改(是否启用审批流)
     * @param   int     $type_id        模块ID
     * @param   int     $status         状态值 1 or 0
     * @author  shen
     */
    public function editStatus($type_id, $status)
    {
        $m_exam_type = M('ExamType');
        if ($m_exam_type->create(array('type_id' => $type_id, 'status' => $status, 'update_time' => time()))) {
            if ($m_exam_type->save()) {
                $this->msg = '修改成功！';
                return true;
            } else {
                $this->msg = '修改失败，刷新后重试。';
            }
        } else {
            $this->msg = '数据对象创建失败！[ExamType]';
        }
        return false;
    }


    /**
     * 审批流状态修改
     * @param   int     $process_id         审批流ID
     * @param   int     $status             状态值 1 or 0
     * @author  shen
     */
    public function editProcessStatus($process_id, $status)
    {
        $m_exam_process = M('ExamProcess');
        if ($m_exam_process->create(array('process_id' => $process_id, 'status' => $status, 'update_time' => time()))) {
            if ($m_exam_process->save()) {
                $this->msg = '修改成功！';
                return true;
            } else {
                $this->msg = '修改失败，刷新后重试。';
            }
        } else {
            $this->msg = '数据对象创建失败！[ExamProcess]';
        }
        return false;
    }


    /**
     * 审批流是否使用中
     * @param   array   $where [type_id, process_id step_id] 模块ID或审批流ID步骤ID
     * @author  shen
     */
    public function processUsed($where)
    {
        $m_r_exam = M('RExam');
        $m_exam_step = M('ExamStep');
        $step_where = array('exam_status' => array('IN', array(0, 1)));
        if (!empty($where['type_id'])) {
            $process_ids = M('ExamProcess')->where(array('type_id' => $where['type_id']))->getField('process_id', true);
            $step_id_list = $m_exam_step->where(array('process_id' => array('IN', $process_ids)))->getField('step_id', true);
            $step_where['step_id'] = array('IN', $step_id_list);
        } elseif (!empty($where['process_id'])) {
            $step_id_list = $m_exam_step->where(array('process_id' => $where['process_id']))->getField('step_id', true);
            $step_where['step_id'] = array('IN', $step_id_list);
        } elseif (!empty($where['step_id'])) {
            $step_where['step_id'] = $where['step_id'];
        }
        return (bool) $m_r_exam->where($step_where)->count();
    }

    
    /**
     * 获取审批类型设置信息
     * @author lee
     */
    public function examType($where)
    {
        return M('ExamType')->where($where)->find();
    }


    /**
     * 获取pd_r_exam 记录的信息 
     * 即获取某个“订单”的审核状态和下一步审批步骤等信息
     * @author lee
     */
    public function orderExam($where)
    {   
        $d_user = D('User');
        $exam = M('RExam')->where($where)->find();

        $role_ids = array_filter(explode(',', $exam['role_ids']));
        foreach ($role_ids as $k => $v) {
            $role_info = $d_user->get_full_name(array($v));
            $exam['role_list'][] = $role_info[$v];
        }

        $exam_step = $this->examStep(array('step_id' => $exam['step_id'])) ?: array();
        $exam = array_merge($exam, $exam_step);
        return $exam;
    }


    /**
     * 获取该步骤的审批信息
     * @param next 默认为空，1表示查询下一审批人的信息
     * @author lee
     */
    public function examStep($where, $next = '')
    {
        $d_user = D('User');
        $d_exam_step = M('ExamStep');
        $exam_step = $d_exam_step->where($where)->find();
        if ($next == 1) {
            $exam_step = $d_exam_step->where(array('process_id' => $exam_step['process_id'], 'step' => $exam_step['step'] + 1))->find();
        }
        if ($exam_step) {
            //$exam_step['max_step'] = $d_exam_step->where('process_id = %d', $exam_step['process_id'])->max('step');
            $exam_step['relation_name'] = $exam_step['relation'] == 1 ? '并' : '或';

            $role_ids = array_filter(explode(',', $exam_step['role_ids']));
            foreach ($role_ids as $k => $v) {
                $role_info = $d_user->get_full_name(array($v));
                $exam_step['next_role_list'][] = $role_info[$v];
            }

            return $exam_step;
        } else {
            return false;
        }
    }


    /**
     * 获取第一步的审批信息
     * 1 查询出第一条审批日志，如果有多轮次，则根据最大log_id 查询出最新一轮的第一步
     * 2 根据step_id是否为0，判断是审批流审批还是自定义审批人审批
     * @author lee
     */
    public function examFirstStep($type_id, $order_id){
        $where['log_id'] = array('gt', $this->maxLogId($type_id, $order_id)); //无多轮次，则默认大于0
        $where['type_id'] = $type_id;
        $where['order_id'] = $order_id;
        $first_log = M('ExamLog')->where($where)->order('log_id asc')->limit(1)->find();
        if ($first_log['step_id'] > 0) {
            //说明是审批流
            $process_id = M('ExamStep')->where('step_id = %d', $first_log['step_id'])->getField('process_id');
            $first_step = $this->examStep(array('process_id' => $process_id, 'step' => 1));
        } else {
            //自定义审批人
            $first_step['role_ids'] = ','.$first_log['role_id'].',';
            $first_step['step_id'] = 0;
        }
        return $first_step;
    }


    /**
     * 判断该次审批是否该步骤的最后一步
     * 当审批人员关系是“并”，审批结果是“拒绝”时，就直接拒绝了
     * 当审批人员关系是“并”且审批结果是“同意”时，通过审批记录判断相关人员审批情况，来判断本步骤最终审批是否“通过”
     * 没有"轮次"字段(撤销后会重新从第一步开始审批)，所以先以 type_id order_id result=0或3为条件（前提是有过“撤销”操作），查询出最大的log_id,再以 type_id order_id log_id大于最大log_id值为条件，查询判断是否当前步骤所有人员都已经“审批”过
     * @param type_id
     * @param order_id
     * @param step_id 本次审批步骤ID
     * @author lee
     */
    public function checkStepIsFinal($type_id, $order_id, $step_id, $relation)
    {   
        if ($relation == 2) {
            return true;
        }
        $m_exam_log = M('ExamLog');
        $max_log_id = $this->maxLogId($type_id, $order_id);
        if ($max_log_id) {
            $where['log_id'] = array('gt', $max_log_id);
        }
        $where['type_id'] = $type_id;
        $where['order_id'] = $order_id;
        $where['step_id'] = $step_id;
        $log_role_count = $m_exam_log->where($where)->count() ?: 0;

        $step_role_count = count(array_filter(explode(',', M('ExamStep')->where('step_id = %d', $step_id)->getField('role_ids'))));

        if (($step_role_count - $log_role_count) == 1) {
            return true;
        }
        return false;
    }


    /**
    * 判断“订单”的是否审批和撤销权限
    * 
    * @author lee
    **/
    public function checkExamPermission($type_id, $order_id){
        $exam = $this->orderExam(array('type_id' => $type_id, 'order_id' => $order_id));

        if ($exam['exam_status'] == 0 || $exam['exam_status'] == 1) {
            if (strpos($exam['role_ids'], ','.session('role_id').',') !== false) {
                //step_id = 0,自选审批人，非审批流
                if ($exam['step_id'] > 0) {
                    //本订单、本人、本轮次、本步骤，已有审批记录则没有审批权限
                    $max_log_id = $this->maxLogId($type_id, $order_id);
                    if ($max_log_id) {
                        $where['log_id'] = array('gt', $max_log_id);
                    }
                    $where['type_id'] = $type_id;
                    $where['order_id'] = $order_id;
                    $where['step_id'] = M('RExam')->where(array('type_id' => $type_id, 'order_id' => $order_id))->getField('step_id');
                    $where['role_id'] = session('role_id');
                    if (!M('ExamLog')->where($where)->find()) {
                        //过滤“并”关系，已审核过的用户的审核权限
                        $data['do_examine'] = 1;
                    }
                } else {
                    $data['do_examine'] = 1;
                } 
            }
        }
        //1审批中 2已通过
        if ($exam['exam_status'] == 1 || $exam['exam_status'] == 2){
            if (session('?admin')) {
                $data['do_revoke'] = 1;
            }
        }
        $data['exam_status'] = $exam['exam_status'];
        return $data;
    }


    /**
     * 修改步骤
     * @author shen
     */
    public function editStep()
    {
        if ($this->processUsed(array('step_id' => $_POST['step_id']))) {
            $this->msg = '流程使用中无法修改';
            return false;
        }
        $m_exam_step = M('ExamStep');
        if ($m_exam_step->create()) {
            if ($m_exam_step->save()) {
                $this->msg = '修改成功';
                return true;
            } else {
                $this->msg = '修改失败，刷新后重试。';
            }
        } else {
            $this->msg = '数据对象创建失败！[ExamStep]';
        }
        return false;
    }


    /**
     * 删除步骤
     * @author  shen
     */
    public function deleteStep($where)
    {
        // 删除空审批流
        $m_exam_step = M('ExamStep');
        if ($m_exam_step->where($where)->count() == 0) {
            $this->msg = '操作成功';
            return true;
        }
        if (!empty($where['step_id'])) {
            $process_id = $m_exam_step->where($where)->getField('process_id');
        }
        if ($res = $m_exam_step->where($where)->delete()) {
            if (!empty($where['step_id'])) {
                $this->stepOrder(array('process_id' => $process_id));    // 重新排序
            }
            $this->msg = '操作成功';
        } else {
            $this->msg = '操作失败';
        }
        return (bool) $res;
    }


    /**
     * 删除流程
     * @author  shen
     */
    public function deleteProcess($where)
    {
        if ($res = M('ExamProcess')->where($where)->delete()) {
            $this->msg = '操作成功';
        } else {
            $this->msg = '操作失败';
        }
        return (bool) $res;
    }


    /**
     * 添加/修改 流程
     * @author  shen
     */
    public function saveProcess($action = 'save')
    {
        $data = $_POST;
        $data['update_time'] = time();
        $m_exam_process = M('ExamProcess');
        if ($m_exam_process->create($data)) {
            if ($m_exam_process->$action()) {
                $this->msg = '操作成功！';
                return true;
            } else {
                $this->msg = '操作失败，刷新后重试。';
            }
        } else {
            $this->msg = '数据对象创建失败！[ExamProcess]';
        }
        return false;
    }


    /**
     * 添加步骤
     * @author shen
     */
    public function addStep($data = null)
    {
        if ($data == null) {
            $data = $_POST;
        }
        $m_exam_step = M('ExamStep');
        $data['relation'] = 1;  // 步骤逻辑关系默认1（并）
        $data['step'] = $m_exam_step->where(array('process_id' => $data['process_id']))->count() + 1;
        if ($m_exam_step->create($data)) {
            if ($step_id = $m_exam_step->add()) {
                $this->msg = '操作成功！';
                return $step_id;
            } else {
                $this->msg = '操作失败！';
            }
        } else {
            $this->msg = '数据对象创建失败！[ExamStep]';
        }
        return false;
    }


    /**
     * 步骤排序
     * @author shen
     */
    public function stepOrder($where)
    {
        $m_exam_step = M('ExamStep');
        if (!empty($where['step_ids'])) {
            $step_id_list = array_filter(explode(',', $where['step_ids']));
        } else {
            if (!empty($where['process_id'])) {
                $process_id = $where['process_id'];
            } else {
                $this->msg = '参数错误';
                return false;
            }
            $step_id_list = $m_exam_step->where(array('process_id' => $process_id))->order('step asc')->getField('step_id', true);
        }
        if (empty($step_id_list)) {
            $this->msg = '参数错误';
            return false;
        }
        foreach ($step_id_list as $key => $val) {
            $step = $key + 1;
            $m_exam_step->where(array('step_id' => $val))->save(array('step' => $step));
        }
        $this->msg = '操作成功';
        return true;
    }


    /**
     * 添加订单时，需要初始化保存审批信息的数据
     * @param process_status 审批开启状态，1开启，2禁用
     * @param temp_id 当process_status=1时值为process_id，2时值为role_id
     * @author lee
     */
    public function examInitData($process_status, $temp_id, $order_id, $type_id){
        if ($process_status == 1) {
            $data = M('ExamStep')->where(array('process_id' => $temp_id, 'step' => 1))->field('role_ids,step_id')->find();
        } else {
            $data['role_ids'] = ','.$temp_id.',';
        }
        $data['exam_status'] = 0;
        return $data;
    }

    

    /**
     * 查询订单是否已审核
     * @author shen
     */
    public function orderStatus($where)
    {
        $type_id = $where['type_id'];
        $order_id = $where['order_id'];
        return (int) M('RExam')->where(array('type_id' => $type_id, 'order_id' => $order_id, 'status' => 2))->getField('exam_status');
    }


    /**
     * “订单”添加或编辑时，事先需要获取的审批相关信息
     * @param type_id  类型ID
     * @param order_id 相关“订单”ID，有传值是-编辑，无传值是-添加
     * @param process_status 1开始审批流程 2未开启
     * @param process_list 当前模块设置的可用审批流 
     * @param order_exam 如果编辑，则查询初始化的审批信息，如果是添加，则此时没有order_id 
     * @author lee
     */
    public function initData($type_id, $order_id = false){
        $m_exam_step = M('ExamStep');
        $data['process_status'] = M('ExamType')->where('type_id = %d', $type_id)->getField('status');
        if ($data['process_status'] == 1) {
            $process_list = M('ExamProcess')->where(array('type_id' => $type_id, 'status' => 1))->select();
            foreach ($process_list as $k => $v) {
                if (!$step = $m_exam_step->where('process_id = %d', $v['process_id'])->select()) {
                    //过滤没有设置步骤的审批流
                    unset($process_list[$k]);
                    continue;
                }
                // 可能用不上，以防万一。
                foreach ($step as $step_temp) {
                    if (trim($step_temp['role_ids'], ',') == '') {
                        unset($process_list[$k]);
                        break;
                    }
                }
            }
            $data['process_list'] = $process_list;
        }
        if ($order_id) {
            $data['order_exam'] = D('Exam')->orderExam(array('type_id' => $type_id, 'order_id' => $order_id));
        }
        return $data;
    }


    /**
     * 获取订单信息
     * @param   int     $order_id   订单ID
     * @param   int     $type_id    模块ID
     * @param   string  $field      查询字段 默认 *
     */
    public function getOrderInfo($param)
    {
        $r_type_table = array(
            1 => 'purchase',    // 采购
            2 => 'sales',       // 采购退货
            3 => 'purchase',    // 销售退货
            4 => 'transfer'     // 库存调拨
        );
        $field = $param['field'] ?: '*';
        $order_id = $param['order_id'];
        $type_id = $param['type_id'];
        $table = $r_type_table[$type_id];
        return M($table)->where(array($table . '_id' => $order_id))->field($field)->find();
    }


    /**
     * 获取相关“订单”ID的集合
     * @param array $where 示例 ['type_id' => 1, 'exam_status' => 0] 
     */
    public function getOrderIds($where)
    {   
        return M('RExam')->where($where)->getField('order_id', true);
    }



} 
