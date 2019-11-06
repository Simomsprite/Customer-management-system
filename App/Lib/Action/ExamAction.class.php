<?php 
class ExamAction extends Action {

	/**
	*  author ZengZhiQiang
	*  用于判断权限
	*  @permission 无限制
	*  @allow 登录用户可访问
	*  @other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('log_view', 'exam', 'log_list')
		);
		B('Authenticate', $action);
        $this->_permissionRes = getPerByAction(MODULE_NAME, ACTION_NAME);
        $this->alert = parseAlert();
        $this->d_exam = D('Exam');
	}


    /**
     * 审核模块列表
     * @author  shen
     */
    public function index()
    {
        $this->list = M('ExamType')->select();
        $this->display();
    }


    /**
     * 审核模块修改
     */
    public function edit()
    {
        // $res = $this->d_exam->editStatus();
    }


    /**
     * 审核模块状态修改
     * @author  shen
     */
    public function status_edit()
    {
        $type_id = (int) $_POST['type_id'];
        $status = (int) (bool) $_POST['status'];
        if ($status == 0) {
            // 审批流是否使用中
            if ($this->d_exam->processUsed(array('type_id' => $type_id))) {
                $this->ajaxReturn(array('msg' => '有审批流使用中，无法修改！', 'status' => 2));
            }
        }
        $res = $this->d_exam->editStatus($type_id, $status);
        $this->ajaxReturn(array('msg' => $this->d_exam->msg, 'status' => (int) $res));
    }


    /**
     * 审批流状态修改
     * @author  shen
     */
    public function process_status_edit()
    {
        $process_id = (int) $_POST['process_id'];
        $status = (int) (bool) $_POST['status'];
        if ($status == 0) {
            // 审批流是否使用中
            if ($this->d_exam->processUsed(array('process_id' => $process_id))) {
                $this->ajaxReturn(array('msg' => '有审批流使用中，无法修改！', 'status' => 2));
            }
        }
        $res = $this->d_exam->editProcessStatus($process_id, $status);
        $this->ajaxReturn(array('msg' => $this->d_exam->msg, 'status' => (int) $res));
    }


    /**
     * 模块审核详情(审批流列表)
     * @author  shen
     */
    public function view()
    {
        $type_id = (int) $_GET['id'];
        $this->info = M('ExamType')->where(array('type_id' => $type_id))->find();
        if ($this->info['status'] == 0) {
            alert('error', '该模块未启用审批流。', U('exam/index'));
        }
        $this->list = $this->d_exam->processList(array('type_id' => $type_id), true);
        $this->display();
    }


    /**
     * 审批流添加
     */
    public function process_add()
    {
        if (IS_POST) {
            $res = $this->d_exam->saveProcess('add');
            $this->ajaxReturn(array('msg' => $this->d_exam->msg, 'status' => (int) $res));
        } else {
            $this->type_id = (int) $_GET['id'];
            $this->display();
        }
    }


    /**
     * 审批流修改
     */
    public function process_edit()
    {
        if (IS_POST) {
            $res = $this->d_exam->saveProcess();
            $this->ajaxReturn(array('msg' => $this->d_exam->msg, 'status' => (int) $res));
        } else {
            $this->info = M('ExamProcess')->where(array('process_id' => (int) $_GET['id']))->find();
            $this->display();
        }
    }


    /**
     * 审批流删除
     * @author  shen
     */
    public function process_delete()
    {
        $process_id = (int) $_POST['process_id'];
        // 审批流是否使用中
        if ($this->d_exam->processUsed(array('process_id' => $process_id))) {
            $this->ajaxReturn(array('msg' => '有审批流使用中，无法删除！', 'status' => 2));
        }
        if ($res = $this->d_exam->deleteProcess(array('process_id' => $process_id))) {
            $res = $this->d_exam->deleteStep(array('process_id' => $process_id));
        }
        $this->ajaxReturn(array('msg' => $this->d_exam->msg, 'status' => (int) $res));
    }
    

