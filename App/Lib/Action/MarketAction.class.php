<?php

/**
 * 市场活动
 * @author Ymob@qq.com
 */
class MarketAction extends Action
{
    /**
     * 用于判断权限
     * @permission 无限制
     * @allow 登录用户可访问
     * @other 其他根据系统设置
     */
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index', 'add', 'view', 'delete', 'customer_market_list', 'customer', 'add_log', 'customer_market_list')
		);
        B('Authenticate', $action);
    }
    
    /**
     * 列表
     */
    public function index()
    {
        if (!checkPerByAction('market', 'index')) {
            alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
        }
        if ((int) $_GET['v_market_id'] != 0) {
            $this->v_market_id = $_GET['v_market_id'];      // 详情弹出
        }
        $field_list = getMainFields('market');
        $field_list[] = array('field' => 'owner_role_id', 'name' => '负责人', 'form_type' => 'role');
        $field_list[] = array('field' => 'creator_role_id', 'name' => '创建人', 'form_type' => 'role');
        $field_list[] = array('field' => 'executor_role_ids', 'name' => '参与人', 'form_type' => 'role_s');
        $field_list[] = array('field' => 'create_time', 'name' => '创建时间', 'form_type' => 'datetime');
        $field_list[] = array('field' => 'update_time', 'name' => '修改时间', 'form_type' => 'datetime');
        $field_list[] = array('field' => 'is_lock', 'name' => '锁定', 'form_type' => 'box');
        $field_list[] = array('field' => 'is_deleted', 'name' => '作废', 'form_type' => 'box');
        $where = array();
        $auth_role_id = getPerByAction('market','index');
        // 高级搜索  or  普通搜索
        if ($_GET['search'] == 'search') {
            //多选类型字段
		    $check_field_arr = M('Fields')->where(array('model' => 'market', 'is_main' => 1, 'form_type' => 'box', 'setting' => array('like','%'."'type'=>'checkbox'".'%')))->getField('field',true);
            foreach ($field_list as $key => $val) {
                if (!empty($_GET[$val['field']])) {      
                    if ($val['form_type'] == 'address') {
                        foreach (array('state', 'city', 'area', 'value') as $address_v) {
                            $address_where[] = '%' . $_GET[$val['field']][$address_v] . '%';
                        }
                        if($_GET[$val['field']]['condition'] == 'not_contain'){
                            $where[$val['field']] = array('notlike', $address_where, 'OR');
                        }else{
                            $where[$val['field']] = array('like', $address_where, 'AND');
                        }
                    } else if ($val['field'] == 'executor_role_ids') {
                        $_GET['executor_role_ids']['value'] = trim($_GET['executor_role_ids']['value'], ',');
                        $executor_role_list = explode(',', $_GET['executor_role_ids']['value']);
                        if (!empty($executor_role_list)) {
                            $executor_role_info_list = D('User')->get_full_name($executor_role_list);
                            $full_name_str = implode(y_array_column($executor_role_info_list, 'full_name'), ',');
                            $this->full_name_str = $full_name_str;
                            foreach ($executor_role_list as $v) {
                                $executor_where[] = '%,' . $v . ',%';
                            }
                            if ($_GET['executor_role_ids']['condition'] == 'contains') {
                                $where[$val['field']] = array('like', $executor_where, 'AND');
                            } else if ($_GET['executor_role_ids']['condition'] == 'not_contain') {
                                $where[$val['field']] = array('notlike', $executor_where, 'AND');
                            }
                        }
                    } else if ($val['form_type'] == 'datetime') {
                        if ($_GET[$val['field']]['start'] && $_GET[$val['field']]['end']) {
                            $where[$val['field']] = array('between',array(strtotime($_GET[$val['field']]['start']),strtotime($_GET[$val['field']]['end'])+86399));
                        } else if ($_GET[$val['field']]['start']) {
                            $where[$val['field']] = array('egt',strtotime($_GET[$val['field']]['start']));
                        } else if ($_GET[$val['field']]['end']) {
                            $where[$val['field']] = array('elt',strtotime($_GET[$val['field']]['end'])+86399);
                        }
                    } else if ($val['form_type'] == 'role' || $val['form_type'] == 'role') {
                        if(!empty($_GET[$val['field']]['value'])){
                            $where[$val['field']] = field($_GET[$val['field']]['value'], $_GET[$val['field']]['condition']);
                        }
                    } else if ($_GET[$val['field']]['value'] != '') {
                        if ($val['form_type'] == 'number') $_GET[$val['field']]['value'] = (int) $_GET[$val['field']]['value'];
                        if ($val['form_type'] == 'floatnumber') $_GET[$val['field']]['value'] = (float) $_GET[$val['field']]['value'];
                        if (isset($_GET[$val['field']]['condition'])) {
                            $where[$val['field']] = field($_GET[$val['field']]['value'], $_GET[$val['field']]['condition']);
                        } else {
                            if (in_array($val['field'], $check_field_arr)) {
                                $where[$val['field']] = field($_GET[$val['field']]['value'], 'contains');
                            } else {
                                $where[$val['field']] = $_GET[$val['field']]['value'];
                            }
                        }
                    } else if (in_array($_GET[$val['field']]['condition'], array('is_empty', 'is_not_empty'))) {
                        $where[$val['field']] = field($_GET[$val['field']]['value'], $_GET[$val['field']]['condition']);
                    }
                }
                foreach ($_GET[$val['field']] as $kk => $vv) {
                    $params[] = $val['field'] . '[' . $kk . ']=' . $vv;
                }
            }
            if (isset($_GET['owner_role_id'])) {
                if (!in_array($_GET['owner_role_id']['value'], $auth_role_id)) {       
                    $where['owner_role_id'] = array('IN', explode(',', $auth_role_id));
                }
            }
            if (isset($where['executor_role_ids'])) {
                if ($where['executor_role_ids'][0] == 'like') {
                    foreach ($executor_role_list as $executor_role_id) {
                        if (!in_array($executor_role_id, $auth_role_id)) {
                            $temp_arr = array();
                            foreach ($auth_role_id as $auth_role_id_val) {
                                $temp_arr[] = '%,' . $auth_role_id_val . ',%';
                            }
                            $where['executor_role_ids'][1] = $temp_arr;
                            break;
                        }
                    }
                }
            }
            $params[] = 'search=search';
        } else if ($_GET['search'] == 'name') {
            if ($_GET['status']) {
                $where['status'] = $_GET['status'];
                $params[] = 'status=' . $_GET['status'];
            }
            if ($_GET['name']) {
                $where['name'] = array('LIKE', '%' . $_GET['name'] . '%');
                $params[] = 'name=' . $_GET['name'];
            }
            $params[] = 'search=name';
        }
        if (!isset($where['executor_role_ids']) && !isset($where['owner_role_id'])) {
            $temp_where['_logic'] = 'OR';
            $temp_arr = array();
            foreach ($auth_role_id as $auth_role_id_val) {
                $temp_arr[] = '%,' . $auth_role_id_val . ',%';
            }
            $temp_where['executor_role_ids'] = array('LIKE', $temp_arr, 'OR');
            $temp_where['owner_role_id'] = array('IN', implode(',', $auth_role_id));
            $where['_complex'] = $temp_where;
        }
        $fields_search = array();

        //高级搜索字段
        foreach($field_list as $k=>$v){
            $fields_data_list[$v['field']] = $v['form_type'];
        }
        foreach($params as $k=>$v){
            if(strpos($v,'[condition]=') || strpos($v,'[value]=') || strpos($v,'[state]=') || strpos($v,'[city]=') || strpos($v,'[area]=') || strpos($v,'[start]=') || strpos($v,'[end]=')){
                $field = explode('[',$v);
                if(strpos($field[0],'.')){
                    $ex_field = explode('.',$field[0]);
                    $field[0] = $ex_field[1];
                }
                if(strpos($v,'[condition]=')){
                    $condition = explode('=',$v);
                    $fields_search[$field[0]]['field'] = $field[0];
                    $fields_search[$field[0]]['condition'] = $condition[1];
                } elseif (strpos($v,'[state]=')) {
                    $state = explode('=',$field[1]);
                    $fields_search[$field[0]]['state'] = $state[1];
                } elseif (strpos($v,'[city]=')) {
                    $city = explode('=',$field[1]);
                    $fields_search[$field[0]]['city'] = $city[1];
                } elseif (strpos($v,'[area]=')) {
                    $area = explode('=',$field[1]);
                    $fields_search[$field[0]]['area'] = $area[1];
                } elseif (strpos($v,'[start]=')) {
                    $start = explode('=',$field[1]);
                    $fields_search[$field[0]]['field'] = $field[0];
                    $fields_search[$field[0]]['start'] = $start[1];
                } elseif (strpos($v,'[end]=')) {
                    $end = explode('=',$field[1]);
                    $fields_search[$field[0]]['end'] = $end[1];
                }else{
                    $value = explode('=',$v);
                    if($fields_search[$field[0]]['field']){
                        $fields_search[$field[0]]['value'] = $value[1];
                    }else{
                        $fields_search[$field[0]]['field'] = $field[0];
                        $fields_search[$field[0]]['condition'] = 'eq';
                        $fields_search[$field[0]]['value'] = $value[1];
                    }
                }
                $fields_search[$field[0]]['form_type'] = $fields_data_list[$field[0]];
            } else {
                $value = explode('=',$v);
                if ($value[0] == 'status') {
                    $fields_search['status'] = array('field' => 'status', 'condition' => 'eq', 'value' => $value[1], 'form_type' => 'box');
                }
            }
        }
        $this->fields_search = $fields_search;
        if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			cookie('listrows', $listrows, 3600 * 24 * 30);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = cookie('listrows') ? cookie('listrows') : 15;
			$params[] = "listrows=".$listrows;
		}
        $p = intval($_GET['p'])?intval($_GET['p']):1;
        $d_market_view = D('MarketView');
        $count = $d_market_view->where($where)->count();
        import("@.ORG.Page");
        $Page = new Page($count, $listrows);
        $field_array = D('Field')->get_list_show_field('market');    // 列表展示字段
        $this->field_array = $field_array;
        $fields = implode(',', y_array_column($field_array, 'field'));
        $list = $d_market_view->field($fields.',owner_role_id,creator_role_id,executor_role_ids,market_id,is_lock,is_deleted')->where($where)->order($order)->page($p.','.$listrows)->select();

        $executor_role_list = array();
        foreach ($list as $key => $val) {
            $list[$key]['creator_role_name'] = D('User')->get_full_name((int) $val['creator_role_id']);
            $list[$key]['owner_role_name'] = D('User')->get_full_name((int) $val['owner_role_id']);
            $executor_role_ids = trim($val['executor_role_ids'], ',');
            $executor_role_list = array_merge($executor_role_list, explode(',', $executor_role_ids));
            $list[$key]['executor_role_list'] = array_slice(explode(',', $executor_role_ids), 0, 4);
            if ($val['is_deleted']) {
                $list[$key]['status'] = '<span style="color: #ccc">已作废<span>';
            }
        }
        $user_ids = array_unique($executor_role_list);
        $user_list = D('User')->get_full_name($user_ids);
        $this->user_list = $user_list;
        $market_status_setting = M('fields')->where(array('model' => 'market', 'field' => 'status'))->getField('setting');
        eval('$market_status_list = ' . $market_status_setting . ';');
        $this->market_status_list = $market_status_list['data'];    // 普通搜索 活动状态
        $this->market_list = $list;     // 活动列表
        $this->alert = parseAlert();        // alert
		$Page->parameter = implode('&', $params);
        $this->page = $Page->show();        // 分页
        $this->listrows = $listrows;        // 每页条数
        $this->count = $count;      // 总条数 
        $this->field_list = $field_list;      // 高级搜索字段
        
        $this->display();
    }

    /**
     * 添加
     */
    public function add()
    {
        if (!checkPerByAction('market', 'add')) {
            alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
        }
        if (IS_POST) {
            $data = $_POST;
            $data['creator_role_id'] = session('role_id');
            $d_market = D('Market');
            $res = $d_market->add($data);
            if ($res) {
				alert('success', $d_market->msg, U('market/index', array('v_market_id' => $d_market->market_id)));
            } else {
                alert('error', $d_market->msg, $_SERVER['HTTP_REFERER']);
            }
        } else {
            $field_list = field_list_html("add","market");
		 	$this->field_list = $field_list;
            $this->alert = parseAlert();
			$this->display();
        }
    }

    public function view()
    {
        if (!checkPerByAction('market', 'view')) {
            echo '<div class="alert alert-error">您没有此权利！</div>';die();
        }
        $market_id = (int) $_GET['market_id'];
        if ($market_id == 0) alert('error', '参数错误', $_SERVER['HTTP_HREFERER']);
        $d_market = D('Market');
        $this->market_id = $market_id;
        $this->market = $d_market->first($market_id, 'view');   // 活动详情
        $this->leads_name = M('field')->where(array('model' => 'leads', 'field' => 'name'))->getField('name');      // 线索字段表示名
        $this->leads = $d_market->get_leads();        // 参与活动线索列表
        $this->customer = $d_market->get_customer();  // 参与活动客户列表
        $this->auth = $d_market->check_auth();        // 编辑修改权限
        $this->role_list = $d_market->role_list;                // 员工详情列表            
        $this->fields = $d_market->view_field;
        $this->display();
    }


    public function edit()
    {
        if (IS_POST) {
            $d_market = D('Market');
            // 修改字段
            if (IS_AJAX) {
                if (!checkPerByAction('market', 'edit')) {
                    $this->ajaxReturn(array('msg' => '您没有此权利！', 'status' => 0));
                }
                $market_id = $_POST['market_id'];
                if (!isset($_POST['field']) || !isset($_POST['value']) || $_POST['market_id'] == 0) {
                    $this->ajaxReturn(array('msg' => '参数错误', 'status' => 0));
                }
                
                $data[$_POST['field']] = $_POST['value'];
                $data['update_time'] = time();
                if (strpos($market_id, ',') !== false) {
                    $market_ids = array_filter(explode(',', $market_id));
                    foreach ($market_ids as $val) {
                        if (!$d_market->check_auth($val)) {
                            $this->ajaxReturn(array('msg' => '您没有此权利！', 'status' => 0));
                        }
                    }
                    $res = M('market')->where(array('market_id' => array('IN', $market_id)))->save($data);
                } else {
                    $market_id = (int) $market_id;
                    if (!$d_market->check_auth($market_id)) {
                        $this->ajaxReturn(array('msg' => '您没有此权利！', 'status' => 0));
                    }
                    $data['market_id'] = $market_id;
                    $res = M('market')->save($data);
                }
                if ($res) {
                    if ($_POST['field'] == 'executor_role_ids') {
                        $old_data = M('market')->where(array('market_id' => $market_id))->getField('executor_role_ids');
                        $d_market->eidtSendMessage(explode(',', trim($old_data, ',')), explode(',', trim($_POST['value'], ',')));
                    } else if ($_POST['field'] == 'owner_role_id') {
                        $old_data = M('market')->where(array('market_id' => $market_id))->getField('owner_role_id');
                        $d_market->eidtSendMessage($old_data, $_POST['value']);
                    }
                    $this->ajaxReturn(array('msg' => '修改成功', 'status' => 1));
                } else {
                    $this->ajaxReturn(array('msg' => '修改失败,稍后重试！', 'status' => 0));
                }
            }
            if (!checkPerByAction('market', 'edit')) {
                alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
            }
            $data = $_POST;
            $res = $d_market->save($data);
            if ($res) {
                alert('success', $d_market->msg, U('market/index', array('v_market_id' => $_POST['market_id'])));
            } else {
                alert('error', $d_market->msg, $_SERVER['HTTP_REFERER']);
            }
        } else {
            if (!checkPerByAction('market', 'edit')) {
                alert('error', '您没有此权利！', $_SERVER['HTTP_REFERER']);
            }
            $market_id = (int) $_GET['market_id'];
            if ($market_id == 0) {
                alert('error', '参数错误', $_SERVER['HTTP_REFERER']);
            }
            $d_user = D('User');
            $market = D('Market')->first($market_id, 'eidt');
            $this->market = $market;
            if ($market['is_lock'] != 0) alert('error', '已锁定，不可编辑。', $_SERVER['HTTP_REFERER']);
            if ($market['is_deleted'] != 0) alert('error', '已锁定，不可编辑。', $_SERVER['HTTP_REFERER']);

            $market['executor_role_ids'] = trim($market['executor_role_ids'], ',');
            $executor_role_ids = $market['executor_role_ids'] ? explode(',', trim($market['executor_role_ids'], ',')) : array();
            $this->executor_role_ids = $executor_role_ids;
            $executor_role_ids[] = $market['owner_role_id'];
            $this->role_list = $d_user->get_full_name($executor_role_ids);
            
            $field_list = field_list_html("edit","market", $market);
            $this->field_list = $field_list;
            $this->market_id = $market_id; 
            $this->alert = parseAlert();
            $this->display();
        }
    }


    public function add_log()
    {
        if (IS_POST) {
            $market_id = $_POST['market_id'];
            if (!D('Market')->check_auth($market_id)) {
                $this->ajaxReturn(array('msg' => '权限不足', 'status' => 0));
            }
            $data['content'] = $_POST['content'];
            $data['role_id'] = session('role_id');
            $data['category_id'] = 1;
            $data['create_date'] = time();
            $data['update_date'] = time();
            $log_id = M('log')->add($data);
            if ($log_id) {
                $r_id = M('RMarketLog')->add(array('market_id' => $market_id, 'log_id' => $log_id));
                if ($r_id) {
                    $role_info = D('User')->get_full_name(array(session('role_id')));
                    $data = $role_info[session('role_id')];
                    if (!$data['img'] || $data['img'] == '__PUBLIC__/img/avatar_default.png') {
                        $data['img'] = './Public/img/avatar_default.png';
                    }
                    $data['log_id'] = $log_id;
                    $data['create_date'] = date('Y-m-d H:i:s');
                    $res = array('msg' => '发表成功', 'status' => '1', 'data' => $data);
                } else {
                    $res = array('msg' => '发表失败', 'status' => '0');
                }
            } else {
                $res = array('msg' => '发表失败', 'status' => '0');
            }
            $this->ajaxReturn($res);
        }
    }


    /**
     * 市场关联客户
     */
    public function customer()
    {
        if (!checkPerByAction('market', 'edit')) {
            $this->ajaxReturn(array('msg' => '您没有此权利！', 'status' => 0));
        }
        $market_id = (int) $_REQUEST['market_id'];
        $customer_ids = (array) $_REQUEST['customer_ids'];
        if ($market_id == 0 || empty($customer_ids)) {
            $arr = array('msg' => '参数错误', 'status' => 0);
        } else {
            $r_market_customer = M('RMarketCustomer');
            $r_ids = array();
            foreach ($customer_ids as $key => $val) {
                $data = array('market_id' => $market_id, 'customer_id' => $val);
                if ($r_market_customer->where($data)->count() == 0) {
                    $r_id = $r_market_customer->add($data);
                    if ($r_id) {
                        $r_ids[] = $r_id;
                    } else {
                        $r_market_customer->where(array('id' => array('IN', implode($r_ids))))->delete();
                        break;
                    }
                } else {
                    unset($customer_ids[$key]);
                }
            }
            if (count($r_ids) == count($customer_ids)) {
                $arr = array('msg' => '添加成功', 'status' => 1);
            } else {
                $arr = array('msg' => '添加失败', 'status' => 0);
            }
        }
        $this->ajaxReturn($arr);
    }


    /**
     * 删除线索、客户与活动间联系
     */
    public function delete()
    {
        if (!checkPerByAction('market', 'edit')) {
            $this->ajaxReturn(array('msg' => '您没有此权利！', 'status' => 0));
        }
        if (IS_POST && IS_AJAX) {
            $model = $_POST['model'];
            $id = (int) $_POST['id'];
            $market_id = (int) $_POST['market_id'];
            if (!in_array($model, array('leads', 'customer')) || $id == 0 || $market_id == 0) {
                $arr = array('msg' => '参数错误', 'status' => 0);
            } else {
                $model_arr = array('leads' => 'RMarketLeads', 'customer' => 'RMarketCustomer');
                $res = M($model_arr[$model])->where(array('market_id' => $market_id, $model . '_id' => $id))->delete();
                if ($res) {
                    $arr = array('msg' => '删除成功', 'status' => 1);
                } else {
                    $arr = array('msg' => '删除失败', 'status' => 0);
                }
            }
            $this->ajaxReturn($arr);
        }
    }


    /**
     * 客户所参与的活动
     */
    public function customer_market_list()
    {
        $customer_id = (int) $_POST['customer_id'];
        if ($customer_id == 0) {
            $arr = array('status' => 0);
        } else {
            $market_list = D('Market')->get_customer_market_list($customer_id);
            if (!empty($market_list)) {
                $arr = array('data' => $market_list, 'status' => 1);
            } else {
                $arr = array('status' => 0);
            }
        }
        $this->ajaxReturn($arr);
    }


    public function widget(){
		$widget_id = $_REQUEST['widget_id'];
		if (!getPerByAction('market','index')) {
			$this->ajaxReturn('','---暂无数据---',0);
		}

        $dashboard = unserialize(M('user')->where(array('role_id'=>session('role_id')))->getField('dashboard'));
        foreach ($dashboard['dashboard'] as $key => $val) {
            if ($val['widget'] == 'Market') {
                $widget = $val; break;
            }
        }
        if (!$widget) {
			$this->ajaxReturn('','---数据异常---',0);
        }
        $level = $widget['level'];
        
        if ($level) {
            $auth_role_id = getPerByAction('market','index');
            $temp_where['_logic'] = 'OR';
            $temp_arr = array();
            foreach ($auth_role_id as $auth_role_id_val) {
                $temp_arr[] = '%,' . $auth_role_id_val . ',%';
            }
            $temp_where['executor_role_ids'] = array('LIKE', $temp_arr, 'OR');
            $temp_where['owner_role_id'] = array('IN', implode(',', $auth_role_id));
            $where['_complex'] = $temp_where;
        } else {
            $temp_where['_logic'] = 'OR';
            $temp_where['executor_role_ids'] = array('LIKE', '%,'. session('role_id') .',%');
            $temp_where['owner_role_id'] = session('role_id');
            $where['_complex'] = $temp_where;
        }
        
		$page_size = 10;
		$p = $_REQUEST['p'] ? $_REQUEST['p'] : 1;
        
        $market = D('Market')->where($where)->field('market_id,name,create_time')->page($p.','.$page_size)->select();
        foreach ($market as $key => $val) {
            $market[$key]['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
        }

		$count = D('Market')->where($where)->count();

		$page = '<div style="margin: 6px 0 0 10px;">共'.$count.'条 '.$p.'/'.ceil($count / $page_size).' 页 ';
		if ($p == 1) {
			$page .= ' <span style="padding: 0 10px; border: 1px solid #ccc;"><i class="glyphicon glyphicon-chevron-left"></i></span> ';
		} else {
			$page .= ' <a class="prev_widget_'.$widget_id.'" href="javascript:void(0);" style="padding: 0 10px; border: 1px solid #ccc;"><i class="glyphicon glyphicon-chevron-left"></i></a> ';
		}
		if ($p == ceil($count / $page_size)) {
			$page .= ' <span style="padding: 0 10px; border: 1px solid #ccc;"><i class="glyphicon glyphicon-chevron-right"></i></span> ';
		} else {
			$page .= ' <a class="next_widget_'.$widget_id.'" href="javascript:void(0);" style="padding: 0 10px; border: 1px solid #ccc;"><i class="glyphicon glyphicon-chevron-right"></i></a> ';
		}
		$page .= '</div>';
		$page .= "
			<script>
				$('.prev_widget_".$widget_id."').on('click', function(){
					widget_page_".$widget_id." -= 1;
					marketWidgetGetData_".$widget_id."($(this));
				});
				$('.next_widget_".$widget_id."').on('click', function(){
					widget_page_".$widget_id." += 1;
					marketWidgetGetData_".$widget_id."($(this));
				});
			</script>
		";
		if(!empty($market)){
			$this->ajaxReturn(array('data' => $market, 'page' => $page),'success',1);
		}else{
			$this->ajaxReturn('','---暂无数据---',0);
		}
	}
    
}
