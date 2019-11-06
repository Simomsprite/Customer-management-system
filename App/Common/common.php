<?php
function deldir($dir) {
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (!is_dir($fullpath)) {
                @unlink($fullpath);
            } else {
                @deldir($fullpath);
            }
        }
    }
    closedir($dh);
}
/*取得文件后缀*/
function getExtension($filename){
    $mytext = substr($filename, strrpos($filename, '.')+1);
    return $mytext;
}
/**
 * @param 图片格式 进行图片格式的判断
 * @return array
 */
function imgFormat(){
    return array('jpg','png','jpeg','gif');
}
/**
 * 根据 后缀显示图标 函数
 */
function show_picture($file_name){
    /*判断文件格式 对应其图片*/
    $houzhui = getExtension($file_name);
    switch ($houzhui) {
        case 'doc':
            $pic = 'doc.png';
            break;
        case 'docx':
            $pic = 'doc.png';
            break;
        case 'pptx':
            $pic = 'ppt.png';
            break;
        case 'ppt':
            $pic = 'ppt.png';
            break;
        case 'xls':
            $pic = 'excel.png';
            break;
        case 'zip':
            $pic = 'zip.png';
            break;
        case 'zip':
            $pic = 'zip.png';
            break;
        case 'pdf':
            $pic = 'pdf.png';
            break;
        case 'png':
            $pic = 'pic.png';
            break;
        case 'jpg':
            $pic = 'pic.png';
            break;
        case 'jpeg':
            $pic = 'pic.png';
            break;
        case 'gif':
            $pic = 'pic.png';
            break;
        default:
            $pic = 'unknown.png';
            break;
    }
    return $pic;
}
//手机端选择负责人
function owner_name_select($role_id){
    $d_role_view = D('RoleView');

    $all_role = M('role')->where('user_id <> 0')->select();
    $below_role = getSubRole(session('role_id'), $all_role);

    $below_ids[] = session('role_id');
    foreach ($below_role as $key=>$value) {
        $below_ids[] = $value['role_id'];
    }
    $where['role.role_id'] = array('in',implode(',',$below_ids));
    $where['user.status'] = 1;
    $role_list = $d_role_view->where($where)->order('department_id ASC,position_id ASC')->field('role_id,user_name,department_name,role_name')->select();

    $string = '<select id="owner_role_id" name="owner_role_id">';
    if(is_array($role_list)){
        $string .= '<option value="0">--请选择--</option>';
        foreach($role_list as $v){
            if($role_id && $role_id == $v['role_id']){
                $string .= '<option selected="selected" value="'.$v['role_id'].'">'.$v['user_name'].'</option>';
            }else{
                $string .= '<option value="'.$v['role_id'].'">'.$v['user_name'].'</option>';
            }
        }
    }
    $string .= '</select>';
    return $string;
}


//高级搜索生成where条件
function field($search,$condition=''){
    switch ($condition) {
        case "is" : $where = array('eq',$search);break;
        case "isnot" :  $where = array('neq',$search);break;
        case "contains" :  $where = array('like','%'.$search.'%');break;
        case "not_contain" :  $where = array('notlike','%'.$search.'%');break;
        case "start_with" :  $where = array('like',$search.'%');break;
        case "not_start_with" :  $where = array('notlike',$search.'%');break;
        case "end_with" :  $where = array('like','%'.$search);break;
        case "is_empty" :  $where = array('eq','');break;
        case "is_not_empty" :  $where = array('neq','');break;
        case "gt" :  $where = array('gt',$search);break;
        case "egt" :  $where = array('egt',$search);break;
        case "lt" :
            if (strtotime($search) !== false && strtotime($search) != -1) {
                $where = array('lt',strtotime($search));
            } else {
                $where = array('lt',$search);
            }
            break;
        case "elt" :  $where = array('elt',$search);break;
        case "eq" : $where = array('eq',$search);break;
        case "neq" : $where = array('neq',$search);break;
        case "between" : $where = array('between',array(strtotime($search)-1,strtotime($search)+86400));break;
        case "nbetween" : $where = array('not between',array(strtotime($search),strtotime($search)+86399));break;
        case "tgt" :  $where = array('gt',strtotime($search)+86400);break;
        case 'in':
            $where = array('IN', $search);
            break;
        case 'not_in':
            $where = array('NOT IN', $search);
            break;
        default : $where = array('eq',$search);
    }
    return $where;
}

function format_price($num){
    $num = round($num, 0);
    $s_num = strval($num);
    $len = strlen($s_num)-1;
    $result = round($num, -$len);
    return $result;
}

//方法说明	获取首页需要显示的列名字符串
function getIndexFields($model,$status){
    if(!$model) return false;
    $m_model = M($model);
    $where['in_index'] = 1;
    $where['model'] = $model;
    if ($model == 'examine') {
        $where['status'] = $status;
    }
    $model_fields = M('Fields')->where($where)->order('order_id ASC')->select();
    return $model_fields;
}
//获取主表字段 用于搜索
function getMainFields($model,$status){
    if(!$model) return false;
    $m_model = M($model);
    $where['is_main'] = 1;
    $where['model'] = $model;
    if ($model == 'examine') {
        $where['status'] = $status;
    }
    $model_fields = M('Fields')->where($where)->order('order_id ASC')->select();
    return $model_fields;
}
/**记录操作日志
 * $id 操作对象id
 * $param_name 参数
 * $text 附加信息
 * 2013-10-23
 **/
function actionLog($id,$param_name='',$text=''){
    $role_id = session('role_id');
    $user = M('user')->where(array('user_id'=>session('user_id')))->find();
    $category = $user['category_id'] == 1 ? L('ADMIN') : L('USER');
    $data['role_id'] = $role_id;
    $action_name = strtolower(ACTION_NAME);
    if($action_name == 'mylog_add'){
        $module_name = 'log';
    } elseif (strtolower(MODULE_NAME) == 'call'){
        $module_name = 'customer';
        $action_name = 'add';
    } else {
        $module_name = strtolower(MODULE_NAME);
    }
    $data['module_name'] = $module_name;
    if($action_name == 'addajax'){
        $data['action_name'] = 'add';
    }elseif($action_name == 'editajax'){
        $data['action_name'] = 'edit';
    }elseif($action_name == 'del'){
        $data['action_name'] = 'delete';
    }else{
        $data['action_name'] = strtolower(ACTION_NAME);
    }
    if(!empty($param_name)){
        $data['param_name'] = strtolower($param_name);
    }
    $data['create_time'] = time();
    $data['action_id'] = $id;
    $data['content'] = L('ACTIONLOG',array($category,$user['full_name'],date('Y-m-d H:i:s'),L($data['action_name']),$id,L($module_name),$text));
    $actionLog = M('actionLog');
    $actionLog->create($data);
    if($actionLog->add()){
        //如果是删除操作，设置action_delete = 1
        if(strtolower($data['action_name']) == 'delete' || strtolower($data['action_name']) == 'completedelete' || strtolower($data['action_name']) == 'log_delete' || strtolower($data['action_name']) == 'del'){
            $actionLog->where('module_name = "%s" and action_id = %d', strtolower($module_name), $id)->setField('action_delete',1);
        }
        return true;
    }else{
        return false;
    }
}
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    if(utf8_strlen($str) < $length) $suffix = false;
    return $suffix ? $slice.'...' : $slice;
}

function utf8_strlen($string = null) {
    preg_match_all("/./us", $string, $match);
    return count($match[0]);
}

function getDateTime($model){
    $user = M('User')->where('role_id = %d',session('role_id'))->field('user_id,last_read_time')->find();
    if($user['last_read_time']){
        $last_read_time = json_decode($user['last_read_time'],true);
    }
    $last_read_time[$model] = time();
    $last_read_time = json_encode($last_read_time);
    M('User')->where('user_id = %d',$user['user_id'])->setField('last_read_time',$last_read_time);
    return true;
}

function getSubCategory($category_id, $category, $separate) {
    $array = array();
    foreach($category AS $value) {
        if ($category_id == $value['parent_id']) {
            $array[] = array('category_id' => $value['category_id'], 'name' => $separate.$value['name'],'description'=>$value['description']);
            $array = array_merge($array, getSubCategory($value['category_id'], $category, $separate.'--'));
        }
    }
    return $array;
}
// 不包括自己所在部门
function getSubDepartment($department_id, $department, $separate, $no_separater) {
    $array = array();
    if($no_separater){
        foreach($department AS $value) {
            if ($department_id == $value['parent_id']) {
                $array[] = array('department_id' => $value['department_id'], 'name' => $separate.$value['name'],'description'=>$value['description']);
                $array = array_merge($array, getSubDepartment($value['department_id'], $department, $separate, 1));
            }
        }
    }else{
        foreach($department AS $value) {
            if ($department_id == $value['parent_id']) {
                $array[] = array('department_id' => $value['department_id'], 'name' => $separate.$value['name'],'description'=>$value['description']);
                $array = array_merge($array, getSubDepartment($value['department_id'], $department, $separate.'--'));
            }
        }
    }
    return $array;
}

//包括自己所在部门
function getSubDepartment2($department_id, $department, $first=0) {
    $array = array();
    $m_department =  M('role_department');
    if($first == 1){
        $depart = $m_department->where('department_id = %d', session('department_id'))->find();
        $array[] = array('department_id'=>$depart['department_id'],'name'=>$depart['name'], 'description'=>$depart['description']);
    }
    foreach($department AS $value) {
        if ($department_id == $value['parent_id']) {
            $array[] = array('department_id' => $value['department_id'], 'name' => $separate.$value['name'],'description'=>$value['description']);
            $array = array_merge($array, getSubDepartment2($value['department_id'], $department, '--'));
        }
    }
    return $array;
}

function getSubDepartmentTreeCode($department_id, $first=0) {
    $string = "";
    $department_list = M('roleDepartment')->where('parent_id = %d', $department_id)->select();

    if ($department_list) {

        $string = "<ul>";

        foreach($department_list AS $value) {
            if($department_id == $value['department_id']){
                $name = '<a href="'.U('user/department','department_id='.$value['department_id']).'" class="control_dep dep1 jstree-clicked">'.$value['name'].'</a>';
            }else{
                $name = '<a href="'.U('user/department','department_id='.$value['department_id']).'" onclick="javascript:window.location.href=\''.U('user/department','department_id='.$value['department_id']).'\'" class="control_dep dep1">'.$value['name'].'</a>';
            }
            $string .="<li class='jstree-open' rel='".$value['department_id']."'>".$name.getSubDepartmentTreeCode($value['department_id'])."</li>";
        }
        $string .= "</ul>";
    }

    return $string;
}
//type == 1获取授权完整树形图
//type == 2获取选择授权树形图
function getSubPositionTreeCode($position_id, $first=0,  $type=1, $department_id) {
    $string = "";
    $position_list = M('position')->where('parent_id = %d', $position_id)->select();
    $m_role_department = M('RoleDepartment');
    $d_role = D('RoleView');
    if($department_id){
        if($first){
            $position_list = M('position')->where('position_id = %d and department_id = %d', $position_id, $department_id)->select();
        }else{
            $position_list = M('position')->where('parent_id = %d and department_id = %d', $position_id, $department_id)->select();
        }
    }else{
        if($first && $department_id){
            $position_list = M('position')->where('position_id = %d', $position_id)->select();
        }else{
            $position_list = M('position')->where('parent_id = %d', $position_id)->select();
        }
    }

    if ($position_list) {
        if ($first) {
            if($type == 1)
                $string = '<ul class="position_browser filetree" style="margin-left:25px;">';
            else
                $string = '<ul class="filetree">';
        } else {
            $string = "<ul id='del'>";
        }
        foreach($position_list AS $value) {
            $department_name = $m_role_department->where('department_id = %d', $value['department_id'])->getField('name');
            $user_list = $d_role->where('position.position_id = %d', $value['position_id'])->select();
            $user_str = '';
            $user_count = 0;
            foreach($user_list as $v){
                $name = '';
                $name = $v['user_name'] ? $v['user_name'] : '无';
                if($v['status'] == '3'){
                    $username = $name.'-未激活';
                }elseif($v['status'] == '1'){
                    $username = $name;
                }
                if($v['status'] != '2'){
                    $user_count++;
                    $user_str .= $username.'、';
                }
            }
            // if(mb_strlen($user_str, 'UTF-8') > 30){
            // 	$user_str = mb_substr($user_str, 0, 30, 'UTF-8').'.....';
            // }
            $user_str = mb_substr($user_str, 0, mb_strlen($user_str, 'UTF-8')-1, 'UTF-8');
            if($user_str) {
                $user_str = '&nbsp;'.$user_str.'&nbsp;';
            }

            $string .= "<li class='jstree-open'><a class='control_dep dep2' rel='".$value['position_id']."' href='javascript:void(0);'>".$value['name']." - ".$department_name."<span style='margin-left:20px;'>【".$user_count."名员工】".$user_str."</span></a>&nbsp;".$link_str.getSubPositionTreeCode($value['position_id'], 0, $type, $department_id)."</li>";
        }
        $string .= "</ul>";
    }
    return $string;
}

/*function getSubPositionTreeCode($position_id, $first=0,  $type=1, $department_id) {
	$string = "";
	$position_list = M('position')->where('parent_id = %d', $position_id)->select();
	if($department_id){
		if($first){
			$position_list = M('position')->where('position_id = %d and department_id = %d', $position_id, $department_id)->select();
		}else{
			$position_list = M('position')->where('parent_id = %d and department_id = %d', $position_id, $department_id)->select();
		}

	}else{
		if($first && $department_id){
			$position_list = M('position')->where('position_id = %d', $position_id)->select();
		}else{
			$position_list = M('position')->where('parent_id = %d', $position_id)->select();

		}
	}

	if ($position_list) {
		if ($first) {
			if($type == 1)
				$string = '<ul class="position_browser filetree" style="margin-left:25px;">';
			else
				$string = '<ul class="filetree">';
		} else {
			$string = "<ul>";
		}
		foreach($position_list AS $value) {
			$department_name = M('RoleDepartment')->where('department_id = %d', $value['department_id'])->getField('name');
			$user_list = D('RoleView')->where('position.position_id = %d', $value['position_id'])->select();
			$user_str = '';
			foreach($user_list as $v){
				if($v['status'] == '0'){
					$username = $v['user_name'].'-未激活';
				}elseif($v['status'] == '2'){
					$username = '<del>'.$v['user_name'].'</del>';
				}else{
					$username = $v['user_name'];
				}
				$user_str .= '<a style="color: #000000;" href="'.U('user/view','id='.$v['user_id']).'">'.$username.'、</a>';
			}
			if($user_str) $user_str = '('.$user_str.')';

			if($type == 1){
				$link_str = " <span class='control' id='control_file".$value['position_id']."'> <a class='position_edit' rel=".$value['position_id']." href='javascript:void(0)'>".L('EDIT')."</a> &nbsp; <a class='permission' rel=".$value['position_id']." href='javascript:void(0)'>".L('AUTHORIZE')."</a> &nbsp; <a class='allow_permission' rel=".$value['position_id']." href='javascript:void(0)'>".'权限继承'."</a> &nbsp; <a class='position_delete' rel=".$value['position_id']." href='javascript:void(0)'>".L('DELETE')."</a></span>";
			}else{
				//$link_str = " <span class='control' id='control_file".$value['position_id']."'> <a class='allow_permission_id' rel=".$value['position_id']." href='javascript:void(0)'>".'选择'."</a> ";
				$link_str = " <span class='control' id='control_file".$value['position_id']."'> <input class='allow_permission_id' type='radio' name='parent_id' rel=".$value['position_id']." href='javascript:void(0)'>";
			}
			$link_str = '';

			$string .= "<li style='list-style-type: none;'><span rel='".$value['position_id']."' class='file '><span class='control_pos'><a href='javascript:;'>".$value['name']."</a> - $department_name"." </span>&nbsp; ".$user_str." &nbsp;".$link_str."</span>".getSubPositionTreeCode($value['position_id'], 0, $type, $department_id)."</li>";

		}
		$string .= "</ul>";
	}

	return $string;
}*/

//参数说明
//$role_id == 0 为当前人下属roleid
//$role_id == 1 为获得所有人roleid
function getSubRoleId($self = true, $role_id=0){
    $all_role = M('role')->where('user_id <> 0')->select();
    if(!$role_id){
        $role_id = session('role_id');
        $below_role = getSubRole($role_id, $all_role);
    }else{
        $below_role = getSubRole(0, $all_role);
    }
    $below_ids = array();
    if ($self) {
        $below_ids[] = $role_id;
    }
    foreach ($below_role as $key=>$value) {
        $below_ids[] = $value['role_id'];
    }
    return array_map('intval', array_unique($below_ids));
}

/*
*	手机端getSubRoleId 此方法弃用，使用 getSubRole
*/
function getSubRoleByRole($role_id,$self = true){
    $all_role = M('role')->where('user_id <> 0')->select();
    $below_role = getSubRole($role_id, $all_role);
    $below_ids = array();
    if ($self) {
        $below_ids[] = $role_id;
    }
    foreach ($below_role as $key=>$value) {
        if($value['role_id'] != session('role_id')){
            $below_ids[] = $value['role_id'];
        }
    }
    return $below_ids;
}

function getSubRole1($role_id, $role_list, $separate) {
    $search_disable_user = M('Config')->where('name="search_disable_user"')->getField('value');
    if ($search_disable_user) {
        $where_search_disable_user = ' && user.status != 3 ';
    } else {
        $where_search_disable_user = ' && user.status = 1 ';
    }
    $d_role = D('RoleView');
    if($d_role->where('role.role_id = %d', $role_id)->find()){
        $position_id = $d_role->where('role.role_id = %d', $role_id)->getField('position_id');
    }else{
        $position_id  = 0;
    }
    $sub_position = getPositionSub($position_id ,true, true);

    foreach($sub_position AS $position_id) {
        $son_role = $d_role->where('role.position_id = %d'.$where_search_disable_user, $position_id['position_id'])->select();
        foreach($son_role as $val){
            $array[] = array('role_id' => $val['role_id'],'user_id' => $val['user_id'], 'parent_id' => $val['parent_id'], 'name' => $separate . $val['department_name'] . ' | ' . $val['role_name']);
        }
    }
    return $array;
}
function getSubRole($role_id, $role_list, $separate) {
    $d_role = D('RoleView');
    if($d_role->where('role.role_id = %d', $role_id)->find()){
        $position_id = $d_role->where('role.role_id = %d', $role_id)->getField('position_id');
    }else{
        $position_id  = 0;
    }
    $sub_position = getPositionSub($position_id ,true, true);

    foreach($sub_position AS $position_id) {
        $son_role = $d_role->where('role.position_id = %d', $position_id['position_id'])->select();
        foreach($son_role as $val){
            $array[] = array('role_id' => $val['role_id'],'user_id' => $val['user_id'], 'parent_id' => $val['parent_id'], 'name' => $separate . $val['department_name'] . ' | ' . $val['role_name']);
        }
    }
    return $array;
}


//$self_flag = true 不包含自己岗位
function getPositionSub($position_id ,$sub = false, $self_flag = false){
    $sub_position = M('position')->where('parent_id = %d', $position_id)->select();

    $array = $sub_position;
    if($sub){
        if($sub_position){
            foreach($sub_position as $value){
                $son_position = getPositionSub($value['position_id'] ,$sub);
                if(!empty($son_position)){
                    $array = array_merge($array, $son_position);
                }
            }
        }else{
            if(!$self_flag)	{
                $sub_position = M('position')->where('position_id = %d', $position_id)->find();
                $array[] = $sub_position;
            }
        }
    }
    return $array;
}


function getSubPosition($position_id, $position, $separate) {
    $array = array();
    foreach($position AS $key=> $value) {
        if ($position_id == $value['parent_id']) {
            $m_department = M('RoleDepartment');
            $department_name = $m_department->where('department_id = %d', $value['department_id'])->getField('name');
            $array[] = array('position_id' => $value['position_id'], 'name' => $separate . $department_name . ' | ' . $value['name'],'description'=>$value['description']);
            $array = array_merge($array, getSubPosition($value['position_id'], $position, $separate.' -- '));
        }
    }
    return $array;
}

