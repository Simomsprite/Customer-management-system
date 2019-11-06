<?php

// +----------------------------------------------------------------------
// | 搜索类，内部开发使用前选择性阅读
// +----------------------------------------------------------------------
// | from_type意为form表单类型，决定了选择不同的搜索条件时（配合pdcrm_more.js）动态生成不同的表单类型如input、select或地址选择插件等
// | from_type在单个需要有搜索功能的方法中设置[$field_list]，可兼容自定义字段
// | 不同的form_type会对应不同的condition条件，根据实际情况可进一步补充，查看本类中conditionList()方法
// +----------------------------------------------------------------------
// | 由于历史代码的原因，pdcrm_more.js（生成搜索的动态html的js）不能通过本类中相关设置同步改变一些效果如condition选项，需要手动去修改
// +----------------------------------------------------------------------

class SearchmModel extends Model{
    
    /**
     * 通过get参数处理查询的where条件，仅支持数组格式查询，单信息类型要么处理成数组，要么在对应方法内处理
     * @param $get_list get请求的所有参数
     * 返回值 where 处理后的查询条件
     * 返回值 page_params 记录分页参数
     * 返回值 fields_search 本次搜索记录，重新打开高级搜索时需要默认记录
     * 返回值 single_list 单一搜索记录，如by、listrows等参数
     * @author lee
     */
    public function getWhere($get_list = array())
    {
        $num = 0;
        if ($get_list) {
            if (isset($get_list['_analytics'])) {
                $this->analytics($get_list);
            }
            foreach ($get_list as $k => $v) {       
                // 记录分页参数，用于分页跳转时保留查询条件
                if(is_array($v)){
                    foreach ($v as $key => $val) {
                        $page_params[] = $k . '[' . $key . ']=' . $val;
                    }
                } else {
                    // 记录特殊【单一】搜索条件，并跳过
                    $page_params[] = $k.'='.$v;
                    if($k != 'p') $single_list[$k] = $v;
                    continue;
                }

                // 记录本次搜索部分参数，配合前台页面保留本次搜索的信息
                $num++;
                $fields_search[$num]['field'] = $k;
                $fields_search[$num]['form_type'] = $v['form_type'];

                // 处理联表查询情况，特殊命名的字段名称
                if (strpos($k, '-')) {
                    $field = str_replace('-', '.', $k);
                } else {
                    $field = $k;
                }

                // 时间搜索做特殊处理
                if ($v['form_type'] == 'date' || $v['form_type'] == 'datetime') {      
                    $time_where = $this->timeWhere($v);
                    if ($time_where) {
                        $where[$field] = $time_where;
                    }
                    continue;
                }

                // 地址搜索做特殊处理
                if ($v['form_type'] == 'address') {
                    $address_where = $this->addressWhere($v);
                    if ($address_where) {
                        $where[$field] = $address_where;
                    }  
                    continue; 
                }

                $v['value'] = trim($v['value']);
                switch ($v['condition']) {
                    case 'contains': $where[$field] = array('like','%'.$v['value'].'%'); break;
                    case 'not_contain': $where[$field] = array('notlike','%'.$v['value'].'%'); break;
                    case 'start_with': $where[$field] = array('like', $v['value'].'%'); break;
                    case 'end_with': $where[$field] = array('like','%'.$v['value']); break;
                    case 'is': $where[$field] = array('eq', $v['value']);break;
                    case 'isnot': $where[$field] = array('neq', $v['value']);break;
                    case 'eq': $where[$field] = array('eq', $v['value']); break;
                    case 'neq': $where[$field] = array('neq', $v['value']); break;
                    case 'gt': $where[$field] = array('gt', $v['value']); break;
                    case 'egt': $where[$field] = array('egt', $v['value']); break;
                    case 'lt': $where[$field] = array('lt', $v['value']); break;
                    case 'elt': $where[$field] = array('elt', $v['value']); break;
                    case 'is_empty': $where[$field] = array('eq',''); break;
                    case 'is_not_empty': $where[$field] = array('neq',''); break;
                    case 'in': $where[$field] = array('IN', $v['value']); break;
                    default : $where[$field] = array('eq', $v['value']); break; break;
                }
            }
            return array('where' => $where, 'page_params' => $page_params, 'fields_search' => $fields_search, 'single_list' => $single_list);
        } else {
            return;
        }
    }


    /**
     * 时间查询where条件
     * @param time_range ['start' => '2019-09-14', 'end' => '2019-09-15']
     * @author lee
     */
    public function timeWhere($time_range)
    {
        $time_start = $time_range['start'] ? strtotime($time_range['start']) : '';
        $time_end = $time_range['end'] ? strtotime($time_range['end']) + 86399 : '';    // 结束时间当天的数据要算上
        if ($time_start && !$time_end) {
            return array('egt', $time_start);
        } else if (!$time_start && $time_end){
            return array('elt', $time_end);
        } else if ($time_start && $time_end){
            return array('between', array($time_start, $time_end));
        } else {
            return false;
        }
    }