    /**
     * 订单审批
     * @param r_exam 审批关系表pd_r_exam 数据
     * @param exam_log 审批日志表pd_exam_log 数据
     * update_exam_status 只有审批拒绝或“并”的情况下全部“同意”审批，才会更新审批状态
     * @author lee
     */
    public function exam()
    {
        $type_id = (int) $_REQUEST['type_id'];
        $order_id = (int) $_REQUEST['order_id'];
        $per_data = $this->d_exam->checkExamPermission($type_id, $order_id);
        if ($per_data['do_examine'] != 1) {
            echo '<div class="alert alert-danger">没有此权限！</div>';
            exit;
        }
        if (IS_POST) {
            $r_exam['type_id'] = $exam_log['type_id'] = $type_id;
            $r_exam['order_id'] = $exam_log['order_id'] = $order_id;
            $temp_val = D('Exam')->getOrderInfo(array('field' => 'owner_role_id', 'type_id' => $type_id, 'order_id' => $order_id));
            $order_owner_role_id = $temp_val['owner_role_id'];
            $r_type_table = array(
                1 => array('url' => 'purchase/view', 'id' => 'id'),    // 采购
                2 => array('url' => 'purchase/return_goods_view', 'id' => 'id'),       // 采购退货
                3 => array('url' => 'purchase/view', 'id' => 'id'),    // 销售退货
                4 => array('url' => 'stock/transfer_view', 'id' => 'transfer_id')     // 库存调拨
            );
            $type_name = M('ExamType')->where(array('type_id' => $type_id))->getField('name');
            if ($_POST['step_id']) {
                //审批流
                $r_exam['role_ids'] = trim($_POST['examine_role_id']);
            } else {
                //自选审批人
                $r_exam['role_ids'] = ','.intval($_POST['examine_role_id']).',';
            }
            $r_exam['step_id'] = (int) $_POST['next_step_id'];

            if ($_POST['is_agree'] == 1) {
                if ($_POST['examine_is_end'] == 1) {
                    //审批同意且结束审批
                    $r_exam['exam_status'] = 2;
                    unset($r_exam['role_ids']);
                    unset($r_exam['step_id']);
                    $exam_log['result'] = 2;

                    sendMessage($order_owner_role_id, '您负责的 <span>'. $type_name .'单</span>，已通过审批。<a href="'. U($r_type_table[$type_id]['url'], array($r_type_table[$type_id]['id'] => $order_id)) .'" target="_blank">查看详情</a>', 1);
                } else if ($_POST['examine_is_end'] == 2){
                    //审批同意且审批未结束
                    $r_exam['exam_status'] = 1;
                    $exam_log['result'] = 1;
                    $temp_val = array_filter(explode(',', $r_exam['role_ids']));
                    foreach ($temp_val as $val) {
                        sendMessage($val, '<span>'. $type_name .'单</span>需要您的审批。<a href="'. U($r_type_table[$type_id]['url'], array($r_type_table[$type_id]['id'] => $order_id)) .'" target="_blank">查看详情</a>', 1);
                    }
                }
                //判断是否审批“同意”的最后一步，需要更新审批状态
                $is_final = $this->d_exam->checkStepIsFinal((int) $_POST['type_id'], (int) $_POST['order_id'], (int) $_POST['step_id'], (int) $_POST['relation']);
                if ($is_final == 1 || !$_POST['step_id']) {
                    $update_exam_status = 1;
                }
                
            } elseif ($_POST['is_agree'] == 2) {
                //审批拒绝
                $r_exam['exam_status'] = 3;
                $exam_log['result'] = 0;
                $update_exam_status = 1;
                
                sendMessage($order_owner_role_id, '您负责的 <span>'. $type_name .'单</span>被<a href="javascript:void(0);" class="role_info" rel="'. session('role_id') .'">'. session('full_name') .'</a><span class="text-danger">驳回</span>。<a href="'. U($r_type_table[$type_id]['url'], array($r_type_table[$type_id]['id'] => $order_id)) .'" target="_blank">查看详情</a>', 1);
            }
            //记录本次审批结果及下次审批人和下次审批步骤
            if ($update_exam_status == 1) {
                $res = $this->d_exam->editOrderExam(array('type_id' => (int) $_POST['type_id'], 'order_id' => (int) $_POST['order_id']), $r_exam);
            } else {
                $res = true;
            }
            if ($res) {
                $exam_log['step_id'] = (int) $_POST['step_id'];
                $exam_log['role_id'] = session('role_id');
                $exam_log['remark'] = trim($_POST['remark']);
                //记录本次审批日志
                $log_id = $this->d_exam->addLog($exam_log);
                if ($log_id) {
                    //是否成功应收款
                    if (intval($_POST['is_receivables']) == 1) {
                        D('Finance')->addFinance($_POST);
                    }
                    alert('success','审批成功！', $_SERVER['HTTP_REFERER']);
                } else {
                    alert('error','审批失败！', $_SERVER['HTTP_REFERER']);
                }
            }
        } else {
            $exam_type = $this->d_exam->examType(array('type_id' => $type_id));
            if ($exam_type['status'] == 1) {
                $this->exam = $exam = $this->d_exam->orderExam(array('type_id' => $type_id, 'order_id' => $order_id));

                $exam_step_next = $this->d_exam->examStep(array('step_id' => $exam['step_id']), 1);
                $this->exam_step_next = $exam_step_next;
            }
            //是否开启审批流程
            $this->option = $exam_type['status'];

            //如果有下次审批步骤，说明本次审批流未结束，否则结束
            $this->exmine_is_end = $exam_step_next ? 0 : 1;

            $this->display();
        }
    }