function getSubDepartmentByRole($role_id = 0){
    if($role_id <= 0) $role_id = session('role_id');
    $department_id = M('Role')->where('role_id = %d', $role_id)->getField('department_id');
    //未完成方法
}
//通过部门id获取该部门员工  $sub=false为下属范围  $sub=true为部门所有人
function getRoleByDepartmentId($department_id, $sub=false){
    $id_array = array($department_id);
    $departments = M('roleDepartment')->select($department_id);
    $where['position.department_id'] = $department_id;
    if(!$sub) $where['role.role_id'] = array('in', getSubRoleId());
    $roleList = D('RoleView')->where($where)->select();
    foreach($departments AS $value) {
        if ($department_id == $value['parent_id']) {
            $id_array[] = $value['department_id'];
            $role_list = getRoleByDepartmentId($value['department_id']);
            if(!$roleList){
                $roleList = $role_list;
            }
            if(!empty($role_list)){
                $roleList = array_merge($roleList, $role_list);
            }
        }
    }
    $result=array();
    for($i=0;$i<count($roleList);$i++){
        $source=$roleList[$i];
        if(array_search($source,$roleList)==$i && $source<>"" ){
            $result[]=$source;
        }
    }
    return $roleList;
}

/**
 * Warning提示信息
 * @param string $type 提示类型 默认支持success, error, info
 * @param string $msg 提示信息
 * @param string $url 跳转的URL地址
 * @param string $time 信息提示时间
 * @return void
 */
function alert($type='info', $msg='', $url='', $time=1000) {
    //多行URL地址支持
    $url        = str_replace(array("\n", "\r"), '', $url);
    $alert = unserialize(stripslashes(cookie('alert')));
    if (!empty($msg)) {
        $alert[$type][] = $msg;
        cookie('alert', serialize($alert));
    }
    cookie('alerttime', $time);

    if (empty($url)) {
        $url = U('index/index'); //定义无URL时的跳转地址
    }
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit();
    } else {
        $str    = "<meta http-equiv='Refresh' content='0;URL={$url}'>";
        exit($str);
    }
    return $alert;
}

function parseAlert() {
    $alert['content'] = unserialize(stripslashes(cookie('alert')));
    $alert['time'] = cookie('alerttime');
    cookie('alert', null);

    return $alert;
}

function getUserByRoleId($role_id){
    $role = D('RoleView')->where('role.role_id = %d', $role_id)->find();
    $role['img'] = headPathHandle($role['img']);
    $role['thumb_path'] = headPathHandle($role['thumb_path']);
    return $role;
}

/**
 * 根据role_id获取员工信息 数组
 */
function getUserByRoleIdArray($idArray = array())
{
    $roleList = array();
    $search_disable_user = M('Config')->where('name="search_disable_user"')->getField('value');
    foreach((array) $idArray as $roleId){
        $user_info = getUserByRoleId($roleId);
        if ($search_disable_user) {
            $roleList[$roleId] = $user_info;
        } elseif ($user_info['status'] != 2) {
            $roleList[$roleId] = $user_info;
        }
    }
    return $roleList;
}

function sendRequest($url, $params = array() , $headers = array()) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if (!empty($params)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    }
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $txt = curl_exec($ch);
    if (curl_errno($ch)) {
        $array[0] = 0;
        $array[1] = L("CONNECT TO A SERVER ERROR");
        $array[2] = -1;
        $return = $array;
    } else {
        $return = json_decode($txt, true);
        if (!$return) {
            $array[0] = 0;
            $array[1] = L("THE SERVER RETURNS DATA ANOMALIES");
            $array[2] = -1;
            $return = $array;
        }
    }

    return $return;
}

//生成评论提醒标题
function createCommentAlertInfo($module,$module_id){
    $author = D('RoleView')->where('role.role_id = %d', session('role_id'))->find();
    if($module == 'log'){
        $log = M('log')->where('log_id = %d', $module_id)->find();
        $title = L('LOG COMMENT TITLE',array($log['subject'],$author['user_name'],$author['department_name'],$author['role_name']));
    }elseif($module == 'task'){
        $task = M('task')->where('task_id = %d', $module_id)->find();
        $title = L('TASK COMMENT TITLE',array($task['subject'],$author['user_name'],$author['department_name'],$author['role_name']));
    }
    return $title;
}

//$sysMessage=1 为系统消息
//$send_type=1 为文本格式 2 附件格式
function sendMessage($id,$content,$sysMessage=0,$send_type){
    if(!$id) return false;
    if(!$content) return false;
    $m_message = M('message');
    $m_config = M('Config');
    if($sysMessage == 0) $data['from_role_id'] = session('role_id');
    $data['to_role_id'] = $id;
    if($send_type == 2){
        $data['file_id'] = $content;
        $data['content'] = '';
        $content = '附件格式';
    }else{
        $data['content'] = $content ? str_replace('/vue.php?','/index.php?',$content) : '';
    }
    //推送过滤html代码
    $appcontent = strip_tags($content);
    $appcontent = str_replace('&nbsp;', ' ', $appcontent);
    $num_id = $m_config->where('name = "num_id"')->getField('value');
    if($num_id){
        $ret = Xinge($num_id.'@'.$id,'您有新的消息!', msubstr($appcontent,  0, 30));
    }
    $data['read_time'] = 0;
    $data['send_time'] = time();
    return $m_message->add($data);
}

/*
	功能:发送邮件
	参数说明：  $to_role_id 收件人role_id
				$title 邮件主题
				$content 邮件内容
				$author 作者
*/
function sysSendEmail($to_role_id,$title,$content,$author){
    C(F('smtp'),'smtp');
    if(!$content) return false;
    if(!$to_role_id) return false;
    if(!$author) $author=C('defaultinfo.name').L('ADMIN');
    import('@.ORG.Mail');
    $to_user = D('RoleView')->where('role.role_id = %d', $to_role_id)->find();
    if(!is_email($to_user['email'])) return false;
    return SendMail($to_user['email'],$title,$content,$author);
}
function userSendEmail($address,$title,$content,$author=false){
    C(F('smtp'),'smtp');
    if(!$address) return false;
    if(!$content) return false;
    $content = preg_replace('/\\\\/','', $content);
    $userid = session('user_id');
    $user = M('user')->where(array('user_id'=>$userid))->find();
    if($author===true) $author=C('defaultinfo.name').'-'.$user['name'];
    else $author=C('defaultinfo.name');
    import('@.ORG.Mail');
    if(!is_email($address)) return false;
    return SendMail($address,$title,$content,$author);
}


function bsendemail($address,$title,$content,$file=array(),$author=false,$selfsmtp=false){
    if(!$address) return false;
    if(!$content) return false;
    $content = eregi_replace("[\]",'',$content);
    $userid = session('user_id');
    $user = M('user')->where(array('user_id'=>$userid))->find();
    if($author===true) $author=C('defaultinfo.name').'-'.$user['name'];
    else $author=C('defaultinfo.name')."";

    if($selfsmtp && $selfsmtp != '-1'){
        $smtp = M('UserSmtp')->where('smtp_id = %d', intval($selfsmtp))->find();
        C(unserialize($smtp['settinginfo']), 'smtp');
    }else{
        C(F('smtp'),'smtp');
    }

    import('@.ORG.Mail');
    $mail= new PHPMailer(true);
    try {
        $mail->IsSMTP();
        $mail->CharSet=C('MAIL_CHARSET');
        $mail->AddAddress($address);
        $mail->Body=$content;
        $mail->From= C('MAIL_ADDRESS');
        $mail->FromName=$author;
        $mail->Subject=$title;
        $mail->Host=C('MAIL_SMTP');
        $mail->Port=C('MAIL_PORT');
        $mail->SMTPAuth=C('MAIL_AUTH');
        $mail->SMTPSecure= C('MAIL_SECURE');
        $mail->Username=C('MAIL_LOGINNAME');
        $mail->Password=C('MAIL_PASSWORD');
        $mail->IsHTML(true);
        $mail->MsgHTML($content);
        ////对邮件正文进行重新编码，保证中文内容不乱码 如果正文引用该图片 就不会以附件形式存在 而是在正文中
        if(!empty($file)){
            foreach($file as $k=>$v){
                $mail->AddAttachment(ltrim($v,'/'));
            }
        }
        //$mail->AddAttachment($content); //上传附件内容
        return($mail->Send());
    } catch (phpmailerException $e) {
        // echo $e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (Exception $e) {
        //echo $e->getMessage(); //Boring error messages from anything else!
    }
}

function sysSendSms($to_role_id,$content){

    if(!$content) return false;
    if(!$to_role_id) return false;
    if(!$title) $title="系统通知";
    if(!$author) $author=C('defaultinfo.name').L('ADMIN');

    $to_user = D('RoleView')->where('role.role_id = %d', $to_role_id)->find();
    if(!is_email($to_user['email'])) return 100;
    return sendSMS($to_user['telephone'],$content,'sign_sysname');
}

function isMobile(){

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");

    $is_mobile = false;

    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            $is_mobile = true;
            break;
        }
    }

    return $is_mobile;
}

function is_utf8($liehuo_net){
    if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$liehuo_net) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$liehuo_net) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$liehuo_net) == true)
    {
        return true;
    }
    else
    {
        return false;
    }
}

//验重二维数组排序  $arr 数组 $keys比较的键值
function array_sort($arr,$keys,$type='asc'){
    $keysvalue = $new_array = array();
    foreach ($arr as $k=>$v){
        $keysvalue[$k] = $v[$keys];
    }
    if($type == 'asc'){
        asort($keysvalue);
    }else{
        arsort($keysvalue);
    }
    reset($keysvalue);
    $i = 0;
    foreach ($keysvalue as $k=>$v){
        if($i < 8 && $arr[$k][search] > 0){
            $new_array[] = $arr[$k]['value'];
            $i++;
        }

    }
    return $new_array;
}

/**
 * function	对二位数组进行指定规则的排序
 * return	排序后的二维数组
 * @arr 	传入要排序的数组
 * @keys	要排序的字段
 * @type	排序规则
 **/
