<?php
/**
*任务模块
*
**/
class GoodsAction extends Action{
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array()
		);
		B('Authenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
	}

	/**
	* 序列号（SN码）跟踪表
	***/
	public function sn_track(){
		$where['stock_in_id'] = array('neq', 0);

		// 普通搜索
		if (!empty($_GET['search'])) {
			$where['sn'] = array('LIKE', '%' . $_GET['search'] . '%');
		}

		//分页
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1;
		if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			cookie('listrows', $listrows, 3600 * 24 * 30);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = cookie('listrows') ?: 15;
			$params[] = "listrows=".$listrows;
		}
		$d_sn = D('Sn');
		$order = 'stock_in_id desc';
		$list = $d_sn->getList($where, $p.','.$listrows, $order);
		$count = $d_sn->count;
		import("@.ORG.Page");
		$Page = new Page($count, $listrows);
		$this->page = $Page->show();
		$this->assign('list',$list);
		$this->alert = parseAlert();
		$this->display();
	}


	/**
	 * SN 状态跟踪
	 */
	public function sn_view()
	{
		$sn_id = (int) $_GET['id'];
		$d_sn = D('Sn');
		$this->data = $d_sn->getProductInfo($sn_id);
		$this->display();
	}
	

}