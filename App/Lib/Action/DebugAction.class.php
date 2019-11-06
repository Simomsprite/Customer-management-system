<?php

class DebugAction extends Action
{
    public function _initialize(){
		$action = array(
            'permission'=>array('kaoqin'),
            'allow'=>array('index', 'kaoqin')
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME, ACTION_NAME);
    }

    public function kaoqin()
    {
        $c = M('KaoqinConfig')->find();
        if ($_GET['act'] == 'bq') {
            $this->sb_time = date('H:i:s', strtotime($c['shangban_time']) - rand(600, 1800));
            $this->xb_time = date('H:i:s', strtotime($c['xiaban_time']) + rand(600, 1800));
            $this->date = $_GET['date'];
            $this->display('Kaoqin:bq');    
        } elseif (IS_POST) {
            $role_id = $_POST['role_id'] ?: session('role_id');
            $status = $_POST['status'] ?: 1;
            $token_id = $_POST['token_id'] ?: '4d49cd7ea775beda';
            $data = array(
                "role_id" => $role_id,
                "x" => "34.730987",
                "y" => "113.772746",
                "address" => "报国大厦",
                "status" => $status,
                "config_type" => 2,
                "shangban_time" => $c['shangban_time'],
                "xiaban_time" => $c['xiaban_time'],
                "token_id" => $token_id,
                "daka_time" => strtotime($_POST['date'] . ' ' . $_POST['time'])
            );
            $status_list = $status == 1 ? '1,2' : '3,4';
            $record = M('kaoqin')->where(array(
              	'role_id' => $role_id,
              	"status" => array('IN', $status_list),
              	'daka_time' => array('between', array(strtotime($_POST['date']), strtotime($_POST['date']) + 86400))
            ))->find();
            if ($record) {
                $this->ajaxReturn(array('msg' => '已有该记录', 'status' => 0));
            } elseif (M('kaoqin')->add($data)) {
                $this->ajaxReturn(array('msg' => '补签成功', 'status' => 1));
            } else {
                $this->ajaxReturn(array('msg' => '补签失败', 'status' => 0));
            }
        } else {
          	if ($_GET['kkrt'] != 'wukong') {
            	alert('error', L('您没有权限'), U('Index/index'));
            }
            $this->display('Kaoqin:index2');    
        }
    }

}