function array_sorts($arr,$keys,$type='asc'){
    $keysvalue = $new_array = array();
    foreach ($arr as $k=>$v){
        $keysvalue[$k] = $v[$keys];
    }
    if($type == 'asc'){
        asort($keysvalue);
    }else{
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k=>$v){
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

/**
 *自定义字段html输出
 *$field为特殊验重字段，$d_module=($ModuelView)，$special为contacts时，用于客户添加时的联系人字段名处理
 *$status 自定义审批类型
 **/
function field_list_html($type="add",$module="",$d_module=array(),$special,$status=0){
    if ($type == "add") {
        if($module == 'customer'){
            $field_list = M('Fields')->where(array('model'=>$module,'in_add'=>1))->order('order_id')->select();
        }else{
            $where_field = array();
            $where_field['model'] = $module;
            $where_field['in_add'] = 1;
            $where_field['status'] = $status;
            $field_list = M('Fields')->where($where_field)->order('order_id')->select();
        }
    } else {
        $where_field = array();
        $where_field['model'] = $module;
        $where_field['status'] = $status;
        $field_list = M('Fields')->where($where_field)->order('order_id')->select();
    }
    foreach($field_list as $k=>$v){
        if(trim($v['input_tips'])){
            $input_tips = ' &nbsp; <span style="color:#999;float:left;margin-top:5px;">('.L('NOTE_').$v['input_tips'].')</span>';
        }else{
            $input_tips = '';
        }
        //客户添加页同时添加联系人时，处理联系人字段名，防止和客户重复
        if($special == 'contacts'){
            $v['field'] = "con_contacts[{$v['field']}]";
        }

        if('add' == $type){
            $value = $v['default_value'];
        } elseif ('edit' == $type && !empty($d_module)) {
            if($module == 'customer' && $type == 'edit' && $d_module['leads_id']){
                $value = isset($d_module[$v['field']]) ? $d_module[$v['field']] : $v['default_value'];
            }else{
                $value = isset($d_module[$v['field']]) ? $d_module[$v['field']] : '';
            }
        }

        if($d_module['customer_id']){
            $customer_id = intval($d_module['customer_id']);
        }else{
            $customer_id = intval($_GET['customer_id']);
        }

        if($customer_id){
            $customer = M('customer')->where('customer_id = %d', $customer_id)->find();
            if($d_module['contacts_id']){
                $contacts = M('contacts')->where('contacts_id = %d', $d_module['contacts_id'])->find();
            }else{
                $contacts = M('contacts')->where('contacts_id = %d', $customer['contacts_id'])->find();
            }
        }
        if ($v['field'] == 'customer_id') {
            if($customer_id){
                if (isMobile()){
                    $field_list[$k]['html'] = '<div class="form-inline"><input type="hidden" name="'.$v['field'].'" id="customer_id" value="'.$customer['customer_id'].'"/>
				<input  type="text" class="txt ptxt100p" style="width:100%;cursor:pointer;" readonly="true" title="点击选择" name="customer_name" id="customer_name" value="'.$customer['name'].'"/>
				 </div>';
                }else {
                    $field_list[$k]['html'] = '<div class="form-inline"><input type="hidden" name="' . $v['field'] . '" id="customer_id" value="' . $customer['customer_id'] . '"/>
                    <input  type="text" class="form-control required pull-left" style="width:100%;cursor:pointer;" readonly="true" title="点击选择" name="customer_name" id="customer_name" value="' . $customer['name'] . '"/> <a target="_blank" class="btn btn-primary btn-sm pull-right" style="width:65px;display:none;" href="' . U('customer/add') . '">' . L('CREATE NEW CUSTOMER') . '</a></div>';
                }
            } else{
                if (isMobile()){
                    $field_list[$k]['html'] = '<div class="form-inline"><input type="hidden" name="'.$v['field'].'" id="customer_id"/>
                    <input  class="txt ptxt100p" style="width:100%;cursor:pointer;" readonly="true" title="点击选择" type="text" name="customer_name" id="customer_name"> 
                   </div>';
                }else {
                    $field_list[$k]['html'] = '<div class="form-inline"><input type="hidden" name="' . $v['field'] . '" id="customer_id"/><input  class="form-control required pull-left" style="width:100%;cursor:pointer;" readonly="true" title="点击选择" type="text" name="customer_name" id="customer_name"> <a target="_blank" class="btn btn-primary btn-sm pull-right" style="width:65px;display:none;" href="' . U('customer/add') . '">' . L('CREATE NEW CUSTOMER') . '</a></div>';
                }
            }
        }elseif($v['field'] == 'contacts_id'){
            if($customer_id){
                if (isMobile()){
                    $field_list[$k]['html'] = '<input type="hidden" name="contacts_id" id="contacts_id" value="'.$contacts['contacts_id'].'"/><input  type="text" class="txt ptxt100p" name="contacts_name" id="contacts_name" value="'.$contacts['name'].'"/>';
                }else {
                    $field_list[$k]['html'] = '<input type="hidden" name="contacts_id" id="contacts_id" value="' . $contacts['contacts_id'] . '"/><input  type="text" class="form-control" name="contacts_name" id="contacts_name" value="' . $contacts['name'] . '"/>';
                }
            }else{
                if (isMobile()){
                    $field_list[$k]['html'] = '<input type="hidden" name="contacts_id" id="contacts_id"/><input  type="text" class="txt ptxt100p" name="contacts_name" id="contacts_name"/>';
                }else {
                    $field_list[$k]['html'] = '<input type="hidden" name="contacts_id" id="contacts_id"/><input  type="text" class="form-control" name="contacts_name" id="contacts_name"/>';
                }
            }
        }elseif($module == 'customer' && $v['field'] == 'tags'){
            //客户标签
            $tagsStr = substr($d_module['tags'], '1', strlen($d_module['tags'])-2);
            if($tagsStr !=''){
                $tagsName = M('tags')->where('tags_id in ( %s )', $tagsStr)->getField('name',true);
            }
            $field_list[$k]['html'] = '<input type="text" name="tags" id="tags" value="'.implode(',', $tagsName).'" />';
        }else{
            //验证
            $required = '';
            $maxlength = '';
            $tip_start = 0;
            //是否为空
            if($v['is_validate'] == 1 && $v['is_null'] == 1){
                $required = 'required';
                $tip_start = 1;
                $field_list[$k]['tip_start'] = $tip_start;
            }
            //是否验重
            $field_onblur = '';
            if($v['is_validate'] == 1 && $v['is_recheck'] == 1){
                //调用查重check方法的结果展示控制
                $field_name = '';
                $field_name = "'".$v['field']."'";
                if (($module == 'customer' && $v['field'] == 'name') || ($module == 'leads' && $v['field'] == 'name')) {
                    $field_onblur = 'onblur="checkinfo('.$field_name.')" is_check="1"';
                } else {
                    $field_onblur = 'onblur="checkinfo('.$field_name.')"';
                }
            }

            if($special == 'contacts'){
                $required = '';
            }
            if(!empty($v['maxlength'])){
                $maxlength = 'maxlength='.'"'.$v['maxlength'].'"';
            }
            if (isMobile()) {
                switch ($v['form_type']) {
                    case 'textarea' :
                        $field_list[$k]['html'] = '<textarea rows="5" class="txt ptxt100p'.$required.'" id="'.$v['field'].'" name="'.$v['field'].'" >'.$value.'</textarea>
                                                    <span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;
                        break;
                    case 'box' :
                        $setting_str = '$setting='.$v['setting'].';';
                        eval($setting_str);

                        $field_list[$k]['setting'] = $setting;
                        if ($setting['type'] == 'select' or $setting['type'] == 'radio') {
                            $str = '';
                            $str .= "<option value=''>--".无."--</option>";
                            foreach ($setting['data'] as $v2) {
                                $str .= "<option value='$v2'";
                                $str .= $value == $v2 ? ' selected="selected" ':'';
                                $str .= ">$v2</option>";
                            }
                            $field_list[$k]['html'] = '<select class="pbsele w100p '.$required.'" id="'.$v['field'].'" name="'.$v['field'].'">'.$str.'</select>
                                                    <span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;
                            break;
                        }/* elseif ($setting['type'] == 'radio') {
                            $str = '';
                            $i = '';
                            foreach ($setting['data'] as $v2) {
                                $str .= " &nbsp; <div class='radio radio-info radio-inline'><input type='radio' name='".$v['field']."' id='".$v['field'].$i."' value='$v2'";
                                $str .= $value == $v2 ? ' checked="checked"':'';
                                $str .= "/><label for='".$v['field'].$i."'>$v2</label></div>";
                                $i++;
                            }
                            $field_list[$k]['html'] = '&nbsp;&nbsp;&nbsp;'.$str.'<span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;
                            break;
                        }*/ elseif ($setting['type'] == 'checkbox') {
                            $str = '';
                            $i = '';
                            foreach ($setting['data'] as $v2) {
                                $str .= " &nbsp; <div class='checkbox checkbox-info' style='float:left;'><input type='checkbox' name='".$v['field']."[]' id='".$v['field'].$i."' value='$v2'";
                                if(strstr($value,$v2)){
                                    $str .= ' checked="checked"';
                                }
                                $str .= '/>&nbsp;<label for="'.$v['field'].$i.'">' .$v2.'</label></div>';
                                $i++;
                            }
                            $field_list[$k]['html'] = $str.' <span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>&nbsp; '.$input_tips;
                            break;
                        }
                        break;
                    case 'datetime' :
                        if($v['field'] == 'nextstep_time'){
                            $time_accuracy = 'yyyy-MM-dd HH:mm';
                            $temp_value = date('Y-m-d H:i', $value);
                        }else{
                            $time_accuracy = 'yyyy-MM-dd';
                            $temp_value = date('Y-m-d', $value);
                        }
                        $field_list[$k]['html'] = '<input class="txt ptxt100p '.$required.'" input_type="time" name="'.$v['field'].'" placeholder="请输入下次联系时间" id="USER_AGE" type="text" value="'.$temp_value.'"/><span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;
                        break;
                    case 'number' :
                        $field_list[$k]['html'] = '<input class="form-control digits '.$required.'" type="text" data-type="nummber" onkeyup="num_input(this)" '.$field_onblur.' id="'.$v['field'].'" name="'.$v['field'].'" '.$maxlength.' value="'.$value.'"/><span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;
                        break;
                    case 'floatnumber' :
                        $value = $value > 0 ? $value : '';
                        $field_list[$k]['html'] = '<input class="form-control number '.$required.'" type="text" id="'.$v['field'].'" name="'.$v['field'].'" value="'.$value.'" onblur="bu(this)" onkeyup="num_input(this)" '.$field_onblur.' /><span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;
                        break;
                    case 'address':
                        if('add' == $type){
                            $defaultinfo = unserialize(M('Config')->where('name = "defaultinfo"')->getField('value'));
                            $state = $defaultinfo['state'];
                            $city = $defaultinfo['city'];
                            $area = $defaultinfo['area'];
                        }else{
                            $address_array = explode(chr(10),$value);
                            $state = $address_array[0];
                            $city = $address_array[1];
                            $area = $address_array[2];
                            $street = $address_array[3];
                        }
                        $field_list[$k]['html'] = '<script type="text/javascript">
					$(function(){
						new PCAS("'.$v['field'].'[state]","'.$v['field'].'[city]","'.$v['field'].'[area]","'.$state.'","'.$city.'","'.$area.'");
					});
					</script><select class="pbsele w50p abe-fl " input_type="address" name="'.$v['field'].'[state]" style="width:32%;float:left;" ></select>
						<select class="pbsele w50p abe-fl " input_type="address" name="'.$v['field'].'[city]" style="width:32%;float:left;margin-left:1%;"></select>
						<select class="pbsele w50p abe-fl " input_type="address" name="'.$v['field'].'[area]" style="width:32%;float:left;margin-left:1%;"></select>
						<input class="txt ptxt100p" input_type="address" type="text" name="'.$v['field'].'[street]" placeholder="'.L('请输入详细地址').'" class="txt ptxt100p" value="'.$street.'" style="float:left;margin-top:5px;"></br></br>';
                        break;
                    case 'p_box':
                        $str = '';
                        $category = M('product_category');
                        $category_list = $category->select();
                        $categoryList = getSubCategory(0, $category_list, '');
                        foreach ($categoryList as $v2) {
                            $checked = '';
                            if($v2['category_id'] == $value){
                                $checked = 'selected="selected"';
                            }
                            $str .= "<option $checked value=".$v2['category_id'].">".$v2['name']."</option>";

                        }
                        $field_list[$k]['html'] = '<select class="form-control '.$required.'" id="'.$v['field'].'" name="'.$v['field'].'">'.$str.'</select><span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;

                        break;
                    case 'b_box':
                        $status = M('BusinessStatus')->order('order_id')->select();
                        $str = '';
                        foreach ($status as $v2) {
                            $checked = '';
                            if($v2['status_id'] == $value){
                                $checked = 'selected="selected"';
                            }
                            $str .= "<option $checked value='".$v2['status_id']."'>".$v2['name']."</option>";
                        }
                        $field_list[$k]['html'] = '<select class="form-control '.$required.'" id="'.$v['field'].'" name="'.$v['field'].'">'.$str.'</select><span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;
                        break;
                    case 'email':
                        $field_list[$k]['html'] = '<input class="txt ptxt100p '.$required.'" type="text" id="email" name="'.$v['field'].'" '.$maxlength.' '.$field_onblur.' placeholder="请输入email" value="'.$value.'" /><span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;
                        break;
                    case 'mobile':
                        $m_type = ($module == 'contacts') ? 'phone' : 'mobile';
                        $field_list[$k]['html'] = '<input class="txt ptxt100p '.$required.'" type="'. $m_type .'" id="'.$v['field'].'" name="'.$v['field'].'" '.$maxlength.' '.$field_onblur.' placeholder="请输入电话号码" value="'.$value.'" οnblur="valiMobile(this.value)" /><span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;
                        break;
                    default:
                        if ($v['field'] == 'create_time' || $v['field'] == 'update_time') {
                            break;
                        }else{
                            $customer_id = intval($_GET['customer_id']);

                            if($v['field'] == 'name' && $customer_id && $module == 'customer') {
                                $value=M('customer')->where('customer_id = %d', $customer_id)->getField('name');
                            }
                            if($special == 'contacts' && $v['field'] == 'con_contacts[name]'){
                                /*$input_tips = ' &nbsp; <span style="color:#999;float:left;margin-top:5px;">如有联系人信息，则联系人姓名不能为空</span>';*/
                            }
                            $field_list[$k]['html'] = '<input class="txt ptxt100p"  type="text" id="'.$v['field'].'" name="'.$v['field'].'"  value="'.$value.'" placeholder="请输入'.$v['name'].'"/>
						<span id="'.$v['field'].'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>'.$input_tips;
                        }
                        break;
                }
            }else {
                switch ($v['form_type']) {
                    case 'textarea' :
                        $field_list[$k]['html'] = '<textarea rows="5" class="form-control ' . $required . '" id="' . $v['field'] . '" name="' . $v['field'] . '" >' . $value . '</textarea><span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;
                        break;
                    case 'box' :
                        $setting_str = '$setting=' . $v['setting'] . ';';
                        eval($setting_str);

                        $field_list[$k]['setting'] = $setting;
                        if ($setting['type'] == 'select') {
                            $str = '';
                            $str .= "<option value=''>--" . L('PLEASE CHOOSE') . "--</option>";
                            foreach ($setting['data'] as $v2) {
                                $str .= "<option value='$v2'";
                                $str .= $value == $v2 ? ' selected="selected" ' : '';
                                $str .= ">$v2</option>";
                            }
                            $field_list[$k]['html'] = '<select class="form-control ' . $required . '" id="' . $v['field'] . '" name="' . $v['field'] . '">' . $str . '</select><span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;
                            break;
                        } elseif ($setting['type'] == 'radio') {
                            $str = '';
                            $i = '';
                            foreach ($setting['data'] as $v2) {
                                $str .= " &nbsp; <div class='radio radio-info radio-inline'><input type='radio' name='" . $v['field'] . "' id='" . $v['field'] . $i . "' value='$v2'";
                                $str .= $value == $v2 ? ' checked="checked"' : '';
                                $str .= "/><label for='" . $v['field'] . $i . "'>$v2</label></div>";
                                $i++;
                            }
                            $field_list[$k]['html'] = '&nbsp;&nbsp;&nbsp;' . $str . '<span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;
                            break;
                        } elseif ($setting['type'] == 'checkbox') {
                            $str = '';
                            $i = '';
                            foreach ($setting['data'] as $v2) {
                                $str .= " &nbsp; <div class='checkbox checkbox-info' style='float:left;'><input type='checkbox' name='" . $v['field'] . "[]' id='" . $v['field'] . $i . "' value='$v2'";
                                if (strstr($value, $v2)) {
                                    $str .= ' checked="checked"';
                                }
                                $str .= '/>&nbsp;<label for="' . $v['field'] . $i . '">' . $v2 . '</label></div>';
                                $i++;
                            }
                            $field_list[$k]['html'] = $str . ' <span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>&nbsp; ' . $input_tips;
                            break;
                        }
                        break;
                    case 'datetime' :
                        if ($v['field'] == 'nextstep_time') {
                            $time_accuracy = 'yyyy-MM-dd HH:mm';
                            $temp_value = date('Y-m-d H:i', $value);
                        } else {
                            $time_accuracy = 'yyyy-MM-dd';
                            $temp_value = date('Y-m-d', $value);
                        }
                        $field_list[$k]['html'] = '<input class="form-control Wdate ' . $required . '" input_type="time" onFocus="WdatePicker({dateFmt:\'' . $time_accuracy . '\'})" name="' . $v['field'] . '" id="' . $v['field'] . '" type="text" value="' . $temp_value . '"/><span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;
                        break;
                    case 'number' :
                        $field_list[$k]['html'] = '<input class="form-control digits ' . $required . '" type="text" data-type="nummber" onkeyup="num_input(this)" ' . $field_onblur . ' id="' . $v['field'] . '" name="' . $v['field'] . '" ' . $maxlength . ' value="' . $value . '"/><span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;
                        break;
                    case 'floatnumber' :
                        $value = $value > 0 ? $value : '';
                        $field_list[$k]['html'] = '<input class="form-control number ' . $required . '" type="text" id="' . $v['field'] . '" name="' . $v['field'] . '" value="' . $value . '" onblur="bu(this)" onkeyup="num_input(this)" ' . $field_onblur . ' /><span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;
                        break;
                    case 'address':
                        if ('add' == $type) {
                            $defaultinfo = unserialize(M('Config')->where('name = "defaultinfo"')->getField('value'));
                            $state = $defaultinfo['state'];
                            $city = $defaultinfo['city'];
                            $area = $defaultinfo['area'];
                        } else {
                            $address_array = explode(chr(10), $value);
                            $state = $address_array[0];
                            $city = $address_array[1];
                            $area = $address_array[2];
                            $street = $address_array[3];
                        }
                        $field_list[$k]['html'] = '<script type="text/javascript">
					$(function(){
						new PCAS("' . $v['field'] . '[state]","' . $v['field'] . '[city]","' . $v['field'] . '[area]","' . $state . '","' . $city . '","' . $area . '");
					});
					</script><select class="form-control " input_type="address" name="' . $v['field'] . '[state]" style="width:32%;float:left;" ></select>
						<select class="form-control " input_type="address" name="' . $v['field'] . '[city]" style="width:32%;float:left;margin-left:1%;"></select>
						<select class="form-control " input_type="address" name="' . $v['field'] . '[area]" style="width:32%;float:left;margin-left:1%;"></select>
						<input class="form-control" input_type="address" type="text" name="' . $v['field'] . '[street]" placeholder="' . L('THE STREET INFORMATION') . '" class="input-large" value="' . $street . '" style="float:left;margin-top:5px;">';
                        break;
                    case 'p_box':
                        $str = '';
                        $category = M('product_category');
                        $category_list = $category->select();
                        $categoryList = getSubCategory(0, $category_list, '');
                        foreach ($categoryList as $v2) {
                            $checked = '';
                            if ($v2['category_id'] == $value) {
                                $checked = 'selected="selected"';
                            }
                            $str .= "<option $checked value=" . $v2['category_id'] . ">" . $v2['name'] . "</option>";

                        }
                        $field_list[$k]['html'] = '<select class="form-control ' . $required . '" id="' . $v['field'] . '" name="' . $v['field'] . '">' . $str . '</select><span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;

                        break;
                    case 'b_box':
                        $status = M('BusinessStatus')->order('order_id')->select();
                        $str = '';
                        foreach ($status as $v2) {
                            $checked = '';
                            if ($v2['status_id'] == $value) {
                                $checked = 'selected="selected"';
                            }
                            $str .= "<option $checked value='" . $v2['status_id'] . "'>" . $v2['name'] . "</option>";
                        }
                        $field_list[$k]['html'] = '<select class="form-control ' . $required . '" id="' . $v['field'] . '" name="' . $v['field'] . '">' . $str . '</select><span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;
                        break;
                    case 'email':
                        $field_list[$k]['html'] = '<input class="form-control ' . $required . '" type="email" id="' . $v['field'] . '" name="' . $v['field'] . '" ' . $maxlength . ' ' . $field_onblur . ' value="' . $value . '" /><span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;
                        break;
                    case 'mobile':
                        $m_type = ($module == 'contacts') ? 'phone' : 'mobile';
                        $field_list[$k]['html'] = '<input class="form-control ' . $required . '" type="' . $m_type . '" id="' . $v['field'] . '" name="' . $v['field'] . '" ' . $maxlength . ' ' . $field_onblur . ' value="' . $value . '" /><span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;
                        break;
                    default:
                        if ($v['field'] == 'create_time' || $v['field'] == 'update_time') {
                            break;
                        } else {
                            $customer_id = intval($_GET['customer_id']);

                            if ($v['field'] == 'name' && $customer_id && $module == 'customer') {
                                $value = M('customer')->where('customer_id = %d', $customer_id)->getField('name');
                            }
                            if ($special == 'contacts' && $v['field'] == 'con_contacts[name]') {
                                $input_tips = ' &nbsp; <span style="color:#999;float:left;margin-top:5px;">如有联系人信息，则联系人姓名不能为空</span>';
                            }
                            $field_list[$k]['html'] = '<input class="form-control ' . $required . '"  type="text" id="' . $v['field'] . '" name="' . $v['field'] . '" ' . $maxlength . ' ' . $field_onblur . ' value="' . $value . '"/><span id="' . $v['field'] . 'Tip" style="float: left;line-height: 32px;margin-left: 5%;color:red;"></span>' . $input_tips;
                        }
                        break;
                }
            }
        }
        if($field_list[$k]['is_main'] == 1){
            $fieldlist['main'][] = $field_list[$k];
        }else{
            $fieldlist['data'][] = $field_list[$k];
        }
    }
    return $fieldlist;
}

//自定义字段html输出     $field为特殊验重字段   $d_module=($ModuelView)
function field_list_mobile_html($type="add",$module="",$d_module=array()){
    if ($type == "add") {
        $field_list = M('Fields')->where('model = "'.$module.'" and in_add = 1')->order('order_id')->select();
    } else {
        $field_list = M('Fields')->where('model = "'.$module.'"')->order('order_id')->select();
    }

    foreach($field_list as $k=>$v){
        if(trim($v['input_tips'])){
            //$input_tips = ' &nbsp; <span style="color:#005580;">('.L('NOTE_').$v['input_tips'].')</span>';
            $input_tips = $v['input_tips'];
        }else{
            $input_tips = '';
        }
        if('add' == $type){
            $value = $v['default_value'];
        } elseif ('edit' == $type && !empty($d_module)) {
            $value = $d_module[$v['field']] !== '' ? $d_module[$v['field']] : '';
        }

        if($d_module['customer_id']){
            $customer_id = intval($d_module['customer_id']);
        }else{
            $customer_id = intval($_GET['customer_id']);
        }

        if($customer_id){
            $customer = M('customer')->where('customer_id = %d', $customer_id)->find();
            if($d_module['contacts_id']){
                $contacts = M('contacts')->where('contacts_id = %d', $d_module['contacts_id'])->find();
            }else{
                $contacts = M('contacts')->where('contacts_id = %d', $customer['contacts_id'])->find();
            }
        }
        if ($v['field'] == 'customer_id') {
            if($customer_id){
                $field_list[$k]['html'] = '<input type="hidden" name="'.$v['field'].'" id="customer_id" value="'.$customer['customer_id'].'"/><input  type="text" name="customer_name" id="customer_name" value="'.$customer['name'].'"/></br><a target="_blank" href="'.U('customer/add').'">'.L('CREATE NEW CUSTOMER').'</a>';

            }else{
                $field_list[$k]['html'] = '<input type="hidden" name="'.$v['field'].'" id="customer_id"/><input  type="text" name="customer_name" id="customer_name"></br><a target="_blank" href="add_customer:business">'.L('CREATE NEW CUSTOMER').'</a>';
            }
        }elseif($v['field'] == 'contacts_id'){
            if($customer_id){
                $field_list[$k]['html'] = '<input type="hidden" name="contacts_id" id="contacts_id" value="'.$contacts['contacts_id'].'"/><input  type="text" name="contacts_name" id="contacts_name" value="'.$contacts['name'].'"/>';
            }else{
                $field_list[$k]['html'] = '<input type="hidden" name="contacts_id" id="contacts_id"/><input  type="text" name="contacts_name" id="contacts_name"/>';
            }
        }elseif($module == 'customer' && $v['field'] == 'tags'){
            //客户标签
            $tagsStr = substr($d_module['tags'], '1', strlen($d_module['tags'])-2);
            if($tagsStr !=''){
                $tagsName = M('tags')->where('tags_id in ( %s )', $tagsStr)->getField('name',true);
            }
            $field_list[$k]['html'] = '<input type="text" name="tags" id="tags" value="'.implode(',', $tagsName).'" />';
        }else{
            switch ($v['form_type']) {
                case 'textarea' :
                    $field_list[$k]['html'] = '<textarea  rows="6"  placeholder="'.$input_tips.'" class="span6" id="'.$v['field'].'" name="'.$v['field'].'" >'.$value.'</textarea><span id="'.$v['field'].'Tip" style="color:red;"></span>';
                    break;
                case 'box' :
                    $setting_str = '$setting='.$v['setting'].';';
                    eval($setting_str);

                    $field_list[$k]['setting'] = $setting;
                    if ($setting['type'] == 'select') {
                        $str = '';
                        $str .= "<option value=''>--请选择--</option>";
                        foreach ($setting['data'] as $v2) {
                            $str .= "<option value='$v2'";
                            $str .= $d_module[$v['field']] == $v2 ? ' selected="selected" ':'';
                            $str .= ">$v2</option>";
                        }
                        $field_list[$k]['html'] = '<select style="width:80%" id="'.$v['field'].'" name="'.$v['field'].'">'.$str.'</select><span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                        break;
                    } elseif ($setting['type'] == 'radio') {
                        $str = '';
                        $str .= "<option value=''>--请选择--</option>";
                        foreach ($setting['data'] as $v2) {
                            $str .= "<option value='$v2'";
                            $str .= $d_module[$v['field']] == $v2 ? ' selected="selected" ':'';
                            $str .= ">$v2</option>";
                        }
                        $field_list[$k]['html'] = '<select style="width:80%" id="'.$v['field'].'" name="'.$v['field'].'">'.$str.'</select><span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                        break;

                    } elseif ($setting['type'] == 'checkbox') {
                        $str = '';
                        $i = '';
                        foreach ($setting['data'] as $v2) {
                            $str .= " &nbsp; <input type='checkbox' name='".$v['field']."[]' id='".$v['field'].$i."' value='$v2'";
                            if(strstr($d_module[$v['field']],$v2)){
                                $str .= ' checked="checked"';
                            }
                            $str .= '/>&nbsp;' .$v2;
                            $i++;
                        }
                        $field_list[$k]['html'] = $str.' <span id="'.$v['field'].'Tip" style="color:red;"></span>&nbsp; '.$input_tips;
                        break;
                    }
                    break;
                case 'editor' :
                    if($type == 'edit' ){
                        $field_list[$k]['html'] = '<input type="text" readonly="true" placeholder="--暂不支持该类型--" />';
                        break;
                    }else{
                        $field_list[$k]['html'] = '<textarea  rows="6"  placeholder="'.$input_tips.'" class="span6" id="'.$v['field'].'" name="'.$v['field'].'" >'.$value.'</textarea><span id="'.$v['field'].'Tip" style="color:red;"></span>';
                        break;
                    }
                case 'datetime' :
                    if($v['field'] == 'nextstep_time'){
                        $time_accuracy = 'yyyy-MM-dd HH:mm';
                    }else{
                        $time_accuracy = 'yyyy-MM-dd';
                    }
                    $field_list[$k]['html'] = '<input readonly="true"  onFocus="WdatePicker({dateFmt:\''.$time_accuracy.'\'})" name="'.$v['field'].'" id="'.$v['field'].'" type="text" value="'.pregtime($value).'"/></br><span id="'.$v['field'].'Tip" style="color:red;"></span>';
                    break;
                case 'number' :
                    $field_list[$k]['html'] = '<input type="text"  placeholder="'.$input_tips.'"  id="'.$v['field'].'" name="'.$v['field'].'" maxlength="'.$v['maxlength'].'" value="'.$value.'"/></br><span id="'.$v['field'].'Tip" style="color:red;"></span>';
                    break;
                case 'floatnumber' :
                    $value = $value > 0 ? $value : '';
                    $field_list[$k]['html'] = '<input type="text" placeholder="'.$input_tips.'" id="'.$v['field'].'" name="'.$v['field'].'" value="'.$value.'"/></br>  <span id="'.$v['field'].'Tip" style="color:red;"></span>';
                    break;
                case 'address':
                    if('add' == $type){
                        $defaultinfo = unserialize(M('Config')->where('name = "defaultinfo"')->getField('value'));
                        $state = $defaultinfo['state'];
                        $city = $defaultinfo['city'];
                        $area = $defaultinfo['area'];
                    }else{
                        $address_array = explode(chr(10),$value);
                        $state = $address_array[0];
                        $city = $address_array[1];
                        $area = $address_array[2];
                        $street = $address_array[3];
                    }
                    $field_list[$k]['html'] = '<script type="text/javascript">
					$(function(){
						new PCAS("'.$v['field'].'[\'state\']","'.$v['field'].'[\'city\']","'.$v['field'].'[\'area\']","'.$state.'","'.$city.'","'.$area.'");
					});
					</script><select name="'.$v['field'].'[\'state\']" class="input-medium"></select>
						<select name="'.$v['field'].'[\'city\']" class="input-medium"></select>
						<select name="'.$v['field'].'[\'area\']" class="input-medium"></select>
						<input type="text" name="'.$v['field'].'[\'street\']" placeholder="'.L('THE STREET INFORMATION').'" class="input-large" value="'.$street.'">';
                    break;
                case 'p_box':
                    $str = '';
                    $category = M('product_category');
                    $category_list = $category->select();
                    $categoryList = getSubCategory(0, $category_list, '');
                    foreach ($categoryList as $v2) {
                        $checked = '';
                        if($v2['category_id'] == $value){
                            $checked = 'selected="selected"';
                        }
                        $str .= "<option $checked value=".$v2['category_id'].">".$v2['name']."</option>";

                    }
                    $field_list[$k]['html'] = '<select style="width:80%" id="'.$v['field'].'" name="'.$v['field'].'">'.$str.'</select><span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;

                    break;
                case 'b_box':
                    $status = M('BusinessStatus')->order('order_id')->select();
                    $str = '';
                    foreach ($status as $v2) {
                        $checked = '';
                        if($v2['status_id'] == $value){
                            $checked = 'selected="selected"';
                        }
                        $str .= "<option $checked value='".$v2['status_id']."'>".$v2['name']."</option>";
                    }
                    $field_list[$k]['html'] = '<select style="width:80%" id="'.$v['field'].'" name="'.$v['field'].'">'.$str.'</select><span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                    break;
                default:
                    if ($v['field'] == 'create_time' || $v['field'] == 'update_time') {
                        break;
                    }else{
                        $customer_id = intval($_GET['customer_id']);
                        if($v['field'] == 'name' && $customer_id && $module == 'customer') $value=M('customer')->where('customer_id = %d', $customer_id)->getField('name');
                        $field_list[$k]['html'] = '<input type="text"  placeholder="'.$input_tips.'" id="'.$v['field'].'" name="'.$v['field'].'" maxlength="'.$v['maxlength'].'" value="'.$value.'"/></br><span id="'.$v['field'].'Tip" style="color:red;"></span>';
                    }
                    break;
            }
        }
        if($field_list[$k]['is_main'] == 1){
            $fieldlist['main'][] = $field_list[$k];
        }else{
            $fieldlist['data'][] = $field_list[$k];
        }
    }
    return $fieldlist;
}

/*
	返回码说明 短信函数返回1发送成功  0进入审核阶段 -4手机号码不正确
*/
//单条短信
//发送到目标手机号码 $telphone手机号码 $message短信内容
function sendSMS($telphone, $message, $sign_name="sign_name",$sendtime=''){
    $flag = 0;
    $sms = F('sms');
    $argv = array(
        'sn'=>$sms['uid'],
        'pwd'=>strtoupper(md5($sms['uid'].$sms['passwd'])),
        'mobile'=>$telphone,
        'content'=>urlencode($message.'【'.$sms[$sign_name].'】'),
        'ext'=>'',
        'rrid'=>'',
        'stime'=>$sendtime
    );
    foreach ($argv as $key=>$value) {
        if ($flag!=0) {
            $params .= "&";
            $flag = 1;
        }
        $params.= $key."="; $params.= urlencode($value);
        $flag = 1;
    }
    $length = strlen($params);
    $fp = fsockopen("sdk2.entinfo.cn",8060,$errno,$errstr,10) or exit($errstr."--->".$errno);
    $header = "POST /webservice.asmx/mdSmsSend_u HTTP/1.1\r\n";
    $header .= "Host:sdk2.entinfo.cn\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: ".$length."\r\n";
    $header .= "Connection: Close\r\n\r\n";
    $header .= $params."\r\n";
    fputs($fp,$header);
    $inheader = 1;
    while (!feof($fp)) {
        $line = fgets($fp,1024);
        if ($inheader && ($line == "\n" || $line == "\r\n")) {
            $inheader = 0;
        }
        if ($inheader == 0) {
        }
    }


    preg_match('/<string xmlns=\"http:\/\/tempuri.org\/\">(.*)<\/string>/',$line,$str);
    $result=explode("-",$str[1]);



    if(count($result)>1){
        //echo '发送失败返回值为:'.$line."请查看webservice返回值";
        return $line;
    }else{
        //echo '发送成功 返回值为:'.$line;
        return 1;
    }
}
function sendtestSMS($uid, $uname, $telphone){
    $flag = 0;
    $sms = F('sms');
    $argv = array(
        'sn'=>$uid,
        'pwd'=>strtoupper(md5($uid.$uname)),
        'mobile'=>$telphone,
        'content'=>urlencode('TEST SMS 【PDCRM】'),
        'ext'=>'',
        'rrid'=>'',
        'stime'=>$sendtime

    );
    foreach ($argv as $key=>$value) {
        if ($flag!=0) {
            $params .= "&";
            $flag = 1;
        }
        $params.= $key."="; $params.= urlencode($value);
        $flag = 1;
    }
    $length = strlen($params);
    $fp = fsockopen("sdk2.entinfo.cn",8060,$errno,$errstr,10) or exit($errstr."--->".$errno);
    $header = "POST /webservice.asmx/mdSmsSend_u HTTP/1.1\r\n";
    $header .= "Host:sdk2.entinfo.cn\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: ".$length."\r\n";
    $header .= "Connection: Close\r\n\r\n";
    $header .= $params."\r\n";
    fputs($fp,$header);
    $inheader = 1;
    while (!feof($fp)) {
        $line = fgets($fp,1024);

        if ($inheader && ($line == "\n" || $line == "\r\n")) {
            $inheader = 0;
        }
        if ($inheader == 0) {
        }
    }
    preg_match('/<string xmlns=\"http:\/\/tempuri.org\/\">(.*)<\/string>/',$line,$str);
    $result=explode("-",$str[1]);
    if(count($result)>1){
        //echo '发送失败返回值为:'.$line."请查看webservice返回值";
        return $line;
    }else{
        //echo '发送成功 返回值为:'.$line;
        return 1;
    }
}

//多条短信 最多600条
//发送到目标手机号码字符串 用","隔开 $telphone手机号码 $message短信内容
function sendGroupSMS($telphone, $message, $sign_name="sign_name",$sendtime=''){
    $flag = 0;
    $sms = F('sms');
    //要post的数据
    $argv = array(
        'sn'=>$sms['uid'], ////替换成您自己的序列号
        'pwd'=>strtoupper(md5($sms['uid'].$sms['passwd'])), //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
        'mobile'=>$telphone,//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
        'content'=>urlencode($message.'【'.$sms[$sign_name].'】'),//短信内容
        'ext'=>'',
        'rrid'=>'',//默认空 如果空返回系统生成的标识串 如果传值保证值唯一 成功则返回传入的值
        'stime'=>$sendtime//定时时间 格式为2011-6-29 11:09:21

    );
    //构造要post的字符串
    foreach ($argv as $key=>$value) {
        if ($flag!=0) {
            $params .= "&";
            $flag = 1;
        }
        $params.= $key."="; $params.= urlencode($value);
        $flag = 1;
    }
    $length = strlen($params);
    //创建socket连接
    $fp = fsockopen("sdk2.entinfo.cn",8060,$errno,$errstr,10) or exit($errstr."--->".$errno);
    //构造post请求的头
    $header = "POST /webservice.asmx/mdSmsSend_u HTTP/1.1\r\n";
    $header .= "Host:sdk2.entinfo.cn\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: ".$length."\r\n";
    $header .= "Connection: Close\r\n\r\n";
    //添加post的字符串
    $header .= $params."\r\n";
    //发送post的数据
    fputs($fp,$header);
    $inheader = 1;
    while (!feof($fp)) {
        $line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据
        if ($inheader && ($line == "\n" || $line == "\r\n")) {
            $inheader = 0;
        }
        if ($inheader == 0) {
            // echo $line;
        }
    }


    preg_match('/<string xmlns=\"http:\/\/tempuri.org\/\">(.*)<\/string>/',$line,$str);
    $result=explode("-",$str[1]);



    if(count($result)>1){
        //echo '发送失败返回值为:'.$line."请查看webservice返回值";
        return $line;
    }else{
        //echo '发送成功 返回值为:'.$line;
        return 1;
    }
}
function getSmsNum(){
    $sms = F('sms');

    $flag = 0;
    //要post的数据
    $argv = array(
        'sn'=>$sms['uid'], //替换成您自己的序列号

        'pwd'=>$sms['passwd'],//替换成您自己的密码
    );
    //构造要post的字符串
    foreach ($argv as $key=>$value) {
        if ($flag!=0) {
            $params .= "&";
            $flag = 1;
        }
        $params.= $key."="; $params.= urlencode($value);
        $flag = 1;
    }
    $length = strlen($params);
    //创建socket连接
    $fp = fsockopen("sdk2.entinfo.cn",8060,$errno,$errstr,10) or exit($errstr."--->".$errno);
    //构造post请求的头
    $header = "POST /webservice.asmx/GetBalance HTTP/1.1\r\n";
    $header .= "Host:sdk2.entinfo.cn\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: ".$length."\r\n";
    $header .= "Connection: Close\r\n\r\n";
    //添加post的字符串
    $header .= $params."\r\n";
    //发送post的数据
    fputs($fp,$header);
    $inheader = 1;
    while (!feof($fp)) {
        $line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据
        if ($inheader && ($line == "\n" || $line == "\r\n")) {
            $inheader = 0;
        }
        if ($inheader == 0) {
            // echo $line;
        }
    }
    //<string xmlns="http://tempuri.org/">-5</string>
    $line=str_replace("<string xmlns=\"http://tempuri.org/\">","",$line);
    $line=str_replace("</string>","",$line);
    $result=explode("-",$line);
    if(count($result)>1)
        return $line;
    else
        return $line;
}
//判断目录是否可写
function check_dir_iswritable($dir_path){
    $dir_path=str_replace('\\','/',$dir_path);
    $is_writale=1;
    if(!is_dir($dir_path)){
        $is_writale=0;
        return $is_writale;
    }else{
        $file_hd=@fopen($dir_path.'/test.txt','w');
        if(!$file_hd){
            @fclose($file_hd);
            @unlink($dir_path.'/test.txt');
            $is_writale=0;
            return $is_writale;
        }
        @fclose($file_hd);
        @unlink($dir_path.'/test.txt');
        $dir_hd=opendir($dir_path);
        while(false!==($file=readdir($dir_hd))){
            if ($file != "." && $file != "..") {
                if(is_file($dir_path.'/'.$file)){
                    //文件不可写，直接返回
                    if(!is_writable($dir_path.'/'.$file)){
                        return 0;
                    }
                }else{
                    $file_hd2=@fopen($dir_path.'/'.$file.'/test.txt','w');
                    if(!$file_hd2){
                        @fclose($file_hd2);
                        @unlink($dir_path.'/'.$file.'/test.txt');
                        $is_writale=0;
                        return $is_writale;
                    }
                    @unlink($dir_path.'/test.txt');
                    //递归
                    $is_writale=check_dir_iswritable($dir_path.'/'.$file);
                }
            }
        }
    }
    return $is_writale;
}

function is_email($email)
{
    //$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
    $pattern = "/^[-_+.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+([a-z]{2,4})|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i";
    return strlen($email) > 7 && preg_match($pattern, $email);
}
function is_phone($phone)
{
    return strlen(trim($phone)) == 11 && preg_match("/^1[3|5|7|8][0-9]{9}$/i", trim($phone));
}
function pregtime($timestamp){
    if($timestamp){
        return date('Y-m-d',$timestamp);
    }else{
        return '';
    }
}


function userLog($uid,$text=''){
    $user = M('user')->where(array('user_id'=>$uid))->find();
    $category = $user['category_id'] == 1 ? L('ADMIN') : L('USER');
    $data['user_id'] = $uid;
    $data['module_name'] = strtolower(MODULE_NAME);
    $data['action_name'] = strtolower(ACTION_NAME);
    $data['create_time'] = time();
    //   $data['action_id'] = $id;
    $data['content'] = sprintf('%s%s在%s%s。%s',$category,$user['name'],date('Y-m-d H:i:s'),L(ACTION_NAME),$text);
    $userLog = M('userLog');
    $userLog->create($data);
    if($userLog->add()){return true;}else{return false;}

}
function vali_permission($m, $a){
    $allow = $params['allow'];

    if (session('?admin')) {
        return true;
    }
    if (in_array($a, $allow)) {
        return true;
    } else {
        switch ($a) {
            case "listdialog" : $a = 'index'; break;
            case "adddialog" : $a = 'add'; break;
            case "excelimport" : $a = 'add'; break;
            case "excelexport" : $a = 'view'; break;
            case "cares" :  $a = 'index'; break;
            case "caresview" :  $a = 'view'; break;
            case "caresedit" :  $a = 'edit'; break;
            case "caresdelete" :   $a = 'delete'; break;
            case "caresadd" :  $a = 'add'; break;
            case "receive" : $a = 'add'; break;
            case "role_add" : $a = 'add';break;
            case "sendsms" : $a = 'marketing';break;
            case "sendemail" : $a = 'marketing';break;
        }
        $url = strtolower($m).'/'.strtolower($a);
        $ask_per = M('permission')->where('url = "%s" and position_id = %d', $url, session('position_id'))->find();
        if (is_array($ask_per) && !empty($ask_per)) {
            return true;
        } else {
            return false;
        }
    }
}
/**
 * @ atuhor		: Lynn
 * @ function 	: formmat the print_r to debug the array conveniently
 **/
function println($data, $offset=true){
    if(empty($data)){
        echo '<pre>返回数据为空！</pre>';
    }else{
        echo '<pre>'; print_r($data); echo '</pre>';
    }
    if($offset){
        die;
    }
}

/**
 * @ atuhor		: zf
 * @ function 	: 验证某条数据的权限
 **/
function check_permission($module_id, $module, $permission_field='owner_role_id'){
    $role_id = intval(session('role_id'));
    $owner_role_id = M($module)->where($module.'_id = %d', $module_id)->getField($permission_field);
    $permission_ids = getSubRoleId();
    if(in_array($owner_role_id, $permission_ids) || !$owner_role_id) return true;
    else return false;
}

/**
 * @ atuhor		: zf
 * @ function 	: 下载方法
 **/
function download($file,$name=''){
    $fileName = $name ? $name : pathinfo($file,PATHINFO_FILENAME);
    $filePath = realpath($file);

    $fp = fopen($filePath,'rb');

    if(!$filePath || !$fp){
        D('File')->ossDownload($file, $name);
    }
    if (strpos($filePath, 'Uploads') === false && strpos($filePath, 'Public') === false) {
        alert('error','sorry，文件不存在！',$_SERVER['HTTP_REFERER']);
    }

    $fileName = $fileName .'.'. pathinfo($filePath,PATHINFO_EXTENSION);
    $encoded_filename = urlencode($fileName);
    $encoded_filename = str_replace("+", "%20", $encoded_filename);

    header('HTTP/1.1 200 OK');
    header( "Pragma: public" );
    header( "Expires: 0" );
    header("Content-type: application/octet-stream");
    header("Content-Length: ".filesize($filePath));
    header("Accept-Ranges: bytes");
    header("Accept-Length: ".filesize($filePath));

    $ua = $_SERVER["HTTP_USER_AGENT"];
    if (preg_match("/MSIE/", $ua)) {
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } else if (preg_match("/Firefox/", $ua)) {
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $fileName . '"');
    } else {
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
    }

    // ob_end_clean(); <--有些情况可能需要调用此函数
    // 输出文件内容
    fpassthru($fp);
    exit;
}


/**
 * @author		: myron
 * @function	: 获取表信息
 * @table_name	: 表名(不含表前缀)
 **/
function getTableInfo($table_name){
    $sql = 'SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = "'.C('DB_NAME').'" and table_name LIKE "'.C('DB_PREFIX').$table_name.'"';
    $result = M('')->query($sql);
    return $result;
}

//filemanager排序
function cmp_func($a, $b) {
    global $order;
    if ($a['is_dir'] && !$b['is_dir']) {
        return -1;
    } else if (!$a['is_dir'] && $b['is_dir']) {
        return 1;
    } else {
        if ($order == 'size') {
            if ($a['filesize'] > $b['filesize']) {
                return 1;
            } else if ($a['filesize'] < $b['filesize']) {
                return -1;
            } else {
                return 0;
            }
        } else if ($order == 'type') {
            return strcmp($a['filetype'], $b['filetype']);
        } else {
            return strcmp($a['filename'], $b['filename']);
        }
    }
}


/**
 * @ atuhor		: zf
 * @ function 	: 判断有无具体操作的权限  返回值为权限类型
 * @ return 	: 返回值为权限类型  1、自己和下属  2、所有人 3、仅自己  4、部门所有人
 * @ des	 	: 参数说明：
 *                $module 对应的模块名 小写
 *                $action 对应的方法名
 **/
function getCheckUrlByAction($m, $a){
    switch (strtolower($a)) {
        case "listdialog" : $a = 'index'; break;
        case "radiolistdialog" : $a = 'index'; break;
        case "checklistdialog" : $a = 'index'; break;
        case "changedialog" : $a = 'index'; break;
        case "adddialog" : $a = 'add'; break;
        case "cares" :  $a = 'index'; break;
        case "excelimportdownload" : $a = 'excelimport'; break;
        case "selectexcelexport" : $a = 'excelexport'; break;
        case "receive" : $a = 'add'; break;
        case "search" : $a = 'index'; break;
        case "role_add" : $a = 'add';break;
        case "remove" : $a = 'edit';break;
        case "changecontent" : $a = 'index';break;
        case "advance" : $a = 'edit';break;
        case "close" : $a = 'edit';break;
        case "revert" : $a = 'delete';break;
        case "getcustomerlist" : $a = 'index';break;
        case "fenpei" : $a = 'add';break;
        case "getwarehouselist" : $a = 'warehouse';break;
        case "changetofirstcontact" : $a = 'edit'; break;
        case "addcategory" :
        case "editcategory" :
            if(strtolower($m) == 'supplier'){
                $a = 'category';
            }
            break;
        case "revokecheck" : $a = 'check'; break;
        default: $a = strtolower($a); break;
    }
    return strtolower($m.'/'.$a);
}

/**
 * @ atuhor		: zf
 * @ function 	: 判断有无具体操作的权限  返回值为权限类型
 * @ return 	: 返回值为权限类型  1、自己和下属  2、所有人 3、仅自己  4、部门所有人
 * @ des	 	: 参数说明：
 *                $module 对应的模块名 小写
 *                $action 对应的方法名
 **/
function checkPerByAction($m, $a){
    $m_permission = M('permission');
    $url = getCheckUrlByAction($m, $a);
    if(session('?admin') ){
        //2为所有人
        return 2;
    }elseif($per = $m_permission->where('url = "%s" and position_id = %d', $url, session('position_id'))->find()){
        //有$url操作权限；
        return $per['type'];
    }else{
        //无$url操作权限；
        return 0;
    }
}


/**
 * sub_role=true  下属role_id 范围
 * sub_role=false 权限内的role_id 范围
 */
function getPerByAction($m, $a, $sub_role=false){
    $m_permission = M('permission');
    $url = getCheckUrlByAction($m, $a);

    $per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
    if($sub_role){
        if($per_type == 3){
            $below_ids = array();
        }elseif($per_type == 4){
            $departmen_role_ids = array();
            $role_ids = getRoleByDepartmentId(session('department_id'), true);
            foreach($role_ids as $v){
                $departmen_role_ids[] = $v['role_id'];
            }
            $temp_below_ids = getSubRoleId(false);
            $below_ids = array_intersect($departmen_role_ids, $temp_below_ids);
        }else{
            $below_ids = getSubRoleId(false);
        }
        if(empty($below_ids))
            return array(-1);
        else
            return $below_ids;
    }else{
        //管理员拥有所有人的数据权限
        if(session('?admin')) return getSubRoleId(true, 1);
        $role_array = array();
        switch($per_type){
            //权限类型为1 包含自己和下属的数据 返回自己和下属role_id数组
            case 1: $role_array = getSubRoleId(); break;
            //权限类型为2 返回false 不加判断条件即所有人都有的权限。
            case 2: $role_array = getSubRoleId(true, 1); break;
            //权限类型为3 仅自己的数据 返回数组 返回仅包含自己role_id的数组
            case 3: $role_array = array(session('role_id')); break;
            //默认 部门所有人
            case 4:
                $role_ids = getRoleByDepartmentId(session('department_id'), true);
                foreach($role_ids as $v){
                    $role_array[] = $v['role_id'];
                }
                break;
        }
        return $role_array;
    }
}

/**
 * @ atuhor		: zf
 * @ function 	: 根据操作权限获取roleid
 * @ return 	: 返回roleid的数组
 * @ des	 	: 参数说明：
 *                $per_array为包含操作的数组 array('leads/add', 'customer/add')
 **/
function getRoleByPer($per_array){
    if($per_array){
        $where['url'] = array('in', $per_array);
        if($position_ids = M('Permission') -> where($where)->getField('position_id', true)){
            $role_ids_array = D('RoleView')->where('role.position_id in(%s)', implode(',', $position_ids))->getField('role_id', true);
            return array_unique($role_ids_array);
        }else{
            return false;
        }
    }else{
        return false;
    }
}

/**
 * @ atuhor		: zf
 * @ function 	: 根据当前处理程序判断顶部菜单按钮项
 * @ return 	: 返回顶部的数组
 * @ des	 	: 参数说明：
 *                $module_name为模块名, $action_name为方法名
 **/
function setSelectedMenu($module_name, $action_name){
    $module_name = strtolower($module_name);
    $action_name = strtolower($action_name);
    if($action_name == 'analytics'){
        return 'menu_analy';
    }elseif($action_name == 'sendsms' || $action_name == 'sendemail' || $action_name == 'smsrecord'){
        return 'menu_market';
    }
    switch($module_name){
        case 'leads':
        case 'customer':
            return 'menu_customer';
        case 'business':
        case 'contract':
        case 'quote':
            return 'menu_business';
        case 'product':
        case 'sales':
        case 'stock':
        case 'supplier':
        case 'purchase':
        case 'contract':
            return 'menu_stock';
        case 'finance':
            return 'menu_finance';
        case 'task':
        case 'knowledge':
        case 'log':
        case 'event':
        case 'user':
        case 'examine':
        case 'workorder':
        case 'announcement':
            return 'menu_office';
        case 'actionlog':
        case 'setting':
            return 'menu_setting';
        case 'message':
            return 'menu_home';
        default : return 'index';
    }
}

function getSubCategoryTreeCode($category_id, $first=0) {
    $string = "";
    $department_list = M('ProductCategory')->where('parent_id = %d', $category_id)->select();
    if ($department_list) {
        if ($first) {
            $string = '<ul id="browser" class="filetree"><li style="list-style-type: none;" class="collapsable"><span rel="0" class="folder ta">全部 &nbsp; <span class="" id="0"> </span></span></li>';
        } else {
            $string = "<ul>";
        }
        foreach($department_list AS $value) {
            if($first){
                $string .= "<li style='list-style-type: none;'><span rel='".$value['category_id']."' class='folder ta'>".$value['name']." &nbsp; <span class='' id='".$value['category_id']."'> </span></span>".getSubCategoryTreeCode($value['category_id'])."</li>";
            } else {
                $string .= "<li style='list-style-type: none;'><span rel='".$value['category_id']."' class='file ta'>".$value['name']." &nbsp; <span class='' id='".$value['category_id']."'> </span></span>".getSubCategoryTreeCode($value['category_id'])."</li>";
            }
        }
        $string .= "</ul>";
    }
    return $string;
}

/**
 * author : myrom
 * function : 截取字符长度，如果超过字符长度，后面追加...
 * @str : 要截取的字符串  $len : 要截取的长度
 **/
function cutString($str='', $len='15'){
    if(empty($str) || empty($len)) return false;
    $pre_content = strip_tags($str);
    $pre_content_len = mb_strlen($pre_content,'utf-8');
    if($pre_content_len <= $len){
        return $pre_content;
    }else{
        $pre_content = mb_substr($pre_content,0,$len,'utf-8');
        return $pre_content.' . . .';
    }
}

/**
 * author : myron
 * function : 在AuthenticateBehavior中判断是否AJAX请求，如果是AJAx请求且在弹窗页没有权限，直接显示无权限
 **/
function isAjaxRequest() {
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
        if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])){
            return true;
        }
    }
    if(!empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])){
        // 判断Ajax方式提交
        return true;
    }
    return false;
}