    /**
     * 创建地址查询where条件
     * @param address 地址信息 ['state' => $state, 'city' => $city, 'area' => $area, 'value' => $value, 'condition' = 'contains']
     * @author lee
     */
    public function addressWhere($address)
    {
        if (!$address['state'] && !$address['value']) {
            return false;
        } else {
            // 联动的省、市、地区
            if ($address['state']) {
                $address_where[] = '%'.$address['state'].'%';
                if($address['city']){
                    $address_where[] = '%'.$address['city'].'%';
                    if($address['area']){
                        $address_where[] = '%'.$address['area'].'%';
                    }
                }
            }
            // 手动填写的街道信息
            if ($address['value']) {
                $address_where[] = '%'.$address['value'].'%';
            }
            if ($address['condition'] == 'not_contain') {
                return array('notlike', $address_where, 'OR');
            } else {
                return array('like', $address_where, 'AND');
            }
        }
    }


    /**
     * 【作废】获取联表查询的关联id集合条件，举个栗子：商机表关联了客户customer_id，查询的商机的条件是客户名称
     * @param field_info
     * @param where 已有的where条件
     * @author lee
     */
    public function relationWhere($field_info, $tab, $where_field, $where)
    {   
        # 作废
        $m_tab = M($tab);
        foreach ($field_info as $k => $v) {
            if (isset($where[$k])) {
                $where_tab[$v] = $where[$k];
                unset($where[$k]);
            }
        }
        if ($where_tab) {
            $field_arr = $m_tab->where($where_tab)->getField($where_field, true);
            $where[$where_field] = array('in', $field_arr ?: '');
        }
        return $where;
    }


    /**
     * 处理role_id有权限范围的情况【暂时只处理 'role_id' => ['eq', 1] 这种结构的情况】
     * @param field 字段名 如 owner_role_id
     * @param where 已有的where条件
     * @param per_range 权限范围
     * @author lee
     */
    public function roleWhere($field, $where, $per_range)
    {
        $condition = $where[$field][0];
        $role_id = $where[$field][1];
        if (intval($where[$field]) > 0) {
            // 如【我的】 $where['owner_role_id'] = 1;这种情况，不做处理
            return $where;
        } else if (is_array($role_id)){
            // 如【全部、下属】$where['owner_role_id'] = array('in', array('1,2,3'));这种情况，权限已做过滤，不做处理
            return $where;
        } else {
            // 处理新的搜索逻辑，如 $where['owner_role_id'] = array('eq', 1); 这种情况
            if ($role_id) {
                if (!in_array($role_id, $per_range)) {
                    // 如果不在权限范围内，则不能查询出数据，删除原有where条件【条件可能很复杂】以提升查询速度
                    unset($where);
                    $where[$field] = -1;
                } else {
                    if ($condition == 'neq') {
                        // array_diff 把数组大的放前面，反之会出问题
                        $role_ids = array_diff($per_range, array($role_id));
                        $where[$field] = array('in', $role_ids ?: '');
                    }
                }
                return $where;
            } else {
                // 没有owner_role_id相关搜索，默认取权限范围内的
                $where[$field] = array('in', $per_range);
                return $where;
            }
        }
    }


    /**
     * 根据权限范围和部门或员工搜索，返回可操作role_id集合【目前仅用于统计那里的搜索】
     * @author lee
     */
    function roleIdRange($below_ids, $range = 'sale'){
        $m_user = M('User');
  
        // 员工role_id范围设置
        if (intval($_GET['role'])) {
            $role_ids = array(intval($_GET['role']));
        } else if (intval($_GET['department'])){
            $role_ids = D('RoleView')->where('role_department.department_id = %d', intval($_GET['department']))->getField('role_id', true);
        } else {  
            if ($range == 'sale') {
                $where_user['type'] = 1;  // 只查销售
            }
            $where_user['role_id'] = array('in', $below_ids);
            $role_ids = $m_user->where($where_user)->getField('role_id', true);
        }
        if (is_array($role_ids)) {
            $role_id_array = array_intersect($role_ids, $below_ids); // 数组交集
        } else {
            $role_id_array = array();
        }
        return array_map('intval', $role_id_array);
    }


    /**
     * 数据范围
     * @param   array   $below_ids  权限范围ID  getPerByAction(MODULE, ACTION);
     * @param   array   role_id 员工ID, department_id 部门ID
     */
    public function authRangew($below_ids = array(), $param = array())
    {
		if (!isset($param['department_id']) && (int) $_GET['department_id'] && $_GET['department_id'] != 'all') {
			$param['department_id'] = (int) $_GET['department_id'];
		}
		if (!isset($param['role_id']) && (int) $_GET['role_id'] && $_GET['role_id'] != 'all') {
			$param['role_id'] = (int) $_GET['role_id'];
		}
        if ($param['role_id']) {
			$role_ids = array($param['role_id']);
		} elseif ($param['department_id']) {
            $role_ids = getRoleByDepartmentId($param['department_id'], 1);
			$role_ids = y_array_column($role_ids, 'role_id');
		} else {
			$role_ids = $below_ids;
		}
        $role_ids = array_map('intval', $role_ids);
		if (!is_subset($role_ids, $below_ids)) {
			$role_ids = $below_ids;
        }
        return array('IN', (array) $role_ids ?: array(-1));
    }
    
}