    /**
     * 撤销审核
     * 只有管理员能撤销，已经审批结束（同意、拒绝）的“订单”
     */
    public function revoke()
    {
        if (!session('?admin')) {
            alert('error','没有此权限！', $_SERVER['HTTP_REFERER']);
        }
        $order_id = (int) $_GET['order_id'];
        $type_id = (int) $_GET['type_id'];

        $msg = '';
        if ($_REQUEST['type_id'] == 1) {
            if (M('StockIn')->where(array('type' => 1, 'type_id' => $order_id))->find()) {
                $msg = '产品已经入库，无法撤销';
            } elseif (M('Sales')->where(array('customer_id' => $order_id, 'type' => 1))->find()) {
                $msg = '存在管理采退单，无法撤销';
            }
        } elseif ($_REQUEST['type_id'] == 2) {
            if (M('StockOut')->where(array('type_id' => $order_id, 'type' => 2))->find()) {
                $msg = '产品已经出库，无法撤销';
            }
        } elseif ($_REQUEST['type_id'] == 3) {
            if (M('StockIn')->where(array('type' => 2, 'type_id' => $order_id))->find()) {
                $msg = '产品已经入库，无法撤销';
            }
        } elseif ($_REQUEST['type_id'] == 4) {
            if (M('StockIn')->where(array('type' => 3, 'type_id' => $order_id))->find()) {
                $msg = '产品已经入库，无法撤销';
            } else if (M('StockOut')->where(array('type_id' => $order_id, 'type' => 3))->find()) {
                $msg = '产品已经出库，无法撤销';
            }
        }
        
        if ($msg != '') {
            alert('error', $msg, $_SERVER['HTTP_REFERER']);
        }


        $first_step = $this->d_exam->examFirstStep($type_id, $order_id);
        $res = $this->d_exam->editOrderExam(array('type_id' => $type_id, 'order_id' => $order_id), array('exam_status' => 0, 'role_ids' => $first_step['role_ids'], 'step_id' => $first_step['step_id']));
        if ($res) {
            //记录本次审批日志
            $exam_log = array(
                'order_id' => $order_id,
                'type_id' => $type_id,
                'role_id' => session('role_id'),
                'result' => 3,
                'remark' => '撤销审批',
            );
            $log_id = $this->d_exam->addLog($exam_log);
            if ($log_id) {
                alert('success','审批已撤销！', $_SERVER['HTTP_REFERER']);
            } else {
                alert('error','审批日志添加失败！', $_SERVER['HTTP_REFERER']);
            }
        } else {
            alert('error','操作失败！', $_SERVER['HTTP_REFERER']);
        }
    }


    /**
     * 查询审批日志列表
     * @author lee
     */
    public function log_list(){
        $type_id = (int) $_GET['type_id'];
        $order_id = (int) $_GET['order_id'];
        $this->list = $this->d_exam->logList(array('type_id' => $type_id, 'order_id' => $order_id));
        $this->display();
    }