/**
 * author : myron
 * function : 读取标签
 * param 	: limit 显示条数。如果没有limit，默认读取所有标签并以热度排序。如果有limit，读取限制条数的标签，并以热度排序
 **/
function getTags($limit){
    $m_tags = M('tags');
    $m_customer_data = M('CustomerData');
    $where['tags'] = array('neq',',');
    $tags_ids = $m_customer_data ->where($where)->getField('tags',true);
    $tags_all_Str = '';
    foreach($tags_ids as $v){
        $tags_arr = array_filter(explode(',',$v));
        if($tags_arr){
            $tags_str = implode(',',$tags_arr);
        }
        $tags_all_Str .= ','.$tags_str;
    }
    $tags_all_arr = array_flip(array_flip(explode(',',$tags_all_Str)));
    $tag['tags_id'] = array('in',$tags_all_arr);
    if(!empty($limit)){
        $tags = $m_tags->where($tag)->limit($limit)->order('hits desc')->select();
    }else{
        $tags = $m_tags->where($tag)->order('hits desc')->select();
    }
    return $tags;
}

function Xinge($nickName,$title,$content,$type,$model_type){
    //IOS
    $ios_id = 2200271087;
    $ios_key = '3c8904d51a13a6b62248694376f973b1';
    //Android
    $android_id = 2100319986;
    $android_key = '45016950bd7886cd81a813e3a486e7a0';

    import('@.ORG.XingeApp');
    $push_ios = new XingeApp($ios_id,$ios_key);
    $push_android = new XingeApp($android_id,$android_key);

    $ios_mess = new MessageIOS();
    //完善Message消息
    $ios_mess->setExpireTime(86400); //消息离线存储多久，单位为秒，最长存储时间3天。选填，默认为3天，不允许为0
    $ios_mess->setAlert($content);
    $ios_mess->setSound("beep.wav");

    //通知被点击动作
    $action = new ClickAction();
    $value = 'com.crm.main.newactivitys.SystemMessActivity';
    $action->setActionType(ClickAction::TYPE_ACTIVITY);//打开activity或app本身
    $action->setActivity($value);

    $android_mess = new Message();
    $android_mess->setTitle($title); //标题
    $android_mess->setExpireTime(86400);
    $android_mess->setContent($content); //内容，标题+内容不得超过800英文字符或400非英文字符
    $android_mess->setType(Message::TYPE_NOTIFICATION); //消息类型
    $android_mess->setAction($action);
    //#含义：样式编号0，响铃，震动，不可从通知栏清除，不影响先前通知
    $android_mess->setStyle(new Style(0,1,1,1,0));
    //IOSENV_PROD 表示推送生产环境； IOSENV_DEV 表示推送开发环境。
    if (is_array($nickName)) {
        $android_ret = $push_android->PushAccountList(0, $nickName, $android_mess, 0);
        $ios_ret = $push_ios->PushAccountList(0, $nickName, $ios_mess, XingeApp::IOSENV_PROD);
    } else {
        $ios_ret = $push_ios->PushSingleAccount(0, $nickName, $ios_mess, XingeApp::IOSENV_PROD);
        $android_ret = $push_android->PushSingleAccount(0, $nickName, $android_mess, 0);
    }
    return array('android' => $android_ret, 'ios' => $ios_ret);
}

function Xinge2($nickName, $title, $content, $sendTime = null)
{
    if (!strtotime($sendTime)) {
        $sendTime = date('Y-m-d H:i:s');
    }
    $content .= $sendTime;
    //IOS
    $ios_app_id = 'd32008b613af6';
    $ios_secret_key = '3c8904d51a13a6b62248694376f973b1';
    $ios_access_id = '2200271087';
    $ios_access_key = 'I511WN6S8AYT';
    //Android
    $android_app_id = '2a2162454df7a';
    $android_secret_key = '45016950bd7886cd81a813e3a486e7a0';
    $android_access_id = '2100319986';
    $android_access_key = 'ADUJ15659JDE';
    import('@.ORG.Xinge');

    $android_push = new XingeApp($android_app_id, $android_secret_key);
    $mess_android = new Message();
    $mess_android->setType(Message::TYPE_NOTIFICATION);
    $mess_android->setTitle($title);
    $mess_android->setContent($content);
    $mess_android->setExpireTime(86400);
    $mess_android->setSendTime($sendTime);

    $ios_push = new XingeApp($ios_app_id, $ios_secret_key);
    $mess_ios = new MessageIOS();
    $mess_ios->setType(MessageIOS::TYPE_APNS_NOTIFICATION);
    $mess_ios->setTitle($title);
    $mess_ios->setContent($content);
    $mess_ios->setExpireTime(86400);
    $mess_ios->setBadge(1);
    $mess_ios->setSound("beep.wav");
    $acceptTime = new TimeInterval(0, 0, 23, 59);
    $mess_ios->addAcceptTime($acceptTime);
    $mess_ios->setSendTime($sendTime);

    //IOSENV_PROD 表示推送生产环境； IOSENV_DEV 表示推送开发环境。
    if (is_array($nickName)) {
        $android_ret = $android_push->PushAccountList($nickName, $mess_android);
        $ios_ret = $ios_push->PushAccountList($nickName, $mess_ios, XingeApp::IOSENV_PROD);
        // dd($ios_ret);
    } else {
        $android_ret = $android_push->PushSingleAccount($nickName, $mess_android);
        $ios_ret = $ios_push->PushSingleAccount($nickName, $mess_ios, XingeApp::IOSENV_PROD);
    }
    return array('android' => $android_ret, 'ios' => $ios_ret);
}

function redirects($string="",$url=""){
    if($string == ""){
        if($url == ""){
            die("<meta charset='utf-8'><Script charset='UTF-8'  Language='JavaScript'>history.back(-1);</Script>");
        }else{
            die("<meta charset='utf-8'><Script charset='UTF-8'  Language='JavaScript'>window.location.href='".$url."';</Script>");
        }
    }else{
        if($url == ""){
            die("<meta charset='utf-8'><Script charset='UTF-8' Language='JavaScript'>alert('".$string."');history.back(-1);</Script>");
        }else{
            die("<meta charset='utf-8'><Script charset='UTF-8' Language='JavaScript'>alert('".$string."');window.location.href='".$url."';</Script>");
        }
    }
}
//自定义字段验重
//params : field 字段名, val 值 ,id 排除当前数据验重,model = 需要查询的模块名
function validate($model,$field,$val,$id) {
    if(!$field || !$val){
        return false;
    }
    $field_info = M('Fields')->where('model = "%s" and field = "%s"',$model,$field)->find();
    if($model == 'contacts'){
        $m_fields = $field_info['is_main'] ? D('contacts') : D('ContactsData');
    }elseif($model == 'customer'){
        $m_fields = $field['is_main'] ? D('Customer') : D('CustomerData');
    }elseif($model == 'product'){
        $m_fields = $field['is_main'] ? D('Product') : D('ProductData');
    }elseif($model == 'leads'){
        $m_fields = $field['is_main'] ? D('Leads') : D('LeadsData');
    }elseif($model == 'contract'){
        $m_fields = $field['is_main'] ? D('Contract') : D('ContractData');
    }elseif($model == 'business'){
        $m_fields = $field['is_main'] ? D('Business') : D('BusinessData');
    }elseif($model == 'examine'){
        $m_fields = $field['is_main'] ? D('Examine') : D('ExamineData');
    }
    $where[$field] = array('eq',$val);
    if($id){
        $where[$m_fields->getpk()] = array('neq',$id);
    }
    if($field){
        if ($m_fields->where($where)->find()) {
            return true;
        } else {
            return false;
        }
    }else{
        return false;
    }
}
//APP列表添加权限判断接口
function apppermission($m,$a){
    if(session('?admin')){
        $permission = array();
        $permission['add'] = 1;
        return $permission;
    }else{
        $m_permission = M('Permission');
        $where['url'] = array('like','%'.$m.'%');
        $where['position_id'] = session('position_id');
        $permission_list = $m_permission->where($where)->select();
        $permission['add'] = 0;
        foreach($permission_list as $k=>$v){
            $permission_info = explode('/',$v['url']);
            if($permission_info[1] == 'add' || ($m == 'Finance' && $permission_info[1] == 'add_'.$a)){
                $permission['add'] = 1;
                break;
            }
        }
        return $permission;
    }
}

function permissionlist($m,$role_id,$type){
    if(session('?admin')){
        $permission = array();
        $permission['edit'] = 1;
        $permission['view'] = 1;
        $permission['delete'] = 1;
        return $permission;
    }else{
        if($role_id){
            if ($m == 'Finance') {
                $list = array('view_'.$type,'edit_'.$type,'delete_'.$type);
            } elseif ($m == 'log') {
                $list = array('mylog_view','mylog_edit','log_delete');
            } else {
                $list = array('view','edit','delete');
            }
            $permission = array();
            foreach($list as $k=>$v){
                $all_ids = getPerByAction($m,$v);
                if($v == 'view' || $v == 'view_'.$type || $v == 'mylog_view'){
                    if(in_array($role_id, $all_ids)){
                        $permission['view'] = 1;
                    }
                }elseif($v == 'edit' || $v == 'edit_'.$type || $v == 'mylog_edit'){
                    if(in_array($role_id, $all_ids)){
                        $permission['edit'] = 1;
                    }
                }elseif($v == 'delete' || $v == 'delete_'.$type || $v == 'log_delete'){
                    if(in_array($role_id, $all_ids)){
                        $permission['delete'] = 1;
                    }
                }
            }
        }else{
            //如果没有负责人，则只给看权限
            $permission = array('view'=>1);
        }
        // return (object)$permission;
        return $permission;
    }
}
//非role_id权限判断接口(手机端)
function getpermission($m){
    if(session('?admin')){
        $permission = array();
        $permission['edit'] = 1;
        $permission['view'] = 1;
        $permission['delete'] = 1;
        return $permission;
    }else{
        $m_permission = M('Permission');
        $where['url'] = array('like','%'.$m.'%');
        $where['position_id'] = session('position_id');
        $permission_list = $m_permission->where($where)->select();
        $permission = array();
        foreach($permission_list as $k=>$v){
            $permission_info = explode('/',$v['url']);
            if(in_array($permission_info[1], array('edit','view','delete'))){
                $permission[$permission_info[1]] = 1;
            }
        }
        return (object)$permission;
    }
}

function doRecord($data){

    if(!$data || !is_array($data)){
        return false;
    }
    //判断cookie类里面是否有浏览记录
    if(cookie('history')){
        $history = unserialize(cookie('history'));
        array_unshift($history, $data); //在浏览记录顶部加入
        /* 去除重复记录 */
        $rows = array();
        foreach ($history as $v){
            if(in_array($v, $rows)){
                continue;
            }
            $rows[] = $v;
        }
        /* 如果记录数量多余5则去除 */
        while (count($rows) > 10){
            array_pop($rows); //弹出
        }
        cookie('history', serialize($rows),time()+3600*24*30);
    }else{
        $history = serialize(array($data));
        cookie('history', $history,time()+3600*24*30);
    }
}

function getKnowDepartmentTreeCode($category_id,$department_id, $first=0) {
    $string = "";
    $department_list = M('roleDepartment')->where('parent_id = %d', $department_id)->select();
    $knowledge_category = M('knowledgeCategory');
    $to_department = $knowledge_category->where('category_id = %s',$category_id)->getField('to_department');
    $department_arr = explode(',',$to_department);
    if ($department_list) {
        if ($first) {
            $string = '<ul id="browser" style="margin-top:12px;" class="filetree">';
        } else {
            $string = "<ul>";
        }
        foreach($department_list AS $value) {
            $checked = '';
            $resoult = in_array($value['department_id'],$department_arr);
            if($resoult){
                $checked = 'checked';
            }
            $string .= "<li><span rel='".$value['department_id']."'><input style='width:15px;' name='department_id[]' ".$checked."  class='check_list' type='checkbox' value='".$value['department_id']."'/><img style='margin-top:-12px;' src='__PUBLIC__/img/kai.png'></img><span style='position: relative;top: -4px;'>".$value['name']." &nbsp;</span> </span>".getKnowDepartmentTreeCode($category_id,$value['department_id'])."</li>";
        }
        $string .= "</ul>";
    }
    return $string;
}
//产品树
function getProductTreeCode($category_id,$first=0) {
    $string = "";
    $category_list = M('ProductCategory')->where('parent_id = %d', $category_id)->select();
    if ($category_list) {
        if ($first) {
            $string = '<ul>';
            //$string .= "<li class='jstree-open'><a href = ".U('product/index').">全部</a></li>";
        } else {
            $string = "<ul>";
        }
        foreach($category_list AS $value) {
            $class = '';
            if($_GET['category_id'] == $value['category_id']){
                $class = 'jstree-clicked';
            }
            $string .= "<li class='jstree-open' rel='".$value['category_id']."'><a class='".$class."' href = ".U('product/index','category_id='.$value['category_id']).">".$value['name']."</a>".getProductTreeCode($value['category_id'],0)."</li>";
        }
        $string .= "</ul>";
    }
    return $string;
}

/**
 * 递归获取产品分类
 */
function getCategoryNodes($category_id, $categories, $selected_category_id) {
    $m_product_category = M('ProductCategory');
    $array = array();
    foreach($categories AS $value) {
        if ($category_id == $value['parent_id']) {
            $son_category = array();
            if($m_product_category->where('parent_id = %d', $category_id)->count() > 0){
                //如果有子分类
                $son_category = getCategoryNodes($value['category_id'], $categories, $selected_category_id);
            }

            $selected = array('selected'=>false);
            if($value['category_id'] == $selected_category_id){
                $selected = array('selected'=>true);
            }
            $array[] = array('id' => $value['category_id'], 'text' => $value['name'],'first' => $first,'state'=>$selected,'children'=>$son_category);
        }
    }
    return $array;
}


//知识树
function getKnowledgeTreeCode($category_id,$first=0) {
    $string = "";
    $category_list = M('KnowledgeCategory')->where('parent_id = %d', $category_id)->select();
    if ($category_list) {
        if ($first) {
            $string = '<ul>';
            $string .= "<li class='jstree-open'><a href = ".U('knowledge/index').">全部</a></li>";
        } else {
            $string = "<ul>";
        }

        foreach($category_list AS $value) {
            $class = '';
            if($_GET['category_id'] == $value['category_id']){
                $class = 'jstree-clicked';
            }
            $string .= "<li class='jstree-open' rel='".$value['category_id']."'><a class='".$class."' href = ".U('knowledge/index','category_id='.$value['category_id']).">".$value['name']."</a>".getKnowledgeTreeCode($value['category_id'],0)."</li>";
        }
        $string .= "</ul>";
    }
    return $string;
}
//获取该部门最高岗位
function firstDepartment($department_id){
    $dep = M('RoleDepartment')->where('department_id = %d', $department_id)->find();
    $pos = M('position')->where('department_id = %d', $department_id)->count();
    $positions = M('position')->where('department_id = %d', $dep['department_id'])->select();
    $position_ids = M('position')->where('department_id = %d', $dep['department_id'])->getField('position_id',true);
    foreach($positions as $k=>$v){
        if(!in_array($v['parent_id'],$position_ids)){
            $first_position_id = $v['position_id'];
            break;
        }
    }
    return $first_position_id;
}