    /**
     * 显示“订单”详情的审批信息模版
     * @author lee
     */
    public function log_view(){
        $m_exam_step = M('ExamStep');
        $m_exam_log = M('ExamLog');
        $type_id = (int) $_GET['type_id'];
        $order_id = (int) $_GET['order_id'];
        $where = array('type_id' => $type_id, 'order_id' => $order_id);

        // 审核信息
        $exam_info = M('RExam')->where($where)->field('id, step_id, role_ids, exam_status')->find();
        $exam_log = array();
        // 是否采用审批流
        if ($exam_info['step_id']) {
            $process_id = $m_exam_step->where(array('step_id' => $exam_info['step_id']))->getField('process_id');
            $step_list = $this->d_exam->processStep(array('process_id' => $process_id));
            foreach ($step_list as $key => $val) {
                if ($exam_info['step_id'] == $val['step_id'] && $exam_info['exam_status'] != 2) {
                    break;
                }
                
                $exam_log[$val['step'] - 1]['relation'] = $val['relation'];         // 本步骤审批逻辑
                foreach ($val['role_list'] as $k => $v) {
                    $where_log = array('role_id' => $v['role_id'], 'step_id' => $val['step_id']);
                    $where_log['type'] = $type_id;
                    $where_log['order_id'] = $order_id;
                    $where_log['log_id'] = array('gt', $this->d_exam->maxLogId($type_id, $order_id));
                    $v['result'] = $m_exam_log->where($where_log)->getField('result');
                    $exam_log[$val['step'] - 1]['role_list'][] = $v;
                }
            }

        } else {
            // 本轮审核日志列表
            $log_list = $this->d_exam->logList($where, true);
            foreach ($log_list as $key => $val) {
                $exam_log[] = array(
                    'relation' => 1,
                    'role_list' => array($val)
                );
            }
        }

        if (in_array($exam_info['exam_status'], array(0, 1))) {
            // 待审和审批中 下一步待审人员
            $role_list = D('User')->get_full_name(array_filter(explode(',', $exam_info['role_ids'])));
            foreach ($role_list as $key => $val) {
                $role_list[$key]['result'] = 4;
            }
            $exam_log[] = array(
                'relation' => 1,
                'role_list' => $role_list
            );
        }       
        $this->exam_log = $exam_log;
        $this->type_id = $type_id;
        $this->order_id = $order_id;
        $this->display();
    }



    /**
     * 审批流步骤
     * @author  shen
     */
    public function step()
    {
        if ($this->d_exam->processUsed(array('process_id' => $_GET['process_id']))) {
            echo '<div class="alert alert-danger">流程使用中无法修改！</div>';
            exit;
        }
        $this->process_id = (int) $_GET['process_id'];
        if ($this->process_id == 0) {
            echo '<div class="alert alert-danger">参数错误！</div>';
            exit;
        }
        $this->list = $this->d_exam->processStep(array('process_id' => $this->process_id));
        $this->display();
    }

    
    /**
     * 步骤修改
     */
    public function step_edit()
    {
        if (IS_POST) {
            $res = $this->d_exam->editStep();
            $this->ajaxReturn(array('msg' => $this->d_exam->msg, 'status' => (int) $res));
        }
    }


    /**
     * 步骤删除/审批流步骤清空
     */
    public function step_delete()
    {
        $where = array();
        if (!empty($_POST['step_id'])) {
            if ($this->d_exam->processUsed(array('step_id' => $_POST['step_id']))) {
                $this->ajaxReturn(array('msg' => '有审批流使用中，无法删除！', 'status' => 2));
            }
            $where['step_id'] = (int) $_POST['step_id'];
        } elseif (!empty($_POST['process_id'])) {
            if ($this->d_exam->processUsed(array('process_id' => $_POST['process_id']))) {
                $this->ajaxReturn(array('msg' => '有审批流使用中，无法删除！', 'status' => 2));
            }
            $where['process_id'] = (int) $_POST['process_id'];
        }
        $res = $this->d_exam->deleteStep($where);
        $this->ajaxReturn(array('msg' => $this->d_exam->msg, 'status' => (int) $res));
    }


    /**
     * 步骤添加
     */
    public function step_add()
    {
        $res = $this->d_exam->addStep();
        $this->ajaxReturn(array('data' => array('step_id' => $res), 'msg' => $this->d_exam->msg, 'status' => (int) (bool) $res));
    }


    /**
     * 步骤排序
     */
    public function step_order()
    {
        if (!empty($_POST['step_ids'])) {
            $res = $this->d_exam->stepOrder(array('step_ids' => $_POST['step_ids']));
            $this->ajaxReturn(array('msg' => $this->d_exam->msg, 'status' => (int) $res));
        } else {
            $this->ajaxReturn(array('msg' => '参数错误', 'status' => 0));
        }
    }
}