/* 排序(选择)
	*$select 要进行排序的select结果集
	*$field  排序的字段
	*$order 排序方式1降序2升序
    */
//二维数组排序
function sort_select($select=array(), $field, $order=1){
    $count = count($select);
    if($order == 1){
        for ($i=0; $i < $count; $i++) {
            $k = $i;
            for ($j=$i; $j < $count; $j++) {
                if ($select[$k][$field] < $select[$j][$field]) {
                    $k = $j;
                }
            }
            $temp = $select[$i];
            $select[$i] = $select[$k];
            $select[$k] = $temp;
        }
        return $select;
    }else{
        for ($i=0; $i < $count; $i++) {
            $k = $i;
            for ($j=$i; $j < $count; $j++) {
                if ($select[$k][$field] > $select[$j][$field]) {
                    $k = $j;
                }
            }
            $temp = $select[$i];
            $select[$i] = $select[$k];
            $select[$k] = $temp;
        }
        return $select;
    }
}
/* 根据时间戳获取星期几
 *$time 要转换的时间戳
*/
function getTimeWeek($time, $i = 0) {
    $weekarray = array("日", "一", "二", "三", "四", "五", "六");
    $oneD = 24 * 60 * 60;
    return "星期" . $weekarray[date("w", $time + $oneD * $i)];
}

//商城分类列表
function getSubCategoryByShop($category_id, $category, $separate,$first=0) {
    $array = array();
    foreach($category AS $value) {
        if ($category_id == $value['parent_id']) {
            $array[] = array('category_id' => $value['category_id'], 'name' => $separate.$value['name'],'first' => $first);
            $array = array_merge($array, getSubCategoryByShop($value['category_id'], $category, $separate,0));
        }
    }
    return $array;
}

//商城提示
function notice($a,$b){
    if($a==''){
        header('Location:'.$b);
    }elseif($b == 'return'){
        echo '<meta content="text/html; charset=utf-8" http-equiv="Content-Type">';
        echo '<script>';
        echo 'alert("'.$a.'");';
        echo 'history.go(-1);';
        echo '</script>';
    }else{
        echo '<meta content="text/html; charset=utf-8" http-equiv="Content-Type">';
        echo '<script>';
        echo 'alert("'.$a.'");';
        echo 'window.location.href="'.$b.'";';
        echo '</script>';
    }
    die;
}

//查询上级部门中的最高岗位
function getTopPositionByDepartment($department_id){
    $m_position = M('Position');
    $top_position_id = $m_position->where(array('department_id'=>$department_id))->order('parent_id asc')->getField('position_id');
    if(!empty($top_position_id)){
        $parent_id = M('RoleDepartment')->where('department_id = %d',$department_id)->getField('parent_id');
        if(empty($parent_id)){
            $top_position_id = getTopPositionByDepartment($parent_id);
        }
    }
    return $top_position_id;
}

//将秒数转换为时间（年、天、小时、分、秒）
function getTimeBySec($time){
    if(is_numeric($time)){
        $value = array(
            "years" => 0, "days" => 0, "hours" => 0,
            "minutes" => 0, "seconds" => 0,
        );
        if($time >= 31556926){
            $value["years"] = floor($time/31556926);
            $time = ($time%31556926);
            $t .= $value["years"] ."年";
        }
        if($time >= 86400){
            $value["days"] = floor($time/86400);
            $time = ($time%86400);
            $t .= $value["days"] ."天";
        }
        if($time >= 3600){
            $value["hours"] = floor($time/3600);
            $time = ($time%3600);
            $t .= $value["hours"] ."小时";
        }
        if($time >= 60){
            $value["minutes"] = floor($time/60);
            $time = ($time%60);
            $t .= $value["minutes"] ."分钟";
        }
        if ($time < 60) {
            $value["seconds"] = floor($time);
            $t .= $value["seconds"] ."秒";
        }
        Return $t;
    }else{
        return (bool) FALSE;
    }
}

//根据月份计算天数
function getmonthdays($year_month){
    $month = date('m',$year_month);
    $year = date('Y',$year_month);
    if (in_array($month, array(1, 3, 5, 7, 8, 01, 03, 05, 07, 08, 10, 12))){
        $days = '31';
    }elseif($month == 2){
        if ($year % 400 == 0 || ($year % 4 == 0 && $year % 100 !== 0)){//判断是否是闰年
            $days = '29';
        }else{
            $days = '28';
        }
    }else{
        $days = '30';
    }
    return $days;
}

/**
 * 生成从开始日期到结束日期的日期数组
 */
function dateList($start,$end){

    if(!is_numeric($start) || !is_numeric($end) || ($end<=$start)) return '';
    $i = 0;
    //从开始日期到结束日期的每日时间戳数组
    $d = array();
    while($start <= $end){
        $d[$i] = $start;
        $start = $start+86400;
        $i++;
    }
    $list = array();
    foreach($d as $k=>$v){
        $list[$k] = getDateRange($v);
    }
    return $list;
}

//获取指定日期开始时间与结束时间
function getDateRange($timestamp){
    $ret = array();
    $ret['sdate'] = strtotime(date('Y-m-d',$timestamp));
    $ret['edate'] = strtotime(date('Y-m-d',$timestamp))+86400;
    return $ret;
}

/**
 * 人民币转大写
 * @param
 * @author
 * @return
 */
function cny($ns){
    static $cnums = array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"),
    $cnyunits = array("圆","角","分"),
    $grees = array("拾","佰","仟","万","拾","佰","仟","亿");
    list($ns1,$ns2) = explode(".",$ns,2);
    $ns2 = array_filter(array($ns2[1],$ns2[0]));
    $ret = array_merge($ns2,array(implode("", _cny_map_unit(str_split($ns1), $grees)), ""));
    $ret = implode("",array_reverse(_cny_map_unit($ret,$cnyunits)));
    return str_replace(array_keys($cnums), $cnums,$ret);
}
function _cny_map_unit($list,$units) {
    $ul = count($units);
    $xs = array();
    foreach (array_reverse($list) as $x) {
        $l = count($xs);
        if($x!="0" || !($l%4)) {
            $n=($x=='0'?'':$x).($units[($l-1)%$ul]);
        }else{
            $n=is_numeric($xs[0][0]) ? $x : '';
        }
        array_unshift($xs, $n);
    }
    return $xs;
}

/**记录任务（活动）操作日志
 * $task_id 任务ID
 * $type 操作类型
 * $about_role_id 其他相关role_id
 * $params_content 相关操作内容
 **/
function taskActionLog($task_id,$type,$about_role_id,$params_content){
    $user_info = M('User')->where(array('role_id'=>session('role_id')))->find();
    $data['task_id'] = $task_id;
    $data['role_id'] = session('role_id');
    $data['type'] = $type;
    $data['create_date'] = date('Y-m-d',time());
    $data['create_time'] = time();
    $data['about_role_id'] = $about_role_id;

    switch($type){
        case 1 : $content = '添加任务'; break;
        case 2 : $content = '修改任务'.$params_content; break;
        case 3 : $content = '完成任务'; break;
        case 4 : $content = '取消完成任务'; break;
        case 5 : $content = '归档了任务'; break;
        case 6 : $content = '激活了任务'; break;
        case 7 : $content = '分配任务给&nbsp;&nbsp;'; break;
        case 8 : $content = '取消分配任务给&nbsp;&nbsp;'; break;
        case 9 : $content = '提醒&nbsp;&nbsp;'; break;
        case 10 : $content = '移除&nbsp;&nbsp;'; break;
        case 11 : $content = '添加子任务&nbsp;&nbsp;'.$params_content; break;
        case 12 : $content = '完成子任务&nbsp;&nbsp;'.$params_content; break;
        case 13 : $content = '取消完成子任务&nbsp;&nbsp;'.$params_content; break;
        case 14 : $content = '从列表&nbsp;&nbsp;'.$params_content; break;
        case 15 : $content = '修改子任务&nbsp;&nbsp;'.$params_content; break;
    }
    $data['content'] = $content;
    $task_action = M('TaskAction');
    if (M('TaskAction')->add($data)) {
        //发送站内信（分配人、关注人、创建人）
        if (in_array($type,array('2','3','4','5','6','11','12','13','14','15'))) {
            $m_task = M('Task');
            $task_info = $m_task->where('task_id = %d',$task_id)->find();
            $about_role_ids = array_filter(explode(',',$task_info['about_roles']));
            $owner_role_ids = array_filter(explode(',',$task_info['owner_role_id']));
            $creator_role_ids[] = $task_info['creator_role_id'];

            $send_roles = $creator_role_ids;
            if ($about_role_ids) {
                $send_roles = array_merge($send_roles,$about_role_ids);
            }
            if ($owner_role_ids) {
                $send_roles = array_merge($send_roles,$owner_role_ids);
            }
            $send_roles = array_unique($send_roles);
            $message_content = '<a class="task_view" rel="'.$task_id.'" href="javascript:void(0);">'.$user_info['full_name'].'&nbsp;&nbsp;'.$content.'</a>';

            foreach ($send_roles as $k=>$v) {
                sendMessage($v,$message_content,1);
            }
        }
        return true;
    } else {
        return false;
    }
}

/**将时间戳转化为日期
 *
 **/
function newTimeDate($time) {
    if ($time > 0) {
        $d = new DateTime('@'.$time);
        $d->setTimezone(new DateTimeZone('PRC'));
        return $d->format('Y-m-d');
    } else {
        return;
    }
}
/**将年月日转化为一个时间戳
 *
 **/
function newDateTime($date) {
    $d = new DateTime($date);
    return $d->format('U');
}

/**其他相关数据，写入日程
 *
 **/
function dataEvent($subject, $start_date, $module, $module_id) {
    $m_event = M('Event');

    $data = array();
    $data['start_date'] = $start_date;
    $data['end_date'] = strtotime(date('Y-m-d',$start_date))+86399;
    $data['module'] = $module;
    $data['module_id'] = $module_id;
    $data['subject'] = $subject;

    switch ($module) {
        case 'remind' : $color = '#46be8a'; break;
        case 'leads' : $color = '#57c7d4'; break;
        case 'contract' : $color = '#f96868'; break;
        case 'customer' : $color = '#f2a654'; break;
        default : $color = '#62a8ea'; break;
    }
    $data['color'] = $color;
    //编辑
    $event_id = $m_event->where(array('module'=>$module,'module_id'=>$module_id,'owner_role_id'=>session('role_id')))->getField('event_id');
    if ($event_id && $module && $module_id) {
        $data['update_date'] = time();
        $m_event->where('event_id = %d',$event_id)->save($data);
        return $event_id;
    } else {
        $data['owner_role_id'] = session('role_id');
        $data['creator_role_id'] = session('role_id');
        $data['create_date'] = time();
        $data['update_date'] = time();
        return $m_event->add($data);
    }
}

/**
 * 检查该字段若必填，加上"*"
 * @param is_validate 是否验证 1是  2否
 * @param is_null     是否为空 0否  1是
 * @param name 字段名称
 **/
function sign_required($is_validate, $is_null, $name){
    if($is_validate == 1 && $is_null == 1){
        return '*'.$name;
    } else {
        return $name;
    }
}

//增加详情操作记录 $type操作类型$duixing操作内容$model_name模块名称$action_id 操作主键ID
function add_record($type,$duixiang,$model_name,$action_id){
    $m_action_record = M('action_record');
    $arr['create_time'] = time();
    $arr['create_role_id'] = session('role_id');
    $arr['type'] = $type;
    $arr['duixiang'] = $duixiang;
    $arr['model_name'] = $model_name;
    $arr['action_id'] = $action_id;
    $result = $m_action_record ->add($arr);
    if($result){
        return true;
    }else{
        return false;
    }
}

//二维数组递归遍历多维数组实现无限分类(产品)

//示例
// $data[]=array('id'=>1,'parent_id'=>0,'name'=>'中国');
// $data[]=array('id'=>2,'parent_id'=>0,'name'=>'美国');
// $data[]=array('id'=>3,'parent_id'=>0,'name'=>'韩国');
// $data[]=array('id'=>4,'parent_id'=>1,'name'=>'北京');
// $data[]=array('id'=>5,'parent_id'=>1,'name'=>'上海');
// $data[]=array('id'=>6,'parent_id'=>1,'name'=>'广西');
// $data[]=array('id'=>7,'parent_id'=>6,'name'=>'桂林');
// $data[]=array('id'=>8,'parent_id'=>6,'name'=>'南宁');
// $data[]=array('id'=>9,'parent_id'=>6,'name'=>'柳州');
// $data[]=array('id'=>10,'parent_id'=>2,'name'=>'纽约');
// $data[]=array('id'=>11,'parent_id'=>2,'name'=>'华盛顿');
// $data[]=array('id'=>12,'parent_id'=>3,'name'=>'首尔');
// $list_arr = build_tree($data,0);

function findChild (&$arr,$id) {
    $childs=array();
    foreach ($arr as $k => $v){
        if ($v['parent_id']== $id) {
            $childs[]=$v;
        }
    }
    return $childs;
}

function build_tree ($rows,$root_id) {
    $childs = findChild($rows,$root_id);
    if (empty($childs)) {
        return null;
    }
    foreach ($childs as $k => $v){
        $rescurTree = build_tree($rows,$v['id']);
        if (null != $rescurTree) {
            $childs[$k]['childs'] = $rescurTree;
        }
    }
    return $childs;
}

/**
 * 在日程数据中追加，周期性提醒内容
 * @param
 **/
function cycel_event($start_time,$end_time){
    $list = array();
    $cycel_list = M('Cycel')->where(array('create_role_id'=>session('role_id'),'start_time'=>array('elt',$end_time),'end_time'=>array('egt',$start_time)))->select();
    $date_list = dateList($start_time,$end_time);
    foreach ($cycel_list as $k=>$v) {
        $array_day = array();
        $array_week = array();
        $array_month = array();
        $array_year = array();
        //type 1周 2月 3年 4仅一次
        if ($v['type'] == 1) {
            $array_week[] = '星期'.$v['num'];
        } elseif ($v['type'] == 2) {
            $array_month[] = $v['num'];
        } elseif ($v['type'] == 3) {
            $array_month[] = $v['num'];
        } else {
            $array_day[] = strtotime(date('Y-m-d',$v['num']));
        }
        foreach ($date_list as $key=>$val) {
            $week_name = '';
            $week_name = getTimeWeek($val['sdate']); //星期

            $month_name = '';
            $month_name = date('d',$val['sdate']);

            $year_name = '';
            $year_name = date('m-d',$val['sdate']);
            if ((in_array($week_name,$array_week) || in_array($month_name,$array_month) || in_array($year_name,$array_year) || in_array($val['sdate'],$array_day)) && $v['start_time'] <= $val['sdate']) {
                //追加数据
                $list[] = array(
                    'event_id'=>$v['module'],
                    'start_date'=>$val['sdate'],
                    'end_date'=>$val['edate'],
                    'color'=>'#f96868',
                    'module'=>$v['module'],
                    'module_id'=>$v['module_id']
                );
            }
        }
    }
    return $list;
}

/**
 * 递归查询签约合同(暂不用，有问题)
 * @param
 **/
function contract_history($contract_id,$ids,$is_first){
    $array = array();
    $where = array();
    $m_contract = M('Contract');
    if ($ids) {
        $str_ids = implode(',',$ids);
        $where['_string'] = '`contract_id` = '.$contract_id.' AND `contract_id` NOT IN ('.$str_ids.')';
        $where['renew_contract_id'] = $contract_id;
        $where['_logic'] = 'or';
    } else {
        $where['contract_id'] = $contract_id;
        $where['renew_contract_id'] = $contract_id;
        $where['_logic'] = 'or';
    }
    $contract_list = $m_contract->where($where)->field('contract_id,renew_contract_id')->select();

    foreach ($contract_list as $k=>$v) {
        if (!in_array($v['contract_id'],$ids)) {
            $ids[] = $v['contract_id'];
            $array[] = $v['contract_id'];
        }
        if ($v['renew_contract_id'] && (!in_array($v['renew_contract_id'],$ids) || $is_first == 1)) {
            $array = array_merge($array,contract_history($v['renew_contract_id'],$ids));
        }
    }
    return $array;
}

/**
 * 计算两个经纬度之间的距离
 * @param
 * @author
 * @return
 */
function getDistance($lat1, $lng1, $lat2, $lng2){
    $earthRadius = 6367000; // approximate radius of earth in meters

    /*
      Convert these degrees to radians
      to work with the formula
    */

    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;

    /*
      Using the
      Haversine formula

      http://en.wikipedia.org/wiki/Haversine_formula

      calculate the distance
    */

    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}

//BD-09(百度)坐标转换成GCJ-02(火星，高德)坐标
//@param bd_lon 百度经度
//@param bd_lat 百度纬度
function bd_decrypt($bd_lon,$bd_lat){
    $data['gg_lon'] = round($bd_lon, 6);
    $data['gg_lat'] = round($bd_lat, 6);
    return $data;
    // 换回百度
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $bd_lon - 0.0065;
    $y = $bd_lat - 0.006;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    // $data['gg_lon'] = $z * cos($theta);
    // $data['gg_lat'] = $z * sin($theta);
    $gg_lon = $z * cos($theta);
    $gg_lat = $z * sin($theta);
    // 保留小数点后六位
    $data['gg_lon'] = round($gg_lon, 6);
    $data['gg_lat'] = round($gg_lat, 6);
    return $data;
}

//GCJ-02(火星，高德)坐标转换成BD-09(百度)坐标
//@param bd_lon 百度经度
//@param bd_lat 百度纬度
function bd_encrypt($gg_lon,$gg_lat){
    $data['bd_lon'] = round($gg_lon, 6);
    $data['bd_lat'] = round($gg_lat, 6);
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $gg_lon;
    $y = $gg_lat;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    $bd_lon = $z * cos($theta) + 0.0065;
    $bd_lat = $z * sin($theta) + 0.006;
    // 保留小数点后六位
    $data['bd_lon'] = round($bd_lon, 6);
    $data['bd_lat'] = round($bd_lat, 6);
    return $data;
}

/**
 * 字节转换
 * @param
 * @author
 * @return
 */
function formatBytes($size) {
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, 2).$units[$i];
}

/**
 *	输出pdf
 * 	@param 	$content 	文档内容
 * 	@param 	$aspect		打印方向，默认 y 纵向，x横向
 *	@param  $download	是否下载
 * 	@return viod
 */
function pdf($content = '', $aspect = 'y', $download = false, $pdfname){
    $aspect_arr = array('y' => 'A4', 'x' => 'S4');
    $aspect = $aspect_arr[$aspect];
    import("@.ORG.mpdf.mpdf", '', '.php');
    // 实例化对象 中文编码 横/纵向排版 默认字体大小 默认字体 左页边距 右页边距 上页边距 下页边距
    $mpdf = new mPDF('utf-8', $aspect, '', '', 10, 10, 10, 10);
    $mpdf->autoScriptToLang = true;
    $mpdf->autoLangToFont = true;
    $mpdf->WriteHTML($content);
    if ($download) {
        if ($pdfname != '') $pdfname = $pdfname.'.pdf';
        $mpdf->Output($pdfname, true);
        $mpdf->Output($pdfname, 'd');
    }
    $mpdf->Output();
    exit;
}


/**
 *	模板打印功能	自定义打印模板解析变量
 *	@param $comtent 	模板内容
 *  @param $variable 	变量
 *					array(
 *						'module1' => array('key1' => 'val1', ...),
 *						...
 *					);
 *	@param $each 	array	需要遍历的变量
 *					array(
 *						array('key1' => 'val1', ...),
 *						...
 *					);
 *	@return $Content 	解析后的模板
 */
function analysis($content, $variable = array(), $each = array())
{
    $default_array = array(); // 各变量的默认值 用于预览时
    // 内容首部到标记处
    $start_str = strstr($content, 'php="each"', 1);
    if ($start_str) {
        // 标记处所在标签索引
        $start_index = strrpos($start_str, '<');
        // 标记处标签到尾部
        $str = substr($content, $start_index);
        // 查看标签名
        $node_name = explode(' ', $str);
        $node_name = $node_name[0];
        $node_name = str_replace('<', '', $node_name);
        // 查找结束标签
        $end_index = strpos($str, '</'.$node_name);
        // 需要遍历的部分
        $each_str = substr($content, $start_index, ($end_index + strlen($start_str)) - $start_index + 1);
        $each_after_str = '';
        if (empty($each)) {
            preg_match_all("/{{[\w]+\.[\w]+}}/", $each_str, $res);
            $old = array();
            $new = array();
            foreach ($res[0] as $key => $val) {
                $old[] = $val;
                $val = str_replace('{{', '', $val);
                $val = str_replace('}}', '', $val);
                if (array_key_exists($val, $default_array)) {
                    $new[] = $default_array[$val];
                } else {
                    $new[] = 'XXX(示例)';
                }
            }
            for ($i=0; $i < 5; $i++) {
                $each_after_str .= str_replace($old, $new, $each_str);
            }
        } else {
            foreach ($each as $key => $val) {
                foreach ($val as $k => $v) {
                    $old = array();
                    $new = array();
                    foreach ($v as $kk => $vv) {
                        $old[] = '{{' . $key . '.' . $kk . '}}';
                        $new[] = $vv;
                    }
                    $each_after_str .= str_replace($old, $new, $each_str);
                }
            }
        }
        $content = str_replace($each_str, $each_after_str, $content);
    }

    if (empty($variable)) {
        preg_match_all("/{{[\w]+\.[\w]+}}|{{[\w]+}}/", $content, $res);
        $old = array();
        $new = array();
        foreach ($res[0] as $key => $val) {
            $old[] = $val;
            $val = str_replace('{{', '', $val);
            $val = str_replace('}}', '', $val);
            if (array_key_exists($val, $default_array)) {
                $new[] = $default_array[$val];
            } else {
                $new[] = 'XXX(示例)';
            }
        }
        $content = str_replace($old, $new, $content);
    } else {
        foreach ($variable as $key => $val) {
            $old = array();
            $new = array();
            foreach ($val as $k => $v) {
                if (strlen($v) == 10 && intval($v) == $v) {
                    $v = date('Y-m-d', $v);
                }
                $old[] = !!$key ? ('{{' . $key . '.' . $k . '}}') : ('{{' . $k . '}}');
                $new[] = $v;
            }
            $content = str_replace($old, $new, $content);
        }
    }

    return $content;
}


/**
 * 打印  dump die;
 * @param	mixed	需要打印的对象，如果是继承Model类的对象会打印最后的sql
 */
function dd()
{
    call_user_func_array('ddd', func_get_args());
    die(1);
}

/**
 * 打印  dump
 * @param	mixed	需要打印的对象，如果是继承Model类的对象会打印最后的sql
 */
function ddd()
{
    // 引入y_dump
    $composer = import('@.ORG.Composer.autoload', '', '.php');
    // 打印函数
    $dump_func = $composer && function_exists('y_dump') ? 'y_dump' : 'dump';
    header('Content-Disposition: ');		// 文件名
    header("Content-type:text/html; charset=utf-8");
    // 调用位置
    $files = debug_backtrace();
    $file = $files[0];
    if ($files[1]['function'] == 'call_user_func_array') {
        $file = $files[2];
    }
    echo '<p style="font-size: 14px; color: #3D80DA">' . $file['file'] . ':' .$file['line'].'行' . '</p>';
    foreach (func_get_args() as $key => $val) {
        if ($val instanceof Model) {
            sqlFormat($val->getlastsql());
        } else {
            $dump_func($val);
        }
    }
}


/**
 * 接口打印
 */
function vdd()
{
    header('Content-Type:application/json; charset=utf-8');
    $param = func_get_args();
    $arr = array();
    foreach ($param as $val) {
        if ($val instanceof Model) {
            $arr[] = $val->getlastsql();
        } else {
            $arr[] = $val;
        }
    }
    $file = debug_backtrace();
    $file_info = pathinfo($file[0]['file']);
    array_push($arr, $file_info['basename'] . ':' .$file[0]['line'].'行');
    echo json_encode($arr);
    die(1);
}

/**
 * 调试方法
 * @author lee
 **/
function p($data, $offset=true){
    echo '<pre><meta charset="utf-8">';
    print_r($data);
    //dump($data);
    echo '</pre>';

    if($offset){
        die;
    }
}

/**
 * curl 模拟GET请求
 * @author lee
 ***/
function curl_get($url){
    //初始化
    $ch = curl_init();
    //设置抓取的url
    curl_setopt($ch, CURLOPT_URL, $url);

    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // https请求 不验证hosts

    //执行命令
    $output = curl_exec($ch);

    curl_close($ch); //释放curl句柄

    return $output;
}

/**
 * curl 模拟POST请求
 * @author lee
 ***/
function curl_post($url,$post_data=array()){
    $post_data = json_encode($post_data);
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    //curl_setopt($curl, CURLOPT_HEADER, 1);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    //设置post数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);

    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($post_data)
    ));

    //执行命令
    $data = curl_exec($curl);

    //关闭URL请求
    curl_close($curl);
    return $data;
}

/**
 * 地址转地理坐标
 * @author lee
 **/
function get_lng_lat($address){
    $map_ak = 'grWGxlWOpGc1D0kVToxUgD6bwwjo35Tr';
    $url = "http://api.map.baidu.com/geocoder/v2/?address=$address&output=json&ak=$map_ak&callback=showLocation";

    $ret_script = curl_get($url);
    preg_match_all("/\{.*?\}}/is", $ret_script, $matches);
    $ret_arr = json_decode($matches[0][0],true);
    if ($ret_arr['status'] == 0) { //成功
        $location['lng'] = $ret_arr['result']['location']['lng'];
        $location['lat'] = $ret_arr['result']['location']['lat'];
        return $location;
    } else {
        return false;
    }
}

/**
 * 根据经纬度点和半径，计算经纬度范围
 * @param lng 经度 lat 纬度  半径raidus 单位米
 * return minLat,minLng,maxLat,maxLng
 * @author lee
 */
function get_around($lng,$lat,$raidus){
    $PI = 3.14159265;

    $latitude = $lat;
    $longitude = $lng;

    $degree = (24901*1609)/360.0;
    $raidusMile = $raidus;

    $dpmLat = 1 / $degree;
    $radiusLat = $dpmLat*$raidusMile;
    $minLat = $latitude - $radiusLat;
    $maxLat = $latitude + $radiusLat;

    $mpdLng = $degree*cos($latitude * ($PI/180));
    $dpmLng = 1 / $mpdLng;
    $radiusLng = $dpmLng*$raidusMile;
    $minLng = $longitude - $radiusLng;
    $maxLng = $longitude + $radiusLng;

    $data['min_lng'] = $minLng;
    $data['max_lng'] = $maxLng;
    $data['min_lat'] = $minLat;
    $data['max_lat'] = $maxLat;
    return $data;
}

/**
 * 过滤post\get数据html
 * @param
 * @author
 */
function removehtml($str){
    if (!is_array($str)) {
        $str = str_replace("<br>","",$str);
        $str = strip_tags($str);
    } else {
        foreach ($str as $key=>$val) {
            $str[$key] = removehtml($val);
        }
    }
    return $str;
}

/**
 * 二维数组取一列   PHP5.5+ 支持
 *
 * @param	array	$array		二维数组
 * @param	mixed	$field  	要取的字段
 * @param	mixed	$index_key	取一列作键
 * @return	mixed	$res		成功返回Array，失败返回False
 */
function y_array_column(Array $array, Mixed $field, Mixed $index_key = null)
{
    if (!is_array($array)) return false;
    $res = array();
    foreach ($array as $arr) {
        if (!is_array($arr)) return false;
        if (is_null($field)) {
            $value = $arr;
        } else {
            $value = $arr[$field];
        }
        if (is_null($index_key)) {
            $res[] = $value;
        } else {
            $key = $arr[$index_key];
            $res[$key] = $value;
        }
    }
    return $res;
}

/**
 * 弹框发送短信校验
 */
function check_alert_sms()
{
    $current_sms_num = getSmsNum();
    $current_sms_num = trim($current_sms_num);
    if ((string)($current_sms_num + 0) != $current_sms_num) {
        if ($current_sms_num + 0 == -3) {
            if (session('?admin')) {
                $info = '未进行配置，请购买服务。';
            } else {
                $info = '未进行配置，请联系管理员。';
            }
        } else if ($current_sms_num + 0 == -4) {
            if (session('?admin')) {
                $info = '余额不足，请进行充值。';
            } else {
                $info = '余额不足，请联系管理员。';
            }
        }
        echo '<span>暂时不能发送短信,原因如下：'. $current_sms_num .'。</span><span style="color: red; margin-left: 20px;">'. $info .'</span>';
        echo "<script>$('[aria-describedby=\"dialog-sendsms\"]').find('.ui-dialog-buttonset button').eq(0).hide();</script>";
        exit;
    }
    return $current_sms_num;
}
/**
 * 授权信息判断
 * @param
 * @author
 */
function kakarote_anthorize(){
    $authorize_setting = array();
    $key_file = file_get_contents(CONF_PATH.'license.dat');
    $is_anthorize = true;
    if ($key_file) {
        //本地部署
        $key_info = F('key_info');
        //解密
        import("@.ORG.Kakarote");
        $key = 'KAKAROTE@pdcrm！@#'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
        $des = new Kakarote();
        $dre_key_info = $des->decrypt(hex2bin($key_info), $key);
        $dre_key_info = unserialize($dre_key_info);

        $authorize_setting['NUM'] = $dre_key_info['number'];
        $authorize_setting['ENDTIME'] = date('Y-m-d', $dre_key_info['end_time']);
        if ($dre_key_info['end_time'] && (time() > strtotime($authorize_setting['ENDTIME']))) {
            $is_anthorize = false;
        }
    } else {
        //云平台
        $authorize_setting = C('AUTHORIZE_SETTING');

        if ($authorize_setting['NUM'] <= 5 && !empty($authorize_setting['OPENTIME']) && intval($authorize_setting['OPENTIME']) > 20171116) {
            if ($authorize_setting['NUM'] > 2 || C('PSS_STATUS') == true) {
                // 2人及以下免费用户，如果开通进销存，则也需要到期时间判断
                if (time() > strtotime($authorize_setting['ENDTIME'])) {
                    $is_anthorize = false;
                }
            }
        } else {
            if (($authorize_setting['NUM'] > 5)) {
                if (time() > strtotime($authorize_setting['ENDTIME'])) {
                    $is_anthorize = false;
                }
            }
        }
    }

    if (M('User')->count() > $authorize_setting['NUM']) {
        $is_anthorize = false;
    }
    return $is_anthorize;
}

/**
 * 数据执行修改时，判断数据是否变化
 */
function checkDataDifficult($new_data, $old_data)
{
    foreach ($new_data as $key => $val) {
        if ($val != $old_data[$key]) return true;
    }
    return false;
}

/**
 * html自定义属性
 * 使用场景：方便父页面获取弹窗列表的属性值
 * @author lee
 */
function custom_attr($data){
    foreach($data as $k => $v){
        if ($data[$k]) {
            $custom_attr .= $k.'="'.$v.'"';
        }
    }
    return $custom_attr;
}

/**
 * 时间插件
 * @author lee
 */
function daterange(){
    //时间插件处理（计算开始、结束时间距今天的天数）
    $daterange = array();
    $daterange[0]['title'] = '上月';
    $daterange[1]['title'] = '本月';
    $daterange[2]['title'] = '上季度';
    $daterange[3]['title'] = '本季度';
    $daterange[4]['title'] = '上一年';
    $daterange[5]['title'] = '本年';
    //上个月
    $daterange[0]['start_day'] = (strtotime(date('Y-m-d',time()))-strtotime(date('Y-m-d', mktime(0,0,0,date('m')-1,1,date('Y')))))/86400;
    $daterange[0]['end_day'] = (strtotime(date('Y-m-d',time()))-strtotime(date('Y-m-01 00:00:00')))/86400 + 1;
    //本月
    $daterange[1]['start_day'] = (strtotime(date('Y-m-d',time()))-strtotime(date('Y-m-01 00:00:00')))/86400;
    $daterange[1]['end_day'] = 0;
    //上季度
    $month = date('m');
    if($month==1 || $month==2 ||$month==3){
        $year = date('Y')-1;
        $daterange_start_time = strtotime(date($year.'-10-01 00:00:00'));
        $daterange_end_time = strtotime(date($year.'-12-31 23:59:59'));
    }elseif($month==4 || $month==5 ||$month==6){
        $daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
        $daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
    }elseif($month==7 || $month==8 ||$month==9){
        $daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
        $daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
    }else{
        $daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
        $daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
    }
    $daterange[2]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
    $daterange[2]['end_day'] = (strtotime(date('Y-m-d',time()))-$daterange_end_time-1)/86400 + 1;
    //本季度
    $month=date('m');
    if($month==1 || $month==2 ||$month==3){
        $daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
        $daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
    }elseif($month==4 || $month==5 ||$month==6){
        $daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
        $daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
    }elseif($month==7 || $month==8 ||$month==9){
        $daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
        $daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
    }else{
        $daterange_start_time = strtotime(date('Y-10-01 00:00:00'));
        $daterange_end_time = strtotime(date("Y-12-31 23:59:59"));
    }
    $daterange[3]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
    $daterange[3]['end_day'] = 0;
    //上一年
    $year = date('Y')-1;
    $daterange_start_time = strtotime(date($year.'-01-01 00:00:00'));
    $daterange_end_time = strtotime(date('Y-01-01 00:00:00'));
    $daterange[4]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
    $daterange[4]['end_day'] = (strtotime(date('Y-m-d',time()))-$daterange_end_time)/86400 + 1;
    //本年度
    $daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
    $daterange[5]['start_day'] = (strtotime(date('Y-m-d',time()))-$daterange_start_time)/86400;
    $daterange[5]['end_day'] = 0;
    return $daterange;
}


/**
 * 检查是否可以访问进销存相关入口
 * @param
 * @author lee
 */
function chekc_pss_access($m, $a){
    switch (strtolower($m)) {
        case 'stock':
        case 'supplier':
        case 'purchase':
        case 'warehouse':
        case 'goods':
        case 'sales':
            return true;
            break;
        default:
            return false;
            break;
    }
}

/**
 * dialog弹窗，无权操作提示
 * @param lee
 */
function dialogAlert($msg){
    if (IS_AJAX) {
        if (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false) {
            //$.load
            echo '<div class="alert alert-error">'.$msg.'</div>';
            die();
        } else if (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false){
            //json
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode(array('info' => $msg, 'msg' => $msg, 'status' => -1)));
        }
    } else {
        alert('error', $msg, $_SERVER['HTTP_REFERER']);
    }
}


/**
 * 阿里云OSS上传文件 密钥生成
 * @param	string		$callbackUrl	上传成功后的回调地址
 * @param	string		$dir			上传成功后的目录
 */
function aliOssToken($host, $callbackUrl = null, $dir = '')
{
    $d_file = D('File');

    $callback_param = array(
        'callbackUrl' => $callbackUrl,
        'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}&oldname=${x:oldname}&module=${x:module}&r=${x:r}&id=${x:id}&role_id=${x:role_id}',
        'callbackBodyType' => "application/x-www-form-urlencoded"
    );
    $callback_string = json_encode($callback_param);

    $base64_callback_body = base64_encode($callback_string);

    function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }

    // 设置该policy超时时间是10分钟. 即这个policy过了这个有效时间，将不能访问
    $end = time() + 600;
    $expiration = gmt_iso8601($end);

    //最大文件大小.用户可以自己设置
    $condition = array(0 => 'content-length-range', 1 => 0, 2 => $d_file::MAX_UPLOAD_SIZE);
    $conditions[] = $condition;

    //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
    $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
    $conditions[] = $start;
    $arr = array('expiration' => $expiration,'conditions' => $conditions);
    $policy = json_encode($arr);
    $base64_policy = base64_encode($policy);
    $string_to_sign = $base64_policy;
    $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $d_file::OSS_KEY, true));

    $response = array();
    $response['accessid'] = $d_file::OSS_ID;
    $response['host'] = $host;
    $response['policy'] = $base64_policy;
    $response['signature'] = $signature;
    $response['expire'] = $end;
    $response['callback'] = $base64_callback_body;
    //这个参数是设置用户上传指定的前缀
    $response['dir'] = $dir;

    return $response;
}



/**
 * 接口请求时，获取完整的服务器路径
 */
function getServerName()
{
    /*$scheme = $_SERVER['REQUEST_SCHEME'] . '://';		// 协议
    $server_name = $_SERVER['SERVER_NAME'];				// 域名/IP
    $port = ':' . $_SERVER['SERVER_PORT'];				// 端口
    $dir = dirname($_SERVER['REQUEST_URI']);			// 项目目录
    return $scheme . $server_name . $port . $dir;*/
    // is_ssl()是tp的函数在functions.php中
    return (is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST'];
}


/**
 * 文件路径处理
 * not_head 默认false 头像，ture 非头像文件
 */
function headPathHandle($file, $not_head = false)
{
    $server_name = getServerName();
    $d_file = D('File');
    if ($file != '') {
        if (strpos($file, '/Uploads') === false) {
            // 没有'/Uploads'字符，说明文件是上传到OSS，而非本地服务器
            return  $d_file::FILE_URL.'/'.$file;
        } else {
            if (!empty($_REQUEST['token'])) {
                // 手机端
                //return $server_name . $file; //055178
                return  $file;
            } else {
                return $file;
            }
        }
    } else {
        if ($not_head) {
            return '';
        }
        if (!empty($_REQUEST['token'])) {
            // 手机端
            //return $server_name . 'Public/img/avatar_default.png';
            return 'Public/img/avatar_default.png'; //055178
        } else {
            return '__PUBLIC__/img/avatar_default.png';
        }
    }
    return '';
}


/**
 * 判断采购单[type_id=1]、采购退货单[type_id=2]、销售退货单[type_id=3]、库存调拨单[type_id=4]是否可删除
 * 只能删除审核状态是待审或驳回，且没有出入库记录的订单
 * @return 返回值 true 可删除 false 不可删除
 * @author lee
 */
function order_can_del($type_id, $order_id)
{
    // 判断审核状态
    $r_exam = M('RExam');
    $exam_status = $r_exam->where(array('type_id' => $type_id, 'order_id' => $order_id))->getField('exam_status');
    if ($exam_status == 1 || $exam_status == 2) {
        return false;
    }
    // 判断是否已有出入库记录
    switch ($type_id) {
        case '1':
            if (M('StockIn')->where(array('type' => 1, 'type_id' => $order_id))->find()) {
                return false;
            }
            break;
        case '2':
            if (M('StockOut')->where(array('type' => 2, 'type_id' => $order_id))->find()) {
                return false;
            }
            break;
        case '3':
            if (M('StockIn')->where(array('type' => 2, 'type_id' => $order_id))->find()) {
                return false;
            }
            break;
        case '4':
            if (M('StockIn')->where(array('type' => 3, 'type_id' => $order_id))->find()) {
                // 调拨单只判断有无入库即可
                return false;
            }
            break;
        default:
            break;
    }
    return true;
}


/**
 * 添加操作记录【目前仅支持信息"编辑"时】
 * @author lee
 */
function recordAction($new_data, $old_data, $model, $id)
{
    $m_customer = M('Customer');
    $m_contacts = M('Contacts');
    $m_business = M('Business');
    $m_user = M('User');

    if ($model == 'setting') {
        $field_list = D('Setting')->configField();
    } else {
        $custom_field_list = M('Fields')->where('model = "'.$model.'"')->field('field,name,form_type')->order('order_id')->select();
        foreach ($custom_field_list as $k => $v) {
            $field_list[$v['field']] = $v;
        }
    }

    // 合同特殊字段追加
    if ($model == 'contract') {
        $field_list['owner_role_id'] = array('field' => 'owner_role_id', 'name' => '合同签约人', 'form_type' => 'role');
    }

    $diff_data = array_diff_assoc($new_data, $old_data);
// p($field_list,'');
// p($diff_data);
    foreach ($diff_data as $k => $v) {
        $field_info = $field_list[$k];
        if ($field_info) {
            if ($field_info['form_type'] == 'datetime') {
                $old_value = date('Y-m-d', $old_data[$k]);
                $new_value = date('Y-m-d', $v);
            } elseif ($field_info['form_type'] == 'role') {
                $old_value = $m_user->where('role_id = %d', $old_data[$k])->getField('full_name');
                $new_value = $m_user->where('role_id = %d', $v)->getField('full_name');
            } elseif ($field_info['field'] == 'customer_id') {
                $old_value = $m_customer->where('customer_id = %d', $old_data[$k])->getField('name');
                $new_value = $m_customer->where('customer_id = %d', $v)->getField('name');
            } elseif ($field_info['field'] == 'contacts_id') {
                $old_value = $m_contacts->where('contacts_id = %d', $old_data[$k])->getField('name');
                $new_value = $m_contacts->where('contacts_id = %d', $v)->getField('name');
            } elseif ($field_info['field'] == 'business_id') {
                $old_value = $m_business->where('business_id = %d', $old_data[$k])->getField('code');
                $new_value = $m_business->where('business_id = %d', $v)->getField('code');
            } else {
                $old_value = $old_data[$k];
                $new_value = $v;
            }
            $action_record .= '<div>将 '.$field_info['name'].' 由 <span style="color:#77B0E9">"'.$old_value.'"</span> 修改为 <span style="color:#E7AE6F">"'.$new_value.'"</span> </div>';
        }
    }
    $record['create_time'] = time();
    $record['create_role_id'] = session('role_id');
    $record['type'] = '修改';
    $record['duixiang'] = $action_record;
    $record['model_name'] = $model;
    $record['action_id'] = $id;
    M('ActionRecord')->add($record);
}


/**
 * 查询操作记录
 * @author lee
 */
function actionRecord($action_id, $model_name)
{
    $m_action_record = M('ActionRecord');
    $m_user = M('User');

    $r_where['action_id'] = $action_id;
    $r_where['model_name'] = $model_name;
    $group_time = $m_action_record->where($r_where)->group('create_time')->order('id desc')->getField('create_time',true);
    foreach($group_time as $k=>$v){ //转换为时间戳
        $group_date[] = date('Y-m-d',$v);
    }
    $group_date = array_flip(array_flip($group_date));
    $group_list = array();
    foreach($group_date as $key=>$val){
        $start_time = strtotime($val);
        $end_time = strtotime($val)+86399;
        $t_where['create_time'] = array('between',array($start_time,$end_time));
        $t_where['action_id'] = $action_id;
        $t_where['model_name'] = $model_name;
        $action_list = $m_action_record->where($t_where)->order('id desc')->select();
        $group_list[$key]['week_name'] = getTimeWeek(strtotime($val));
        $group_list[$key]['create_date'] = date('m-d',strtotime($val));
        foreach($action_list as $k=>$v){
            $create_role_info = $m_user->where('role_id = %d',$v['create_role_id'])->field('role_id,full_name,thumb_path')->find();
            $action_list[$k]['create_role_info'] = $create_role_info; //获取修改人信息
            switch($v['type']){
                case '修改' : $i_class = 'fa fa-square-o'; $color_class = 'ai-yellow'; break;
                case '放入客户池' : $i_class = 'fa fa-archive'; $color_class = 'ai-red'; break;
                case '客户分享' : $i_class = 'fa fa-user'; $color_class = 'ai-green'; break;
                case '取消分享' : $i_class = 'fa fa-user'; $color_class = 'ai-red'; break;
                case '提醒' : $i_class = 'fa fa-eye'; $color_class = 'ai-purple'; break;
                case '移除' : $i_class = 'fa fa-eye'; $color_class = 'ai-red'; break;
                case '分配' : $i_class = 'fa fa-check-square'; $color_class = 'ai-green'; break;
                case '领取' : $i_class = 'fa fa-square-o'; $color_class = 'ai-red'; break;
                case '客户转移' : $i_class = 'fa fa-share-square-o'; $color_class = 'ai-orange'; break;
            }
            $action_list[$k]['i_class'] = $i_class;
            $action_list[$k]['color_class'] = $color_class;
        }
        $group_list[$key]['action_list'] = $action_list;
    }
    return $group_list;
}




/**
 * 导出SCV
 * @param	string		$file		文件名
 * @param	array		$head		表头 二维数组 如果指定字段的话
 * @param	int			$count		数据总条数
 * @param	callback	$callback	回调函数、返回数据，详见示例
 * @author	shen
 */
function csvExport(string $file, array $table_head, int $count, callback $callback)
{
    // 示例
    if (false) {
        $m_test = M('Test');
        $count = $m_test->count();
        $t_head = array(
            array('pdcrm导出数据'),
            // 键名可不指定,导出所有字段,指定键名则导出指定字段
            // 多维数组用 . 分隔  如果字段名内含有 . ,在匿名函数内进行处理完
            'field' => array('name' => '姓名', 'age' => '年龄', 'sex' => '性别')
        );
        csvExport('file_name', $t_head, $count, function($page) use ($m_test) {
            return $m_test->Page($page)->select();
        });
    }

    ini_set('memory_limit', '128M');		// 内存上限
    set_time_limit(0);		//执行超时时间
    header('Content-type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment;filename="' . $file . '.csv"');		// 文件名
    header('Cache-Control: max-age=0');		// 禁用缓存

    header('Expires: 0');
    header('Cache-control: private');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');
    header('Content-Encoding: UTF-8');
    echo "\xEF\xBB\xBF"; 	// UTF-8 BOM

    // global $fp;
    $fp = fopen('php://output', 'a');	//直接输出到浏览器
    foreach ($table_head as $v) {
        fputcsv($fp, $v);
    }
    $page_size = 10000;
    for ($i = 1; $i <= ceil($count / $page_size); $i++) {
        $data = $callback($i . ',' . $page_size);	// 传入page 分页参数
        foreach ($data as $val) {
            $row = array();
            if (isset($table_head['field'])) {
                foreach ($table_head['field'] as $k => $v) {
                    $fields = explode('.', $k);
                    if (count($fields) > 1) {
                        $temp = $val;
                        foreach ($fields as $field) {
                            $temp = $temp[$field];
                        }
                    } else {
                        $temp = $val[$fields[0]];
                    }
                    // if (is_numeric($temp)) {
                    // 	$temp = ' ' . $temp;
                    // }
                    $row[$v] = $temp . "\t";
                }
            } else {
                foreach ($val as $v) {
                    // if (is_numeric($temp)) {
                    // 	$temp = ' ' . $temp;
                    // }
                    $row[$v] = $temp . "\t";
                }
            }
            fputcsv($fp, $row);
        }
        unset($data);
        ob_flush();
        flush();
    }
    exit();
}


/**
 * 格式化sql语句
 */
function sqlFormat($sql)
{
    if (import('@.ORG.SqlFormatter', '', '.php')) {
        $dump_func = function_exists('y_dump') ? 'y_dump' : 'dump';
        echo '<div style="padding: 0 20px; border: 1px solid #ccc; border-radius:20px;">' . SqlFormatter::format($sql) . '</div>';
        $dump_func(M()->query($sql));
    } else {
        echo '<div>' . $sql . '</div>';
    }
}


/**
 * 读取csv数据
 */
function csvRead($file = '', $start_line = 0, $end_line = 10, $fields = array(), $callback = null)
{
    ini_set('memory_limit','128M');
    set_time_limit (0);
    $i = $c = 0;
    $data = array();
    $f_count = count($fields);
    $max = 1000;
    while ($line = fgetcsv($file)) { //每次读取CSV里面的一行内容
        $i++;
        if ($i < $start_line) continue;
        if ($i > $end_line) break;
        if ($f_count >= $l_count = count($line)) {
            $line = array_pad($line, $f_count, '');
            $line = array_combine($fields, $line);
        }
        $data[] = array_map('iconv', array_pad(array(), count($line), 'GB2312'), array_pad(array(), count($line), 'utf-8'), $line);
        if ($callback !== null) {
            if (count($data) > ($c + 1) * $max) {
                $callback(array_slice($data, $c * $max, $max));
                $c++;
            }
        }
    }
    if ($callback !== null) {
        $callback(array_slice($data, $c * $max, $max));
    }
    return $data;
}


/**
 * 读取excel数据
 * @param	string		$file
 * @param	int			$start_line		开始行数（包含）
 * @param	int			$end_line		结束行数（包含） 0 表示所有行
 * @param	array		$fields			字段 必填 同列数一致 必须索引数组
 * @param	callback	$callback		回调
 * @param	int			$max			回调函数执行周期
 */
function excelRead($file = '', $start_line = 0, $end_line = 0, $fields = array(), $callback = null, $max = 100)
{
    ini_set('memory_limit','128M');
    set_time_limit (0);
    $fields = array_values($fields);
    $fields_count = count($fields);
    import("ORG.PHPExcel.PHPExcel");
    $PHPExcel = new PHPExcel();
    $PHPReader = new PHPExcel_Reader_Excel2007();
    if(!$PHPReader->canRead($file)){
        $PHPReader = new PHPExcel_Reader_Excel5();
    }
    $PHPExcel = $PHPReader->load($file);
    $currentSheet = $PHPExcel->getSheet(0);
    $allRow = $currentSheet->getHighestRow();
    $data = array();
    if ($end_line == 0) {
        $max_row = $allRow;
    } else {
        $max_row = $end_line > $allRow ? $allRow : $end_line;
    }
    $c = 0;		// 回调函数执行次数
    for ($row = $start_line; $row <= $max_row; $row++) {
        $line = array();
        $null_count = 0;
        foreach ($fields as $k => $v) {
            $col = PHPExcel_Cell::stringFromColumnIndex($k);
            if (null === $temp_val = $currentSheet->getCell($col.$row)->getValue()) {
                $temp_val = '';
                $null_count++;
            }
            $line[$v] = $temp_val;
        }
        if ($null_count == $fields_count) {
            break;
        }
        $data[$row] = $line;
        if ($callback !== null) {
            if (count($data) > ($c + 1) * $max) {
                $callback(array_slice($data, $c * $max, $max, true));
                $c++;
            }
        }
    }
    if ($callback !== null) {
        $callback(array_slice($data, $c * $max, $max, true));
    }
    return $data;
}


/**
 * 内存占用
 * @param	int			$before		使用前占用内存	单位 bit
 * @param	string		$unit		单位 bit,B,K,M,G
 */
function ramUsage($before = null, $unit = '')
{
    if ($before === null) {
        $before = memory_get_usage();
    }
    $ram = memory_get_usage() - $before;
    switch (strtoupper($unit)) {
        case 'BIT':
            return number_format($ram, 2) . 'bit';
            break;
        case 'B':
            return number_format($ram / 8, 2) . 'B';
            break;
        case 'K':
            return number_format($ram / (1024 * 8), 2) . 'K';
            break;
        case 'M':
            return number_format($ram / (pow(1024, 2) * 8), 2) . 'M';
            break;
        case 'G':
            return number_format($ram / (pow(1024, 3) * 8), 2) . 'G';
            break;
        default :
            if ($ram < 8) {
                return number_format($ram, 2) . 'bit';
            } elseif (($ram / 8) < 1024) {
                return number_format($ram / 8, 2) . 'B';
            } elseif (($ram / (1024 * 8)) < 1024) {
                return number_format($ram / (1024 * 8), 2) . 'K';
            } elseif (($ram / (pow(1024, 2) * 8)) < 1024) {
                return number_format($ram / (pow(1024, 2) * 8), 2) . 'M';
            } else{
                return number_format($ram / (pow(1024, 3) * 8), 2) . 'G';
            }
            break;
    }
}

/**
 * 性能测试
 * @param 	callback	$callback	需要测试的函数
 * @param	int			$lop		执行次数
 */
function test($callback = null, $loop = 1)
{
    $s_ram = memory_get_usage();
    $s_time = microtime(true);

    if ($callback !== null) {
        for ($i = 0; $i < $loop; $i++) {
            $callback();
        }
    }

    return array(
        'time' => number_format(microtime(true) - $s_time, 4) . 's',
        'ram' => ramUsage('', $s_ram)
    );
}


/**
 * 导出csv
 * @param $file_name 导出文件名称
 * @param $field_list 导出字段列表 ['create_time' => ['name' => '创建时间', 'form_type' => 'date']]
 * @param $total_export_count 前台设置的单次导出总行数
 * @param $callback 回调函数，查询需要导出的数据
 * @author lee
 **/
function exportCsv($file_name, $field_list, $total_export_count, $already_export_count,callback $callback)
{
    ini_set('memory_limit','128M');
    set_time_limit (0);

    //调试时，先把下面这个两个header注释即可
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:filename=" . $file_name . ".csv");

    header('Expires: 0');
    header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Export');
    header('Content-Encoding: UTF-8');
    // 加上bom头，防止用office打开时乱码
    echo "\xEF\xBB\xBF"; 	// UTF-8 BOM

    session('export_status', 1);

    // 打开PHP文件句柄，php://output 表示直接输出到浏览器
    $fp = fopen('php://output', 'a');

    // 将中文标题转换编码，否则乱码
    foreach ($field_list as $i => $v) {
        $title_cell[$i] = $v['name'];
    }
    // 将标题名称通过fputcsv写到文件句柄
    fputcsv($fp, $title_cell);

    // 后台设置每次导出条数【后台在前台设置的单次导出条数基础上再次分段导出】
    $each_count = 1000;
    // 计算后台需要导出次数【等于1说明前台设置的每次导出条数小于后台的设置的1000，那么后台单次导出条数按前台的数量导出】
    $export_times = ceil($total_export_count / $each_count);
    if ($export_times == 1) {
        $each_count = $total_export_count;
    }
    for ($i = 0; $i < $export_times; $i++){
        $limit_start = $i * $each_count + $already_export_count;
        $export_data = $callback($limit_start.','.$each_count);
        foreach ($export_data as $item) {
            $rows = array();
            foreach ($field_list as $filed_name => $rule) {
                $rows[] = formatValue($item[$filed_name], $rule['form_type']);
            }
            fputcsv($fp, $rows);
        }
        // 将已经写到csv中的数据存储变量销毁，释放内存占用
        // $m = memory_get_usage();
        unset($export_data);
        // $mm = memory_get_usage();
        // p('data:'.$m.'---'.$mm,'');
        ob_flush();
        flush();
    }
    fclose($fp);
    session('export_status', 0);
    exit();
}


/**
 * 根据form_type处理数据
 * "\t" 间隔字符，解决导出的时间格式变成####和防止数字变成科学计数法
 * @author lee
 */
function formatValue($val, $form_type)
{
    if ($form_type == 'datetime') {
        $val = $val > 0 ? date('Y-m-d H:i:s')."\t" : '';
    } elseif ($form_type == 'date'){
        $val = $val > 0 ? date('Y-m-d')."\t" : '';
    } elseif ($form_type == 'address'){
        $val = implode('', explode(chr(10), $val));
    } elseif ($form_type == 'role'){
        $val = M('User')->where('role_id = %d', $val)->getField('full_name');
    } elseif ($form_type == 'mobile'){
        $val = "{$val}\t";
    } else if ($form_type == 'invoice.billing_type'){
        // 发票导出的发票类型
        switch ($val) {
            case '1':
                $val = '增值税普通发票'; break;
            case '2':
                $val = '增值税专用发票'; break;
            case '3':
                $val = '收据'; break;
            default:
                $val = ''; break;
        }
    }
    return $val;
}


/**
 * 判断数组是否是另一个数组的子集
 * @param	array	子集
 * @param	array	父集
 */
function is_subset($sub, $arr)
{
    $res = array_diff($sub, $arr);
    return empty($res);
}


/**
 * 根据url获取部门
 * @param	string	$m	模块
 * @param	string	$a	方法
 */
function getDepartmentByUrl($m = MODULE_NAME, $a = ACTION_NAME)
{
    $url = getCheckUrlByAction($m, $a);
    $per_type =  M('Permission') -> where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
    if($per_type == 2 || session('?admin')){
        $where = '';
    }else{
        $where = array('department_id' => session('department_id'));
    }
    return (array) M('RoleDepartment')->where($where)->select();
}


/**
 * highmaps插件，省份名称和标识的对应
 * @author lee
 */
function mapProvince()
{
    return array(
        'cn-sh' => '上海',
        'cn-zj' => '浙江',
        'cn-gs' => '甘肃',
        'cn-nx' => '宁夏',
        'cn-sa' => '陕西',
        'cn-ah' => '安徽',
        'cn-hu' => '湖北',
        'cn-gd' => '广东',
        'cn-fj' => '福建',
        'cn-bj' => '北京',
        'cn-hb' => '河北',
        'cn-sd' => '山东',
        'cn-tj' => '天津',
        'cn-js' => '江苏',
        'cn-ha' => '海南',
        'cn-qh' => '青海',
        'cn-jl' => '吉林',
        'cn-xz' => '西藏',
        'cn-xj' => '新疆',
        'cn-he' => '河南',
        'cn-nm' => '内蒙', // 古
        'cn-hl' => '黑龙', // 江
        'cn-yn' => '云南',
        'cn-gx' => '广西',
        'cn-ln' => '辽宁',
        'cn-sc' => '四川',
        'cn-cq' => '重庆',
        'cn-gz' => '贵州',
        'cn-hn' => '湖南',
        'cn-sx' => '山西',
        'cn-jx' => '江西',
        'tw-tw' => '台湾',
        'cn-6668' => '香港',
        'cn-3681' => '澳门',
        'cn-3664' => '南沙', // 群岛
    );
}


/**
 * 统计计算增长率
 * @param $val 本次值
 * @param $contrast_val 对比值
 * @author lee
 */
function growthRate($val, $contrast_val)
{
    if (empty($contrast_val)) {
        return '-';
    } else {
        $rate = round(($val - $contrast_val) / $contrast_val, 4) * 100;
        if ($rate > 0) {
            $rate_str = "{$rate}%".'<i class="fa fa-long-arrow-up" style="font-size: 16px;color: green;"></i>';
        } else if ($rate == 0){
            $rate_str = "{$rate}%";
        } else if ($rate < 0){
            $rate_str = "{$rate}%".'<i class="fa fa-long-arrow-down" style="font-size: 16px;color: red;"></i>';
        }
        return $rate_str;
    }
}


/**
 * 时间范围插件
 * @param	int		$start_date		开始时间	时间戳
 * @param	int		$end_date		结束时间	时间戳
 * @param	string	$value			input框默认值
 * @return	string	$data			JSON 字符串
 */
function dateRangePicker($start_date = null, $end_date = null, $value = null)
{
    $start_date = $start_date === null ? date('Y-m-01') : date('Y-m-d', $start_date);
    $end_date = $end_date === null ? date('Y-m-d') : date('Y-m-d', $end_date);
    $value = $value ?: $start_date .' - ' . $end_date;
    $date = daterange();	// ***
    $data = '{value: "'. $value .'", start_date: "'. $start_date .'", end_date: "'. $end_date .'","ranges":{';
    // $data .= "'今日' : [moment().startOf('day'), moment()],";
    // $data .= "'昨日' : [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],";
    foreach ($date as $val) {
        $data .=  $val['title'] ." : [moment().subtract('days', ". $val['start_day'] ."), moment().subtract('days', ". $val['end_day'] .")],";
    }
    $data .= '}}';
    return $data;
}


/**
 * 判断用户浏览器(内核)类型
 * @author lee
 */
function browserType()
{
     $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($http_user_agent, 'Chrome')) {
        return 'chrome'; // 谷歌
    } else if (strpos($http_user_agent, 'Firefox')){
        return 'firefox'; // 火狐
    } else if (strpos($http_user_agent, 'Trident')){
        return 'ie'; // IE
    }
}


/**
 * 获取当前控制器与方法名
 * @author  wxs
 */
function get_action_name()
{
    $wap_action = 'Wap_'.MODULE_NAME.':'.ACTION_NAME;
    return $wap_action;

}



